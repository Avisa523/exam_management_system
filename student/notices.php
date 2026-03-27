<?php
session_start();
include('../includes/config.php');

if(!isset($_SESSION['role'])){
    header("Location: ../authentication/login.php");
    exit;
    // 2. Check if registration is completed
$user_id = $_SESSION['id'];
$result = $conn->query("SELECT completed_registration FROM student_profiles WHERE user_id='$user_id'");

if($result && $row = $result->fetch_assoc()){
    if($row['completed_registration'] == 0){
        // Not registered → force registration
        header("Location: registration.php");
        exit;
    }
} else {
    // No profile found → force registration
    header("Location: registration.php");
    exit;
}
}

$role = $_SESSION['role'];
$notices = $conn->query("SELECT * FROM noticeboard ORDER BY posted_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Noticeboard</title>
    <link rel="stylesheet" href="../assets/css/student.css">
    
</head>
<body>

<!-- Header Bar -->
<div class="notice-header-bar">
    <h1>Noticeboard</h1>
    <a href="dashboard.php" class="home-icon">🏠</a>
</div>

<!-- Main Content -->
<div class="main-content">

    <?php if($notices && $notices->num_rows > 0): ?>
        <div class="notice-cards">
            <?php while($n = $notices->fetch_assoc()): ?>
                <div class="notice-box">
                    <h3><?php echo htmlspecialchars($n['title']); ?></h3>
                    <p><?php echo nl2br(htmlspecialchars($n['message'])); ?></p>
                    <small>Posted: <?php echo htmlspecialchars($n['posted_at']); ?></small>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="notice-box">
            <h3>No notices available.</h3>
        </div>
    <?php endif; ?>

</div>

<?php include('../includes/footer.php'); ?>

</body>
</html>