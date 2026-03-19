<?php
session_start();
include('../includes/config.php');

// Only teachers can see
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'teacher'){
    header('Location: ../authentication/login.php');
    exit;
}

// Fetch notices from database
$notices = $conn->query("SELECT * FROM noticeboard ORDER BY posted_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Teacher Notices</title>
<link rel="stylesheet" href="../assets/css/style.css">
<style>
.notice-box {
    background: #fff;
    border-left: 5px solid #4CAF50;
    padding: 15px;
    margin-bottom: 15px;
    border-radius: 5px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}
.notice-box h3 { color: #0d6efd; margin-bottom: 8px; }
.notice-box p { color: #555; margin-bottom: 5px; }
.notice-box small { color: #888; font-size: 12px; }
</style>
</head>
<header>
    <h1>Notice Board</h1>

<a href="dashboard.php" class="home-icon">🏠</a>

</header>
<body>
<!-- <div class="topbar">
    <div>Teacher Notices</div>
    <div>Welcome <?php echo $_SESSION['username']; ?></div>
</div> -->

<div class="container">
    <h2>Notice Board</h2>
    <?php if($notices->num_rows > 0): ?>
        <?php while($row = $notices->fetch_assoc()): ?> 
            <div class="notice-box">
                <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                <p><?php echo htmlspecialchars($row['message']); ?></p>
                <small><?php echo htmlspecialchars($row['posted_at']); ?></small>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>No notices found.</p>
    <?php endif; ?>
</div>
</body>
</html>