<?php
session_start();

// Only admin can access
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header('Location: ../authentication/login.php');
    exit;
}

include(__DIR__ . '/../includes/config.php');

// Approve/Disapprove a question paper
if(isset($_GET['approve'])){
    $id = intval($_GET['approve']);
    $conn->query("UPDATE question_papers SET approved=1 WHERE id=$id");
}

if(isset($_GET['disapprove'])){
    $id = intval($_GET['disapprove']);
    $conn->query("UPDATE question_papers SET approved=2 WHERE id=$id");
}

// Fetch all question papers with teacher and subject info
$result = $conn->query("
    SELECT qp.id, qp.title, qp.question, qp.description, qp.created_at, qp.approved,
           t.full_name AS teacher_name, s.subject_name
    FROM question_papers qp
    LEFT JOIN teachers t ON t.id = qp.teacher_id
    LEFT JOIN subjects s ON s.id = qp.subject_id
    ORDER BY qp.created_at DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Admin: Approve Question Papers</title>
<style>
body {font-family: Arial, sans-serif; background:#f4f6f8; padding:20px;}
h2 {margin-bottom:20px;}
table {width:100%; border-collapse: collapse; background: white; border-radius:8px; overflow:hidden; box-shadow:0 2px 6px rgba(0,0,0,0.1);}
th, td {padding:12px 15px; border-bottom:1px solid #ddd; text-align:left;}
th {background:#1e90ff; color:white;}
tr:hover {background:#f1f1f1;}
.approve-btn, .disapprove-btn {padding:5px 8px; color:white; border-radius:4px; text-decoration:none; font-size:13px; transition:0.3s;}
.approve-btn {background:#28a745;}
.disapprove-btn {background:#dc3545;}
.approve-btn:hover, .disapprove-btn:hover {opacity:0.8;}
.status-pending {color:#ffa500; font-weight:bold;}
.status-approved {color:#28a745; font-weight:bold;}
.status-disapproved {color:#dc3545; font-weight:bold;}
.view-btn {padding:5px 8px; background:#1e90ff; color:white; text-decoration:none; border-radius:4px; font-size:13px;}
.view-btn:hover {opacity:0.8;}
</style>
</head>
<body>

<h2>Admin: Approve Question Papers</h2>


<table>
<tr>
    <th>S.No</th>
    <th>Title</th>
    <th>Subject</th>
    <th>Teacher</th>
    <th>Question</th>
    <th>Description</th>
    <th>Created At</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php $counter=1; while($row = $result->fetch_assoc()){ 
    $status_text = '';
    if($row['approved']==0) $status_text = '<span class="status-pending">Pending</span>';
    elseif($row['approved']==1) $status_text = '<span class="status-approved">Approved</span>';
    elseif($row['approved']==2) $status_text = '<span class="status-disapproved">Disapproved</span>';
?>
<tr>
    <td><?php echo $counter++; ?></td>
    <td><?php echo $row['title']; ?></td>
    <td><?php echo $row['subject_name'] ?: '-'; ?></td>
    <td><?php echo $row['teacher_name'] ?: '-'; ?></td>
    <td>
        <a class="view-btn" href="../teachers/view_question_paper.php?id=<?php echo $row['id']; ?>" target="_blank">View</a>
        
    </td>
    <td><?php echo $row['description'] ?: '-'; ?></td>
    <td><?php echo $row['created_at']; ?></td>
    <td><?php echo $status_text; ?></td>
    <td>
        <?php if($row['approved']==0){ ?>
            <a class="approve-btn" href="?approve=<?php echo $row['id']; ?>">Approve</a>
            <a class="disapprove-btn" href="?disapprove=<?php echo $row['id']; ?>">Disapprove</a>
        <?php } else { echo '-'; } ?>
    </td>
</tr>
<?php } ?>

</table>

</body>
</html>