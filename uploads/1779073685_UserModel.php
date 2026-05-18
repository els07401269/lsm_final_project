<?php

require_once __DIR__ . "/../config/Database.php";
require_once __DIR__ . "/../config/constants.php";

class UserModel {

    private $conn;

    public function __construct() {
        $this->conn = Database::getInstance()->getConnection();
    }

    
    // REGISTER (FIXED + SAFE)
    
    public function register($data) {
        try {

            // check is user already exist, if may kaparehong email/username
            $check = $this->conn->prepare("
                SELECT id FROM users
                WHERE email = :email OR username = :username
                LIMIT 1
            ");

            $check->execute([
                ":email" => $data["email"],
                ":username" => $data["username"]
            ]);

            $existingUser = $check->fetch(PDO::FETCH_ASSOC);

            // if user is already exist, if may user na di na allowed mag register
            if (!empty($existingUser)) {
                return [
                    "status" => "exists",
                    "message" => MSG_USER_EXISTS
                ];
            }

            //insert new user
            $stmt = $this->conn->prepare("
                INSERT INTO users
                (fname, mname, lname, email, username, password, role)
                VALUES
                (:fname, :mname, :lname, :email, :username, :password, :role)
            ");

            $stmt->execute([
                ":fname" => $data["fname"],
                ":mname" => $data["mname"] ?? "",
                ":lname" => $data["lname"],
                ":email" => $data["email"],
                ":username" => $data["username"],
                ":password" => password_hash($data["password"], PASSWORD_DEFAULT),
                ":role" => $data["role"]
            ]);

            return true;

        } catch (PDOException $e) {
            return ["error" => $e->getMessage()];
        }
    }

    
    // LOGIN
    
    public function login($username, $password) {
        try {

            $stmt = $this->conn->prepare("
                SELECT * FROM users 
                WHERE username = :username 
                LIMIT 1
            ");

            $stmt->execute([
                ":username" => $username
            ]);

            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) return false;

            if (password_verify($password, $user["password"])) {
                unset($user["password"]);
                return $user;
            }

            return false;

        } catch (PDOException $e) {
            return false;
        }
    }

    
    // UPDATE PASSWORD
    
    public function updatePassword($email, $newpass) {
        try {

            $stmt = $this->conn->prepare("
                UPDATE users 
                SET password = :pass 
                WHERE email = :email
            ");

            return $stmt->execute([
                ":pass" => password_hash($newpass, PASSWORD_DEFAULT),
                ":email" => $email
            ]);

        } catch (PDOException $e) {
            return false;
        }
    }

    // ADD THIS: GET USER BY EMAIL (for forgot password)
    public function getByEmail($email) {
        try {
            $stmt = $this->conn->prepare("
                SELECT * FROM users WHERE email = :email LIMIT 1
            ");

            $stmt->execute([
                ":email" => $email
            ]);

            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            return false;
        }
    }
}