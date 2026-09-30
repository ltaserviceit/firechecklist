<?php
session_start();
require 'db.php';

// ── Inline shared helpers ──────────────────────────────────────────────────
function handle_image_upload($input_name, $idx1, $idx2 = null) {
    if (!isset($_FILES[$input_name])) return "";
    $file = $_FILES[$input_name];
    
    if ($idx2 !== null) {
        if (isset($file['error'][$idx1][$idx2]) && $file['error'][$idx1][$idx2] === UPLOAD_ERR_OK) {
            $tmp = $file['tmp_name'][$idx1][$idx2];
            $name = $file['name'][$idx1][$idx2];
        } else { return ""; }
    } else {
        if (isset($file['error'][$idx1]) && $file['error'][$idx1] === UPLOAD_ERR_OK) {
            $tmp = $file['tmp_name'][$idx1];
            $name = $file['name'][$idx1];
        } else { return ""; }
    }
    
    $clean_name = time() . '_' . preg_replace("/[^a-zA-Z0-9.]/", "", basename($name));
    $path = 'uploads/' . $clean_name;
    if (!is_dir('uploads')) mkdir('uploads', 0777, true);
    if (move_uploaded_file($tmp, $path)) return $path;
    return "";
}

function render_form_header($title) {
    echo '<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>' . htmlspecialchars($title) . ' - Technician Portal</title>
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
            font-family: \'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif; 
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
        .page-header p { margin: 0; color: var(--text-muted); font-size: 16px; line-height: 1.5; }

        /* ============ TABLES ============ */
        .table-responsive { overflow-x: auto; width: 100%; margin-bottom: 40px; border-radius: 12px; background: var(--surface); border: 1px solid var(--border); box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05); }
        table { width: 100%; border-collapse: collapse; min-width: 900px; }
        
        th, td { padding: 18px 20px; text-align: left; vertical-align: middle; font-size: 15px; border-bottom: 1px solid var(--border); line-height: 1.4; }
        
        th { background: var(--row-alt); color: var(--text-muted); font-weight: 700; text-transform: uppercase; font-size: 13px; letter-spacing: 1px; }
        th.section-header { background: var(--surface) !important; color: var(--text-main) !important; font-size: 18px; padding: 24px 20px; font-weight: 800; border-bottom: 3px solid var(--border); letter-spacing: 0.5px; }
        
        tbody tr:nth-child(even) td { background-color: var(--row-alt); }
        tbody tr:hover td { background-color: var(--row-hover); }
        tr:last-child td { border-bottom: none; }

        /* ============ INPUTS & REMARKS ============ */
        input[type="text"], select.cond-select { 
            width: 100%; padding: 14px 16px; border: 1.5px solid var(--border); 
            border-radius: 8px; font-size: 16px; background: var(--surface); 
            transition: all 0.2s; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02); 
            -webkit-appearance: none; color: var(--text-main); font-weight: 500;
        }
        input[type="text"]:focus, select.cond-select:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 4px rgba(196, 30, 58, 0.1); }
        input[type="text"]::placeholder { color: #94a3b8; font-weight: 400; }

        .remark-group { display: flex; gap: 8px; align-items: center; }
        .file-upload-btn {
            background: #f1f5f9; border: 1.5px solid var(--border); border-radius: 8px; 
            padding: 12px; cursor: pointer; display: flex; align-items: center; justify-content: center; 
            transition: all 0.2s; flex-shrink: 0; color: var(--text-muted); box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
        }
        .file-upload-btn:hover { background: #e2e8f0; border-color: #cbd5e1; color: var(--text-main); }
        .file-upload-btn svg { width: 20px; height: 20px; stroke: currentColor; }
        .file-upload-btn.attached { background: var(--success); border-color: var(--success); color: white; box-shadow: 0 4px 8px rgba(16, 185, 129, 0.25); }
        .file-input { display: none; }

        /* ============ BUTTONS ============ */
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
        .btn-delete-row svg { width: 20px; height: 20px; stroke: currentColor; }

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
<body>';
}
function render_form_footer() { echo '</div></body></html>'; }
function status_select($name) {
    return '<select name="' . htmlspecialchars($name) . '" class="cond-select"><option value="Not Applicable">Not Applicable</option><option value="Normal">Normal</option><option value="Faulty">Faulty</option></select>';
}
function checklist_checkbox($name) {
    $id = 'chk_' . str_replace(['[',']'], '_', $name) . '_' . uniqid();
    return '<div class="chk-wrap"><input type="checkbox" name="' . htmlspecialchars($name) . '" id="' . $id . '" value="Done"><label for="' . $id . '"></label></div>';
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'technician') { header("Location: index.php"); exit(); }
$table_name = "fire_sprinkler_system";
$task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) : 0;

