<?php
session_start();
include("../includes/config.php");

// Check student login
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'student'){
    header("Location: ../authentication/login.php");
    exit();
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

// Map users.id to students.id
$user_id = $_SESSION['id'];
$student_query = $conn->query("SELECT id, full_name FROM students WHERE user_id = '$user_id'");
$student_row = $student_query->fetch_assoc();
$student_id = $student_row['id'] ?? 0;
$student_name = $student_row['full_name'] ?? "Student";

// Calculate results
$query = "SELECT 
            SUM(marks) AS total_obtained,
            SUM(total_marks) AS total_marks,
            COUNT(subject_id) AS total_subjects
          FROM exam_results
          WHERE student_id = '$student_id'";

$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

$total_obtained = $data['total_obtained'] ?? 0;
$total_marks = $data['total_marks'] ?? 0;
$total_subjects = $data['total_subjects'] ?? 0;

$percentage = ($total_marks > 0) ? ($total_obtained / $total_marks) * 100 : 0;
$final_result = ($percentage >= 40) ? "PASS" : "FAIL";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Final Result</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="marksheet-box">
    <!-- Header -->
    <div class="marksheet-header">
        <h1>Marksheet</h1>
        <a href="dashboard.php" class="home-icon">🏠</a>
    </div>

    <!-- Content -->
    <div class="marksheet-content">
        <h2><?php echo htmlspecialchars($student_name); ?></h2>
        <p><strong>Total Subjects:</strong> <?php echo $total_subjects; ?></p>
        <p><strong>Total Obtained:</strong> <?php echo $total_obtained; ?></p>
        <p><strong>Total Marks:</strong> <?php echo $total_marks; ?></p>
        <p><strong>Percentage:</strong> <?php echo number_format($percentage,2); ?>%</p>
        <div class="final-result <?php echo strtolower($final_result); ?>">Final Result: <?php echo $final_result; ?></div>
    </div>
</div>

</body>
</html>