<?php
session_start();
include('../includes/config.php');

// Only logged-in users
if(!isset($_SESSION['role'])){
    header("Location: ../authentication/login.php");
    exit;
}

$subject = '';
$questions = null;

// Get paper via ID (admin / direct view)
$questionId = isset($_GET['id']) ? intval($_GET['id']) : null;

if ($questionId) {

    $res = $conn->query("SELECT * FROM question_papers WHERE id='$questionId' LIMIT 1");

    if($res && $res->num_rows > 0){

        $questionRow = $res->fetch_assoc();
        $subject_id = $questionRow['subject_id'];

        // Only approved questions
        $questions = $conn->query("
            SELECT * FROM question_papers 
            WHERE subject_id='$subject_id' AND approved=1 
            ORDER BY id ASC
        ");

    } else {
        die("Invalid Question Paper ID");
    }

} else {

    // Teacher
    if($_SESSION['role'] == 'teacher'){
        $teacher_id = $_SESSION['id'];

        $res = $conn->query("SELECT subject_id FROM teachers WHERE id='$teacher_id'");

        if($res && $res->num_rows > 0){
            $teacher = $res->fetch_assoc();
            $subject_id = $teacher['subject_id'];
        } else {
            die("Teacher not found");
        }

    // Student
    } else {
        $student_id = $_SESSION['id'];

        $res = $conn->query("SELECT subject_id FROM students WHERE id='$student_id'");

        if($res && $res->num_rows > 0){
            $student = $res->fetch_assoc();
            $subject_id = $student['subject_id'];
        } else {
            die("Student not found");
        }
    }

    // Only approved questions
    $questions = $conn->query("
        SELECT * FROM question_papers 
        WHERE subject_id='$subject_id' AND approved=1 
        ORDER BY id ASC
    ");
}

// Get subject name safely
$subRes = $conn->query("SELECT subject_name FROM subjects WHERE id='$subject_id'");
if($subRes && $subRes->num_rows > 0){
    $subject = $subRes->fetch_assoc()['subject_name'];
} else {
    $subject = "Unknown Subject";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Question Paper - <?php echo htmlspecialchars($subject); ?></title>
<link rel="stylesheet" href="../assets/css/question_paper.css">
<!-- <style>
body{
    font-family: Arial, sans-serif;
    background:#f4f6f9;
    margin:0;
    padding:40px;
}

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

/* Dashboard Button */
.home-icon{
    position:absolute;
    top:10px;
    right:20px;
    font-size:26px;
    text-decoration:none;
}

@media print{
    .print-btn,
    .home-icon{
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
</style> -->

</head>

<body>
<!-- Dashboard Button -->
    <a href="<?php 
        if($_SESSION['role']=='admin') echo '../admin/dashboard.php';
        elseif($_SESSION['role']=='teacher') echo '../teachers/dashboard.php';
        else echo '../students/dashboard.php';
    ?>" class="home-icon">🏠</a>

<div class="paper">

<header>

   

    <!-- Print -->
    <button class="print-btn" onclick="window.print()">🖨 Print</button>

    <img src="../assets/images/everest logo.png" alt="Logo">

    <h2>Everest College</h2>
    <h3>Exam Question Paper</h3>

    <div class="marks">
        Full Marks: 100<br>
        Pass Marks: 40
    </div>

    <p><strong>Subject:</strong> <?php echo htmlspecialchars($subject); ?></p>
    <p><strong>Date:</strong> <?php echo date("Y-m-d"); ?></p>

</header>

<?php $counter = 1; ?>

<?php if($questions && $questions->num_rows > 0): ?>

    <?php while($q = $questions->fetch_assoc()): ?>

        <div class="question">

            <strong>
                <?php echo $counter++; ?>.
                <?php echo htmlspecialchars($q['question']); ?>
            </strong>

            <?php if(!empty($q['description'])): ?>
                <p><?php echo htmlspecialchars($q['description']); ?></p>
            <?php endif; ?>

            <div class="answer"></div>

        </div>

    <?php endwhile; ?>

<?php else: ?>

    <p>No approved questions available.</p>

<?php endif; ?>

<p><strong>Attempt all questions.</strong></p>

</div>

</body>
</html>