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

// 3. Fetch the Company and Building name
$sql = "SELECT company_name, building_name FROM tasks WHERE task_id = $task_id AND status = 'Pending'";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("<div style='text-align:center; padding: 40px; font-family: sans-serif; background: #f4f5f7; height: 100vh;'>Task not found or already completed. <br><br><a href='tech_dashboard.php' style='color:#C41E3A; font-weight:bold; text-decoration:none;'>Go Back to Dashboard</a></div>");
}

$task = $result->fetch_assoc();

// 4. Count total submitted checklists for the badge
$submitted_count = 0;
foreach ($system_forms as $table => $name) {
    $rows = @$conn->query("SELECT DISTINCT section_name, item_bil FROM `$table` WHERE task_id = $task_id AND section_name = 'PANEL PROFILE'");
    if ($rows && $rows->num_rows > 0) {
        $submitted_count += $rows->num_rows;
    } else {
        $check = @$conn->query("SELECT id FROM `$table` WHERE task_id = $task_id LIMIT 1");
        if ($check && $check->num_rows > 0) {
            $submitted_count++;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Select System - Technician Portal</title>
    <style>
        :root {
            --primary: #C41E3A;
            --primary-hover: #a8182f;
            --bg-color: #f4f5f7;
            --surface: #ffffff;
            --text-main: #111827;
            --text-muted: #6b7280;
            --border: #e5e7eb;
            --cat-alarm: #ef4444;
            --cat-suppress: #f59e0b;
            --cat-water: #3b82f6;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: var(--bg-color); 
            color: var(--text-main);
            min-height: 100vh;
            padding-bottom: 80px;
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
        
        .back-btn {
            background: rgba(255,255,255,0.15);
            color: white;
            text-decoration: none;
            padding: 8px 14px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            transition: background 0.2s;
            display: flex; align-items: center; gap: 6px;
            border: 1px solid rgba(255,255,255,0.25);
        }
        .back-btn:hover { background: rgba(255,255,255,0.28); }

        /* ============ CONTAINER ============ */
        .container { max-width: 960px; margin: 0 auto; padding: clamp(16px, 4vw, 28px); }

        /* ============ SITE INFO CARD ============ */
        .status-strip {
            background: var(--surface);
            border-radius: 14px;
            padding: 20px 22px;
            margin-bottom: 20px;
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

        /* ============ SUBMITTED REPORTS BANNER BUTTON ============ */
        .submitted-banner {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: white;
            border-radius: 14px;
            padding: 18px 22px;
            margin-bottom: 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-decoration: none;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
            transition: all 0.2s;
            border: 1px solid #334155;
            gap: 16px;
        }
        .submitted-banner:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(15, 23, 42, 0.25);
            border-color: #475569;
        }
        .banner-left { display: flex; align-items: center; gap: 14px; }
        .banner-icon {
            width: 44px; height: 44px; background: rgba(255,255,255,0.1);
            border-radius: 10px; display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .banner-icon svg { width: 22px; height: 22px; stroke: #fff; }
        .banner-text h4 { font-size: 16px; font-weight: 800; margin: 0 0 2px 0; color: #fff; }
        .banner-text p { font-size: 13px; margin: 0; color: #94a3b8; }
        
        .banner-right { display: flex; align-items: center; gap: 10px; }
        .badge-count {
            background: var(--primary);
            color: white;
            font-size: 13px;
            font-weight: 800;
            padding: 4px 12px;
            border-radius: 20px;
        }
        .banner-arrow { font-size: 18px; font-weight: 900; color: #94a3b8; }

        /* ============ CATEGORY HEADERS ============ */
        .category-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 28px 0 14px 0;
            font-size: 15px;
            font-weight: 800;
            color: var(--text-main);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .category-header:first-of-type { margin-top: 10px; }
        .cat-dot { width: 10px; height: 10px; border-radius: 50%; }
        .cat-dot.red { background: var(--cat-alarm); box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.15); }
        .cat-dot.amber { background: var(--cat-suppress); box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15); }
        .cat-dot.blue { background: var(--cat-water); box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15); }

        /* ============ SYSTEM GRID ============ */
        .system-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); 
            gap: 14px; 
        }
        
        .system-card { 
            background-color: var(--surface); 
            border: 1.5px solid var(--border); 
            padding: 18px 20px; 
            border-radius: 12px; 
            text-decoration: none; 
            color: var(--text-main); 
            font-weight: 700; 
            font-size: 14px; 
            box-shadow: 0 2px 6px rgba(0,0,0,0.02); 
            transition: all 0.2s; 
            display: flex; 
            align-items: center; 
            justify-content: space-between;
            min-height: 64px;
        }
        .system-card:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 6px 16px rgba(0,0,0,0.06); 
            border-color: #cbd5e1;
        }
        .system-card-title { display: flex; align-items: center; gap: 12px; }
        .system-card-title svg { width: 20px; height: 20px; stroke: var(--text-muted); flex-shrink: 0; }
        .system-card-arrow { color: #94a3b8; font-weight: 900; transition: transform 0.2s; }
        .system-card:hover .system-card-arrow { transform: translateX(3px); color: var(--primary); }

        /* ============ COMPLETION AREA ============ */
        .completion-area { 
            margin-top: 48px; text-align: center; background: var(--surface); 
            padding: clamp(30px, 6vw, 40px) 20px; border-radius: 14px; 
            box-shadow: 0 4px 20px rgba(0,0,0,0.03); border: 1px solid var(--border); 
            display: flex; flex-direction: column; align-items: center;
        }
        .completion-area h3 { margin-top: 0; font-size: clamp(18px, 5vw, 22px); color: var(--text-main); margin-bottom: 8px; font-weight: 800; }
        .completion-area p { color: var(--text-muted); margin-bottom: 24px; font-size: 14px; max-width: 480px; line-height: 1.5; }
        
        .btn-complete { 
            background-color: var(--primary); color: white; border: none; 
            padding: 16px 32px; font-size: 16px; font-weight: 800; 
            border-radius: 10px; cursor: pointer; transition: all 0.2s; 
            box-shadow: 0 4px 14px rgba(196,30,58,0.25); width: 100%; max-width: 340px; 
            display: flex; align-items: center; justify-content: center; gap: 10px;
        }
        .btn-complete:hover { background-color: var(--primary-hover); transform: translateY(-2px); box-shadow: 0 6px 18px rgba(196,30,58,0.3); }

        /* ============ MODAL ============ */
        .modal-overlay { 
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; 
            background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; 
            padding: clamp(16px, 4vw, 24px); backdrop-filter: blur(4px); 
        }
        .modal-content { 
            background: var(--surface); padding: clamp(24px, 5vw, 32px); border-radius: 16px; 
            width: 100%; max-width: 550px; max-height: 90vh; overflow-y: auto; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.15); position: relative; 
        }
        .modal-header { font-size: 22px; font-weight: 800; margin-bottom: 8px; color: var(--text-main); }
        .modal-subtitle { color: var(--text-muted); font-size: 14px; margin-bottom: 24px; line-height: 1.5; }
        
        .close-btn { 
            position: absolute; top: 20px; right: 20px; background: #f1f5f9; 
            border: none; width: 32px; height: 32px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: var(--text-muted); transition: all 0.2s; 
        }
        .close-btn:hover { background: #e2e8f0; color: var(--text-main); }
        .close-btn svg { width: 18px; height: 18px; stroke: currentColor; stroke-width: 2; }
        
        .sign-group { margin-bottom: 24px; background: #fafbfc; padding: 20px; border-radius: 12px; border: 1px solid var(--border); }
        .sign-group label { display: block; font-weight: 700; margin-bottom: 8px; color: var(--text-main); font-size: 13px; }
        
        .sign-group input[type="text"] { 
            width: 100%; padding: 12px 14px; border: 1.5px solid var(--border); 
            border-radius: 8px; margin-bottom: 16px; font-size: 14px; 
            transition: all 0.2s; background: var(--surface); -webkit-appearance: none; 
            text-transform: uppercase;
        }
        .sign-group input[type="text"]:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(196,30,58,0.1); }
        
        canvas.sig-pad { border: 1.5px solid var(--border); background: var(--surface); width: 100%; height: 160px; border-radius: 8px; touch-action: none; cursor: crosshair; }
        
        .clear-btn { 
            background: var(--surface); color: var(--text-main); border: 1px solid var(--border); 
            padding: 8px 14px; border-radius: 6px; cursor: pointer; font-size: 12px; margin-top: 10px; 
            font-weight: 700; transition: all 0.2s;
        }
        .clear-btn:hover { background: #f1f5f9; }
        
        .btn-submit-modal { 
            width: 100%; background: var(--primary); color: white; padding: 16px; 
            border: none; font-size: 16px; font-weight: 800; border-radius: 8px; 
            cursor: pointer; transition: all 0.2s;
        }
        .btn-submit-modal:hover { background: var(--primary-hover); transform: translateY(-1px); }
        
        @media (max-width: 600px) {
            .status-strip { flex-direction: column; align-items: flex-start; }
            .submitted-banner { flex-direction: column; align-items: flex-start; }
            .banner-right { width: 100%; justify-content: space-between; margin-top: 4px; }
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
                System Selection
                <span>Maintenance Checklist</span>
            </div>
        </div>
        <a href="tech_dashboard.php" class="back-btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
            Back
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

        <!-- SUBMITTED REPORTS BANNER BUTTON -->
        <a href="tech_submitted_list.php?task_id=<?php echo $task_id; ?>" class="submitted-banner">
            <div class="banner-left">
                <div class="banner-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                </div>
                <div class="banner-text">
                    <h4>View Submitted Reports</h4>
                    <p>Review, edit, or remove completed checklists for this building</p>
                </div>
            </div>
            <div class="banner-right">
                <span class="badge-count"><?php echo $submitted_count; ?> Submitted</span>
                <span class="banner-arrow">➔</span>
            </div>
        </a>

        <!-- CATEGORY 1: ALARM & DETECTION -->
        <div class="category-header">
            <span class="cat-dot red"></span>
            Fire Alarm & Detection Systems
        </div>
        <div class="system-grid">
            <a href="form_fas_con.php?task_id=<?php echo $task_id; ?>" class="system-card">
                <div class="system-card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                    Conventional Fire Alarm
                </div>
                <span class="system-card-arrow">➔</span>
            </a>
            <a href="form_fas_add.php?task_id=<?php echo $task_id; ?>" class="system-card">
                <div class="system-card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path><path d="M13.73 21a2 2 0 0 1-3.46 0"></path></svg>
                    Addressable Fire Alarm
                </div>
                <span class="system-card-arrow">➔</span>
            </a>
            <a href="form_intercom.php?task_id=<?php echo $task_id; ?>" class="system-card">
                <div class="system-card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                    Fireman Intercom System
                </div>
                <span class="system-card-arrow">➔</span>
            </a>
        </div>

        <!-- CATEGORY 2: SUPPRESSION & CHEMICAL -->
        <div class="category-header">
            <span class="cat-dot amber"></span>
            Gas & Chemical Suppression Systems
        </div>
        <div class="system-grid">
            <a href="form_sup_1.php?task_id=<?php echo $task_id; ?>" class="system-card">
                <div class="system-card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 8a4 4 0 0 1 4-4h2a4 4 0 0 1 4 4v11a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2V8Z"></path><path d="M10 4V2h4v2"></path><line x1="8" y1="10" x2="16" y2="10"></line></svg>
                    CO2 Suppression System
                </div>
                <span class="system-card-arrow">➔</span>
            </a>
            <a href="form_sup_2.php?task_id=<?php echo $task_id; ?>" class="system-card">
                <div class="system-card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 8a4 4 0 0 1 4-4h2a4 4 0 0 1 4 4v11a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2V8Z"></path><path d="M10 4V2h4v2"></path><line x1="8" y1="10" x2="16" y2="10"></line></svg>
                    FM200 Suppression System
                </div>
                <span class="system-card-arrow">➔</span>
            </a>
            <a href="form_sup_3.php?task_id=<?php echo $task_id; ?>" class="system-card">
                <div class="system-card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 8a4 4 0 0 1 4-4h2a4 4 0 0 1 4 4v11a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2V8Z"></path><path d="M10 4V2h4v2"></path><line x1="8" y1="10" x2="16" y2="10"></line></svg>
                    FE-13 Suppression System
                </div>
                <span class="system-card-arrow">➔</span>
            </a>
            <a href="form_sup_4.php?task_id=<?php echo $task_id; ?>" class="system-card">
                <div class="system-card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 8a4 4 0 0 1 4-4h2a4 4 0 0 1 4 4v11a2 2 0 0 1-2 2H9a2 2 0 0 1-2-2V8Z"></path><path d="M10 4V2h4v2"></path><line x1="8" y1="10" x2="16" y2="10"></line></svg>
                    Inert Gas Suppression System
                </div>
                <span class="system-card-arrow">➔</span>
            </a>
            <a href="form_wet_chem.php?task_id=<?php echo $task_id; ?>" class="system-card">
                <div class="system-card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 2v7.527a2 2 0 0 1-.211.896L4.72 20.55a1 1 0 0 0 .9 1.45h12.76a1 1 0 0 0 .9-1.45l-5.069-10.127A2 2 0 0 1 14 9.527V2"></path><path d="M8.5 2h7"></path><path d="M7 16h10"></path></svg>
                    Wet Chemical System
                </div>
                <span class="system-card-arrow">➔</span>
            </a>
        </div>

        <!-- CATEGORY 3: WATER-BASED PROTECTION -->
        <div class="category-header">
            <span class="cat-dot blue"></span>
            Water-Based Protection Systems
        </div>
        <div class="system-grid">
            <a href="form_hose_reel.php?task_id=<?php echo $task_id; ?>" class="system-card">
                <div class="system-card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="M12 8v4l3 3"></path></svg>
                    Fire Hose Reel System
                </div>
                <span class="system-card-arrow">➔</span>
            </a>
            <a href="form_sprinkler.php?task_id=<?php echo $task_id; ?>" class="system-card">
                <div class="system-card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path></svg>
                    Fire Sprinkler System
                </div>
                <span class="system-card-arrow">➔</span>
            </a>
            <a href="form_wet_riser.php?task_id=<?php echo $task_id; ?>" class="system-card">
                <div class="system-card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path></svg>
                    Wet Riser System
                </div>
                <span class="system-card-arrow">➔</span>
            </a>
            <a href="form_dry_riser.php?task_id=<?php echo $task_id; ?>" class="system-card">
                <div class="system-card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path></svg>
                    Dry Riser System
                </div>
                <span class="system-card-arrow">➔</span>
            </a>
            <a href="form_hydrant.php?task_id=<?php echo $task_id; ?>" class="system-card">
                <div class="system-card-title">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 10h14"></path><path d="M7 10v10a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V10"></path><path d="M9 10V6a3 3 0 0 1 6 0v4"></path><path d="M3 14h2"></path><path d="M19 14h2"></path><circle cx="12" cy="15" r="2"></circle></svg>
                    Pressurised Hydrant System
                </div>
                <span class="system-card-arrow">➔</span>
            </a>
        </div>

        <!-- COMPLETION AREA -->
        <div class="completion-area">
            <h3>Done with this building?</h3>
            <p>Once you have filled out all required checklists for this location, sign off to mark the task as complete.</p>
            <button type="button" onclick="openModal()" class="btn-complete">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="width: 20px; height: 20px;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                Mark Job as Completed
            </button>
        </div>
    </div>

    <!-- SIGN-OFF MODAL -->
    <div id="sigModal" class="modal-overlay">
        <div class="modal-content">
            <button class="close-btn" onclick="closeModal()">
                <svg viewBox="0 0 24 24" fill="none"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
            <div class="modal-header">Final Sign-Off</div>
            <p class="modal-subtitle">Please provide names and signatures to verify completion of this job.</p>
            
            <form id="completionForm" action="complete_task.php" method="POST">
                <input type="hidden" name="task_id" value="<?php echo $task_id; ?>">
                <input type="hidden" name="tech_signature" id="tech_signature">
                <input type="hidden" name="client_signature" id="client_signature">

                <div class="sign-group">
                    <label>Technician's Full Name</label>
                    <input type="text" name="tech_sign_name" required placeholder="Enter your name" oninput="this.value = this.value.toUpperCase()">
                    
                    <label>Technician's Company</label>
                    <input type="text" name="tech_company" required value="LTA SERVICES" oninput="this.value = this.value.toUpperCase()">

                    <label>Technician's Signature</label>
                    <canvas id="techPad" class="sig-pad"></canvas>
                    <button type="button" class="clear-btn" onclick="clearCanvas('techPad')">Clear Signature</button>
                </div>

                <div class="sign-group">
                    <label>Client / Representative's Full Name</label>
                    <input type="text" name="client_sign_name" required placeholder="Enter client's name" oninput="this.value = this.value.toUpperCase()">
                    
                    <label>Client's Signature</label>
                    <canvas id="clientPad" class="sig-pad"></canvas>
                    <button type="button" class="clear-btn" onclick="clearCanvas('clientPad')">Clear Signature</button>
                </div>

                <button type="button" onclick="submitSignatures()" class="btn-submit-modal">Submit & Complete Job</button>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('sigModal').style.display = 'flex';
            setTimeout(() => {
                initPad('techPad');
                initPad('clientPad');
            }, 50);
        }

        function closeModal() {
            document.getElementById('sigModal').style.display = 'none';
        }

        function initPad(canvasId) {
            var canvas = document.getElementById(canvasId);
            var ctx = canvas.getContext('2d');
            
            var rect = canvas.getBoundingClientRect();
            canvas.width = rect.width;
            canvas.height = rect.height;

            var drawing = false;

            function getMousePos(e) {
                var rect = canvas.getBoundingClientRect();
                var clientX = e.touches ? e.touches[0].clientX : e.clientX;
                var clientY = e.touches ? e.touches[0].clientY : e.clientY;
                return { x: clientX - rect.left, y: clientY - rect.top };
            }

            function startPos(e) { e.preventDefault(); drawing = true; draw(e); }
            function endPos(e) { e.preventDefault(); drawing = false; ctx.beginPath(); }
            
            function draw(e) {
                if (!drawing) return;
                e.preventDefault();
                var pos = getMousePos(e);
                ctx.lineWidth = 2;
                ctx.lineCap = 'round';
                ctx.strokeStyle = '#000';
                ctx.lineTo(pos.x, pos.y);
                ctx.stroke();
                ctx.beginPath();
                ctx.moveTo(pos.x, pos.y);
            }

            canvas.replaceWith(canvas.cloneNode(true));
            canvas = document.getElementById(canvasId);
            ctx = canvas.getContext('2d');
            
            canvas.addEventListener('mousedown', startPos);
            canvas.addEventListener('mouseup', endPos);
            canvas.addEventListener('mousemove', draw);
            canvas.addEventListener('mouseout', endPos);

            canvas.addEventListener('touchstart', startPos, {passive: false});
            canvas.addEventListener('touchend', endPos, {passive: false});
            canvas.addEventListener('touchmove', draw, {passive: false});
        }

        function clearCanvas(canvasId) {
            var canvas = document.getElementById(canvasId);
            var ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
        }

        function submitSignatures() {
            var techCanvas = document.getElementById('techPad');
            var clientCanvas = document.getElementById('clientPad');
            
            var techBlank = isCanvasBlank(techCanvas);
            var clientBlank = isCanvasBlank(clientCanvas);
            
            if (techBlank || clientBlank) {
                alert("Please ensure both the Technician and Client provide a signature.");
                return;
            }

            document.getElementById('tech_signature').value = techCanvas.toDataURL();
            document.getElementById('client_signature').value = clientCanvas.toDataURL();

            document.getElementById('completionForm').submit();
        }
        
        function isCanvasBlank(canvas) {
            const blank = document.createElement('canvas');
            blank.width = canvas.width;
            blank.height = canvas.height;
            return canvas.toDataURL() === blank.toDataURL();
        }
    </script>
</body>
</html>