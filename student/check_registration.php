<?php
session_start();
include("../includes/config.php");


if(!isset($_SESSION['role']) || $_SESSION['role'] != 'student'){
    header("Location: ../authentication/login.php");
    exit;
}

// Fetch registration status
$user_id = $_SESSION['id'];
$sql = "SELECT completed_registration FROM student_profiles WHERE user_id='$user_id'";
$result = $conn->query($sql);

if($result && $row = $result->fetch_assoc()){
    if($row['completed_registration'] == 1){
        // Already registered → allow access to dashboard
        // Don't redirect
    } else {
        // Not registered → force registration
        header("Location: registration.php");
        exit;
    }
} else {
    // No profile found → force registration
    header("Location: registration.php");
    exit;
}
?>