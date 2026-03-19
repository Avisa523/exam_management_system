<?php
session_start();
if($_SESSION['role'] != 'teacher') header('Location: ../index.php');
include('../includes/config.php');

$subject_id = $_SESSION['subject_id'];
$subject_name = $conn->query("SELECT subject_name FROM subjects WHERE id='$subject_id'")->fetch_assoc()['subject_name'];

$results = $conn->query("SELECT s.full_name, er.marks, er.approved FROM exam_results er 
                         JOIN students s ON er.student_id = s.id
                         WHERE er.subject_id='$subject_id'
                         ORDER BY s.full_name");
?>

<h2>Result Report for Subject: <?php echo $subject_name; ?></h2>
<table border="1">
<tr>
    <th>Student</th>
    <th>Marks</th>
    <th>Approved</th>
</tr>
<?php while($r = $results->fetch_assoc()): ?>
<tr>
    <td><?php echo $r['full_name']; ?></td>
    <td><?php echo $r['marks']; ?></td>
    <td><?php echo $r['approved'] ? 'Yes' : 'No'; ?></td>
</tr>
<?php endwhile; ?>
</table>