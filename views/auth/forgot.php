<?php
session_start();

require_once "../../controllers/UserController.php";
require_once "../../controllers/MessageController.php";

$userController = new UserController();
$messageController = new MessageController();

$msg = "";




if (isset($_POST['email'])) {

    $email = $_POST['email'];

    
    $user = $userController->getByEmail($email);

    if ($user) {

        $_SESSION['reset_email'] = $email;

        $code = rand(100000, 999999);
        $_SESSION['code'] = $code;
        $_SESSION['code_time'] = time();

        
        $userType = $user['role'] ?? 'student';

        
        $messageController->sendCode(
            $user['id'],
            $code,
            $userType
        );

        $msg = "Code sent to your inbox!";
    } else {
        $msg = "Email not found!";
    }
}



if (isset($_POST['code'])) {

    if (!isset($_SESSION['code_time'])) {
        $msg = "Please request a code first.";
    }

    elseif (time() - $_SESSION['code_time'] > 300) {
        $msg = "Code expired!";
    }

    elseif ($_POST['code'] == $_SESSION['code']) {

        $userController->updatePassword(
            $_SESSION['reset_email'],
            $_POST['newpass']
        );

        unset($_SESSION['code']);
        unset($_SESSION['code_time']);
        unset($_SESSION['reset_email']);

        $msg = "Password updated! <a href='login.php'>Login</a>";

    } else {
        $msg = "Invalid code!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
</head>
<body>

<h2>Forgot Password</h2>

<form method="POST">
    <input name="email" placeholder="Enter Email" required>
    <button type="submit">Send Code</button>
</form>

<br>

<form method="POST">
    <input name="code" placeholder="Enter Code" required>
    <br><br>
    <input name="newpass" type="password" placeholder="New Password" required>
    <button type="submit">Reset Password</button>
</form>

<p style="color:green;">
    <?= $msg ?>
</p>

</body>
</html>