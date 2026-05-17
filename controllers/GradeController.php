<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/../models/GradeModel.php";

class GradeController {

    private $model;
    private $conn;

    public function __construct() {
        $this->model = new GradeModel();
        $this->conn = Database::getInstance()->getConnection();
    }

    public function save($class_id, $classwork_id, $student_id, $grade) {

        // =========================
        // GET CLASSWORK TYPE
        // =========================
        $stmt = $this->conn->prepare("
            SELECT type 
            FROM classworks 
            WHERE id = ?
        ");
        $stmt->execute([$classwork_id]);
        $cw = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cw) {
            $_SESSION['error'] = "Classwork not found";
            return false;
        }

        // =========================
        // NO GRADING FOR ANNOUNCEMENT
        // =========================
        if ($cw['type'] === 'announcement') {
            $_SESSION['error'] = "Announcement cannot be graded";
            return false;
        }

        // =========================
        // VALIDATION
        // =========================
        if (!is_numeric($grade)) {
            $_SESSION['error'] = "Invalid grade input";
            return false;
        }

        if ($grade > 100) {
            $_SESSION['error'] = "Max grade is 100 only";
            return false;
        }

        if ($grade < 0) {
            $_SESSION['error'] = "Invalid grade";
            return false;
        }

        // =========================
        // SAVE GRADE
        // =========================
        $result = $this->model->save(
            $class_id,
            $classwork_id,
            $student_id,
            $grade
        );

        if ($result) {
            $_SESSION['success'] = "Grade saved successfully";
        } else {
            $_SESSION['error'] = "Failed to save grade";
        }

        return $result;
    }

    public function getByClass($class_id) {
        return $this->model->getByClass($class_id);
    }
}