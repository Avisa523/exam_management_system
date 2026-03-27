<?php
session_start();
include("../includes/config.php");

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $email = trim($_POST['email']);

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows === 1){
        $user = $result->fetch_assoc();
        $token = bin2hex(random_bytes(16));
        $expires = date("Y-m-d H:i:s", strtotime("+1 hour"));

        // Insert token into password_resets table
        $stmt2 = $conn->prepare("INSERT INTO password_resets(user_id, token, expires_at) VALUES(?,?,?) ON DUPLICATE KEY UPDATE token=?, expires_at=?");
        $stmt2->bind_param("issss", $user['id'], $token, $expires, $token, $expires);
        $stmt2->execute();

        // TODO: Send email to user with link
        // Example: reset_password.php?token=$token

        echo "<script>alert('Password reset link sent. Check your email.'); window.location='login.php';</script>";
        exit;
    } else {
        echo "<script>alert('Email not found'); window.location='forgot_password.php';</script>";
    }
}
?>
<form method="POST" style="width:300px; margin:auto; text-align:center;">
    <h2>Forgot Password 🔒</h2>
    <input type="email" name="email" placeholder="Enter your registered email" required><br><br>
    <input type="submit" value="Send Reset Link">
</form>