<?php
session_start();

// Include database connection
include("../includes/config.php"); // adjust path if needed

// Check if logged in as student
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'student'){
    header("Location: ../authentication/login.php");
    exit;
}

// Check registration complete
$stmtReg = $conn->prepare("SELECT completed_registration FROM student_profiles WHERE user_id=?");
$stmtReg->bind_param("i", $_SESSION['id']);
$stmtReg->execute();
$regRow = $stmtReg->get_result()->fetch_assoc();
$stmtReg->close();

if($regRow['completed_registration'] == 0){
    header("Location: register.php");
    exit;
 // ✅ Now the dashboard page content
echo "<h1>Welcome, ".$_SESSION['username']."</h1>";   
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

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
                <li><a href="../authentication/logout.php">
                    <i class="fas fa-sign-out-alt"></i> 🔒 Logout</a></li>
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