$pump_info = ["1"=>"FIRE PUMP LOCATION","2"=>"FIRE PUMP TYPE","3"=>"WORKING PRESSURE","4"=>"WATER TANK SIZE AND CAPACITY (GALLON)",];
$pressure_items = ["1"=>"CUT IN (PSI)","2"=>"CUT OUT (PSI)","3"=>"SUCTION VALVE (STATUS)","4"=>"DISCHARGE VALVE (STATUS)",];
$panel_items = [
    "1"=>"CONTROL PANEL","2"=>"DUTY AND STANDBY PUMP","3"=>"SEALED LEAD ACID BATTERY",
    "4"=>"CONTROL PANEL AND PUMP INCOMING SUPPLY DELIVERING 415V AC","5"=>"FUSES, LED LIGHT BULB, SWITCHES & BUTTONS",
    "6"=>"VOLT METER","7"=>"AMPERE METER","8"=>"WIRINGS AND CABLINGS",
];

$equipment_items = [
    "1" => "BREACHING INLET IS FREE FROM OBSTRUCTION",
    "2" => "CONDITION OF SPRINKLER HEAD",
    "3" => "ALARM VALVE",
    "4" => "ALARM GONG",
    "5" => "PRESSURE GAUGE",
    "6" => "BUTTERFLY VALVE",
    "7" => "FLOW SWITCH",
    "8" => "AUTOMATIC AIR RELEASE VALVE IN THE PIPE STACK IS CHECKED AND CLEAN",
    "9" => "ACTIVATE LIVE SPRINKLER"
];

