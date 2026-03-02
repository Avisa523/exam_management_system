<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Question Paper</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #f5f5f5;
    margin: 0;
    padding: 0;
}

/* HEADER */
.header-bar {
    background: #0d6efd;
    color: white;
    padding: 20px;
    text-align: center;
}
.header-bar h1 {
    margin: 0;
    font-size: 24px;
}

/* FORM CONTAINER */
.form-container {
    max-width: 900px;
    margin: 30px auto;
    background: white;
    padding: 30px 40px;
    border-radius: 8px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

/* MESSAGES */
.success-msg, .error-msg {
    padding: 12px 15px;
    margin-bottom: 20px;
    border-radius: 5px;
    font-weight: bold;
}
.success-msg { background: #d4edda; color: #155724; border-left: 5px solid #28a745; }
.error-msg { background: #f8d7da; color: #721c24; border-left: 5px solid #dc3545; }

/* PAPER INFO */
.paper-info {
    background: #f1f1f1;
    padding: 15px 20px;
    border-radius: 5px;
    margin-bottom: 25px;
}
.paper-info p {
    margin: 5px 0;
}
.paper-info strong {
    color: #333;
}

/* FORM GROUP */
.form-group {
    margin-bottom: 20px;
}
.form-group label {
    display: block;
    font-weight: bold;
    margin-bottom: 6px;
    color: #333;
}
.form-group input, .form-group textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 14px;
    font-family: inherit;
}
.form-group textarea { resize: vertical; min-height: 120px; }

/* BUTTONS */
.form-actions {
    display: flex;
    gap: 12px;
    margin-top: 25px;
}
.form-actions button, .form-actions a {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    font-size: 14px;
    cursor: pointer;
    text-decoration: none;
    text-align: center;
    transition: 0.3s;
}
.btn-save { background: #28a745; color: white; }
.btn-save:hover { background: #218838; }
.btn-cancel { background: #6c757d; color: white; }
.btn-cancel:hover { background: #5a6268; }

/* RESPONSIVE */
@media(max-width: 600px){
    .form-container { padding: 20px; }
    .form-actions { flex-direction: column; }
}
</style>
</head>
<body>

<div class="header-bar">
    <h1>Edit Question Paper</h1>
</div>

<div class="form-container">
    
    <?php if(!empty($message)): ?>
        <div class="<?php echo strpos($message, 'required') !== false ? 'error-msg' : 'success-msg'; ?>">
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