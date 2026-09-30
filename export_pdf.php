<?php
session_start();
require 'db.php';
require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// 1. Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Unauthorized access.");
}

$task_id = isset($_GET['task_id']) ? intval($_GET['task_id']) : 0;
if ($task_id === 0) die("Invalid Task ID.");

// 2. Fetch General Task Information
$stmt = $conn->prepare("SELECT * FROM tasks WHERE task_id = ?");
$stmt->bind_param("i", $task_id);
$stmt->execute();
$task_result = $stmt->get_result();
if ($task_result->num_rows === 0) die("Task not found.");
$task = $task_result->fetch_assoc();

// Function to convert image to base64
function getImageBase64($path) {
    if (file_exists($path)) {
        try {
            $type = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            // Dompdf doesn't recognize "image/jpg" - the correct MIME type is "image/jpeg"
            if ($type === 'jpg') {
                $type = 'jpeg';
            }
            $data = file_get_contents($path);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        } catch (Exception $e) {
            return '';
        }
    }
    return '';
}

// Get logo as base64
$logo_base64 = getImageBase64('LTA_logo(white).png');

// Get watermark as base64
$watermark_base64 = getImageBase64('lta_watermarkPDF.jpeg');

// 3. Define System Tables
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
            while (isset($submissions[$sub_idx][$sec][$bil])) { $sub_idx++; }
            $submissions[$sub_idx][$sec][$bil] = $row;
        }
        
        // Filter out N/A items from each submission
        foreach ($submissions as $sub_idx => $sections) {
            foreach ($sections as $section_name => $items_keyed) {
                // Skip filtering for PANEL PROFILE sections as they don't have checklist field
                if ($section_name === 'PANEL PROFILE') {
                    continue;
                }
                
                // Filter items - keep only those with checklist = 'Done' or checklist is empty/null
                $filtered_items = [];
                foreach ($items_keyed as $bil => $item) {
                    // Check if this item has a checklist field and if it's N/A
                    if (isset($item['checklist'])) {
                        // Keep items that are 'Done', empty, or null - filter out 'N/A'
                        if ($item['checklist'] !== 'N/A') {
                            $filtered_items[$bil] = $item;
                        }
                    } else {
                        // If no checklist field, keep the item
                        $filtered_items[$bil] = $item;
                    }
                }
                
                // Replace with filtered items
                $submissions[$sub_idx][$section_name] = $filtered_items;
            }
        }
        
        // Remove empty sections and submissions
        foreach ($submissions as $sub_idx => $sections) {
            // Remove empty sections
            foreach ($sections as $section_name => $items) {
                if (empty($items)) {
                    unset($submissions[$sub_idx][$section_name]);
                }
            }
            // If submission has no sections, remove it
            if (empty($submissions[$sub_idx])) {
                unset($submissions[$sub_idx]);
            }
        }
        
        // Re-index submissions
        $submissions = array_values($submissions);
        
        // Only add to report data if there are submissions with data
        if (!empty($submissions)) {
            $report_data[$table] = [
                'system_name' => $system_name,
                'submissions' => $submissions
            ];
        }
    }
}

// 4. Formatting Helpers for Dompdf
function format_check_pdf($chk) {
    if (empty($chk) || $chk === '-') return '-';
    if ($chk === 'Done') return '<span style="color: #059669; font-weight: bold;">[ DONE ]</span>';
    if ($chk === 'N/A') return '<span style="color: #dc2626; font-weight: bold;">[ N/A ]</span>';
    return htmlspecialchars($chk);
}

function format_condition_pdf($cond) {
    if (empty($cond) || $cond === '-') return '-';
    if ($cond === 'Faulty') return '<span style="color: #dc2626; font-weight: bold;">FAULTY</span>';
    return htmlspecialchars($cond);
}

