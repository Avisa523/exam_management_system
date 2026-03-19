<?php
session_start();
include("../includes/config.php");

// When a new user registers, we create an account in users table with status="pending".
// Admin can later approve this user via the Manage Student page.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $role = trim($_POST['role']);

    // Basic validation
    if (!$fullname || !$email || !$password || !$confirm_password || !$role) {
        echo "<script>alert('Please fill all required fields.'); window.location='register.php';</script>";
        exit;
    }

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match.'); window.location='register.php';</script>";
        exit;
    }

    if (!in_array($role, ['student', 'teacher', 'admin'])) {
        echo "<script>alert('Invalid role selected.'); window.location='register.php';</script>";
        exit;
    }

    // Prevent duplicate registration by email
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Email already registered. Please login.'); window.location='login.php';</script>";
        exit;
    }

    // Add status column if missing (safe fallback)
    $hasStatus = $conn->query("SHOW COLUMNS FROM users LIKE 'status'")->num_rows > 0;
    if (!$hasStatus) {
        $conn->query("ALTER TABLE users ADD COLUMN status VARCHAR(20) DEFAULT 'pending'");
    }

    $username = preg_replace('/\s+/', '_', strtolower($fullname));
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Students require admin approval. Teacher/admin accounts are auto-approved.
    $status = $role === 'student' ? 'pending' : 'approved';

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $username, $email, $passwordHash, $role, $status);

    if ($stmt->execute()) {
        echo "<script>alert('Registration submitted successfully. Please wait for admin approval.'); window.location='login.php';</script>";
        exit;
    }

    echo "<script>alert('Registration failed: " . addslashes($stmt->error) . "'); window.location='register.php';</script>";
}
?>