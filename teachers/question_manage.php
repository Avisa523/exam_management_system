<?php
session_start();
include('../includes/config.php');

if(!isset($_SESSION['id'])){
    header("Location: ../authentication/login.php");
    exit;
}

$teacher_id = $_SESSION['id'];
$edit = null;

/* ADD QUESTION */
if(isset($_POST['add'])){
    $question = $_POST['question'];
    $description = $_POST['description'];


    $conn->query("INSERT INTO question_papers (teacher_id,question,description,marks)
    VALUES ('$teacher_id','$question','$description','$marks')");

    header("Location: manage_questions.php");
}

/* DELETE QUESTION */
if(isset($_GET['delete'])){
    $id = $_GET['delete'];

    $conn->query("DELETE FROM question_papers WHERE id='$id'");

    header("Location: manage_questions.php");
}

/* EDIT QUESTION */
if(isset($_GET['edit'])){
    $id = $_GET['edit'];

    $edit = $conn->query("SELECT * FROM question_papers WHERE id='$id'")->fetch_assoc();
}

/* UPDATE QUESTION */
if(isset($_POST['update'])){
    $id = $_POST['id'];
    $question = $_POST['question'];
    $description = $_POST['description'];
    

    $conn->query("UPDATE question_papers 
    SET question='$question',description='$description',
    WHERE id='$id'");

    header("Location: manage_questions.php");
}

/* FETCH QUESTIONS */
$questions = $conn->query("SELECT * FROM question_papers WHERE teacher_id='$teacher_id'");
?>

<!DOCTYPE html>
<html>
<head>

<title>Manage Questions</title>

<style>

body{
font-family: Arial;
background:#f4f6f9;
padding:30px;
}

.card{
background:white;
padding:25px;
border-radius:8px;
box-shadow:0 4px 10px rgba(0,0,0,0.08);
}

h2{
margin-bottom:20px;
}

input, textarea{
width:100%;
padding:8px;
margin-top:5px;
margin-bottom:15px;
border:1px solid #ccc;
border-radius:4px;
}

button{
background:#007bff;
color:white;
border:none;
padding:10px 18px;
border-radius:4px;
cursor:pointer;
}

table{
width:100%;
border-collapse:collapse;
margin-top:20px;
}

th,td{
border:1px solid #ddd;
padding:8px;
}

th{
background:#007bff;
color:white;
}

a{
text-decoration:none;
margin-right:10px;
}

.delete{
color:red;
}

.edit{
color:green;
}

</style>

</head>

<header>
    <h1>Question Management</h1>

<a href="0_dashboard.php" class="home-icon">🏠</a>

</header>

<body>

<div class="card">

<h2>Manage Questions</h2>

<h3><?php echo $edit ? "Edit Question" : "Add Question"; ?></h3>

<form method="POST">

<?php if($edit): ?>
<input type="hidden" name="id" value="<?php echo $edit['id']; ?>">
<?php endif; ?>

<label>Question</label>
<textarea name="question" required><?php echo $edit['question'] ?? ''; ?></textarea>

<label>Description</label>
<textarea name="description"><?php echo $edit['description'] ?? ''; ?></textarea>

<label>Marks</label>
<input type="number" name="marks" required value="<?php echo $edit['marks'] ?? ''; ?>">

<?php if($edit): ?>
<button type="submit" name="update">Update Question</button>
<?php else: ?>
<button type="submit" name="add">Add Question</button>
<?php endif; ?>

</form>


<h3>All Questions</h3>

<table>

<tr>
<th>ID</th>
<th>Question</th>
<th>Description</th>
<th>Marks</th>
</tr>

<?php while($q=$questions->fetch_assoc()): ?>

<tr>

<td><?php echo $q['id']; ?></td>

<td><?php echo $q['question']; ?></td>

<td><?php echo $q['description']; ?></td>


<td>

<a class="edit" href="?edit=<?php echo $q['id']; ?>">Edit</a>

<a class="delete" href="?delete=<?php echo $q['id']; ?>"
onclick="return confirm('Delete this question?')">Delete</a>

</td>

</tr>

<?php endwhile; ?>

</table>

</div>

</body>
</html>