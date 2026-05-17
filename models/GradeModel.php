<?php
require_once __DIR__ . "/../config/Database.php";

class GradeModel {

    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    // INSERT OR UPDATE GRADE
    public function save($class_id, $classwork_id, $student_id, $grade) {

        $check = $this->conn->prepare("
            SELECT id FROM grades
            WHERE class_id = ? AND classwork_id = ? AND student_id = ?
        ");
        $check->execute([$class_id, $classwork_id, $student_id]);

        if ($check->rowCount() > 0) {

            $update = $this->conn->prepare("
                UPDATE grades 
                SET grade = ?
                WHERE class_id = ? AND classwork_id = ? AND student_id = ?
            ");

            return $update->execute([$grade, $class_id, $classwork_id, $student_id]);

        } else {

            $insert = $this->conn->prepare("
                INSERT INTO grades (class_id, classwork_id, student_id, grade)
                VALUES (?, ?, ?, ?)
            ");

            return $insert->execute([$class_id, $classwork_id, $student_id, $grade]);
        }
    }

    // GET BY CLASS
    public function getByClass($class_id) {

        $stmt = $this->conn->prepare("
            SELECT g.*, u.fname, u.lname, c.title, c.max_points
            FROM grades g
            JOIN users u ON g.student_id = u.id
            JOIN classworks c ON g.classwork_id = c.id
            WHERE g.class_id = ?
            ORDER BY g.id DESC
        ");

        $stmt->execute([$class_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}