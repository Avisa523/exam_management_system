<?php
session_start();

if (!isset($_SESSION['id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header('Location: ../authentication/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Registration</title>
<link rel="stylesheet" href="../assets/css/registration.css">
<script src="../assets/js/registration.js"></script>
</head>

<body>

<div class="container">

<div class="stepper">
<div class="step active"><div class="circle">1</div>Personal Details</div>
<div class="step"><div class="circle">2</div>Family Details</div>
<div class="step"><div class="circle">3</div>Academic Details</div>
<div class="step"><div class="circle">4</div>Documents</div>
<div class="step"><div class="circle">5</div>Submit</div>
</div>

<form id="multiStepForm" action="save_student.php" method="POST" enctype="multipart/form-data">

<!-- STEP 1 PERSONAL -->
<div class="form-step active">

<h2>Personal Details</h2>

<label>Full Name</label>
<input type="text" name="name" required>

<label>Date of Birth</label>
<input type="date" name="dob">

<label>Gender</label>
<select name="gender">
<option>Select Gender</option>
<option>Male</option>
<option>Female</option>
<option>Other</option>
</select>

<label>Email</label>
<input type="email" name="email">


<button type="button" onclick="nextStep()">Next</button>

</div>

<!-- STEP 2 FAMILY -->

<div class="form-step">

<h2>Family / Contact Details</h2>

<div class="flex-row">
<div class="input-group">
<label>Father Name</label>
<input type="text" name="father_name">
</div>

<div class="input-group">
<label>Father Phone</label>
<input type="text" name="father_phone">
</div>
</div>

<div class="flex-row">
<div class="input-group">
<label>Mother Name</label>
<input type="text" name="mother_name">
</div>

<div class="input-group">
<label>Mother Phone</label>
<input type="text" name="mother_phone">
</div>
</div>

<label>Permanent Address</label>
<input type="text" name="permanent_address">

<label>Current Address</label>
<input type="text" name="current_address">

<label>Mobile Number</label>
<input type="text" name="mobile">

<button type="button" onclick="prevStep()">Previous</button>
<button type="button" onclick="nextStep()">Next</button>

</div>

<!-- STEP 3 ACADEMIC -->

<div class="form-step">

<h2>Academic Details</h2>

<label>Board / University</label>
<select name="board" required>
<option value="">Select Board</option>
<option value="NEB">NEB (National Examination Board)</option>
<option value="CBSE">CBSE</option>
<option value="ISC">ISC</option>
<option value="Other">Other</option>
</select>

<label>Stream</label>
<select name="stream" required>
<option value="">Select Stream</option>
<option value="Science">Science</option>
<option value="Management">Management</option>
<option value="Humanities">Humanities</option>
<option value="Education">Education</option>
</select>

<label>Year of Passing</label>
<input type="text" name="year_of_passing">

<label>GPA</label>
<input type="text" name="gpa" placeholder="e.g. 3.8">

<label>School/College</label>
<input type="text" name="school_college">

<button type="button" onclick="prevStep()">Previous</button>
<button type="button" onclick="nextStep()">Next</button>

</div>

<!-- STEP 4 DOCUMENTS -->

<div class="form-step">

<h2>Upload Documents</h2>

<label>ID Proof</label>
<input type="file" name="id_proof" accept=".pdf,.jpg,.jpeg,.png">

<label>+2 Marksheets</label>
<input type="file" name="marksheet[]" accept=".pdf" multiple>

<label>Character Certificate</label>
<input type="file" name="character_certificate" accept=".pdf">

<label>Profile Photo</label>
<input type="file" name="photo" accept="image/*">

<label>Signature</label>
<input type="file" name="signature" accept="image/*">

<button type="button" onclick="prevStep()">Previous</button>
<button type="button" onclick="nextStep()">Next</button>

</div>

<!-- STEP 5 SUBMIT -->

<div class="form-step">

<h2>Submit</h2>

<p>Review all details before submitting.</p>

<button type="button" onclick="prevStep()">Previous</button>
<button type="submit">Submit</button>

</div>

</form>
</div>


</body>
</html>