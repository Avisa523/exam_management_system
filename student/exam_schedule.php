<?php
session_start();
include('../includes/config.php');
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'student'){
    header("Location: ../authentication/login.php");
    exit;
}

// 2. Check if registration is completed
$user_id = $_SESSION['id'];
$result = $conn->query("SELECT completed_registration FROM student_profiles WHERE user_id='$user_id'");

if($result && $row = $result->fetch_assoc()){
    if($row['completed_registration'] == 0){
        // Not registered → force registration
        header("Location: registration.php");
        exit;
    }
} else {
    // No profile found → force registration
    header("Location: registration.php");
    exit;
}



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
<head><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Schedule</title>
    <link rel="stylesheet" href="../assets/css/student.css">
</head>
<body>

<!-- Blue Header -->
<div class="exam-header-bar">
    <h1>Exam Schedule</h1>
    <a href="dashboard.php" >🏠</a>
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
