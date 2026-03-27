<?php
session_start();

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../authentication/login.php");
    exit;
}

include('../includes/config.php');

/* ADD EXAM */
if(isset($_POST['add_exam'])){
    $subject_id = $_POST['subject_id'];
    $exam_date = $_POST['exam_date'];
    $exam_time = $_POST['exam_time'];
    $end_time = $_POST['end_time'];
    $room = $_POST['room'];

    $conn->query("
    INSERT INTO exam_schedule(subject_id,exam_date,exam_time,end_time,room)
    VALUES('$subject_id','$exam_date','$exam_time','$end_time','$room')
    ");
}

/* UPDATE EXAM */
if(isset($_POST['update_exam'])){
    $id = $_POST['exam_id'];
    $subject_id = $_POST['subject_id'];
    $exam_date = $_POST['exam_date'];
    $exam_time = $_POST['exam_time'];
    $end_time = $_POST['end_time'];
    $room = $_POST['room'];

    $conn->query("
    UPDATE exam_schedule 
    SET subject_id='$subject_id',
        exam_date='$exam_date',
        exam_time='$exam_time',
        end_time='$end_time',
        room='$room'
    WHERE id='$id'
    ");
    header("Location: exam_schedule.php"); // Redirect after update
    exit;
}

/* DELETE EXAM */
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $conn->query("DELETE FROM exam_schedule WHERE id='$id'");
    header("Location: exam_schedule.php");
    exit;
}

/* FETCH SUBJECTS */
$subjects = $conn->query("SELECT * FROM subjects");

/* FETCH EXAMS */
$exams = $conn->query("
SELECT es.id, es.exam_date, es.exam_time, es.end_time, room, s.subject_name
FROM exam_schedule es
JOIN subjects s ON es.subject_id = s.id
ORDER BY es.exam_date ASC, es.exam_time ASC
");

/* FETCH EXAM TO EDIT */
$editingExam = null;
if(isset($_GET['edit'])){
    $edit_id = intval($_GET['edit']);
    $result = $conn->query("SELECT * FROM exam_schedule WHERE id='$edit_id'");
    if($result && $result->num_rows > 0){
        $editingExam = $result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Exam Schedule</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<div class="exam-header-bar">
    <h1>Manage Exam Schedule</h1>
    <a href="dashboard.php" class="home-icon">🏠</a>
</div>

<div class="main-content">

<h2><?php echo $editingExam ? 'Edit Exam' : 'Add Exam'; ?></h2>

<form method="POST" class="exam-form">

    <?php if($editingExam): ?>
        <input type="hidden" name="exam_id" value="<?php echo $editingExam['id']; ?>">
    <?php endif; ?>

    <select name="subject_id" required>
        <option value="">Select Subject</option>
        <?php 
        $subjects->data_seek(0); // reset pointer
        while($sub = $subjects->fetch_assoc()): ?>
            <option value="<?php echo $sub['id']; ?>" 
                <?php echo ($editingExam && $editingExam['subject_id'] == $sub['id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($sub['subject_name']); ?>
            </option>
        <?php endwhile; ?>
    </select>

    <input type="date" name="exam_date" required value="<?php echo $editingExam ? $editingExam['exam_date'] : ''; ?>">

    <input type="time" name="exam_time" required value="<?php echo $editingExam ? $editingExam['exam_time'] : ''; ?>">

    <input type="time" name="end_time" required value="<?php echo $editingExam ? $editingExam['end_time'] : ''; ?>">

    <input type="text" name="room" placeholder="Room" required value="<?php echo $editingExam ? htmlspecialchars($editingExam['room']) : ''; ?>">

    <div style="margin-top:15px;">
        <?php if($editingExam): ?>
            <button type="submit" name="update_exam">Update Exam</button>
            <a href="exam_schedule.php" style="padding:10px 15px; background:#6c757d; color:white; border-radius:5px; text-decoration:none;">Cancel</a>
        <?php else: ?>
            <button type="submit" name="add_exam">Add Exam</button>
        <?php endif; ?>
    </div>

</form>

<br><br>

<h2>Exam Schedule</h2>

<table class="exam-table">
<tr>
    <th>S.No</th>
    <th>Subject</th>
    <th>Date</th>
    <th>Time</th>
    <th>Room</th>
    <th>Actions</th>
</tr>

<?php
$i=1;
if($exams && $exams->num_rows>0):
    while($row = $exams->fetch_assoc()):
?>
<tr>
    <td><?php echo $i++; ?></td>
    <td><?php echo htmlspecialchars($row['subject_name']); ?></td>
    <td><?php echo $row['exam_date']; ?></td>
    <td>
        <?php
        echo date("g:i A",strtotime($row['exam_time'])) . " - " .
             date("g:i A",strtotime($row['end_time']));
        ?>
    </td>
    <td><?php echo htmlspecialchars($row['room']); ?></td>
    <td>
        <a class="edit-btn" href="?edit=<?php echo $row['id']; ?>">Edit</a>
        <a class="delete-btn" href="?delete=<?php echo $row['id']; ?>" 
           onclick="return confirm('Delete this exam?')">Delete</a>
    </td>
</tr>
<?php endwhile; else: ?>
<tr>
    <td colspan="6" style="text-align:center;">No exams scheduled</td>
</tr>
<?php endif; ?>
</table>

</div>

</body>
</html>