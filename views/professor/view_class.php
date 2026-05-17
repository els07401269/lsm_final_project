<?php
session_start();
require_once "../../config/Database.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$conn = Database::getInstance()->getConnection();

$class_id = $_GET['id'] ?? null;

if (!$class_id) {
    die("Invalid class ID");
}

/* CLASS */

$stmt = $conn->prepare("SELECT * FROM classes WHERE id = ?");
$stmt->execute([$class_id]);
$class = $stmt->fetch(PDO::FETCH_ASSOC);

/* CLASSWORKS */

$stmt = $conn->prepare("
    SELECT * FROM classworks
    WHERE class_id = ?
    ORDER BY created_at DESC
");

$stmt->execute([$class_id]);
$classworks = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* STUDENTS */

$stmt = $conn->prepare("
    SELECT u.id, u.fname, u.lname
    FROM enrollments e
    JOIN users u ON e.student_id = u.id
    WHERE e.class_id = ?
");

$stmt->execute([$class_id]);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* JOIN REQUESTS */

$stmt = $conn->prepare("
    SELECT jr.*, u.fname, u.lname
    FROM join_requests jr
    JOIN users u ON u.id = jr.student_id
    WHERE jr.class_id = ?
    AND jr.status = 'pending'
");

$stmt->execute([$class_id]);
$join_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* SUBMISSIONS */

$stmt = $conn->prepare("
    SELECT s.*, u.fname, u.lname, cw.title
    FROM submissions s
    JOIN users u ON s.student_id = u.id
    JOIN classworks cw ON cw.id = s.classwork_id
    WHERE cw.class_id = ?
");

$stmt->execute([$class_id]);
$submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>

<title>Class View</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    background:#eef2f7;
    font-family:'Poppins',sans-serif;
    color:#1e293b;
}

/* HEADER */

.header{
    background:linear-gradient(135deg,#312e81,#4f46e5);
    color:white;
    padding:25px;
}

.header h1{
    font-size:28px;
}

/* NAV */

.nav{
    background:white;
    padding:15px;
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    box-shadow:0 2px 10px rgba(0,0,0,0.05);
}

.nav a{
    text-decoration:none;
    background:#eef2ff;
    color:#4338ca;
    padding:10px 15px;
    border-radius:12px;
    font-size:14px;
    font-weight:600;
    transition:.3s ease;
}

.nav a:hover{
    background:#4f46e5;
    color:white;
}

/* CONTAINER */

.container{
    max-width:1200px;
    margin:auto;
    padding:20px;
}

/* CARD */

.card{
    background:white;
    padding:25px;
    border-radius:20px;
    box-shadow:0 6px 18px rgba(0,0,0,0.05);
}

/* POSTS */

.post{
    background:#ede9fe;
    border-left:6px solid #7c3aed;
    padding:20px;
    border-radius:18px;
    margin-bottom:18px;
    transition:.3s ease;
    animation:fadeIn .3s ease;
}

.post:hover{
    transform:translateY(-3px);
    box-shadow:0 8px 18px rgba(124,58,237,0.18);
}

.post h3{
    margin-bottom:10px;
    color:#312e81;
}

.post p{
    color:#475569;
    line-height:1.7;
}

/* BADGE */

.badge{
    display:inline-block;
    background:#7c3aed;
    color:white;
    padding:6px 12px;
    border-radius:999px;
    font-size:12px;
    font-weight:600;
}

/* ITEMS */

.item{
    background:#f8fafc;
    padding:15px;
    border-radius:14px;
    margin-bottom:12px;
}

/* BUTTONS */

button{
    border:none;
    padding:10px 15px;
    border-radius:10px;
    cursor:pointer;
    margin-top:10px;
    font-weight:600;
}

.accept{
    background:#10b981;
    color:white;
}

.reject{
    background:#ef4444;
    color:white;
}

.grade-btn{
    background:#4f46e5;
    color:white;
}

/* FORMS */

input,
select,
textarea{
    width:100%;
    padding:12px;
    margin-top:5px;
    margin-bottom:12px;
    border-radius:10px;
    border:1px solid #cbd5e1;
}

textarea{
    resize:vertical;
    min-height:100px;
}

/* MODAL */

.modal{
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,0.5);
}

.modal-content{
    background:white;
    width:450px;
    max-width:95%;
    margin:40px auto;
    padding:25px;
    border-radius:18px;
    animation:fadeIn .3s ease;
}

/* ANIMATION */

@keyframes fadeIn{
    from{
        opacity:0;
        transform:translateY(10px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}

</style>

</head>

<body>

<div class="header">
    <h1><?= htmlspecialchars($class['class_name']) ?></h1>
</div>

<!-- NAV -->

<div class="nav">

    <a href="#" onclick="filterPosts('all')">🏠 All</a>
    <a href="#" onclick="filterPosts('lesson')">📘 Lesson</a>
    <a href="#" onclick="filterPosts('quiz')">🧠 Quiz</a>
    <a href="#" onclick="filterPosts('assignment')">📝 Assignment</a>
    <a href="#" onclick="filterPosts('activity')">🎯 Activity</a>
    <a href="#" onclick="filterPosts('announcement')">📢 Announcement</a>

    <a href="#" onclick="showSection('students')">👥 Students</a>
    <a href="#" onclick="showSection('submissions')">📥 Submissions</a>
    <a href="#" onclick="showSection('requests')">📩 Join Requests</a>

    <a href="#" onclick="openModal()">➕ Create</a>

</div>

<div class="container">

<!-- FEED -->

<div id="feed" class="card">

<h2 style="margin-bottom:20px;">Class Feed</h2>

<?php foreach($classworks as $cw): ?>

<div class="post"
     data-type="<?= trim(strtolower($cw['type'])) ?>">

    <h3><?= htmlspecialchars($cw['title']) ?></h3>

    <span class="badge">
        <?= ucfirst($cw['type']) ?>
    </span>

    <br><br>

    <p>
        <?= nl2br(htmlspecialchars($cw['description'])) ?>
    </p>

</div>

<?php endforeach; ?>

</div>

<!-- STUDENTS -->

<div id="students" class="card" style="display:none;">

<h2 style="margin-bottom:20px;">Students</h2>

<?php foreach($students as $s): ?>

<div class="item">
    👤 <?= $s['fname'] . " " . $s['lname'] ?>
</div>

<?php endforeach; ?>

</div>

<!-- SUBMISSIONS -->

<div id="submissions" class="card" style="display:none;">

<h2 style="margin-bottom:20px;">Submissions</h2>

<?php foreach($submissions as $sub): ?>

<div class="item">

    <b><?= $sub['fname'] ?> <?= $sub['lname'] ?></b>

    <br><br>

    <?= $sub['title'] ?>

    <form method="POST"
          action="../../controllers/add_grade.php">

        <input type="hidden"
               name="student_id"
               value="<?= $sub['student_id'] ?>">

        <input type="hidden"
               name="classwork_id"
               value="<?= $sub['classwork_id'] ?>">

        <input type="hidden"
               name="class_id"
               value="<?= $class_id ?>">

        <input type="number"
               name="grade"
               value="100">

        <button type="submit" class="grade-btn">
            Put Grade
        </button>

    </form>

</div>

<?php endforeach; ?>

</div>

<!-- REQUESTS -->

<div id="requests" class="card" style="display:none;">

<h2 style="margin-bottom:20px;">Join Requests</h2>

<?php foreach($join_requests as $r): ?>

<div class="item">

    <b><?= $r['fname'] ?> <?= $r['lname'] ?></b>

    <br><br>

    <form method="POST"
          action="../../controllers/process_join_request.php">

        <input type="hidden"
               name="request_id"
               value="<?= $r['id'] ?>">

        <input type="hidden"
               name="student_id"
               value="<?= $r['student_id'] ?>">

        <input type="hidden"
               name="class_id"
               value="<?= $r['class_id'] ?>">

        <button name="action"
                value="accept"
                class="accept">

            Accept

        </button>

        <button name="action"
                value="reject"
                class="reject">

            Reject

        </button>

    </form>

</div>

<?php endforeach; ?>

</div>

</div>

<!-- MODAL -->

<div id="modal" class="modal">

<div class="modal-content">

<h2>Create Classwork</h2>

<br>

<form method="POST"
      action="../../controllers/add_classwork.php">

    <input type="hidden"
           name="class_id"
           value="<?= $class_id ?>">

    <label>Title</label>
    <input type="text" name="title" required>

    <label>Type</label>

    <select name="type" required>
        <option value="lesson">Lesson</option>
        <option value="quiz">Quiz</option>
        <option value="assignment">Assignment</option>
        <option value="activity">Activity</option>
        <option value="announcement">Announcement</option>
    </select>

    <label>Description</label>

    <textarea name="description"></textarea>

    <label>Max Points</label>

    <input type="number"
           name="max_points"
           value="100">

    <button type="submit" class="grade-btn">
        Create
    </button>

    <button type="button"
            class="reject"
            onclick="closeModal()">

        Close

    </button>

</form>

</div>
</div>

<script>

function hideSections(){

    document.getElementById("students").style.display = "none";
    document.getElementById("submissions").style.display = "none";
    document.getElementById("requests").style.display = "none";

    document.getElementById("feed").style.display = "block";
}

function filterPosts(type){

    hideSections();

    const posts = document.querySelectorAll(".post");

    posts.forEach(post => {

        let postType =
            post.getAttribute("data-type");

        postType =
            postType.trim().toLowerCase();

        if(type === "all"){
            post.style.display = "block";
        }
        else if(postType === type){
            post.style.display = "block";
        }
        else{
            post.style.display = "none";
        }

    });
}

function showSection(section){

    document.getElementById("feed").style.display = "none";

    document.getElementById("students").style.display = "none";
    document.getElementById("submissions").style.display = "none";
    document.getElementById("requests").style.display = "none";

    document.getElementById(section).style.display = "block";
}

function openModal(){
    document.getElementById("modal").style.display = "block";
}

function closeModal(){
    document.getElementById("modal").style.display = "none";
}

</script>

</body>
</html>