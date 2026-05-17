<?php
session_start();
require_once "../../config/Database.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

/* =========================
   AUTH CHECK
========================= */
if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user = $_SESSION['user'];
$conn = Database::getInstance()->getConnection();

/* =========================
   CLASSWORK ID
========================= */
$classwork_id = $_GET['classwork_id'] ?? null;
$class_id = $_GET['class_id'] ?? null;

if (!$classwork_id || !$class_id) {
    die("Missing parameters");
}

/* =========================
   CLASSWORK INFO
========================= */
$stmt = $conn->prepare("SELECT * FROM classworks WHERE id = ?");
$stmt->execute([$classwork_id]);
$classwork = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$classwork) {
    die("Classwork not found");
}

/* =========================
   STUDENTS
========================= */
$stmt = $conn->prepare("
    SELECT u.id, u.fname, u.lname
    FROM enrollments e
    JOIN users u ON e.student_id = u.id
    WHERE e.class_id = ?
");
$stmt->execute([$class_id]);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   SUBMISSIONS
========================= */
$stmt = $conn->prepare("
    SELECT *
    FROM submissions
    WHERE classwork_id = ?
");
$stmt->execute([$classwork_id]);

$submissions = [];
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $sub) {
    $submissions[$sub['student_id']] = $sub;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>View Submissions</title>

<style>
body{
    font-family:Arial;
    background:#f5f7fb;
    margin:0;
}

.header{
    background:#1e293b;
    color:white;
    padding:20px;
}

.container{
    padding:20px;
}

.card{
    background:white;
    padding:15px;
    border-radius:10px;
    box-shadow:0 3px 10px rgba(0,0,0,0.1);
}

table{
    width:100%;
    border-collapse:collapse;
}

th,td{
    padding:12px;
    border-bottom:1px solid #ddd;
    text-align:left;
}

th{
    background:#2c3e50;
    color:white;
}

.badge{
    padding:5px 10px;
    border-radius:20px;
    font-size:12px;
    color:white;
}

.submitted{ background:#22c55e; }
.not-submitted{ background:#ef4444; }

a{
    color:#1abc9c;
    text-decoration:none;
    font-weight:bold;
}
</style>

</head>

<body>

<div class="header">
    <h2>View Submissions - <?= htmlspecialchars($classwork['title']) ?></h2>
</div>

<div class="container">

<div class="card">

<table>
<tr>
    <th>Student</th>
    <th>Status</th>
    <th>File</th>
    <th>Submitted At</th>
</tr>

<?php foreach ($students as $s): ?>

<?php $submission = $submissions[$s['id']] ?? null; ?>

<tr>

    <td>
        <?= htmlspecialchars($s['fname'] . " " . $s['lname']) ?>
    </td>

    <td>
        <?php if ($submission): ?>
            <span class="badge submitted">Submitted</span>
        <?php else: ?>
            <span class="badge not-submitted">Not Submitted</span>
        <?php endif; ?>
    </td>

    <td>
        <?php if (!empty($submission['file_path'])): ?>
            <a href="../../<?= $submission['file_path'] ?>" target="_blank">
                View File
            </a>
        <?php else: ?>
            -
        <?php endif; ?>
    </td>

    <td>
        <?= $submission['submitted_at'] ?? '-' ?>
    </td>

</tr>

<?php endforeach; ?>

</table>

</div>

</div>

</body>
</html>