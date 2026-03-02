<?php
session_start();
include('../includes/config.php');

// Only admin
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../authentication/login.php");
    exit;
}

$questionId = isset($_GET['id']) ? intval($_GET['id']) : 1;
if(!$questionId) die("id is required");

// Fetch question paper info
$stmt = $conn->prepare("
    SELECT qp.*, t.full_name AS teacher_name, s.subject_name 
    FROM question_papers qp
    LEFT JOIN teachers t ON t.id = qp.teacher_id
    LEFT JOIN subjects s ON s.id = qp.subject_id
    WHERE qp.id = ?
");
$stmt->bind_param("i", $questionId);
$stmt->execute();
$paper = $stmt->get_result()->fetch_assoc();
$stmt->close();

if(!$paper) die("Question Paper not found.");

$questions = $conn->query("
    SELECT * FROM question_papers 
    WHERE teacher_id = {$paper['teacher_id']} 
      AND subject_id = {$paper['subject_id']} 
    ORDER BY id ASC
");

// Admin action messages
$message = '';
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])){
    $action = $_POST['action'];
    $reason = trim($_POST['reason'] ?? '');
    
    if($action == 'approve'){
        $conn->query("UPDATE question_papers SET approved=1, disapproval_reason=NULL WHERE id={$paper['id']}");
        $message = "Question Paper Approved";
        $paper['approved'] = 1;
        $paper['disapproval_reason'] = '';
    } elseif($action == 'disapprove'){
        $conn->query("UPDATE question_papers SET approved=2, disapproval_reason='". $conn->real_escape_string($reason) ."' WHERE id={$paper['id']}");
        $message = "Question Paper Disapproved";
        $paper['approved'] = 2;
        $paper['disapproval_reason'] = $reason;
    }
}

$full_marks = 100;
$pass_marks = 40;
?>

<!DOCTYPE html>
<head>
<title>Question Paper - Admin View</title>
<link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>
<div class="topbar">
    <h1>Question Paper Details</h1>
    <a href="dashboard.php">🏠 </a>
</div>

<!-- Admin Actions -->
<?php if($_SESSION['role']=='admin'): ?>
<div class="admin-controls">
    <h3>Admin Decision</h3>
    <?php if($message): ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <form method="post">
        <label><strong>Disapproval Reason (if disapproving):</strong></label><br>
        <textarea name="reason" rows="3" cols="50"><?php echo htmlspecialchars($paper['disapproval_reason'] ?? ''); ?></textarea><br><br>
        <button type="submit" name="action" value="approve">Approve</button>
        <button type="submit" name="action" value="disapprove">Disapprove</button>
        
    </form>
</div>
<?php endif; ?>

<!-- Printable Question Paper -->
<div class="paper">
    <header>
        <button class="print-btn" onclick="window.print()">🖨 Print</button>
        <img src="../assets/images/everest logo.png" alt="Logo" style="height:60px;">
        <h2>Everest College</h2>
        <h3>Exam Question Paper</h3>
        <div class="marks">
            Full Marks: <?php echo $full_marks; ?><br>
            Pass Marks: <?php echo $pass_marks; ?>
        </div>
        <p>Subject: <?php echo htmlspecialchars($paper['subject_name']); ?></p>
        <p>Date: <?php echo date("Y-m-d"); ?></p>
    </header>

    <?php $counter = 1; ?>
<?php if($questions && $questions->num_rows>0): ?>
    <?php while($q = $questions->fetch_assoc()): ?>
        <div class="question">
            <strong><?php echo $counter++; ?>. <?php echo htmlspecialchars($q['question']); ?></strong>
            <?php if(!empty($q['description'])): ?>
                <p><?php echo htmlspecialchars($q['description']); ?></p>
            <?php endif; ?>
            <div class="answer"></div>
        </div>
    <?php endwhile; ?>
<?php else: ?>
    <p>No questions found.</p>
<?php endif; ?>

    <p><strong>Attempt all questions.</strong></p>
</div>

</body>
</html>