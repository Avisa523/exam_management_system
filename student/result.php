<?php
session_start();
include('../includes/config.php');

// Determine role
$role = $_SESSION['role'] ?? '';
$student_id = null;

// -------------------------
// Admin: student_id from URL
// -------------------------
if($role == 'admin'){
    if(!isset($_GET['student_id'])){
        echo "No student selected.";
        exit;
    }
    $student_id = intval($_GET['student_id']);
}

// -------------------------
// Student: get ID from session
// -------------------------
elseif($role == 'student'){
    if(!isset($_SESSION['id'])){
        header("Location: ../authentication/login.php");
        exit;
    }
    $user_id = $_SESSION['id'];
    // Map to students.id
    $stmt = $conn->prepare("SELECT * FROM students WHERE user_id=?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $student = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if(!$student){
        echo "Student not found.";
        exit;
    }
    $student_id = $student['id'];
}

// -------------------------
// Fetch student info
// -------------------------
if($role == 'admin'){
    $stmt = $conn->prepare("SELECT * FROM students WHERE id=?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $student = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if(!$student){
        echo "Student not found.";
        exit;
    }
}

// -------------------------
// Fetch totals
// -------------------------
$stmt = $conn->prepare("
    SELECT 
        SUM(er.marks) AS total_obtained,
        SUM(su.full_marks) AS total_full_marks,
        ROUND(SUM(er.marks)/SUM(su.full_marks)*100,2) AS percentage
    FROM exam_results er
    JOIN subjects su ON er.subject_id = su.id
    WHERE er.student_id=?
");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$totals = $stmt->get_result()->fetch_assoc();
$stmt->close();

// -------------------------
// Fetch marks per subject
// -------------------------
$stmt = $conn->prepare("
    SELECT 
        su.subject_name,
        su.full_marks,
        er.marks,
        CASE 
            WHEN (er.marks / su.full_marks * 100) >= 80 THEN 'A+'
            WHEN (er.marks / su.full_marks * 100) >= 70 THEN 'A'
            WHEN (er.marks / su.full_marks * 100) >= 60 THEN 'B+'
            WHEN (er.marks / su.full_marks * 100) >= 50 THEN 'B'
            WHEN (er.marks / su.full_marks * 100) >= 40 THEN 'C'
            ELSE 'F'
        END AS grade,
        CASE WHEN er.approved = 1 THEN 'Approved' ELSE 'Pending' END AS status
    FROM exam_results er
    JOIN subjects su ON er.subject_id = su.id
    WHERE er.student_id=?
    ORDER BY su.subject_name ASC
");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$results = $stmt->get_result();
$stmt->close();

// -------------------------
// Final result PASS/FAIL
// -------------------------
$perc = $totals['percentage'] ?? 0;
$final_result = ($perc >= 40) ? "PASS" : "FAIL";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Marksheet - <?php echo htmlspecialchars($student['full_name']); ?></title>
<link rel="stylesheet" href="../assets/css/admin.css">
<style>
.marksheet {
    width: 800px;
    margin: 20px auto;
    padding: 30px;
    border: 2px solid #000;
    background: #fff;
    position: relative;
    font-family: Arial, sans-serif;
}
.marksheet .print-btn {
    position: absolute;
    top: 15px;
    right: 15px;
    padding: 5px 10px;
    font-size: 12px;
    background-color: #1e90ff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
.marksheet .print-btn:hover { background-color: #0d6efd; }
.marksheet header { text-align: center; margin-bottom: 25px; }
.marksheet header img { height: 100px; margin-bottom: 10px; }
.marksheet header h2, .marksheet header h3 { margin: 5px 0; font-weight: bold; }
.marksheet .student-info { margin-bottom: 20px; }
.marksheet .student-info p { margin: 4px 0; }
.marksheet table { width: 100%; border-collapse: collapse; margin-top: 10px; }
.marksheet table, .marksheet th, .marksheet td { border: 1px solid #000; }
.marksheet th { background-color: #1e90ff; color: white; }
.marksheet td { background-color: #f9f9f9; }
.marksheet th, .marksheet td { padding: 8px; text-align: center; }
.marksheet .total-row { font-weight: bold; background: #f0f0f0; }
.marksheet .final-pass { color: #28a745; font-weight: bold; }
.marksheet .final-fail { color: #dc3545; font-weight: bold; }
.marksheet .remarks { margin-top: 20px; font-weight: bold; }
@media print { .marksheet .print-btn { display: none !important; } }
</style>
</head>
<body>
<div class="exam-header-bar">
    <h1>Results</h1>
    <a href="dashboard.php" >🏠</a>
</div>
<div class="marksheet">
    <button class="print-btn" onclick="window.print()">🖨 Print</button>

    <header>
        <img src="../assets/images/everest logo.png" alt="Logo">
        <h2>Everest College</h2>
        <h3>Student Marksheet</h3>
    </header>

    <div class="student-info">
        <p><strong>Name:</strong> <?php echo htmlspecialchars($student['full_name']); ?></p>
        <p><strong>Roll / ID:</strong> <?php echo htmlspecialchars($student['id']); ?></p>
        <p><strong>Date:</strong> <?php echo date("Y-m-d"); ?></p>
    </div>

    <table>
        <tr>
            <th>Subject</th>
            <th>Full Marks</th>
            <th>Marks Obtained</th>
            <th>Grade</th>
            <th>Status</th>
        </tr>
        <?php
        if($results->num_rows > 0){
            while($row = $results->fetch_assoc()){
                echo "<tr>
                        <td>".htmlspecialchars($row['subject_name'])."</td>
                        <td>".htmlspecialchars($row['full_marks'])."</td>
                        <td>".htmlspecialchars($row['marks'])."</td>
                        <td>".htmlspecialchars($row['grade'])."</td>
                        <td>".htmlspecialchars($row['status'])."</td>
                      </tr>";
            }
            echo "<tr class='total-row'>
                    <td colspan='2'>Total Marks</td>
                    <td>".htmlspecialchars($totals['total_obtained'])."</td>
                    <td colspan='2'>Percentage: ".htmlspecialchars($totals['percentage'])."%</td>
                  </tr>";
            echo "<tr class='total-row'>
                    <td colspan='5'>Final Result: <span class='".strtolower($final_result == 'PASS' ? 'final-pass' : 'final-fail')."'>".$final_result."</span></td>
                  </tr>";
        } else {
            echo "<tr><td colspan='5'>No exam results found.</td></tr>";
        }
        ?>
    </table>

    <div class="remarks">
        <p><strong>Teacher's Remark:</strong> 
        <?php 
            if($perc >= 80) echo "Excellent Performance";
            elseif($perc >= 60) echo "Good Performance";
            elseif($perc >= 40) echo "Satisfactory";
            else echo "Needs Improvement";
        ?>
        </p>
    </div>
</div>

</body>
</html>