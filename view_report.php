<?php
session_start();
require 'db.php';

// 1. Security Check: Only Admin can view reports
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

$task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) : 0;

if ($task_id === 0) {
    die("Invalid Task ID.");
}

// 2. Fetch General Task Information
$stmt = $conn->prepare("SELECT * FROM tasks WHERE task_id = ?");
$stmt->bind_param("i", $task_id);
$stmt->execute();
$task_result = $stmt->get_result();

if ($task_result->num_rows === 0) {
    die("Task not found.");
}
$task = $task_result->fetch_assoc();

// 3. Define all system tables to scan
$system_tables = [
    'fire_alarm_system_add' => 'Addressable Fire Alarm System',
    'fire_alarm_system_con' => 'Conventional Fire Alarm System',
    'fireman_intercom_system' => 'Fireman Intercom System',
    'wet_chemical_system' => 'Wet Chemical System',
    'fire_hose_reel_system' => 'Fire Hose Reel System',
    'fire_sprinkler_system' => 'Fire Sprinkler System',
    'fire_suppression_system_1' => 'CO2 Fire Suppression System',
    'fire_suppression_system_2' => 'FM200 Fire Suppression System',
    'fire_suppression_system_3' => 'FE-13 Fire Suppression System',
    'fire_suppression_system_4' => 'Inert Gas Fire Suppression System',
    'wet_riser_system' => 'Wet Riser System',
    'dry_riser_system' => 'Dry Riser System',
    'pressurised_hydrant_system' => 'Pressurised Hydrant System'
];

// 4. Scan ALL tables and collect data, using the SMART GROUPING logic
$report_data = [];

foreach ($system_tables as $table => $system_name) {
    $query = "SELECT * FROM `$table` WHERE task_id = $task_id ORDER BY id ASC";
    $result = @$conn->query($query);
    
    if ($result && $result->num_rows > 0) {
        $submissions = [];
        while ($row = $result->fetch_assoc()) {
            $sec = $row['section_name'] ?? 'GENERAL';
            $bil = $row['item_bil'];
            
            $sub_idx = 0;
            while (isset($submissions[$sub_idx][$sec][$bil])) {
                $sub_idx++;
            }
            $submissions[$sub_idx][$sec][$bil] = $row;
        }
        $report_data[$table] = [
            'system_name' => $system_name,
            'submissions' => $submissions
        ];
    }
}

// Helper to format checklist marks 
function format_check($chk) {
    if (empty($chk) || $chk === '-') return '-';
    // Green Tick Mark
    if ($chk === 'Done') return "<span class='badge badge-done' title='Done' style='padding: 6px 10px; font-size: 14px;'>&#10004;</span>";
    // Red Cross Mark
    if ($chk === 'N/A') return "<span class='badge badge-cross' title='Not Checked' style='padding: 6px 10px; font-size: 14px;'>&#10008;</span>";
    return htmlspecialchars($chk);
}

// Helper to highlight Faulty conditions
function format_condition($cond) {
    if (empty($cond) || $cond === '-') return '-';
    if ($cond === 'Faulty') return "<span class='badge badge-faulty'>Faulty</span>";
    return htmlspecialchars($cond);
}

