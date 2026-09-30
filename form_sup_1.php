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
        th.sub-header { background: var(--row-alt) !important; color: var(--primary) !important; font-size: 14px; padding: 14px 20px; border-bottom: 2px solid var(--border); }
        
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
    $id = 'chk_' . str_replace(['[',']'], '_', $name);
    return '<div class="chk-wrap"><input type="checkbox" name="' . htmlspecialchars($name) . '" id="' . $id . '" value="Done"><label for="' . $id . '"></label></div>';
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'technician') { header("Location: index.php"); exit(); }
$table_name = "fire_suppression_system_1";
$task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) : 0;

$profile_items = [
    "1"=>"AGENT", "2"=>"LOCATION/ROOM", "3"=>"TYPE OF PANEL", "4"=>"NO. OF ZONE",
    "5"=>"BRAND", "6"=>"QUANTITY OF CYLINDER", "7"=>"CYLINDER CAPACITY (KG)", "8"=>"QUANTITY OF PILOT CYLINDER",
    "9"=>"MANUAL KEY SWITCH", "10"=>"MANUAL PULL BOX", "11"=>"MANUAL ABORT SWITCH", "12"=>"QUANTITY OF NOZZLE",
    "13"=>"QUANTITY OF HEAT DETECTORS", "14"=>"QUANTITY OF SMOKE DETECTORS",
];

