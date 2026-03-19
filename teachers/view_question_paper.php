<?php
session_start();
include('../includes/config.php');

// Only teachers or students can view
if(!isset($_SESSION['role'])){
    header("Location: ../authentication/login.php");
    exit;
}

// Optional: For teachers, show only their subject
if($_SESSION['role'] == 'teacher'){
    $teacher_id = $_SESSION['id'];
    $teacher = $conn->query("SELECT subject_id FROM teachers WHERE id='$teacher_id'")->fetch_assoc();
    $subject_id = $teacher['subject_id'];
    $questions = $conn->query("SELECT * FROM question_papers WHERE subject_id='$subject_id' ORDER BY id ASC");
} else {
    // For students, get subject from student info
    $student_id = $_SESSION['id'];
    $student = $conn->query("SELECT subject_id FROM students WHERE id='$student_id'")->fetch_assoc();
    $subject_id = $student['subject_id'];
    $questions = $conn->query("SELECT * FROM question_papers WHERE subject_id='$subject_id' ORDER BY id ASC");
}

// Fetch subject name
$subject = $conn->query("SELECT subject_name FROM subjects WHERE id='$subject_id'")->fetch_assoc()['subject_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Question Paper - <?php echo htmlspecialchars($subject); ?></title>
<style>
body { font-family: Arial; padding: 20px; }
h2 { text-align: center; }
ol { margin-top: 20px; }
.question { margin-bottom: 15px; }
</style>
</head>
<body>

<h2>Question Paper - <?php echo htmlspecialchars($subject); ?></h2>

<ol>
<?php while($q = $questions->fetch_assoc()): ?>
    <li class="question">
        <strong><?php echo htmlspecialchars($q['title']); ?></strong><br>
        <?php echo nl2br(htmlspecialchars($q['description'])); ?>
    </li>
<?php endwhile; ?>
</ol>

</body>
</html>