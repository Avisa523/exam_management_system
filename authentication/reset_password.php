<?php
session_start();
include("../includes/config.php");

$token = $_GET['token'] ?? '';

if (!$token) {
    die("Invalid password reset link.");
}

$stmt = $conn->prepare("SELECT pr.user_id, pr.expires_at, u.email FROM password_resets pr JOIN users u ON pr.user_id=u.id WHERE pr.token=?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Invalid or expired link.");
}

$resetData = $result->fetch_assoc();
$expires = strtotime($resetData['expires_at']);
if (time() > $expires) {
    die("This reset link has expired.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = trim($_POST['password']);
    $confirm  = trim($_POST['confirm_password']);

    if ($password !== $confirm) {
        echo "<script>alert('Passwords do not match');</script>";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt2 = $conn->prepare("UPDATE users SET password=? WHERE id=?");
        $stmt2->bind_param("si", $hash, $resetData['user_id']);
        $stmt2->execute();

        // Delete token
        $stmt3 = $conn->prepare("DELETE FROM password_resets WHERE user_id=?");
        $stmt3->bind_param("i", $resetData['user_id']);
        $stmt3->execute();

        echo "<script>alert('Password successfully changed'); window.location='login.php';</script>";
        exit;
    }
}
?>

<form method="POST" style="width:300px; margin:auto; text-align:center;">
    <h2>Reset Password 🔑</h2>
    <input type="password" name="password" placeholder="New Password" required><br><br>
    <input type="password" name="confirm_password" placeholder="Confirm Password" required><br><br>
    <input type="submit" value="Reset Password">
</form>