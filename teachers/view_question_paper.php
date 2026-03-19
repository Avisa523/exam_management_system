<?php
session_start();
include('../includes/config.php');

// Only teachers or students can view
if(!isset($_SESSION['role'])){
    header("Location: ../authentication/login.php");
    exit;
}

// Support direct question paper view via ?id=... (shows full paper for that subject)
$questionId = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($questionId) {
    // Find the subject for this question, then show all approved questions for that subject
    $questionRow = $conn->query("SELECT * FROM question_papers WHERE id='$questionId' LIMIT 1")->fetch_assoc();
    $subject_id = $questionRow['subject_id'];

    $questions = $conn->query("SELECT * FROM question_papers WHERE subject_id='$subject_id' AND approved=1 ORDER BY id ASC");
    $subject = $conn->query("SELECT subject_name FROM subjects WHERE id='$subject_id'")->fetch_assoc()['subject_name'];
} else {
    // Optional: For teachers, show only their subject
    if($_SESSION['role'] == 'teacher'){
        $teacher_id = $_SESSION['id'];
        $teacher = $conn->query("SELECT subject_id FROM teachers WHERE id='$teacher_id'")->fetch_assoc();
        $subject_id = $teacher['subject_id'];
        $questions = $conn->query("SELECT * FROM question_papers WHERE subject_id='$subject_id' AND approved=1 ORDER BY id ASC");
    } else {
        // For students, get subject from student info
        $student_id = $_SESSION['id'];
        $student = $conn->query("SELECT subject_id FROM students WHERE id='$student_id'")->fetch_assoc();
        $subject_id = $student['subject_id'];
        $questions = $conn->query("SELECT * FROM question_papers WHERE subject_id='$subject_id' AND approved=1 ORDER BY id ASC");
    }

    // Fetch subject name
    $subject = $conn->query("SELECT subject_name FROM subjects WHERE id='$subject_id'")->fetch_assoc()['subject_name'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Question Paper - <?php echo htmlspecialchars($subject); ?></title>
<link rel="stylesheet" href="../assets/css/dashboard.css">
<link rel="stylesheet" href="../assets/css/teachers.css">
</head>
</head>

<body>

<div class="paper">

<header>

    <button class="print-btn" onclick="window.print()">🖨 Print</button>

    <img src="../assets/images/everest logo.png" alt="Logo">

    <h2>Everest College</h2>
    <h3>Exam Question Paper</h3>

    <div class="marks">
        Full Marks: 100<br>
        Pass Marks: 40
    </div>

    <p>Subject: <?php echo htmlspecialchars($subject); ?></p>
    <p>Date: <?php echo date("Y-m-d"); ?></p>

</header>

<?php $counter = 1; ?>

<?php while($q = $questions->fetch_assoc()): ?>

<div class="question">

<strong>
<?php echo $counter++; ?>.
<?php echo htmlspecialchars($q['question']); ?>
</strong>

<div class="answer"></div>

</div>

<?php endwhile; ?>

<p><strong>Attempt all questions.</strong></p>

</div>

</body>
</html>