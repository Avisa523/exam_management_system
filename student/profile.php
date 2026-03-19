<?php
session_start();
include("../includes/config.php"); // Database connection

// Make sure user is logged in as student
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'student'){
    header("Location: ../authentication/login.php");
    exit;
}

// --- Check if registration is completed ---
$student_id = $_SESSION['id'];
$regCheck = $conn->query("SELECT * FROM students WHERE id='$student_id'");
if($regCheck && $student = $regCheck->fetch_assoc()){
    if($student['registration_completed'] == 0){
        // Registration not done → redirect
        header("Location: registration.php");
        exit;
    }
} else {
    // Student record missing → redirect to registration
    header("Location: registration.php");
    exit;
}

// --- Fetch student info to display ---
$username = $_SESSION['username'];
$full_name = $student['full_name'];
$dob = $student['dob'] ?? '';
$gender = $student['gender'] ?? '';
$email = $_SESSION['email'] ?? '';
$mobile = $student['mobile'] ?? '';
$board = $student['board'] ?? '';
$stream = $student['stream'] ?? '';
$year_of_passing = $student['year_of_passing'] ?? '';
$gpa = $student['gpa'] ?? '';
$school_college = $student['school_college'] ?? '';
$photo = $student['photo'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Profile | Student Dashboard</title>
<link rel="stylesheet" href="../assets/css/dashboard.css">
<style>
.profile-container{
    max-width:800px;
    margin:30px auto;
    padding:20px;
    background:#fff;
    border-radius:8px;
    box-shadow:0 5px 15px rgba(0,0,0,0.1);
}
.profile-container h2{
    color:#0d6efd;
    margin-bottom:20px;
    text-align:center;
}
.profile-row{
    display:flex;
    gap:20px;
    margin-bottom:15px;
}
.profile-label{
    width:150px;
    font-weight:bold;
    color:#333;
}
.profile-value{
    flex:1;
}
.profile-photo{
    text-align:center;
    margin-bottom:20px;
}
.profile-photo img{
    width:150px;
    height:150px;
    object-fit:cover;
    border-radius:50%;
    border:2px solid #0d6efd;
}
</style>
</head>
<body>

<?php include('dashboard_header.php'); // optional: include your topbar/sidebar ?>

<div class="profile-container">
    <h2>My Profile</h2>

    <?php if($photo): ?>
    <div class="profile-photo">
        <img src="../uploads/<?php echo htmlspecialchars($photo); ?>" alt="Profile Photo">
    </div>
    <?php endif; ?>

    <div class="profile-row">
        <div class="profile-label">Full Name:</div>
        <div class="profile-value"><?php echo htmlspecialchars($full_name); ?></div>
    </div>

    <div class="profile-row">
        <div class="profile-label">Date of Birth:</div>
        <div class="profile-value"><?php echo htmlspecialchars($dob); ?></div>
    </div>

    <div class="profile-row">
        <div class="profile-label">Gender:</div>
        <div class="profile-value"><?php echo htmlspecialchars($gender); ?></div>
    </div>

    <div class="profile-row">
        <div class="profile-label">Email:</div>
        <div class="profile-value"><?php echo htmlspecialchars($email); ?></div>
    </div>

    <div class="profile-row">
        <div class="profile-label">Mobile:</div>
        <div class="profile-value"><?php echo htmlspecialchars($mobile); ?></div>
    </div>

    <div class="profile-row">
        <div class="profile-label">Board / University:</div>
        <div class="profile-value"><?php echo htmlspecialchars($board); ?></div>
    </div>

    <div class="profile-row">
        <div class="profile-label">Stream:</div>
        <div class="profile-value"><?php echo htmlspecialchars($stream); ?></div>
    </div>

    <div class="profile-row">
        <div class="profile-label">Year of Passing:</div>
        <div class="profile-value"><?php echo htmlspecialchars($year_of_passing); ?></div>
    </div>

    <div class="profile-row">
        <div class="profile-label">GPA:</div>
        <div class="profile-value"><?php echo htmlspecialchars($gpa); ?></div>
    </div>

    <div class="profile-row">
        <div class="profile-label">School/College:</div>
        <div class="profile-value"><?php echo htmlspecialchars($school_college); ?></div>
    </div>
</div>

</body>
</html>