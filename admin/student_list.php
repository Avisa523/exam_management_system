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
<link rel="stylesheet" href="../assets/css/admin.css">
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