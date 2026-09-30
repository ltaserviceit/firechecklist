<?php
session_start();

// 1. Security Check: Kick them out if they aren't the admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// 2. Connect to the Database
require 'db.php'; 

// 3. Fetch Data for the Dashboard Summary Cards
$total_query = $conn->query("SELECT COUNT(*) as count FROM tasks");
$total_tasks = $total_query->fetch_assoc()['count'];

$pending_query = $conn->query("SELECT COUNT(*) as count FROM tasks WHERE status = 'Pending'");
$pending_tasks = $pending_query->fetch_assoc()['count'];

$completed_query = $conn->query("SELECT COUNT(*) as count FROM tasks WHERE status = 'Completed'");
$completed_tasks = $completed_query->fetch_assoc()['count'];

// 4. Task History - filterable by Company Name (user-typed, case-insensitive), Month and Year
$filter_company = isset($_GET['company']) ? trim($_GET['company']) : '';
$filter_month   = isset($_GET['month']) ? trim($_GET['month']) : ''; // '01'-'12'
$filter_year    = isset($_GET['year']) ? trim($_GET['year']) : '';   // e.g. '2026'

// Distinct company list, used only as typing suggestions (datalist) - not a restriction
$companies_result = $conn->query("SELECT DISTINCT company_name FROM tasks WHERE company_name IS NOT NULL AND company_name != '' ORDER BY company_name ASC");

// Distinct years present in the data, for the Year dropdown
$years_result = $conn->query("SELECT DISTINCT YEAR(created_at) as yr FROM tasks WHERE created_at IS NOT NULL ORDER BY yr DESC");

$month_names = [
    '01' => 'January', '02' => 'February', '03' => 'March',     '04' => 'April',
    '05' => 'May',      '06' => 'June',     '07' => 'July',      '08' => 'August',
    '09' => 'September','10' => 'October',  '11' => 'November',  '12' => 'December',
];

$where  = [];
$params = [];
$types  = '';

if ($filter_company !== '') {
    // LIKE is case-insensitive by default in MySQL (with standard collations); escape % and _ so they aren't treated as wildcards
    $where[]  = "company_name LIKE ?";
    $params[] = '%' . addcslashes($filter_company, '%_') . '%';
    $types   .= 's';
}
if ($filter_month !== '' && $filter_year !== '') {
    $where[]  = "DATE_FORMAT(created_at, '%Y-%m') = ?";
    $params[] = $filter_year . '-' . $filter_month;
    $types   .= 's';
} elseif ($filter_year !== '') {
    $where[]  = "YEAR(created_at) = ?";
    $params[] = $filter_year;
    $types   .= 's';
} elseif ($filter_month !== '') {
    $where[]  = "MONTH(created_at) = ?";
    $params[] = $filter_month;
    $types   .= 's';
}

$has_filters = !empty($where);

$sql = "SELECT * FROM tasks";
if ($has_filters) {
    $sql .= " WHERE " . implode(" AND ", $where);
}
$sql .= " ORDER BY created_at DESC";
if (!$has_filters) {
    $sql .= " LIMIT 5"; // no filters -> just show the 5 most recent, as before
}

