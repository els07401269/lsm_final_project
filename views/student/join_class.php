<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

require_once __DIR__ . "/../../controllers/ClassController.php";
require_once __DIR__ . "/../../config/Database.php";

$user = $_SESSION['user'];
$conn = Database::getInstance()->getConnection();

$classController = new ClassController();

$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $code = trim($_POST["class_code"]);

    if (empty($code)) {

        $msg = "Please enter a class code.";

    } else {

        $class = $classController->getByCode($code);

        if (!$class) {

            $msg = "Invalid class code.";

        } else {

            $class_id = $class["id"];
            $student_id = $user["id"];

            /* CHECK ENROLLMENT */
            $checkEnroll = $conn->prepare("
                SELECT id FROM enrollments
                WHERE class_id = ? AND student_id = ?
            ");
            $checkEnroll->execute([$class_id, $student_id]);

            if ($checkEnroll->fetch()) {

                $msg = "You are already enrolled in this class.";

            } else {

                /* CHECK REQUEST */
                $stmt = $conn->prepare("
                    SELECT * FROM join_requests
                    WHERE class_id = ? AND student_id = ?
                    ORDER BY id DESC
                    LIMIT 1
                ");
                $stmt->execute([$class_id, $student_id]);
                $existing = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($existing) {

                    if ($existing['status'] === 'pending') {

                        $msg = "⏳ Waiting for professor approval.";

                    } else {

                        $stmt = $conn->prepare("
                            UPDATE join_requests
                            SET status = 'pending'
                            WHERE class_id = ? AND student_id = ?
                        ");
                        $stmt->execute([$class_id, $student_id]);

                        $msg = "⏳ Join request sent again! Waiting for approval.";
                    }

                } else {

                    $stmt = $conn->prepare("
                        INSERT INTO join_requests (class_id, student_id, status)
                        VALUES (?, ?, 'pending')
                    ");
                    $stmt->execute([$class_id, $student_id]);

                    $msg = "⏳ Join request sent! Waiting for approval.";
                }

                /* NOTIFICATION */
                $stmt = $conn->prepare("
                    SELECT professor_id FROM classes WHERE id = ?
                ");
                $stmt->execute([$class_id]);
                $prof = $stmt->fetch(PDO::FETCH_ASSOC);

                $professor_id = $prof['professor_id'] ?? null;

                $stmt = $conn->prepare("
                    SELECT fname, lname FROM users WHERE id = ?
                ");
                $stmt->execute([$student_id]);
                $u = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($professor_id) {

                    $stmt = $conn->prepare("
                        INSERT INTO notifications (user_id, message, type)
                        VALUES (?, ?, ?)
                    ");

                    $stmt->execute([
                        $professor_id,
                        "📩 New join request from " . $u['fname'] . " " . $u['lname'],
                        "join_request"
                    ]);
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Join Class</title>

<style>
body {
    margin: 0;
    font-family: Arial;
    background: linear-gradient(135deg, #153c6b, #7d784a);
}

.topbar {
    background: #2c3e50;
    color: white;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo {
    height: 60px;
}

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

.container {
    padding: 30px;
    display: flex;
    justify-content: center;
}

.card {
    width: 400px;
    background: white;
    padding: 25px;
    border-radius: 10px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    text-align: center;
}

input {
    padding: 10px;
    width: 80%;
    margin-top: 10px;
}

button {
    margin-top: 15px;
    padding: 10px 15px;
    background: #1abc9c;
    border: none;
    color: white;
    cursor: pointer;
}

button:hover {
    background: #16a085;
}

.msg {
    margin-top: 15px;
    font-weight: bold;
    color: #2c3e50;
}
</style>

</head>

<body>

<div class="topbar">
    <div style="display:flex; align-items:center; gap:15px;">
        <img src="../../assets/images/logo.png" class="logo">
        <h2>JOIN CLASS</h2>
    </div>
</div>

<div class="nav">
    <a href="dashboard.php">Dashboard</a>
    <a href="join_class.php">Join Class</a>
    <a href="my_classes.php">My Classes</a>
</div>

<div class="container">

<div class="card">
    <h2>Join Class</h2>

    <form method="POST">
        <input type="text" name="class_code" placeholder="Enter Class Code" required>
        <br>
        <button type="submit">Send Request</button>
    </form>

    <?php if ($msg): ?>
        <p class="msg"><?= $msg ?></p>
    <?php endif; ?>
</div>

</div>

</body>
</html>