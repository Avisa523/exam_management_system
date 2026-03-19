<?php
session_start();
include("../includes/config.php");   // <-- Make sure this path matches where config.php is

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role     = trim($_POST['role']);

    // Fetch user from database
    $sql = "SELECT * FROM users WHERE username=? AND email=? AND role=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();

        // ✅ SIMPLE PASSWORD CHECK (supports both hashed and plain passwords)
        if (password_verify($password, $user['password']) || $password === $user['password']) {

            // Store session values
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Role-based redirect (correct paths)
            if ($user['role'] == 'admin') {
                header("Location: ../admin/dashboard.php");
            } elseif ($user['role'] == 'teacher') {
                header("Location: ../teachers/dashboard.php");
            } else {
                 // Check registration status
    $student_id = $user['id'];
    $regQuery = $conn->query("SELECT registration_completed FROM students WHERE id='$student_id'");
    $regRow = $regQuery->fetch_assoc();

    if ($regRow['registration_completed'] == 0) {
        // Not registered → force registration
        header("Location: ../student/register.php");
    } else {
        // Already registered → normal dashboard
                header("Location: ../student/dashboard.php");
            }
            }
            exit();

        } else {
            echo "<script>alert('Wrong Password'); window.location='login.php';</script>";
        }

    } else {
        echo "<script>alert('User Not Found'); window.location='login.php';</script>";
    }
}
?>