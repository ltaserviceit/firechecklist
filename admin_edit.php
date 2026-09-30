<?php
session_start();

// Security: Only Admin can access this
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

require 'db.php';

$task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) : 0;

// Fetch task info
$task_result = $conn->query("SELECT * FROM tasks WHERE task_id = $task_id");
if ($task_result->num_rows == 0) {
    die("<div style='text-align:center; padding:50px; font-family:Arial;'>
            <h2 style='color:#d9534f;'>Error</h2>
            <p>Task not found.</p>
            <a href='admin_dashboard.php'>Return to Dashboard</a>
         </div>");
}
$task = $task_result->fetch_assoc();

// All 13 system tables
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

// Helper to parse images from remarks
function extract_remark_data($remark_string) {
    $text = $remark_string;
    $img = "";
    if (preg_match('/\[IMG: (.*?)\]/', $remark_string, $matches)) {
        $img = $matches[1];
        $text = str_replace($matches[0], '', $remark_string);
    }
    return ['text' => trim($text), 'img' => $img];
}

// Handle POST save
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

    // 2. Update task info (Including Tech ID)
    $company  = $conn->real_escape_string($_POST['company_name']);
    $building = $conn->real_escape_string($_POST['building_name']);
    $tech_id  = $conn->real_escape_string($_POST['tech_id']);
    $status   = $conn->real_escape_string($_POST['status']);
    
    $conn->query("UPDATE tasks SET company_name='$company', building_name='$building', tech_id='$tech_id', status='$status' WHERE task_id=$task_id");

    // 3. Update Checklist Data for Existing Rows
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
                
            } else {
                $cond = $conn->real_escape_string($fields['condition'] ?? '');
                $rem  = $fields['remarks'] ?? '';
                
                $existing_img = $fields['existing_img'] ?? '';
                $delete_img = isset($fields['delete_img']);
                
                $final_img = $existing_img;
                if ($delete_img) $final_img = ""; 
                
                if (isset($_FILES['new_img']['error'][$row_id]) && $_FILES['new_img']['error'][$row_id] === UPLOAD_ERR_OK) {
                    $tmp = $_FILES['new_img']['tmp_name'][$row_id];
                    $name = $_FILES['new_img']['name'][$row_id];
                    $clean_name = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "", basename($name));
                    $path = 'uploads/' . $clean_name;
                    if (!is_dir('uploads')) mkdir('uploads', 0777, true);
                    if (move_uploaded_file($tmp, $path)) {
                        $final_img = $path;
                    }
                }
                
                if (!empty($final_img)) {
                    $rem .= " [IMG: " . $final_img . "]";
                }
                $rem = $conn->real_escape_string($rem);

                if (in_array($section, ['SYSTEM INFO', 'PUMP INFO', 'PUMP PRESSURE'])) {
                    if ($table === 'fire_hose_reel_system' && isset($fields['duty']) && isset($fields['standby'])) {
                        $duty = $conn->real_escape_string($fields['duty']);
                        $standby = $conn->real_escape_string($fields['standby']);
                        $cl = "DUTY: $duty | STANDBY: $standby";
                        $conn->query("UPDATE `$table` SET checklist='$cl', remarks='$rem' WHERE id=$row_id AND task_id=$task_id");
                    } elseif (in_array($table, ['fire_sprinkler_system', 'wet_riser_system', 'pressurised_hydrant_system']) && $section === 'PUMP PRESSURE') {
                        $cl = $conn->real_escape_string($fields['checklist'] ?? '');
                        $conn->query("UPDATE `$table` SET checklist='$cl' WHERE id=$row_id AND task_id=$task_id");
                        if (!empty($fields['is_primary']) && !empty($fields['group_bil'])) {
                            $base_bil = $conn->real_escape_string($fields['group_bil']);
                            $conn->query("UPDATE `$table` SET remarks='$rem' WHERE task_id=$task_id AND section_name='PUMP PRESSURE' AND item_bil LIKE '$base_bil-%'");
                        }
                    } else {
                        $cl = $conn->real_escape_string($fields['checklist'] ?? '');
                        $conn->query("UPDATE `$table` SET checklist='$cl', remarks='$rem' WHERE id=$row_id AND task_id=$task_id");
                    }
                } elseif ($section === 'DEVICES') {
                    $cl   = isset($fields['checklist']) ? 'Done' : 'N/A';
                    $zl   = $conn->real_escape_string($fields['zone_loop'] ?? '');
                    $loc  = $conn->real_escape_string($fields['location_floor'] ?? '');
                    $conn->query("UPDATE `$table` SET location_floor='$loc', zone_loop='$zl', checklist='$cl', item_condition='$cond', remarks='$rem' WHERE id=$row_id AND task_id=$task_id");
                } elseif ($section === 'EQUIPMENT') {
                    $desc = $conn->real_escape_string($fields['description'] ?? '');
                    $cl   = isset($fields['checklist']) ? 'Done' : 'N/A';
                    $conn->query("UPDATE `$table` SET description='$desc', checklist='$cl', item_condition='$cond', remarks='$rem' WHERE id=$row_id AND task_id=$task_id");
                } else {
                    $cl   = isset($fields['checklist']) ? 'Done' : 'N/A';
                    $conn->query("UPDATE `$table` SET checklist='$cl', item_condition='$cond', remarks='$rem' WHERE id=$row_id AND task_id=$task_id");
                }
            }
        }
    }

    // 4. Process new dynamic rows added during edit
    if (!empty($_POST['new_rows'])) {
        foreach ($_POST['new_rows'] as $tbl => $nrows) {
            $tbl_clean = $conn->real_escape_string($tbl);
            foreach ($nrows as $unique_idx => $nfields) {
                $sec  = $conn->real_escape_string($nfields['section'] ?? 'DEVICES');
                $bil  = $conn->real_escape_string($nfields['item_bil'] ?? '');
                $desc = $conn->real_escape_string($nfields['description'] ?? '');
                $loc  = $conn->real_escape_string($nfields['location_floor'] ?? '');
                $zl   = $conn->real_escape_string($nfields['zone_loop'] ?? '');
                $cl   = isset($nfields['checklist']) ? 'Done' : 'N/A';
                $cond = $conn->real_escape_string($nfields['condition'] ?? '');
                $rem  = $nfields['remarks'] ?? '';

                if (isset($_FILES['new_row_img']['error'][$tbl][$unique_idx]) && $_FILES['new_row_img']['error'][$tbl][$unique_idx] === UPLOAD_ERR_OK) {
                    $tmp = $_FILES['new_row_img']['tmp_name'][$tbl][$unique_idx];
                    $name = $_FILES['new_row_img']['name'][$tbl][$unique_idx];
                    $clean_name = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "", basename($name));
                    $path = 'uploads/' . $clean_name;
                    if (!is_dir('uploads')) mkdir('uploads', 0777, true);
                    if (move_uploaded_file($tmp, $path)) {
                        $rem .= " [IMG: " . $path . "]";
                    }
                }
                $rem = $conn->real_escape_string($rem);

                $conn->query("INSERT INTO `$tbl_clean` (task_id, section_name, item_bil, description, location_floor, zone_loop, checklist, item_condition, remarks) 
                    VALUES ($task_id, '$sec', '$bil', '$desc', '$loc', '$zl', '$cl', '$cond', '$rem')");
            }
        }
    }
    
    header("Location: admin_dashboard.php?edited=1");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Edit Task #<?php echo $task_id; ?> - FireSafe Admin</title>
    <style>
        :root {
            --primary: #C41E3A;
            --primary-hover: #a8182f;
            --bg-color: #f4f7f6;
            --surface: #ffffff;
            --text-main: #1a1a1a;
            --text-muted: #6b7280;
            --border: #e5e7eb;
            --success: #10b981;
            --row-alt: #f9fafb;
            --row-hover: #f3f4f6;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: linear-gradient(135deg, #f0f2f5 0%, #e1e4e8 100%); color: var(--text-main); display: flex; min-height: 100vh; }

        .sidebar { width: 260px; background-color: var(--surface); border-right: 1.5px solid var(--border); display: flex; flex-direction: column; position: fixed; height: 100vh; z-index: 100; transition: all 0.3s ease; }
        .brand { padding: 30px 24px; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid var(--border); }
        .brand-icon { width: 36px; height: 36px; border-radius: 8px; background: var(--primary); display: flex; align-items: center; justify-content: center; flex-shrink: 0; box-shadow: 0 4px 10px rgba(196, 30, 58, 0.3); }
        .brand-icon svg { width: 18px; height: 18px; stroke: #fff; }
        .brand-name { font-size: 15px; font-weight: 700; color: var(--text-main); line-height: 1.2; }
        .brand-name span { display: block; font-size: 11px; font-weight: 400; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        
        .nav-links { padding: 24px 12px; display: flex; flex-direction: column; gap: 8px; }
        .sidebar a { display: flex; align-items: center; gap: 10px; color: var(--text-main); padding: 12px 16px; text-decoration: none; font-weight: 500; font-size: 14px; border-radius: 8px; transition: background 0.2s, color 0.2s; }
        .sidebar a:hover { background-color: var(--bg-color); }
        
        .logout { margin-top: auto; margin-bottom: 24px; margin-left: 12px; margin-right: 12px; color: #dc2626 !important; background: #fef2f2; border: 1px solid #fecaca; }
        .logout:hover { background-color: #fca5a5 !important; color: white !important; }

        .main-content { margin-left: 260px; padding: clamp(20px, 4vw, 40px); width: calc(100% - 260px); display: flex; flex-direction: column; align-items: center; }
        .center-wrapper { width: 100%; max-width: 1300px; padding-bottom: 80px; }
        
        /* ============ POLISHED BACK BUTTON ============ */
        .header-section { margin-bottom: 24px; display: flex; flex-direction: column; gap: 8px; }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--surface);
            color: var(--text-main);
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 8px;
            border: 1.5px solid var(--border);
            font-weight: 600;
            font-size: 14px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            transition: all 0.2s ease;
            width: fit-content;
        }
        .back-link:hover {
            background: var(--row-hover);
            border-color: #cbd5e1;
            transform: translateX(-3px);
            color: var(--primary);
        }
        .back-link svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            stroke: currentColor;
            transition: transform 0.2s ease;
        }
        .back-link:hover svg {
            transform: translateX(-2px);
        }

        .task-info-box { background: var(--surface); padding: 24px; border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 4px 15px rgba(0,0,0,0.03); margin-bottom: 30px; border-top: 5px solid #f59e0b; }
        .task-info-box h2 { margin: 0 0 20px 0; color: var(--text-main); font-size: 20px; font-weight: 700; display: flex; align-items: center; gap: 10px; }
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; }
        .info-grid label { display: block; font-weight: 600; color: var(--text-muted); margin-bottom: 8px; font-size: 13px; text-transform: uppercase; letter-spacing: 0.5px; }

        .system-separator { background: var(--surface); color: var(--primary); text-align: left; padding: 18px 24px; margin-top: 50px; margin-bottom: 25px; font-size: 16px; font-weight: 700; text-transform: uppercase; border-radius: 8px; border: 1px solid var(--border); border-left: 6px solid var(--primary); box-shadow: 0 4px 10px rgba(0,0,0,0.03); letter-spacing: 0.5px; }
        
        .table-responsive { overflow-x: auto; width: 100%; margin-bottom: 30px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.03); background: var(--surface); border: 1px solid var(--border); }
        table { width: 100%; border-collapse: collapse; min-width: 900px; margin: 0; }
        th, td { border-bottom: 1px solid var(--border); padding: 14px 16px; text-align: left; vertical-align: middle; font-size: 14px; }
        th { background: var(--row-alt); text-align: center; font-weight: 600; color: var(--text-muted); text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; }
        th.section-header { background: var(--surface) !important; color: var(--text-main) !important; font-size: 15px; padding: 18px 16px; font-weight: 700; border-bottom: 2px solid var(--border); border-top: none; }
        th.sub-header { background: var(--surface) !important; color: var(--text-muted) !important; font-size: 13px; padding: 12px 16px; border-bottom: 2px solid var(--border); }
        
        tbody tr:nth-child(even) td { background-color: var(--row-alt); }
        tbody tr:hover td { background-color: var(--row-hover); }

        input[type="text"], select { width: 100%; padding: 10px 12px; border: 1.5px solid var(--border); border-radius: 6px; font-size: 14px; box-sizing: border-box; transition: all 0.2s; background: var(--surface); color: var(--text-main); font-weight: 500; }
        input[type="text"]:focus, select:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(196, 30, 58, 0.15); }
        input[type="file"] { font-size: 11px; color: var(--text-muted); }

        .split-edit-press { display: flex; gap: 8px; width: 100%; }
        .split-edit-press div { flex: 1; display: flex; align-items: center; gap: 4px; }
        .split-edit-press label { font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; }

        .btn-add-floor {
            background: #f1f5f9; color: var(--primary); border: 1.5px dashed var(--border);
            padding: 6px 12px; border-radius: 6px; font-weight: 700; font-size: 12px;
            cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
            transition: all 0.2s;
        }
        .btn-add-floor:hover {
            background: #e2e8f0; border-color: var(--primary);
        }

        .btn-delete-row {
            background: #fee2e2; border: 1.5px solid #fca5a5; color: #dc2626;
            border-radius: 6px; padding: 10px; cursor: pointer;
            display: inline-flex; align-items: center; justify-content: center;
            transition: all 0.2s; flex-shrink: 0;
        }
        .btn-delete-row:hover {
            background: #ef4444; border-color: #ef4444; color: white;
        }
        .btn-delete-row svg { width: 18px; height: 18px; stroke: currentColor; }

        .chk-wrap { display: flex; align-items: center; justify-content: center; }
        .chk-wrap input[type="checkbox"] { display: none; }
        .chk-wrap label.chk-icon { width: 30px; height: 30px; border: 2px solid #cbd5e1; border-radius: 6px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 18px; color: transparent; background: var(--surface); transition: all 0.2s; font-weight: 800; box-shadow: inset 0 1px 2px rgba(0,0,0,0.05); }
        .chk-wrap label.chk-icon::after { content: "✓"; }
        .chk-wrap input[type="checkbox"]:checked + label.chk-icon { background: var(--success); border-color: var(--success); color: white; box-shadow: 0 2px 6px rgba(16, 185, 129, 0.2); }

        .save-bar { position: fixed; bottom: 0; left: 260px; width: calc(100% - 260px); background: rgba(255,255,255,0.95); padding: 20px 0; border-top: 1px solid var(--border); text-align: center; z-index: 10; box-shadow: 0 -4px 20px rgba(0,0,0,0.05); backdrop-filter: blur(8px); }
        .btn-save { background: var(--primary); color: white; border: none; padding: 14px 40px; font-size: 16px; font-weight: 700; border-radius: 8px; cursor: pointer; transition: all 0.2s; box-shadow: 0 4px 12px rgba(196,30,58,0.25); display: inline-flex; align-items: center; gap: 8px; }
        .btn-save:hover { background: var(--primary-hover); transform: translateY(-2px); box-shadow: 0 6px 16px rgba(196,30,58,0.3); }
        
        .center { text-align: center; }
        .bold { font-weight: 600; color: var(--text-main); }
        .indent { padding-left: 24px !important; color: var(--text-muted); }

        @media (max-width: 860px) {
            body { flex-direction: column; }
            .sidebar { position: relative; width: 100%; height: auto; border-right: none; border-bottom: 1.5px solid var(--border); }
            .brand { justify-content: center; border-bottom: none; padding: 20px; }
            .nav-links { flex-direction: row; flex-wrap: wrap; justify-content: center; padding: 0 20px 20px; }
            .logout { margin: 0; }
            .main-content { margin-left: 0; width: 100%; padding: 20px; }
            .save-bar { left: 0; width: 100%; }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="brand">
        <div class="brand-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/>
            </svg>
        </div>
        <div class="brand-name">
            FireSafe Admin
            <span>Dashboard</span>
        </div>
    </div>

    <div class="nav-links">
        <a href="admin_dashboard.php">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg>
            Overview
        </a>
        <a href="admin_create.php">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Create Task
        </a>
        <a href="logout.php" class="logout" onclick="return confirm('Are you sure you want to logout?');">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
            Sign Out
        </a>
    </div>
</div>

<div class="main-content">
    <div class="center-wrapper">
        
        <div class="header-section">
            <a href="admin_dashboard.php" class="back-link">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                Back to Dashboard
            </a>
        </div>

        <form method="POST" enctype="multipart/form-data" id="editForm">
            <div id="deletedContainer"></div>

            <div class="task-info-box">
                <h2>
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="stroke: #f59e0b;"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                    Edit Task #<?php echo str_pad($task_id, 5, '0', STR_PAD_LEFT); ?>
                </h2>
                <div class="info-grid">
                    <div class="field">
                        <label>Company Name</label>
                        <input type="text" name="company_name" value="<?php echo htmlspecialchars($task['company_name']); ?>" required>
                    </div>
                    <div class="field">
                        <label>Building Name</label>
                        <input type="text" name="building_name" value="<?php echo htmlspecialchars($task['building_name']); ?>" required>
                    </div>
                    <div class="field">
                        <label>Technician ID</label>
                        <input type="text" name="tech_id" value="<?php echo htmlspecialchars($task['tech_id'] ?? ''); ?>" placeholder="e.g. TECH01">
                    </div>
                    <div class="field">
                        <label>Status</label>
                        <select name="status">
                            <option value="Pending" <?php echo $task['status'] == 'Pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="Completed" <?php echo $task['status'] == 'Completed' ? 'selected' : ''; ?>>Completed</option>
                        </select>
                    </div>
                </div>
            </div>

            <?php foreach ($systems as $table => $system_name): ?>
                <?php
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
                    if (empty($loc_label) && count($submissions) > 1) { $loc_label = " - SUBMISSION " . ($sub_idx + 1); }
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
                                        <tr><th colspan="4" class="section-header"><?= strtoupper($system_name) ?></th></tr>
                                        <tr><th width="10%">BIL</th><th width="40%">LOCATION</th><th width="30%">STACK</th><th width="20%">NO. OF STACK</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $item): ?>
                                        <tr>
                                            <input type="hidden" name="rows[<?= $item['id'] ?>][table]" value="<?= htmlspecialchars($table) ?>">
                                            <input type="hidden" name="rows[<?= $item['id'] ?>][section]" value="<?= htmlspecialchars($section_name) ?>">
                                            <input type="hidden" name="rows[<?= $item['id'] ?>][description]" value="<?= htmlspecialchars($item['description'] ?? 'PANEL') ?>">
                                            
                                            <td class="center bold"><?= htmlspecialchars($item['item_bil']) ?></td>
                                            <td><input type="text" name="rows[<?= $item['id'] ?>][location_floor]" value="<?= htmlspecialchars($item['location_floor'] ?? '') ?>"></td>
                                            <td><input type="text" name="rows[<?= $item['id'] ?>][panel_type]" value="<?= htmlspecialchars($item['panel_type'] ?? '') ?>"></td>
                                            <td><input type="text" name="rows[<?= $item['id'] ?>][panel_qty]" value="<?= htmlspecialchars($item['panel_qty'] ?? '') ?>"></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                </div>
                            <?php elseif (strpos($table, 'suppression') !== false || $table === 'wet_chemical_system'): ?>
                                <div class="table-responsive">
                                <table>
                                    <thead>
                                        <tr><th colspan="4" class="section-header"><?= strtoupper($system_name) ?></th></tr>
                                        <tr><th colspan="4" class="sub-header"><?= strtoupper($system_name) ?></th></tr>
                                        <tr><th width="10%">BIL</th><th width="40%">LOCATION/ROOM</th><th width="25%">CYLINDER CAPACITY (KG)</th><th width="25%">QUANTITY OF CYLINDER</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($items as $item): ?>
                                        <tr>
                                            <input type="hidden" name="rows[<?= $item['id'] ?>][table]" value="<?= htmlspecialchars($table) ?>">
                                            <input type="hidden" name="rows[<?= $item['id'] ?>][section]" value="<?= htmlspecialchars($section_name) ?>">
                                            <input type="hidden" name="rows[<?= $item['id'] ?>][description]" value="<?= htmlspecialchars($item['description'] ?? 'PANEL') ?>">
                                            
                                            <td class="center bold"><?= htmlspecialchars($item['item_bil']) ?></td>
                                            <td><input type="text" name="rows[<?= $item['id'] ?>][location_floor]" value="<?= htmlspecialchars($item['location_floor'] ?? '') ?>"></td>
                                            <td><input type="text" name="rows[<?= $item['id'] ?>][panel_type]" value="<?= htmlspecialchars($item['panel_type'] ?? '') ?>"></td>
                                            <td><input type="text" name="rows[<?= $item['id'] ?>][panel_qty]" value="<?= htmlspecialchars($item['panel_qty'] ?? '') ?>"></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                </div>
                            <?php elseif ($table === 'fire_alarm_system_add' || $table === 'fire_alarm_system_con' || $table === 'fireman_intercom_system'): ?>
                                <div class="table-responsive">
                                <table>
                                    <thead>
                                        <tr><th colspan="6" class="section-header"><?= strtoupper($system_name) ?> CONTROL PANEL PROFILE</th></tr>
                                        <tr>
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
                                            
                                            <td class="center bold"><?= htmlspecialchars($item['item_bil']) ?></td>
                                            <td>
                                                <select name="rows[<?= $item['id'] ?>][description]">
                                                    <option value="" <?= empty($item['description']) ? 'selected' : '' ?>>-- Select --</option>
                                                    <option value="Main Panel" <?= ($item['description'] == 'Main Panel') ? 'selected' : '' ?>>Main Panel</option>
                                                    <option value="Sub Panel" <?= ($item['description'] == 'Sub Panel') ? 'selected' : '' ?>>Sub Panel</option>
                                                </select>
                                            </td>
                                            <td>
                                                <select name="rows[<?= $item['id'] ?>][panel_type]">
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
                                            <td><input type="text" name="rows[<?= $item['id'] ?>][panel_brand]" value="<?= htmlspecialchars($item['panel_brand'] ?? '') ?>"></td>
                                            <td class="center"><input type="text" name="rows[<?= $item['id'] ?>][panel_qty]" value="<?= htmlspecialchars($item['panel_qty'] ?? '') ?>"></td>
                                            <td><input type="text" name="rows[<?= $item['id'] ?>][location_floor]" value="<?= htmlspecialchars($item['location_floor'] ?? '') ?>"></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                <table>
                                    <thead>
                                        <tr><th colspan="7" class="section-header"><?= strtoupper($system_name) ?> CONTROL PANEL PROFILE</th></tr>
                                        <tr>
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
                                            
                                            <td class="center bold"><?= htmlspecialchars($item['item_bil']) ?></td>
                                            <td><input type="text" name="rows[<?= $item['id'] ?>][description]" value="<?= htmlspecialchars($item['description'] ?? '') ?>"></td>
                                            <td><input type="text" name="rows[<?= $item['id'] ?>][panel_type]" value="<?= htmlspecialchars($item['panel_type'] ?? '') ?>"></td>
                                            <td><input type="text" name="rows[<?= $item['id'] ?>][panel_brand]" value="<?= htmlspecialchars($item['panel_brand'] ?? '') ?>"></td>
                                            <td><input type="text" name="rows[<?= $item['id'] ?>][panel_model]" value="<?= htmlspecialchars($item['panel_model'] ?? '') ?>"></td>
                                            <td class="center"><input type="text" name="rows[<?= $item['id'] ?>][panel_qty]" value="<?= htmlspecialchars($item['panel_qty'] ?? '') ?>"></td>
                                            <td><input type="text" name="rows[<?= $item['id'] ?>][location_floor]" value="<?= htmlspecialchars($item['location_floor'] ?? '') ?>"></td>
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
                                    <tr><th colspan="6" class="section-header"><?= strtoupper($system_name . ' - ' . $section_name) ?></th></tr>
                                    <tr>
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
                                        <td class="center bold"><?= htmlspecialchars($base_bil) ?></td>
                                        <td class="bold"><?= htmlspecialchars($valve_name) ?></td>
                                        
                                        <?php foreach (['jockey', 'duty', 'standby'] as $col): ?>
                                            <?php $sub_item = $types[$col] ?? null; ?>
                                            <td class="center">
                                                <?php if ($sub_item): ?>
                                                    <input type="hidden" name="rows[<?= $sub_item['id'] ?>][table]" value="<?= htmlspecialchars($table) ?>">
                                                    <input type="hidden" name="rows[<?= $sub_item['id'] ?>][section]" value="<?= htmlspecialchars($section_name) ?>">
                                                    <input type="text" name="rows[<?= $sub_item['id'] ?>][checklist]" value="<?= htmlspecialchars($sub_item['checklist'] ?? '') ?>">
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                        <?php endforeach; ?>
                                        
                                        <td>
                                            <input type="hidden" name="rows[<?= $first_item['id'] ?>][is_primary]" value="1">
                                            <input type="hidden" name="rows[<?= $first_item['id'] ?>][group_bil]" value="<?= htmlspecialchars($base_bil) ?>">
                                            <input type="text" name="rows[<?= $first_item['id'] ?>][remarks]" value="<?= htmlspecialchars($parsed['text']) ?>">
                                            <input type="hidden" name="rows[<?= $first_item['id'] ?>][existing_img]" value="<?= htmlspecialchars($parsed['img']) ?>">
                                            <?php if(!empty($parsed['img'])): ?>
                                                <div style="margin-top: 8px; padding: 10px; background: #fff; border: 1px solid var(--border); border-radius: 8px; display: flex; align-items: center; justify-content: space-between;">
                                                    <a href="<?= htmlspecialchars($parsed['img']) ?>" target="_blank"><img src="<?= htmlspecialchars($parsed['img']) ?>" style="height: 40px; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></a>
                                                    <label style="color: #dc2626; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 4px;"><input type="checkbox" name="rows[<?= $first_item['id'] ?>][delete_img]" value="1"> Delete Image</label>
                                                </div>
                                            <?php endif; ?>
                                            <input type="file" name="new_img[<?= $first_item['id'] ?>]" style="margin-top: 8px; display: block; width: 100%;">
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
                                    <tr><th colspan="4" class="section-header"><?= strtoupper($system_name . ' - ' . $section_name) ?></th></tr>
                                    <tr><th width="5%" class="center">BIL</th><th width="45%">DESCRIPTION</th><th width="20%" class="center"><?= $section_name === 'PUMP PRESSURE' ? 'VALUE' : 'STATUS' ?></th><th width="30%">REMARKS</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): 
                                        $parsed = extract_remark_data($item['remarks'] ?? '');
                                    ?>
                                    <tr>
                                        <input type="hidden" name="rows[<?= $item['id'] ?>][table]" value="<?= htmlspecialchars($table) ?>">
                                        <input type="hidden" name="rows[<?= $item['id'] ?>][section]" value="<?= htmlspecialchars($section_name) ?>">
                                        
                                        <td class="center bold"><?= htmlspecialchars($item['item_bil']) ?></td>
                                        <td><?= htmlspecialchars($item['description']) ?></td>
                                        <td class="center">
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
                                        <td>
                                            <input type="text" name="rows[<?= $item['id'] ?>][remarks]" value="<?= htmlspecialchars($parsed['text']) ?>">
                                            <input type="hidden" name="rows[<?= $item['id'] ?>][existing_img]" value="<?= htmlspecialchars($parsed['img']) ?>">
                                            <?php if(!empty($parsed['img'])): ?>
                                                <div style="margin-top: 8px; padding: 10px; background: #fff; border: 1px solid var(--border); border-radius: 8px; display: flex; align-items: center; justify-content: space-between;">
                                                    <a href="<?= htmlspecialchars($parsed['img']) ?>" target="_blank"><img src="<?= htmlspecialchars($parsed['img']) ?>" style="height: 40px; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></a>
                                                    <label style="color: #dc2626; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 4px;"><input type="checkbox" name="rows[<?= $item['id'] ?>][delete_img]" value="1"> Delete Image</label>
                                                </div>
                                            <?php endif; ?>
                                            <input type="file" name="new_img[<?= $item['id'] ?>]" style="margin-top: 8px; display: block; width: 100%;">
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
                                    <tr><th colspan="5" class="section-header"><?= strtoupper($system_name . ' - ' . $section_name) ?></th></tr>
                                    <tr>
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
                                                echo '<tr style="background-color: var(--surface); border-top: 3px solid var(--border);">
                                                        <td class="center bold">'.htmlspecialchars($main_bil).'</td>
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
                                                ?>
                                                <tr class="eq-row-<?= $table ?>-<?= $main_bil ?>">
                                                    <input type="hidden" name="rows[<?= $item['id'] ?>][table]" value="<?= htmlspecialchars($table) ?>">
                                                    <input type="hidden" name="rows[<?= $item['id'] ?>][section]" value="EQUIPMENT">
                                                    <td class="center bold"><?= htmlspecialchars($sub_bil) ?></td>
                                                    <td class="indent"><input type="text" name="rows[<?= $item['id'] ?>][description]" value="<?= htmlspecialchars($item['description']) ?>" placeholder="Location / Floor"></td>
                                                    <td class="center">
                                                        <?php
                                                        $uid = 'chk_edit_' . $item['id'];
                                                        $checked = ($item['checklist'] == 'Done') ? 'checked' : '';
                                                        ?>
                                                        <div class="chk-wrap">
                                                            <input type="checkbox" name="rows[<?= $item['id'] ?>][checklist]" id="<?= $uid ?>" value="Done" <?= $checked ?>>
                                                            <label for="<?= $uid ?>" class="chk-icon"></label>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <select name="rows[<?= $item['id'] ?>][condition]">
                                                            <?php
                                                            $cond = $item['item_condition'] ?? 'Not Applicable';
                                                            foreach (['Not Applicable', 'Normal', 'Faulty'] as $opt) {
                                                                $sel = ($cond == $opt) ? 'selected' : '';
                                                                echo "<option value=\"$opt\" $sel>$opt</option>";
                                                            }
                                                            ?>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <div style="display: flex; gap: 8px; align-items: center;">
                                                            <input type="text" name="rows[<?= $item['id'] ?>][remarks]" value="<?= htmlspecialchars($parsed['text']) ?>">
                                                            <button type="button" class="btn-delete-row" title="Delete Row" onclick="deleteEditRow(this, '<?= $table ?>', '<?= $item['id'] ?>', '<?= $main_bil ?>', 'eq-row')">
                                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                            </button>
                                                        </div>
                                                        <input type="hidden" name="rows[<?= $item['id'] ?>][existing_img]" value="<?= htmlspecialchars($parsed['img']) ?>">
                                                        <?php if(!empty($parsed['img'])): ?>
                                                            <div style="margin-top: 8px; padding: 10px; background: #fff; border: 1px solid var(--border); border-radius: 8px; display: flex; align-items: center; justify-content: space-between;">
                                                                <a href="<?= htmlspecialchars($parsed['img']) ?>" target="_blank"><img src="<?= htmlspecialchars($parsed['img']) ?>" style="height: 40px; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></a>
                                                                <label style="color: #dc2626; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 4px;"><input type="checkbox" name="rows[<?= $item['id'] ?>][delete_img]" value="1"> Delete Image</label>
                                                            </div>
                                                        <?php endif; ?>
                                                        <input type="file" name="new_img[<?= $item['id'] ?>]" style="margin-top: 8px; display: block; width: 100%;">
                                                    </td>
                                                </tr>
                                                <?php
                                                continue;
                                            } else {
                                                continue; 
                                            }
                                        }
                                    ?>
                                    <tr>
                                        <input type="hidden" name="rows[<?= $item['id'] ?>][table]" value="<?= htmlspecialchars($table) ?>">
                                        <input type="hidden" name="rows[<?= $item['id'] ?>][section]" value="EQUIPMENT">
                                        <td class="center bold"><?= htmlspecialchars($item['item_bil']) ?></td>
                                        <td class="bold"><?= htmlspecialchars($item['description']) ?></td>
                                        <td class="center">
                                            <?php
                                            $uid = 'chk_edit_' . $item['id'];
                                            $checked = ($item['checklist'] == 'Done') ? 'checked' : '';
                                            ?>
                                            <div class="chk-wrap">
                                                <input type="checkbox" name="rows[<?= $item['id'] ?>][checklist]" id="<?= $uid ?>" value="Done" <?= $checked ?>>
                                                <label for="<?= $uid ?>" class="chk-icon"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <select name="rows[<?= $item['id'] ?>][condition]">
                                                <?php
                                                $cond = $item['item_condition'] ?? 'Not Applicable';
                                                foreach (['Not Applicable', 'Normal', 'Faulty'] as $opt) {
                                                    $sel = ($cond == $opt) ? 'selected' : '';
                                                    echo "<option value=\"$opt\" $sel>$opt</option>";
                                                }
                                                ?>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" name="rows[<?= $item['id'] ?>][remarks]" value="<?= htmlspecialchars($parsed['text']) ?>">
                                            <input type="hidden" name="rows[<?= $item['id'] ?>][existing_img]" value="<?= htmlspecialchars($parsed['img']) ?>">
                                            <?php if(!empty($parsed['img'])): ?>
                                                <div style="margin-top: 8px; padding: 10px; background: #fff; border: 1px solid var(--border); border-radius: 8px; display: flex; align-items: center; justify-content: space-between;">
                                                    <a href="<?= htmlspecialchars($parsed['img']) ?>" target="_blank"><img src="<?= htmlspecialchars($parsed['img']) ?>" style="height: 40px; border-radius: 4px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></a>
                                                    <label style="color: #dc2626; font-size: 13px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 4px;"><input type="checkbox" name="rows[<?= $item['id'] ?>][delete_img]" value="1"> Delete Image</label>
                                                </div>
                                            <?php endif; ?>
                                            <input type="file" name="new_img[<?= $item['id'] ?>]" style="margin-top: 8px; display: block; width: 100%;">
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
                                        <tr><th colspan="5" class="section-header"><?= strtoupper($system_name . ' - ' . $section_name) ?></th></tr>
                                        <tr>
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
                                            $bil_parts = explode('-', $item['item_bil']);
                                            if (count($bil_parts) > 1) {
                                                $main_bil = $bil_parts[0];
                                                $sub_bil = $bil_parts[1];
                                                if ($main_bil !== $current_main_bil) {
                                                    echo '<tr style="background-color: var(--surface); border-top: 3px solid var(--border);">
                                                            <td class="center bold">1</td>
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
                                            
                                            <td class="center bold"><?= htmlspecialchars($sub_bil) ?></td>
                                            <?php if (count($bil_parts) > 1): ?>
                                                <td class="indent"><input type="text" name="rows[<?= $item['id'] ?>][location_floor]" value="<?= htmlspecialchars($item['location_floor'] ?? '') ?>" placeholder="Location / Floor"></td>
                                            <?php else: ?>
                                                <td class="bold"><?= htmlspecialchars($item['description']) ?></td>
                                            <?php endif; ?>
                                            <td class="center"><?= checklist_select("rows[{$item['id']}][checklist]", $item['checklist'] == 'Done') ?></td>
                                            <td><?= status_select("rows[{$item['id']}][condition]", $item['item_condition']) ?></td>
                                            <td>
                                                <div class="remark-group">
                                                    <input type="text" name="rows[<?= $item['id'] ?>][remarks]" value="<?= htmlspecialchars($item['remarks'] ?? '') ?>">
                                                    <?php if (count($bil_parts) > 1): ?>
                                                        <button type="button" class="btn-delete-row" title="Delete Row" onclick="deleteEditRow(this, '<?= $table ?>', '<?= $item['id'] ?>', '<?= $bil_parts[0] ?>', 'dev-row')">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
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
                                        <tr><th colspan="6" class="section-header"><?= strtoupper($system_name . ' - ' . $section_name) ?></th></tr>
                                        <tr>
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
                                            $bil_parts = explode('-', $item['item_bil']);
                                            if (count($bil_parts) > 1) {
                                                $main_bil = $bil_parts[0];
                                                $sub_bil = $bil_parts[1];
                                                if ($main_bil !== $current_main_bil) {
                                                    $btn_text = "+ Add Floor / Zone";
                                                    if ($table === 'fire_alarm_system_add') $btn_text = "+ Add Floor / Loop";

                                                    echo '<tr style="background-color: var(--surface); border-top: 3px solid var(--border);">
                                                            <td class="center bold">'.htmlspecialchars($main_bil).'</td>
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
                                            
                                            <td class="center bold"><?= htmlspecialchars($sub_bil) ?></td>
                                            <?php if (count($bil_parts) > 1): ?>
                                                <td class="indent"><input type="text" name="rows[<?= $item['id'] ?>][location_floor]" value="<?= htmlspecialchars($item['location_floor'] ?? '') ?>" placeholder="Location / Floor"></td>
                                            <?php else: ?>
                                                <td class="bold"><?= htmlspecialchars($item['description']) ?></td>
                                            <?php endif; ?>
                                            <td class="center"><input type="text" name="rows[<?= $item['id'] ?>][zone_loop]" value="<?= htmlspecialchars($item['zone_loop'] ?? '') ?>"></td>
                                            <td class="center"><?= checklist_select("rows[{$item['id']}][checklist]", $item['checklist'] == 'Done') ?></td>
                                            <td><?= status_select("rows[{$item['id']}][condition]", $item['item_condition']) ?></td>
                                            <td>
                                                <div class="remark-group">
                                                    <input type="text" name="rows[<?= $item['id'] ?>][remarks]" value="<?= htmlspecialchars($item['remarks'] ?? '') ?>">
                                                    <?php if (count($bil_parts) > 1): ?>
                                                        <button type="button" class="btn-delete-row" title="Delete Row" onclick="deleteEditRow(this, '<?= $table ?>', '<?= $item['id'] ?>', '<?= $bil_parts[0] ?>', 'dev-row')">
                                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
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
                                    <tr><th colspan="5" class="section-header"><?= strtoupper($system_name . ' - ' . $section_name) ?></th></tr>
                                    <tr>
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
                                        $bil_parts = explode('-', $item['item_bil']);
                                        $is_nested = count($bil_parts) > 1;
                                        if ($is_nested) {
                                            $main_bil = $bil_parts[0];
                                            $sub_bil = $bil_parts[1];
                                            $desc_parts = explode(' - ', $item['description'], 2);
                                            $main_desc = $desc_parts[0];
                                            $sub_desc = $desc_parts[1] ?? $main_desc;
                                            if ($main_bil !== $current_main_bil) {
                                                echo '<tr style="background-color: var(--surface); border-top: 2px solid var(--border);"><td class="center bold">'.htmlspecialchars($main_bil).'</td><td colspan="4" class="bold" style="color: var(--primary); font-size: 16px;">'.htmlspecialchars($main_desc).'</td></tr>';
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
                                        
                                        <td class="center bold"><?= htmlspecialchars($sub_bil) ?></td>
                                        <td class="<?= $is_nested ? 'indent' : 'bold' ?>"><?= htmlspecialchars($sub_desc) ?></td>
                                        <td class="center"><?= checklist_select("rows[{$item['id']}][checklist]", $item['checklist'] == 'Done') ?></td>
                                        <td><?= status_select("rows[{$item['id']}][condition]", $item['item_condition']) ?></td>
                                        <td><input type="text" name="rows[<?= $item['id'] ?>][remarks]" value="<?= htmlspecialchars($item['remarks'] ?? '') ?>"></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            </div>

                        <?php else: ?>
                            <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr><th colspan="5" class="section-header"><?= strtoupper($system_name . ' - ' . $section_name) ?></th></tr>
                                    <tr>
                                        <th width="5%" class="center">BIL</th>
                                        <th width="50%">DESCRIPTION</th>
                                        <th width="12%" class="center">CHECKLIST</th>
                                        <th width="12%" class="center">CONDITION</th>
                                        <th width="21%">REMARKS</th>
                                    </tr>
                                    <?php if (strpos($section_name, 'PUMP TEST') !== false): ?>
                                        <tr style="background-color: var(--surface); border-top: 2px solid var(--border);">
                                            <td class="center bold"><?= strpos($section_name, 'MANUAL') !== false ? '1' : '2' ?></td>
                                            <td colspan="4" class="bold" style="color: var(--primary); font-size: 16px;"><?= strpos($section_name, 'MANUAL') !== false ? 'MANUAL TEST FOR:' : 'AUTO TEST FOR:' ?></td>
                                        </tr>
                                    <?php endif; ?>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                    <tr>
                                        <input type="hidden" name="rows[<?= $item['id'] ?>][table]" value="<?= htmlspecialchars($table) ?>">
                                        <input type="hidden" name="rows[<?= $item['id'] ?>][section]" value="<?= htmlspecialchars($section_name) ?>">
                                        
                                        <td class="center bold"><?= htmlspecialchars($item['item_bil']) ?></td>
                                        <td class="<?= strpos($section_name, 'PUMP TEST') !== false ? 'indent' : 'bold' ?>"><?= htmlspecialchars($item['description']) ?></td>
                                        <td class="center"><?= checklist_select("rows[{$item['id']}][checklist]", $item['checklist'] == 'Done') ?></td>
                                        <td><?= status_select("rows[{$item['id']}][condition]", $item['item_condition']) ?></td>
                                        <td><input type="text" name="rows[<?= $item['id'] ?>][remarks]" value="<?= htmlspecialchars($item['remarks'] ?? '') ?>"></td>
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
        
        <td class="center bold">${roman}</td>
        <td class="indent"><input type="text" name="new_rows[${table}][${uniqueIdx}][location_floor]" placeholder="Location / Floor"></td>
        <td class="center"><input type="text" name="new_rows[${table}][${uniqueIdx}][zone_loop]"></td>
        <td class="center">
            <div class="chk-wrap">
                <input type="checkbox" name="new_rows[${table}][${uniqueIdx}][checklist]" id="${uniqueId}" value="Done">
                <label for="${uniqueId}"></label>
            </div>
        </td>
        <td>
            <select name="new_rows[${table}][${uniqueIdx}][condition]" class="cond-select">
                <option value="Not Applicable">Not Applicable</option>
                <option value="Normal">Normal</option>
                <option value="Faulty">Faulty</option>
            </select>
        </td>
        <td>
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
        
        <td class="center bold">${roman}</td>
        <td class="indent"><input type="text" name="new_rows[${table}][${uniqueIdx}][location_floor]" placeholder="Location / Floor"></td>
        <td class="center">
            <div class="chk-wrap">
                <input type="checkbox" name="new_rows[${table}][${uniqueIdx}][checklist]" id="${uniqueId}" value="Done">
                <label for="${uniqueId}"></label>
            </div>
        </td>
        <td>
            <select name="new_rows[${table}][${uniqueIdx}][condition]" class="cond-select">
                <option value="Not Applicable">Not Applicable</option>
                <option value="Normal">Normal</option>
                <option value="Faulty">Faulty</option>
            </select>
        </td>
        <td>
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
        
        <td class="center bold">${roman}</td>
        <td class="indent"><input type="text" name="new_rows[${table}][${uniqueIdx}][description]" placeholder="Location / Floor"></td>
        <td class="center">
            <div class="chk-wrap">
                <input type="checkbox" name="new_rows[${table}][${uniqueIdx}][checklist]" id="${uniqueId}" value="Done">
                <label for="${uniqueId}"></label>
            </div>
        </td>
        <td>
            <select name="new_rows[${table}][${uniqueIdx}][condition]" class="cond-select">
                <option value="Not Applicable">Not Applicable</option>
                <option value="Normal">Normal</option>
                <option value="Faulty">Faulty</option>
            </select>
        </td>
        <td>
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