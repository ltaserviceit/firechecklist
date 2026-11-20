<?php
session_start();
require 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php"); exit();
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { die("Invalid request."); }

$allowed = [
    'fire_alarm_system_add','fire_alarm_system_con','fireman_intercom_system',
    'wet_chemical_system','fire_hose_reel_system','fire_sprinkler_system',
    'fire_suppression_system_1','fire_suppression_system_2','fire_suppression_system_3',
    'fire_suppression_system_4','wet_riser_system','dry_riser_system','pressurised_hydrant_system'
];

$task_id = intval($_POST['task_id'] ?? 0);
$table   = $_POST['table'] ?? '';
if ($task_id <= 0 || !in_array($table, $allowed, true)) { die("Invalid request."); }

$ids = array_values(array_filter(array_map('intval', explode(',', $_POST['ids'] ?? ''))));
if (empty($ids)) { die("No rows selected."); }

// Safety: never delete every row of this system for the task
$c = $conn->prepare("SELECT COUNT(*) FROM `$table` WHERE task_id = ?");
$c->bind_param("i", $task_id);
$c->execute();
$c->bind_result($total);
$c->fetch();
$c->close();
if (count($ids) >= $total) {
    die("Refusing to remove the only submission for this system. <a href='view_report.php?task_id=$task_id'>Back</a>");
}

$in   = implode(',', array_fill(0, count($ids), '?'));
$stmt = $conn->prepare("DELETE FROM `$table` WHERE task_id = ? AND id IN ($in)");
$params = array_merge([$task_id], $ids);
$stmt->bind_param(str_repeat('i', count($params)), ...$params);
$stmt->execute();

header("Location: view_report.php?task_id=$task_id&removed=" . $stmt->affected_rows);
exit();
