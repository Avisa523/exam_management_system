<?php
session_start();
include('../includes/config.php');

// Only teachers
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'teacher'){
    header("Location: ../authentication/login.php");
    exit;
}

$teacher_id = $_SESSION['id'];

// Fetch all questions for this teacher including approval status and disapproval reason
$questions = $conn->query("
SELECT id, title, question, description, approved, disapproval_reason 
FROM question_papers 
WHERE teacher_id='$teacher_id'
ORDER BY id ASC
");

// Full marks & pass marks
$full_marks = 100;
$pass_marks = 40;

// Get teacher subject
$teacher = $conn->query("SELECT subject_id FROM teachers WHERE id='$teacher_id'")->fetch_assoc();
$subject_id = $teacher['subject_id'] ?? 0;

$subject = '';
if($subject_id){
    $subject_row = $conn->query("SELECT subject_name FROM subjects WHERE id='$subject_id'")->fetch_assoc();
    $subject = $subject_row['subject_name'] ?? '';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Question Paper</title>
<link rel="stylesheet" href="../assets/css/question_paper.css">

<style>
body{
    font-family: Arial, sans-serif;
    background:#f4f6f9;
    margin:0;
    padding:40px;
}

/* Paper container */
.paper{
    background:white;
    padding:40px;
    max-width:900px;
    margin:auto;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
}

/* Header */
header{
    text-align:center;
    border-bottom:2px solid #000;
    padding-bottom:10px;
    margin-bottom:25px;
    position:relative;
}

.home-icon{
    position: absolute;
    top: 10px;
    right: 20px;
    font-size: 26px;
    text-decoration: none;
}

header img{
    width:100px;
    height:100px;
}

.marks{
    position:absolute;
    top:10px;
    right:0;
    text-align:right;
}

.print-btn{
    position:absolute;
    top:10px;
    left:0;
    padding:6px 12px;
    cursor:pointer;
}

@media print{
    .print-btn{
        display:none;
    }
}

/* Questions */
.question{
    margin-bottom:25px;
}

.answer{
    border-bottom:1px solid #000;
    height:50px;
    margin-top:10px;
}
</style>

</head>
<body>

<div class="paper">
<a href="dashboard.php" class="home-icon">🏠</a>
<header>

    <button class="print-btn" onclick="window.print()">🖨 Print</button>

    <img src="../assets/images/everest logo.png" alt="Logo">

    <h2>Everest College</h2>
    <h3>Exam Question Paper</h3>

    <div class="marks">
        Full Marks: <?php echo $full_marks; ?><br>
        Pass Marks: <?php echo $pass_marks; ?>
    </div>

    <p>Subject: <?php echo htmlspecialchars($subject); ?></p>
    <p>Date: <?php echo date("Y-m-d"); ?></p>
</header>

<?php $counter = 1; ?>

<?php if($questions->num_rows == 0): ?>
    <p>No questions found for this teacher.</p>
<?php else: ?>
    <?php while($q = $questions->fetch_assoc()): ?>
    <div class="question">
        <strong><?php echo $counter++; ?>. <?php echo htmlspecialchars($q['question']); ?></strong>
        <p>Status: 
            <?php
            if($q['approved']==1) echo '<span style="color:#28a745;font-weight:bold;">Approved</span>';
            elseif($q['approved']==2) echo '<span style="color:#dc3545;font-weight:bold;">Disapproved</span>';
            else echo '<span style="color:#f39c12;font-weight:bold;">Pending</span>';
            ?>
        </p>
        <?php if($q['approved']==2 && !empty($q['disapproval_reason'])): ?>
            <p><strong>Disapproval Reason:</strong> <?php echo htmlspecialchars($q['disapproval_reason']); ?></p>
        <?php endif; ?>
        <?php if(!empty($q['description'])): ?>
            <p><?php echo htmlspecialchars($q['description']); ?></p>
        <?php endif; ?>
    </div>
    <?php endwhile; ?>
<?php endif; ?>

<p><strong>Attempt all questions.</strong></p>

</div>

</body>
</html>