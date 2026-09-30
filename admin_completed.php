<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit();
}

require 'db.php';

$completed_tasks = $conn->query("SELECT * FROM tasks WHERE status = 'Completed' ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Completed Tasks - Admin</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f7f6; margin: 0; display: flex; }
        .sidebar { width: 250px; background-color: #333; color: white; height: 100vh; padding-top: 20px; position: fixed; }
        .sidebar h2 { text-align: center; color: #fca5a5; margin-bottom: 30px; }
        .sidebar a { display: block; color: white; padding: 15px 20px; text-decoration: none; font-weight: bold; border-bottom: 1px solid #444; }
        .sidebar a:hover { background-color: #fca5a5; color: black; }
        .sidebar a.active { background-color: #fca5a5; color: black; }
        .logout { background-color: #d9534f; text-align: center; }
        .logout:hover { background-color: #c9302c !important; color: white !important; }
        .main-content { margin-left: 250px; padding: 30px; width: calc(100% - 250px); }
        h1 { color: #333; }
        .table-container { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border-bottom: 1px solid #ddd; padding: 12px; text-align: left; }
        th { background-color: #fca5a5; color: black; }
        tr:hover { background-color: #f9f9f9; }
        .badge-completed { background-color: #5cb85c; color: white; padding: 5px 10px; border-radius: 4px; font-size: 12px; }
        .btn-delete { background: #d9534f; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: bold; }
        .btn-delete:hover { background: #c9302c; }
        .btn-view { background: #0275d8; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; font-size: 12px; font-weight: bold; text-decoration: none; display: inline-block; }
        .btn-view:hover { background: #025aa5; }
        .empty-state { text-align: center; padding: 50px; color: #777; }
        .feedback-success { background:#d4edda; color:#155724; padding:12px 16px; border-radius:4px; margin-bottom:20px; border:1px solid #c3e6cb; }
        .feedback-error { background:#f8d7da; color:#721c24; padding:12px 16px; border-radius:4px; margin-bottom:20px; border:1px solid #f5c6cb; }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="admin_dashboard.php">Dashboard</a>
    <a href="admin_create.php">Create New Task</a>
    <a href="admin_completed.php" class="active">Completed Tasks</a>
    <a href="logout.php" class="logout" style="position: absolute; bottom: 20px; width: 100%; box-sizing: border-box;">Logout</a>
</div>

<div class="main-content">
    <h1>Completed Tasks</h1>

    <?php if(isset($_GET['deleted'])): ?>
        <div class="feedback-success">&#10003; Task deleted successfully.</div>
    <?php endif; ?>
    <?php if(isset($_GET['error'])): ?>
        <div class="feedback-error">&#10007; Error deleting task. Please try again.</div>
    <?php endif; ?>

    <div class="table-container">
        <h3 style="margin-top:0;">All Completed Tasks (<?php echo $completed_tasks->num_rows; ?>)</h3>
        <table>
            <tr>
                <th width="5%">ID</th>
                <th width="25%">Company Name</th>
                <th width="25%">Building Name</th>
                <th width="15%">Date Created</th>
                <th width="10%">Status</th>
                <th width="20%">Action</th>
            </tr>
            <?php if ($completed_tasks->num_rows > 0): ?>
                <?php while($row = $completed_tasks->fetch_assoc()): ?>
                <tr>
                    <td>#<?php echo $row['task_id']; ?></td>
                    <td><?php echo htmlspecialchars($row['company_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['building_name']); ?></td>
                    <td><?php echo date('d M Y', strtotime($row['created_at'])); ?></td>
                    <td><span class="badge-completed">Completed</span></td>
                    <td style="display:flex; gap:6px;">
                        <a href="admin_view_task.php?task_id=<?php echo $row['task_id']; ?>" class="btn-view">&#128065; View</a>
                        <form action="delete_task.php" method="POST" onsubmit="return confirm('Delete this task? This cannot be undone.');" style="margin:0;">
                            <input type="hidden" name="task_id" value="<?php echo $row['task_id']; ?>">
                            <input type="hidden" name="redirect" value="admin_completed.php">
                            <button type="submit" class="btn-delete">&#128465; Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="6" class="empty-state">No completed tasks yet.</td></tr>
            <?php endif; ?>
        </table>
    </div>
</div>

</body>
</html>