<?php
session_start();
include("config.php"); // your DB connection

// Sanitize input to prevent XSS / SQL injection
function sanitize($input) {
    return htmlspecialchars(trim($input));
}

// Escape string for SQL safely
function escape($string) {
    global $conn;
    return mysqli_real_escape_string($conn, $string);
}

// Execute a SELECT query and return all results
function getAll($query) {
    global $conn;
    $result = mysqli_query($conn, $query);
    if ($result) return mysqli_fetch_all($result, MYSQLI_ASSOC);
    return [];
}

// Get single row
function getRow($query) {
    global $conn;
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) return mysqli_fetch_assoc($result);
    return null;
}

// Execute INSERT, UPDATE, DELETE
function execute($query) {
    global $conn;
    return mysqli_query($conn, $query);
}

// Get last inserted ID
function lastInsertId() {
    global $conn;
    return mysqli_insert_id($conn);
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['id']) && isset($_SESSION['role']);
}

// Check user role
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

// Redirect to a page
function redirect($url) {
    header("Location: " . $url);
    exit();
}

// Get current user info
function currentUser() {
    if (!isLoggedIn()) return null;
    return [
        'id' => $_SESSION['id'],
        'username' => $_SESSION['username'],
        'role' => $_SESSION['role']
    ];
}

// Format date / datetime
function formatDate($date) {
    return date("d M Y", strtotime($date));
}
function formatDateTime($datetime) {
    return date("d M Y, h:i A", strtotime($datetime));
}

// Password handling
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Login function
function loginUser($username, $email, $password, $role) {
    global $conn;
    $username = escape($username);
    $email = escape($email);
    $role = escape($role);

    $user = getRow("SELECT * FROM users WHERE username='$username' AND email='$email' AND role='$role'");
    if ($user && verifyPassword($password, $user['password'])) {
        $_SESSION['id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        return true;
    }
    return false;
}

// Role-based dashboard redirect
function redirectDashboard() {
    if (!isLoggedIn()) redirect("login.php");

    switch ($_SESSION['role']) {
        case 'admin': redirect("admin/dashboard.php"); break;
        case 'teacher': redirect("teachers/dashboard.php"); break;
        case 'student': redirect("student/dashboard.php"); break;
        default:
            session_unset();
            session_destroy();
            redirect("login.php");
    }
}

// Logout function
function logout() {
    session_unset();
    session_destroy();
    redirect("login.php");
}
?>