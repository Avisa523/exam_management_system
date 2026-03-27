<?php
session_start();

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header('Location: ../authentication/login.php');
    exit;
}

include(__DIR__ . '/../includes/config.php');

$message = '';
$editingStudent = null;

// Approve student
if(isset($_GET['approve'])){
    $id = intval($_GET['approve']);
    $conn->query("UPDATE users SET status='approved' WHERE id='$id'");
    $conn->query("UPDATE students SET status=1 WHERE id='$id'");
}

// Handle Add Student
if(isset($_POST['save_student'])){
    $full_name = $_POST['full_name'];
    $semester = $_POST['semester'];
    $contact_no = $_POST['contact_no'];
    $gender = $_POST['gender'];

    if(isset($_POST['edit_id']) && !empty($_POST['edit_id'])){
        // UPDATE
        $id = intval($_POST['edit_id']);
        $stmt = $conn->prepare("UPDATE students SET full_name=?, semester=?, contact_no=?, gender=? WHERE id=?");
        $stmt->bind_param('ssssi', $full_name, $semester, $contact_no, $gender, $id);
        if($stmt->execute()){
            $message = 'Student updated successfully!';
        }
        $stmt->close();
    } else {
        // ADD
        $stmt = $conn->prepare("INSERT INTO students(full_name, semester, contact_no, gender, status) VALUES(?,?,?,?,1)");
        $stmt->bind_param('ssss', $full_name, $semester, $contact_no, $gender);
        if($stmt->execute()){
            $message = 'Student added successfully!';
        }
        $stmt->close();
    }
}

// Handle Delete
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM students WHERE id=$id");
    header('Location: students.php');
    exit;
}

// Handle Edit
if(isset($_GET['edit'])){
    $id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM students WHERE id=?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $editingStudent = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Fetch all students
$result = $conn->query("SELECT * FROM students ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Students - Admin</title>
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<div class="topbar">
    <div>Exam Management System - Admin</div>
    <div><a href="dashboard.php">🏠 Dashboard</a></div>
</div>

<div class="container">

    <?php if($message): ?>
        <div class="success-msg"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <!-- ADD / EDIT STUDENT FORM -->
    <h2><?php echo $editingStudent ? 'Edit Student' : 'Add Student'; ?></h2>
    <form method="POST">
        <?php if($editingStudent): ?>
            <input type="hidden" name="edit_id" value="<?php echo $editingStudent['id']; ?>">
        <?php endif; ?>
        <input type="text" name="full_name" placeholder="Student Name" required value="<?php echo $editingStudent ? htmlspecialchars($editingStudent['full_name']) : ''; ?>">
        <input type="text" name="semester" placeholder="Semester" required value="<?php echo $editingStudent ? htmlspecialchars($editingStudent['semester']) : ''; ?>">
        <input type="text" name="contact_no" placeholder="Contact Number" required value="<?php echo $editingStudent ? htmlspecialchars($editingStudent['contact_no']) : ''; ?>">
        <select name="gender" required>
            <option value="">Select Gender</option>
            <option value="Male" <?php echo ($editingStudent && $editingStudent['gender']=='Male') ? 'selected' : ''; ?>>Male</option>
            <option value="Female" <?php echo ($editingStudent && $editingStudent['gender']=='Female') ? 'selected' : ''; ?>>Female</option>
            <option value="Other" <?php echo ($editingStudent && $editingStudent['gender']=='Other') ? 'selected' : ''; ?>>Other</option>
        </select>
        <div class="form-submit-wrap">
    <button type="submit" name="save_student">
        <?php echo $editingStudent ? 'Update Student' : 'Add Student'; ?>
    </button>
    <?php if($editingStudent): ?>
        <a href="./manage_student.php" class="cancel-btn">Cancel</a>
    <?php endif; ?>
</div>
    </form>

    <!-- STUDENT TABLE -->
    <h2>All Students</h2>
    <table>
        <tr>
            <th>S.No</th>
            <th>Name</th>
            <th>Semester</th>
            <th>Contact No</th>
            <th>Gender</th>
            <th>Action</th>
        </tr>
        <?php 
        $counter = 1;
        while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $counter++; ?></td>
            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
            <td><?php echo htmlspecialchars($row['semester']); ?></td>
            <td><?php echo htmlspecialchars($row['contact_no']); ?></td>
            <td><?php echo htmlspecialchars($row['gender']); ?></td>
            <td>
                <a class="edit-btn" href="?edit=<?php echo $row['id']; ?>">Edit</a>
                <a class="delete-btn" href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this student?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

</div>
</body>
</html>