<?php
session_start();
include("../includes/config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $role = 'student'; // only students register here
    $gender = trim($_POST['gender'] ?? 'Other');

    if (!$fullname || !$email || !$contact || !$password || !$confirm_password) {
        echo "<script>alert('Please fill all fields.'); window.location='register.php';</script>";
        exit;
    }

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match.'); window.location='register.php';</script>";
        exit;
    }

    // Prevent duplicate
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo "<script>alert('Email already registered. Please login.'); window.location='login.php';</script>";
        exit;
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $username = preg_replace('/\s+/', '_', strtolower($fullname));
    $status = 'pending';

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $username, $email, $passwordHash, $role, $status);

    if ($stmt->execute()) {
        $user_id = $conn->insert_id;

        $stmtStudent = $conn->prepare("INSERT INTO students (user_id, full_name, contact_no, gender, status, approved) VALUES (?, ?, ?, ?, 'pending', 0)");
        $stmtStudent->bind_param("isss", $user_id, $fullname, $contact, $gender);
        $stmtStudent->execute();
        $stmtStudent->close();

        echo "<script>alert('Registration submitted successfully. Please wait for admin approval.'); window.location='login.php';</script>";
        exit;
    } else {
        echo "<script>alert('Registration failed: " . addslashes($stmt->error) . "'); window.location='register.php';</script>";
        exit;
    }
}
?>