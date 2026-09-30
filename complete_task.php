<?php
session_start();
require 'db.php';

// Security Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'technician') {
    header("Location: index.php");
    exit();
}

// Ensure the form was submitted with the new Signature inputs
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['task_id'])) {
    
    $task_id = intval($_POST['task_id']);
    
    // Capture Names and Signatures
    $tech_name = $conn->real_escape_string($_POST['tech_sign_name']);
    $client_name = $conn->real_escape_string($_POST['client_sign_name']);
    $tech_sig = $conn->real_escape_string($_POST['tech_signature']);
    $client_sig = $conn->real_escape_string($_POST['client_signature']);
    
    // Update the task status to Completed AND attach the signature data
    $query = "UPDATE tasks 
              SET status = 'Completed', 
                  tech_sign_name = '$tech_name', 
                  client_sign_name = '$client_name', 
                  tech_signature = '$tech_sig', 
                  client_signature = '$client_sig' 
              WHERE task_id = $task_id AND status = 'Pending'";
    
    if ($conn->query($query)) {
        // Success! Send them back to the main tech dashboard
        header("Location: tech_dashboard.php?completed=success");
    } else {
        // Database Error
        header("Location: tech_dashboard.php?error=failed");
    }

} else {
    // If they tried to access this file directly without clicking the button
    header("Location: tech_dashboard.php");
}
exit();
?>