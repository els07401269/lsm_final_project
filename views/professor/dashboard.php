<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user = $_SESSION['user'];

/* ROLE SAFETY */
if ($user['role'] !== 'professor') {
    header("Location: ../student/dashboard.php");
    exit;
}

require_once "../../controllers/MessageController.php";

$messageController = new MessageController();
$messages = $messageController->inbox($user['id']);

$profile = $user['profile_pic'] ?? null;
$initial = strtoupper(substr($user['fname'] ?? 'P', 0, 1));
?>

<!DOCTYPE html>
<html>
<head>
<title>Professor Dashboard</title>

<style>
body {
    margin: 0;
    font-family: Arial;
    background: linear-gradient(135deg, #153c6b, #7d784a);
}

/* TOPBAR */
.topbar {
    background: #2c3e50;
    color: white;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo { height: 60px; }

.top-actions {
    display: flex;
    align-items: center;
    gap: 15px;
}

.msg-link {
    color: white;
    text-decoration: none;
    font-weight: bold;
}

/* PROFILE */
.avatar, .avatar-letter {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    cursor: pointer;
}

.avatar {
    object-fit: cover;
}

.avatar-letter {
    background: #16a085;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}

/* PROFILE MENU */
.profile-menu {
    display: none;
    position: absolute;
    right: 0;
    top: 60px;
    background: white;
    min-width: 160px;
    border-radius: 10px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    overflow: hidden;
    z-index: 9999;
}

.profile-menu a {
    display: block;
    padding: 10px;
    text-decoration: none;
    color: #2c3e50;
    font-weight: bold;
}

.profile-menu a:hover {
    background: #f2f2f2;
}

/* NAV */
.nav {
    background: #34495e;
    padding: 12px 20px;
    display: flex;
    gap: 25px;
}

.nav a {
    color: white;
    text-decoration: none;
    font-weight: bold;
}

/* CONTENT */
.container {
    padding: 40px;
    display: flex;
    justify-content: center;
}

/* CARD */
.welcome-card {
    background: white;
    padding: 50px;
    border-radius: 15px;
    width: 520px;
    text-align: center;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}

.success {
    background: #2ecc71;
    color: white;
    padding: 10px;
    border-radius: 6px;
    margin-bottom: 15px;
}

/* MODAL */
.modal {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.5);
    justify-content: center;
    align-items: center;
    z-index: 99999;
}

.modal-box {
    width: 420px;
    max-height: 80vh;
    background: white;
    border-radius: 12px;
    overflow: hidden;
}

.modal-header {
    background: #2c3e50;
    color: white;
    padding: 10px;
    display: flex;
    justify-content: space-between;
}

.modal-body {
    padding: 10px;
    overflow-y: auto;
    max-height: 60vh;
}

.msg-item {
    padding: 10px;
    border-bottom: 1px solid #eee;
}
</style>

</head>

<body>

<!-- TOPBAR -->
<div class="topbar">

    <div style="display:flex; align-items:center; gap:15px;">
        <img src="../../assets/images/logo.png" class="logo">
        <h2>JOSH JELO ELSA UNIVERSITY</h2>
    </div>

    <div class="top-actions">

        <a href="javascript:void(0)" class="msg-link" onclick="openMsg()">📩 Messages</a>

        <!-- PROFILE -->
        <div style="position:relative;">

            <?php if (!empty($profile) && file_exists(__DIR__ . "/../../uploads/" . $profile)): ?>
                <img src="../../uploads/<?= $profile ?>" class="avatar" onclick="toggleProfile()">
            <?php else: ?>
                <div class="avatar-letter" onclick="toggleProfile()">
                    <?= $initial ?>
                </div>
            <?php endif; ?>

            <div class="profile-menu" id="profileMenu">
                <a href="../professor/change_profile.php">Change Profile</a>
                <a href="../auth/logout.php">Logout</a>
            </div>

        </div>

    </div>

</div>

<!-- NAV -->
<div class="nav">
    <a href="dashboard.php">Dashboard</a>
    <a href="create_class.php">Create Class</a>
    <a href="my_classes.php">My Classes</a>
</div>

<!-- CONTENT -->
<div class="container">

    <div class="welcome-card">

        <?php if (isset($_SESSION['success'])): ?>
            <div class="success"><?= $_SESSION['success']; ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <h1>
            Welcome Professor <?= htmlspecialchars($user['fname']) ?> 👨‍🏫
        </h1>

        <p>📚 Manage your classes, create activities, and guide students.</p>

        <div style="margin-top:15px;">
            <span style="background:#1abc9c;color:white;padding:6px 12px;border-radius:20px;">
                Active Teaching Mode
            </span>
        </div>

    </div>

</div>

<!-- MESSAGE MODAL -->
<div id="msgModal" class="modal">
    <div class="modal-box">

        <div class="modal-header">
            <h3>Inbox</h3>
            <span onclick="closeMsg()" style="cursor:pointer;">✕</span>
        </div>

        <div class="modal-body">

            <?php if (!empty($messages)): ?>
                <?php foreach ($messages as $msg): ?>
                    <div class="msg-item">
                        <div><?= htmlspecialchars($msg['message']) ?></div>
                        <small><?= $msg['created_at'] ?></small>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No messages yet</p>
            <?php endif; ?>

        </div>

    </div>
</div>

<script>
function openMsg(){
    document.getElementById("msgModal").style.display = "flex";
}

function closeMsg(){
    document.getElementById("msgModal").style.display = "none";
}

function toggleProfile(){
    const menu = document.getElementById("profileMenu");
    menu.style.display = (menu.style.display === "block") ? "none" : "block";
}

document.addEventListener("click", function(e){
    const box = document.querySelector(".top-actions");
    const menu = document.getElementById("profileMenu");

    if (!box.contains(e.target)) {
        menu.style.display = "none";
    }

    const modal = document.getElementById("msgModal");
    if (e.target === modal) {
        modal.style.display = "none";
    }
});
</script>

</body>
</html>