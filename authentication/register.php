<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
*{
    box-sizing: border-box;
    font-family: Arial, sans-serif;
}

body{
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: linear-gradient(to right, #0d6efd, #6c63ff);
}

.register-container{
    background: #fff;
    padding: 30px;
    width: 420px;
    border-radius: 10px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
}

h1{
    text-align: center;
    margin-bottom: 20px;
    color: #0d6efd;
}

label{
    font-weight: bold;
    margin-top: 10px;
    display: block;
}

input, select{
    width: 100%;
    padding: 10px;
    margin-top: 5px;
    border-radius: 5px;
    border: 1px solid #ccc;
}

input[type="submit"]{
    margin-top: 20px;
    background: #0d6efd;
    color: #fff;
    border: none;
    cursor: pointer;
}

input[type="submit"]:hover{
    background: #0056b3;
}

.role-section{
    display: none;
}

p{
    text-align: center;
    margin-top: 15px;
}
a{
    color: #0d6efd;
    text-decoration: none;
}
</style>
</head>

<body>

<div class="register-container">
<h1>Register</h1>

<form action="register_process.php" method="post">

<label>Full Name</label>
<input type="text" name="fullname" required>

<label>Email</label>
<input type="email" name="email" required>

<label>Contact Number</label>
<input type="tel" name="contact" required>

<div style="position:relative;">
<label>Password</label>
<input type="password" name="password" required>
<!-- Eye Icon -->
    <span onclick="togglePassword()" 
          style="position:absolute; right:10px; top:50%; transform:translateY(-90%); cursor:pointer;">
        👁️
    </span>
</div>

<script>
function togglePassword() {
    let pass = document.getElementById("password");

    if (pass.type === "password") {
        pass.type = "text";
    } else {
        pass.type = "password";
    }
}
</script>

<label>Confirm Password</label>
<input type="password" name="confirm_password" required>

<label>Register As</label>
<select name="role" id="role" required onchange="showRoleFields()">
    <option value="">Select Role</option>
    <option value="student">Student</option>
    <option value="teacher">Teacher</option>
    <option value="admin">Admin</option>
</select>


<input type="submit" value="Register">

</form>

<p>Already registered? <a href="login.php">Login here</a></p>
</div>

<script>
function showRoleFields(){
    document.getElementById("student").style.display = "none";
    document.getElementById("teacher").style.display = "none";
    document.getElementById("admin").style.display = "none";

    let role = document.getElementById("role").value;
    if(role){
        document.getElementById(role).style.display = "block";
    }
}
</script>

</body>
</html>
