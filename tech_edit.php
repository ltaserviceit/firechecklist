<?php
session_start();

// Security: Only Technicians can access this
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'technician') {
    header("Location: index.php");
    exit();
}

require 'db.php';

$task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) : 0;

// Catch the specific table and submission index from the URL
$target_table = isset($_GET['table']) ? $_GET['table'] : '';
$target_sub_idx = isset($_GET['sub_idx']) && $_GET['sub_idx'] !== '' ? intval($_GET['sub_idx']) : -1;

$task_result = $conn->query("SELECT * FROM tasks WHERE task_id = $task_id AND status = 'Pending'");
if ($task_result->num_rows == 0) {
    die("<div style='text-align:center; padding:50px; font-family:Arial;'>
            <h2 style='color:#d9534f;'>Access Denied</h2>
            <p>Task not found or already marked as Completed.</p>
            <a href='tech_dashboard.php'>Return to Dashboard</a>
         </div>");
}
$task = $task_result->fetch_assoc();

$systems = [
    "fire_alarm_system_con"      => "Fire Alarm System (Conventional)",
    "fire_alarm_system_add"      => "Fire Alarm System (Addressable)",
    "fireman_intercom_system"    => "Fireman Intercom System",
    "fire_suppression_system_1"  => "Fire Suppression System 1 (CO2)",
    "fire_suppression_system_2"  => "Fire Suppression System 2 (FM200)",
    "fire_suppression_system_3"  => "Fire Suppression System 3 (FE-13)",
    "fire_suppression_system_4"  => "Fire Suppression System 4 (Inert Gas)",
    "wet_chemical_system"        => "Wet Chemical System",
    "fire_hose_reel_system"      => "Fire Hose Reel System",
    "fire_sprinkler_system"      => "Fire Sprinkler System",
    "wet_riser_system"           => "Wet Riser System",
    "dry_riser_system"           => "Dry Riser System",
    "pressurised_hydrant_system" => "Pressurised Hydrant System",
];

// Helper to extract image path from remarks text without showing the raw tag
function extract_remark_data($remark_string) {
    $text = $remark_string;
    $img = "";
    if (preg_match('/\[IMG: (.*?)\]/', $remark_string, $matches)) {
        $img = $matches[1];
        $text = str_replace($matches[0], '', $remark_string);
    }
    return ['text' => trim($text), 'img' => $img];
}

// Helper to sort items logically by item_bil (1, 1-I, 1-II, 2...) instead of DB auto-increment id
function sort_checklist_items(&$items) {
    $roman_map = [
        "I"=>1, "II"=>2, "III"=>3, "IV"=>4, "V"=>5, "VI"=>6, "VII"=>7, "VIII"=>8, "IX"=>9, "X"=>10,
        "XI"=>11, "XII"=>12, "XIII"=>13, "XIV"=>14, "XV"=>15, "XVI"=>16, "XVII"=>17, "XVIII"=>18, "XIX"=>19, "XX"=>20
    ];
    usort($items, function($a, $b) use ($roman_map) {
        $a_bil = strval($a['item_bil']);
        $b_bil = strval($b['item_bil']);
        
        $a_parts = explode('-', $a_bil);
        $b_parts = explode('-', $b_bil);
        
        $a_main = $a_parts[0];
        $b_main = $b_parts[0];
        
        if (is_numeric($a_main) && is_numeric($b_main)) {
            if (intval($a_main) !== intval($b_main)) {
                return intval($a_main) - intval($b_main);
            }
        } elseif (isset($roman_map[$a_main]) && isset($roman_map[$b_main])) {
            if ($roman_map[$a_main] !== $roman_map[$b_main]) {
                return $roman_map[$a_main] - $roman_map[$b_main];
            }
        } else {
            $cmp = strcmp($a_main, $b_main);
            if ($cmp !== 0) return $cmp;
        }
        
        $a_sub = $a_parts[1] ?? '';
        $b_sub = $b_parts[1] ?? '';
        
        if ($a_sub === '' && $b_sub !== '') return -1;
        if ($a_sub !== '' && $b_sub === '') return 1;
        
        if (isset($roman_map[$a_sub]) && isset($roman_map[$b_sub])) {
            return $roman_map[$a_sub] - $roman_map[$b_sub];
        }
        if (is_numeric($a_sub) && is_numeric($b_sub)) {
            return intval($a_sub) - intval($b_sub);
        }
        
        return $a['id'] - $b['id'];
    });
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Process deleted rows
    if (!empty($_POST['deleted_ids'])) {
        foreach ($_POST['deleted_ids'] as $del_table => $ids) {
            $del_table_clean = $conn->real_escape_string($del_table);
            foreach ($ids as $del_id) {
                $del_id = intval($del_id);
                $conn->query("DELETE FROM `$del_table_clean` WHERE id=$del_id AND task_id=$task_id");
            }
        }
    }

    // 2. Process updates to existing rows
    if (!empty($_POST['rows'])) {
        foreach ($_POST['rows'] as $id => $fields) {
            $table   = $conn->real_escape_string($fields['table']);
            $row_id  = intval($id);
            $section = $fields['section'] ?? '';

            if ($section === 'PANEL PROFILE') {
                $desc  = $conn->real_escape_string($fields['description'] ?? '');
                $type  = $conn->real_escape_string($fields['panel_type'] ?? '');
                $brand = $conn->real_escape_string($fields['panel_brand'] ?? '');
                $model = $conn->real_escape_string($fields['panel_model'] ?? '');
                $qty   = $conn->real_escape_string($fields['panel_qty'] ?? '');
                $loc   = $conn->real_escape_string($fields['location_floor'] ?? '');
                $conn->query("UPDATE `$table` SET description='$desc', panel_type='$type', panel_brand='$brand', panel_model='$model', panel_qty='$qty', location_floor='$loc' WHERE id=$row_id AND task_id=$task_id");
                
            } elseif (in_array($section, ['SYSTEM INFO', 'PUMP INFO', 'PUMP PRESSURE'])) {
                if ($table === 'fire_hose_reel_system' && isset($fields['duty']) && isset($fields['standby'])) {
                    $duty = $conn->real_escape_string($fields['duty']);
                    $standby = $conn->real_escape_string($fields['standby']);
                    $val = "DUTY: $duty | STANDBY: $standby";
                    $rem = $fields['remarks'] ?? '';
                    $existing_img = $fields['existing_img'] ?? '';
                    if (!empty($existing_img)) { $rem .= " [IMG: " . $existing_img . "]"; }
                    $rem = $conn->real_escape_string($rem);
                    $conn->query("UPDATE `$table` SET checklist='$val', remarks='$rem' WHERE id=$row_id AND task_id=$task_id");
                } elseif (in_array($table, ['fire_sprinkler_system', 'wet_riser_system', 'pressurised_hydrant_system']) && $section === 'PUMP PRESSURE') {
                    $val = $conn->real_escape_string($fields['checklist'] ?? '');
                    $conn->query("UPDATE `$table` SET checklist='$val' WHERE id=$row_id AND task_id=$task_id");
                    if (!empty($fields['is_primary']) && !empty($fields['group_bil'])) {
                        $rem = $fields['remarks'] ?? '';
                        $existing_img = $fields['existing_img'] ?? '';
                        if (!empty($existing_img)) { $rem .= " [IMG: " . $existing_img . "]"; }
                        $rem = $conn->real_escape_string($rem);
                        $base_bil = $conn->real_escape_string($fields['group_bil']);
                        $conn->query("UPDATE `$table` SET remarks='$rem' WHERE task_id=$task_id AND section_name='PUMP PRESSURE' AND item_bil LIKE '$base_bil-%'");
                    }
                } else {
                    $val = $conn->real_escape_string($fields['checklist'] ?? ''); 
                    $rem = $fields['remarks'] ?? '';
                    $existing_img = $fields['existing_img'] ?? '';
                    if (!empty($existing_img)) { $rem .= " [IMG: " . $existing_img . "]"; }
                    $rem = $conn->real_escape_string($rem);
                    $conn->query("UPDATE `$table` SET checklist='$val', remarks='$rem' WHERE id=$row_id AND task_id=$task_id");
                }
                
            } elseif ($section === 'DEVICES') {
                $zl   = $conn->real_escape_string($fields['zone_loop'] ?? '');
                $loc  = $conn->real_escape_string($fields['location_floor'] ?? '');
                $cl   = isset($fields['checklist']) ? 'Done' : 'N/A';
                $cond = $conn->real_escape_string($fields['condition'] ?? '');
                $rem  = $fields['remarks'] ?? '';
                $existing_img = $fields['existing_img'] ?? '';
                if (!empty($existing_img)) { $rem .= " [IMG: " . $existing_img . "]"; }
                $rem = $conn->real_escape_string($rem);
                $conn->query("UPDATE `$table` SET location_floor='$loc', zone_loop='$zl', checklist='$cl', item_condition='$cond', remarks='$rem' WHERE id=$row_id AND task_id=$task_id");
                
            } elseif ($section === 'EQUIPMENT') {
                $desc = $conn->real_escape_string($fields['description'] ?? '');
                $cl   = isset($fields['checklist']) ? 'Done' : 'N/A';
                $cond = $conn->real_escape_string($fields['condition'] ?? '');
                $rem  = $fields['remarks'] ?? '';
                $existing_img = $fields['existing_img'] ?? '';
                if (!empty($existing_img)) { $rem .= " [IMG: " . $existing_img . "]"; }
                $rem = $conn->real_escape_string($rem);
                $conn->query("UPDATE `$table` SET description='$desc', checklist='$cl', item_condition='$cond', remarks='$rem' WHERE id=$row_id AND task_id=$task_id");
                
            } else {
                $cl   = isset($fields['checklist']) ? 'Done' : 'N/A';
                $cond = $conn->real_escape_string($fields['condition'] ?? '');
                $rem  = $fields['remarks'] ?? '';
                $existing_img = $fields['existing_img'] ?? '';
                if (!empty($existing_img)) { $rem .= " [IMG: " . $existing_img . "]"; }
                $rem = $conn->real_escape_string($rem);
                $conn->query("UPDATE `$table` SET checklist='$cl', item_condition='$cond', remarks='$rem' WHERE id=$row_id AND task_id=$task_id");
            }
        }
    }

    // 3. Process new dynamic rows added during edit
    if (!empty($_POST['new_rows'])) {
        foreach ($_POST['new_rows'] as $tbl => $nrows) {
            $tbl_clean = $conn->real_escape_string($tbl);
            foreach ($nrows as $nfields) {
                $sec  = $conn->real_escape_string($nfields['section'] ?? 'DEVICES');
                $bil  = $conn->real_escape_string($nfields['item_bil'] ?? '');
                $desc = $conn->real_escape_string($nfields['description'] ?? '');
                $loc  = $conn->real_escape_string($nfields['location_floor'] ?? '');
                $zl   = $conn->real_escape_string($nfields['zone_loop'] ?? '');
                $cl   = isset($nfields['checklist']) ? 'Done' : 'N/A';
                $cond = $conn->real_escape_string($nfields['condition'] ?? '');
                $rem  = $conn->real_escape_string($nfields['remarks'] ?? '');
                
                $conn->query("INSERT INTO `$tbl_clean` (task_id, section_name, item_bil, description, location_floor, zone_loop, checklist, item_condition, remarks) 
                    VALUES ($task_id, '$sec', '$bil', '$desc', '$loc', '$zl', '$cl', '$cond', '$rem')");
            }
        }
    }

    header("Location: tech_menu.php?task_id=$task_id");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Edit Checklists #<?php echo $task_id; ?></title>
    <style>
        :root {
            --primary: #C41E3A;
            --primary-hover: #a8182f;
            --bg-color: #f4f5f7;
            --surface: #ffffff;
            --text-main: #111827; 
            --text-muted: #4b5563; 
            --border: #d1d5db; 
            --success: #10b981;
            --row-alt: #f8fafc;
            --row-hover: #f1f5f9;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: var(--bg-color); 
            color: var(--text-main);
            min-height: 100vh;
            padding-bottom: 90px;
        }

        /* ============ TOP BAR ============ */
        .topbar {
            background: var(--primary);
            color: white;
            padding: clamp(16px, 4vw, 20px) clamp(16px, 5vw, 24px);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 14px rgba(196,30,58,0.18);
        }
        
        .topbar-title { font-size: clamp(18px, 4vw, 22px); font-weight: 700; letter-spacing: 0.5px; display: flex; align-items: center; gap: 12px; }
        .topbar-title svg { width: 28px; height: 28px; background: rgba(255,255,255,0.15); padding: 4px; border-radius: 6px; }

        .back-btn {
            background: rgba(255,255,255,0.15); color: white; text-decoration: none;
            padding: 10px 16px; border-radius: 6px; font-weight: 600; font-size: 15px;
            transition: background 0.2s; display: flex; align-items: center; gap: 8px;
            border: 1px solid rgba(255,255,255,0.25);
        }
        .back-btn:hover { background: rgba(255,255,255,0.28); }

        /* ============ CONTAINER ============ */
        .container { max-width: 1200px; margin: 30px auto; padding: 0 clamp(16px, 4vw, 24px); }
        
        .page-header { background: var(--surface); padding: 28px; border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 2px 10px rgba(0,0,0,0.02); margin-bottom: 30px; border-left: 8px solid var(--primary); }
        .page-header h1 { margin: 0 0 10px 0; color: var(--text-main); font-size: clamp(22px, 5vw, 28px); font-weight: 800; letter-spacing: -0.5px; }
        .page-header p { margin: 0 0 16px 0; color: var(--text-muted); font-size: 16px; line-height: 1.5; }
        
        .readonly-tag { font-size: 14px; color: var(--text-main); background: var(--row-hover); padding: 10px 16px; border-radius: 6px; border: 1px solid var(--border); display: inline-flex; align-items: center; gap: 10px; font-weight: 600; }
        .readonly-tag svg { width: 16px; height: 16px; color: var(--text-muted); }

        .system-separator { 
            background: var(--surface); color: var(--primary); 
            padding: 18px 24px; margin-top: 48px; margin-bottom: 24px; 
            font-size: 16px; font-weight: 800; border-radius: 8px; 
            border: 1px solid var(--border); border-left: 6px solid var(--primary);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); text-transform: uppercase; letter-spacing: 0.5px;
        }

        /* ============ TABLES ============ */
        .table-responsive { overflow-x: auto; width: 100%; margin-bottom: 40px; border-radius: 12px; background: var(--surface); border: 1px solid var(--border); box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05); }
        table { width: 100%; border-collapse: collapse; min-width: 900px; }
        
        th, td { padding: 18px 20px; text-align: left; vertical-align: middle; font-size: 15px; border-bottom: 1px solid var(--border); line-height: 1.4; }
        
        th { background: var(--row-alt); color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 13px; letter-spacing: 1px; }
        th.section-header { background: var(--surface) !important; color: var(--text-main) !important; font-size: 18px; padding: 24px 20px; font-weight: 800; border-bottom: 3px solid var(--border); letter-spacing: 0.5px; }
        th.sub-header { background: var(--surface) !important; color: var(--text-muted) !important; font-size: 14px; padding: 16px 20px; border-bottom: 2px solid var(--border); }
        
        tbody tr:nth-child(even) td { background-color: var(--row-alt); }
        tbody tr:hover td { background-color: var(--row-hover); }
        tr:last-child td { border-bottom: none; }

        /* ============ INPUTS & BUTTONS ============ */
        input[type="text"], select.cond-select { 
            width: 100%; padding: 14px 16px; border: 1.5px solid var(--border); 
            border-radius: 8px; font-size: 16px; background: var(--surface); 
            transition: all 0.2s; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02); 
            -webkit-appearance: none; color: var(--text-main); font-weight: 500;
        }
        input[type="text"]:focus, select.cond-select:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 4px rgba(196, 30, 58, 0.1); }
        input[type="text"]::placeholder { color: #94a3b8; font-weight: 400; }

        .split-edit-press { display: flex; gap: 8px; width: 100%; }
        .split-edit-press div { flex: 1; display: flex; align-items: center; gap: 4px; }
        .split-edit-press label { font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; }

        .remark-group { display: flex; gap: 8px; align-items: center; }

        .btn-add-floor {
            background: #f1f5f9; color: var(--primary); border: 1.5px dashed var(--border);
            padding: 8px 14px; border-radius: 6px; font-weight: 700; font-size: 13px;
            cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
            transition: all 0.2s;
        }
        .btn-add-floor:hover {
            background: #e2e8f0; border-color: var(--primary);
        }

        .btn-delete-row {
            background: #fee2e2; border: 1.5px solid #fca5a5; color: #dc2626;
            border-radius: 8px; padding: 12px; cursor: pointer;
            display: inline-flex; align-items: center; justify-content: center;
            transition: all 0.2s; flex-shrink: 0;
        }
        .btn-delete-row:hover {
            background: #ef4444; border-color: #ef4444; color: white;
        }
        .btn-delete-row svg { width: 18px; height: 18px; stroke: currentColor; }

        /* ============ CHECKBOXES ============ */
        .chk-wrap { display: flex; align-items: center; justify-content: center; }
        .chk-wrap input[type="checkbox"] { display: none; }
        .chk-wrap label { 
            width: 34px; height: 34px; border: 2.5px solid #cbd5e1; border-radius: 8px; 
            display: flex; align-items: center; justify-content: center; cursor: pointer; 
            font-size: 20px; color: transparent; background: var(--surface); 
            transition: all 0.2s; font-weight: 900; box-shadow: 0 2px 4px rgba(0,0,0,0.05); 
        }
        .chk-wrap label::after { content: "✓"; }
        .chk-wrap input[type="checkbox"]:checked + label { background: var(--success); border-color: var(--success); color: white; box-shadow: 0 4px 8px rgba(16, 185, 129, 0.25); }

        /* ============ SAVE BAR ============ */
        .save-bar { 
            position: fixed; bottom: 0; left: 0; width: 100%; padding: 20px; 
            text-align: center; z-index: 100; background: rgba(255, 255, 255, 0.95); 
            backdrop-filter: blur(10px); border-top: 2px solid var(--border); box-shadow: 0 -4px 20px rgba(0,0,0,0.08);
        }
        button[type="submit"] { 
            background: var(--primary); color: white; border: none; padding: 18px 48px; 
            font-size: 18px; font-weight: 800; border-radius: 10px; cursor: pointer; 
            transition: all 0.2s; box-shadow: 0 6px 16px rgba(196, 30, 58, 0.3); letter-spacing: 0.5px;
            display: inline-flex; align-items: center; gap: 10px;
        }
        button[type="submit"]:hover { background: var(--primary-hover); transform: translateY(-2px); box-shadow: 0 8px 20px rgba(196, 30, 58, 0.35); }

        .center { text-align: center; }
        .bold { font-weight: 700; color: var(--text-main); }
        .indent { padding-left: 40px !important; color: var(--text-muted); font-weight: 500; }

        /* ============ ACCORDION ============ */
        th.section-header { cursor: pointer; user-select: none; padding: 0; }
        .accordion-toggle {
            display: flex; align-items: center; justify-content: space-between;
            width: 100%; padding: 20px 20px; gap: 12px;
        }
        .accordion-icon {
            width: 22px; height: 22px; stroke: var(--text-muted); stroke-width: 2.5;
            transition: transform 0.25s ease; flex-shrink: 0;
        }
        .table-responsive.collapsed .accordion-icon { transform: rotate(-90deg); }
        .table-responsive.collapsed .col-header-row,
        .table-responsive.collapsed tbody { display: none; }
        .table-responsive { transition: box-shadow 0.2s; }

        .accordion-controls { display: flex; justify-content: flex-end; gap: 10px; margin-bottom: 16px; }
        .btn-accordion-all {
            background: var(--surface); color: var(--text-muted); border: 1.5px solid var(--border);
            padding: 10px 16px; border-radius: 8px; font-weight: 700; font-size: 13px;
            cursor: pointer; transition: all 0.2s;
        }
        .btn-accordion-all:hover { background: var(--row-alt); color: var(--text-main); border-color: var(--primary); }

        /* ============ MOBILE STACKED (CARD) VIEW ============ */
        @media (max-width: 760px) {
            .table-responsive { overflow-x: visible; }
            table { min-width: 0; width: 100%; }

            table, tbody, tr, td { display: block; width: 100%; }
            .col-header-row { display: none !important; }

            tbody { padding: 12px; }
            tbody tr {
                background: var(--surface); border: 1px solid var(--border); border-radius: 10px;
                margin-bottom: 12px; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.03);
            }
            tbody tr:last-child { margin-bottom: 0; }
            tbody tr:nth-child(even) td { background-color: var(--surface); }

            tbody tr.group-row { background: var(--row-alt) !important; border-radius: 10px; }
            tbody tr.group-row td { background: transparent !important; }

            td {
                padding: 12px 16px; border-bottom: 1px solid var(--border);
                width: 100% !important;
            }
            td:last-child { border-bottom: none; }
            td[colspan] { text-align: left; }

            td[data-label]::before {
                content: attr(data-label);
                display: block;
                font-size: 11px; font-weight: 700; text-transform: uppercase;
                letter-spacing: 0.6px; color: var(--text-muted); margin-bottom: 6px;
            }

            td.center { text-align: left; }
            td.indent { padding-left: 16px !important; }

            .remark-group { flex-wrap: wrap; }
        }
    </style>
