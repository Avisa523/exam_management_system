<?php
session_start();
include('../includes/config.php');

// Only teachers
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'teacher'){
    header("Location: ../authentication/login.php");
    exit;
}

$teacher_id = $_SESSION['id'];
$edit = null;

/* ------------------- ADD QUESTION ------------------- */
if(isset($_POST['add'])){
    $question = trim($_POST['question']);
    if(empty($question)){
        die("Error: Question cannot be empty.");
    }

    $sql = "INSERT INTO question_papers (teacher_id, question) VALUES ('$teacher_id','$question')";
    if($conn->query($sql) === FALSE){
        die("Add Error: " . $conn->error);
    }

    header("Location: manage_questions.php");
    exit;
}

/* ------------------- DELETE QUESTION ------------------- */
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']); // ensure numeric

    if($conn->query("DELETE FROM question_papers WHERE id='$id'") === FALSE){
        die("Delete Error: " . $conn->error);
    }

    header("Location: manage_questions.php");
    exit;
}

/* ------------------- EDIT QUESTION ------------------- */
if(isset($_GET['edit'])){
    $id = intval($_GET['edit']); // ensure numeric

    $edit_result = $conn->query("SELECT * FROM question_papers WHERE id='$id'");
    if($edit_result->num_rows == 0){
        die("Error: Question not found.");
    }

    $edit = $edit_result->fetch_assoc();
}

/* ------------------- UPDATE QUESTION ------------------- */
if(isset($_POST['update'])){
    $id = intval($_POST['id']);
    $question = trim($_POST['question']);
    if(empty($question)){
        die("Error: Question cannot be empty.");
    }

    if($conn->query("UPDATE question_papers SET question='$question' WHERE id='$id'") === FALSE){
        die("Update Error: " . $conn->error);
    }

    header("Location: manage_questions.php");
    exit;
}

/* ------------------- FETCH QUESTIONS ------------------- */
$questions = $conn->query("SELECT * FROM question_papers ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Questions</title>
<style>
body{ font-family: Arial; background:#f4f6f9; padding:30px; }
.card{ background:white; padding:25px; border-radius:8px; box-shadow:0 4px 10px rgba(0,0,0,0.08); }
h2{ margin-bottom:20px; }
input, textarea{ width:100%; padding:8px; margin-top:5px; margin-bottom:15px; border:1px solid #ccc; border-radius:4px; }
button{ background:#007bff; color:white; border:none; padding:10px 18px; border-radius:4px; cursor:pointer; }
table{ width:100%; border-collapse:collapse; margin-top:20px; }
th,td{ border:1px solid #ddd; padding:8px; text-align:left; }
th{ background:#007bff; color:white; }
a{ text-decoration:none; margin-right:10px; }
.delete{ color:red; }
.edit{ color:green; }
</style>
</head>
<body>

<header>
    <h1>Question Management</h1>
    <a href="dashboard.php" class="home-icon">🏠</a>
</header>

<div class="card">

<h2>Manage Questions</h2>

<h3><?php echo $edit ? "Edit Question" : "Add Question"; ?></h3>

<form method="POST">
    <?php if($edit): ?>
        <input type="hidden" name="id" value="<?php echo $edit['id']; ?>">
    <?php endif; ?>

    <label>Question</label>
    <textarea name="question" required><?php echo $edit['question'] ?? ''; ?></textarea>

    <?php if($edit): ?>
        <button type="submit" name="update">Update Question</button>
        <a href="manage_questions.php">Cancel</a>
    <?php else: ?>
        <button type="submit" name="add">Add Question</button>
    <?php endif; ?>
</form>

<h3>All Questions</h3>

<table>
<tr>
<th>ID</th>
<th>Question</th>
<th>Action</th>
</tr>

<?php while($q = $questions->fetch_assoc()): ?>
<tr>
    <td><?php echo $q['id']; ?></td>
    <td><?php echo htmlspecialchars($q['question']); ?></td>
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