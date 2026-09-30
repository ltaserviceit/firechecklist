<?php
    session_start();
    require 'db.php';
    date_default_timezone_set('Asia/Kuching');

    // Security Check
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'technician') {
        header("Location: index.php");
        exit();
    }

    // Ensure the form was submitted with the Signature inputs
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['task_id'])) {

        $task_id = intval($_POST['task_id']);

        // Capture Names and Signatures
        $tech_name = $conn->real_escape_string($_POST['tech_sign_name']);
        $client_name = $conn->real_escape_string($_POST['client_sign_name']);
        $tech_sig = $conn->real_escape_string($_POST['tech_signature']);
        $client_sig = $conn->real_escape_string($_POST['client_signature']);

        // Update task as completed
        $query = "UPDATE tasks 
                SET status = 'Completed',
                    inspection_date = NOW(),
                    tech_sign_name = '$tech_name',
                    client_sign_name = '$client_name',
                    tech_signature = '$tech_sig',
                    client_signature = '$client_sig'
                WHERE task_id = $task_id 
                AND status = 'Pending'";
        if ($conn->query($query)) {
            // Success - return to technician dashboard
            header("Location: tech_dashboard.php?completed=success");
        } else {
            // Database Error
            header("Location: tech_dashboard.php?error=failed");
        }
    } else {
        // Direct access without submitting the form
        header("Location: tech_dashboard.php");
    }
    exit();
?>