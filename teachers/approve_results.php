<?php
session_start();
include('../includes/config.php');

// Only teachers
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'teacher'){
    header("Location: ../authentication/login.php");
    exit;
}

$teacher_id = $_SESSION['id'];
$subject_id = $conn->query("SELECT subject_id FROM teachers WHERE id='$teacher_id'")->fetch_assoc()['subject_id'];

// Handle form submission to update marks and grade
if(isset($_POST['update'])){
    $result_id = intval($_POST['result_id']);
    $marks = floatval($_POST['marks']);
    $total_marks = 100;
    $percentage = ($marks / $total_marks) * 100;

    // Calculate grade
    if ($percentage >= 80) $grade = 'A';
    elseif ($percentage >= 60) $grade = 'B';
    elseif ($percentage >= 50) $grade = 'C';
    elseif ($percentage >= 40) $grade = 'D';
    else $grade = 'F';

    $conn->query("UPDATE exam_results SET marks='$marks', grade='$grade', approved=1 WHERE id='$result_id'");
    header("Location: approve_results.php");
    exit;
}

// Fetch all results for this teacher's subject
$results = $conn->query("
    SELECT er.id, s.full_name, er.marks, er.grade, er.approved
    FROM exam_results er
    JOIN students s ON er.student_id = s.id
    WHERE er.subject_id='$subject_id'
    ORDER BY s.full_name
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Approve / Edit Results</title>
    <link rel="stylesheet" href="../assets/css/teachers.css">
</head>
<body>
    <header>
    

<a href="dashboard.php">🏠</a>

</header>

<h2>Approve / Edit Results</h2>

<table>
    <tr>
        <th>Student</th>
        <th>Marks</th>
        <th>Grade</th>
        <th>Status</th>
        <th>Action</th>
    </tr>
    <?php while($r = $results->fetch_assoc()): ?>
    <tr>
        <form method="POST">
            <td><?php echo htmlspecialchars($r['full_name']); ?></td>
            <td>
                <input type="number" name="marks" value="<?php echo htmlspecialchars($r['marks']); ?>" min="0" max="100" required>
            </td>
            <td>
                <?php echo htmlspecialchars($r['grade']); ?>
            </td>
            <td><?php echo $r['approved'] ? 'Approved' : 'Pending'; ?></td>
            <td>
                <input type="hidden" name="result_id" value="<?php echo $r['id']; ?>">
                <button type="submit" name="update">Save & Approve</button>
            </td>
        </form>
    </tr>
    <?php endwhile; ?>
</table>

</body>
</html>