<?php
session_start();
include('../includes/config.php');

if($_SESSION['role'] != 'teacher'){
header("Location: ../authentication/login.php");
exit;
}

$teacher_id = $_SESSION['id'];

$teacher = $conn->query("SELECT subject_id FROM teachers WHERE id='$teacher_id'")->fetch_assoc();
$subject_id = $teacher['subject_id'];
?>

<h2>Upload Question</h2>

<form action="save.php" method="POST">

<label>Title</label>
<input type="text" name="title" required>

<br><br>

<label>Description</label>
<textarea name="description"></textarea>

<input type="hidden" name="subject_id" value="<?php echo $subject_id; ?>">

<br><br>

<button type="submit">Upload</button>

</form>