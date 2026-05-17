<?php
require_once __DIR__ . "/../config/Database.php";

class ClassworkController {

    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    // =========================
    // ADD CLASSWORK (WITH FILE)
    // =========================
    public function add($class_id, $title, $type, $description, $max_points, $file_path = null) {

        $stmt = $this->conn->prepare("
            INSERT INTO classworks 
            (class_id, title, type, description, max_points, file_path)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $class_id,
            $title,
            $type,
            $description,
            $max_points,
            $file_path
        ]);
    }

    // =========================
    // GET ALL CLASSWORKS
    // =========================
    public function getByClass($class_id) {

        $stmt = $this->conn->prepare("
            SELECT * FROM classworks
            WHERE class_id = ?
            ORDER BY created_at DESC
        ");

        $stmt->execute([$class_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================
    // GET SINGLE CLASSWORK
    // =========================
    public function getOne($id) {

        $stmt = $this->conn->prepare("
            SELECT * FROM classworks
            WHERE id = ?
        ");

        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // =========================
    // OPTIONAL: DELETE CLASSWORK
    // =========================
    public function delete($id) {

        $stmt = $this->conn->prepare("
            DELETE FROM classworks
            WHERE id = ?
        ");

        return $stmt->execute([$id]);
    }

    // =========================
    // OPTIONAL: UPDATE CLASSWORK
    // =========================
    public function update($id, $title, $type, $description, $max_points) {

        $stmt = $this->conn->prepare("
            UPDATE classworks
            SET title = ?, type = ?, description = ?, max_points = ?
            WHERE id = ?
        ");

        return $stmt->execute([
            $title,
            $type,
            $description,
            $max_points,
            $id
        ]);
    }
}