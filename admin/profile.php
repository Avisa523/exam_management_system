<?php
session_start();
if($_SESSION['role'] != 'admin') header('Location: ../index.php');
include('includes/config.php');

$admin_id = $_SESSION['id'];
$admin = $conn->query("SELECT * FROM admin WHERE id='$admin_id'")->fetch_assoc();

if(isset($_POST['update_profile'])){
    $username = $_POST['username'];
    $contact = $_POST['contact_no'];
    $conn->query("UPDATE admin SET username='$username', contact_no='$contact' WHERE id='$admin_id'");
    echo "<p style='color:green'>Profile updated!</p>";
}
?>

<h2>My Profile</h2>
<form method="POST">
<label>Username:</label><input type="text" name="username" value="<?php echo $admin['username']; ?>" required><br>
<label>Contact No:</label><input type="text" name="contact_no" value="<?php echo $admin['contact_no']; ?>"><br>
<button type="submit" name="update_profile">Update Profile</button>
</form>