</head>
<body>

<div class="topbar">
    <div class="topbar-title">
        <svg viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
        EDIT SUBMISSION
    </div>
    <a href="tech_menu.php?task_id=<?= $task_id ?>" class="back-btn">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        Cancel & Back
    </a>
</div>

<div class="container">
    <form method="POST" id="editForm">
        <div id="deletedContainer"></div>

        <div class="page-header">
            <h1>Reviewing Checklists for Task #<?php echo $task_id; ?></h1>
            <p>Modify your answers below and click Save at the bottom when you are done.</p>
            <div class="readonly-tag">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>
                <?php echo htmlspecialchars($task['company_name']); ?> — <?php echo htmlspecialchars($task['building_name']); ?>
            </div>
        </div>

        <div class="accordion-controls">
            <button type="button" class="btn-accordion-all" onclick="setAllAccordions(true)">Expand All</button>
            <button type="button" class="btn-accordion-all" onclick="setAllAccordions(false)">Collapse All</button>
        </div>

        <?php foreach ($systems as $table => $system_name): ?>
            <?php
            if ($target_table !== '' && $table !== $target_table) { continue; }

            $rows = $conn->query("SELECT * FROM `$table` WHERE task_id = $task_id ORDER BY id ASC");
            if (!$rows || $rows->num_rows == 0) continue;

            $submissions = [];
            while ($r = $rows->fetch_assoc()) {
                $sec = $r['section_name'];
                $bil = $r['item_bil'];
                $sub_idx = 0;
                while (isset($submissions[$sub_idx][$sec][$bil])) { $sub_idx++; }
                $submissions[$sub_idx][$sec][$bil] = $r;
            }

            if ($target_sub_idx >= 0) {
                if (isset($submissions[$target_sub_idx])) {
                    $submissions = [$target_sub_idx => $submissions[$target_sub_idx]];
                } else { continue; }
            }
            ?>
            
            <?php foreach ($submissions as $sub_idx => $sections): ?>
                <?php
                $loc_label = "";
                if (isset($sections['PANEL PROFILE'])) {
                    foreach ($sections['PANEL PROFILE'] as $item) {
                        if (!empty($item['location_floor'])) {
                            $loc_label = " - " . strtoupper($item['location_floor']);
                            break;
                        }
                    }
                }
                if (empty($loc_label) && count($submissions) > 1) {
                    $loc_label = " - SUBMISSION " . ($sub_idx + 1);
                }
                ?>
            
                <div class="system-separator">
                    <?= htmlspecialchars($system_name) ?><?= htmlspecialchars($loc_label) ?>
                </div>
                
                <?php foreach ($sections as $section_name => $items_keyed): ?>
                    <?php 
                    $items = array_values($items_keyed); 
                    sort_checklist_items($items);
                    ?>
                    
                    <?php if ($section_name === 'PANEL PROFILE'): ?>
                        <?php if (strpos($table, 'riser') !== false): ?>
                            <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr><th colspan="4" class="section-header" onclick="toggleAccordion(this)">
                                        <div class="accordion-toggle">
                                            <span><?= strtoupper($system_name) ?></span>
                                            <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                        </div>
                                    </th></tr>
                                    <tr class="col-header-row">
                                        <th width="10%" class="center">BIL</th>
                                        <th width="40%">LOCATION</th>
                                        <th width="30%">STACK</th>
                                        <th width="20%">NO. OF STACK</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                    <tr>
                                        <input type="hidden" name="rows[<?= $item['id'] ?>][table]" value="<?= htmlspecialchars($table) ?>">
                                        <input type="hidden" name="rows[<?= $item['id'] ?>][section]" value="<?= htmlspecialchars($section_name) ?>">
                                        <input type="hidden" name="rows[<?= $item['id'] ?>][description]" value="<?= htmlspecialchars($item['description'] ?? 'PANEL') ?>">
                                        
                                        <td class="center bold" data-label="BIL"><?= htmlspecialchars($item['item_bil']) ?></td>
                                        <td data-label="Location"><input type="text" name="rows[<?= $item['id'] ?>][location_floor]" value="<?= htmlspecialchars($item['location_floor'] ?? '') ?>"></td>
                                        <td data-label="Stack"><input type="text" name="rows[<?= $item['id'] ?>][panel_type]" value="<?= htmlspecialchars($item['panel_type'] ?? '') ?>"></td>
                                        <td data-label="No. of Stack"><input type="text" name="rows[<?= $item['id'] ?>][panel_qty]" value="<?= htmlspecialchars($item['panel_qty'] ?? '') ?>"></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            </div>
                        <?php elseif (strpos($table, 'suppression') !== false || $table === 'wet_chemical_system'): ?>
                            <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr><th colspan="4" class="section-header" onclick="toggleAccordion(this)">
                                        <div class="accordion-toggle">
                                            <span><?= strtoupper($system_name) ?></span>
                                            <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                        </div>
                                    </th></tr>
                                    <tr class="col-header-row">
                                        <th width="10%" class="center">BIL</th>
                                        <th width="40%">LOCATION/ROOM</th>
                                        <th width="25%">CYLINDER CAPACITY (KG)</th>
                                        <th width="25%">QUANTITY OF CYLINDER</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                    <tr>
                                        <input type="hidden" name="rows[<?= $item['id'] ?>][table]" value="<?= htmlspecialchars($table) ?>">
                                        <input type="hidden" name="rows[<?= $item['id'] ?>][section]" value="<?= htmlspecialchars($section_name) ?>">
                                        <input type="hidden" name="rows[<?= $item['id'] ?>][description]" value="<?= htmlspecialchars($item['description'] ?? 'PANEL') ?>">
                                        
                                        <td class="center bold" data-label="BIL"><?= htmlspecialchars($item['item_bil']) ?></td>
                                        <td data-label="Location/Room"><input type="text" name="rows[<?= $item['id'] ?>][location_floor]" value="<?= htmlspecialchars($item['location_floor'] ?? '') ?>"></td>
                                        <td data-label="Cylinder Capacity (KG)"><input type="text" name="rows[<?= $item['id'] ?>][panel_type]" value="<?= htmlspecialchars($item['panel_type'] ?? '') ?>"></td>
                                        <td data-label="Quantity of Cylinder"><input type="text" name="rows[<?= $item['id'] ?>][panel_qty]" value="<?= htmlspecialchars($item['panel_qty'] ?? '') ?>"></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            </div>
                        <?php elseif ($table === 'fire_alarm_system_add' || $table === 'fire_alarm_system_con' || $table === 'fireman_intercom_system'): ?>
                            <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr><th colspan="6" class="section-header" onclick="toggleAccordion(this)">
                                        <div class="accordion-toggle">
                                            <span><?= strtoupper($system_name) ?> CONTROL PANEL PROFILE</span>
                                            <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                        </div>
                                    </th></tr>
                                    <tr class="col-header-row">
                                        <th width="5%" class="center">BIL</th>
                                        <th width="20%">PANEL PROFILE</th>
                                        <th width="15%">TYPE OF PANEL</th>
                                        <th width="15%">BRAND OF PANEL</th>
                                        <th width="10%">QTY/ZONE</th>
                                        <th width="35%">LOCATION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                    <tr>
                                        <input type="hidden" name="rows[<?= $item['id'] ?>][table]" value="<?= htmlspecialchars($table) ?>">
                                        <input type="hidden" name="rows[<?= $item['id'] ?>][section]" value="<?= htmlspecialchars($section_name) ?>">
                                        
                                        <td class="center bold" data-label="BIL"><?= htmlspecialchars($item['item_bil']) ?></td>
                                        <td data-label="Panel Profile">
                                            <select name="rows[<?= $item['id'] ?>][description]" class="cond-select">
                                                <option value="" <?= empty($item['description']) ? 'selected' : '' ?>>-- Select --</option>
                                                <option value="Main Panel" <?= ($item['description'] == 'Main Panel') ? 'selected' : '' ?>>Main Panel</option>
                                                <option value="Sub Panel" <?= ($item['description'] == 'Sub Panel') ? 'selected' : '' ?>>Sub Panel</option>
                                            </select>
                                        </td>
                                        <td data-label="Type of Panel">
                                            <select name="rows[<?= $item['id'] ?>][panel_type]" class="cond-select">
                                                <option value="" <?= empty($item['panel_type']) ? 'selected' : '' ?>>-- Select --</option>
                                                <?php if ($table === 'fire_alarm_system_con'): ?>
                                                    <option value="Conventional" <?= ($item['panel_type'] == 'Conventional') ? 'selected' : '' ?>>Conventional</option>
                                                <?php elseif ($table === 'fireman_intercom_system'): ?>
                                                    <option value="Conventional" <?= ($item['panel_type'] == 'Conventional') ? 'selected' : '' ?>>Conventional</option>
                                                    <option value="Addressable" <?= ($item['panel_type'] == 'Addressable') ? 'selected' : '' ?>>Addressable</option>
                                                    <option value="Semi Addressable" <?= ($item['panel_type'] == 'Semi Addressable') ? 'selected' : '' ?>>Semi Addressable</option>
                                                <?php else: ?>
                                                    <option value="Conventional" <?= ($item['panel_type'] == 'Conventional') ? 'selected' : '' ?>>Conventional</option>
                                                    <option value="Addressable" <?= ($item['panel_type'] == 'Addressable') ? 'selected' : '' ?>>Addressable</option>
                                                <?php endif; ?>
                                            </select>
                                        </td>
                                        <td data-label="Brand of Panel"><input type="text" name="rows[<?= $item['id'] ?>][panel_brand]" value="<?= htmlspecialchars($item['panel_brand'] ?? '') ?>"></td>
                                        <td class="center" data-label="Qty/Zone"><input type="text" name="rows[<?= $item['id'] ?>][panel_qty]" value="<?= htmlspecialchars($item['panel_qty'] ?? '') ?>"></td>
                                        <td data-label="Location"><input type="text" name="rows[<?= $item['id'] ?>][location_floor]" value="<?= htmlspecialchars($item['location_floor'] ?? '') ?>"></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr><th colspan="7" class="section-header" onclick="toggleAccordion(this)">
                                        <div class="accordion-toggle">
                                            <span><?= strtoupper($system_name) ?> CONTROL PANEL PROFILE</span>
                                            <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                        </div>
                                    </th></tr>
                                    <tr class="col-header-row">
                                        <th width="5%" class="center">BIL</th>
                                        <th width="20%">PANEL PROFILE</th>
                                        <th width="15%">TYPE OF PANEL</th>
                                        <th width="15%">BRAND OF PANEL</th>
                                        <th width="12%">MODEL</th>
                                        <th width="10%">QTY/ZONE</th>
                                        <th width="23%">LOCATION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                    <tr>
                                        <input type="hidden" name="rows[<?= $item['id'] ?>][table]" value="<?= htmlspecialchars($table) ?>">
                                        <input type="hidden" name="rows[<?= $item['id'] ?>][section]" value="<?= htmlspecialchars($section_name) ?>">
                                        
                                        <td class="center bold" data-label="BIL"><?= htmlspecialchars($item['item_bil']) ?></td>
                                        <td data-label="Panel Profile"><input type="text" name="rows[<?= $item['id'] ?>][description]" value="<?= htmlspecialchars($item['description'] ?? '') ?>"></td>
                                        <td data-label="Type of Panel"><input type="text" name="rows[<?= $item['id'] ?>][panel_type]" value="<?= htmlspecialchars($item['panel_type'] ?? '') ?>"></td>
                                        <td data-label="Brand of Panel"><input type="text" name="rows[<?= $item['id'] ?>][panel_brand]" value="<?= htmlspecialchars($item['panel_brand'] ?? '') ?>"></td>
                                        <td data-label="Model"><input type="text" name="rows[<?= $item['id'] ?>][panel_model]" value="<?= htmlspecialchars($item['panel_model'] ?? '') ?>"></td>
                                        <td class="center" data-label="Qty/Zone"><input type="text" name="rows[<?= $item['id'] ?>][panel_qty]" value="<?= htmlspecialchars($item['panel_qty'] ?? '') ?>"></td>
                                        <td data-label="Location"><input type="text" name="rows[<?= $item['id'] ?>][location_floor]" value="<?= htmlspecialchars($item['location_floor'] ?? '') ?>"></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            </div>
                        <?php endif; ?>

                    <?php elseif (in_array($table, ['fire_sprinkler_system', 'wet_riser_system', 'pressurised_hydrant_system']) && $section_name === 'PUMP PRESSURE'): ?>
                        <div class="table-responsive">
                        <table>
                            <thead>
                                <tr><th colspan="6" class="section-header" onclick="toggleAccordion(this)">
                                    <div class="accordion-toggle">
                                        <span><?= strtoupper($system_name . ' - ' . $section_name) ?></span>
                                        <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                    </div>
                                </th></tr>
                                <tr class="col-header-row">
                                    <th width="5%" class="center">BIL</th>
                                    <th width="30%">VALVE</th>
                                    <th width="10%" class="center">JOCKEY</th>
                                    <th width="10%" class="center">DUTY</th>
                                    <th width="10%" class="center">STANDBY</th>
                                    <th width="35%">REMARKS & PHOTOS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $grouped_press = [];
                                foreach ($items as $item) {
                                    $parts = explode('-', $item['item_bil']);
                                    $base_bil = $parts[0];
                                    $type = strtolower($parts[1] ?? 'jockey');
                                    $grouped_press[$base_bil][$type] = $item;
                                }
                                $pressure_labels = [
                                    "1" => "CUT IN (PSI)",
                                    "2" => "CUT OUT (PSI)",
                                    "3" => "SUCTION VALVE (STATUS)",
                                    "4" => "DISCHARGE VALVE (STATUS)"
                                ];
                                foreach ($pressure_labels as $base_bil => $valve_name):
                                    if (!isset($grouped_press[$base_bil])) continue;
                                    $types = $grouped_press[$base_bil];
                                    $first_item = $types['jockey'] ?? ($types['duty'] ?? ($types['standby'] ?? null));
                                    if (!$first_item) continue;
                                    $parsed = extract_remark_data($first_item['remarks'] ?? '');
                                ?>
                                <tr>
                                    <td class="center bold" data-label="BIL"><?= htmlspecialchars($base_bil) ?></td>
                                    <td class="bold" data-label="Valve"><?= htmlspecialchars($valve_name) ?></td>
                                    
                                    <?php foreach (['jockey', 'duty', 'standby'] as $col): ?>
                                        <?php $sub_item = $types[$col] ?? null; ?>
                                        <td class="center" data-label="<?= ucfirst($col) ?>">
                                            <?php if ($sub_item): ?>
                                                <input type="hidden" name="rows[<?= $sub_item['id'] ?>][table]" value="<?= htmlspecialchars($table) ?>">
                                                <input type="hidden" name="rows[<?= $sub_item['id'] ?>][section]" value="<?= htmlspecialchars($section_name) ?>">
                                                <input type="text" name="rows[<?= $sub_item['id'] ?>][checklist]" value="<?= htmlspecialchars($sub_item['checklist'] ?? '') ?>">
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; ?>
                                    
                                    <td data-label="Remarks & Photos">
                                        <input type="hidden" name="rows[<?= $first_item['id'] ?>][is_primary]" value="1">
                                        <input type="hidden" name="rows[<?= $first_item['id'] ?>][group_bil]" value="<?= htmlspecialchars($base_bil) ?>">
                                        <input type="text" name="rows[<?= $first_item['id'] ?>][remarks]" value="<?= htmlspecialchars($parsed['text']) ?>">
                                        <input type="hidden" name="rows[<?= $first_item['id'] ?>][existing_img]" value="<?= htmlspecialchars($parsed['img']) ?>">
                                        <?php if(!empty($parsed['img'])): ?>
                                            <div style="margin-top: 8px; display: inline-block;">
                                                <a href="<?= htmlspecialchars($parsed['img']) ?>" target="_blank" title="View Attached Photo">
                                                    <img src="<?= htmlspecialchars($parsed['img']) ?>" style="height: 42px; border-radius: 6px; border: 1px solid var(--border); box-shadow: 0 2px 4px rgba(0,0,0,0.08); display: block;">
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        </div>

                    <?php elseif (in_array($section_name, ['SYSTEM INFO', 'PUMP INFO', 'PUMP PRESSURE'])): ?>
                        <div class="table-responsive">
                        <table>
                            <thead>
                                <tr><th colspan="4" class="section-header" onclick="toggleAccordion(this)">
                                    <div class="accordion-toggle">
                                        <span><?= strtoupper($system_name . ' - ' . $section_name) ?></span>
                                        <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                    </div>
                                </th></tr>
                                <tr class="col-header-row">
                                    <th width="5%" class="center">BIL</th>
                                    <th width="55%">DESCRIPTION</th>
                                    <th width="20%" class="center"><?= $section_name === 'PUMP PRESSURE' ? 'VALUE' : 'STATUS' ?></th>
                                    <th width="20%">REMARKS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): 
                                    $parsed = extract_remark_data($item['remarks'] ?? '');
                                ?>
                                <tr>
                                    <input type="hidden" name="rows[<?= $item['id'] ?>][table]" value="<?= htmlspecialchars($table) ?>">
                                    <input type="hidden" name="rows[<?= $item['id'] ?>][section]" value="<?= htmlspecialchars($section_name) ?>">
                                    
                                    <td class="center bold" data-label="BIL"><?= htmlspecialchars($item['item_bil']) ?></td>
                                    <td class="bold" data-label="Description"><?= htmlspecialchars($item['description']) ?></td>
                                    <td class="center" data-label="Value/Status">
                                        <?php if ($table === 'fire_hose_reel_system' && in_array($item['item_bil'], ['8 a)', '8 b)'])): ?>
                                            <?php
                                            preg_match('/DUTY:\s*(.*?)\s*\|\s*STANDBY:\s*(.*)/i', $item['checklist'], $matches);
                                            $duty_val = $matches[1] ?? '';
                                            $standby_val = $matches[2] ?? '';
                                            ?>
                                            <div class="split-edit-press">
                                                <div><label>Duty</label><input type="text" name="rows[<?= $item['id'] ?>][duty]" value="<?= htmlspecialchars($duty_val) ?>"></div>
                                                <div><label>Stby</label><input type="text" name="rows[<?= $item['id'] ?>][standby]" value="<?= htmlspecialchars($standby_val) ?>"></div>
                                            </div>
                                        <?php else: ?>
                                            <input type="text" name="rows[<?= $item['id'] ?>][checklist]" value="<?= htmlspecialchars($item['checklist'] ?? '') ?>">
                                        <?php endif; ?>
                                    </td>
                                    <td data-label="Remarks">
                                        <input type="text" name="rows[<?= $item['id'] ?>][remarks]" value="<?= htmlspecialchars($parsed['text']) ?>">
                                        <input type="hidden" name="rows[<?= $item['id'] ?>][existing_img]" value="<?= htmlspecialchars($parsed['img']) ?>">
                                        <?php if(!empty($parsed['img'])): ?>
                                            <div style="margin-top: 8px; display: inline-block;">
                                                <a href="<?= htmlspecialchars($parsed['img']) ?>" target="_blank" title="View Attached Photo">
                                                    <img src="<?= htmlspecialchars($parsed['img']) ?>" style="height: 42px; border-radius: 6px; border: 1px solid var(--border); box-shadow: 0 2px 4px rgba(0,0,0,0.08); display: block;">
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        </div>

                    <?php elseif ($section_name === 'EQUIPMENT'): ?>
                        <div class="table-responsive">
                        <table>
                            <thead>
                                <tr><th colspan="5" class="section-header" onclick="toggleAccordion(this)">
                                    <div class="accordion-toggle">
                                        <span><?= strtoupper($system_name . ' - ' . $section_name) ?></span>
                                        <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                    </div>
                                </th></tr>
                                <tr class="col-header-row">
                                    <th width="5%" class="center">BIL</th><th width="45%">DESCRIPTION</th>
                                    <th width="10%" class="center">CHECKLIST</th><th width="15%" class="center">CONDITION</th><th width="25%">REMARKS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $current_main_bil = '';
                                foreach ($items as $item): 
                                    $parsed = extract_remark_data($item['remarks'] ?? '');
                                    $bil_parts = explode('-', $item['item_bil']);
                                    $main_bil = $bil_parts[0];
                                    $is_sub = count($bil_parts) > 1;

                                    if ($table === 'fire_sprinkler_system' && ($main_bil == 6 || $main_bil == 7)) {
                                        if ($main_bil !== $current_main_bil) {
                                            $header_desc = ($main_bil == 6) ? "BUTTERFLY VALVE" : "FLOW SWITCH";
                                            echo '<tr class="group-row" style="background-color: var(--surface); border-top: 3px solid var(--border);">
                                                    <td class="center bold" data-label="BIL">'.htmlspecialchars($main_bil).'</td>
                                                    <td colspan="4" class="bold" style="color: var(--primary); font-size: 16px;">
                                                        '.htmlspecialchars($header_desc).'
                                                        <button type="button" class="btn-add-floor" onclick="addEditSprinklerRow(\''.$table.'\', \''.$main_bil.'\')" style="margin-left: 14px;">
                                                            + Add Location / Floor
                                                        </button>
                                                    </td>
                                                  </tr>';
                                            $current_main_bil = $main_bil;
                                        }

                                        if ($is_sub) {
                                            $sub_bil = $bil_parts[1];
                                            echo '<tr class="eq-row-'.$table.'-'.$main_bil.'">
                                                    <input type="hidden" name="rows['.$item['id'].'][table]" value="'.htmlspecialchars($table).'">
                                                    <input type="hidden" name="rows['.$item['id'].'][section]" value="EQUIPMENT">
                                                    <td class="center bold" data-label="BIL">'.htmlspecialchars($sub_bil).'</td>
                                                    <td class="indent" data-label="Description"><input type="text" name="rows['.$item['id'].'][description]" value="'.htmlspecialchars($item['description']).'" placeholder="Location / Floor"></td>
                                                    <td class="center" data-label="Checklist">'.checklist_select("rows[{$item['id']}][checklist]", $item['checklist'] == 'Done').'</td>
                                                    <td data-label="Condition">'.status_select("rows[{$item['id']}][condition]", $item['item_condition']).'</td>
                                                    <td data-label="Remarks">
                                                        <div class="remark-group">
                                                            <input type="text" name="rows['.$item['id'].'][remarks]" value="'.htmlspecialchars($parsed['text']).'">
                                                            <button type="button" class="btn-delete-row" title="Delete Row" onclick="deleteEditRow(this, \''.$table.'\', \''.$item['id'].'\', \''.$main_bil.'\', \'eq-row\')">
                                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                            </button>
                                                        </div>
                                                        <input type="hidden" name="rows['.$item['id'].'][existing_img]" value="'.htmlspecialchars($parsed['img']).'">
                                                        '.(!empty($parsed['img']) ? '
                                                            <div style="margin-top: 8px; display: inline-block;">
                                                                <a href="'.htmlspecialchars($parsed['img']).'" target="_blank" title="View Attached Photo">
                                                                    <img src="'.htmlspecialchars($parsed['img']).'" style="height: 42px; border-radius: 6px; border: 1px solid var(--border); box-shadow: 0 2px 4px rgba(0,0,0,0.08); display: block;">
                                                                </a>
                                                            </div>' : '').'
                                                    </td>
                                                  </tr>';
                                            continue;
                                        } else {
                                            continue; // Skip rendering standalone parent item as input
                                        }
                                    }
                                ?>
                                <tr>
                                    <input type="hidden" name="rows[<?= $item['id'] ?>][table]" value="<?= htmlspecialchars($table) ?>">
                                    <input type="hidden" name="rows[<?= $item['id'] ?>][section]" value="EQUIPMENT">
                                    <td class="center bold" data-label="BIL"><?= htmlspecialchars($item['item_bil']) ?></td>
                                    <td class="bold" data-label="Description"><?= htmlspecialchars($item['description']) ?></td>
                                    <td class="center" data-label="Checklist"><?= checklist_select("rows[{$item['id']}][checklist]", $item['checklist'] == 'Done') ?></td>
                                    <td data-label="Condition"><?= status_select("rows[{$item['id']}][condition]", $item['item_condition']) ?></td>
                                    <td data-label="Remarks">
                                        <input type="text" name="rows[<?= $item['id'] ?>][remarks]" value="<?= htmlspecialchars($parsed['text']) ?>">
                                        <input type="hidden" name="rows[<?= $item['id'] ?>][existing_img]" value="<?= htmlspecialchars($parsed['img']) ?>">
                                        <?php if(!empty($parsed['img'])): ?>
                                            <div style="margin-top: 8px; display: inline-block;">
                                                <a href="<?= htmlspecialchars($parsed['img']) ?>" target="_blank" title="View Attached Photo">
                                                    <img src="<?= htmlspecialchars($parsed['img']) ?>" style="height: 42px; border-radius: 6px; border: 1px solid var(--border); box-shadow: 0 2px 4px rgba(0,0,0,0.08); display: block;">
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        </div>

                    <?php elseif ($section_name === 'DEVICES'): ?>
                        <?php if ($table === 'fireman_intercom_system'): ?>
                            <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr><th colspan="5" class="section-header" onclick="toggleAccordion(this)">
                                        <div class="accordion-toggle">
                                            <span><?= strtoupper($system_name . ' - ' . $section_name) ?></span>
                                            <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                        </div>
                                    </th></tr>
                                    <tr class="col-header-row">
                                        <th width="5%" class="center">BIL</th>
                                        <th width="40%">DESCRIPTION</th>
                                        <th width="10%" class="center">CHECKLIST</th>
                                        <th width="15%" class="center">CONDITION</th>
                                        <th width="30%">REMARKS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $current_main_bil = '';
                                    foreach ($items as $item): 
                                        $parsed = extract_remark_data($item['remarks'] ?? '');
                                        $bil_parts = explode('-', $item['item_bil']);
                                        if (count($bil_parts) > 1) {
                                            $main_bil = $bil_parts[0];
                                            $sub_bil = $bil_parts[1];
                                            if ($main_bil !== $current_main_bil) {
                                                echo '<tr class="group-row" style="background-color: var(--surface); border-top: 3px solid var(--border);">
                                                        <td class="center bold" data-label="BIL">1</td>
                                                        <td colspan="4" class="bold" style="color: var(--primary); font-size: 16px;">
                                                            FIREMAN INTERCOM HANDSET
                                                            <button type="button" class="btn-add-floor" onclick="addEditIntercomRow(\''.$table.'\', \'1\')" style="margin-left: 14px;">
                                                                + Add Floor / Handset
                                                            </button>
                                                        </td>
                                                      </tr>';
                                                $current_main_bil = $main_bil;
                                            }
                                        } else {
                                            $sub_bil = $item['item_bil'];
                                        }
                                    ?>
                                    <tr class="dev-row-<?= $table ?>-<?= count($bil_parts) > 1 ? $bil_parts[0] : $sub_bil ?>">
                                        <input type="hidden" name="rows[<?= $item['id'] ?>][table]" value="<?= htmlspecialchars($table) ?>">
                                        <input type="hidden" name="rows[<?= $item['id'] ?>][section]" value="<?= htmlspecialchars($section_name) ?>">
                                        
                                        <td class="center bold" data-label="BIL"><?= htmlspecialchars($sub_bil) ?></td>
                                        <?php if (count($bil_parts) > 1): ?>
                                            <td class="indent" data-label="Description/Floor"><input type="text" name="rows[<?= $item['id'] ?>][location_floor]" value="<?= htmlspecialchars($item['location_floor'] ?? '') ?>" placeholder="Location / Floor"></td>
                                        <?php else: ?>
                                            <td class="bold" data-label="Description"><?= htmlspecialchars($item['description']) ?></td>
                                        <?php endif; ?>
                                        <td class="center" data-label="Checklist"><?= checklist_select("rows[{$item['id']}][checklist]", $item['checklist'] == 'Done') ?></td>
                                        <td data-label="Condition"><?= status_select("rows[{$item['id']}][condition]", $item['item_condition']) ?></td>
                                        <td data-label="Remarks">
                                            <div class="remark-group">
                                                <input type="text" name="rows[<?= $item['id'] ?>][remarks]" value="<?= htmlspecialchars($parsed['text']) ?>">
                                                <?php if (count($bil_parts) > 1): ?>
                                                    <button type="button" class="btn-delete-row" title="Delete Row" onclick="deleteEditRow(this, '<?= $table ?>', '<?= $item['id'] ?>', '<?= $bil_parts[0] ?>', 'dev-row')">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                            <input type="hidden" name="rows[<?= $item['id'] ?>][existing_img]" value="<?= htmlspecialchars($parsed['img']) ?>">
                                            <?php if(!empty($parsed['img'])): ?>
                                                <div style="margin-top: 8px; display: inline-block;">
                                                    <a href="<?= htmlspecialchars($parsed['img']) ?>" target="_blank" title="View Attached Photo">
                                                        <img src="<?= htmlspecialchars($parsed['img']) ?>" style="height: 42px; border-radius: 6px; border: 1px solid var(--border); box-shadow: 0 2px 4px rgba(0,0,0,0.08); display: block;">
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr><th colspan="6" class="section-header" onclick="toggleAccordion(this)">
                                        <div class="accordion-toggle">
                                            <span><?= strtoupper($system_name . ' - ' . $section_name) ?></span>
                                            <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                        </div>
                                    </th></tr>
                                    <tr class="col-header-row">
                                        <th width="5%" class="center">BIL</th>
                                        <th width="40%">DESCRIPTION</th>
                                        <th width="15%" class="center">ZONE/LOOP</th>
                                        <th width="12%" class="center">CHECKLIST</th>
                                        <th width="12%" class="center">CONDITION</th>
                                        <th width="16%">REMARKS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $current_main_bil = '';
                                    foreach ($items as $item): 
                                        $parsed = extract_remark_data($item['remarks'] ?? '');
                                        $bil_parts = explode('-', $item['item_bil']);
                                        if (count($bil_parts) > 1) {
                                            $main_bil = $bil_parts[0];
                                            $sub_bil = $bil_parts[1];
                                            if ($main_bil !== $current_main_bil) {
                                                $btn_text = "+ Add Floor / Zone";
                                                if ($table === 'fire_alarm_system_add') $btn_text = "+ Add Floor / Loop";

                                                echo '<tr class="group-row" style="background-color: var(--surface); border-top: 3px solid var(--border);">
                                                        <td class="center bold" data-label="BIL">'.htmlspecialchars($main_bil).'</td>
                                                        <td colspan="5" class="bold" style="color: var(--primary); font-size: 16px;">
                                                            '.htmlspecialchars($item['description']).'
                                                            <button type="button" class="btn-add-floor" onclick="addEditFloorRow(\''.$table.'\', \''.$main_bil.'\', \''.addslashes($item['description']).'\')" style="margin-left: 14px;">
                                                                '.$btn_text.'
                                                            </button>
                                                        </td>
                                                      </tr>';
                                                $current_main_bil = $main_bil;
                                            }
                                        } else {
                                            $sub_bil = $item['item_bil'];
                                        }
                                    ?>
                                    <tr class="dev-row-<?= $table ?>-<?= count($bil_parts) > 1 ? $bil_parts[0] : $sub_bil ?>">
                                        <input type="hidden" name="rows[<?= $item['id'] ?>][table]" value="<?= htmlspecialchars($table) ?>">
                                        <input type="hidden" name="rows[<?= $item['id'] ?>][section]" value="<?= htmlspecialchars($section_name) ?>">
                                        
                                        <td class="center bold" data-label="BIL"><?= htmlspecialchars($sub_bil) ?></td>
                                        <?php if (count($bil_parts) > 1): ?>
                                            <td class="indent" data-label="Description"><input type="text" name="rows[<?= $item['id'] ?>][location_floor]" value="<?= htmlspecialchars($item['location_floor'] ?? '') ?>" placeholder="Location / Floor"></td>
                                        <?php else: ?>
                                            <td class="bold" data-label="Description"><?= htmlspecialchars($item['description']) ?></td>
                                        <?php endif; ?>
                                        <td class="center" data-label="Zone/Loop"><input type="text" name="rows[<?= $item['id'] ?>][zone_loop]" value="<?= htmlspecialchars($item['zone_loop'] ?? '') ?>"></td>
                                        <td class="center" data-label="Checklist"><?= checklist_select("rows[{$item['id']}][checklist]", $item['checklist'] == 'Done') ?></td>
                                        <td data-label="Condition"><?= status_select("rows[{$item['id']}][condition]", $item['item_condition']) ?></td>
                                        <td data-label="Remarks">
                                            <div class="remark-group">
                                                <input type="text" name="rows[<?= $item['id'] ?>][remarks]" value="<?= htmlspecialchars($parsed['text']) ?>">
                                                <?php if (count($bil_parts) > 1): ?>
                                                    <button type="button" class="btn-delete-row" title="Delete Row" onclick="deleteEditRow(this, '<?= $table ?>', '<?= $item['id'] ?>', '<?= $bil_parts[0] ?>', 'dev-row')">
                                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                            <input type="hidden" name="rows[<?= $item['id'] ?>][existing_img]" value="<?= htmlspecialchars($parsed['img']) ?>">
                                            <?php if(!empty($parsed['img'])): ?>
                                                <div style="margin-top: 8px; display: inline-block;">
                                                    <a href="<?= htmlspecialchars($parsed['img']) ?>" target="_blank" title="View Attached Photo">
                                                        <img src="<?= htmlspecialchars($parsed['img']) ?>" style="height: 42px; border-radius: 6px; border: 1px solid var(--border); box-shadow: 0 2px 4px rgba(0,0,0,0.08); display: block;">
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            </div>
                        <?php endif; ?>

                    <?php elseif ($section_name === 'SIGNAL TEST'): ?>
                        <div class="table-responsive">
                        <table>
                            <thead>
                                <tr><th colspan="5" class="section-header" onclick="toggleAccordion(this)">
                                    <div class="accordion-toggle">
                                        <span><?= strtoupper($system_name . ' - ' . $section_name) ?></span>
                                        <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                    </div>
                                </th></tr>
                                <tr class="col-header-row">
                                    <th width="5%" class="center">BIL</th>
                                    <th width="50%">DESCRIPTION</th>
                                    <th width="15%" class="center">CHECKLIST</th>
                                    <th width="15%" class="center">CONDITION</th>
                                    <th width="15%">REMARKS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $current_main_bil = '';
                                foreach ($items as $item): 
                                    $parsed = extract_remark_data($item['remarks'] ?? '');
                                    $bil_parts = explode('-', $item['item_bil']);
                                    $is_nested = count($bil_parts) > 1;
                                    if ($is_nested) {
                                        $main_bil = $bil_parts[0];
                                        $sub_bil = $bil_parts[1];
                                        $desc_parts = explode(' - ', $item['description'], 2);
                                        $main_desc = $desc_parts[0];
                                        $sub_desc = $desc_parts[1] ?? $main_desc;
                                        if ($main_bil !== $current_main_bil) {
                                            echo '<tr class="group-row" style="background-color: var(--surface); border-top: 2px solid var(--border);"><td class="center bold" data-label="BIL">'.htmlspecialchars($main_bil).'</td><td colspan="4" class="bold" style="color: var(--primary); font-size: 16px;">'.htmlspecialchars($main_desc).'</td></tr>';
                                            $current_main_bil = $main_bil;
                                        }
                                    } else {
                                        $sub_bil = $item['item_bil'];
                                        $sub_desc = $item['description'];
                                    }
                                ?>
                                <tr>
                                    <input type="hidden" name="rows[<?= $item['id'] ?>][table]" value="<?= htmlspecialchars($table) ?>">
                                    <input type="hidden" name="rows[<?= $item['id'] ?>][section]" value="<?= htmlspecialchars($section_name) ?>">
                                    
                                    <td class="center bold" data-label="BIL"><?= htmlspecialchars($sub_bil) ?></td>
                                    <td class="<?= $is_nested ? 'indent' : 'bold' ?>" data-label="Description"><?= htmlspecialchars($sub_desc) ?></td>
                                    <td class="center" data-label="Checklist"><?= checklist_select("rows[{$item['id']}][checklist]", $item['checklist'] == 'Done') ?></td>
                                    <td data-label="Condition"><?= status_select("rows[{$item['id']}][condition]", $item['item_condition']) ?></td>
                                    <td data-label="Remarks">
                                        <input type="text" name="rows[<?= $item['id'] ?>][remarks]" value="<?= htmlspecialchars($parsed['text']) ?>">
                                        <input type="hidden" name="rows[<?= $item['id'] ?>][existing_img]" value="<?= htmlspecialchars($parsed['img']) ?>">
                                        <?php if(!empty($parsed['img'])): ?>
                                            <div style="margin-top: 8px; display: inline-block;">
                                                <a href="<?= htmlspecialchars($parsed['img']) ?>" target="_blank" title="View Attached Photo">
                                                    <img src="<?= htmlspecialchars($parsed['img']) ?>" style="height: 42px; border-radius: 6px; border: 1px solid var(--border); box-shadow: 0 2px 4px rgba(0,0,0,0.08); display: block;">
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        </div>

                    <?php else: ?>
                        <div class="table-responsive">
                        <table>
                            <thead>
                                <tr><th colspan="5" class="section-header" onclick="toggleAccordion(this)">
                                    <div class="accordion-toggle">
                                        <span><?= strtoupper($system_name . ' - ' . $section_name) ?></span>
                                        <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                                    </div>
                                </th></tr>
                                <tr class="col-header-row">
                                    <th width="5%" class="center">BIL</th>
                                    <th width="50%">DESCRIPTION</th>
                                    <th width="12%" class="center">CHECKLIST</th>
                                    <th width="12%" class="center">CONDITION</th>
                                    <th width="21%">REMARKS</th>
                                </tr>
                                <?php if (strpos($section_name, 'PUMP TEST') !== false): ?>
                                    <tr class="group-row" style="background-color: var(--surface); border-top: 2px solid var(--border);">
                                        <td class="center bold" data-label="BIL"><?= strpos($section_name, 'MANUAL') !== false ? '1' : '2' ?></td>
                                        <td colspan="4" class="bold" style="color: var(--primary); font-size: 16px;"><?= strpos($section_name, 'MANUAL') !== false ? 'MANUAL TEST FOR:' : 'AUTO TEST FOR:' ?></td>
                                    </tr>
                                <?php endif; ?>
                            </thead>
                            <tbody>
                                <?php foreach ($items as $item): 
                                    $parsed = extract_remark_data($item['remarks'] ?? '');
                                ?>
                                <tr>
                                    <input type="hidden" name="rows[<?= $item['id'] ?>][table]" value="<?= htmlspecialchars($table) ?>">
                                    <input type="hidden" name="rows[<?= $item['id'] ?>][section]" value="<?= htmlspecialchars($section_name) ?>">
                                    
                                    <td class="center bold" data-label="BIL"><?= htmlspecialchars($item['item_bil']) ?></td>
                                    <td class="<?= strpos($section_name, 'PUMP TEST') !== false ? 'indent' : 'bold' ?>" data-label="Description"><?= htmlspecialchars($item['description']) ?></td>
                                    <td class="center" data-label="Checklist"><?= checklist_select("rows[{$item['id']}][checklist]", $item['checklist'] == 'Done') ?></td>
                                    <td data-label="Condition"><?= status_select("rows[{$item['id']}][condition]", $item['item_condition']) ?></td>
                                    <td data-label="Remarks">
                                        <input type="text" name="rows[<?= $item['id'] ?>][remarks]" value="<?= htmlspecialchars($parsed['text']) ?>">
                                        <input type="hidden" name="rows[<?= $item['id'] ?>][existing_img]" value="<?= htmlspecialchars($parsed['img']) ?>">
                                        <?php if(!empty($parsed['img'])): ?>
                                            <div style="margin-top: 8px; display: inline-block;">
                                                <a href="<?= htmlspecialchars($parsed['img']) ?>" target="_blank" title="View Attached Photo">
                                                    <img src="<?= htmlspecialchars($parsed['img']) ?>" style="height: 42px; border-radius: 6px; border: 1px solid var(--border); box-shadow: 0 2px 4px rgba(0,0,0,0.08); display: block;">
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
        <?php endforeach; ?>

        <div class="save-bar">
            <button type="submit" class="btn-save">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="width: 24px; height: 24px;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                Save All Changes
            </button>
        </div>
    </form>
