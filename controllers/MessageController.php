<?php
require_once __DIR__ . "/../models/MessageModel.php";

class MessageController {

    private $model;

    public function __construct() {
        $this->model = new MessageModel();
    }

    public function sendCode($userId, $code, $userType = "student") {
        return $this->model->sendCode($userId, $code, $userType);
    }

    public function inbox($userId) {
        return $this->model->getMessages($userId);
    }
}