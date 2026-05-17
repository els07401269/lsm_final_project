<?php

// I-import ang UserModel (dito naka-handle ang database logic ng users)
require_once __DIR__ . "/../models/UserModel.php";

// I-import ang Database connection (para sa direct DB update ng profile)
require_once __DIR__ . "/../config/Database.php";


//USER CONTROLLER - Dito dumadaan ang login, register, update, at iba pang user actions

class UserController {

    private $model;

    // Constructor: automatic pag gumawa ng UserController object
    public function __construct() {
        $this->model = new UserModel();
    }

    
    //REGISTER USER - Ipinapasa ang data sa Model para ma-save sa database
    
    public function register($data) {
        return $this->model->register($data);
    }

    
    //LOGIN USER - Sinusuri kung tama ang username at password

    public function login($username, $password) {
        return $this->model->login($username, $password);
    }

    /* UPDATE PASSWORD-Pinapalitan ang password ng user gamit email as reference*/
    public function updatePassword($email, $newpass) {
        return $this->model->updatePassword($email, $newpass);
    }

    /*
    GET USER BY EMAIL
    - Useful sa reset password / verification
    */
    public function getByEmail($email) {
        return $this->model->getByEmail($email);
    }

    //Ginagamit sa profile change feature
    public function updateProfile($userId, $file)
    {
        // Allowed file types (para security)
        $allowed = ['jpg','jpeg','png','gif'];

        // Kunin extension ng file
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        // Check kung valid image format
        if (!in_array($ext, $allowed)) {
            return false;
        }

        //Gumagawa ng unique filename, para iwas overwrite ng files
        $newName = "profile_" . $userId . "_" . time() . "." . $ext;

        // Path kung saan ise-save ang file
        $uploadPath = __DIR__ . "/../uploads/" . $newName;

        //Kapag wala pang folder, gagawa ng bago
        if (!is_dir(__DIR__ . "/../uploads")) {
            mkdir(__DIR__ . "/../uploads", 0777, true);
        }

        //Ililipat ang uploaded file papunta sa uploads folder
        if (move_uploaded_file($file['tmp_name'], $uploadPath)) {

            // Kumuha ng database connection
            $conn = Database::getInstance()->getConnection();

            //Ise-save ang bagong profile picture filename sa users table
            $stmt = $conn->prepare("UPDATE users SET profile_pic = ? WHERE id = ?");
            $stmt->execute([$newName, $userId]);

            // Return filename kung success
            return $newName;
        }

        // Return false kapag failed upload
        return false;
    }
}