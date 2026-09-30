<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['task_id'])) {
    $task_id = intval($_POST['task_id']);

    // Deleting the task will also cascade delete all related system checklist data
    $sql = "DELETE FROM tasks WHERE task_id = $task_id";

    if ($conn->query($sql) === TRUE) {
        header("Location: admin_dashboard.php?deleted=1");
    } else {
        header("Location: admin_dashboard.php?error=1");
    }
    exit();
}

header("Location: admin_dashboard.php");
exit();
?>