$pump_test_items = ["I"=>"AC FAIL", "II"=>"SPRINKLER DUTY PUMP", "III"=>"SPRINKLER STANDBY PUMP", "IV"=>"SPRINKLER JOCKEY PUMP"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Save Pump Info
    foreach ($pump_info as $bil => $desc_text) {
        $desc = $conn->real_escape_string($desc_text);
        $val  = $conn->real_escape_string($_POST['pump_status'][$bil] ?? '');
        $rem  = $conn->real_escape_string($_POST['pump_rem'][$bil] ?? '');
        
        $img = handle_image_upload('pump_image', $bil);
        if ($img !== "") { $rem .= " [IMG: $img]"; }
        
        $conn->query("INSERT INTO $table_name (task_id,section_name,item_bil,description,checklist,remarks) VALUES ($task_id,'PUMP INFO','$bil','$desc','$val','$rem')");
    }
    
    // 2. Save Pump Pressures (Using unique item_bil to avoid splitting into multiple submissions)
    if (!empty($_POST['press'])) {
        foreach ($_POST['press'] as $bil => $vals) {
            $desc_base = $pressure_items[$bil];
            $rem_base = $_POST['press_rem'][$bil] ?? '';
            
            $img = handle_image_upload('press_image', $bil);
            if ($img !== "") { $rem_base .= " [IMG: $img]"; }
            
            $rem = $conn->real_escape_string($rem_base);
            
            foreach (['jockey','duty','standby'] as $col) {
                $v = $conn->real_escape_string($vals[$col] ?? '');
                if ($v !== '') {
                    $desc = $conn->real_escape_string($desc_base . " (" . strtoupper($col) . ")");
                    $unique_bil = $conn->real_escape_string($bil . '-' . strtoupper($col));
                    $conn->query("INSERT INTO $table_name (task_id,section_name,item_bil,description,checklist,remarks) VALUES ($task_id,'PUMP PRESSURE','$unique_bil','$desc','$v','$rem')");
                }
            }
        }
    }

    // 3. Save Panel Checklist
    foreach ($panel_items as $bil => $desc_text) {
        $desc = $conn->real_escape_string($desc_text);
        $cond = $conn->real_escape_string($_POST['cp_condition'][$bil] ?? '');
        $rem  = $conn->real_escape_string($_POST['cp_remarks'][$bil] ?? '');
        
        $img = handle_image_upload('cp_image', $bil);
        if ($img !== "") { $rem .= " [IMG: $img]"; }
        
        $cl   = isset($_POST['cp_checklist'][$bil]) ? 'Done' : 'N/A';
        $conn->query("INSERT INTO $table_name (task_id,section_name,item_bil,description,checklist,item_condition,remarks) VALUES ($task_id,'CONTROL PANEL','$bil','$desc','$cl','$cond','$rem')");
    }

    // 4. Save Dynamic Equipment Checklist
    foreach ($equipment_items as $bil => $desc_text) {
        $is_header = ($bil == 6 || $bil == 7);
        
        if ($is_header) {
            // Save the parent header row
            $conn->query("INSERT INTO $table_name (task_id,section_name,item_bil,description,checklist,item_condition,remarks) VALUES ($task_id,'EQUIPMENT','$bil','$desc_text','','','')");
            
            // Save dynamically added location/floor sub-items
            if (!empty($_POST['eq_desc'][$bil])) {
                foreach ($_POST['eq_desc'][$bil] as $roman => $desc_val) {
                    if (trim($desc_val) === '') continue;
                    $desc = $conn->real_escape_string($desc_val);
                    $cond = $conn->real_escape_string($_POST['eq_condition'][$bil][$roman] ?? '');
                    $rem  = $conn->real_escape_string($_POST['eq_remarks'][$bil][$roman] ?? '');
                    
                    $img = handle_image_upload('eq_image', $bil, $roman);
                    if ($img !== "") { $rem .= " [IMG: $img]"; }
                    
                    $cl      = isset($_POST['eq_checklist'][$bil][$roman]) ? 'Done' : 'N/A';
                    $sub_bil = $bil . '-' . $roman;
                    $conn->query("INSERT INTO $table_name (task_id,section_name,item_bil,description,checklist,item_condition,remarks) VALUES ($task_id,'EQUIPMENT','$sub_bil','$desc','$cl','$cond','$rem')");
                }
            }
        } else {
            $desc = $conn->real_escape_string($desc_text);
            $cond = $conn->real_escape_string($_POST['eq_condition'][$bil] ?? '');
            $rem  = $conn->real_escape_string($_POST['eq_remarks'][$bil] ?? '');
            
            $img = handle_image_upload('eq_image', $bil);
            if ($img !== "") { $rem .= " [IMG: $img]"; }
            
            $cl   = isset($_POST['eq_checklist'][$bil]) ? 'Done' : 'N/A';
            $conn->query("INSERT INTO $table_name (task_id,section_name,item_bil,description,checklist,item_condition,remarks) VALUES ($task_id,'EQUIPMENT','$bil','$desc','$cl','$cond','$rem')");
        }
    }

    // 5. Save Pump Testing
    foreach (['manual','auto'] as $mode) {
        $mode_upper = strtoupper($mode);
        $section_val = $conn->real_escape_string("PUMP TEST - " . $mode_upper);
        
        foreach ($pump_test_items as $key => $desc_text) {
            $desc = $conn->real_escape_string($desc_text);
            $cl   = isset($_POST["pump_{$mode}_cl"][$key]) ? 'Done' : 'N/A';
            $cond = $conn->real_escape_string($_POST["pump_{$mode}_cond"][$key] ?? '');
            $rem  = $conn->real_escape_string($_POST["pump_{$mode}_rem"][$key] ?? '');
            
            $img = handle_image_upload("pump_{$mode}_image", $key);
            if ($img !== "") { $rem .= " [IMG: $img]"; }
            
            $conn->query("INSERT INTO $table_name (task_id,section_name,item_bil,description,checklist,item_condition,remarks) VALUES ($task_id,'$section_val','$key','$desc','$cl','$cond','$rem')");
        }
    }
    
    header("Location: tech_menu.php?task_id=$task_id"); exit();
}

