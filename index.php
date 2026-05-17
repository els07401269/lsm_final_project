<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once __DIR__ . "/envParser.php";
require_once __DIR__ . "/config/constants.php";
require_once __DIR__ . "/controllers/UserController.php";
require_once __DIR__ . "/config/sanitize.php";

$controller = new UserController();
$action = $_GET['action'] ?? '';

// HANDLE POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $data = Sanitize::cleanArray($_POST);

    // LOGIN
    if ($action === 'login') {

        $user = $controller->login($data['username'], $data['password']);

        if ($user) {

            $_SESSION['user'] = $user;
            $_SESSION['success_msg'] = "Login successful! Welcome " . $user['fname'];

            //example of magic string
            if ($user['role'] === ROLE_PROFESSOR) {
                header("Location: views/professor/dashboard.php");
                exit;
            }

            header("Location: views/student/dashboard.php");
            exit;

        } else {
            $_SESSION['error_msg'] = "Invalid username or password";
            header("Location: views/auth/login.php");
            exit;
        }
    }

    // REGISTER
    if ($action === 'register') {

        $result = $controller->register($data);

        // SUCCESS
        if ($result === true) {

            unset($_SESSION['user']); // remove old logged-in user

            $_SESSION['success_msg'] = "Account created successfully! Please login.";
            header("Location: views/auth/login.php");
            exit;
        }

        // EXISTS
        if (is_array($result) && isset($result['status']) && $result['status'] === 'exists') {
            $_SESSION['error_msg'] = $result['message'];
            header("Location: views/auth/login.php");
            exit;
        }

        if ($result === MSG_USER_EXISTS) {
            $_SESSION['error_msg'] = MSG_USER_EXISTS;
            header("Location: views/auth/login.php");
            exit;
        }

        $_SESSION['error_msg'] = "Registration failed";
        header("Location: views/auth/login.php");
        exit;
    }
}

// AUTO REDIRECT
if (isset($_SESSION['user'])) {

    if ($_SESSION['user']['role'] === ROLE_PROFESSOR) {
        header("Location: views/professor/dashboard.php");
        exit;
    }

    header("Location: views/student/dashboard.php");
    exit;
}

// DEFAULT PAGE
header("Location: views/auth/login.php");
exit;