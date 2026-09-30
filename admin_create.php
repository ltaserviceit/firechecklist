<?php
session_start();

// 1. Security Check: Kick them out if they aren't the admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

// 2. Connect to the Database
require 'db.php';

$feedback_message = "";

// 3. Process the form when the Admin clicks "Create Task"
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize the inputs to prevent SQL injection
    $company_name = $conn->real_escape_string($_POST['company_name']);
    $building_name = $conn->real_escape_string($_POST['building_name']);

    // Insert the new task into the 'tasks' hub table
    $sql = "INSERT INTO tasks (company_name, building_name, status) VALUES ('$company_name', '$building_name', 'Pending')";

    if ($conn->query($sql) === TRUE) {
        $feedback_message = '
        <div class="alert alert-success">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
            Task successfully created for <strong>' . htmlspecialchars($company_name) . '</strong>!
        </div>';
    } else {
        $feedback_message = '
        <div class="alert alert-danger">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            Error: ' . htmlspecialchars($conn->error) . '
        </div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Create Task - FireSafe Admin</title>
    <style>
        :root {
            --primary: #C41E3A;
            --primary-hover: #a8182f;
            --bg-color: #f7f7f8;
            --surface: #ffffff;
            --text-main: #1a1a1a;
            --text-muted: #8a8f98;
            --border: #e6e7eb;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: var(--bg-color); 
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
        }

        .brand-icon svg { width: 18px; height: 18px; stroke: #fff; }

        .brand-name { font-size: 15px; font-weight: 700; color: var(--text-main); line-height: 1.2; }
        .brand-name span { display: block; font-size: 11px; font-weight: 400; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }

        .nav-links { padding: 24px 12px; display: flex; flex-direction: column; gap: 8px; }

        .sidebar a { 
            display: flex; align-items: center; gap: 10px; color: var(--text-main); 
            padding: 12px 16px; text-decoration: none; font-weight: 500; font-size: 14px;
            border-radius: 8px; transition: background 0.2s, color 0.2s; 
        }
        
        .sidebar a:hover { background-color: var(--bg-color); }
        .sidebar a.active { background-color: rgba(196, 30, 58, 0.08); color: var(--primary); font-weight: 600; }
        
        .logout { margin-top: auto; margin-bottom: 24px; margin-left: 12px; margin-right: 12px; color: #b3273f !important; }
        .logout:hover { background-color: #fdecee !important; }

        /* ============ MAIN CONTENT ============ */
        .main-content { 
            margin-left: 260px; 
            padding: clamp(30px, 5vw, 60px); 
            width: calc(100% - 260px); 
            transition: all 0.3s ease; 
            display: flex; 
            flex-direction: column; 
            align-items: center; 
        }

        .header-section {
            text-align: center;
            margin-bottom: 30px;
            width: 100%;
            max-width: 500px;
        }
        
        h1 { 
            font-size: clamp(24px, 4vw, 28px); 
            font-weight: 700;
            letter-spacing: -0.3px;
            margin-bottom: 8px;
            color: var(--text-main);
        }

        .subtitle {
            color: var(--text-muted);
            font-size: 14px;
            line-height: 1.6;
        }

        /* ============ FORM CONTAINER ============ */
        .form-container { 
            background: var(--surface); 
            padding: clamp(24px, 5vw, 40px); 
            border-radius: 12px; 
            border: 1.5px solid var(--border);
            box-shadow: 0 4px 20px rgba(0,0,0,0.03);
            width: 100%; 
            max-width: 500px; 
        }
        
        .input-group { margin-bottom: 20px; }
        
        .input-group label { 
            display: block; 
            margin-bottom: 8px; 
            font-weight: 600; 
            color: var(--text-main); 
            font-size: 13px; 
            letter-spacing: 0.2px;
        }
        
        .input-group input { 
            width: 100%; 
            padding: 13px 14px; 
            border: 1.5px solid var(--border); 
            border-radius: 8px; 
            font-size: 14px; 
            background: #fafafa;
            transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
            -webkit-appearance: none;
            color: var(--text-main);
        }
        
        .input-group input::placeholder { color: #b0b4bb; }
        
        .input-group input:focus { 
            outline: none; 
            border-color: var(--primary); 
            background: var(--surface);
            box-shadow: 0 0 0 4px rgba(196, 30, 58, 0.08); 
        }
        
        button[type="submit"] { 
            background-color: var(--primary); 
            color: white; 
            border: none; 
            padding: 14px; 
            border-radius: 8px; 
            font-size: 15px; 
            font-weight: 700; 
            letter-spacing: 0.3px;
            cursor: pointer; 
            width: 100%; 
            margin-top: 10px;
            transition: background-color 0.2s, transform 0.1s, box-shadow 0.2s; 
            -webkit-appearance: none;
            box-shadow: 0 4px 14px rgba(196, 30, 58, 0.25);
        }
        
        button[type="submit"]:hover { background-color: var(--primary-hover); }
        button[type="submit"]:active { transform: scale(0.98); }

        /* ============ ALERTS ============ */
        .alert { 
            padding: 14px 16px; 
            border-radius: 8px; 
            margin-bottom: 24px; 
            font-size: 14px; 
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            line-height: 1.4;
        }
        .alert strong { font-weight: 700; }
        .alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .alert-danger { background: #fdecee; color: #b3273f; border: 1px solid #f6c9d0; }

        /* ============ RESPONSIVE ============ */
        @media (max-width: 860px) {
            body { flex-direction: column; }
            .sidebar { 
                position: relative; width: 100%; height: auto; 
                border-right: none; border-bottom: 1.5px solid var(--border); 
            }
            .brand { justify-content: center; border-bottom: none; padding: 20px; }
            .nav-links { flex-direction: row; flex-wrap: wrap; justify-content: center; padding: 0 20px 20px; }
            .logout { margin: 0; }
            .main-content { margin-left: 0; width: 100%; padding: 30px 20px; }
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
            <a href="admin_create.php" class="active">
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
            <h1>Create Maintenance Task</h1>
            <p class="subtitle">Assign a new fire system inspection and checklist generation for a specific location.</p>
        </div>

        <div class="form-container">
            <?php echo $feedback_message; ?>

            <form method="POST" action="">
                <div class="input-group">
                    <label for="company_name">Company Name</label>
                    <input type="text" id="company_name" name="company_name" placeholder="e.g., Koperasi Mamacare Sarawak Berhad" required autocomplete="off">
                </div>
                
                <div class="input-group">
                    <label for="building_name">Building Name / Location</label>
                    <input type="text" id="building_name" name="building_name" placeholder="e.g., HQ Block A" required autocomplete="off">
                </div>

                <button type="submit">Create Task</button>
            </form>
        </div>

    </div>

</body>
</html>