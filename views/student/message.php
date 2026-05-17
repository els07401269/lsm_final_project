<?php
session_start();

require_once __DIR__ . "/../../controllers/MessageController.php";

if (!isset($_SESSION['user'])) {
    exit("Unauthorized");
}

$controller = new MessageController();

$userId = $_SESSION['user']['id'];
$messages = $controller->inbox($userId);
?>

<!DOCTYPE html>
<html>
<head>
<title>Inbox</title>

<style>
body {
    margin: 0;
    font-family: Arial;
    background: #eef1f6;

    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

/* CENTER CONTAINER */
.container {
    width: 420px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    padding: 20px;
    position: relative;
}

/* HEADER */
h2 {
    text-align: center;
    margin: 0 0 15px;
}

/* CLOSE BUTTON */
.close {
    position: absolute;
    right: 12px;
    top: 10px;
    font-size: 18px;
    cursor: pointer;
    color: #777;
}

.close:hover {
    color: red;
}

/* BUTTON CLEAN */
.btn {
    display: block;
    width: 100%;
    padding: 10px;
    margin-bottom: 15px;
    border: none;
    background: #2b6cb0;
    color: #fff;
    border-radius: 6px;
    cursor: pointer;
}

.btn:hover {
    background: #1e4f86;
}

/* MESSAGE BOX */
.msg {
    padding: 10px;
    border-bottom: 1px solid #eee;
}

.msg:last-child {
    border-bottom: none;
}

small {
    color: #888;
    font-size: 12px;
}

.success {
    color: green;
    text-align: center;
    margin-bottom: 10px;
}
</style>

</head>
<body>

<div class="container">

    <!-- CLOSE -->
    <div class="close" onclick="window.location.href='../student/dashboard.php'">✕</div>

    <h2>Inbox</h2>

    <!-- SUCCESS MESSAGE -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="success">
            <?= $_SESSION['success']; ?>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- SEND CODE BUTTON -->
    <a href="../../controllers/send_code.php">
        <button class="btn">Send Reset Code</button>
    </a>

    <!-- MESSAGES -->
    <?php if (!empty($messages)): ?>
        <?php foreach ($messages as $msg): ?>
            <div class="msg">
                <div><?= htmlspecialchars($msg['message']) ?></div>
                <small><?= $msg['created_at'] ?></small>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p style="text-align:center; color:#888;">No messages yet</p>
    <?php endif; ?>

</div>

</body>
</html>