render_form_header("FIRE SPRINKLER SYSTEM - Checklist");
?>

<div class="topbar">
    <div class="topbar-title">
        <svg viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
        SPRINKLER SYSTEM
    </div>
    <a href="tech_menu.php?task_id=<?= $task_id ?>" class="back-btn">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        Cancel & Back
    </a>
</div>

<div class="container">
    <div class="page-header">
        <h1>Fire Sprinkler System Checklist</h1>
        <p>Complete the inspection form below for Task #<?= $task_id ?></p>
    </div>

<form method="POST" enctype="multipart/form-data">

<div class="accordion-controls">
    <button type="button" class="btn-accordion-all" onclick="setAllAccordions(true)">Expand All</button>
    <button type="button" class="btn-accordion-all" onclick="setAllAccordions(false)">Collapse All</button>
</div>

<div class="table-responsive" id="section-fire-sprinkler-system-pump">
<table>
    <thead>
        <tr><th colspan="4" class="section-header" onclick="toggleAccordion(this)">
            <div class="accordion-toggle">
                <span>FIRE SPRINKLER SYSTEM PUMP</span>
                <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
        </th></tr>
        <tr class="col-header-row"><th width="5%" class="center">BIL</th><th width="45%">DESCRIPTION</th><th width="15%">STATUS</th><th width="35%">REMARKS & PHOTOS</th></tr>
    </thead>
    <tbody>
        <?php foreach($pump_info as $bil=>$desc): ?>
        <tr>
            <td data-label="BIL" class="center bold"><?= $bil ?></td>
            <td data-label="DESCRIPTION" class="bold"><?= htmlspecialchars($desc) ?></td>
            <td data-label="STATUS"><input type="text" name="pump_status[<?=$bil?>]"></td>
            <td data-label="REMARKS & PHOTOS">
                <div class="remark-group">
                    <input type="text" name="pump_rem[<?=$bil?>]" placeholder="Remarks">
                    <label class="file-upload-btn" title="Attach Image">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                        <input type="file" name="pump_image[<?=$bil?>]" class="file-input" accept="image/*" capture="environment" onchange="if(this.files.length > 0) { this.parentElement.classList.add('attached'); this.parentElement.title='Image Attached'; }">
                    </label>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>

<div class="table-responsive collapsed" id="section-fire-sprinkler-system-pump-pressure">
<table>
    <thead>
        <tr><th colspan="6" class="section-header" onclick="toggleAccordion(this)">
            <div class="accordion-toggle">
                <span>FIRE SPRINKLER SYSTEM PUMP PRESSURE</span>
                <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
        </th></tr>
        <tr class="col-header-row"><th width="5%" class="center">BIL</th><th width="30%">VALVE</th><th width="10%" class="center">JOCKEY</th><th width="10%" class="center">DUTY</th><th width="10%" class="center">STANDBY</th><th width="35%">REMARKS & PHOTOS</th></tr>
    </thead>
    <tbody>
        <?php foreach($pressure_items as $bil=>$desc): ?>
        <tr>
            <td data-label="BIL" class="center bold"><?= $bil ?></td>
            <td data-label="VALVE" class="bold"><?= htmlspecialchars($desc) ?></td>
            <td data-label="JOCKEY"><input type="text" name="press[<?=$bil?>][jockey]"></td>
            <td data-label="DUTY"><input type="text" name="press[<?=$bil?>][duty]"></td>
            <td data-label="STANDBY"><input type="text" name="press[<?=$bil?>][standby]"></td>
            <td data-label="REMARKS & PHOTOS">
                <div class="remark-group">
                    <input type="text" name="press_rem[<?=$bil?>]" placeholder="Remarks">
                    <label class="file-upload-btn" title="Attach Image">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                        <input type="file" name="press_image[<?=$bil?>]" class="file-input" accept="image/*" capture="environment" onchange="if(this.files.length > 0) { this.parentElement.classList.add('attached'); this.parentElement.title='Image Attached'; }">
                    </label>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>

