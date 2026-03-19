<?php
session_start();
include("../includes/config.php"); // Database connection

// Only logged-in students can access
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'student'){
    header("Location: ../authentication/login.php");
    exit;
}

// Check form submission
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    // Collect personal info
    $name      = trim($_POST['name']);
    $dob       = trim($_POST['dob']);
    $gender    = trim($_POST['gender']);
    $email     = trim($_POST['email']);
    $mobile    = trim($_POST['mobile']);
    
    // Family info
    $father_name    = trim($_POST['father_name']);
    $father_phone   = trim($_POST['father_phone']);
    $mother_name    = trim($_POST['mother_name']);
    $mother_phone   = trim($_POST['mother_phone']);
    $permanent_address = trim($_POST['permanent_address']);
    $current_address   = trim($_POST['current_address']);

    // Academic info
    $board        = trim($_POST['board']);
    $stream       = trim($_POST['stream']);
    $year_of_passing = trim($_POST['year_of_passing']);
    $gpa          = trim($_POST['gpa']);
    $school_college = trim($_POST['school_college']);

    // UPLOAD FILES
    $uploads_dir = "../uploads/"; // Make sure this folder exists and is writable

    // Profile Photo
    if(isset($_FILES['photo']) && $_FILES['photo']['error'] == 0){
        $photo_name = time().'_'.basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], $uploads_dir.$photo_name);
    }else{
        $photo_name = '';
    }

    // ID Proof
    if(isset($_FILES['id_proof']) && $_FILES['id_proof']['error'] == 0){
        $idproof_name = time().'_'.basename($_FILES['id_proof']['name']);
        move_uploaded_file($_FILES['id_proof']['tmp_name'], $uploads_dir.$idproof_name);
    }else{
        $idproof_name = '';
    }

    // +2 Marksheets (multiple files)
    $marksheets_names = [];
    if(isset($_FILES['marksheet'])){
        foreach($_FILES['marksheet']['tmp_name'] as $key => $tmp_name){
            if($_FILES['marksheet']['error'][$key] == 0){
                $mark_name = time().'_'.$key.'_'.basename($_FILES['marksheet']['name'][$key]);
                move_uploaded_file($tmp_name, $uploads_dir.$mark_name);
                $marksheets_names[] = $mark_name;
            }
        }
    }
    $marksheets_json = json_encode($marksheets_names); // store as JSON in DB

    // Character certificate
    if(isset($_FILES['character_certificate']) && $_FILES['character_certificate']['error'] == 0){
        $cc_name = time().'_'.basename($_FILES['character_certificate']['name']);
        move_uploaded_file($_FILES['character_certificate']['tmp_name'], $uploads_dir.$cc_name);
    }else{
        $cc_name = '';
    }

    // Signature
    if(isset($_FILES['signature']) && $_FILES['signature']['error'] == 0){
        $signature_name = time().'_'.basename($_FILES['signature']['name']);
        move_uploaded_file($_FILES['signature']['tmp_name'], $uploads_dir.$signature_name);
    }else{
        $signature_name = '';
    }

    // INSERT into student_profiles table (use prepared statement to prevent SQL injection)
    $user_id = $_SESSION['id']; // logged-in user's ID
    
    $sql = "INSERT INTO student_profiles 
        (user_id, full_name, dob, gender, father_name, mother_name, permanent_address, current_address, mobile_no, profile_photo, id_proof, marksheets, signature, completed_registration)
        VALUES
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)";
    
    $stmt = $conn->prepare($sql);
    if(!$stmt){
        echo "Query Error: " . $conn->error;
        exit;
    }
    
    $stmt->bind_param(
        "issssssssssss",
        $user_id, $name, $dob, $gender, $father_name, $mother_name, 
        $permanent_address, $current_address, $mobile, $photo_name, 
        $idproof_name, $marksheets_json, $signature_name
    );
    
    if($stmt->execute()){
        echo "<script>alert('Registration completed successfully!'); window.location='dashboard.php';</script>";
        exit;
    }else{
        echo "Error: " . $stmt->error;
        exit;
    }

}else{
    header("Location: registration.php"); // if not POST, redirect
    exit;
}
?>