<?php
session_start();
include('../includes/config.php');

// Only admin can access
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header('Location: ../authentication/login.php');
    exit;
}

// Manage approve/disapprove
if(isset($_POST['action']) && isset($_POST['question_id'])){
    $id = intval($_POST['question_id']);

    if($_POST['action'] === 'approve'){
        $stmt = $conn->prepare('UPDATE question_papers SET approved=1, disapproval_reason=NULL WHERE id=?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $stmt->close();
        header('Location: question_paper.php?msg=approved');
        exit;
    }

    if($_POST['action'] === 'disapprove' && !empty(trim($_POST['reason']))){
        $reason = trim($_POST['reason']);
        $stmt = $conn->prepare('UPDATE question_papers SET approved=2, disapproval_reason=? WHERE id=?');
        $stmt->bind_param('si', $reason, $id);
        $stmt->execute();
        $stmt->close();
        header('Location: question_paper.php?msg=disapproved');
        exit;
    }
}

$viewId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$renderPaperView = false;
$paper = null;

if($viewId){
    $stmt = $conn->prepare('SELECT qp.*, t.full_name AS teacher_name, s.subject_name 
        FROM question_papers qp 
        LEFT JOIN teachers t ON t.id=qp.teacher_id 
        LEFT JOIN subjects s ON s.id=qp.subject_id 
        WHERE qp.id=? LIMIT 1');
    $stmt->bind_param('i', $viewId);
    $stmt->execute();
    $paper = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if(!$paper){
        die('Question paper not found.');
    }

    $renderPaperView = true;
} else {
    $papers = $conn->query('SELECT qp.*, t.full_name AS teacher_name, s.subject_name 
        FROM question_papers qp 
        LEFT JOIN teachers t ON t.id=qp.teacher_id 
        LEFT JOIN subjects s ON s.id=qp.subject_id 
        ORDER BY qp.created_at DESC');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="topbar">
    <h1>Question Paper</h1>
    <div><a href="dashboard.php">🏠</a></div>
</div>

<?php if($renderPaperView): ?>

<div>

    <div class="admin-only">
        <a href="question_paper.php">← Back to List</a>
        <button onclick="window.print()">Print</button>
    </div>

    <div>
        <img src="../assets/images/everest logo.png" alt="Logo">
        <h2>Everest College</h2>
        <h3>Mid-term Question Paper</h3>

        <div>
            <div><strong>Subject:</strong> <?php echo htmlspecialchars($paper['subject_name']); ?></div>
            <div><strong>Date:</strong> <?php echo date('Y-m-d'); ?></div>
            <div>
                <div><strong>Full Marks:</strong> 100</div>
                <div><strong>Pass Marks:</strong> 40</div>
            </div>
        </div>
    </div>

    <div class="admin-only">
        <strong>Teacher:</strong> <?php echo htmlspecialchars($paper['teacher_name']); ?> |
        <strong>Status:</strong>

        <?php
        if($paper['approved']==1) echo 'Approved';
        elseif($paper['approved']==2) echo 'Disapproved';
        else echo 'Pending';
        ?>

        <?php if(!empty($paper['disapproval_reason'])): ?>
            <div>
                <strong>Reason:</strong> <?php echo htmlspecialchars($paper['disapproval_reason']); ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="question">
        <h4>1. <?php echo htmlspecialchars($paper['title'] ?: $paper['question']); ?></h4>
        <p><?php echo nl2br(htmlspecialchars($paper['question'])); ?></p>

        <?php if(!empty($paper['description'])): ?>
            <p><?php echo nl2br(htmlspecialchars($paper['description'])); ?></p>
        <?php endif; ?>

        <div class="answer"></div>
    </div>

    <p><strong>Attempt all questions.</strong></p>

    <hr class="admin-only">

    <div class="admin-only">
        <h4>Admin Decision</h4>

        <form method="post">
            <input type="hidden" name="question_id" value="<?php echo $paper['id']; ?>">

            <button type="submit" name="action" value="approve">Approve</button>

            <a href="edit_question.php?id=<?php echo $paper['id']; ?>">Edit</a>

            <div>
                <label>Reject with reason:</label>
                <textarea name="reason" rows="3"></textarea>
                <button type="submit" name="action" value="disapprove">Disapprove</button>
            </div>
        </form>
    </div>

</div>

<?php else: ?>

<h2>Admin: Approve Question Papers</h2>

<table border="1" cellpadding="10">
<tr>
    <th>Id</th>
    <th>Title</th>
    <th>Subject</th>
    <th>Teacher</th>
    <th>Created At</th>
    <th>Status</th>
    <th>Reason</th>
    <th>Action</th>
</tr>

<?php if($papers && $papers->num_rows > 0): ?>
    <?php while($row = $papers->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
            <td><?php echo htmlspecialchars($row['teacher_name']); ?></td>
            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
            <td>
                <?php
                if($row['approved']==1) echo 'Approved';
                elseif($row['approved']==2) echo 'Disapproved';
                else echo 'Pending';
                ?>
            </td>
            <td><?php echo htmlspecialchars($row['disapproval_reason'] ?? ''); ?></td>
            <td>
                <a href="question_paper.php?id=<?php echo $row['id']; ?>">View</a>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr><td colspan="8">No question papers found.</td></tr>
<?php endif; ?>

</table>

<?php endif; ?>

</body>
</html>