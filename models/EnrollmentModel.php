<?php
require_once __DIR__ . "/../config/Database.php";

class EnrollmentModel {

    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    
    // JOIN CLASS
    public function joinClass($class_id, $student_id) {

        $check = $this->conn->prepare("
            SELECT id FROM enrollments
            WHERE class_id = :class_id AND student_id = :student_id
        ");

        $check->execute([
            ":class_id" => $class_id,
            ":student_id" => $student_id
        ]);

        if ($check->fetch()) {
            return "exists";
        }

        $stmt = $this->conn->prepare("
            INSERT INTO enrollments (class_id, student_id)
            VALUES (:class_id, :student_id)
        ");

        return $stmt->execute([
            ":class_id" => $class_id,
            ":student_id" => $student_id
        ]);
    }

    
    // GET STUDENT CLASSES
    public function getStudentClasses($student_id) {

        $stmt = $this->conn->prepare("
            SELECT 
                c.*,

                -- PROFESSOR NAME
                CONCAT(u.fname, ' ', u.lname) AS prof_name,
                u.profile_pic

            FROM classes c
            JOIN enrollments e ON c.id = e.class_id
            JOIN users u ON c.professor_id = u.id
            WHERE e.student_id = :id
            ORDER BY c.id DESC
        ");

        $stmt->execute([
            ":id" => $student_id
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    // GET CLASS STUDENTS
    public function getClassStudents($class_id) {

        $stmt = $this->conn->prepare("
            SELECT u.*
            FROM users u
            JOIN enrollments e ON u.id = e.student_id
            WHERE e.class_id = :id
        ");

        $stmt->execute([
            ":id" => $class_id
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    // LEAVE CLASS
    public function leaveClass($class_id, $student_id) {

        $stmt = $this->conn->prepare("
            DELETE FROM enrollments
            WHERE class_id = :class_id 
            AND student_id = :student_id
        ");

        return $stmt->execute([
            ":class_id" => $class_id,
            ":student_id" => $student_id
        ]);
    }

    
    // CHECK ENROLLMENT
    public function checkEnrollment($class_id, $student_id) {

        $stmt = $this->conn->prepare("
            SELECT id FROM enrollments
            WHERE class_id = :class_id 
            AND student_id = :student_id
        ");

        $stmt->execute([
            ":class_id" => $class_id,
            ":student_id" => $student_id
        ]);

        return $stmt->fetch() ? true : false;
    }
}