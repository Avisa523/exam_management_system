<?php
session_start();
include("../includes/config.php");

// Ensure student is logged in
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'student'){
    header("Location: ../authentication/login.php");
    exit;
}

// Fetch student profile
$user_id = $_SESSION['id'];
$stmt = $conn->prepare("SELECT * FROM student_profiles WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if(!$result || $result->num_rows === 0){
    header("Location: registration.php");
    exit;
}

$student = $result->fetch_assoc();

// Redirect if registration not completed
if(empty($student['completed_registration']) || $student['completed_registration'] == 0){
    header("Location: registration.php");
    exit;
}

// Assign variables
$username = $_SESSION['username'] ?? 'Student';

$fields = [
    "Full Name" => $student['full_name'] ?? '',
    "Gender" => $student['gender'] ?? '',
    "Dob" => $student['dob'] ?? '',
    "Father's Name" => $student['father_name'] ?? '',
    "Father Phone" => $student['father_phone'] ?? '',
    "Mother's Name" => $student['mother_name'] ?? '',
    "Mother Phone" => $student['mother_phone'] ?? '',
    "Permanent Address" => $student['permanent_address'] ?? '',
    "Current Address" => $student['current_address'] ?? '',
    "Mobile Number" => $student['mobile_no'] ?? '',
    "Email Address" => $student['email'] ?? '',
    "Board / University" => $student['board'] ?? '',
    "Stream" => $student['stream'] ?? '',
    "Year of Passing" => $student['year_of_passing'] ?? '',
    "GPA" => $student['gpa'] ?? '',
    "School/College" => $student['school_college'] ?? '',
];

$photo = $student['profile_photo'] ?? '';
$id_proof = $student['id_proof'] ?? '';
$marksheets = $student['marksheets'] ? unserialize($student['marksheets']) : [];
$signature = $student['signature'] ?? '';

function display_file($file, $folder="students"){
    $path = "../uploads/$folder/".basename($file);
    if(file_exists($path)){
        return '<a href="'.htmlspecialchars($path).'" target="_blank">
                    '.htmlspecialchars($file).'
                </a>';
    }
    return 'Not uploaded';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Profile</title>
<link rel="stylesheet" href="../assets/css/profile.css">
</head>
<body>

<div class="topbar">
    <div class="title">Exam Management System - Profile</div>
    <div class="user">
        <a href="dashboard.php">🏠</a>
        Welcome <?php echo htmlspecialchars($username); ?>
    </div>
</div>

<div class="full-content">
    <div class="dashboard-box">
        <h2>My Profile</h2>

        <?php if($photo && file_exists("../uploads/students/".basename($photo))): ?>
        <div class="profile-photo">
            <img src="../uploads/students/<?php echo htmlspecialchars($photo); ?>" alt="Profile Photo">
        </div>
        <?php endif; ?>

        <table>
            <?php foreach($fields as $label => $value): ?>
            <tr>
                <th><?php echo $label; ?></th>
                <td><?php echo htmlspecialchars($value) ?: 'Not provided'; ?></td>
            </tr>
            <?php endforeach; ?>

            <tr>
                <th>ID Proof</th>
                <td><?php echo $id_proof ? display_file($id_proof) : 'Not uploaded'; ?></td>
            </tr>
            <tr>
                <th>+2 Marksheets</th>
                <td>
                    <?php
                    if($marksheets){
                        foreach($marksheets as $file){
                            echo display_file($file) . "<br>";
                        }
                    } else {
                        echo "Not uploaded";
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th>Signature</th>
                <td><?php echo $signature ? display_file($signature) : 'Not uploaded'; ?></td>
            </tr>
        </table>

    </div>
</div>

</body>
</html>