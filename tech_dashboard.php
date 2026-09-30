<?php
session_start();

// 1. Security Check: Kick them out if they aren't the technician
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'technician') {
    header("Location: index.php");
    exit();
}

// 2. Connect to the Database
require 'db.php'; 

// 3. Fetch ONLY the Pending tasks from the main hub
$sql = "SELECT * FROM tasks WHERE status = 'Pending' ORDER BY created_at ASC";
$pending_tasks = $conn->query($sql);
$total_pending = $pending_tasks->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Technician Dashboard - Fire System Maintenance</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f5f7;
            min-height: 100vh;
            color: #1a1a1a;
            padding-bottom: 40px;
        }

        /* ============ TOP BAR ============ */
        .topbar {
            background: #C41E3A;
            color: white;
            padding: clamp(16px, 4vw, 24px) clamp(16px, 5vw, 30px);
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
            width: 42px; height: 42px;
            background: rgba(255,255,255,0.15);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .topbar-icon svg { width: 22px; height: 22px; }
        .topbar-title { font-size: clamp(16px, 4vw, 19px); font-weight: 700; letter-spacing: 0.3px; }
        .topbar-title span {
            display: block; font-size: 11px; font-weight: 400; opacity: 0.8;
            text-transform: uppercase; letter-spacing: 1.5px; margin-top: 2px;
        }
        .logout-btn {
            background: rgba(255,255,255,0.15);
            color: white;
            text-decoration: none;
            padding: 9px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: clamp(13px, 3vw, 14px);
            transition: background 0.2s;
            display: flex; align-items: center; gap: 6px;
            border: 1px solid rgba(255,255,255,0.25);
        }
        .logout-btn:hover { background: rgba(255,255,255,0.28); }
        .logout-btn svg { width: 16px; height: 16px; }

        /* ============ CONTAINER ============ */
        .container { max-width: 900px; margin: 0 auto; padding: clamp(16px, 4vw, 30px); }

        /* ============ ALERTS ============ */
        .alert {
            padding: 14px 16px; border-radius: 8px; margin-bottom: 20px;
            font-size: clamp(13px, 3.5vw, 15px); font-weight: 500;
            display: flex; align-items: center; gap: 10px;
        }
        .alert svg { width: 18px; height: 18px; flex-shrink: 0; }
        .alert-success { background: #e7f6ec; color: #1e7a3d; border: 1px solid #c3e6cb; }
        .alert-danger { background: #fdecee; color: #b3273f; border: 1px solid #f6c9d0; }

        /* ============ STATUS STRIP ============ */
        .status-strip {
            background: white;
            border-radius: 12px;
            padding: 18px 22px;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            flex-wrap: wrap;
            gap: 12px;
        }
        .status-strip .greeting {
            font-size: clamp(15px, 4vw, 18px);
            font-weight: 700;
            color: #1a1a1a;
        }
        .status-strip .greeting span {
            display: block;
            font-size: 12px;
            font-weight: 400;
            color: #9aa0a8;
            margin-top: 3px;
            letter-spacing: 0.3px;
        }
        .pending-count {
            background: #fdecee;
            color: #C41E3A;
            font-weight: 700;
            font-size: clamp(13px, 3.5vw, 15px);
            padding: 8px 16px;
            border-radius: 30px;
            display: flex; align-items: center; gap: 8px;
        }
        .pending-count .num {
            background: #C41E3A; color: white;
            border-radius: 50%; width: 24px; height: 24px;
            display: flex; align-items: center; justify-content: center;
            font-size: 13px;
        }

        /* ============ SECTION TITLE ============ */
        h2.section-title {
            color: #1a1a1a;
            font-size: clamp(17px, 4.5vw, 21px);
            margin-bottom: 16px;
            font-weight: 700;
            display: flex; align-items: center; gap: 10px;
        }
        h2.section-title svg { width: 22px; height: 22px; stroke: #C41E3A; }

        /* ============ JOB CARDS ============ */
        .job-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            margin-bottom: 16px;
            overflow: hidden;
            transition: transform 0.15s, box-shadow 0.15s;
            border: 1px solid #f0f0f0;
        }
        .job-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }
        .job-card-top {
            padding: 18px 20px 14px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 12px;
        }
        .job-card-main h3 {
            font-size: clamp(16px, 4.5vw, 19px);
            color: #1a1a1a;
            margin-bottom: 6px;
            font-weight: 700;
        }
        .job-card-main .building-row {
            display: flex; align-items: center; gap: 6px;
            color: #6b7280; font-size: clamp(13px, 3.5vw, 14px);
            margin-bottom: 4px;
        }
        .job-card-main .building-row svg { width: 14px; height: 14px; stroke: #9aa0a8; flex-shrink: 0; }

        .badge-id {
            background: #f4f5f7;
            color: #6b7280;
            font-size: 12px;
            font-weight: 700;
            padding: 5px 10px;
            border-radius: 6px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .job-card-bottom {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 20px;
            background: #fafbfc;
            border-top: 1px solid #f0f0f0;
            gap: 12px;
            flex-wrap: wrap;
        }
        .assigned-date {
            font-size: 12px; color: #9aa0a8;
            display: flex; align-items: center; gap: 6px;
        }
        .assigned-date svg { width: 13px; height: 13px; stroke: #b0b4bb; }

        .badge-pending {
            background: #fff4e5; color: #c2750c;
            padding: 5px 12px; border-radius: 20px;
            font-size: 12px; font-weight: 700;
            display: inline-flex; align-items: center; gap: 5px;
        }
        .badge-pending::before {
            content: ""; width: 6px; height: 6px; border-radius: 50%;
            background: #f0ad4e; display: inline-block;
        }

        /* ============ START BUTTON ============ */
        .start-btn {
            background-color: #C41E3A;
            color: white;
            text-decoration: none;
            padding: 13px 24px;
            border-radius: 8px;
            font-weight: 700;
            font-size: clamp(14px, 3.5vw, 15px);
            text-align: center;
            transition: all 0.2s;
            white-space: nowrap;
            box-shadow: 0 4px 10px rgba(196,30,58,0.2);
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .start-btn:hover {
            background-color: #a8182f;
            transform: translateY(-1px);
            box-shadow: 0 6px 14px rgba(196,30,58,0.3);
        }
        .start-btn svg { width: 16px; height: 16px; }

        /* ============ EMPTY STATE ============ */
        .empty-state {
            text-align: center;
            padding: clamp(50px, 12vw, 70px) 20px;
            background: white;
            border-radius: 12px;
            color: #6b7280;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            border: 2px dashed #e6e7eb;
        }
        .empty-state .icon-wrap {
            width: 64px; height: 64px;
            background: #fdecee;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 18px;
        }
        .empty-state .icon-wrap svg { width: 30px; height: 30px; stroke: #C41E3A; }
        .empty-state h2 { color: #1a1a1a; margin-bottom: 8px; font-size: clamp(17px, 4.5vw, 20px); font-weight: 700; }
        .empty-state p { font-size: clamp(13px, 3.5vw, 14px); }

        /* ============ MOBILE ============ */
        @media (max-width: 600px) {
            .job-card-top { flex-direction: column; }
            .badge-id { align-self: flex-start; }
            .job-card-bottom { flex-direction: column; align-items: stretch; }
            .start-btn { width: 100%; justify-content: center; padding: 14px; }
            .status-strip { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>

    <!-- TOP BAR -->
    <div class="topbar">
        <div class="topbar-left">
            <div class="topbar-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/>
                </svg>
            </div>
            <div class="topbar-title">
                Technician Portal
                <span>Maintenance Checklist</span>
            </div>
        </div>
        <a href="logout.php" class="logout-btn" onclick="return confirm('Are you sure you want to log out?');">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Logout
        </a>
    </div>

    <div class="container">

        <?php if(isset($_GET['completed']) && $_GET['completed'] == 'success'): ?>
            <div class="alert alert-success">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                Job successfully marked as completed and submitted to Admin!
            </div>
        <?php endif; ?>
        <?php if(isset($_GET['error']) && $_GET['error'] == 'failed'): ?>
            <div class="alert alert-danger">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Error saving task. Please try again or contact Admin.
            </div>
        <?php endif; ?>

        <!-- STATUS STRIP -->
        <div class="status-strip">
            <div class="greeting">
                Good to go!
                <span>Here are the sites assigned to you</span>
            </div>
            <div class="pending-count">
                <span class="num"><?php echo $total_pending; ?></span>
                <?php echo $total_pending == 1 ? 'Job Pending' : 'Jobs Pending'; ?>
            </div>
        </div>

        <h2 class="section-title">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
            My Assigned Jobs
        </h2>

        <?php if ($pending_tasks->num_rows > 0): ?>
            <?php while($row = $pending_tasks->fetch_assoc()): ?>
                <div class="job-card">
                    <div class="job-card-top">
                        <div class="job-card-main">
                            <h3><?php echo htmlspecialchars($row['company_name']); ?></h3>
                            <div class="building-row">
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>
                                <?php echo htmlspecialchars($row['building_name']); ?>
                            </div>
                        </div>
                        <span class="badge-id">TASK #<?php echo $row['task_id']; ?></span>
                    </div>
                    <div class="job-card-bottom">
                        <div style="display:flex; align-items:center; gap:12px;">
                            <span class="badge-pending">Pending</span>
                            <span class="assigned-date">
                                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                <?php echo date('d M Y, h:i A', strtotime($row['created_at'])); ?>
                            </span>
                        </div>
                        <a href="tech_menu.php?task_id=<?php echo $row['task_id']; ?>" class="start-btn">
                            Open Job
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">
                <div class="icon-wrap">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <h2>All caught up!</h2>
                <p>The admin hasn't assigned any new maintenance tasks yet. Take a break.</p>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>