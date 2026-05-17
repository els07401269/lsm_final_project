<?php
session_start();
require_once "../../config/Database.php";

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/login.php");
    exit;
}

$conn = Database::getInstance()->getConnection();

$user = $_SESSION['user'];

$classwork_id = $_POST['classwork_id'];
$class_id = $_POST['class_id'];

/* FILE UPLOAD */
$file_path = null;

if (!empty($_FILES['file']['name'])) {

    $uploadDir = __DIR__ . "/../../uploads/";

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = time() . "_" . basename($_FILES['file']['name']);
    $fileName = preg_replace("/[^a-zA-Z0-9.\-_]/", "", $fileName);

    $targetFile = $uploadDir . $fileName;

    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFile)) {
        $file_path = "uploads/" . $fileName;
    }
}

/* INSERT SUBMISSION */
$stmt = $conn->prepare("
    INSERT INTO submissions 
    (classwork_id, class_id, student_id, file_path, submitted_at)
    VALUES (?, ?, ?, ?, NOW())
");

$stmt->execute([
    $classwork_id,
    $class_id,
    $user['id'],
    $file_path
]);

$_SESSION['success'] = "Submitted successfully!";

header("Location: view_class.php?id=" . $class_id);
exit;
?>