</div>

<?php
function status_select($name, $selected = 'Not Applicable') {
    $html = '<select name="' . htmlspecialchars($name) . '" class="cond-select">';
    foreach (['Not Applicable', 'Normal', 'Faulty'] as $opt) {
        $sel = ($selected == $opt) ? 'selected' : '';
        $html .= "<option value=\"$opt\" $sel>$opt</option>";
    }
    $html .= '</select>';
    return $html;
}

function checklist_select($name, $checked = false) {
    $id = 'chk_' . md5($name . uniqid());
    $chk = $checked ? 'checked' : '';
    return '<div class="chk-wrap"><input type="checkbox" name="' . htmlspecialchars($name) . '" id="' . $id . '" value="Done" ' . $chk . '><label for="' . $id . '"></label></div>';
}
?>

<script>
function toggleAccordion(headerEl) {
    const wrapper = headerEl.closest('.table-responsive');
    wrapper.classList.toggle('collapsed');
}

function setAllAccordions(expand) {
    document.querySelectorAll('.table-responsive').forEach(function (wrapper) {
        wrapper.classList.toggle('collapsed', !expand);
    });
}

const romanNumerals = ["I","II","III","IV","V","VI","VII","VIII","IX","X","XI","XII","XIII","XIV","XV","XVI","XVII","XVIII","XIX","XX"];
let newRowCounter = 0;

