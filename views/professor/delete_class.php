<?php
session_start();

require_once "../../config/Database.php";

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$conn = Database::getInstance()->getConnection();

$id = $_GET['id'] ?? null;

if ($id) {

    $stmt = $conn->prepare("
        DELETE FROM classes 
        WHERE id = ? AND professor_id = ?
    ");

    $stmt->execute([$id, $_SESSION['user']['id']]);
}

header("Location: my_classes.php");
exit;