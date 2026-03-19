<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Registration</title>

<style>

body{
font-family: Arial, sans-serif;
background:#f5f6f8;
}

.container{
width:70%;
margin:30px auto;
background:#fff;
padding:30px;
border-radius:6px;
}

.stepper{
display:flex;
justify-content:space-between;
margin-bottom:40px;
}

.step{
text-align:center;
flex:1;
position:relative;
color:#aaa;
}

.step::after{
content:'';
position:absolute;
top:15px;
right:-50%;
width:100%;
height:2px;
background:#ddd;
}

.step:last-child::after{
display:none;
}

.circle{
width:30px;
height:30px;
border-radius:50%;
background:#ddd;
margin:0 auto 5px;
line-height:30px;
color:#fff;
}

.step.active{
color:#0d6efd;
}

.step.active .circle{
background:#0d6efd;
}

h2{
color:#0d6efd;
text-align:center;
margin-bottom:30px;
}

label{
font-weight:bold;
display:block;
margin-bottom:5px;
}

input, select{
width:100%;
padding:10px;
margin-bottom:20px;
border:1px solid #ccc;
border-radius:4px;
}

button{
background:#0d6efd;
color:#fff;
padding:10px 25px;
border:none;
border-radius:4px;
cursor:pointer;
margin-right:10px;
}

.form-step{
display:none;
}

.form-step.active{
display:block;
}

.flex-row{
display:flex;
gap:30px;
}

.flex-row .input-group{
flex:1;
}

</style>
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

<script>

let currentStep = 0;

const steps = document.querySelectorAll(".form-step");
const indicators = document.querySelectorAll(".step");

function showStep(index){

steps.forEach((s,i)=>s.classList.toggle("active",i===index));

indicators.forEach((ind,i)=>ind.classList.toggle("active",i===index));

}

function nextStep(){

if(currentStep < steps.length-1){
currentStep++;
showStep(currentStep);
}

}

function prevStep(){

if(currentStep > 0){
currentStep--;
showStep(currentStep);
}

}

</script>

</body>
</html>