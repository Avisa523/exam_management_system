<?php
session_start();

// Only admin can access
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header('Location: ../authentication/login.php');
    exit;
}

include(__DIR__ . '/../includes/config.php');

/* ADD SUBJECT */
if(isset($_POST['add_subject'])){
    $subject_name = $_POST['subject_name'];
    $subject_code = $_POST['subject_code'];
    $status = 1;

    $conn->query("INSERT INTO subjects(subject_name, subject_code, status) 
                  VALUES('$subject_name', '$subject_code', '$status')");
}

/* DELETE SUBJECT */
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $conn->query("DELETE FROM subjects WHERE id=$id");
}

/* FETCH SUBJECTS */
$result = $conn->query("SELECT * FROM subjects ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Subjects - Admin</title>
<style>
/* RESET */
* {margin:0; padding:0; box-sizing:border-box; font-family: Arial, sans-serif;}
body {background-color: #f4f6f8; color: #333;}

/* TOPBAR */
.topbar {
    height: 60px;
    background-color: #1e90ff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 20px;
    color: white;
    font-weight: bold;
    font-size: 20px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}
.topbar a {color: white; text-decoration: none; font-size: 22px;}

/* MAIN CONTAINER */
.container {
    padding: 20px 40px;
}

/* FORM */
form {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 25px;
    background: white;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

form input, form button {
    padding: 8px 10px;
    font-size: 13px;
    border-radius: 4px;
    border: 1px solid #ccc;
}

form input {flex: 1 1 200px;}
form button {
    padding: 7px 12px;
    font-size: 13px;
    background-color: #1e90ff;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

form button:hover {background-color: #0d6efd;}

/* TABLE */
table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

th, td {
    padding: 12px 15px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

th {background-color: #1e90ff; color: white;}
tr:hover {background-color: #f1f1f1;}

/* ACTION BUTTONS */
.edit-btn, .delete-btn {
    padding: 5px 8px;
    border-radius: 4px;
    color: white;
    text-decoration: none;
    font-size: 13px;
    transition: 0.3s;
}
.edit-btn {background-color: #28a745;}
.delete-btn {background-color: #dc3545;}
.edit-btn:hover, .delete-btn:hover {opacity: 0.8;}

/* RESPONSIVE */
@media (max-width: 768px) {
    form {flex-direction: column;}
    table, th, td {font-size: 13px;}
}
</style>
</head>
<body>

<!-- TOPBAR -->
<div class="topbar">
    <div>Exam Management System - Admin</div>
    <div><a href="dashboard.php">🏠 Dashboard</a></div>
</div>

<div class="container">

    <!-- ADD SUBJECT FORM -->
    <form method="POST">
        <input type="text" name="subject_name" placeholder="Subject Name" required>
        <input type="text" name="subject_code" placeholder="Subject Code" required>
        <button type="submit" name="add_subject">Add Subject</button>
    </form>

    <!-- SUBJECT TABLE -->
    <table>
        <tr>
            <th>S.No</th>
            <th>Subject Name</th>
            <th>Subject Code</th>
            <th>Action</th>
        </tr>

        <?php 
        $counter = 1;
        while($row = $result->fetch_assoc()){ ?>
        <tr>
            <td><?php echo $counter++; ?></td>
            <td><?php echo $row['subject_name']; ?></td>
            <td><?php echo $row['subject_code']; ?></td>
            <td>
                <a class="edit-btn" href="#">Edit</a>
                <a class="delete-btn" href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this subject?')">Delete</a>
            </td>
        </tr>
        <?php } ?>
    </table>

</div>
</body>
</html>