<?php
session_start();
include('../includes/config.php');

if($_SESSION['role'] != 'teacher'){
header("Location: ../authentication/login.php");
exit;
}

$title = $_POST['title'];
$description = $_POST['description'];
$subject_id = $_POST['subject_id'];
$teacher_id = $_SESSION['id'];

$sql = "INSERT INTO question_papers
(subject_id, teacher_id, title, description, created_at)
VALUES
('$subject_id','$teacher_id','$title','$description',NOW())";

if($conn->query($sql)){
echo "Question Uploaded Successfully";
}
else{
echo "Error: ".$conn->error;
}
?>