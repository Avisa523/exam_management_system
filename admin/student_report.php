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

$student_id = $_GET['student_id'];

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

// Fetch student's exam results
$stmt = $conn->prepare("
    SELECT er.*, s.subject_name
    FROM exam_results er
    LEFT JOIN subjects s ON er.subject_id = s.id
    WHERE er.student_id=?
    ORDER BY er.id DESC
");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$results = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Report - <?php echo htmlspecialchars($student['full_name']); ?></title>
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<h2>Report for <?php echo htmlspecialchars($student['full_name']); ?></h2>
<p>Course: <?php echo htmlspecialchars($student['course']); ?></p>

<a href="student_list.php" class="back-btn">← Back to Student List</a>

<table>
<tr>
    <th>Subject</th>
    <th>Marks</th>
    <th>Grade</th>
    <th>Status</th>
</tr>

<?php
$totalMarks = 0;
$count = 0;
if($results->num_rows > 0){
    while($row = $results->fetch_assoc()){
        $totalMarks += $row['marks'];
        $count++;
        echo "<tr>
                <td>".htmlspecialchars($row['subject_name'])."</td>
                <td>".htmlspecialchars($row['marks'])."</td>
                <td>".htmlspecialchars($row['grade'])."</td>
                <td>".(($row['approved']==1)?"Approved":"Pending")."</td>
              </tr>";
    }
    $average = round($totalMarks / $count, 2);
    echo "<tr>
            <td colspan='4'><strong>Total Marks: $totalMarks | Average: $average</strong></td>
          </tr>";
} else {
    echo "<tr><td colspan='4'>No exam results found.</td></tr>";
}
?>

</table>

</body>
</html>