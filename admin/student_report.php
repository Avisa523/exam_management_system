<?php
session_start();
include('../includes/config.php');

// Only allow admin access
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../authentication/login.php");
    exit;
}

// Get student ID from URL
if(!isset($_GET['student_id'])){
    echo "No student selected.";
    exit;
}

$student_id = intval($_GET['student_id']);

// Fetch student info
$stmt = $conn->prepare("SELECT * FROM students WHERE id=?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

if(!$student){
    echo "Student not found.";
    exit;
}

// Fetch total marks and percentage
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

// Fetch marks per subject with grade and status
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Marksheet - <?php echo htmlspecialchars($student['full_name']); ?></title>
<link rel="stylesheet" href="../assets/css/admin.css">

</head>
<body>
<div class="topbar">
    <h2>Student Marksheet</h2>
    <a href="dashboard.php">🏠</a>
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
        } else {
            echo "<tr><td colspan='5'>No exam results found.</td></tr>";
        }
        ?>
    </table>

    <div class="remarks">
        <p><strong>Teacher's Remark:</strong> 
        <?php 
            $perc = $totals['percentage'] ?? 0;
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