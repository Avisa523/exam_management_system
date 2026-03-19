<?php
session_start();
include('../includes/config.php');

// Only teachers can access
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'teacher'){
    header('Location: ../authentication/login.php');
    exit;
}

// Get teacher's subject
$teacher_id = $_SESSION['id'];
$teacher = $conn->query("SELECT subject_id FROM teachers WHERE id='$teacher_id'")->fetch_assoc();
$subject_id = $teacher['subject_id'];

// Get subject name
$subject = $conn->query("SELECT subject_name FROM subjects WHERE id='$subject_id'")->fetch_assoc()['subject_name'];

// Fetch students for this subject
$students = $conn->query("
    SELECT st.id, st.full_name, st.contact_no, st.semester, s.subject_name, er.marks
    FROM students st
    LEFT JOIN exam_results er ON st.id = er.student_id AND er.subject_id = '$subject_id'
    CROSS JOIN subjects s
    WHERE s.subject_id = '$subject_id'
    ORDER BY st.full_name
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student List</title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}
table th, table td {
    border: 1px solid #ccc;
    padding: 10px;
    text-align: left;
}
table th {
    background-color: #f4f4f4;
}
.edit-btn {
    background: #0d6efd;
    color: #fff;
    padding: 5px 10px;
    text-decoration: none;
    border-radius: 3px;
}
.edit-btn:hover {
    background: #0b5ed7;
}
</style>
</head>

<header>
    <h1>Student List</h1>

<a href="dashboard.php" class="home-icon">🏠</a>

</header>
<body>

<h2>Student List - <?php echo htmlspecialchars($subject); ?></h2>

<table>
    <tr>
        <th>#</th>
        <th>Full Name</th>
        <th>Contact</th>
        <th>Semester</th>
        <th>Subject</th>
    
    </tr>
    <?php
    $counter = 1;
    while($s = $students->fetch_assoc()):
    ?>
    <tr>
        <td><?php echo $counter++; ?></td>
        <td><?php echo htmlspecialchars($s['full_name']); ?></td>
        <td><?php echo htmlspecialchars($s['contact_no']); ?></td>
        <td><?php echo htmlspecialchars($s['semester']); ?></td>
        <td><?php echo htmlspecialchars($s['subject_name']); ?></td>
        
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>