<div class="table-responsive collapsed" id="section-fire-sprinkler-system-control-panel">
<table>
    <thead>
        <tr><th colspan="5" class="section-header" onclick="toggleAccordion(this)">
            <div class="accordion-toggle">
                <span>FIRE SPRINKLER SYSTEM - CONTROL PANEL</span>
                <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
        </th></tr>
        <tr class="col-header-row"><th width="5%" class="center">BIL</th><th width="45%">DESCRIPTION</th><th width="10%" class="center">CHECKLIST</th><th width="15%">CONDITION</th><th width="25%">REMARKS & PHOTOS</th></tr>
    </thead>
    <tbody>
        <?php foreach($panel_items as $bil=>$desc): ?>
        <tr>
            <td data-label="BIL" class="center bold"><?= $bil ?></td>
            <td data-label="DESCRIPTION" class="bold"><?= htmlspecialchars($desc) ?></td>
            <td data-label="CHECKLIST" class="center"><?= checklist_checkbox("cp_checklist[$bil]") ?></td>
            <td data-label="CONDITION"><?= status_select("cp_condition[$bil]") ?></td>
            <td data-label="REMARKS & PHOTOS">
                <div class="remark-group">
                    <input type="text" name="cp_remarks[<?=$bil?>]" placeholder="Remarks">
                    <label class="file-upload-btn" title="Attach Image">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                        <input type="file" name="cp_image[<?=$bil?>]" class="file-input" accept="image/*" capture="environment" onchange="if(this.files.length > 0) { this.parentElement.classList.add('attached'); this.parentElement.title='Image Attached'; }">
                    </label>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>