function addEditFloorRow(table, mainBil, description) {
    const rows = document.querySelectorAll(`.dev-row-${table}-${mainBil}`);
    const nextIndex = rows.length;
    
    if (nextIndex >= romanNumerals.length) {
        alert("Maximum limit reached for this item.");
        return;
    }

    const roman = romanNumerals[nextIndex];
    const lastRow = rows[rows.length - 1];
    const uniqueIdx = 'new_' + (++newRowCounter);
    const uniqueId = 'chk_edit_' + table + '_' + mainBil + '_' + uniqueIdx;

    const newRow = document.createElement('tr');
    newRow.className = `dev-row-${table}-${mainBil}`;
    newRow.innerHTML = `
        <input type="hidden" name="new_rows[${table}][${uniqueIdx}][section]" value="DEVICES">
        <input type="hidden" name="new_rows[${table}][${uniqueIdx}][item_bil]" value="${mainBil}-${roman}">
        <input type="hidden" name="new_rows[${table}][${uniqueIdx}][description]" value="${description}">
        
        <td class="center bold" data-label="BIL">${roman}</td>
        <td class="indent" data-label="Description"><input type="text" name="new_rows[${table}][${uniqueIdx}][location_floor]" placeholder="Location / Floor"></td>
        <td class="center" data-label="Zone/Loop"><input type="text" name="new_rows[${table}][${uniqueIdx}][zone_loop]"></td>
        <td class="center" data-label="Checklist">
            <div class="chk-wrap">
                <input type="checkbox" name="new_rows[${table}][${uniqueIdx}][checklist]" id="${uniqueId}" value="Done">
                <label for="${uniqueId}"></label>
            </div>
        </td>
        <td data-label="Condition">
            <select name="new_rows[${table}][${uniqueIdx}][condition]" class="cond-select">
                <option value="Not Applicable">Not Applicable</option>
                <option value="Normal">Normal</option>
                <option value="Faulty">Faulty</option>
            </select>
        </td>
        <td data-label="Remarks">
            <div class="remark-group">
                <input type="text" name="new_rows[${table}][${uniqueIdx}][remarks]">
                <button type="button" class="btn-delete-row" title="Delete Row" onclick="deleteNewEditRow(this, '${table}', '${mainBil}', 'dev-row')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                </button>
            </div>
        </td>
    `;

    lastRow.parentNode.insertBefore(newRow, lastRow.nextSibling);
    reindexEditRows(table, mainBil, 'dev-row');
}

