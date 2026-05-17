<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once "../../controllers/UserController.php";

$controller = new UserController();
$msg = "";

// HANDLE REGISTER
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $result = $controller->register($_POST);

    // USER EXISTS
    if (is_array($result) && isset($result["status"]) && $result["status"] === "exists") {
        $msg = $result["message"];
    }

    // SUCCESS
    else if ($result === true) {

        $_SESSION['success_msg'] = "Account created successfully. You can now login.";

        header("Location: login.php");
        exit;
    }

    // ERROR FALLBACK
    else {
        $msg = "Something went wrong. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>

<body>

<div class="bg">
    <img src="../../assets/images/bg.png">
</div>

<img src="../../assets/images/logo.png" class="logo-left">

<div class="auth-container">

    <h2>REGISTER</h2>

    <!-- MESSAGE -->
    <?php if (!empty($msg)) : ?>
        <p class="msg"><?= $msg ?></p>
    <?php endif; ?>

    <form method="POST">

        <input type="text" name="fname" placeholder="First Name" required>
        <input type="text" name="mname" placeholder="Middle Name">
        <input type="text" name="lname" placeholder="Last Name" required>

        <input type="email" name="email" placeholder="Email" required>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>

        <select name="role" required>
            <option value="">Select Role</option>
            <option value="student">Student</option>
            <option value="professor">Professor</option>
        </select>

        <button type="submit">Register</button>
    </form>

    <p class="link">
        Already have an account? <a href="login.php">Back to Login</a>
    </p>

</div>

<script src="../../assets/js/script.js"></script>

</body>
</html>