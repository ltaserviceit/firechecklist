<?php
// Start the session to keep the user logged in across pages
session_start();

// If the user is already logged in, send them straight to their dashboard
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin_dashboard.php");
        exit();
    } elseif ($_SESSION['role'] == 'technician') {
        header("Location: tech_dashboard.php");
        exit();
    }
}

$error_message = "";

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // 1. Check Admin Credentials
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['role'] = 'admin';
        header("Location: admin_dashboard.php");
        exit();
    } 
    // 2. Check Technician Credentials
    elseif ($username === 'technician' && $password === 'tech123') {
        $_SESSION['role'] = 'technician';
        header("Location: tech_dashboard.php");
        exit();
    } 
    // 3. Invalid Login
    else {
        $error_message = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=5.0">
    <title>Fire Alarm System Maintenance Checklist - Login</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            background: #f7f7f8;
            color: #1a1a1a;
        }

        /* ============ LEFT PANEL — BRAND / VISUAL ============ */
        .brand-panel {
            flex: 1.1;
            background: #C41E3A;
            background-image:
                radial-gradient(circle at 15% 20%, rgba(255,255,255,0.07) 0%, transparent 40%),
                radial-gradient(circle at 85% 80%, rgba(255,255,255,0.05) 0%, transparent 45%);
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: clamp(30px, 5vw, 70px);
            position: relative;
            overflow: hidden;
        }

        .brand-panel::before {
            content: "";
            position: absolute;
            top: -120px;
            right: -120px;
            width: 320px;
            height: 320px;
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 50%;
        }
        .brand-panel::after {
            content: "";
            position: absolute;
            bottom: -160px;
            left: -100px;
            width: 360px;
            height: 360px;
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 50%;
        }

        .brand-top {
            display: flex;
            align-items: center;
            gap: 14px;
            position: relative;
            z-index: 1;
        }

        .brand-icon {
            width: 46px;
            height: 46px;
            border-radius: 10px;
            background: rgba(255,255,255,0.15);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .brand-icon svg { width: 24px; height: 24px; }

        .brand-name {
            font-size: 17px;
            font-weight: 700;
            letter-spacing: 0.5px;
            line-height: 1.3;
        }
        .brand-name span {
            display: block;
            font-size: 12px;
            font-weight: 400;
            opacity: 0.75;
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 2px;
        }

        .brand-mid {
            position: relative;
            z-index: 1;
            max-width: 420px;
            margin-bottom: auto;
            margin-top: 20%;
        }

        .brand-mid h1 {
            font-size: clamp(28px, 4vw, 42px);
            font-weight: 700;
            line-height: 1.25;
            letter-spacing: -0.5px;
            margin-bottom: 18px;
        }

        .brand-mid p {
            font-size: 15px;
            line-height: 1.7;
            opacity: 0.85;
            font-weight: 300;
        }

        /* ============ RIGHT PANEL — LOGIN FORM ============ */
        .form-panel {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(20px, 5vw, 60px);
            background: #ffffff;
        }

        .login-box {
            width: 100%;
            max-width: 380px;
        }

        .login-box .eyebrow {
            color: #C41E3A;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .login-box h2 {
            font-size: clamp(22px, 4vw, 28px);
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 8px;
            letter-spacing: -0.3px;
        }

        .login-box p.subtitle {
            color: #8a8f98;
            font-size: 14px;
            margin-bottom: clamp(28px, 5vw, 38px);
            font-weight: 400;
        }

        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #3a3d44;
            font-size: 13px;
            letter-spacing: 0.2px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper svg {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            stroke: #b0b4bb;
            pointer-events: none;
            transition: stroke 0.2s;
        }

        .input-group input {
            width: 100%;
            padding: 13px 14px 13px 42px;
            border: 1.5px solid #e6e7eb;
            border-radius: 8px;
            font-size: 14px;
            background: #fafafa;
            transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
            -webkit-appearance: none;
            color: #1a1a1a;
        }

        .input-group input::placeholder { color: #b0b4bb; }

        .input-group input:focus {
            outline: none;
            border-color: #C41E3A;
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(196, 30, 58, 0.08);
        }

        .input-group input:focus + svg,
        .input-wrapper:focus-within svg {
            stroke: #C41E3A;
        }

        button {
            width: 100%;
            padding: 14px;
            background-color: #C41E3A;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 15px;
            font-weight: 700;
            letter-spacing: 0.3px;
            cursor: pointer;
            transition: background-color 0.2s, transform 0.1s, box-shadow 0.2s;
            margin-top: 8px;
            -webkit-appearance: none;
            box-shadow: 0 4px 14px rgba(196, 30, 58, 0.25);
        }

        button:hover { background-color: #a8182f; }
        button:active { transform: scale(0.98); }

        .error {
            background-color: #fdecee;
            color: #b3273f;
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
            border: 1px solid #f6c9d0;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .footer-note {
            margin-top: 28px;
            text-align: center;
            font-size: 12px;
            color: #b0b4bb;
            letter-spacing: 0.3px;
        }

        /* ============ RESPONSIVE ============ */
        @media (max-width: 860px) {
            body { flex-direction: column; }
            .brand-panel {
                flex: none;
                padding: 32px 24px;
                min-height: 220px;
                justify-content: flex-start;
                gap: 24px;
            }
            .brand-mid h1 { font-size: 24px; }
            .brand-mid p { display: none; }
            .form-panel { flex: none; padding: 32px 24px 50px; }
        }
    </style>
</head>
<body>

    <div class="brand-panel">
        <div class="brand-top">
            <div class="brand-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M8.5 14.5A2.5 2.5 0 0 0 11 12c0-1.38-.5-2-1-3-1.072-2.143-.224-4.054 2-6 .5 2.5 2 4.9 4 6.5 2 1.6 3 3.5 3 5.5a7 7 0 1 1-14 0c0-1.153.433-2.294 1-3a2.5 2.5 0 0 0 2.5 2.5z"/>
                </svg>
            </div>
            <div class="brand-name">
                FireSafe Systems
                <span>Maintenance Portal</span>
            </div>
        </div>

        <div class="brand-mid">
            <h1>Fire Alarm System<br>Maintenance Checklist</h1>
            <p>A unified platform for technicians and administrators to manage inspection schedules, complete digital checklists, and track compliance across every site.</p>
        </div>
    </div>

    <div class="form-panel">
        <div class="login-box">
            <div class="eyebrow">Welcome Back</div>
            <h2>Sign in to your account</h2>
            <p class="subtitle">Enter your credentials to access the system</p>

            <?php if(!empty($error_message)): ?>
                <div class="error">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="input-group">
                    <label for="username">Username</label>
                    <div class="input-wrapper">
                        <input type="text" id="username" name="username" placeholder="Enter your username" required autocomplete="off">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </div>
                </div>
                <button type="submit">Log In</button>
            </form>

            <p class="footer-note">© <?php echo date('Y'); ?> FireSafe Systems · Internal Use Only</p>
        </div>
    </div>

</body>
</html>