<div class="table-responsive collapsed" id="section-fire-sprinkler-system-maintenance-checklist">
<table>
    <thead>
        <tr><th colspan="5" class="section-header" onclick="toggleAccordion(this)">
            <div class="accordion-toggle">
                <span>FIRE SPRINKLER SYSTEM MAINTENANCE CHECKLIST</span>
                <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
        </th></tr>
        <tr class="col-header-row"><th width="5%" class="center">BIL</th><th width="45%">DESCRIPTION</th><th width="10%" class="center">CHECKLIST</th><th width="15%">CONDITION</th><th width="25%">REMARKS & PHOTOS</th></tr>
    </thead>
    <tbody>
        <?php foreach($equipment_items as $bil=>$desc): ?>
            <?php $is_header = ($bil == 6 || $bil == 7); ?>
            
            <?php if ($is_header): ?>
                <tr style="background-color: var(--surface); border-top: 3px solid var(--border);" class="group-row">
                    <td data-label="BIL" class="center bold"><?= $bil ?></td>
                    <td colspan="4" class="bold" style="color: var(--primary); font-size: 16px;">
                        <?= htmlspecialchars($desc) ?>
                        <button type="button" class="btn-add-floor" onclick="addSprinklerRow('<?= $bil ?>')" style="margin-left: 14px;">
                            + Add Location / Floor
                        </button>
                    </td>
                </tr>
                <!-- Default 1st Location/Floor Row -->
                <tr class="eq-group-<?= $bil ?>">
                    <td data-label="BIL" class="center bold">I</td>
                    <td data-label="DESCRIPTION" class="indent"><input type="text" name="eq_desc[<?= $bil ?>][I]" placeholder="Location / Floor"></td>
                    <td data-label="CHECKLIST" class="center"><?= checklist_checkbox("eq_checklist[$bil][I]") ?></td>
                    <td data-label="CONDITION"><?= status_select("eq_condition[$bil][I]") ?></td>
                    <td data-label="REMARKS & PHOTOS">
                        <div class="remark-group">
                            <input type="text" name="eq_remarks[<?= $bil ?>][I]" placeholder="Remarks">
                            <label class="file-upload-btn" title="Attach Image">
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                                <input type="file" name="eq_image[<?= $bil ?>][I]" class="file-input" accept="image/*" capture="environment" onchange="if(this.files.length > 0) { this.parentElement.classList.add('attached'); this.parentElement.title='Image Attached'; }">
                            </label>
                        </div>
                    </td>
                </tr>
            <?php else: ?>
                <tr>
                    <td data-label="BIL" class="center bold"><?= $bil ?></td>
                    <td data-label="DESCRIPTION" class="bold"><?= htmlspecialchars($desc) ?></td>
                    <td data-label="CHECKLIST" class="center"><?= checklist_checkbox("eq_checklist[$bil]") ?></td>
                    <td data-label="CONDITION"><?= status_select("eq_condition[$bil]") ?></td>
                    <td data-label="REMARKS & PHOTOS">
                        <div class="remark-group">
                            <input type="text" name="eq_remarks[<?=$bil?>]" placeholder="Remarks">
                            <label class="file-upload-btn" title="Attach Image">
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                                <input type="file" name="eq_image[<?=$bil?>]" class="file-input" accept="image/*" capture="environment" onchange="if(this.files.length > 0) { this.parentElement.classList.add('attached'); this.parentElement.title='Image Attached'; }">
                            </label>
                        </div>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
    </tbody>
</table>
</div>

<div class="table-responsive collapsed" id="section-fire-sprinkler-system-pump-testing">
<table>
    <thead>
        <tr><th colspan="5" class="section-header" onclick="toggleAccordion(this)">
            <div class="accordion-toggle">
                <span>FIRE SPRINKLER SYSTEM - PUMP TESTING</span>
                <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
        </th></tr>
        <tr class="col-header-row"><th width="5%" class="center">BIL</th><th width="45%">DESCRIPTION</th><th width="10%" class="center">CHECKLIST</th><th width="15%">CONDITION</th><th width="25%">REMARKS & PHOTOS</th></tr>
    </thead>
    <tbody>
        <tr style="background-color: var(--surface); border-top: 3px solid var(--border);" class="group-row"><td data-label="BIL" class="center bold">1</td><td colspan="4" class="bold" style="color: var(--primary); font-size: 16px;">MANUAL TEST FOR:</td></tr>
        <?php foreach($pump_test_items as $key=>$desc): ?>
        <tr>
            <td data-label="BIL" class="center bold"><?= $key ?></td>
            <td data-label="DESCRIPTION" class="indent"><?= htmlspecialchars($desc) ?></td>
            <td data-label="CHECKLIST" class="center"><?= checklist_checkbox("pump_manual_cl[$key]") ?></td>
            <td data-label="CONDITION"><?= status_select("pump_manual_cond[$key]") ?></td>
            <td data-label="REMARKS & PHOTOS">
                <div class="remark-group">
                    <input type="text" name="pump_manual_rem[<?=$key?>]" placeholder="Remarks">
                    <label class="file-upload-btn" title="Attach Image">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                        <input type="file" name="pump_manual_image[<?=$key?>]" class="file-input" accept="image/*" capture="environment" onchange="if(this.files.length > 0) { this.parentElement.classList.add('attached'); this.parentElement.title='Image Attached'; }">
                    </label>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
        
        <tr style="background-color: var(--surface); border-top: 3px solid var(--border);" class="group-row"><td data-label="BIL" class="center bold">2</td><td colspan="4" class="bold" style="color: var(--primary); font-size: 16px;">AUTO TEST FOR:</td></tr>
        <?php foreach($pump_test_items as $key=>$desc): ?>
        <tr>
            <td data-label="BIL" class="center bold"><?= $key ?></td>
            <td data-label="DESCRIPTION" class="indent"><?= htmlspecialchars($desc) ?></td>
            <td data-label="CHECKLIST" class="center"><?= checklist_checkbox("pump_auto_cl[$key]") ?></td>
            <td data-label="CONDITION"><?= status_select("pump_auto_cond[$key]") ?></td>
            <td data-label="REMARKS & PHOTOS">
                <div class="remark-group">
                    <input type="text" name="pump_auto_rem[<?=$key?>]" placeholder="Remarks">
                    <label class="file-upload-btn" title="Attach Image">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                        <input type="file" name="pump_auto_image[<?=$key?>]" class="file-input" accept="image/*" capture="environment" onchange="if(this.files.length > 0) { this.parentElement.classList.add('attached'); this.parentElement.title='Image Attached'; }">
                    </label>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>

