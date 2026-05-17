<?php
session_start();
require_once "../../config/Database.php";


if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$conn = Database::getInstance()->getConnection();

$request_id = $_POST['request_id'] ?? null;
$student_id = $_POST['student_id'] ?? null;
$class_id = $_POST['class_id'] ?? null;
$action = $_POST['action'] ?? null;

if (!$request_id || !$student_id || !$class_id || !$action) {
    die("Missing data.");
}

/* =========================
   GET CLASS INFO (IMPORTANT)
========================= */
$stmt = $conn->prepare("SELECT * FROM classes WHERE id = ?");
$stmt->execute([$class_id]);
$class = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$class) {
    die("Class not found.");
}

/* =========================
   GET STUDENT NAME
========================= */
$stmt = $conn->prepare("SELECT fname, lname FROM users WHERE id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

$student_name = $student ? $student['fname'] . " " . $student['lname'] : "Student";

/* =========================
   ACCEPT REQUEST
========================= */
if ($action === "accept") {

    // update request
    $stmt = $conn->prepare("
        UPDATE join_requests
        SET status = 'accepted'
        WHERE id = ?
    ");
    $stmt->execute([$request_id]);

    // enroll student
    $stmt = $conn->prepare("
        INSERT INTO enrollments (class_id, student_id)
        VALUES (?, ?)
    ");
    $stmt->execute([$class_id, $student_id]);

    /* =========================
       NOTIFY STUDENT
    ========================= */
    $stmt = $conn->prepare("
        INSERT INTO notifications (user_id, message, type)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([
        $student_id,
        "Your request to join '{$class['class_name']}' has been approved.",
        "join_approved"
    ]);

    $_SESSION['success'] = "Student accepted and enrolled.";
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

    /* =========================
       NOTIFY STUDENT
    ========================= */
    $stmt = $conn->prepare("
        INSERT INTO notifications (user_id, message, type)
        VALUES (?, ?, ?)
    ");
    $stmt->execute([
        $student_id,
        "Your request to join '{$class['class_name']}' was rejected.",
        "join_rejected"
    ]);

    $_SESSION['success'] = "Request rejected.";
}

/* RETURN */
header("Location: view_join_requests.php?id=" . $class_id);
exit;
?>