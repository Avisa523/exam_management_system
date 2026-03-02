<?php
session_start();

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../authentication/login.php");
    exit;
}

include('../includes/config.php');

/* TOTAL STUDENTS */
$students = $conn->query("SELECT COUNT(*) as total FROM students");
$total_students = $students->fetch_assoc()['total'];

/* TOTAL TEACHERS */
$teachers = $conn->query("SELECT COUNT(*) as total FROM teachers");
$total_teachers = $teachers->fetch_assoc()['total'];

/* TOTAL SUBJECTS */
$subjects = $conn->query("SELECT COUNT(*) as total FROM subjects");
$total_subjects = $subjects->fetch_assoc()['total'];

/* TOTAL EXAMS */
$exams = $conn->query("SELECT COUNT(*) as total FROM exam_schedule");
$total_exams = $exams->fetch_assoc()['total'];
?>

<link rel="stylesheet" href="../assets/css/dashboard.css">

<div class="topbar">
    Exam Management System
</div>

<div class="container-layout">

    <div class="left-column">
        <div class="logo">
            <img src="../assets/images/everest logo.png" alt="College Logo">
        </div>

        <div class="sidebar">
            <ul>
                <li><a href="dashboard.php">🏠 Dashboard</a></li>
                <li><a href="question_paper.php">📄 Questions</a></li>
                <li><a href="exam_result.php">📊 Results/ 🖨️ Marksheet</a></li>
                <li><a href="notice.php">📢 Noticeboard</a></li>
                <li><a href="../authentication/logout.php">
                    <i class="fas fa-sign-out-alt"></i> 🔒 Logout</a></li>
            </ul>
        </div>
    </div>

    <div class="right-column">
        <h2>Admin Dashboard</h2>

        <div class="cards">

            <a href="manage_student.php" class="card card-blue">
                <h3>Students</h3>
                
            </a>

            <a href="manage_teacher.php" class="card card-green">
                <h3>Teachers</h3>
            
            </a>

            <a href="manage_subject.php" class="card card-orange">
                <h3>Subjects</h3>
                
            </a>

            <a href="exam_schedule.php" class="card card-red">
                <h3>Exams</h3>
                
            </a>

        </div>
    </div>

</div>

<style>
.cards {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    margin-top: 20px;
}

.card {
    flex: 1 1 calc(50% - 20px);
    background: #3498db;
    padding: 40px 20px;
    text-align: center;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.card h3 { margin-bottom: 10px; font-size: 20px; }
.card p { font-size: 28px; font-weight: bold; }

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
}

.card-blue { background: #3498db; }
.card-green { background: #2ecc71; }
.card-orange { background: #f39c12; }
.card-red { background: #e74c3c; }

@media (max-width: 768px){
    .card { flex: 1 1 100%; }
}
</style>