<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'student'){
    header("Location: ../authentication/login.php");
    exit;
}

include('../includes/config.php');

// Fetch exams with start time, end time, and room
$exams = $conn->query("
    SELECT es.exam_date, es.exam_time, es.end_time, es.room, s.subject_name
    FROM exam_schedule es
    JOIN subjects s ON es.subject_id = s.id
    ORDER BY es.exam_date ASC, es.exam_time ASC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Exam Schedule</title>
    <link rel="stylesheet" href="../assets/css/style.css">
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
            <th>Room No</th>
        </tr>
    </thead>
    <tbody>

    <?php if($exams && $exams->num_rows > 0): ?>
        <?php while($row = $exams->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
                <td><?php echo htmlspecialchars($row['exam_date']); ?></td>
                <td>
                    <?php 
                        echo date("g:i A", strtotime($row['exam_time'])) . 
                             " - " . 
                             date("g:i A", strtotime($row['end_time']));
                    ?>
                </td>
                <td><?php echo htmlspecialchars($row['room']); ?></td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="4" style="text-align:center;">No exams scheduled yet.</td>
        </tr>
    <?php endif; ?>

    </tbody>
</table>