function addEditIntercomRow(table, mainBil) {
    const rows = document.querySelectorAll(`.dev-row-${table}-${mainBil}`);
    const nextIndex = rows.length;
    
    if (nextIndex >= romanNumerals.length) {
        alert("Maximum limit reached for this item.");
        return;
    }

    const roman = romanNumerals[nextIndex];
    const lastRow = rows[rows.length - 1];
    const uniqueIdx = 'new_' + (++newRowCounter);
    const uniqueId = 'chk_edit_' + table + '_' + mainBil + '_' + uniqueIdx;

    const newRow = document.createElement('tr');
    newRow.className = `dev-row-${table}-${mainBil}`;
    newRow.innerHTML = `
        <input type="hidden" name="new_rows[${table}][${uniqueIdx}][section]" value="DEVICES">
        <input type="hidden" name="new_rows[${table}][${uniqueIdx}][item_bil]" value="${mainBil}-${roman}">
        <input type="hidden" name="new_rows[${table}][${uniqueIdx}][description]" value="FIREMAN INTERCOM HANDSET">
        
        <td class="center bold" data-label="BIL">${roman}</td>
        <td class="indent" data-label="Description/Floor"><input type="text" name="new_rows[${table}][${uniqueIdx}][location_floor]" placeholder="Location / Floor"></td>
        <td class="center" data-label="Checklist">
            <div class="chk-wrap">
                <input type="checkbox" name="new_rows[${table}][${uniqueIdx}][checklist]" id="${uniqueId}" value="Done">
                <label for="${uniqueId}"></label>
            </div>
        </td>
        <td data-label="Condition">
            <select name="new_rows[${table}][${uniqueIdx}][condition]" class="cond-select">
                <option value="Not Applicable">Not Applicable</option>
                <option value="Normal">Normal</option>
                <option value="Faulty">Faulty</option>
            </select>
        </td>
        <td data-label="Remarks">
            <div class="remark-group">
                <input type="text" name="new_rows[${table}][${uniqueIdx}][remarks]">
                <button type="button" class="btn-delete-row" title="Delete Row" onclick="deleteNewEditRow(this, '${table}', '${mainBil}', 'dev-row')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                </button>
            </div>
        </td>
    `;

    lastRow.parentNode.insertBefore(newRow, lastRow.nextSibling);
    reindexEditRows(table, mainBil, 'dev-row');
}

