<?php

require_once __DIR__ . "/../config/Database.php";

class ClassModel {

    private $conn;

    /* =========================
       DB CONNECTION
    ========================= */
    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    /* =========================
       CREATE CLASS
    ========================= */
    public function createClass($data) {

        $sql = "
            INSERT INTO classes
            (
                class_name,
                block,
                program,
                class_code,
                professor_id
            )
            VALUES
            (
                :class_name,
                :block,
                :program,
                :class_code,
                :professor_id
            )
        ";

        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            ":class_name"   => trim($data["class_name"]),
            ":block"        => trim($data["block"] ?? ''),
            ":program"      => trim($data["program"]),
            ":class_code"   => trim($data["class_code"]),
            ":professor_id" => $data["professor_id"]
        ]);
    }

    /* =========================
       CHECK CLASS EXISTS
    ========================= */
    public function classExists($className, $professorId) {

        $stmt = $this->conn->prepare("
            SELECT id
            FROM classes
            WHERE class_name = :name
            AND professor_id = :prof
        ");

        $stmt->execute([
            ":name" => $className,
            ":prof" => $professorId
        ]);

        return $stmt->fetch() ? true : false;
    }

    /* =========================
       GET CLASSES BY PROFESSOR
    ========================= */
    public function getByProfessor($professorId) {

        $stmt = $this->conn->prepare("
            SELECT *
            FROM classes
            WHERE professor_id = :id
            ORDER BY id DESC
        ");

        $stmt->execute([
            ":id" => $professorId
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* =========================
       GET CLASS BY CODE
    ========================= */
    public function getByCode($code) {

        $stmt = $this->conn->prepare("
            SELECT *
            FROM classes
            WHERE class_code = :code
            LIMIT 1
        ");

        $stmt->execute([
            ":code" => $code
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* =========================
       GET CLASS BY ID
    ========================= */
    public function getById($id) {

        $stmt = $this->conn->prepare("
            SELECT *
            FROM classes
            WHERE id = :id
        ");

        $stmt->execute([
            ":id" => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /* =========================
       DELETE CLASS
    ========================= */
    public function deleteClass($id) {

        $stmt = $this->conn->prepare("
            DELETE FROM classes
            WHERE id = :id
        ");

        return $stmt->execute([
            ":id" => $id
        ]);
    }

    /* =========================
       UPDATE CLASS
    ========================= */
    public function updateClass($id, $data) {

        $stmt = $this->conn->prepare("
            UPDATE classes
            SET
                class_name = :class_name,
                block = :block,
                program = :program
            WHERE id = :id
        ");

        return $stmt->execute([
            ":class_name" => trim($data["class_name"]),
            ":block"      => trim($data["block"] ?? ''),
            ":program"    => trim($data["program"]),
            ":id"         => $id
        ]);
    }
}