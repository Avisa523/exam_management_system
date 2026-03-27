<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'teacher'){
    header("Location: ../authentication/login.php");
    exit;
}

include('../includes/config.php');

// Fetch exams
$exams = $conn->query("
    SELECT es.exam_date, es.exam_time, s.subject_name
    FROM exam_schedule es
    JOIN subjects s ON es.subject_id = s.id
    ORDER BY es.exam_date ASC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Exam Schedule</title>
    <link rel="stylesheet" href="../assets/css/teachers.css">
</head>
<body>

<!-- Blue Header -->
<div class="exam-header-bar">
    <h1>Exam Schedule</h1>
    <a href="dashboard.php" class="home-icon">🏠</a>
</div><br><br>

<div class="main-content">

    <table class="exam-table">
        <thead>
            <tr>
                <th>Subject</th>
                <th>Date</th>
                <th>Time</th>
            </tr>
        </thead>
        <tbody>

        <?php if($exams && $exams->num_rows > 0): ?>
            <?php while($row = $exams->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['exam_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['exam_time']); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="3" style="text-align:center;">No exams scheduled yet.</td>
            </tr>
        <?php endif; ?>

        </tbody>
    </table>

</div>

<?php include('../includes/footer.php'); ?>

</body>
</html>