function addEditSprinklerRow(table, mainBil) {
    const rows = document.querySelectorAll(`.eq-row-${table}-${mainBil}`);
    const nextIndex = rows.length;
    
    if (nextIndex >= romanNumerals.length) {
        alert("Maximum limit reached for this item.");
        return;
    }

    const roman = romanNumerals[nextIndex];
    const lastRow = rows[rows.length - 1];
    const uniqueIdx = 'new_' + (++newRowCounter);
    const uniqueId = 'chk_edit_spk_' + mainBil + '_' + uniqueIdx;

    const newRow = document.createElement('tr');
    newRow.className = `eq-row-${table}-${mainBil}`;
    newRow.innerHTML = `
        <input type="hidden" name="new_rows[${table}][${uniqueIdx}][section]" value="EQUIPMENT">
        <input type="hidden" name="new_rows[${table}][${uniqueIdx}][item_bil]" value="${mainBil}-${roman}">
        
        <td class="center bold" data-label="BIL">${roman}</td>
        <td class="indent" data-label="Description"><input type="text" name="new_rows[${table}][${uniqueIdx}][description]" placeholder="Location / Floor"></td>
        <td class="center" data-label="Checklist">
            <div class="chk-wrap">
                <input type="checkbox" name="new_rows[${table}][${uniqueIdx}][checklist]" id="${uniqueId}" value="Done">
                <label for="${uniqueId}"></label>
            </div>
        </td>
        <td data-label="Condition">
            <select name="new_rows[${table}][${uniqueIdx}][condition]" class="cond-select">
                <option value="Not Applicable">Not Applicable</option>
                <option value="Normal">Normal</option>
                <option value="Faulty">Faulty</option>
            </select>
        </td>
        <td data-label="Remarks">
            <div class="remark-group">
                <input type="text" name="new_rows[${table}][${uniqueIdx}][remarks]">
                <button type="button" class="btn-delete-row" title="Delete Row" onclick="deleteNewEditRow(this, '${table}', '${mainBil}', 'eq-row')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                </button>
            </div>
        </td>
    `;

    lastRow.parentNode.insertBefore(newRow, lastRow.nextSibling);
    reindexEditRows(table, mainBil, 'eq-row');
}

function deleteEditRow(button, table, rowId, mainBil, prefix = 'dev-row') {
    if (confirm("Are you sure you want to delete this row?")) {
        const container = document.getElementById('deletedContainer');
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = `deleted_ids[${table}][]`;
        input.value = rowId;
        container.appendChild(input);

        const row = button.closest('tr');
        row.remove();
        reindexEditRows(table, mainBil, prefix);
    }
}

function deleteNewEditRow(button, table, mainBil, prefix = 'dev-row') {
    const row = button.closest('tr');
    row.remove();
    reindexEditRows(table, mainBil, prefix);
}

function reindexEditRows(table, mainBil, prefix) {
    const rows = document.querySelectorAll(`.${prefix}-${table}-${mainBil}`);
    rows.forEach((row, index) => {
        const roman = romanNumerals[index];
        row.querySelector('td:first-child').textContent = roman;
        
        const bilInput = row.querySelector(`input[name*="[item_bil]"]`);
        if (bilInput) {
            bilInput.value = `${mainBil}-${roman}`;
        }
    });
}
</script>

</body>
</html>