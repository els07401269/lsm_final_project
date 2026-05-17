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

<h2>Professor Inbox</h2>

<?php if (isset($_SESSION['success'])): ?>
    <p style="color:green"><?= $_SESSION['success']; ?></p>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<hr>

<?php if (!empty($messages)): ?>
    <?php foreach ($messages as $msg): ?>
        <div style="border:1px solid #ccc; padding:10px; margin-bottom:5px;">
            <?= $msg['message']; ?>
            <br>
            <small><?= $msg['created_at']; ?></small>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No messages yet.</p>
<?php endif; ?>