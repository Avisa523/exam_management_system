<?php
session_start();
include("../includes/config.php");

// CHECK LOGIN
if(!isset($_SESSION['id'])){
    header("Location: ../authentication/login.php");
    exit;
}

$user_id = $_SESSION['id'];

// CHECK REGISTRATION STATUS
$result = $conn->query("SELECT registration_completed FROM students WHERE id='$user_id'");
$row = $result->fetch_assoc();

// IF NOT REGISTERED → SHOW MESSAGE
if(!$row || $row['registration_completed'] == 0){
?>
<!DOCTYPE html>
<html>
<head>
    <title>Complete Registration</title>
    <style>
        body{
            font-family: Arial;
            text-align:center;
            padding:100px;
            background:#f5f6f8;
        }
        .box{
            background:#fff;
            padding:40px;
            border-radius:8px;
            display:inline-block;
        }
        a{
            background:#0d6efd;
            color:#fff;
            padding:12px 25px;
            text-decoration:none;
            border-radius:5px;
        }
    </style>
</head>
<body>

<div class="box">
    <h2>Please Complete Your Registration First</h2>
    <br>
    <a href="register.php">Complete Registration</a>
</div>

</body>
</html>
<?php
exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

<div class="topbar">
    <div class="title">Exam Management System</div>
    <div class="user">Welcome <?php echo $_SESSION['username']; ?></div>
</div>

<div class="container-layout">

    <div class="left-column">

        <div class="logo">
            <img src="../assets/images/everest logo.png" width="120">
        </div>

        <div class="sidebar">
            <ul>
                <li><a href="dashboard.php">🏠 Dashboard</a></li>
                <li><a href="register.php">📝 Registration</a></li>
                <li><a href="exam_schedule.php">📅 Exam Schedule</a></li>
                <li><a href="notices.php">📢 Notices</a></li>
                <li><a href="result.php">📊 Result</a></li>
                <li><a href="../authentication/logout.php">🔒 Logout</a></li>
            </ul>
        </div>

    </div>

    <div class="right-column">
        <div class="dashboard-box">

            <h2>Dashboard</h2>
            <p>Welcome <?php echo $_SESSION['username']; ?></p>

            <div class="cards">

                <div class="card blue">
                    <h3>Exam Schedule</h3>
                    <a href="exam_schedule.php">Open</a>
                </div>

                <div class="card green">
                    <h3>Notices</h3>
                    <a href="notices.php">Open</a>
                </div>

                <div class="card orange">
                    <h3>Results</h3>
                    <a href="result.php">Open</a>
                </div>

            </div>

        </div>
    </div>

</div>

</body>
</html>