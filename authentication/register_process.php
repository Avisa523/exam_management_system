<?php
session_start();
include("../includes/config.php"); // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Match HTML form names
    $fullname = trim($_POST['full_name']);   // for students table
    $username = $fullname;                  // goes to users.username
    $email = trim($_POST['email']);
    $contact = trim($_POST['contact']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $role = trim($_POST['role']);

    // Check password match
    if ($password != $confirm_password) {
        echo "<script>alert('Passwords do not match'); window.location='register.php';</script>";
        exit();
    }

     // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo "<script>alert('Email already registered. Try login.'); window.location='register.php';</script>";
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert into users table
    $stmt = $conn->prepare("INSERT INTO users (username, email, contact, password, role, status) VALUES (?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("sssss", $username, $email, $contact, $hashed_password, $role);

    if ($stmt->execute()) {

        $user_id = $stmt->insert_id;

        // If student → insert into students table
        if ($role == 'student') {
            $stmt2 = $conn->prepare("INSERT INTO students (id, full_name, registration_completed, approved) VALUES (?, ?, 0, 0)");
            $stmt2->bind_param("is", $user_id, $fullname);
            $stmt2->execute();
        }

        echo "<script>alert('Registered Successfully! Wait for admin approval'); window.location='login.php';</script>";
        exit();

    } else {
        echo "Error: " . $stmt->error;
        exit();
    }

    // Fetch user from database
    $sql = "SELECT * FROM users WHERE full_name=? AND email=? AND role=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $full_name, $email, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify hashed password
        if (password_verify($password, $user['password'])) {
            // Login successful
            $_SESSION['id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];

            // Role-based redirect
            if ($user['role'] == 'admin') {
                header("Location: admin/dashboard.php");
            } elseif ($user['role'] == 'teacher') {
                header("Location: teachers/dashboard.php");
            } else {
                // Check if student completed registration
    $student_id = $user['id'];
    $regCheck = $conn->query("SELECT registration_completed FROM students WHERE id='$student_id'");
    $regRow = $regCheck->fetch_assoc();

    if ($regRow['registration_completed'] == 0) {
        // Not registered → redirect to registration form
        header("Location: student/register.php");
    } else {
        // Already registered → dashboard
                header("Location: student/dashboard.php");
            }
            }
            exit();
        } else {
            // Invalid password
            echo "<script>alert('Invalid Login Credentials'); window.location='login.php';</script>";
        }
    } else {
        // User not found
        echo "<script>alert('Invalid Login Credentials'); window.location='login.php';</script>";
    }
}
?>