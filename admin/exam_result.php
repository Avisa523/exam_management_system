<?php
session_start();
include('../includes/config.php');

if(!isset($_SESSION['role']) || $_SESSION['role']!='admin'){
header("Location: ../authentication/login.php");
exit;
}

/* FETCH RESULTS */

$results = $conn->query("
SELECT r.*, st.full_name, sub.subject_name
FROM exam_results r
LEFT JOIN students st ON r.student_id = st.id
LEFT JOIN subjects sub ON r.subject_id = sub.id
ORDER BY r.id DESC
");

?>

<!DOCTYPE html>
<html>
<head>

<title>Admin Exam Results</title>

<style>

body{
font-family:Arial;
background:#f4f6f8;
padding:30px;
}

table{
width:100%;
border-collapse:collapse;
background:white;
}

th,td{
padding:10px;
border:1px solid #ddd;
text-align:center;
}

th{
background:#2c3e50;
color:white;
}

.view-btn{
background:#3498db;
color:white;
padding:6px 10px;
border-radius:4px;
text-decoration:none;
}

</style>

</head>

<body>

<h2>Exam Results</h2>

<table>

<tr>
<th>ID</th>
<th>Student</th>
<th>Subject</th>
<th>Marks</th>
<th>Grade</th>
<th>Status</th>
<th>Report</th>
</tr>

<?php while($row=$results->fetch_assoc()): ?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo $row['full_name']; ?></td>

<td><?php echo $row['subject_name']; ?></td>

<td><?php echo $row['marks']; ?></td>

<td><?php echo $row['grade']; ?></td>

<td>

<?php
echo ($row['approved']==1) ? "Approved" : "Pending";
?>

</td>

<td>

<a class="view-btn" href="student_report.php?student_id=<?php echo $row['student_id']; ?>">
View Report
</a>

</td>

</tr>

<?php endwhile; ?>

</table>

</body>
</html>