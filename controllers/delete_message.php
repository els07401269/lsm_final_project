<?php
session_start();

require_once "../models/MessageModel.php";

if (!isset($_GET['id'])) {
    exit("No ID");
}

$model = new MessageModel();

$id = $_GET['id'];


$model->deleteMessage($id);

header("Location: ../views/student/dashboard.php");
exit;