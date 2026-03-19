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
    "Full Name (Devnagari)" => $student['full_name_dev'] ?? '',
    "Gender" => $student['gender'] ?? '',
    "Marital Status" => $student['marital_status'] ?? '',
    "Birth Date (BS)" => $student['dob_bs'] ?? '',
    "Birth Date (AD)" => $student['dob'] ?? '',
    "Citizenship No" => $student['citizenship_no'] ?? '',
    "District" => $student['district'] ?? '',
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
<style>
body { font-family: Arial, sans-serif; background:#f5f6f8; margin:0; padding:0; color:#333; }
.topbar { display:flex; justify-content:space-between; align-items:center; background:#1a73e8; color:#fff; padding:15px 30px; }
.topbar .title { font-size:18px; font-weight:bold; }
.topbar .user a.back-button { color:#fff; text-decoration:none; margin-right:15px; font-size:20px; }

.full-content { max-width:1000px; margin:30px auto; padding:0 20px; }
.dashboard-box { background:#fff; padding:25px 30px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1); }
.dashboard-box h2 { font-size:22px; margin-bottom:20px; border-bottom:2px solid #eee; padding-bottom:10px; text-align:center; }

.profile-photo { text-align:center; margin-bottom:20px; }
.profile-photo img { width:150px; height:150px; object-fit:cover; border-radius:50%; border:2px solid #1a73e8; }

table { width:100%; border-collapse:collapse; margin-top:20px; }
table, th, td { border:1px solid #ddd; }
th, td { padding:10px 15px; text-align:left; vertical-align:top; }
th { background:#f2f2f2; width:30%; }
.uploaded-img { max-width:150px; height:auto; margin-top:5px; border:1px solid #ccc; border-radius:4px; }
</style>
</head>
<body>

<div class="topbar">
    <div class="title">Exam Management System - Profile</div>
    <div class="user">
        <a href="dashboard.php" class="back-button">🏠</a>
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