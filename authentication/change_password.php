<?php
session_start();
include("../includes/config.php");

// Check if user is logged in
if (!isset($_SESSION['id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $current = trim($_POST['current_password']);
    $new = trim($_POST['new_password']);
    $confirm = trim($_POST['confirm_password']);
    $user_id = $_SESSION['id'];

    if (!$current || !$new || !$confirm) {
        $message = "All fields are required.";
    } elseif ($new !== $confirm) {
        $message = "New password and confirmation do not match.";
    } else {
        // Fetch current password from DB
        $stmt = $conn->prepare("SELECT password FROM users WHERE id=?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($row && (password_verify($current, $row['password']) || $current === $row['password'])) {
            // Update password
            $newHash = password_hash($new, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
            $stmt->bind_param("si", $newHash, $user_id);
            if ($stmt->execute()) {
                $message = "Password changed successfully!";
            } else {
                $message = "Failed to update password.";
            }
            $stmt->close();
        } else {
            $message = "Current password is incorrect.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Change Password 🔒</title>
<script src="../assets/js/registration.js"></script>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="container">
    <h2>Change Password 🔒</h2>
    <form method="POST" action="change_password_process.php">
    <h2>Change Password</h2>

    <label for="old_password">Old Password:</label>
    <div style="position:relative;">
        <input type="password" id="old_password" name="old_password" placeholder="Enter old password" required>
        <span onclick="togglePassword('old_password')" 
              style="position:absolute; right:10px; top:50%; transform:translateY(-50%); cursor:pointer;">
            👁️
        </span>
    </div>

    <label for="new_password">New Password:</label>
    <div style="position:relative;">
        <input type="password" id="new_password" name="new_password" placeholder="Enter new password" required>
        <span onclick="togglePassword('new_password')" 
              style="position:absolute; right:10px; top:50%; transform:translateY(-50%); cursor:pointer;">
            👁️
        </span>
    </div>

    <label for="confirm_password">Confirm New Password:</label>
    <div style="position:relative;">
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm new password" required>
        <span onclick="togglePassword('confirm_password')" 
              style="position:absolute; right:10px; top:50%; transform:translateY(-50%); cursor:pointer;">
            👁️
        </span>
    </div>

    <input type="submit" value="Change Password">
</form>

<script>
function togglePassword(id) {
    let pass = document.getElementById(id);
    if(pass.type === "password") {
        pass.type = "text";
    } else {
        pass.type = "password";
    }
}
</script>
</div>
</body>
</html>