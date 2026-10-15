<?php
session_start();
require 'db.php';
require 'report_info.php';

// Admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['task_id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$task_id = intval($_POST['task_id']);
$action  = $_POST['action'] ?? 'save';

if ($action === 'reset') {
    // Remove all edits -> report goes back to the original task data
    $stmt = $conn->prepare("UPDATE tasks SET report_info = NULL WHERE task_id = ?");
    $stmt->bind_param("i", $task_id);
    $stmt->execute();
    header("Location: view_report.php?task_id=$task_id&reset=1");
    exit();
}

// Whitelist: only the report-information fields can be saved here.
// Checklist tables and signature columns are never touched.
$data = [];
foreach (array_keys(report_info_fields()) as $key) {
    $value = trim($_POST['info'][$key] ?? '');
    $data[$key] = mb_substr($value, 0, 150);
}
$json = json_encode($data, JSON_UNESCAPED_UNICODE);

$stmt = $conn->prepare("UPDATE tasks SET report_info = ? WHERE task_id = ?");
$stmt->bind_param("si", $json, $task_id);
$stmt->execute();

if ($action === 'download') {
    header("Location: export_pdf.php?task_id=$task_id");
} else {
    header("Location: view_report.php?task_id=$task_id&saved=1");
}
exit();
