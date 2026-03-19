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
<link rel="stylesheet" href="../assets/css/style.css">
<style>
/* ===== Header ===== */
.topbar {
    width: 100%;
    height: 60px;
    background: #2c3e50;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 25px;
    position: fixed;
    top: 0;
    z-index: 1000;
}
.topbar .title { font-size: 20px; font-weight: bold; }
.topbar .user { font-size: 14px; }

/* ===== Layout ===== */
body { background: #f5f6f8; padding-top: 80px; margin:0; font-family: Arial, sans-serif; }
.container-layout { 
    display: flex;
     gap: 20px; 
     padding: 20px; 

    }

/* ===== Left Column ===== */
.left-column { 
    display: flex; 
    flex-direction: column; 
    align-items: center; 
    gap: 20px; 
    width: 250px;
    flex-shrink: 0;
 }

/* Logo Box */
.logo-box {
    background: white;
    padding: 20px;
    border-radius: 10px;
    text-align: center;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
.logo-box img { width:120px; height:120px; object-fit:cover; border-radius:50%; margin-bottom:10px; }
.logo-box h3 { font-size:16px; color:#2c3e50; }

/* Sidebar */
.sidebar {
    background:#34495e;
    padding:15px;
    border-radius:10px;
    width:100%;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
.sidebar ul { list-style:none; padding:0; margin:0; }
.sidebar li { margin-bottom:10px; }
.sidebar li a {
    display:block;
    color:white;
    text-decoration:none;
    padding:10px 12px;
    border-radius:5px;
    transition:0.3s;
}
.sidebar li a:hover { background:#1abc9c; }

/* Right Column */
.right-column { 
    flex:1;
    min-width: 0;
 }

/* Dashboard Box */
.dashboard-box {
    text-align:center;
    background:white;
    padding:30px;
    border-radius:12px;
    box-shadow:0 8px 25px rgba(0,0,0,0.15);
    box-sizing: border-box;
}

/* Cards */
.cards { display:grid; grid-template-columns: repeat(auto-fit,minmax(250px,1fr)); gap:25px; margin-top:20px; }
.card {
    padding:25px;
    border-radius:8px;
    color:white;
    text-align:center;
    box-shadow:0 5px 10px rgba(0,0,0,0.1);
}
.card h3 { margin-bottom:10px; }
.card p { margin-bottom:15px; }
.card a {
    background:white;
    color:#333;
    padding:8px 16px;
    border-radius:6px;
    text-decoration:none;
    font-weight:bold;
}
.card a:hover { background:#f1f1f1; }

/* Card colors */
.blue { background:#3498db; }
.green { background:#27ae60; }
.orange { background:#f39c12; }
.red { background:#e74c3c; }
.purple { background:#9b59b6; }



/* Responsive */
@media screen and (max-width:900px){
    .container-layout { flex-direction: column; }
    .cards { grid-template-columns: 1fr; }
    .left-column { width:100%; align-items:center; }
}
</style>
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
            <h3>Everest College</h3>
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
            <h2>Teacher Dashboard</h2>
            <div class="cards">
                <div class="card blue">
                    <h3>Manage Questions</h3>
                    <p>Add/Edit/Delete question papers</p>
                    <a href="question_manage.php">Open</a>
                </div>
                <div class="card green">
                    <h3>Add Marks</h3>
                    <p>Enter student marks</p>
                    <a href="exam_resultsheet.php">Open</a>
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