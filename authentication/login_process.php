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

            // Only students require admin approval; teachers can log in immediately.
            $status = strtolower($user['status'] ?? 'approved');
            if ($user['role'] === 'student' && $status !== 'approved') {
                echo "<script>alert('Your account is pending admin approval.'); window.location='login.php';</script>";
                exit;
            }

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
                // Check if student has completed registration
                $user_id = $user['id'];
                $stmtReg = $conn->prepare("SELECT completed_registration FROM student_profiles WHERE user_id=?");
                $stmtReg->bind_param("i", $user_id);
                $stmtReg->execute();
                $regRow = $stmtReg->get_result()->fetch_assoc();
                $stmtReg->close();


    if ($regRow['completed_registration'] == 0) {
        // Not registered → force registration
        header("Location: ../student/registration.php");
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