<?php
session_start();
require_once "../../config/Database.php";

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$conn = Database::getInstance()->getConnection();

$class_id = $_GET['id'] ?? null;
if (!$class_id) die("Invalid class ID");

/* GET PENDING REQUESTS */
$stmt = $conn->prepare("
    SELECT jr.*, u.fname, u.lname
    FROM join_requests jr
    JOIN users u ON u.id = jr.student_id
    WHERE jr.class_id = ? AND jr.status = 'pending'
    ORDER BY jr.created_at DESC
");

$stmt->execute([$class_id]);
$requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<title>Join Requests</title>

<style>
body {
    font-family: Arial;
    background: #f5f7fb;
    margin: 0;
}

.header {
    background: #1e293b;
    color: white;
    padding: 15px;
    font-size: 18px;
}

.card {
    background: white;
    margin: 15px;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
}

button {
    padding: 6px 10px;
    border: none;
    cursor: pointer;
    border-radius: 5px;
}

.accept { background: #10b981; color: white; }
.reject { background: #ef4444; color: white; }
</style>

</head>

<body>

<div class="header">
    Join Requests
</div>

<div class="card">

<?php if (empty($requests)): ?>
    <p>No pending requests.</p>
<?php endif; ?>

<?php foreach ($requests as $r): ?>

    <div style="padding:10px;border-bottom:1px solid #eee;">

        <b><?= htmlspecialchars($r['fname'] . " " . $r['lname']) ?></b>

        <form method="POST" action="process_request.php" style="margin-top:10px;">

            <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
            <input type="hidden" name="student_id" value="<?= $r['student_id'] ?>">
            <input type="hidden" name="class_id" value="<?= $r['class_id'] ?>">

            <button class="accept" name="action" value="accept">
                Accept
            </button>

            <button class="reject" name="action" value="reject">
                Reject
            </button>

        </form>

    </div>

<?php endforeach; ?>

</div>

</body>
</html>