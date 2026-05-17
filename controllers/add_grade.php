<?php
session_start();
require_once "../config/Database.php";

$conn = Database::getInstance()->getConnection();

$class_id = $_POST['class_id'];
$classwork_id = $_POST['classwork_id'];
$student_id = $_POST['student_id'];
$grade = $_POST['grade'];

/* check if exists */
$stmt = $conn->prepare("
SELECT id FROM grades 
WHERE class_id=? AND classwork_id=? AND student_id=?
");
$stmt->execute([$class_id,$classwork_id,$student_id]);

if($stmt->rowCount()>0){

    $stmt = $conn->prepare("
    UPDATE grades SET grade=? 
    WHERE class_id=? AND classwork_id=? AND student_id=?
    ");

    $stmt->execute([$grade,$class_id,$classwork_id,$student_id]);

}else{

    $stmt = $conn->prepare("
    INSERT INTO grades(class_id,classwork_id,student_id,grade)
    VALUES(?,?,?,?)
    ");

    $stmt->execute([$class_id,$classwork_id,$student_id,$grade]);
}

$_SESSION['grade_success']="Grade saved!";
header("Location: ../views/professor/view_class.php?id=".$class_id);
exit;