$checklist_items = [
    "1"=>"FIRE SUPPRESSION CONTROL PANEL","2"=>"SEALED LEAD ACID BATTERY",
    "3"=>"FIRE SUPPRESSION CONTROL PANEL INCOMING SUPPLY DELIVERING 240V AC AND 24V DC SUPPLY",
    "4"=>"DC VOLTAGE AND CHARGING VOLTAGE ADJUSTED AND MEASURED (BETWEEN 22V TO 26V)",
    "5"=>"POWER SUPPLY UNIT","6"=>"FUSES, LED LIGHT BULB, SWITCHES & BUZZER",
    "7"=>"VOLT METER","8"=>"AMPERE METER","9"=>"WIRINGS AND CABLINGS",
    "10"=>"EACH ZONES OPERATES CORRECTLY BETWEEN ALARM, FAULT AND ISOLATE INDICATION",
    "11"=>"ALARM BELL INTERMITTENT, GENERAL ALARM, TRIPPING SIGNALS, AND INDICATIONS SOUND PERFECTLY ACCORDING TO SEQUENCE",
    "12"=>"HEAT & SMOKE DETECTOR OPERATIONS","13"=>"PILOT CYLINDER, 24V DC SIGNAL, TESTED",
    "14"=>'FIRE SUPPRESSION DISCHARGE WITHIN 30 SECONDS FROM "DOUBLE KNOCKING" DETECTION',
    "15"=>"TRIPPING OF MECHANICAL FAN AND ASBESTOS CURTAIN ON ALARM MODE",
    "16"=>"TRIPPING AND CYLINDER BRACKET RIGIDLY MOUNTED",
    "17"=>"ALL TUBING CONNECTIONS TO CO2 SYSTEM CYLINDER",
    "18"=>"PRESSURE GAUGE IN CYLINDER IN OPERATE RANGE",
    "19"=>"TESTING OF FIRE CURTAIN RELEASE SOLENOID","20"=>"FLASHING LIGHT INDICATOR",
    "21"=>"EVACUATE SIGN",
    "22"=>"VERIFICATION FIRE SUPPRESSION GAS CYLINDER OPERATION LIFESPAN (NOT EXCEEDING TEN YEARS).",
    "23"=>"CONDITION OF MANIFOLD, CONNECTING HOSE AND DISCHARGE HOSE (FREE OF CRACKING, KINKING AND FOLDING)",
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (!empty($_POST['panel_location'])) {
        foreach ($_POST['panel_location'] as $bil => $loc) {
            $loc_val  = $conn->real_escape_string($loc);
            $cap_val = $conn->real_escape_string($_POST['panel_cap'][$bil] ?? '');
            $qty_val = $conn->real_escape_string($_POST['panel_cyl_qty'][$bil] ?? '');
            
            if ($loc_val !== '' || $cap_val !== '' || $qty_val !== '') {
                $conn->query("INSERT INTO $table_name (task_id, section_name, item_bil, description, location_floor, panel_type, panel_qty) 
                    VALUES ($task_id, 'PANEL PROFILE', '$bil', 'CO2 CYLINDER', '$loc_val', '$cap_val', '$qty_val')");
            }
        }
    }

    foreach ($profile_items as $bil => $desc_text) {
        $desc = $conn->real_escape_string($desc_text);
        $val  = $conn->real_escape_string($_POST['profile_status'][$bil] ?? '');
        $rem  = $conn->real_escape_string($_POST['profile_remarks'][$bil] ?? '');
        
        $img = handle_image_upload('profile_image', $bil);
        if ($img !== "") { $rem .= " [IMG: $img]"; }
        
        $conn->query("INSERT INTO $table_name (task_id,section_name,item_bil,description,checklist,remarks) VALUES ($task_id,'SYSTEM INFO','$bil','$desc','$val','$rem')");
    }
    
    foreach ($checklist_items as $bil => $desc_text) {
        $desc = $conn->real_escape_string($desc_text);
        $cond = $conn->real_escape_string($_POST['cl_condition'][$bil] ?? '');
        $rem  = $conn->real_escape_string($_POST['cl_remarks'][$bil] ?? '');
        
        $img = handle_image_upload('cl_image', $bil);
        if ($img !== "") { $rem .= " [IMG: $img]"; }
        
        $cl   = isset($_POST['cl_checklist'][$bil]) ? 'Done' : 'N/A';
        $conn->query("INSERT INTO $table_name (task_id,section_name,item_bil,description,checklist,item_condition,remarks) VALUES ($task_id,'MAINTENANCE','$bil','$desc','$cl','$cond','$rem')");
    }
    header("Location: tech_menu.php?task_id=$task_id"); exit();
}

render_form_header("CO2 Fire Suppression System - Checklist");
?>

<div class="topbar">
    <div class="topbar-title">
        <svg viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
        CO2 SUPPRESSION
    </div>
    <a href="tech_menu.php?task_id=<?= $task_id ?>" class="back-btn">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        Cancel & Back
    </a>
</div>

<div class="container">
    <div class="page-header">
        <h1>CO2 Suppression System Checklist</h1>
        <p>Complete the inspection form below for Task #<?= $task_id ?></p>
    </div>

<form method="POST" enctype="multipart/form-data">

<div class="accordion-controls">
    <button type="button" class="btn-accordion-all" onclick="setAllAccordions(true)">Expand All</button>
    <button type="button" class="btn-accordion-all" onclick="setAllAccordions(false)">Collapse All</button>
</div>

<div class="table-responsive" id="section-cylinder">
<table>
    <thead>
        <tr><th colspan="4" class="section-header" onclick="toggleAccordion(this)">
            <div class="accordion-toggle">
                <span>FIRE SUPPRESSION SYSTEM</span>
                <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
        </th></tr>
        <tr class="col-header-row"><th colspan="4" class="sub-header">CO2 FIRE SUPPRESSION SYSTEM</th></tr>
        <tr class="col-header-row">
            <th width="10%" class="center">BIL</th>
            <th width="40%">LOCATION/ROOM</th>
            <th width="25%">CYLINDER CAPACITY (KG)</th>
            <th width="25%">QUANTITY OF CYLINDER</th>
        </tr>
    </thead>
    <tbody>
        <?php for ($i=1; $i<=5; $i++): ?>
        <tr>
            <td class="center bold" data-label="BIL"><?= $i ?></td>
            <td data-label="Location/Room"><input type="text" name="panel_location[<?= $i ?>]"></td>
            <td data-label="Cylinder Capacity (KG)"><input type="text" name="panel_cap[<?= $i ?>]"></td>
            <td data-label="Quantity of Cylinder"><input type="text" name="panel_cyl_qty[<?= $i ?>]"></td>
        </tr>
        <?php endfor; ?>
    </tbody>
</table>
</div>

<div class="table-responsive collapsed" id="section-panel-cylinder">
<table>
    <thead>
        <tr><th colspan="4" class="section-header" onclick="toggleAccordion(this)">
            <div class="accordion-toggle">
                <span>FIRE SUPPRESSION SYSTEM PANEL & CYLINDER</span>
                <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
        </th></tr>
        <tr class="col-header-row"><th colspan="4" class="sub-header">CO2 FIRE SUPPRESSION SYSTEM</th></tr>
        <tr class="col-header-row"><th width="5%" class="center">BIL</th><th width="45%">DESCRIPTION</th><th width="15%">STATUS</th><th width="35%">REMARKS & PHOTOS</th></tr>
    </thead>
    <tbody>
        <?php foreach($profile_items as $bil=>$desc): ?>
        <tr>
            <td class="center bold" data-label="BIL"><?= $bil ?></td>
            <td class="bold" data-label="Description"><?= htmlspecialchars($desc) ?></td>
            <td data-label="Status"><input type="text" name="profile_status[<?=$bil?>]"></td>
            <td data-label="Remarks & Photos">
                <div class="remark-group">
                    <input type="text" name="profile_remarks[<?=$bil?>]" placeholder="Remarks">
                    <label class="file-upload-btn" title="Attach Image">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                        <input type="file" name="profile_image[<?=$bil?>]" class="file-input" accept="image/*" capture="environment" onchange="if(this.files.length > 0) { this.parentElement.classList.add('attached'); this.parentElement.title='Image Attached'; }">
                    </label>
                </div>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>

<div class="table-responsive collapsed" id="section-maintenance">
<table>
    <thead>
        <tr><th colspan="5" class="section-header" onclick="toggleAccordion(this)">
            <div class="accordion-toggle">
                <span>FIRE SUPPRESSION SYSTEM MAINTENANCE CHECKLIST</span>
                <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
        </th></tr>
        <tr class="col-header-row"><th colspan="5" class="sub-header">CO2 FIRE SUPPRESSION SYSTEM</th></tr>
        <tr class="col-header-row"><th width="5%" class="center">BIL</th><th width="45%">DESCRIPTION</th><th width="10%" class="center">CHECKLIST</th><th width="15%">CONDITION</th><th width="25%">REMARKS & PHOTOS</th></tr>
    </thead>
    <tbody>
        <?php foreach($checklist_items as $bil=>$desc): ?>
        <tr>
            <td class="center bold" data-label="BIL"><?= $bil ?></td>
            <td class="bold" data-label="Description"><?= htmlspecialchars($desc) ?></td>
            <td class="center" data-label="Checklist"><?= checklist_checkbox("cl_checklist[$bil]") ?></td>
            <td data-label="Condition"><?= status_select("cl_condition[$bil]") ?></td>
            <td data-label="Remarks & Photos">
                <div class="remark-group">
                    <input type="text" name="cl_remarks[<?=$bil?>]" placeholder="Remarks">
                    <label class="file-upload-btn" title="Attach Image">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                        <input type="file" name="cl_image[<?=$bil?>]" class="file-input" accept="image/*" capture="environment" onchange="if(this.files.length > 0) { this.parentElement.classList.add('attached'); this.parentElement.title='Image Attached'; }">
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
</script>

<?php render_form_footer(); ?>