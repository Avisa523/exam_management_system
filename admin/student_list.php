<?php
session_start();
include('../includes/config.php');

// Only allow admin access
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../authentication/login.php");
    exit;
}

// Fetch all students
$result = $conn->query("SELECT * FROM students ORDER BY id ASC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Students List</title>
<style>
body { font-family: Arial; background: #f4f6f8; padding: 30px; }
table { width: 100%; border-collapse: collapse; background: white; }
th, td { padding: 10px; border: 1px solid #ddd; text-align: center; }
th { background: #2c3e50; color: white; }
a.view-btn { padding: 6px 12px; background: #2980b9; color: white; text-decoration: none; border-radius: 4px; }
a.view-btn:hover { background: #1c5980; }
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}
.dashboard-btn {
    padding: 6px 12px;
    background: #27ae60;
    color: white;
    text-decoration: none;
    border-radius: 4px;
}
.dashboard-btn:hover {
    background: #1e8449;
}
</style>
</head>
<body>

<div class="header">
    <h2>Students List</h2>
    <a href="dashboard.php" class="dashboard-btn">🏠</a>
</div>

<table>
<tr>
    <th>ID</th>
    <th>Full Name</th>
    <th>Class</th>
    <th>Action</th>
</tr>

<?php while($student = $result->fetch_assoc()): ?>
<tr>
    <td><?php echo htmlspecialchars($student['id']); ?></td>
    <td><?php echo htmlspecialchars($student['full_name']); ?></td>
    <td><?php echo htmlspecialchars($student['course']); ?></td>
    <td><a class="view-btn" href="student_report.php?student_id=<?php echo $student['id']; ?>">View Report</a></td>
</tr>
<?php endwhile; ?>

</table>

</body>
</html>