// NEW HELPER: Formats the remark text and extracts the image to display it!
function format_remark_with_image($remark_string) {
    if (empty($remark_string) || $remark_string === '-') return '-';
    
    $text = $remark_string;
    $img_html = "";
    
    // Check if there is an image tag in the text
    if (preg_match('/\[IMG: (.*?)\]/', $remark_string, $matches)) {
        $img_path = htmlspecialchars($matches[1]);
        $text = trim(str_replace($matches[0], '', $remark_string)); // Remove the tag from the text
        
        // Create the image thumbnail HTML
        $img_html = "<br><a href='$img_path' target='_blank'>
                        <img src='$img_path' style='max-width: 140px; max-height: 140px; border-radius: 6px; margin-top: 8px; border: 1px solid #ddd; box-shadow: 0 2px 4px rgba(0,0,0,0.05);'>
                     </a>";
    }
    
    return htmlspecialchars($text) . $img_html;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Service Report - Task #<?= $task_id ?> - FireSafe Systems</title>
    <style>
        :root {
            --primary: #C41E3A;
            --primary-hover: #a8182f;
            --bg-color: #f3f4f6;
            --surface: #ffffff;
            --text-main: #111827;
            --text-muted: #6b7280;
            --border: #e5e7eb;
            --success-bg: #ecfdf5;
            --success-text: #059669;
            --warning-bg: #fffbeb;
            --warning-text: #d97706;
            --danger-bg: #fef2f2;
            --danger-text: #dc2626;
            --row-alt: #f9fafb;
            --row-hover: #f3f4f6;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: var(--bg-color); color: var(--text-main); padding: clamp(16px, 4vw, 40px); }

        /* CONTROLS */
        .controls { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; margin-bottom: 24px; max-width: 1200px; margin-left: auto; margin-right: auto; }
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; border: 1.5px solid transparent; transition: all 0.2s; text-decoration: none; }
        .btn-back { background: var(--surface); color: var(--text-main); border-color: var(--border); box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .btn-back:hover { background: var(--row-hover); }
        .btn-print { background: var(--primary); color: white; box-shadow: 0 4px 12px rgba(196,30,58,0.2); }
        .btn-print:hover { background: var(--primary-hover); transform: translateY(-2px); box-shadow: 0 6px 16px rgba(196,30,58,0.25); }

        /* CONTAINER */
        .report-container { max-width: 1200px; margin: auto; background: var(--surface); padding: clamp(30px, 6vw, 60px); border-radius: 12px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); border: 1px solid var(--border); }

        /* TASK DETAILS */
        .task-details { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 24px; background: var(--row-alt); padding: 32px; border-radius: 12px; border: 1px solid var(--border); margin-bottom: 48px; }
        .task-details p { margin: 10px 0; font-size: 15px; color: var(--text-main); display: flex; align-items: flex-start; }
        .task-details strong { color: var(--text-muted); text-transform: uppercase; font-size: 13px; letter-spacing: 0.5px; width: 140px; flex-shrink: 0; margin-top: 1px; }

        /* SYSTEM SEPARATOR */
        .system-separator { background: var(--surface); color: var(--primary); padding: 18px 24px; margin-top: 60px; margin-bottom: 24px; font-size: 16px; font-weight: 700; border-radius: 8px; border: 1px solid var(--border); border-left: 6px solid var(--primary); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); text-transform: uppercase; letter-spacing: 0.5px; }

        /* TABLES - HIGH READABILITY */
        .table-responsive { overflow-x: auto; width: 100%; margin-bottom: 32px; border-radius: 12px; border: 1px solid var(--border); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        table { width: 100%; border-collapse: collapse; font-size: 14px; min-width: 800px; }
        th, td { padding: 14px 16px; text-align: left; vertical-align: middle; border-bottom: 1px solid var(--border); }
        
        /* Table Headers */
        th { background: var(--row-alt); text-align: center; font-weight: 600; color: var(--text-muted); text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; }
        th.section-header { background: var(--surface) !important; color: var(--text-main) !important; font-size: 15px; padding: 20px 16px; font-weight: 700; border-bottom: 2px solid var(--border); }
        th.sub-header { background: var(--surface) !important; color: var(--text-muted) !important; font-size: 13px; border-bottom: 2px solid var(--border); }
        
        /* Zebra Striping & Hover */
        tbody tr:nth-child(even) td { background-color: var(--row-alt); }
        tbody tr:hover td { background-color: var(--row-hover); }
        tr:last-child td { border-bottom: none; }
        
        /* Text Utilities */
        .center { text-align: center; }
        .bold { font-weight: 600; color: var(--text-main); }
        .indent { padding-left: 32px !important; color: var(--text-muted); }

        /* Custom alignment layout for horizontal fields */
        .split-press-view { display: flex; gap: 24px; width: 100%; justify-content: center; }
        .split-press-view div { display: flex; flex-direction: column; align-items: center; gap: 4px; }
        .split-press-view label { font-size: 11px; font-weight: bold; color: var(--text-muted); text-transform: uppercase; border-bottom: 1px solid var(--border); padding-bottom: 2px; }
        
        /* BADGES */
        .badge { padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; justify-content: center; gap: 4px; box-shadow: inset 0 0 0 1px rgba(0,0,0,0.05); }
        .badge-done { background-color: var(--success-bg); color: var(--success-text); }
        .badge-na { background-color: var(--warning-bg); color: var(--warning-text); }
        .badge-cross { background-color: var(--danger-bg); color: var(--danger-text); border: 1px solid #fca5a5; }
        .badge-faulty { background-color: var(--danger-bg); color: var(--danger-text); border: 1px solid #fca5a5; }
        
        /* SIGNATURES */
        .signatures-wrapper { margin-top: 80px; display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 40px; page-break-inside: avoid; }
        .sign-box { text-align: center; background: var(--row-alt); border: 1px solid var(--border); border-radius: 12px; padding: 32px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05); }
        .sign-img-container { height: 120px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; }
        .sign-img-container img { max-height: 120px; max-width: 100%; object-fit: contain; }
        .sign-line { border-top: 2px solid var(--border); margin: 0 auto 20px; width: 80%; }
        .sign-name { margin: 0 0 6px 0; font-weight: 700; color: var(--text-main); text-transform: uppercase; font-size: 16px; letter-spacing: 0.5px; }
        .sign-role { margin: 0; font-size: 13px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }

        /* PRINT STYLES */
        @media print {
            body { background: white; padding: 0; font-size: 12px; }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
            .report-container { box-shadow: none; padding: 0; max-width: 100%; border: none; }
            .controls { display: none; }
            .table-responsive { overflow-x: visible; border: 1px solid #ccc; box-shadow: none; margin-bottom: 20px; }
            table { min-width: 100%; }
            tr { page-break-inside: avoid; }
            th { background: #f3f4f6 !important; color: #000 !important; }
            .system-separator { border: 1px solid #ccc !important; border-left: 4px solid var(--primary) !important; color: #000; background: #f9fafb; margin-top: 30px; box-shadow: none; }
            .badge-done { background-color: #ecfdf5 !important; color: #059669 !important; border: 1px solid #a7f3d0; }
            .badge-na { background-color: #fffbeb !important; color: #d97706 !important; border: 1px solid #fde68a; }
            .badge-cross { background-color: #fef2f2 !important; color: #dc2626 !important; border: 1px solid #fca5a5; }
            .badge-faulty { background-color: #fef2f2 !important; color: #dc2626 !important; border: 1px solid #fca5a5; }
            .sign-box { border: 1px solid #ccc; break-inside: avoid; background: transparent; box-shadow: none; }
            tbody tr:nth-child(even) td { background-color: #f9fafb !important; }
            a { text-decoration: none; color: inherit; } /* Stop links from looking blue in print */
        }
        
        /* MOBILE */
        @media (max-width: 600px) {
            .controls { flex-direction: column; align-items: stretch; }
            .btn { justify-content: center; width: 100%; }
            .task-details { grid-template-columns: 1fr; padding: 20px; }
            .task-details p { flex-direction: column; gap: 4px; }
            .task-details strong { width: 100%; }
            .signatures-wrapper { grid-template-columns: 1fr; gap: 24px; margin-top: 40px; }
        }
    </style>
</head>
<body>

    <div class="controls">
        <a href="admin_dashboard.php" class="btn btn-back">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Back to Dashboard
        </a>
        <a href="export_pdf.php?task_id=<?= $task_id ?>" target="_blank" class="btn btn-print">
            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
            Download Official PDF
        </a>
    </div>

    <div class="report-container">
        
        <div class="report-header" style="text-align: center; border-bottom: 1px solid var(--border); padding-bottom: 24px; margin-bottom: 30px;">
            <h1 style="margin: 0 0 12px 0; color: var(--text-main); font-size: clamp(24px, 5vw, 32px); text-transform: uppercase; font-weight: 900; letter-spacing: 0.5px; line-height: 1.2;">
                FIRE PROTECTION SYSTEM<br>SERVICE REPORT
            </h1>
            <div style="font-size: 13px; font-weight: 700; margin-bottom: 4px; color: var(--text-main);">
                SYSTEMS INCLUDED:
            </div>
            <div style="font-size: 13px; color: var(--text-muted);">
                <?php 
                    $included = array_column($report_data, 'system_name');
                    echo !empty($included) ? implode(' &bull; ', $included) : 'None';
                ?>
            </div>
        </div>

        <div class="task-details">
            <div>
                <p><strong>Task ID</strong> #<?= str_pad($task['task_id'], 5, '0', STR_PAD_LEFT) ?></p>
                <p><strong>Company Name</strong> <?= htmlspecialchars($task['company_name'] ?? 'N/A') ?></p>
                <p><strong>Building Name</strong> <?= htmlspecialchars($task['building_name'] ?? 'N/A') ?></p>
            </div>
            <div>
                <p><strong>Date Created</strong> <?= date('d M Y, h:i A', strtotime($task['created_at'])) ?></p>
                <p><strong>Status</strong> <?= htmlspecialchars($task['status']) ?></p>
                <p><strong>Inspected By</strong> Technician (ID: <?= htmlspecialchars($task['tech_id'] ?? 'Unknown') ?>)</p>
            </div>
        </div>

        <?php if (empty($report_data)): ?>
            <div style="text-align:center; padding: 60px 20px; border: 2px dashed var(--border); border-radius: 12px; color: var(--text-muted); background: var(--row-alt);">
                <svg viewBox="0 0 24 24" width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 16px;"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="9" y1="15" x2="15" y2="15"></line></svg>
                <h3 style="margin: 0; font-weight: 600; color: var(--text-main);">No Checklist Data Found</h3>
                <p style="margin: 8px 0 0 0; font-size: 14px;">This task has no associated system checks.</p>
            </div>
        <?php else: ?>
            
            <?php foreach ($report_data as $table => $table_data): ?>
                <?php 
                $system_name = $table_data['system_name'];
                
                foreach ($table_data['submissions'] as $sub_idx => $sections): 
                    $loc_label = "";
                    if (isset($sections['PANEL PROFILE'])) {
                        foreach ($sections['PANEL PROFILE'] as $item) {
                            if (!empty($item['location_floor'])) {
                                $loc_label = " - " . strtoupper($item['location_floor']);
                                break;
                            }
                        }
                    }
                    if (empty($loc_label) && count($table_data['submissions']) > 1) {
                        $loc_label = " - SUBMISSION " . ($sub_idx + 1);
                    }
                ?>
                    
                    <div class="system-separator"><?= htmlspecialchars($system_name) ?><?= htmlspecialchars($loc_label) ?></div>

                    <?php foreach ($sections as $section_name => $items_keyed): ?>
                        <?php $rows = array_values($items_keyed); ?>
                        
                        <?php if ($section_name === 'PANEL PROFILE'): ?>
                            
                            <?php if (strpos($table, 'riser') !== false): ?>
                                <div class="table-responsive">
                                <table>
                                    <thead>
                                        <tr><th colspan="4" class="section-header"><?= strtoupper($system_name) ?></th></tr>
                                        <tr><th width="10%">BIL</th><th width="40%">LOCATION</th><th width="30%">STACK</th><th width="20%">NO. OF STACK</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($rows as $row): ?>
                                        <tr>
                                            <td class="center bold"><?= htmlspecialchars($row['item_bil']) ?></td>
                                            <td><?= htmlspecialchars($row['location_floor'] ?: '-') ?></td>
                                            <td><?= htmlspecialchars($row['panel_type'] ?: '-') ?></td>
                                            <td><?= htmlspecialchars($row['panel_qty'] ?: '-') ?></td>
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
                                        <?php foreach ($rows as $row): ?>
                                        <tr>
                                            <td class="center bold"><?= htmlspecialchars($row['item_bil']) ?></td>
                                            <td><?= htmlspecialchars($row['location_floor'] ?: '-') ?></td>
                                            <td><?= htmlspecialchars($row['panel_type'] ?: '-') ?></td>
                                            <td><?= htmlspecialchars($row['panel_qty'] ?: '-') ?></td>
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
                                        <tr><th width="5%" class="center">BIL</th><th width="20%">PANEL PROFILE</th><th width="15%">TYPE OF PANEL</th><th width="15%">BRAND OF PANEL</th><th width="12%">MODEL</th><th width="10%">QTY/ZONE</th><th width="23%">LOCATION</th></tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($rows as $row): ?>
                                        <tr>
                                            <td class="center bold"><?= htmlspecialchars($row['item_bil']) ?></td>
                                            <td class="bold"><?= htmlspecialchars($row['description']) ?></td>
                                            <td><?= htmlspecialchars($row['panel_type'] ?: '-') ?></td>
                                            <td><?= htmlspecialchars($row['panel_brand'] ?: '-') ?></td>
                                            <td><?= htmlspecialchars($row['panel_model'] ?: '-') ?></td>
                                            <td class="center"><?= htmlspecialchars($row['panel_qty'] ?: '-') ?></td>
                                            <td><?= htmlspecialchars($row['location_floor'] ?: '-') ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                                </div>
                            <?php endif; ?>

                        <?php elseif ($section_name === 'SYSTEM INFO'): ?>
                            <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr><th colspan="4" class="section-header">FIRE SUPPRESSION SYSTEM PANEL & CYLINDER</th></tr>
                                    <tr><th colspan="4" class="sub-header"><?= strtoupper($system_name) ?></th></tr>
                                    <tr><th width="5%" class="center">BIL</th><th width="55%">DESCRIPTION</th><th width="20%" class="center">STATUS</th><th width="20%">REMARKS</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rows as $row): ?>
                                    <tr>
                                        <td class="center bold"><?= htmlspecialchars($row['item_bil']) ?></td>
                                        <td class="bold"><?= htmlspecialchars($row['description']) ?></td>
                                        <td class="center"><?= htmlspecialchars($row['checklist'] ?: '-') ?></td>
                                        <td><?= format_remark_with_image($row['remarks'] ?? '') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            </div>

                        <?php elseif ($section_name === 'PUMP INFO'): ?>
                            <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr><th colspan="4" class="section-header"><?= strtoupper($system_name) ?> PUMP</th></tr>
                                    <tr><th width="5%" class="center">BIL</th><th width="55%">DESCRIPTION</th><th width="20%" class="center">STATUS</th><th width="20%">REMARKS</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rows as $row): ?>
                                    <tr>
                                        <td class="center bold"><?= htmlspecialchars($row['item_bil']) ?></td>
                                        <td class="<?= in_array($row['item_bil'], ['8 a)', '8 b)']) ? 'indent' : 'bold' ?>"><?= htmlspecialchars($row['description']) ?></td>
                                        
                                        <!-- Custom format rendering specifically for Hose Reel Cut In / Cut Out format -->
                                        <?php if ($table === 'fire_hose_reel_system' && in_array($row['item_bil'], ['8 a)', '8 b)'])): ?>
                                            <td class="center">
                                                <?php
                                                preg_match('/DUTY:\s*(.*?)\s*\|\s*STANDBY:\s*(.*)/i', $row['checklist'], $matches);
                                                $duty_val = $matches[1] ?? '-';
                                                $standby_val = $matches[2] ?? '-';
                                                ?>
                                                <div class="split-press-view">
                                                    <div><label>Duty</label><span><?= htmlspecialchars($duty_val) ?></span></div>
                                                    <div><label>Standby</label><span><?= htmlspecialchars($standby_val) ?></span></div>
                                                </div>
                                            </td>
                                        <?php else: ?>
                                            <td class="center"><?= htmlspecialchars($row['checklist'] ?: '-') ?></td>
                                        <?php endif; ?>
                                        
                                        <td><?= format_remark_with_image($row['remarks'] ?? '') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            </div>

                        <?php elseif ($section_name === 'PUMP PRESSURE'): ?>
                            <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr><th colspan="4" class="section-header"><?= strtoupper($system_name) ?> PUMP PRESSURE</th></tr>
                                    <tr><th width="5%" class="center">BIL</th><th width="55%">VALVE (TYPE)</th><th width="20%" class="center">VALUE</th><th width="20%">REMARKS</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rows as $row): ?>
                                    <tr>
                                        <td class="center bold"><?= htmlspecialchars($row['item_bil']) ?></td>
                                        <td class="bold"><?= htmlspecialchars($row['description']) ?></td>
                                        <td class="center"><?= htmlspecialchars($row['checklist'] ?: '-') ?></td>
                                        <td><?= format_remark_with_image($row['remarks'] ?? '') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            </div>

                        <?php elseif ($section_name === 'DEVICES'): ?>
                            <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr><th colspan="6" class="section-header"><?= strtoupper($system_name . ' - ' . $section_name) ?></th></tr>
                                    <tr><th width="5%" class="center">BIL</th><th width="40%">DESCRIPTION</th><th width="15%" class="center">ZONE/LOOP</th><th width="12%" class="center">CHECKLIST</th><th width="12%" class="center">CONDITION</th><th width="16%">REMARKS</th></tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $current_main_bil = '';
                                    foreach ($rows as $row): 
                                        $bil_parts = explode('-', $row['item_bil']);
                                        if (count($bil_parts) > 1) {
                                            $main_bil = $bil_parts[0];
                                            $sub_bil = $bil_parts[1];
                                            if ($main_bil !== $current_main_bil) {
                                                echo '<tr style="background-color: var(--surface); border-top: 2px solid var(--border);"><td class="center bold">'.htmlspecialchars($main_bil).'</td><td colspan="5" class="bold" style="color: var(--primary);">'.htmlspecialchars($row['description']).'</td></tr>';
                                                $current_main_bil = $main_bil;
                                            }
                                            echo '<tr>';
                                            echo '<td class="center">' . htmlspecialchars($sub_bil) . '</td>';
                                            echo '<td class="indent">' . htmlspecialchars($row['location_floor'] ?: '-') . '</td>';
                                            echo '<td class="center">' . htmlspecialchars($row['zone_loop'] ?: '-') . '</td>';
                                            echo '<td class="center">' . format_check($row['checklist']) . '</td>';
                                            echo '<td class="center">' . format_condition($row['item_condition']) . '</td>';
                                            // UPDATED REMARK FUNCTION
                                            echo '<td>' . format_remark_with_image($row['remarks'] ?? '') . '</td>';
                                            echo '</tr>';
                                        } else {
                                            echo '<tr>';
                                            echo '<td class="center bold">' . htmlspecialchars($row['item_bil']) . '</td>';
                                            echo '<td class="bold">' . htmlspecialchars($row['description']) . '</td>';
                                            echo '<td class="center">' . htmlspecialchars($row['zone_loop'] ?: '-') . '</td>';
                                            echo '<td class="center">' . format_check($row['checklist']) . '</td>';
                                            echo '<td class="center">' . format_condition($row['item_condition']) . '</td>';
                                            // UPDATED REMARK FUNCTION
                                            echo '<td>' . format_remark_with_image($row['remarks'] ?? '') . '</td>';
                                            echo '</tr>';
                                        }
                                    endforeach; 
                                    ?>
                                </tbody>
                            </table>
                            </div>

                        <?php elseif ($section_name === 'SIGNAL TEST'): ?>
                            <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr><th colspan="5" class="section-header"><?= strtoupper($system_name . ' - ' . $section_name) ?></th></tr>
                                    <tr><th width="5%" class="center">BIL</th><th width="50%">DESCRIPTION</th><th width="15%" class="center">CHECKLIST</th><th width="15%" class="center">CONDITION</th><th width="15%">REMARKS</th></tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $current_main_bil = '';
                                    foreach ($rows as $row): 
                                        $bil_parts = explode('-', $row['item_bil']);
                                        if (count($bil_parts) > 1) {
                                            $main_bil = $bil_parts[0];
                                            $sub_bil = $bil_parts[1];
                                            $desc_parts = explode(' - ', $row['description'], 2);
                                            $main_desc = $desc_parts[0];
                                            $sub_desc = $desc_parts[1] ?? $main_desc;
                                            
                                            if ($main_bil !== $current_main_bil) {
                                                echo '<tr style="background-color: var(--surface); border-top: 2px solid var(--border);"><td class="center bold">'.htmlspecialchars($main_bil).'</td><td colspan="4" class="bold" style="color: var(--primary);">'.htmlspecialchars($main_desc).'</td></tr>';
                                                $current_main_bil = $main_bil;
                                            }
                                            echo '<tr>';
                                            echo '<td class="center">' . htmlspecialchars($sub_bil) . '</td>';
                                            echo '<td class="indent">' . htmlspecialchars($sub_desc) . '</td>';
                                            echo '<td class="center">' . format_check($row['checklist']) . '</td>';
                                            echo '<td class="center">' . format_condition($row['item_condition']) . '</td>';
                                            // UPDATED REMARK FUNCTION
                                            echo '<td>' . format_remark_with_image($row['remarks'] ?? '') . '</td>';
                                            echo '</tr>';
                                        } else {
                                            echo '<tr>';
                                            echo '<td class="center bold">' . htmlspecialchars($row['item_bil']) . '</td>';
                                            echo '<td class="bold">' . htmlspecialchars($row['description']) . '</td>';
                                            echo '<td class="center">' . format_check($row['checklist']) . '</td>';
                                            echo '<td class="center">' . format_condition($row['item_condition']) . '</td>';
                                            // UPDATED REMARK FUNCTION
                                            echo '<td>' . format_remark_with_image($row['remarks'] ?? '') . '</td>';
                                            echo '</tr>';
                                        }
                                    endforeach; 
                                    ?>
                                </tbody>
                            </table>
                            </div>

                        <?php elseif (strpos($section_name, 'PUMP TEST') !== false): ?>
                            <div class="table-responsive">
                            <table>
                                <thead>
                                    <tr><th colspan="5" class="section-header"><?= strtoupper($system_name . ' - ' . $section_name) ?></th></tr>
                                    <tr><th width="5%" class="center">BIL</th><th width="50%">DESCRIPTION</th><th width="10%" class="center">CHECKLIST</th><th width="15%" class="center">CONDITION</th><th width="20%">REMARKS</th></tr>
                                    <tr style="background-color: var(--surface); border-top: 2px solid var(--border);">
                                        <td class="center bold"><?= strpos($section_name, 'MANUAL') !== false ? '1' : '2' ?></td>
                                        <td colspan="4" class="bold" style="color: var(--primary);"><?= strpos($section_name, 'MANUAL') !== false ? 'MANUAL TEST FOR:' : 'AUTO TEST FOR:' ?></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rows as $row): ?>
                                        <tr>
                                            <td class="center"><?= htmlspecialchars($row['item_bil']) ?></td>
                                            <td class="indent"><?= htmlspecialchars($row['description']) ?></td>
                                            <td class="center"><?= format_check($row['checklist']) ?></td>
                                            <td class="center"><?= format_condition($row['item_condition']) ?></td>
                                            <td><?= format_remark_with_image($row['remarks'] ?? '') ?></td>
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
                                    <tr><th width="5%" class="center">BIL</th><th width="50%">DESCRIPTION</th><th width="12%" class="center">CHECKLIST</th><th width="12%" class="center">CONDITION</th><th width="21%">REMARKS</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rows as $row): 
                                        $is_sub = strpos($row['item_bil'], '-') !== false;
                                        $display_bil = $is_sub ? explode('-', $row['item_bil'])[1] : $row['item_bil'];
                                        $is_header = empty($row['checklist']) && empty($row['item_condition']); 
                                    ?>
                                        <tr>
                                            <td class="center <?= !$is_sub ? 'bold' : '' ?>"><?= htmlspecialchars($display_bil) ?></td>
                                            <td class="<?= $is_sub ? 'indent' : ($is_header ? 'bold' : '') ?>"><?= htmlspecialchars($row['description']) ?></td>
                                            <td class="center"><?= format_check($row['checklist']) ?></td>
                                            <td class="center"><?= format_condition($row['item_condition']) ?></td>
                                            <td><?= format_remark_with_image($row['remarks'] ?? '') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            </div>
                        <?php endif; ?>
                        
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
            
        <?php endif; ?>

        <?php if (!empty($task['tech_signature']) || !empty($task['client_signature'])): ?>
        <div class="signatures-wrapper">
            <div class="sign-box">
                <div class="sign-img-container">
                    <?php if(!empty($task['tech_signature'])): ?>
                        <img src="<?php echo htmlspecialchars($task['tech_signature']); ?>" alt="Technician Signature">
                    <?php endif; ?>
                </div>
                <div class="sign-line"></div>
                <p class="sign-name"><?php echo !empty($task['tech_sign_name']) ? htmlspecialchars(strtoupper($task['tech_sign_name'])) : 'TECHNICIAN'; ?></p>
                <p class="sign-role" style="font-weight: bold; color: var(--text-main); margin-bottom: 4px;"><?php echo !empty($task['tech_company']) ? htmlspecialchars(strtoupper($task['tech_company'])) : 'LTA SERVICES'; ?></p>
                <p class="sign-role">Technician Signature</p>
            </div>

            <div class="sign-box">
                <div class="sign-img-container">
                    <?php if(!empty($task['client_signature'])): ?>
                        <img src="<?php echo htmlspecialchars($task['client_signature']); ?>" alt="Client Signature">
                    <?php endif; ?>
                </div>
                <div class="sign-line"></div>
                <p class="sign-name"><?php echo !empty($task['client_sign_name']) ? htmlspecialchars(strtoupper($task['client_sign_name'])) : 'CLIENT REPRESENTATIVE'; ?></p>
                <p class="sign-role">Client Signature</p>
            </div>
        </div>
        <?php else: ?>
        <div class="signatures-wrapper">
            <div class="sign-box">
                <div class="sign-img-container"></div>
                <div class="sign-line"></div>
                <p class="sign-name">__________________</p>
                <p class="sign-role" style="font-weight: bold; color: var(--text-main); margin-bottom: 4px;">LTA SERVICES</p>
                <p class="sign-role">Technician Name & Signature</p>
                <p class="sign-role" style="margin-top: 6px;">Date: ____________</p>
            </div>
            <div class="sign-box">
                <div class="sign-img-container"></div>
                <div class="sign-line"></div>
                <p class="sign-name">__________________</p>
                <p class="sign-role">Client/Rep Name & Signature</p>
                <p class="sign-role" style="margin-top: 6px;">Date: ____________</p>
            </div>
        </div>
        <?php endif; ?>

    </div>
</body>
</html>