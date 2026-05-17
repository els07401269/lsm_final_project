<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

require_once "../../controllers/MessageController.php";

$user = $_SESSION['user'];

$messageController = new MessageController();
$messages = $messageController->inbox($user['id']);

$profile = $user['profile_pic'] ?? null;
$initial = strtoupper(substr($user['fname'], 0, 1));
?>

<!DOCTYPE html>
<html>
<head>
<title>Student Dashboard</title>

<style>
:root {
    --primary: #2c3e50;
    --secondary: #34495e;
    --accent: #16a085;
    --bg: #f4f6f9;
    --card: #ffffff;
}

body {
    margin: 0;
    font-family: Arial;
    background: linear-gradient(135deg, #153c6b, #7d784a);
}

/* TOPBAR */
.topbar {
    background: var(--primary);
    color: white;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* LOGO */
.logo { height: 60px; }

.top-actions {
    display: flex;
    align-items: center;
    gap: 15px;
}

/* 🔥 VARSITY TITLE */
.varsity-title {
    font-family: 'Arial Black', Impact, sans-serif;
    font-size: 28px;
    font-weight: 900;
    text-transform: uppercase;
    color: white;

    letter-spacing: 3px;

    /* outline */
    -webkit-text-stroke: 1.5px #181d25;

    /* shadow depth */
    text-shadow:
        2px 2px 0 #0d47a1,
        4px 4px 0 rgba(0,0,0,0.3);

    margin: 0;
}

/* MESSAGE LINK */
.msg-link {
    color: white;
    text-decoration: none;
    font-weight: bold;
    padding: 6px 10px;
    border-radius: 6px;
    transition: 0.3s;
}

.msg-link:hover {
    background: rgba(255,255,255,0.15);
}

/* PROFILE */
.avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    object-fit: cover;
    cursor: pointer;
    transition: 0.3s;
}

.avatar:hover {
    transform: scale(1.05);
}

.avatar-letter {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: var(--accent);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s;
}

.avatar-letter:hover {
    transform: scale(1.05);
}

/* PROFILE DROPDOWN */
.profile-menu {
    display: none;
    position: absolute;
    right: 0;
    top: 60px;
    background: white;
    min-width: 160px;
    border-radius: 10px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    overflow: hidden;
    z-index: 9999;
    animation: fadeIn 0.2s ease-in-out;
}

.profile-menu a {
    display: block;
    padding: 10px;
    text-decoration: none;
    color: var(--primary);
    font-weight: bold;
    transition: 0.3s;
}

.profile-menu a:hover {
    background: #e5e7b3;
    padding-left: 15px;
}

/* NAV */
.nav {
    background: var(--secondary);
    padding: 12px 20px;
    display: flex;
    gap: 25px;
}

.nav a {
    color: white;
    text-decoration: none;
    font-weight: bold;
    position: relative;
}

.nav a::after {
    content: "";
    position: absolute;
    left: 0;
    bottom: -4px;
    width: 0%;
    height: 2px;
    background: var(--accent);
    transition: 0.3s;
}

.nav a:hover::after {
    width: 100%;
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
    display: flex;
    flex-direction: column;
    animation: fadeIn 0.2s ease-in-out;
}

/* CONTENT */
.container {
    padding: 40px;
    display: flex;
    justify-content: center;
    flex-direction: column;
    align-items: center;
}

/* WELCOME CARD */
.welcome-card {
    background: white;
    padding: 50px;
    border-radius: 15px;
    width: 500px;
    text-align: center;
    box-shadow: 0 6px 18px rgba(0,0,0,0.1);
    transition: 0.3s;
    border-top: 5px solid var(--accent);
}

.welcome-card:hover {
    transform: translateY(-5px);
}

/* QUICK STATS */
.quick-stats {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
}

.stat-card {
    background: white;
    padding: 15px;
    border-radius: 12px;
    width: 150px;
    text-align: center;
    box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    transition: 0.3s;
}

.stat-card:hover {
    transform: translateY(-5px);
}

/* SUCCESS */
.success {
    background: #2ecc71;
    color: white;
    padding: 10px;
    border-radius: 5px;
}

/* ANIMATION */
@keyframes fadeIn {
    from {opacity: 0; transform: translateY(-10px);}
    to {opacity: 1; transform: translateY(0);}
}
</style>

</head>

<body>

<!-- TOPBAR -->
<div class="topbar">

    <div style="display:flex; align-items:center; gap:15px;">
        <img src="../../assets/images/logo.png" class="logo">
        <h2 class="varsity-title">JOSH JELO ELSA UNIVERSITY</h2>
    </div>

    <div class="top-actions">

        <a href="javascript:void(0)" class="msg-link" onclick="openMsg()">📩 Messages</a>

        <div style="position:relative;">

            <?php if (!empty($profile) && file_exists("../../uploads/" . $profile)): ?>
                <img src="../../uploads/<?= $profile ?>" class="avatar" onclick="toggleProfile()">
            <?php else: ?>
                <div class="avatar-letter" onclick="toggleProfile()">
                    <?= $initial ?>
                </div>
            <?php endif; ?>

            <div class="profile-menu" id="profileMenu">
                <a href="change_profile.php">Change Profile</a>
                <a href="../auth/logout.php">Logout</a>
            </div>

        </div>

    </div>

</div>

<!-- NAV -->
<div class="nav">
    <a href="dashboard.php">Dashboard</a>
    <a href="join_class.php">Join Class</a>
    <a href="my_classes.php">My Classes</a>
</div>

<!-- CONTENT -->
<div class="container">

    <div class="quick-stats">
        <div class="stat-card">
            <h3>📚 Classes</h3>
            <p>Active</p>
        </div>

        <div class="stat-card">
            <h3>📩 Messages</h3>
            <p>Inbox</p>
        </div>

        <div class="stat-card">
            <h3>🎯 Progress</h3>
            <p>Updated</p>
        </div>
    </div>

    <div class="welcome-card">

        <?php if (isset($_SESSION['success'])): ?>
            <div class="success"><?= $_SESSION['success']; ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <h1>Welcome <?= htmlspecialchars($user['fname']) ?></h1>
        <p style="color:gray;">Ready to continue your learning today?</p>

    </div>
</div>

<!-- MODAL -->
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

                        <div class="actions">
                            <a href="../../controllers/delete_message.php?id=<?= $msg['id'] ?>"
                               onclick="return confirm('Delete message?')"
                               style="color:red;">
                               Delete
                            </a>
                        </div>
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
});

document.addEventListener("click", function(e){
    const modal = document.getElementById("msgModal");
    if (e.target === modal) {
        modal.style.display = "none";
    }
});
</script>

</body>
</html>
