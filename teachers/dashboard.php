<?php
session_start();
include('../includes/config.php');

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'teacher'){
    header("Location: ../authentication/login.php");
    exit;
}

// Get teacher info
$teacher_id = $_SESSION['id'];
$teacher = $conn->query("SELECT subject_id FROM teachers WHERE id='$teacher_id'")->fetch_assoc();
$subject_id = $teacher['subject_id'];

// Fetch latest notices for dashboard
$notices = $conn->query("SELECT * FROM noticeboard ");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Teacher Dashboard</title>
<link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

<!-- Header -->
<div class="topbar">
    <div class="title">Exam Management System</div>
    <div class="user">Welcome <?php echo $_SESSION['username']; ?></div>
</div>

<div class="container-layout">

    <!-- Left Column -->
    <div class="left-column">
        <div class="logo-box">
            <img src="../assets/images/everest logo.png" alt="Logo">
        </div>

        <div class="sidebar">
            <ul>
                <li><a href="dashboard.php">🏠 Dashboard</a></li>
                <li><a href="question_papers.php">📝 Question Papers</a></li>
                <li><a href="exam_schedule.php">📅 Exam Schedule</a></li>
                <li><a href="notice.php">📢 Notice</a></li>
                <li><a href="../authentication/logout.php">
                    <i class="fas fa-sign-out-alt"></i> 🔒 Logout</a></li>
                    
            </ul>
        </div>
    </div>

    <!-- Right Column -->
    <div class="right-column">
        <div class="dashboard-box">
            <div class="header">
                <h2>Dashboard</h2>
                <span>Welcome <?php echo $_SESSION['username']; ?></span>
            </div>
            <div class="cards">
                <div class="card blue">
                    <h3>Manage Questions</h3>
                    <p>Add/Edit/Delete question papers</p>
                    <a href="question_manage.php">Open</a>
                </div>
                <div class="card green">
                    <h3>Exam Schedule</h3>
                    <p>View exam schedule</p>
                    <a href="exam_schedule.php">Open</a>
                </div>
                <div class="card orange">
                    <h3>Approve Results</h3>
                    <p>Approve student results</p>
                    <a href="approve_results.php">Open</a>
                </div>
                <div class="card red">
                    <h3>Student List</h3>
                    <p>View students and results</p>
                    <a href="student_list.php">Open</a>
                </div>
                
            </div>


        </div>
    </div>

</div>

</body>
</html>