function format_remark_pdf($remark_string) {
    if (empty($remark_string) || $remark_string === '-') return '-';
    $text = $remark_string;
    $img_html = "";
    
    // Convert relative image path to safe base64 for Dompdf rendering
    if (preg_match('/\[IMG: (.*?)\]/', $remark_string, $matches)) {
        $rel_path = $matches[1];
        $abs_path = __DIR__ . '/' . $rel_path;
        $text = trim(str_replace($matches[0], '', $remark_string));
        
        if (file_exists($abs_path)) {
            $type = pathinfo($abs_path, PATHINFO_EXTENSION);
            $data = file_get_contents($abs_path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            $img_html = "<br><img src='$base64' style='max-height: 80px; margin-top: 8px; border: 1px solid #e5e7eb; border-radius: 4px;'>";
        }
    }
    return htmlspecialchars($text) . $img_html;
}

// 5. Build HTML
ob_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Service Report Task #<?= $task_id ?></title>
    <style>
        /* Force zero margins on the page to allow edge-to-edge bleeding */
        @page { margin: 165px 40px 40px 40px; }
        
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #111827; margin: 0; padding: 0; }
        
        /* ============ WATERMARKS ============ */
        #watermark-center {
            position: fixed;
            top: 25%;
            left: 15%;
            width: 70%;
            z-index: -1000;
            text-align: center;
            opacity: 0.15;
        }
        #watermark-center img {
            width: 100%;
            height: auto;
        }

        #watermark-footer {
            position: fixed;
            bottom: 30px;
            right: 40px;
            width: 100px;
            z-index: -1000;
            opacity: 0.2;
        }
        #watermark-footer img {
            width: 100%;
            height: auto;
        }

        /* ============ LTA RED BANNER LETTERHEAD ============ */
        .letterhead-container {
            background-color: #B00E16;
            color: #ffffff;
            padding: 0;
            margin: 0;
            position: fixed;
            z-index: 1000;
            top: 0;
            left:0;
            right: 0;
        }
        .letterhead-table { width: 100%; border-collapse: collapse; border: none; }
        .letterhead-table td { border: none; padding: 0; vertical-align: middle; }
        .lh-logo-cell { width: 22%; text-align: center; background-color: #B00E16; padding: 18px 20px; }
        .lh-text-cell { width: 78%; padding: 20px 30px; vertical-align: middle; }
        .lh-logo { max-width: 150px; max-height: 95px; }
        .lh-title { font-size: 26px; font-weight: bold; margin: 0 0 8px 0; letter-spacing: 0.3px; line-height: 1.2; }
        .lh-reg { font-size: 13px; font-weight: normal; }
        .lh-details { font-size: 11.5px; line-height: 1.75; margin-top: 4px; }
        
        .lh-trim-line { height: 5px; background-color: #8BA4B5; width: 100%; }
        
        /* ============ MAIN CONTENT PADDING ============ */
        /* Since body margins are 0, this pushes the text inward to look normal */
        .content-wrapper { padding: 25px 40px 40px 40px; }

        .report-header { text-align: center; margin-bottom: 20px; }
        .report-header h1 { margin: 0 0 8px 0; font-size: 20px; font-weight: 900; color: #111827; letter-spacing: 0.5px; text-transform: uppercase; }
        .report-header .sys-title { font-size: 11px; font-weight: bold; margin-bottom: 4px; color: #111827; }
        .report-header .sys-list { font-size: 11px; color: #6b7280; }
        
        .task-meta { width: 100%; border-collapse: collapse; margin-top: 15px; margin-bottom: 30px; background: #f8fafc; border-top: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb; }
        .task-meta td { padding: 12px 15px; border: none; vertical-align: top; width: 50%; font-size: 11px; line-height: 1.6; }
        .task-meta strong { color: #6b7280; text-transform: uppercase; font-size: 10px; display: inline-block; width: 100px; letter-spacing: 0.5px; }

        .system-separator { background-color: #f1f5f9; border-left: 4px solid #C41E3A; padding: 10px 14px; margin-top: 35px; margin-bottom: 15px; font-size: 13px; font-weight: bold; color: #C41E3A; text-transform: uppercase; letter-spacing: 0.5px;}

        /* ============ MODERN SMOOTH TABLES ============ */
        table.data-table { width: 100%; border-collapse: collapse; margin-bottom: 25px; table-layout: fixed; }
        table.data-table th, table.data-table td { 
            border: none; 
            border-bottom: 1px solid #e5e7eb; 
            padding: 8px 10px; 
            vertical-align: middle; 
            word-wrap: break-word; 
        }
        table.data-table th { 
            background-color: #f9fafb; 
            color: #6b7280; 
            font-weight: bold; 
            text-transform: uppercase; 
            font-size: 9px; 
            text-align: center; 
            letter-spacing: 0.5px; 
        }
        table.data-table th.section-header { 
            background-color: #FC5151 !important; 
            color: #ffffff !important; 
            font-size: 12px; 
            padding: 10px 12px; 
            text-align: left; 
            border-bottom: none;
            letter-spacing: 0.5px;
        }
        table.data-table th.sub-header { 
            background-color: #f1f5f9 !important; 
            color: #C41E3A !important; 
            font-size: 10px; 
            text-align: left; 
            padding: 8px 12px; 
        }
        
        tbody tr:nth-child(even) td { background-color: #fafafa; }
        table.data-table tr:last-child td { border-bottom: 2px solid #e5e7eb; } 
        
        .center { text-align: center; }
        .bold { font-weight: bold; color: #111827; }
        .indent { padding-left: 20px; color: #4b5563; }

        .signatures { width: 100%; margin-top: 50px; border-collapse: collapse; page-break-inside: avoid; }
        .signatures td { border: none; text-align: center; vertical-align: bottom; width: 50%; padding: 10px; }
        .sig-box { border: 1px solid #e5e7eb; background: #ffffff; padding: 25px; border-radius: 8px; margin: 0 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.02); }
        .sig-img { max-height: 80px; max-width: 100%; margin-bottom: 12px; }
        .sig-line { border-top: 1px solid #d1d5db; width: 80%; margin: 0 auto 12px auto; }
        .sig-name { font-weight: bold; font-size: 12px; text-transform: uppercase; margin: 0 0 6px 0; color: #111827; letter-spacing: 0.5px; }
        .sig-role { font-size: 10px; color: #6b7280; text-transform: uppercase; margin: 0; letter-spacing: 0.5px; }
    </style>
</head>
<body>

<?php if (!empty($watermark_base64)): ?>
<div id="watermark-center">
    <img src="<?php echo $watermark_base64; ?>" alt="watermark center">
</div>
<div id="watermark-footer">
    <img src="<?php echo $watermark_base64; ?>" alt="watermark footer">
</div>
<?php endif; ?>

<div class="letterhead-container">
    <table class="letterhead-table">
        <tr>
            <td class="lh-logo-cell">
                <?php if (!empty($logo_base64)): ?>
                    <img src="<?php echo $logo_base64; ?>" alt="LTA Logo" class="lh-logo">
                <?php else: ?>
                    <div style="color: #ffffff; font-size: 32px; font-weight: bold; padding: 10px 0;">
                        LTA
                    </div>
                <?php endif; ?>
            </td>
            <td class="lh-text-cell">
                <div class="lh-title">LTA SERVICES SDN. BHD. <span class="lh-reg">(705567-V)</span></div>
                <div class="lh-details">
                    2<sup>nd</sup> Floor, SL113, Plot 24, Gala City, Jalan Tun Jugah, 93350 Kuching, Sarawak.<br>
                    Tel: 082-265761<br>
                    Email: lta.firemaintenance@gmail.com<br>
                    Website: www.ltaservicessdnbhd.com
                </div>
            </td>
        </tr>
    </table>
</div>
    
    <div class="lh-trim-line"></div>

    <div class="content-wrapper">

        <div class="report-header">
            <h1>MAINTENANCE SERVICE REPORT</h1>
            <div class="sys-title">SYSTEMS INCLUDED:</div>
            <div class="sys-list">
                <?php 
                    $included = array_column($report_data, 'system_name');
                    echo !empty($included) ? implode(' &bull; ', array_map('htmlspecialchars', $included)) : 'None';
                ?>
            </div>
        </div>

        <table class="task-meta">
            <tr>
                <td style="border-right: 1px solid #e5e7eb;">
                    <div><strong>Task ID:</strong> #<?= str_pad($task['task_id'], 5, '0', STR_PAD_LEFT) ?></div>
                    <div><strong>Company:</strong> <?= htmlspecialchars($task['company_name'] ?? 'N/A') ?></div>
                    <div><strong>Building:</strong> <?= htmlspecialchars($task['building_name'] ?? 'N/A') ?></div>
                </td>
                <td>
                    <div><strong>Date Created:</strong> <?= date('d M Y, h:i A', strtotime($task['created_at'])) ?></div>
                    <div><strong>Status:</strong> <span style="color: #C41E3A; font-weight: bold;"><?= htmlspecialchars($task['status']) ?></span></div>
                    <div><strong>Inspected By:</strong> 
                        <?php 
                            echo !empty($task['tech_sign_name']) ? htmlspecialchars(strtoupper($task['tech_sign_name'])) : 'Technician (ID: ' . htmlspecialchars($task['tech_id'] ?? 'Unknown') . ')'; 
                        ?>
                    </div>
                    <div>
                        <strong>Date Inspected:</strong>
                        <?= !empty($task['inspection_date'])
                            ? date('d M Y, h:i A', strtotime($task['inspection_date']))
                            : 'N/A' ?>
                    </div>
                </td>
            </tr>
        </table>

        <?php if (empty($report_data)): ?>
            <div style="text-align:center; padding: 40px; color: #6b7280; font-style: italic; border: 1px solid #e5e7eb; background: #f9fafb; border-radius: 6px;">No completed checklist items found for this task. All items marked as N/A have been filtered out.</div>
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
                                <table class="data-table">
                                    <thead>
                                        <tr><th colspan="4" class="section-header"><?= strtoupper($system_name) ?></th></tr>
                                        <tr><th width="10%">BIL</th><th width="40%" style="text-align:left;">LOCATION</th><th width="30%" style="text-align:left;">STACK</th><th width="20%" style="text-align:left;">NO. OF STACK</th></tr>
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
                            <?php elseif (strpos($table, 'suppression') !== false || $table === 'wet_chemical_system'): ?>
                                <table class="data-table">
                                    <thead>
                                        <tr><th colspan="4" class="section-header"><?= strtoupper($system_name) ?></th></tr>
                                        <tr><th colspan="4" class="sub-header"><?= strtoupper($system_name) ?></th></tr>
                                        <tr><th width="10%">BIL</th><th width="40%" style="text-align:left;">LOCATION/ROOM</th><th width="25%" style="text-align:left;">CYLINDER CAPACITY (KG)</th><th width="25%" style="text-align:left;">QUANTITY OF CYLINDER</th></tr>
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
                            <?php else: ?>
                                <table class="data-table">
                                    <thead>
                                        <tr><th colspan="7" class="section-header"><?= strtoupper($system_name) ?> CONTROL PANEL PROFILE</th></tr>
                                        <tr><th width="5%">BIL</th><th width="20%" style="text-align:left;">PANEL PROFILE</th><th width="15%" style="text-align:left;">TYPE OF PANEL</th><th width="15%" style="text-align:left;">BRAND OF PANEL</th><th width="12%" style="text-align:left;">MODEL</th><th width="10%">QTY/ZONE</th><th width="23%" style="text-align:left;">LOCATION</th></tr>
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
                            <?php endif; ?>

                        <?php elseif ($section_name === 'SYSTEM INFO'): ?>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th colspan="4" class="section-header">FIRE SUPPRESSION SYSTEM PANEL & CYLINDER</th>
                                    </tr>
                                    <tr>
                                        <th colspan="4" class="sub-header"><?= strtoupper($system_name) ?></th>
                                    </tr>
                                    <tr>
                                        <th width="8%">BIL</th>
                                        <th width="42%" style="text-align:left;">DESCRIPTION</th>
                                        <th width="15%" style="text-align:center;">STATUS</th>
                                        <th width="35%" style="text-align:left;">REMARKS & PHOTOS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rows as $row): ?>
                                    <tr>
                                        <td class="center bold"><?= htmlspecialchars($row['item_bil']) ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($row['description'] ?: '-') ?></strong>
                                            <?php if (!empty($row['location_floor']) && $row['location_floor'] !== '-'): ?>
                                                <br><span style="font-size: 8px; color: #6b7280;">Location: <?= htmlspecialchars($row['location_floor']) ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($row['panel_type']) && $row['panel_type'] !== '-'): ?>
                                                <br><span style="font-size: 8px; color: #6b7280;">Type: <?= htmlspecialchars($row['panel_type']) ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($row['panel_brand']) && $row['panel_brand'] !== '-'): ?>
                                                <br><span style="font-size: 8px; color: #6b7280;">Brand: <?= htmlspecialchars($row['panel_brand']) ?></span>
                                            <?php endif; ?>
                                            <?php if (!empty($row['panel_model']) && $row['panel_model'] !== '-'): ?>
                                                <br><span style="font-size: 8px; color: #6b7280;">Model: <?= htmlspecialchars($row['panel_model']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="center">
                                            <?php if ($row['checklist'] === 'Done'): ?>
                                                <span style="color: #059669; font-weight: bold;">[ DONE ]</span>
                                            <?php elseif ($row['checklist'] === 'N/A'): ?>
                                                <span style="color: #dc2626; font-weight: bold;">[ N/A ]</span>
                                            <?php else: ?>
                                                <?= htmlspecialchars($row['checklist'] ?: '-') ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= format_remark_pdf($row['remarks'] ?? '') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                        <?php elseif ($section_name === 'PUMP INFO'): ?>
                            <table class="data-table">
                                <thead>
                                    <tr><th colspan="4" class="section-header"><?= strtoupper($system_name) ?> PUMP</th></tr>
                                    <tr><th width="5%">BIL</th><th width="45%" style="text-align:left;">DESCRIPTION</th><th width="15%">STATUS</th><th width="35%" style="text-align:left;">REMARKS & PHOTOS</th></tr>
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
                                                
                                                // Create a clean layout for the PDF
                                                echo "<div style='text-align: left; font-size: 9px;'>
                                                        <div><span style='color:#6b7280; font-weight:bold;'>DUTY:</span> " . htmlspecialchars($duty_val) . "</div>
                                                        <div style='margin-top:2px;'><span style='color:#6b7280; font-weight:bold;'>STBY:</span> " . htmlspecialchars($standby_val) . "</div>
                                                      </div>";
                                                ?>
                                            </td>
                                        <?php else: ?>
                                            <td class="center"><?= htmlspecialchars($row['checklist'] ?: '-') ?></td>
                                        <?php endif; ?>
                                        
                                        <td><?= format_remark_pdf($row['remarks'] ?? '') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                        <?php elseif ($section_name === 'PUMP PRESSURE'): ?>
                            <table class="data-table">
                                <thead>
                                    <tr><th colspan="4" class="section-header"><?= strtoupper($system_name) ?> PUMP PRESSURE</th></tr>
                                    <tr><th width="5%">BIL</th><th width="45%" style="text-align:left;">VALVE (TYPE)</th><th width="15%">VALUE</th><th width="35%" style="text-align:left;">REMARKS & PHOTOS</th></tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rows as $row): ?>
                                    <tr>
                                        <td class="center bold"><?= htmlspecialchars($row['item_bil']) ?></td>
                                        <td class="bold"><?= htmlspecialchars($row['description']) ?></td>
                                        <td class="center"><?= htmlspecialchars($row['checklist'] ?: '-') ?></td>
                                        <td><?= format_remark_pdf($row['remarks'] ?? '') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                        <?php elseif ($section_name === 'DEVICES'): ?>
                            <table class="data-table">
                                <thead>
                                    <tr><th colspan="6" class="section-header"><?= strtoupper($system_name . ' - ' . $section_name) ?></th></tr>
                                    <tr><th width="5%">BIL</th><th width="35%" style="text-align:left;">DESCRIPTION</th><th width="15%">ZONE/LOOP</th><th width="10%">CHECKLIST</th><th width="10%">CONDITION</th><th width="25%" style="text-align:left;">REMARKS & PHOTOS</th></tr>
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
                                                echo '<tr><td class="center bold">'.htmlspecialchars($main_bil).'</td><td colspan="5" class="bold" style="background:#f1f5f9; color:#111827;">'.htmlspecialchars($row['description']).'</td></tr>';
                                                $current_main_bil = $main_bil;
                                            }
                                            echo '<tr>';
                                            echo '<td class="center">' . htmlspecialchars($sub_bil) . '</td>';
                                            echo '<td class="indent">' . htmlspecialchars($row['location_floor'] ?: '-') . '</td>';
                                            echo '<td class="center">' . htmlspecialchars($row['zone_loop'] ?: '-') . '</td>';
                                            echo '<td class="center">' . format_check_pdf($row['checklist']) . '</td>';
                                            echo '<td class="center">' . format_condition_pdf($row['item_condition']) . '</td>';
                                            echo '<td>' . format_remark_pdf($row['remarks'] ?? '') . '</td>';
                                            echo '</tr>';
                                        } else {
                                            echo '<tr>';
                                            echo '<td class="center bold">' . htmlspecialchars($row['item_bil']) . '</td>';
                                            echo '<td class="bold">' . htmlspecialchars($row['description']) . '</td>';
                                            echo '<td class="center">' . htmlspecialchars($row['zone_loop'] ?: '-') . '</td>';
                                            echo '<td class="center">' . format_check_pdf($row['checklist']) . '</td>';
                                            echo '<td class="center">' . format_condition_pdf($row['item_condition']) . '</td>';
                                            echo '<td>' . format_remark_pdf($row['remarks'] ?? '') . '</td>';
                                            echo '</tr>';
                                        }
                                    endforeach; 
                                    ?>
                                </tbody>
                            </table>

                        <?php elseif ($section_name === 'SIGNAL TEST'): ?>
                            <table class="data-table">
                                <thead>
                                    <tr><th colspan="5" class="section-header"><?= strtoupper($system_name . ' - ' . $section_name) ?></th></tr>
                                    <tr><th width="5%">BIL</th><th width="45%" style="text-align:left;">DESCRIPTION</th><th width="10%">CHECKLIST</th><th width="15%">CONDITION</th><th width="25%" style="text-align:left;">REMARKS & PHOTOS</th></tr>
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
                                                echo '<tr><td class="center bold">'.htmlspecialchars($main_bil).'</td><td colspan="4" class="bold" style="background:#f1f5f9; color:#111827;">'.htmlspecialchars($main_desc).'</td></tr>';
                                                $current_main_bil = $main_bil;
                                            }
                                            echo '<tr>';
                                            echo '<td class="center">' . htmlspecialchars($sub_bil) . '</td>';
                                            echo '<td class="indent">' . htmlspecialchars($sub_desc) . '</td>';
                                            echo '<td class="center">' . format_check_pdf($row['checklist']) . '</td>';
                                            echo '<td class="center">' . format_condition_pdf($row['item_condition']) . '</td>';
                                            echo '<td>' . format_remark_pdf($row['remarks'] ?? '') . '</td>';
                                            echo '</tr>';
                                        } else {
                                            echo '<tr>';
                                            echo '<td class="center bold">' . htmlspecialchars($row['item_bil']) . '</td>';
                                            echo '<td class="bold">' . htmlspecialchars($row['description']) . '</td>';
                                            echo '<td class="center">' . format_check_pdf($row['checklist']) . '</td>';
                                            echo '<td class="center">' . format_condition_pdf($row['item_condition']) . '</td>';
                                            echo '<td>' . format_remark_pdf($row['remarks'] ?? '') . '</td>';
                                            echo '</tr>';
                                        }
                                    endforeach; 
                                    ?>
                                </tbody>
                            </table>

                        <?php elseif (strpos($section_name, 'PUMP TEST') !== false): ?>
                            <table class="data-table">
                                <thead>
                                    <tr><th colspan="5" class="section-header"><?= strtoupper($system_name . ' - ' . $section_name) ?></th></tr>
                                    <tr><th width="5%">BIL</th><th width="45%" style="text-align:left;">DESCRIPTION</th><th width="10%">CHECKLIST</th><th width="15%">CONDITION</th><th width="25%" style="text-align:left;">REMARKS & PHOTOS</th></tr>
                                    <tr style="background-color: #f1f5f9; border-top: 1px solid #d1d5db;">
                                        <td class="center bold"><?= strpos($section_name, 'MANUAL') !== false ? '1' : '2' ?></td>
                                        <td colspan="4" class="bold" style="color:#111827;"><?= strpos($section_name, 'MANUAL') !== false ? 'MANUAL TEST FOR:' : 'AUTO TEST FOR:' ?></td>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rows as $row): ?>
                                        <tr>
                                            <td class="center"><?= htmlspecialchars($row['item_bil']) ?></td>
                                            <td class="indent"><?= htmlspecialchars($row['description']) ?></td>
                                            <td class="center"><?= format_check_pdf($row['checklist']) ?></td>
                                            <td class="center"><?= format_condition_pdf($row['item_condition']) ?></td>
                                            <td><?= format_remark_pdf($row['remarks'] ?? '') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                        <?php else: ?>
                            <table class="data-table">
                                <thead>
                                    <tr><th colspan="5" class="section-header"><?= strtoupper($system_name . ' - ' . $section_name) ?></th></tr>
                                    <tr><th width="5%">BIL</th><th width="45%" style="text-align:left;">DESCRIPTION</th><th width="10%">CHECKLIST</th><th width="15%">CONDITION</th><th width="25%" style="text-align:left;">REMARKS & PHOTOS</th></tr>
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
                                            <td class="center"><?= format_check_pdf($row['checklist']) ?></td>
                                            <td class="center"><?= format_condition_pdf($row['item_condition']) ?></td>
                                            <td><?= format_remark_pdf($row['remarks'] ?? '') ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                        
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
            
        <?php endif; ?>

        <?php if (!empty($task['tech_signature']) || !empty($task['client_signature'])): ?>
        <table class="signatures">
            <tr>
                <td>
                    <div class="sig-box">
                        <?php if(!empty($task['tech_signature'])): ?>
                            <img src="<?php echo htmlspecialchars($task['tech_signature']); ?>" class="sig-img">
                        <?php endif; ?>
                        <div class="sig-line"></div>
                        <p class="sig-name"><?php echo !empty($task['tech_sign_name']) ? htmlspecialchars(strtoupper($task['tech_sign_name'])) : 'TECHNICIAN'; ?></p>
                        <p class="sig-role" style="font-weight: bold; color: #111827; margin-bottom: 4px;">LTA SERVICES SDN. BHD.</p>
                        <p class="sig-role">Technician Signature</p>
                    </div>
                </td>
                <td>
                    <div class="sig-box">
                        <?php if(!empty($task['client_signature'])): ?>
                            <img src="<?php echo htmlspecialchars($task['client_signature']); ?>" class="sig-img">
                        <?php endif; ?>
                        <div class="sig-line"></div>
                        <p class="sig-name"><?php echo !empty($task['client_sign_name']) ? htmlspecialchars(strtoupper($task['client_sign_name'])) : 'CLIENT REPRESENTATIVE'; ?></p>
                        <p class="sig-role">Client Signature</p>
                    </div>
                </td>
            </tr>
        </table>
        <?php endif; ?>

    </div> 
</body>
</html>
<?php
$html = ob_get_clean();

// 6. Generate and output PDF
$options = new Options();
$options->set('isRemoteEnabled', true); 
$options->set('isHtml5ParserEnabled', true);
$options->set('defaultFont', 'Helvetica');

$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$filename = "Service_Report_Task_" . $task_id . ".pdf";
$dompdf->stream($filename, ["Attachment" => false]);
exit();
?>