<?php
session_start();
if($_SESSION['role'] != 'admin'){
    header("Location: ../index.php");
    exit;
}
?>

<h1>Admin Homepage</h1>
<p>Welcome, Admin 👑</p>

<ul>
    <li><a href="manage_students.php">Manage Students</a></li>
    <li><a href="manage_teachers.php">Manage Teachers</a></li>
    <li><a href="question_papers.php">Question Papers</a></li>
    <li><a href="exam_schedule.php">Exam Schedule</a></li>
    <li><a href="exam_results.php">Exam Results</a></li>
    <li><a href="student_report.php">Student Report</a></li>
    <li><a href="noticeboard.php">Noticeboard</a></li>
    <li><a href="profile.php">My Profile</a></li>
    <li><a href="logout.php">Logout</a></li>
</ul>