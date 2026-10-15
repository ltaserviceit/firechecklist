<?php
session_start();
require 'db.php';

// Admin only
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['task_id'], $_POST['table'])) {
    header("Location: admin_dashboard.php");
    exit();
}

// Only these tables can be edited
$allowed_tables = [
    'fire_alarm_system_add', 'fire_alarm_system_con', 'fireman_intercom_system',
    'wet_chemical_system', 'fire_hose_reel_system', 'fire_sprinkler_system',
    'fire_suppression_system_1', 'fire_suppression_system_2', 'fire_suppression_system_3',
    'fire_suppression_system_4', 'wet_riser_system', 'dry_riser_system',
    'pressurised_hydrant_system'
];

$table = $_POST['table'];
if (!in_array($table, $allowed_tables, true)) {
    die("Invalid table.");
}

$task_id = intval($_POST['task_id']);
$anchor  = preg_replace('/[^A-Za-z0-9_-]/', '', $_POST['anchor'] ?? '');

// Only these columns can be edited, and only if they exist in this table
$existing_cols = [];
$res = $conn->query("SHOW COLUMNS FROM `$table`");
while ($c = $res->fetch_assoc()) {
    $existing_cols[] = $c['Field'];
}
$editable = array_intersect(
    ['description', 'panel_type', 'panel_brand', 'panel_model', 'panel_qty',
     'location_floor', 'zone_loop', 'checklist', 'item_condition', 'remarks'],
    $existing_cols
);

foreach (($_POST['rows'] ?? []) as $row_id => $fields) {
    $row_id = intval($row_id);
    if (!is_array($fields)) continue;

    $sets = [];
    $vals = [];

    foreach ($editable as $col) {
        if (!isset($fields[$col])) continue;
        $v = mb_substr(trim($fields[$col]), 0, 500);

        // Keep the uploaded photo tag on remarks - it is not editable here
        if ($col === 'remarks') {
            $q = $conn->prepare("SELECT remarks FROM `$table` WHERE id = ? AND task_id = ?");
            $q->bind_param("ii", $row_id, $task_id);
            $q->execute();
            $old = $q->get_result()->fetch_assoc()['remarks'] ?? '';
            if (preg_match('/\[IMG: .*?\]/', (string)$old, $m)) {
                $v = trim($v . ' ' . $m[0]);
            }
        }

        $sets[] = "`$col` = ?";
        $vals[] = $v;
    }

    if (!$sets) continue;

    $stmt = $conn->prepare("UPDATE `$table` SET " . implode(', ', $sets) . " WHERE id = ? AND task_id = ?");
    $types  = str_repeat('s', count($vals)) . 'ii';
    $params = array_merge($vals, [$row_id, $task_id]);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
}

header("Location: view_report.php?task_id=$task_id&section_saved=1" . ($anchor ? "#$anchor" : ''));
exit();
