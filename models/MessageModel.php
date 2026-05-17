<?php
require_once __DIR__ . "/../config/Database.php";

class MessageModel {

    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    
    // SEND CODE
    
    public function sendCode($userId, $code, $userType = "student") {

        $msg = "Your reset code is: " . $code;

        $stmt = $this->conn->prepare("
            INSERT INTO messages (user_id, message, code, user_type, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");

        return $stmt->execute([$userId, $msg, $code, $userType]);
    }

    
    // GET MESSAGES
    
    public function getMessages($userId) {

        $stmt = $this->conn->prepare("
            SELECT * FROM messages
            WHERE user_id = ?
            ORDER BY created_at DESC
        ");

        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    
    // DELETE MESSAGE (ADDED FIX)
    
    public function deleteMessage($id) {

        $stmt = $this->conn->prepare("
            DELETE FROM messages WHERE id = ?
        ");

        return $stmt->execute([$id]);
    }
}