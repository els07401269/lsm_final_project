<?php
session_start();

/* FIXED PATH (IMPORTANT) */
require_once __DIR__ . "/../config/Database.php";

/* DB CONNECTION */
$conn = Database::getInstance()->getConnection();

/* SAFETY CHECKS */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Invalid request method.");
}

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

/* INPUT DATA */
$request_id = $_POST['request_id'] ?? null;
$student_id = $_POST['student_id'] ?? null;
$class_id   = $_POST['class_id'] ?? null;
$action     = $_POST['action'] ?? null;

/* VALIDATION */
if (!$request_id || !$student_id || !$class_id || !$action) {
    die("Missing required data.");
}

/* =========================
   ACCEPT REQUEST
========================= */
if ($action === "accept") {

    /* update request status */
    $stmt = $conn->prepare("
        UPDATE join_requests
        SET status = 'accepted'
        WHERE id = ?
    ");
    $stmt->execute([$request_id]);

    /* enroll student */
    $stmt = $conn->prepare("
        INSERT INTO enrollments (class_id, student_id)
        VALUES (?, ?)
    ");
    $stmt->execute([$class_id, $student_id]);

    $_SESSION['success'] = "Student accepted and enrolled successfully.";

}

/* =========================
   REJECT REQUEST
========================= */
else {

    $stmt = $conn->prepare("
        UPDATE join_requests
        SET status = 'rejected'
        WHERE id = ?
    ");
    $stmt->execute([$request_id]);

    $_SESSION['success'] = "Join request rejected.";
}

/* REDIRECT BACK */
header("Location: ../views/professor/view_class.php?id=" . $class_id);
exit;
?>