<?php
session_start();
include("../includes/config.php"); // Ensure path is correct

// Check role
if(!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher'){
    header("Location: ../login.php");
    exit();
}

// Insert Marks
if(isset($_POST['submit'])){

    $student_id = intval($_POST['student_id']);
    $subject_id = intval($_POST['subject_id']);
    $marks = floatval($_POST['marks']);
    $total_marks = 100;
    $percentage = ($marks / $total_marks) * 100;

    // Grade Calculation
    if ($percentage >= 80) {
        $grade = 'A';
    } elseif ($percentage >= 60) {
        $grade = 'B';
    } elseif ($percentage >= 50) {
        $grade = 'C';
    } elseif ($percentage >= 40) {
        $grade = 'D';
    } else {
        $grade = 'F';
    }

    // Prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO exam_results (student_id, subject_id, marks, total_marks, grade, result_date) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("iidis", $student_id, $subject_id, $marks, $total_marks, $grade);
    if($stmt->execute()){
        echo "<script>alert('Marks Added Successfully');</script>";
    } else {
        echo "<script>alert('Failed to add marks');</script>";
    }
    $stmt->close();
}

?>

<h2>Add Student Marks</h2>

<form method="POST">

<label>Select Student</label>
<select name="student_id" required>
<?php
$students = $conn->query("SELECT * FROM students");
while($s = $students->fetch_assoc()){
    echo "<option value='{$s['id']}'>{$s['full_name']}</option>";
}
?>
</select>

<br><br>

<label>Select Subject</label>
<select name="subject_id" required>
<?php
$subjects = $conn->query("SELECT * FROM subjects");
while($sub = $subjects->fetch_assoc()){
    echo "<option value='{$sub['id']}'>{$sub['subject_name']}</option>";
}
?>
</select>

<br><br>

<label>Marks (Out of 100)</label>
<input type="number" name="marks" required min="0" max="100">

<br><br>

<button type="submit" name="submit">Add Marks</button>

</form>