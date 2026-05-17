<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once "../../controllers/ClassController.php";
require_once "../../controllers/MessageController.php";

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user = $_SESSION['user'];

$classController = new ClassController();
$messageController = new MessageController();

$messages = $messageController->inbox($user['id']);

$msg = "";

function generateCode($length = 6)
{
    $chars = "ABCDEFGHJKLMNPQRSTUVWXYZ23456789";
    return substr(str_shuffle($chars), 0, $length);
}

/* =========================
   CREATE CLASS HANDLER
========================= */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $className = trim($_POST['class_name'] ?? '');
    $block     = trim($_POST['block'] ?? ''); // ✅ FIXED
    $program   = trim($_POST['program'] ?? '');

    if ($className === '') {

        $msg = "Class name is required.";

    } else {

        if ($classController->exists($className, $user['id'])) {

            $msg = "Class name already exists.";

        } else {

            $generatedCode = generateCode();

            $data = [
                'class_name'   => $className,
                'block'        => !empty($block) ? $block : null, // ✅ FIXED
                'program'      => $program,
                'class_code'   => $generatedCode,
                'professor_id' => $user['id']
            ];

            if ($classController->create($data)) {

                $msg = "
                    Class created successfully! <br>
                    Your Class Code is:
                    <strong style='font-size:22px'>
                        $generatedCode
                    </strong>
                ";

            } else {

                $msg = "Failed to create class.";
            }
        }
    }
}

$profile = $user['profile_pic'] ?? null;
$initial = strtoupper(substr($user['fname'], 0, 1));
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Create Class</title>

<!-- CSS MO (UNCHANGED) -->
<style>
/* ALL YOUR ORIGINAL CSS - NO CHANGES */
body {
    margin: 0;
    font-family: Arial, sans-serif;
    background: linear-gradient(135deg, #153c6b, #7d784a);
}

.topbar {
    background: #2c3e50;
    color: white;
    padding: 15px 25px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

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

.avatar,
.avatar-letter {
    width: 45px;
    height: 45px;
    border-radius: 50%;
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

.nav {
    background: #34495e;
    padding: 14px 25px;
    display: flex;
    gap: 25px;
}

.nav a {
    color: white;
    text-decoration: none;
    font-weight: bold;
}

.container {
    display: flex;
    justify-content: center;
    padding: 50px 20px;
}

.welcome-card {
    width: 550px;
    background: white;
    border-radius: 18px;
    padding: 40px;
}

h2 {
    text-align: center;
}

.success {
    background: #2ecc71;
    color: white;
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 20px;
    text-align: center;
}

.form-group {
    margin-bottom: 20px;
}

label {
    font-weight: bold;
}

.form-control {
    width: 100%;
    padding: 12px;
    border: 1px solid #ccc;
    border-radius: 10px;
}

.btn {
    width: 100%;
    padding: 14px;
    background: #2c3e50;
    color: white;
    border: none;
    border-radius: 10px;
    cursor: pointer;
}

.btn:hover {
    background: #1a252f;
}
</style>

</head>
<body>

<div class="topbar">
    <h2>CREATE CLASS</h2>

    <div class="top-actions">


    </div>
</div>

<div class="nav">
    <a href="dashboard.php">Dashboard</a>
    <a href="create_class.php">Create Class</a>
    <a href="my_classes.php">My Classes</a>
</div>

<div class="container">

    <div class="welcome-card">

        <h2>Create New Class</h2>

        <?php if (!empty($msg)): ?>
            <div class="success"><?= $msg ?></div>
        <?php endif; ?>

        <form method="POST">

            <div class="form-group">
                <label>Class Name</label>
                <input type="text" name="class_name" class="form-control" required>
            </div>

            <!-- 🔥 FIXED ONLY -->
            <div class="form-group">
                <label>Block</label>
                <input type="text" name="block" class="form-control">
            </div>

            <div class="form-group">
                <label>Program</label>
                <select name="program" class="form-control">

                    <option value="BSIT">Bachelor of Science in Information Technology (BSIT)</option>
                    <option value="BSCS">Bachelor of Science in Computer Science (BSCS)</option>
                    <option value="BSIS">Bachelor of Science in Information Systems (BSIS)</option>
                    <option value="BSEMC">Bachelor of Science in Entertainment and Multimedia Computing (BSEMC)</option>
                    <option value="BSBA">Bachelor of Science in Business Administration (BSBA)</option>
                    <option value="BSA">Bachelor of Science in Accountancy (BSA)</option>
                    <option value="BSHM">Bachelor of Science in Hospitality Management (BSHM)</option>
                    <option value="BSTM">Bachelor of Science in Tourism Management (BSTM)</option>
                    <option value="BSN">Bachelor of Science in Nursing (BSN)</option>
                    <option value="BSCRIM">Bachelor of Science in Criminology (BSCRIM)</option>
                    <option value="BSED">Bachelor of Secondary Education (BSED)</option>
                    <option value="BEED">Bachelor of Elementary Education (BEED)</option>
                    <option value="ABCOMM">AB Communication (ABCOMM)</option>
                    <option value="ABPSY">AB Psychology (ABPSY)</option>
                    <option value="BSCE">Bachelor of Science in Civil Engineering (BSCE)</option>
                    <option value="BSEE">Bachelor of Science in Electrical Engineering (BSEE)</option>
                    <option value="BSME">Bachelor of Science in Mechanical Engineering (BSME)</option>
                    <option value="BSCHEM">Bachelor of Science in Chemistry (BSCHEM)</option>
                    <option value="BSBIO">Bachelor of Science in Biology (BSBIO)</option>
                    <option value="BAPHILO">Bachelor of Arts in Philosophy (BAPHILO)</option>
                </select>
            </div>

            <button type="submit" class="btn">Create Class</button>

        </form>

    </div>

</div>

</body>
</html>