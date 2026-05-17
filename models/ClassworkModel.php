<?php
require_once __DIR__ . "/../config/Database.php";

class ClassworkModel {

    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    // GET ALL CLASSWORKS BY CLASS
    public function getByClass($class_id) {

        $stmt = $this->conn->prepare("
            SELECT * 
            FROM classworks
            WHERE class_id = ?
            ORDER BY created_at DESC
        ");

        $stmt->execute([$class_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // GET SINGLE CLASSWORK
    public function getById($id) {

        $stmt = $this->conn->prepare("
            SELECT * 
            FROM classworks
            WHERE id = ?
        ");

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // OPTIONAL: GET WITH STATUS (OPEN/CLOSED helper ready)
    public function getByClassWithStatus($class_id) {

        $stmt = $this->conn->prepare("
            SELECT *,
            CASE 
                WHEN due_date IS NOT NULL AND NOW() > due_date THEN 'CLOSED'
                ELSE 'OPEN'
            END AS status
            FROM classworks
            WHERE class_id = ?
            ORDER BY created_at DESC
        ");

        $stmt->execute([$class_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}