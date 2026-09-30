<?php
session_start();

// 1. Security Check: Kick them out if they aren't the technician
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'technician') {
    header("Location: index.php");
    exit();
}

require 'db.php';

// 2. Catch the Task ID from the URL
if (!isset($_GET['task_id']) || empty($_GET['task_id'])) {
    header("Location: tech_dashboard.php");
    exit();
}

$task_id = intval($_GET['task_id']);

// Define allowed system tables for security
$system_forms = [
    'fire_alarm_system_add' => 'Addressable Fire Alarm',
    'fire_alarm_system_con' => 'Conventional Fire Alarm',
    'fireman_intercom_system' => 'Fireman Intercom System',
    'wet_chemical_system' => 'Wet Chemical System',
    'fire_hose_reel_system' => 'Fire Hose Reel System',
    'fire_sprinkler_system' => 'Fire Sprinkler System',
    'fire_suppression_system_1' => 'CO2 Suppression System',
    'fire_suppression_system_2' => 'FM200 Suppression System',
    'fire_suppression_system_3' => 'FE-13 Suppression System',
    'fire_suppression_system_4' => 'Inert Gas Suppression System',
    'wet_riser_system' => 'Wet Riser System',
    'dry_riser_system' => 'Dry Riser System',
    'pressurised_hydrant_system' => 'Pressurised Hydrant System'
];

// 3. Handle Checklist Deletion Request
if (isset($_GET['del_table']) && isset($_GET['del_idx'])) {
    $del_table = $_GET['del_table'];
    $del_idx = intval($_GET['del_idx']);
    
    // Ensure table is strictly from the allowed whitelist
    if (array_key_exists($del_table, $system_forms)) {
        $rows = $conn->query("SELECT * FROM `$del_table` WHERE task_id = $task_id ORDER BY id ASC");
        if ($rows && $rows->num_rows > 0) {
            $submissions = [];
            while ($r = $rows->fetch_assoc()) {
                $sec = $r['section_name'];
                $bil = $r['item_bil'];
                $sub_idx = 0;
                while (isset($submissions[$sub_idx][$sec][$bil])) {
                    $sub_idx++;
                }
                $submissions[$sub_idx][$sec][$bil] = $r;
            }
            
            // Gather all row IDs belonging to that specific submission index and delete them
            if (isset($submissions[$del_idx])) {
                $ids_to_delete = [];
                foreach ($submissions[$del_idx] as $sec_name => $items) {
                    foreach ($items as $bil_key => $row_data) {
                        $ids_to_delete[] = intval($row_data['id']);
                    }
                }
                if (!empty($ids_to_delete)) {
                    $id_list = implode(',', $ids_to_delete);
                    $conn->query("DELETE FROM `$del_table` WHERE id IN ($id_list) AND task_id = $task_id");
                }
            }
        }
    }
    header("Location: tech_submitted_list.php?task_id=$task_id");
    exit();
}

// 4. Fetch the Company and Building name
$sql = "SELECT company_name, building_name FROM tasks WHERE task_id = $task_id AND status = 'Pending'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("<div style='text-align:center; padding: 40px; font-family: sans-serif; background: #f4f5f7; height: 100vh;'>Task not found or already completed. <br><br><a href='tech_dashboard.php' style='color:#C41E3A; font-weight:bold; text-decoration:none;'>Go Back to Dashboard</a></div>");
}

$task = $result->fetch_assoc();

