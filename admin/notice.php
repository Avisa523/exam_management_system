<?php
session_start();
include('../includes/config.php');

// Only admin can access
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header('Location: ../authentication/login.php');
    exit;
}

$message = '';
$editingNotice = null;

// Delete
if(isset($_GET['delete_id'])){
    $id = intval($_GET['delete_id']);
    $stmt = $conn->prepare('DELETE FROM noticeboard WHERE id=?');
    $stmt->bind_param('i', $id);
    if($stmt->execute()){ $message = 'Notice deleted successfully!'; }
    $stmt->close();
    header('Location: notice.php');
    exit;
}

// Create / Update
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $title = trim($_POST['title'] ?? '');
    $message_text = trim($_POST['message'] ?? '');
    $visibility = $_POST['visibility'] ?? 'all';
    
    if(empty($title) || empty($message_text)){
        $message = 'Title and message are required!';
    } else {
        if(isset($_POST['edit_id']) && !empty($_POST['edit_id'])){
            $edit_id = intval($_POST['edit_id']);
            $stmt = $conn->prepare('UPDATE noticeboard SET title=?, message=?, visibility=? WHERE id=?');
            $stmt->bind_param('sssi', $title, $message_text, $visibility, $edit_id);
            $stmt->execute();
            $stmt->close();
        } else {
            $posted_by = $_SESSION['user_id'] ?? 0;
            $stmt = $conn->prepare('INSERT INTO noticeboard (title, message, visibility, posted_by) VALUES (?, ?, ?, ?)');
            $stmt->bind_param('sssi', $title, $message_text, $visibility, $posted_by);
            $stmt->execute();
            $stmt->close();
        }
        header('Location: notice.php');
        exit;
    }
}

// Get notice to edit
if(isset($_GET['edit_id'])){
    $edit_id = intval($_GET['edit_id']);
    $stmt = $conn->prepare('SELECT * FROM noticeboard WHERE id=?');
    $stmt->bind_param('i', $edit_id);
    $stmt->execute();
    $editingNotice = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Get all notices
$notices = $conn->query('SELECT * FROM noticeboard ORDER BY posted_at DESC');
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Notices</title>
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<div class="main-content">
    <div class="notice-header-bar">
        <h1>Manage Notices</h1>
        <a href="dashboard.php" class="home-icon">🏠</a>
    </div>

    <?php if(!empty($message)): ?>
        <div class="<?php echo strpos($message,'Error')!==false?'error-msg':'success-msg'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <!-- Form -->
    <div class="form-container">
        <h2><?php echo $editingNotice?'Edit Notice':'Create New Notice'; ?></h2>
        <form method="POST">
            <?php if($editingNotice): ?>
                <input type="hidden" name="edit_id" value="<?php echo $editingNotice['id']; ?>">
            <?php endif; ?>
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" value="<?php echo $editingNotice ? htmlspecialchars($editingNotice['title']) : ''; ?>" required>
            </div>
            <div class="form-group">
                <label>Message</label>
                <textarea name="message" required><?php echo $editingNotice ? htmlspecialchars($editingNotice['message']) : ''; ?></textarea>
            </div>
            <div class="form-group">
                <label>Visibility</label>
                <select name="visibility">
                    <option value="all" <?php echo (!$editingNotice || $editingNotice['visibility']=='all')?'selected':''; ?>>All</option>
                    <option value="students" <?php echo ($editingNotice && $editingNotice['visibility']=='students')?'selected':''; ?>>Students</option>
                    <option value="teachers" <?php echo ($editingNotice && $editingNotice['visibility']=='teachers')?'selected':''; ?>>Teachers</option>
                </select>
            </div>
            <div class="form-submit-wrap">
                <button type="submit"><?php echo $editingNotice?'Update Notice':'+ Create Notice'; ?></button>
                <?php if($editingNotice): ?><a href="notice.php">Cancel</a><?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Notices List -->
    <div class="notice-cards">
        <?php while($n=$notices->fetch_assoc()): ?>
            <?php $vis = $n['visibility'] ?? 'all'; ?>
            <div class="notice-box notice-<?php echo $vis; ?>">
                <h3><?php echo htmlspecialchars($n['title']); ?></h3>
                <p><?php echo nl2br(htmlspecialchars(substr($n['message'],0,150))); ?><?php echo strlen($n['message'])>150?'...':''; ?></p>
                <small>Posted: <?php echo date('M d, Y H:i', strtotime($n['posted_at'])); ?></small>
                <div class="notice-visibility"><?php echo ucfirst($vis); ?></div>
                <div class="notice-actions">
                    <a href="notice.php?edit_id=<?php echo $n['id']; ?>" class="edit-link">✏ Edit</a>
                    <a href="notice.php?delete_id=<?php echo $n['id']; ?>" class="delete-btn" onclick="return confirm('Delete this notice?');">✗ Delete</a>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

</div>

</body>
</html>