$stmt = $conn->prepare($sql);
if ($types !== '') {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$recent_tasks = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Admin Dashboard - FireSafe Systems</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #C41E3A; /* Cardinal Red Main Theme */
            --primary-hover: #a8182f;
            --bg-color: #f4f7f6;
            --surface: #ffffff;
            --text-main: #1a1a1a;
            --text-muted: #6b7280;
            --border: #e5e7eb;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #f0f2f5 0%, #e1e4e8 100%); 
            color: var(--text-main);
            display: flex; 
            min-height: 100vh; 
        }
        
        /* ============ SIDEBAR ============ */
        .sidebar { 
            width: 260px; 
            background-color: var(--surface); 
            border-right: 1.5px solid var(--border);
            display: flex; 
            flex-direction: column; 
            position: fixed; 
            height: 100vh; 
            z-index: 100; 
            transition: all 0.3s ease; 
        }

        .brand {
            padding: 30px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid var(--border);
        }

        .brand-icon {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: var(--primary); 
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 4px 10px rgba(196, 30, 58, 0.3);
        }

        .brand-icon svg { width: 18px; height: 18px; stroke: #fff; }

        .brand-name {
            font-size: 15px;
            font-weight: 700;
            color: var(--text-main);
            line-height: 1.2;
        }

        .brand-name span {
            display: block;
            font-size: 11px;
            font-weight: 400;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .nav-links {
            padding: 24px 12px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .sidebar a { 
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--text-main); 
            padding: 12px 16px; 
            text-decoration: none; 
            font-weight: 500; 
            font-size: 14px;
            border-radius: 8px;
            transition: background 0.2s, color 0.2s; 
        }
        
        .sidebar a:hover { background-color: var(--bg-color); }
        .sidebar a.active { 
            background-color: rgba(196, 30, 58, 0.08); 
            color: var(--primary); 
            font-weight: 600; 
            border-right: 4px solid var(--primary); 
        }
        
        .logout { 
            margin-top: auto; 
            margin-bottom: 24px;
            margin-left: 12px;
            margin-right: 12px;
            color: #dc2626 !important;
            background: #fef2f2;
            border: 1px solid #fecaca;
        }
        .logout:hover { background-color: #fca5a5 !important; color: white !important; }

        /* ============ MAIN CONTENT ============ */
        .main-content { 
            margin-left: 260px; 
            padding: clamp(24px, 5vw, 40px); 
            width: calc(100% - 260px); 
        }
        
        .header-section {
            margin-bottom: 30px;
        }

        h1 { 
            font-size: clamp(24px, 4vw, 28px); 
            font-weight: 700;
            letter-spacing: -0.3px;
            margin-bottom: 6px;
        }
        
        .subtitle {
            color: var(--text-muted);
            font-size: 14px;
        }

        /* ============ COLORFUL CARDS ============ */
        .cards-container { 
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px; 
            margin-bottom: 30px; 
        }
        
        .card { 
            padding: 24px; 
            border-radius: 12px; 
            border: 1.5px solid var(--border);
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
            transition: transform 0.2s, box-shadow 0.2s; 
        }
        
        .card:hover { 
            transform: translateY(-4px); 
            box-shadow: 0 10px 25px rgba(0,0,0,0.06);
        }

        .card h3 { 
            margin: 0; 
            font-size: 13px; 
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card .number { 
            font-size: 32px; 
            font-weight: 800; 
            margin: 12px 0 0; 
            letter-spacing: -0.5px;
        }

        .card-total { border-top: 5px solid #3b82f6; background: linear-gradient(145deg, #ffffff, #eff6ff); }
        .card-total h3 { color: #3b82f6; }
        .card-total .number { color: #2563eb; }

        .card-pending { border-top: 5px solid #ef4444; background: linear-gradient(145deg, #ffffff, #fef2f2); }
        .card-pending h3 { color: #ef4444; }
        .card-pending .number { color: #dc2626; }

        .card-completed { border-top: 5px solid #10b981; background: linear-gradient(145deg, #ffffff, #ecfdf5); }
        .card-completed h3 { color: #10b981; }
        .card-completed .number { color: #059669; }

        /* ============ DASHBOARD PANELS (Chart & Table) ============ */
        .dashboard-row { 
            display: flex; 
            flex-wrap: wrap; 
            gap: 20px; 
            align-items: flex-start; 
        } 
        
        .panel {
            background: var(--surface); 
            padding: 24px; 
            border-radius: 12px; 
            border: 1.5px solid var(--border);
            box-shadow: 0 4px 15px rgba(0,0,0,0.03);
        }

        .panel h3 { 
            margin-top: 0; 
            color: var(--text-main); 
            font-size: 16px; 
            font-weight: 600;
            margin-bottom: 20px;
        }

        .chart-container { 
            flex: 1; 
            min-width: 280px; 
            background: linear-gradient(145deg, #ffffff, #fefce8); 
            border-top: 5px solid #f59e0b;
        }
        .canvas-wrapper { position: relative; height: 250px; width: 100%; } 
        
        .table-container { flex: 2; min-width: 300px; overflow: hidden; padding: 0; border-top: 5px solid var(--primary); }
        .table-container h3 { padding: 24px 24px 0 24px; margin-bottom: 15px;}

        /* ============ FILTER BAR ============ */
        .filter-bar {
            display: flex;
            flex-wrap: wrap;
            align-items: flex-end;
            gap: 14px;
            padding: 0 24px 20px 24px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .filter-group label {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
        }

        .filter-group select,
        .filter-group input[type="text"] {
            padding: 10px 14px;
            min-width: 220px;
            border-radius: 8px;
            border: 1.5px solid var(--border);
            background: var(--bg-color);
            color: var(--text-main);
            font-family: inherit;
            font-size: 14px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
        }

        .filter-group select { cursor: pointer; }

        .filter-group select:hover,
        .filter-group input[type="text"]:hover {
            border-color: #c9ccd1;
        }

        .filter-group select:focus,
        .filter-group input[type="text"]:focus {
            outline: none;
            border-color: var(--primary);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(196, 30, 58, 0.12);
        }

        .filter-btn {
            padding: 10.5px 22px;
            border-radius: 8px;
            border: none;
            background: var(--primary);
            color: #fff;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.2s ease, transform 0.15s ease, box-shadow 0.2s ease;
        }

        .filter-btn:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 14px rgba(196, 30, 58, 0.25);
        }

        .filter-btn:active { transform: translateY(0); }

        .filter-clear {
            padding: 10px 16px;
            border-radius: 8px;
            border: 1.5px solid var(--border);
            font-size: 13px;
            font-weight: 600;
            color: var(--text-muted);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .filter-clear:hover {
            background: var(--bg-color);
            color: var(--text-main);
            border-color: #c9ccd1;
        }

        .filter-summary {
            padding: 0 24px 16px 24px;
            font-size: 13px;
            color: var(--text-muted);
            margin-top: -6px;
        }

        .filter-summary strong { color: var(--text-main); }

        /* ============ SMOOTH TABLE TRANSITIONS ============ */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .table-responsive { animation: fadeInUp 0.35s ease both; }
        .table-responsive tr { transition: background-color 0.15s ease; }
        
        /* ============ TABLE ============ */
        .table-responsive { overflow-x: auto; width: 100%; }
        table { width: 100%; border-collapse: collapse; min-width: 600px; }
        
        th { 
            background-color: var(--primary);
            color: white; 
            text-align: left; 
            font-size: 13px; 
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 14px 16px;
        }

        td { 
            border-bottom: 1px solid var(--border); 
            padding: 16px; 
            font-size: 14px; 
            color: var(--text-main);
        }

        tr:hover td { background-color: #f9fafb; }
        tr:last-child td { border-bottom: none; }
        
        /* Badges */
        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            display: inline-block;
        }
        .badge-pending { background-color: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
        .badge-completed { background-color: #d1fae5; color: #059669; border: 1px solid #a7f3d0; }
        
        /* Colorful Action Buttons */
        .action-buttons { display: flex; gap: 8px; flex-wrap: wrap; }
        
        .btn-action {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            border: 1px solid transparent;
            transition: all 0.2s;
        }
        
        .btn-view { background: #eff6ff; color: #2563eb; border-color: #bfdbfe; }
        .btn-view:hover { background: #dbeafe; }
        
        .btn-edit { background: #fef3c7; color: #d97706; border-color: #fde68a; }
        .btn-edit:hover { background: #fde68a; }
        
        .btn-delete { background: #fef2f2; color: #dc2626; border-color: #fecaca; }
        .btn-delete:hover { background: #fecaca; }

        /* ============ ALERTS ============ */
        .alert { 
            padding: 14px 16px; 
            border-radius: 8px; 
            margin-bottom: 24px; 
            font-size: 14px; 
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .alert-danger { background: #fdecee; color: #b3273f; border: 1px solid #f6c9d0; }

        /* ============ RESPONSIVE ============ */
        @media (max-width: 860px) {
            body { flex-direction: column; }
            .sidebar { 
                position: relative; 
                width: 100%; 
                height: auto; 
                border-right: none;
                border-bottom: 1.5px solid var(--border);
            }
            .brand { justify-content: center; border-bottom: none; padding: 20px; }
            .nav-links { flex-direction: row; flex-wrap: wrap; justify-content: center; padding: 0 20px 20px; }
            .logout { margin: 0; }
            .main-content { margin-left: 0; width: 100%; }
            .dashboard-row { flex-direction: column; }
            .chart-container, .table-container { width: 100%; }
            .filter-bar { flex-direction: column; align-items: stretch; }
            .filter-group select, .filter-group input[type="text"] { min-width: 0; width: 100%; }
            .filter-btn, .filter-clear { width: 100%; justify-content: center; }
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
            <a href="admin_dashboard.php" class="active">
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
        
        <div class="header-section">
            <h1>System Overview</h1>
            <p class="subtitle">Monitor system metrics and manage maintenance tasks.</p>
        </div>
        
        <?php if(isset($_GET['deleted'])): ?>
            <div class="alert alert-success">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                Task deleted successfully.
            </div>
        <?php endif; ?>
        <?php if(isset($_GET['edited'])): ?>
            <div class="alert alert-success">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                Task updated successfully.
            </div>
        <?php endif; ?>
        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                Error performing action. Please try again.
            </div>
        <?php endif; ?>

        <div class="cards-container">
            <div class="card card-total">
                <h3>Total Tasks</h3>
                <div class="number"><?php echo $total_tasks; ?></div>
            </div>
            <div class="card card-pending">
                <h3>Pending Tasks</h3>
                <div class="number"><?php echo $pending_tasks; ?></div>
            </div>
            <div class="card card-completed">
                <h3>Completed Tasks</h3>
                <div class="number"><?php echo $completed_tasks; ?></div>
            </div>
        </div>

        <div class="dashboard-row">
            
            <div class="panel chart-container">
                <h3>Task Status Breakdown</h3>
                <div class="canvas-wrapper">
                    <canvas id="taskChart"></canvas>
                </div>
            </div>

            <div class="panel table-container">
                <h3><?php echo $has_filters ? 'Task History' : 'Recently Created Tasks'; ?></h3>

                <form method="GET" class="filter-bar">
                    <div class="filter-group">
                        <label for="filter-company">Company</label>
                        <input type="text" id="filter-company" name="company" list="company-list" placeholder="Type company name..." value="<?php echo htmlspecialchars($filter_company); ?>" autocomplete="off">
                        <datalist id="company-list">
                            <?php while ($c = $companies_result->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($c['company_name']); ?>">
                            <?php endwhile; ?>
                        </datalist>
                    </div>

                    <div class="filter-group">
                        <label for="filter-month">Month</label>
                        <select id="filter-month" name="month">
                            <option value="">Any Month</option>
                            <?php foreach ($month_names as $num => $name): ?>
                                <option value="<?php echo $num; ?>" <?php echo ($filter_month === $num) ? 'selected' : ''; ?>><?php echo $name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label for="filter-year">Year</label>
                        <select id="filter-year" name="year">
                            <option value="">Any Year</option>
                            <?php while ($y = $years_result->fetch_assoc()): ?>
                                <option value="<?php echo $y['yr']; ?>" <?php echo ($filter_year === (string)$y['yr']) ? 'selected' : ''; ?>><?php echo $y['yr']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <button type="submit" class="filter-btn">Apply Filter</button>
                    <?php if ($has_filters): ?>
                        <a href="admin_dashboard.php" class="filter-clear">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                            Clear
                        </a>
                    <?php endif; ?>
                </form>

                <?php if ($has_filters): ?>
                    <div class="filter-summary">
                        Showing <strong><?php echo $recent_tasks->num_rows; ?></strong> result<?php echo $recent_tasks->num_rows == 1 ? '' : 's'; ?>
                        <?php if ($filter_company !== ''): ?> for <strong><?php echo htmlspecialchars($filter_company); ?></strong><?php endif; ?>
                        <?php if ($filter_month !== '' && $filter_year !== ''): ?>
                            in <strong><?php echo $month_names[$filter_month] . ' ' . $filter_year; ?></strong>
                        <?php elseif ($filter_year !== ''): ?>
                            in <strong><?php echo $filter_year; ?></strong>
                        <?php elseif ($filter_month !== ''): ?>
                            in <strong><?php echo $month_names[$filter_month]; ?></strong> (any year)
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <div class="table-responsive">
                    <table>
                        <tr>
                            <th>ID</th>
                            <th>Company Name</th>
                            <th>Building Name</th>
                            <th>Date Created</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                        <?php if ($recent_tasks->num_rows > 0): ?>
                            <?php while($row = $recent_tasks->fetch_assoc()): ?>
                                <tr>
                                    <td style="font-weight: 600;">#<?php echo $row['task_id']; ?></td>
                                    <td style="font-weight: 500; color: #111;"><?php echo htmlspecialchars($row['company_name']); ?></td>
                                    <td><?php echo htmlspecialchars($row['building_name']); ?></td>
                                    <td style="color: var(--text-muted); font-size: 13px;"><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
                                    <td>
                                        <?php if($row['status'] == 'Pending'): ?>
                                            <span class="badge badge-pending">Pending</span>
                                        <?php else: ?>
                                            <span class="badge badge-completed">Completed</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <?php if($row['status'] == 'Completed'): ?>
                                                <a href="view_report.php?task_id=<?php echo $row['task_id']; ?>" class="btn-action btn-view">View</a>
                                            <?php endif; ?>
                                            
                                            <a href="admin_edit.php?task_id=<?php echo $row['task_id']; ?>" class="btn-action btn-edit">Edit</a>
                                            
                                            <form action="delete_task.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this task? This cannot be undone.');" style="margin: 0;">
                                                <input type="hidden" name="task_id" value="<?php echo $row['task_id']; ?>">
                                                <button type="submit" class="btn-action btn-delete">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" style="text-align:center; padding: 40px; color: var(--text-muted);">
                                <?php echo $has_filters ? 'No tasks match these filters.' : 'No tasks found. Create one!'; ?>
                            </td></tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script>
        const ctx = document.getElementById('taskChart').getContext('2d');
        
        // PENDING = RED, COMPLETED = GREEN
        const colorPending = '#ef4444'; 
        const colorCompleted = '#10b981'; 

        const taskChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Pending', 'Completed'],
                datasets: [{
                    data: [<?php echo $pending_tasks; ?>, <?php echo $completed_tasks; ?>],
                    backgroundColor: [colorPending, colorCompleted],
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                cutout: '75%', 
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            font: {
                                family: "'Segoe UI', sans-serif",
                                size: 14,
                                weight: '600'
                            }
                        }
                    }
                }
            }
        });
    </script>

</body>
</html>