<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

require_once __DIR__ . "/../../config/Database.php";

$conn = Database::getInstance()->getConnection();

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $class_id = $_POST['class_id'] ?? null;
    $student_id = $_SESSION['user']['id'];

    if (!empty($class_id)) {

        // 1. REMOVE ENROLLMENT
        $stmt = $conn->prepare("
            DELETE FROM enrollments
            WHERE class_id = ? AND student_id = ?
        ");
        $stmt->execute([$class_id, $student_id]);

        // 2. DELETE JOIN REQUEST (IMPORTANT FIX)
        $stmt = $conn->prepare("
            DELETE FROM join_requests
            WHERE class_id = ? AND student_id = ?
        ");
        $stmt->execute([$class_id, $student_id]);

        $_SESSION['success'] = "You have successfully left the class.";
    }

    header("Location: my_classes.php");
    exit;
}