<div class="save-bar">
    <button type="submit" onclick="return confirm('Are you sure you are completely finished with this list? Click OK to save and submit.');">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="width: 24px; height: 24px;"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
        Finalize and Submit Report
    </button>
</div>

</form>

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

function addSprinklerRow(bil) {
    const rows = document.querySelectorAll(`.eq-group-${bil}`);
    const nextIndex = rows.length;
    
    if (nextIndex >= romanNumerals.length) {
        alert("Maximum limit reached for this item.");
        return;
    }

    const roman = romanNumerals[nextIndex];
    const lastRow = rows[rows.length - 1];
    const uniqueId = 'chk_' + bil + '_' + roman + '_' + Math.random().toString(36).substr(2, 9);

    const newRow = document.createElement('tr');
    newRow.className = `eq-group-${bil}`;
    newRow.innerHTML = `
        <td class="center bold" data-label="BIL">${roman}</td>
        <td class="indent" data-label="DESCRIPTION"><input type="text" name="eq_desc[${bil}][${roman}]" placeholder="Location / Floor"></td>
        <td class="center" data-label="CHECKLIST">
            <div class="chk-wrap">
                <input type="checkbox" name="eq_checklist[${bil}][${roman}]" id="${uniqueId}" value="Done">
                <label for="${uniqueId}"></label>
            </div>
        </td>
        <td data-label="CONDITION">
            <select name="eq_condition[${bil}][${roman}]" class="cond-select">
                <option value="Not Applicable">Not Applicable</option>
                <option value="Normal">Normal</option>
                <option value="Faulty">Faulty</option>
            </select>
        </td>
        <td data-label="REMARKS & PHOTOS">
            <div class="remark-group">
                <input type="text" name="eq_remarks[${bil}][${roman}]" placeholder="Remarks">
                <label class="file-upload-btn" title="Attach Image">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                    <input type="file" name="eq_image[${bil}][${roman}]" class="file-input" accept="image/*" capture="environment" onchange="if(this.files.length > 0) { this.parentElement.classList.add('attached'); this.parentElement.title='Image Attached'; }">
                </label>
                <button type="button" class="btn-delete-row" title="Delete Row" onclick="deleteSprinklerRow(this, '${bil}')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                </button>
            </div>
        </td>
    `;

    lastRow.parentNode.insertBefore(newRow, lastRow.nextSibling);
    reindexSprinklerRows(bil);
}

function deleteSprinklerRow(button, bil) {
    const row = button.closest('tr');
    row.remove();
    reindexSprinklerRows(bil);
}

function reindexSprinklerRows(bil) {
    const rows = document.querySelectorAll(`.eq-group-${bil}`);
    rows.forEach((row, index) => {
        const roman = romanNumerals[index];
        row.querySelector('td:first-child').textContent = roman;
    });
}
</script>

<?php render_form_footer(); ?>