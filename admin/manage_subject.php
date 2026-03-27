<?php
session_start();

// Only admin can access
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header('Location: ../authentication/login.php');
    exit;
}

include(__DIR__ . '/../includes/config.php');

// Handle Add / Update Subject
if(isset($_POST['save_subject'])){
    $subject_name = $_POST['subject_name'];
    $subject_code = $_POST['subject_code'];

    if(isset($_POST['edit_id']) && !empty($_POST['edit_id'])){
        // UPDATE
        $id = intval($_POST['edit_id']);
        $stmt = $conn->prepare("UPDATE subjects SET subject_name=?, subject_code=? WHERE id=?");
        $stmt->bind_param("ssi", $subject_name, $subject_code, $id);
        if($stmt->execute()){
            // Redirect to clear form and show message
            header('Location: subjects.php?success=updated');
            exit;
        }
        $stmt->close();
    } else {
        // ADD
        $stmt = $conn->prepare("INSERT INTO subjects(subject_name, subject_code, status) VALUES(?,?,1)");
        $stmt->bind_param("ss", $subject_name, $subject_code);
        if($stmt->execute()){
            // Redirect to clear form and show message
            header('Location: subjects.php?success=added');
            exit;
        }
        $stmt->close();
    }
}

// Handle Delete
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM subjects WHERE id=$id");
    header('Location: subjects.php?success=deleted');
    exit;
}

// Handle Edit
$editingSubject = null;
if(isset($_GET['edit'])){
    $id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM subjects WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $editingSubject = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Fetch all subjects
$result = $conn->query("SELECT * FROM subjects ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Subjects - Admin</title>
<link rel="stylesheet" href="../assets/css/admin.css">
<style>
/* ===== SUBJECT TABLE ACTION BUTTONS ===== */
.edit-btn,
.delete-btn {
    display: inline-block;
    padding: 6px 12px;
    font-size: 13px;
    font-weight: 600;
    border-radius: 5px;
    color: #fff;
    text-decoration: none;
    transition: 0.3s;
    margin-right: 5px;
}

/* Edit button */
.edit-btn {
    background-color: #28a745; /* green */
}

.edit-btn:hover {
    background-color: #218838;
}

/* Delete button */
.delete-btn {
    background-color: #dc3545; /* red */
}

.delete-btn:hover {
    background-color: #c82333;
}
</style>
</head>
<body>
    <div class="topbar">
    <h2>Subjects</h2>
    <div><a href="dashboard.php">🏠</a></div>
</div>

<div class="container">

    
    <!-- Add / Edit Form -->
    <h2><?php echo $editingSubject ? 'Edit Subject' : 'Add Subject'; ?></h2>
    <form method="POST">
        <?php if($editingSubject): ?>
            <input type="hidden" name="edit_id" value="<?php echo $editingSubject['id']; ?>">
        <?php endif; ?>
        <input type="text" name="subject_name" placeholder="Subject Name" required value="<?php echo $editingSubject ? htmlspecialchars($editingSubject['subject_name']) : ''; ?>">
        <input type="text" name="subject_code" placeholder="Subject Code" required value="<?php echo $editingSubject ? htmlspecialchars($editingSubject['subject_code']) : ''; ?>">
        <div class="form-submit-wrap">
            <button type="submit" name="save_subject"><?php echo $editingSubject ? 'Update Subject' : 'Add Subject'; ?></button>
            <?php if($editingSubject): ?>
                <a href="./manage_subject.php" class="cancel-btn">Cancel</a>
            <?php endif; ?>
        </div>
    </form>

    <!-- Subjects Table -->
    <h2>All Subjects</h2>
    <table>
        <tr>
            <th>S.No</th>
            <th>Subject Name</th>
            <th>Subject Code</th>
            <th>Action</th>
        </tr>
        <?php $counter=1; while($row=$result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $counter++; ?></td>
            <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
            <td><?php echo htmlspecialchars($row['subject_code']); ?></td>
            <td>
                <a href="?edit=<?php echo $row['id']; ?>">Edit</a>
                <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this subject?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

</div>

</body>
</html>