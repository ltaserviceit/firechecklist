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

        input::-webkit-calendar-picker-indicator { opacity: 1; cursor: pointer; color: var(--text-muted); }

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
        .accordion-toggle .section-status {
            font-size: 12px; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase;
            color: var(--text-muted); background: var(--row-alt); border: 1px solid var(--border);
            padding: 4px 10px; border-radius: 20px; margin-left: auto; margin-right: 4px; flex-shrink: 0;
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

            /* Full-width divider rows (section/group labels spanning multiple columns) stay as plain bars */
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
    return '<select name="' . htmlspecialchars($name) . '" class="cond-select">
        <option value="Not Applicable">Not Applicable</option>
        <option value="Normal">Normal</option>
        <option value="Faulty">Faulty</option>
    </select>';
}
function checklist_select($name) {
    $id = 'chk_' . str_replace(['[',']'], '_', $name) . '_' . uniqid();
    return '<div class="chk-wrap"><input type="checkbox" name="' . htmlspecialchars($name) . '" id="' . $id . '" value="Done"><label for="' . $id . '"></label></div>';
}
// ──────────────────────────────────────────────────────────────────────────

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'technician') {
    header("Location: index.php"); exit();
}

$table_name = "fire_alarm_system_add";
$task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) : 0;

$control_panel_items = [
    "1"  => "MAIN FIRE ALARM PANEL",
    "2"  => "SEALED LEAD ACID BATTERY",
    "3"  => "MAIN FIRE ALARM INCOMING SUPPLY DELIVERING 240V AC AND 24V DC SUPPLY",
    "4"  => "DC VOLTAGE AND CHARGING VOLTAGE ADJUSTED AND MEASURED (BETWEEN 22V TO 26V)",
    "5"  => "POWER SUPPLY UNIT",
    "6"  => "AVR STABILISER",
    "7"  => "FUSES, FACIAL LED DISPLAY, KEYPAD/BUTTON AND PANEL PROCESSOR",
    "8"  => "PRINTER AND PAPER",
    "9"  => "INDICATION OF ALL FUNCTIONS AND STATUS IS CORRECTLY DISPLAYED ON THE LED SCREEN",
    "10" => "FIELD DEVICES, MODULES AND RELAYS",
    "11" => "AC SURGE ARRESTOR",
    "12" => "DC SURGE ARRESTOR",
    "13" => "WIRINGS AND CABLINGS",
    "14" => "MIMIC DIAGRAM DRAWING",
    "15" => "SISTEM PENGAWASAN KEBAKARAN AUTOMATIK ACTIVATION",
];

$device_items = [
    "1" => "ACTIVATE ALARM BELL",
    "2" => "ACTIVATE MANUAL CALL POINT",
    "3" => "ACTIVATE SMOKE DETECTOR",
    "4" => "ACTIVATE HEAT DETECTOR",
    "5" => "ACTIVATE SMOKE HEAT DETECTOR",
    "6" => "ACTIVATE BEAM DETECTOR",
    "7" => "ACTIVATE FLOW SWITCH",
];