// 5. Scan which Checklists have been submitted
$submitted_forms = [];
foreach ($system_forms as $table => $name) {
    $rows = @$conn->query("SELECT * FROM `$table` WHERE task_id = $task_id ORDER BY id ASC");
    if ($rows && $rows->num_rows > 0) {
        $submissions = [];
        while ($r = $rows->fetch_assoc()) {
            $sec = $r['section_name'];
            $bil = $r['item_bil'];
            $sub_idx = 0;
            while (isset($submissions[$sub_idx][$sec][$bil])) {
                $sub_idx++;
            }
            $submissions[$sub_idx][$sec][$bil] = $r;
        }
        
        foreach ($submissions as $idx => $sub) {
            $loc = "";
            if (isset($sub['PANEL PROFILE'])) {
                foreach ($sub['PANEL PROFILE'] as $item) {
                    if (!empty($item['location_floor'])) {
                        $loc = $item['location_floor'];
                        break; 
                    }
                }
            }
            if (!empty($loc)) {
                $label = $name . " - " . strtoupper($loc);
            } else {
                $label = $name . (count($submissions) > 1 ? " - SUBMISSION " . ($idx + 1) : "");
            }
            $submitted_forms[] = [ 'label' => $label, 'table' => $table, 'idx' => $idx ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Submitted Reports - Technician Portal</title>
    <style>
        :root {
            --primary: #C41E3A;
            --primary-hover: #a8182f;
            --bg-color: #f4f5f7;
            --surface: #ffffff;
            --text-main: #111827;
            --text-muted: #6b7280;
            --border: #e5e7eb;
            --success: #10b981;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: var(--bg-color); 
            color: var(--text-main);
            min-height: 100vh;
            padding-bottom: 60px;
        }

        /* ============ TOP BAR ============ */
        .topbar {
            background: var(--primary);
            color: white;
            padding: clamp(14px, 3vw, 18px) clamp(16px, 5vw, 28px);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 4px 14px rgba(196,30,58,0.18);
        }
        
        .topbar-left { display: flex; align-items: center; gap: 12px; }
        
        .topbar-icon {
            width: 40px; height: 40px;
            background: rgba(255,255,255,0.15);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .topbar-icon svg { width: 20px; height: 20px; stroke: #fff; }
        
        .topbar-title { font-size: clamp(16px, 4vw, 18px); font-weight: 700; letter-spacing: 0.3px; }
        .topbar-title span {
            display: block; font-size: 11px; font-weight: 400; opacity: 0.85;
            text-transform: uppercase; letter-spacing: 1px; margin-top: 2px;
        }
        
        /* ============ FIXED BACK BUTTON ============ */
        .back-btn {
            background: rgba(255,255,255,0.15);
            color: white;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(255,255,255,0.25);
            white-space: nowrap;
            flex-shrink: 0;
        }
        .back-btn:hover {
            background: rgba(255,255,255,0.25);
            transform: translateX(-2px);
        }
        .back-btn svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            stroke: currentColor;
            stroke-width: 2.5;
        }

        /* ============ CONTAINER ============ */
        .container { max-width: 960px; margin: 0 auto; padding: clamp(16px, 4vw, 28px); }

        /* ============ SITE INFO CARD ============ */
        .status-strip {
            background: var(--surface);
            border-radius: 14px;
            padding: 20px 22px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            border: 1px solid var(--border);
            gap: 16px;
            flex-wrap: wrap;
        }
        .status-strip .greeting {
            font-size: clamp(18px, 5vw, 22px);
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: -0.3px;
        }
        .status-strip .building-row {
            display: flex; align-items: center; gap: 6px;
            color: var(--text-muted); font-size: 14px;
            margin-top: 4px; font-weight: 600;
        }
        .status-strip .building-row svg { width: 16px; height: 16px; stroke: #9aa0a8; flex-shrink: 0; }
        
        .badge-id {
            background: #f1f5f9;
            color: #334155;
            font-size: 12px;
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 8px;
            white-space: nowrap;
            border: 1px solid #e2e8f0;
        }

        /* ============ PAGE TITLE ============ */
        .page-header {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }
        .page-header h2 {
            font-size: 20px;
            font-weight: 800;
            color: var(--text-main);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .page-header h2 svg { width: 22px; height: 22px; stroke: var(--success); }
        .submission-count {
            font-size: 13px; font-weight: 700; background: #ecfdf5; color: #047857;
            padding: 6px 12px; border-radius: 12px; border: 1px solid #a7f3d0;
        }

        /* ============ SUBMISSION GRID ============ */
        .submission-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 14px;
        }
        
        .submission-card {
            background: var(--surface);
            border: 1.5px solid var(--border);
            border-radius: 12px;
            padding: 18px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 16px;
            transition: all 0.2s;
            box-shadow: 0 2px 6px rgba(0,0,0,0.02);
        }
        .submission-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 6px 16px rgba(0,0,0,0.06);
            transform: translateY(-2px);
        }
        .submission-info {
            display: flex; align-items: flex-start; gap: 12px;
        }
        .submission-info svg { width: 20px; height: 20px; stroke: var(--text-muted); flex-shrink: 0; margin-top: 2px; }
        .submission-label { font-weight: 700; color: var(--text-main); font-size: 15px; line-height: 1.4; }
        
        .submission-actions {
            display: flex; gap: 10px;
        }
        .btn-edit-report {
            flex: 1;
            background: var(--primary);
            color: white;
            text-decoration: none;
            padding: 11px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: 0.2s;
            box-shadow: 0 2px 4px rgba(196, 30, 58, 0.15);
        }
        .btn-edit-report:hover { background: var(--primary-hover); transform: translateY(-1px); }
        .btn-edit-report svg { width: 14px; height: 14px; stroke: currentColor; }
        
        .btn-del-report {
            background: #fef2f2;
            color: #dc2626;
            text-decoration: none;
            padding: 11px 16px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 700;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border: 1px solid #fecaca;
            transition: 0.2s;
        }
        .btn-del-report:hover { background: #ef4444; color: white; border-color: #ef4444; }
        .btn-del-report svg { width: 14px; height: 14px; stroke: currentColor; }

        /* ============ EMPTY STATE ============ */
        .empty-state {
            background: var(--surface);
            border-radius: 14px;
            padding: 50px 20px;
            text-align: center;
            border: 1px solid var(--border);
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
            margin-top: 10px;
        }
        .empty-state svg { width: 48px; height: 48px; stroke: #94a3b8; margin-bottom: 14px; }
        .empty-state h3 { font-size: 18px; font-weight: 800; color: var(--text-main); margin: 0 0 6px 0; }
        .empty-state p { color: var(--text-muted); font-size: 14px; margin: 0 0 20px 0; max-width: 400px; margin-left: auto; margin-right: auto; line-height: 1.5; }
        .btn-empty-return {
            background: var(--primary); color: white; text-decoration: none;
            padding: 12px 24px; border-radius: 8px; font-weight: 700; font-size: 14px;
            display: inline-flex; align-items: center; gap: 8px; transition: 0.2s;
        }
        .btn-empty-return:hover { background: var(--primary-hover); }

        @media (max-width: 600px) {
            .status-strip { flex-direction: column; align-items: flex-start; }
            .submission-actions { width: 100%; }
            .btn-edit-report, .btn-del-report { flex: 1; }
        }
    </style>
</head>
<body>

    <div class="topbar">
        <div class="topbar-left">
            <div class="topbar-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <polyline points="14 2 14 8 20 8"></polyline>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <polyline points="10 9 9 9 8 9"></polyline>
                </svg>
            </div>
            <div class="topbar-title">
                Submitted Reports
                <span>Task Maintenance History</span>
            </div>
        </div>
        <a href="tech_menu.php?task_id=<?php echo $task_id; ?>" class="back-btn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Back to Menu
        </a>
    </div>

    <div class="container">

        <div class="status-strip">
            <div>
                <div class="greeting"><?php echo htmlspecialchars($task['company_name']); ?></div>
                <div class="building-row">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>
                    <?php echo htmlspecialchars($task['building_name']); ?>
                </div>
            </div>
            <div class="badge-id">TASK #<?php echo $task_id; ?></div>
        </div>

        <div class="page-header">
            <h2>
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                Submitted Checklists
            </h2>
            <?php if (!empty($submitted_forms)): ?>
                <span class="submission-count"><?php echo count($submitted_forms); ?> Total Reports</span>
            <?php endif; ?>
        </div>

        <?php if (empty($submitted_forms)): ?>
            <div class="empty-state">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                <h3>No Checklists Submitted Yet</h3>
                <p>You haven't completed any system inspection checklists for this task. Return to the menu to select a system.</p>
                <a href="tech_menu.php?task_id=<?php echo $task_id; ?>" class="btn-empty-return">
                    ← Return to System Selection
                </a>
            </div>
        <?php else: ?>
            <div class="submission-grid">
                <?php foreach ($submitted_forms as $form): ?>
                    <div class="submission-card">
                        <div class="submission-info">
                            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                            <span class="submission-label"><?php echo htmlspecialchars($form['label']); ?></span>
                        </div>
                        <div class="submission-actions">
                            <a href="tech_edit.php?task_id=<?php echo $task_id; ?>&table=<?php echo $form['table']; ?>&sub_idx=<?php echo $form['idx']; ?>" class="btn-edit-report">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                Edit Form
                            </a>
                            <a href="tech_submitted_list.php?task_id=<?php echo $task_id; ?>&del_table=<?php echo $form['table']; ?>&del_idx=<?php echo $form['idx']; ?>" class="btn-del-report" onclick="return confirm('Are you sure you want to delete this submitted checklist? This action cannot be undone.');" title="Delete Form">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                                Delete
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>