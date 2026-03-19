<?php
session_start();
include("../includes/config.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'student'){
    header("Location: ../authentication/login.php");
    exit;
}

$user_id = $_SESSION['id'];
$check = $conn->query("SELECT completed_registration FROM student_profiles WHERE user_id='$user_id'");
$row = $check->fetch_assoc();

if(!$row || $row['completed_registration'] == 1){
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>

<!-- HEADER -->
<div class="topbar">
    <div class="title">Exam Management System</div>
    <div class="user">Welcome <?php echo $_SESSION['username'] ?? 'User'; ?></div>
</div>

<!-- MAIN LAYOUT -->
<div class="container-layout">

    <!-- LEFT COLUMN: Logo + Sidebar -->
    <div class="left-column">

        <!-- LOGO -->
        <div class="logo">
            <img src="../assets/images/everest logo.png" alt="College Logo">
        </div>

        <!-- SIDEBAR -->
        <div class="sidebar">
            <ul>
                <li><a href="dashboard.php">🏠 Dashboard</a></li>
                <li><a href="exam_schedule.php">📅 Exam Schedule</a></li>
                <li><a href="notices.php">📢 Notices</a></li>
                <li><a href="result.php">📊 Result</a></li>
                <li><a href="profile.php">👤 Profile</a></li>
                <li><a href="../authentication/logout.php">🚪 Logout</a></li>
            </ul>
        </div>

    </div>

    <!-- RIGHT COLUMN: Dashboard Box -->
    <div class="right-column">
        <div class="dashboard-box">

            <div class="header">
                <h2>Dashboard</h2>
                <span>Welcome <?php echo $_SESSION['name'] ?? 'Student'; ?></span>
            </div>

            <div class="cards">
                <div class="card blue">
                    <h3>Exam Schedule</h3>
                    <p>View Exams</p>
                    <a href="exam_schedule.php">Open</a>
                </div>
                <div class="card green">
                    <h3>Notices</h3>
                    <p>College Notices</p>
                    <a href="notices.php">Open</a>
                </div>
                <div class="card orange">
                    <h3>Results</h3>
                    <p>Your Results</p>
                    <a href="result.php">Open</a>
                </div>
                <div class="card red">
                    <h3>Profile</h3>
                    <p>Your Information</p>
                    <a href="profile.php">Open</a>
                </div>
            </div>

        </div>
    </div>

</div>

</body>
</html>