$signal_groups = [
    "I"   => ["FROM HOSE REEL PUMP", ["A"=>"AC FAIL","B"=>"DUTY RUN","C"=>"DUTY TRIP","D"=>"STANDBY RUN","E"=>"STANDBY TRIP","F"=>"WATER TANK LOW"]],
    "II"  => ["FROM FIRE SPRINKLER PUMP", ["A"=>"AC FAIL","B"=>"JOCKEY RUN","C"=>"JOCKEY TRIP","D"=>"DUTY RUN","E"=>"DUTY TRIP","F"=>"STANDBY RUN","G"=>"STANDBY TRIP","H"=>"WATER TANK LOW"]],
    "III" => ["FROM WET RISER PUMP", ["A"=>"AC FAIL","B"=>"JOCKEY RUN","C"=>"JOCKEY TRIP","D"=>"DUTY RUN","E"=>"DUTY TRIP","F"=>"STANDBY RUN","G"=>"STANDBY TRIP","H"=>"WATER TANK LOW"]],
    "IV"  => ["PRESSURISED HYDRANT PUMP", ["A"=>"AC FAIL","B"=>"JOCKEY RUN","C"=>"JOCKEY TRIP","D"=>"DUTY RUN","E"=>"DUTY TRIP","F"=>"STANDBY RUN","G"=>"STANDBY TRIP","H"=>"WATER TANK LOW"]],
    "V"   => ["FROM CO2 FIRE SUPPRESSION SYSTEM", []],
    "VI"  => ["FROM FE-13 FIRE SUPPRESSION SYSTEM", []],
    "VII" => ["FROM FM200 FIRE SUPPRESSION SYSTEM", []],
    "VIII"=> ["FROM INERT GAS FIRE SUPPRESSION SYSTEM", []],
    "IX"  => ["FROM WET CHEMICAL SYSTEM", []],
    "X"   => ["FROM P.A SYSTEM", []],
    "XI"  => ["FROM SMOKE SPILLED FAN", []],
    "XII" => ["FROM SMOKE PRESSURISED SYSTEM", []],
    "XIII"=> ["FROM LIFT HOMING", []],
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Panel Profile
    if (!empty($_POST['panel_profile'])) {
        foreach ($_POST['panel_profile'] as $bil => $profile) {
            if (!empty($profile)) {
                $type  = $conn->real_escape_string($_POST['panel_type'][$bil] ?? '');
                $brand = $conn->real_escape_string($_POST['panel_brand'][$bil] ?? '');
                $lp    = $conn->real_escape_string($_POST['panel_loops'][$bil] ?? '');
                $loc   = $conn->real_escape_string($_POST['panel_location'][$bil] ?? '');
                $prof  = $conn->real_escape_string($profile);
                
                $conn->query("INSERT INTO $table_name (task_id, section_name, item_bil, description, location_floor, panel_type, panel_brand, panel_model, panel_qty)
                    VALUES ($task_id, 'PANEL PROFILE', '$bil', '$prof', '$loc', '$type', '$brand', '', '$lp')");
            }
        }
    }
    
    // 2. Control Panel
    foreach ($control_panel_items as $bil => $desc_text) {
        $desc = $conn->real_escape_string($desc_text);
        $cond = $conn->real_escape_string($_POST['cp_condition'][$bil] ?? '');
        $rem  = $conn->real_escape_string($_POST['cp_remarks'][$bil] ?? '');
        
        $img = handle_image_upload('cp_image', $bil);
        if ($img !== "") { $rem .= " [IMG: $img]"; }
        
        $cl   = isset($_POST['cp_checklist'][$bil]) ? 'Done' : 'N/A';
        
        $conn->query("INSERT INTO $table_name (task_id, section_name, item_bil, description, checklist, item_condition, remarks)
            VALUES ($task_id, 'CONTROL PANEL', '$bil', '$desc', '$cl', '$cond', '$rem')");
    }
    
    // 3. Devices (Dynamic Floor Rows)
    foreach ($device_items as $bil => $desc_text) {
        if (!empty($_POST['dev_loc'][$bil])) {
            foreach ($_POST['dev_loc'][$bil] as $loop => $loc_val) {
                $desc     = $conn->real_escape_string($desc_text);
                $loc      = $conn->real_escape_string($loc_val);
                $cond     = $conn->real_escape_string($_POST['dev_condition'][$bil][$loop] ?? '');
                $rem      = $conn->real_escape_string($_POST['dev_remarks'][$bil][$loop] ?? '');
                
                $img = handle_image_upload('dev_image', $bil, $loop);
                if ($img !== "") { $rem .= " [IMG: $img]"; }

                $loop_val = $conn->real_escape_string($_POST['dev_loop'][$bil][$loop] ?? '');
                $cl       = isset($_POST['dev_checklist'][$bil][$loop]) ? 'Done' : 'N/A';
                
                $conn->query("INSERT INTO $table_name (task_id, section_name, item_bil, description, location_floor, zone_loop, checklist, item_condition, remarks)
                    VALUES ($task_id, 'DEVICES', '$bil-$loop', '$desc', '$loc', '$loop_val', '$cl', '$cond', '$rem')");
            }
        }
    }
    
    // 4. Signal Test
    foreach ($signal_groups as $roman => $data) {
        [$label, $subs] = $data;
        if (empty($subs)) {
            $cl   = isset($_POST['sig_checklist'][$roman]) ? 'Done' : 'N/A';
            $cond = $conn->real_escape_string($_POST['sig_condition'][$roman] ?? '');
            $rem  = $conn->real_escape_string($_POST['sig_remarks'][$roman] ?? '');
            
            $img = handle_image_upload('sig_image', $roman);
            if ($img !== "") { $rem .= " [IMG: $img]"; }

            $lbl  = $conn->real_escape_string($label);
            
            $conn->query("INSERT INTO $table_name (task_id, section_name, item_bil, description, checklist, item_condition, remarks)
                VALUES ($task_id, 'SIGNAL TEST', '$roman', '$lbl', '$cl', '$cond', '$rem')");
        } else {
            foreach ($subs as $letter => $sub_label) {
                $cl   = isset($_POST['sig_checklist'][$roman][$letter]) ? 'Done' : 'N/A';
                $cond = $conn->real_escape_string($_POST['sig_condition'][$roman][$letter] ?? '');
                $rem  = $conn->real_escape_string($_POST['sig_remarks'][$roman][$letter] ?? '');
                
                $img = handle_image_upload('sig_image', $roman, $letter);
                if ($img !== "") { $rem .= " [IMG: $img]"; }

                $lbl  = $conn->real_escape_string($label . ' - ' . $sub_label);
                
                $conn->query("INSERT INTO $table_name (task_id, section_name, item_bil, description, checklist, item_condition, remarks)
                    VALUES ($task_id, 'SIGNAL TEST', '$roman-$letter', '$lbl', '$cl', '$cond', '$rem')");
            }
        }
    }
    
    header("Location: tech_menu.php?task_id=$task_id"); exit();
}

render_form_header("Addressable Fire Alarm System");
?>

<div class="topbar">
    <div class="topbar-title">
        <svg viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
        ADDRESSABLE ALARM
    </div>
    <a href="tech_menu.php?task_id=<?= $task_id ?>" class="back-btn">
        <svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        Cancel & Back
    </a>
</div>

<div class="container">
    <div class="page-header">
        <h1>Addressable Fire Alarm System</h1>
        <p>Complete the inspection form below for Task #<?= $task_id ?></p>
    </div>

<form method="POST" enctype="multipart/form-data">

<datalist id="brand_options">
    <option value="ASENWARE">
    <option value="BOSCH">
    <option value="COOPER">
    <option value="DEMCO">
    <option value="EATON">
    <option value="EDWARDS">
    <option value="GENT">
    <option value="GST">
    <option value="HOCHIKI">
    <option value="HONEYWELL">
    <option value="HORING LIH">
    <option value="KENTEC">
    <option value="MENVIER">
    <option value="MORLEY">
    <option value="NITTAN">
    <option value="NOTIFIER">
    <option value="OMEGA">
    <option value="QSS">
    <option value="SIEMENS">
    <option value="SIMPLEX">
    <option value="SRI">
    <option value="SYSTEM SENSOR">
    <option value="TYCO">
    <option value="ZEKI">
</datalist>

<div class="accordion-controls">
    <button type="button" class="btn-accordion-all" onclick="setAllAccordions(true)">Expand All</button>
    <button type="button" class="btn-accordion-all" onclick="setAllAccordions(false)">Collapse All</button>
</div>

<div class="table-responsive" id="section-panel-profile">
<table>
    <thead>
        <tr><th colspan="6" class="section-header" onclick="toggleAccordion(this)">
            <div class="accordion-toggle">
                <span>FIRE ALARM SYSTEM CONTROL PANEL PROFILE</span>
                <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
        </th></tr>
        <tr class="col-header-row">
            <th width="5%" class="center">BIL</th><th width="20%">PANEL PROFILE</th><th width="15%">TYPE OF PANEL</th>
            <th width="15%">BRAND OF PANEL</th><th width="10%" class="center">NO. OF LOOP</th><th width="35%">LOCATION</th>
        </tr>
    </thead>
    <tbody>
        <?php for ($i=1;$i<=5;$i++): ?>
        <tr>
            <td class="center bold" data-label="BIL"><?= $i ?></td>
            <td data-label="Panel Profile">
                <select name="panel_profile[<?=$i?>]" class="cond-select">
                    <option value="">-- Select --</option>
                    <option value="Main Panel">Main Panel</option>
                    <option value="Sub Panel">Sub Panel</option>
                </select>
            </td>
            <td data-label="Type of Panel">
                <select name="panel_type[<?=$i?>]" class="cond-select">
                    <option value="">-- Select --</option>
                    <option value="Conventional">Conventional</option>
                    <option value="Addressable">Addressable</option>
                </select>
            </td>
            <td data-label="Brand of Panel">
                <input type="text" name="panel_brand[<?=$i?>]" list="brand_options" placeholder="Select or Type">
            </td>
            <td data-label="No. of Loop"><input type="text" name="panel_loops[<?=$i?>]"></td>
            <td data-label="Location"><input type="text" name="panel_location[<?=$i?>]"></td>
        </tr>
        <?php endfor; ?>
    </tbody>
</table>
</div>

<div class="table-responsive collapsed" id="section-control-panel">
<table>
    <thead>
        <tr><th colspan="5" class="section-header" onclick="toggleAccordion(this)">
            <div class="accordion-toggle">
                <span>ADDRESSABLE FIRE ALARM SYSTEM - CONTROL PANEL</span>
                <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
        </th></tr>
        <tr class="col-header-row"><th width="5%" class="center">BIL</th><th width="45%">DESCRIPTION</th><th width="10%" class="center">CHECKLIST</th><th width="15%">CONDITION</th><th width="25%">REMARKS & PHOTOS</th></tr>
    </thead>
    <tbody>
        <?php foreach ($control_panel_items as $bil => $desc): ?>
        <tr>
            <td class="center bold" data-label="BIL"><?= $bil ?></td>
            <td class="bold" data-label="Description"><?= htmlspecialchars($desc) ?></td>
            <td class="center" data-label="Checklist"><?= checklist_select("cp_checklist[$bil]") ?></td>
            <td data-label="Condition"><?= status_select("cp_condition[$bil]") ?></td>
            <td data-label="Remarks & Photos">
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

<div class="table-responsive collapsed" id="section-devices">
<table>
    <thead>
        <tr><th colspan="6" class="section-header" onclick="toggleAccordion(this)">
            <div class="accordion-toggle">
                <span>ADDRESSABLE FIRE ALARM SYSTEM - DEVICES</span>
                <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
        </th></tr>
        <tr class="col-header-row">
            <th width="5%" class="center">BIL</th>
            <th width="35%">DESCRIPTION</th>
            <th width="10%" class="center">LOOP</th>
            <th width="10%" class="center">CHECKLIST</th>
            <th width="15%">CONDITION</th>
            <th width="25%">REMARKS & PHOTOS</th>
        </tr>
    </thead>
    <tbody id="devices_tbody">
        <?php foreach ($device_items as $bil => $desc): ?>
            <tr class="group-row" style="background-color: var(--surface); border-top: 3px solid var(--border);">
                <td class="center bold" data-label="BIL"><?= $bil ?></td>
                <td colspan="5" class="bold" style="color: var(--primary); font-size: 16px;">
                    <?= htmlspecialchars($desc) ?>
                    <button type="button" class="btn-add-floor" onclick="addFloorRow(<?= $bil ?>)" style="margin-left: 14px;">
                        + Add Floor / Loop
                    </button>
                </td>
            </tr>
            <!-- Default 1st Floor Row -->
            <tr class="dev-group-<?= $bil ?>">
                <td class="center bold" data-label="Floor">I</td>
                <td class="indent" data-label="Location / Floor"><input type="text" name="dev_loc[<?=$bil?>][I]" placeholder="Location / Floor"></td>
                <td data-label="Loop"><input type="text" name="dev_loop[<?=$bil?>][I]"></td>
                <td class="center" data-label="Checklist"><?= checklist_select("dev_checklist[$bil][I]") ?></td>
                <td data-label="Condition"><?= status_select("dev_condition[$bil][I]") ?></td>
                <td data-label="Remarks & Photos">
                    <div class="remark-group">
                        <input type="text" name="dev_remarks[<?=$bil?>][I]" placeholder="Remarks">
                        <label class="file-upload-btn" title="Attach Image">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                            <input type="file" name="dev_image[<?=$bil?>][I]" class="file-input" accept="image/*" capture="environment" onchange="if(this.files.length > 0) { this.parentElement.classList.add('attached'); this.parentElement.title='Image Attached'; }">
                        </label>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
</div>

<div class="table-responsive collapsed" id="section-signal-test">
<table>
    <thead>
        <tr><th colspan="5" class="section-header" onclick="toggleAccordion(this)">
            <div class="accordion-toggle">
                <span>ADDRESSABLE FIRE ALARM SYSTEM - SIGNAL TEST</span>
                <svg class="accordion-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
        </th></tr>
        <tr class="col-header-row"><th width="5%" class="center">BIL</th><th width="45%">DESCRIPTION</th><th width="10%" class="center">CHECKLIST</th><th width="15%">CONDITION</th><th width="25%">REMARKS & PHOTOS</th></tr>
    </thead>
    <tbody>
        <?php foreach ($signal_groups as $roman => [$label, $subs]): ?>
            <?php if (empty($subs)): ?>
            <tr>
                <td class="center bold" data-label="BIL"><?= $roman ?></td>
                <td class="bold" data-label="Description"><?= htmlspecialchars($label) ?></td>
                <td class="center" data-label="Checklist"><?= checklist_select("sig_checklist[$roman]") ?></td>
                <td data-label="Condition"><?= status_select("sig_condition[$roman]") ?></td>
                <td data-label="Remarks & Photos">
                    <div class="remark-group">
                        <input type="text" name="sig_remarks[<?=$roman?>]" placeholder="Remarks">
                        <label class="file-upload-btn" title="Attach Image">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                            <input type="file" name="sig_image[<?=$roman?>]" class="file-input" accept="image/*" capture="environment" onchange="if(this.files.length > 0) { this.parentElement.classList.add('attached'); this.parentElement.title='Image Attached'; }">
                        </label>
                    </div>
                </td>
            </tr>
            <?php else: ?>
            <tr class="group-row" style="background-color: var(--surface); border-top: 3px solid var(--border);">
                <td class="center bold" data-label="BIL"><?= $roman ?></td>
                <td colspan="4" class="bold" style="color: var(--primary); font-size: 16px;"><?= htmlspecialchars($label) ?></td>
            </tr>
                <?php foreach ($subs as $letter => $sub): ?>
                <tr>
                    <td class="center bold" data-label="BIL"><?= $letter ?></td>
                    <td class="indent" data-label="Description"><?= htmlspecialchars($sub) ?></td>
                    <td class="center" data-label="Checklist"><?= checklist_select("sig_checklist[$roman][$letter]") ?></td>
                    <td data-label="Condition"><?= status_select("sig_condition[$roman][$letter]") ?></td>
                    <td data-label="Remarks & Photos">
                        <div class="remark-group">
                            <input type="text" name="sig_remarks[<?=$roman?>][<?=$letter?>]" placeholder="Remarks">
                            <label class="file-upload-btn" title="Attach Image">
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                                <input type="file" name="sig_image[<?=$roman?>][<?=$letter?>]" class="file-input" accept="image/*" capture="environment" onchange="if(this.files.length > 0) { this.parentElement.classList.add('attached'); this.parentElement.title='Image Attached'; }">
                            </label>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
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

function addFloorRow(bil) {
    const rows = document.querySelectorAll(`.dev-group-${bil}`);
    const nextIndex = rows.length;
    
    if (nextIndex >= romanNumerals.length) {
        alert("Maximum floor limit reached for this item.");
        return;
    }

    const roman = romanNumerals[nextIndex];
    const lastRow = rows[rows.length - 1];
    const uniqueId = 'chk_' + bil + '_' + roman + '_' + Math.random().toString(36).substr(2, 9);

    const newRow = document.createElement('tr');
    newRow.className = `dev-group-${bil}`;
    newRow.innerHTML = `
        <td class="center bold" data-label="Floor">${roman}</td>
        <td class="indent" data-label="Location / Floor"><input type="text" name="dev_loc[${bil}][${roman}]" placeholder="Location / Floor"></td>
        <td data-label="Loop"><input type="text" name="dev_loop[${bil}][${roman}]"></td>
        <td class="center" data-label="Checklist">
            <div class="chk-wrap">
                <input type="checkbox" name="dev_checklist[${bil}][${roman}]" id="${uniqueId}" value="Done">
                <label for="${uniqueId}"></label>
            </div>
        </td>
        <td data-label="Condition">
            <select name="dev_condition[${bil}][${roman}]" class="cond-select">
                <option value="Not Applicable">Not Applicable</option>
                <option value="Normal">Normal</option>
                <option value="Faulty">Faulty</option>
            </select>
        </td>
        <td data-label="Remarks & Photos">
            <div class="remark-group">
                <input type="text" name="dev_remarks[${bil}][${roman}]" placeholder="Remarks">
                <label class="file-upload-btn" title="Attach Image">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                    <input type="file" name="dev_image[${bil}][${roman}]" class="file-input" accept="image/*" capture="environment" onchange="if(this.files.length > 0) { this.parentElement.classList.add('attached'); this.parentElement.title='Image Attached'; }">
                </label>
                <button type="button" class="btn-delete-row" title="Delete Row" onclick="deleteFloorRow(this)">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                </button>
            </div>
        </td>
    `;

    lastRow.parentNode.insertBefore(newRow, lastRow.nextSibling);
    reindexFloorRows(bil);
}

function deleteFloorRow(button) {
    const row = button.closest('tr');
    const bil = row.className.replace('dev-group-', '');
    row.remove();
    reindexFloorRows(bil);
}

function reindexFloorRows(bil) {
    const rows = document.querySelectorAll(`.dev-group-${bil}`);
    rows.forEach((row, index) => {
        const roman = romanNumerals[index];
        row.querySelector('td:first-child').textContent = roman;
    });
}
</script>

<?php render_form_footer(); ?>