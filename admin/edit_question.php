<?php
session_start();
include('../includes/config.php');

// Only admin can access
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header('Location: ../authentication/login.php');
    exit;
}

$message = '';
$paper = null;

// Get question paper ID from URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if(!$id){
    die('Question paper ID not provided.');
}

// Fetch the question paper
$stmt = $conn->prepare('SELECT qp.*, t.full_name AS teacher_name, s.subject_name FROM question_papers qp LEFT JOIN teachers t ON t.id=qp.teacher_id LEFT JOIN subjects s ON s.id=qp.subject_id WHERE qp.id=? LIMIT 1');
$stmt->bind_param('i', $id);
$stmt->execute();
$paper = $stmt->get_result()->fetch_assoc();
$stmt->close();

if(!$paper){
    die('Question paper not found.');
}

// Handle Update
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $title = trim($_POST['title'] ?? '');
    $question = trim($_POST['question'] ?? '');
    $description = trim($_POST['description'] ?? '');
    
    if(empty($title) || empty($question)){
        $message = 'Title and question are required!';
    } else {
        $stmt = $conn->prepare('UPDATE question_papers SET title=?, question=?, description=? WHERE id=?');
        $stmt->bind_param('sssi', $title, $question, $description, $id);
        if($stmt->execute()){
            $message = 'Question paper updated successfully!';
            // Refresh the paper data
            $stmt = $conn->prepare('SELECT qp.*, t.full_name AS teacher_name, s.subject_name FROM question_papers qp LEFT JOIN teachers t ON t.id=qp.teacher_id LEFT JOIN subjects s ON s.id=qp.subject_id WHERE qp.id=? LIMIT 1');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $paper = $stmt->get_result()->fetch_assoc();
            $stmt->close();
        }
    }
}

$subjects = $conn->query('SELECT * FROM subjects ORDER BY subject_name ASC');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Question Paper</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .form-container { max-width:900px; margin:0 auto; background:white; padding:30px; border-radius:5px; }
        .form-group { margin-bottom:20px; }
        .form-group label { display:block; font-weight:bold; margin-bottom:8px; color:#333; }
        .form-group input, .form-group textarea, .form-group select { 
            width:100%; 
            padding:10px; 
            border:1px solid #ddd; 
            border-radius:4px; 
            font-size:14px;
            font-family:inherit;
        }
        .form-group textarea { resize:vertical; min-height:150px; }
        .form-actions { display:flex; gap:10px; margin-top:30px; }
        .form-actions button, .form-actions a { 
            padding:10px 20px; 
            border:none; 
            border-radius:4px; 
            cursor:pointer; 
            font-size:14px;
            text-decoration:none;
            text-align:center;
        }
        .btn-save { background:#28a745; color:white; }
        .btn-save:hover { background:#218838; }
        .btn-cancel { background:#6c757d; color:white; }
        .btn-cancel:hover { background:#5a6268; }
        .success-msg { background:#d4edda; color:#155724; padding:12px; border-radius:4px; margin-bottom:20px; border-left:4px solid #28a745; }
        .error-msg { background:#f8d7da; color:#721c24; padding:12px; border-radius:4px; margin-bottom:20px; border-left:4px solid #dc3545; }
        .paper-info { background:#f5f5f5; padding:15px; border-radius:4px; margin-bottom:20px; }
        .paper-info p { margin:5px 0; }
        .paper-info strong { color:#333; }
        .header-bar { background:#0d6efd; color:white; padding:20px; text-align:center; margin-bottom:30px; }
        .header-bar h1 { margin:0; }
    </style>
</head>
<body>

<div class="header-bar">
    <h1>Edit Question Paper</h1>
</div>

<div class="form-container">
    
    <?php if(!empty($message)): ?>
        <div class="<?php echo strpos($message, 'Error') !== false || strpos($message, 'required') !== false ? 'error-msg' : 'success-msg'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <div class="paper-info">
        <p><strong>Teacher:</strong> <?php echo htmlspecialchars($paper['teacher_name'] ?? 'N/A'); ?></p>
        <p><strong>Subject:</strong> <?php echo htmlspecialchars($paper['subject_name'] ?? 'N/A'); ?></p>
        <p><strong>Created:</strong> <?php echo date('M d, Y H:i', strtotime($paper['created_at'])); ?></p>
        <p><strong>Status:</strong> 
            <?php
            if($paper['approved']==1) echo '<span style="color:#28a745; font-weight:bold;">✓ Approved</span>';
            elseif($paper['approved']==2) echo '<span style="color:#dc3545; font-weight:bold;">✗ Disapproved</span>';
            else echo '<span style="color:#f39c12; font-weight:bold;">⊘ Pending</span>';
            ?>
        </p>
    </div>

    <form method="POST">
        <div class="form-group">
            <label for="title">Question Title</label>
            <input type="text" name="title" id="title" placeholder="e.g., Explain Database Normalization" value="<?php echo htmlspecialchars($paper['title'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label for="question">Question (Full Text)</label>
            <textarea name="question" id="question" placeholder="Enter the complete question here..." required><?php echo htmlspecialchars($paper['question'] ?? ''); ?></textarea>
        </div>

        <div class="form-group">
            <label for="description">Description / Answer Hints (Optional)</label>
            <textarea name="description" id="description" placeholder="Optional: Add hints, keywords, or guidance for students..."><?php echo htmlspecialchars($paper['description'] ?? ''); ?></textarea>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-save">✓ Update Question</button>
            <a href="question_paper.php?id=<?php echo $paper['id']; ?>" class="btn-cancel">← Back to Review</a>
        </div>
    </form>
</div>

<?php include('../includes/footer.php'); ?>

</body>
</html>
