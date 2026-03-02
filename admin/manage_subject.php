<?php
session_start();

// Only admin can access
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header('Location: ../authentication/login.php');
    exit;
}

include(__DIR__ . '/../includes/config.php');

// Initialize variables
$editingSubject = null;
$result = null;

// Handle Add / Update Subject
if(isset($_POST['save_subject'])){
    $subject_name = $_POST['subject_name'] ?? '';
    $subject_code = $_POST['subject_code'] ?? '';

    if(isset($_POST['edit_id']) && !empty($_POST['edit_id'])){
        // UPDATE
        $id = intval($_POST['edit_id']);
        $stmt = $conn->prepare("UPDATE subjects SET subject_name=?, subject_code=? WHERE id=?");
        $stmt->bind_param("ssi", $subject_name, $subject_code, $id);
        if($stmt->execute()){
            header('Location: manage_subject.php?success=updated');
            exit;
        }
        $stmt->close();
    } else {
        // ADD
        $stmt = $conn->prepare("INSERT INTO subjects(subject_name, subject_code, status) VALUES(?,?,1)");
        $stmt->bind_param("ss", $subject_name, $subject_code);
        if($stmt->execute()){
            header('Location: manage_subject.php?success=added');
            exit;
        }
        $stmt->close();
    }
}

// Handle Delete
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM subjects WHERE id=$id");
    header('Location: manage_subject.php?success=deleted');
    exit;
}

// Handle Edit
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
if(!$result){
    $result = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Subjects - Admin</title>
<style>
body {
    font-family: Arial, sans-serif;
    background: #f5f5f5;
    margin: 0;
    padding: 0;
}

/* TOPBAR */
.topbar {
    background: #0d6efd;
    color: white;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.topbar h2 { margin: 0; }
.topbar a { color: white; font-size: 20px; text-decoration: none; }

/* CONTAINER */
.container {
    max-width: 900px;
    margin: 30px auto;
    background: white;
    padding: 30px 40px;
    border-radius: 8px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
}

/* FORM */
form {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-bottom: 30px;
}
form input[type="text"] {
    padding: 10px;
    font-size: 14px;
    border: 1px solid #ccc;
    border-radius: 5px;
}
.form-submit-wrap {
    display: flex;
    gap: 10px;
}
form button, .cancel-btn {
    padding: 10px 20px;
    border-radius: 5px;
    border: none;
    cursor: pointer;
    text-decoration: none;
    color: white;
    font-weight: bold;
    transition: 0.3s;
}
form button { background: blue; }
form button:hover { background: blue; }
.cancel-btn { background: #6c757d; display: inline-block; }
.cancel-btn:hover { background: #5a6268; }

/* TABLE */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}
table th, table td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}
table th {
    background: #0d6efd;
    color: white;
}
table tr:nth-child(even) { background: #f9f9f9; }

/* ACTION BUTTONS */
.edit-btn, .delete-btn {
    display: inline-block;
    padding: 6px 12px;
    font-size: 13px;
    font-weight: 600;
    border-radius: 5px;
    color: white;
    text-decoration: none;
    transition: 0.3s;
    margin-right: 5px;
}
.edit-btn { background-color: #28a745; }
.edit-btn:hover { background-color: #218838; }
.delete-btn { background-color: #dc3545; }
.delete-btn:hover { background-color: #c82333; }

/* RESPONSIVE */
@media(max-width:600px){
    .topbar { flex-direction: column; align-items: flex-start; gap: 10px; }
    .form-submit-wrap { flex-direction: column; }
}
</style>
</head>
<body>

<div class="topbar">
    <h2>Subjects</h2>
    <div><a href="dashboard.php">🏠 Dashboard</a></div>
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
                <a href="?edit=<?php echo $row['id']; ?>" class="edit-btn">Edit</a>
                <a href="?delete=<?php echo $row['id']; ?>" class="delete-btn" onclick="return confirm('Delete this subject?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

</div>

</body>
</html>