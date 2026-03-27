<?php
session_start();

// Only admin can access
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header('Location: ../authentication/login.php');
    exit;
}

include(__DIR__ . '/../includes/config.php');

$message = '';
$editingTeacher = null;

// Handle Add / Update Teacher
if(isset($_POST['save_teacher'])){
    $full_name = $_POST['full_name'];
    $subject = $_POST['subject'];
    $contact_no = $_POST['contact_no'];
    $email = $_POST['email'];

    if(isset($_POST['edit_id']) && !empty($_POST['edit_id'])){
        // UPDATE
        $id = intval($_POST['edit_id']);
        $stmt = $conn->prepare("UPDATE teachers SET full_name=?, subject=?, contact_no=?, email=? WHERE id=?");
        $stmt->bind_param("ssssi", $full_name, $subject, $contact_no, $email, $id);
        if($stmt->execute()){
            $message = 'Teacher updated successfully!';
        }
        $stmt->close();
    } else {
        // ADD
        $stmt = $conn->prepare("INSERT INTO teachers(full_name, subject, contact_no, email, status) VALUES(?,?,?,?,1)");
        $stmt->bind_param("ssss", $full_name, $subject, $contact_no, $email);
        if($stmt->execute()){
            $message = 'Teacher added successfully!';
        }
        $stmt->close();
    }
}

// Handle Delete
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM teachers WHERE id=$id");
    header('Location: teachers.php');
    exit;
}

// Handle Edit
if(isset($_GET['edit'])){
    $id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM teachers WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $editingTeacher = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

// Fetch all teachers
$result = $conn->query("SELECT * FROM teachers ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Teachers - Admin</title>
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<!-- TOPBAR -->
<div class="topbar">
    <div>Exam Management System - Admin</div>
    <div><a href="dashboard.php">🏠 Dashboard</a></div>
</div>

<div class="container">

    <?php if($message): ?>
        <div class="success-msg"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <!-- ADD / EDIT TEACHER FORM -->
    <h2><?php echo $editingTeacher ? 'Edit Teacher' : 'Add Teacher'; ?></h2>
    <form method="POST">
        <?php if($editingTeacher): ?>
            <input type="hidden" name="edit_id" value="<?php echo $editingTeacher['id']; ?>">
        <?php endif; ?>
        <input type="text" name="full_name" placeholder="Teacher Name" required value="<?php echo $editingTeacher ? htmlspecialchars($editingTeacher['full_name']) : ''; ?>">
        <input type="text" name="subject" placeholder="Subject" required value="<?php echo $editingTeacher ? htmlspecialchars($editingTeacher['subject']) : ''; ?>">
        <input type="text" name="contact_no" placeholder="Contact Number" required value="<?php echo $editingTeacher ? htmlspecialchars($editingTeacher['contact_no']) : ''; ?>">
        <input type="email" name="email" placeholder="Email" required value="<?php echo $editingTeacher ? htmlspecialchars($editingTeacher['email']) : ''; ?>">
        <button type="submit" name="save_teacher"><?php echo $editingTeacher ? 'Update Teacher' : 'Add Teacher'; ?></button>
        <?php if($editingTeacher): ?>
            <a href="teachers.php" class="cancel-btn">Cancel</a>
        <?php endif; ?>
    </form>

    <!-- TEACHER TABLE -->
    <h2>All Teachers</h2>
    <table>
        <tr>
            <th>S.No</th>
            <th>Name</th>
            <th>Subject</th>
            <th>Contact No</th>
            <th>Email</th>
            <th>Action</th>
        </tr>
        <?php 
        $counter = 1;
        while($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $counter++; ?></td>
            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
            <td><?php echo htmlspecialchars($row['subject']); ?></td>
            <td><?php echo htmlspecialchars($row['contact_no']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td>
                <a class="edit-btn" href="?edit=<?php echo $row['id']; ?>">Edit</a>
                <a class="delete-btn" href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this teacher?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

</div>
</body>
</html>