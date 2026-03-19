<?php
session_start();
include('../includes/config.php');

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'teacher'){
    header('Location: ../authentication/login.php');
    exit;
}

$teacher_id = $_SESSION['id'];

$teacher = $conn->query("SELECT subject_id FROM teachers WHERE id='$teacher_id'")->fetch_assoc();
$subject_id = $teacher['subject_id'];

$results = $conn->query("
SELECT er.id, st.full_name, s.subject_name, er.marks, er.grade, er.result_date
FROM exam_results er
JOIN students st ON er.student_id = st.id
JOIN subjects s ON er.subject_id = s.id
WHERE er.subject_id='$subject_id'
ORDER BY st.full_name
");
?>

<header>


<a href="dashboard.php" class="home-icon">🏠</a>

</header>

<h2>Exam Result Sheet</h2>
<table border="1" cellpadding="6">
    <tr>
        <th>Student</th>
        <th>Subject</th>
        <th>Marks</th>
        <th>Grade</th>
        <th>Date</th>
    </tr>
    <?php while($row = $results->fetch_assoc()): ?>
    <tr>
        <td><?php echo htmlspecialchars($row['full_name']); ?></td>
        <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
        <td><?php echo htmlspecialchars($row['marks']); ?></td>
        <td><?php echo htmlspecialchars($row['grade']); ?></td>
        <td><?php echo htmlspecialchars($row['result_date']); ?></td>
    </tr>
    <?php endwhile; ?>
</table>