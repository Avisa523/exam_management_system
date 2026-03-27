<?php
session_start();
include("../includes/config.php");

// Only allow logged-in students
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'student'){
    header("Location: ../authentication/login.php");
    exit;
}
 
// // File size limits (in bytes)
// $maxIDProof = 2 * 1024 * 1024;       // 2MB
// $maxMarksheet = 5 * 1024 * 1024;     // 5MB
// $maxPhoto = 1 * 1024 * 1024;         // 1MB
// $maxSignature = 500 * 1024;          // 500KB

// // Check if form is submitted
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {

//     // ID Proof
//     if (isset($_FILES['id_proof']) && $_FILES['id_proof']['size'] > $maxIDProof) {
//         die("ID Proof file size exceeds 2MB!");
//     }

//     // +2 Marksheets (multiple files)
//     if (isset($_FILES['marksheet'])) {
//         foreach ($_FILES['marksheet']['size'] as $size) {
//             if ($size > $maxMarksheet) {
//                 die("+2 Marksheets file size exceeds 5MB!");
//             }
//         }
//     }

//     // Profile photo
//     if (isset($_FILES['photo']) && $_FILES['photo']['size'] > $maxPhoto) {
//         die("Profile photo exceeds 1MB!");
//     }

//     // Signature
//     if (isset($_FILES['signature']) && $_FILES['signature']['size'] > $maxSignature) {
//         die("Signature file exceeds 500KB!");
//     } 
// }
$user_id = $_SESSION['id'];

// Collect data
$full_name = $_POST['name'];
$dob = $_POST['dob'];
$gender = $_POST['gender'];
$father_name = $_POST['father_name'];
$mother_name = $_POST['mother_name'];
$permanent_address = $_POST['permanent_address'];
$current_address = $_POST['current_address'];
$mobile_no = $_POST['mobile'];
$course = $_POST['board'] ?? NULL;
$semester = $_POST['stream'] ?? NULL; // map stream to semester or field
$section = $_POST['year_of_passing'] ?? NULL; // optional

// Handle file uploads
$uploads_dir = "../uploads/students/";
if(!file_exists($uploads_dir)) mkdir($uploads_dir, 0777, true);

$profile_photo = $_FILES['photo']['name'] ?? NULL;
if($profile_photo) move_uploaded_file($_FILES['photo']['tmp_name'], $uploads_dir.$profile_photo);

$id_proof = $_FILES['id_proof']['name'] ?? NULL;
if($id_proof) move_uploaded_file($_FILES['id_proof']['tmp_name'], $uploads_dir.$id_proof);

$marksheets = [];
if(!empty($_FILES['marksheet']['name'][0])){
    foreach($_FILES['marksheet']['name'] as $key => $filename){
        move_uploaded_file($_FILES['marksheet']['tmp_name'][$key], $uploads_dir.$filename);
        $marksheets[] = $filename;
    }
}
$marksheets_serialized = serialize($marksheets);

$signature = $_FILES['signature']['name'] ?? NULL;
if($signature) move_uploaded_file($_FILES['signature']['tmp_name'], $uploads_dir.$signature);

// Mark registration complete
$completed = 1;

// First, check if profile exists - if not, create it
$checkStmt = $conn->prepare("SELECT id FROM student_profiles WHERE user_id=?");
$checkStmt->bind_param("i", $user_id);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows === 0) {
    // Insert new row if it doesn't exist
    $insertStmt = $conn->prepare("INSERT INTO student_profiles (user_id, completed_registration) VALUES (?, 0)");
    $insertStmt->bind_param("i", $user_id);
    $insertStmt->execute();
    $insertStmt->close();
}
$checkStmt->close();

// Now update with all the registration data
$stmt = $conn->prepare("UPDATE student_profiles SET 
    full_name=?, dob=?, gender=?, father_name=?, mother_name=?, permanent_address=?, current_address=?, mobile_no=?,
    course=?, semester=?, section=?, profile_photo=?, id_proof=?, marksheets=?, signature=?, completed_registration=?
    WHERE user_id=?");

$stmt->bind_param("ssssssssssssssssi",
    $full_name,$dob,$gender,$father_name,$mother_name,$permanent_address,$current_address,$mobile_no,
    $course,$semester,$section,$profile_photo,$id_proof,$marksheets_serialized,$signature,$completed,$user_id
);

if($stmt->execute()){
    // ✅ After saving, redirect to dashboard
    header("Location: ../student/dashboard.php");
    exit;
}else{
    echo "Error: ".$stmt->error;
}
?>