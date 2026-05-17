<?php
require_once __DIR__ . "/../envParser.php";
require_once __DIR__ . "/../controllers/UserController.php";
require_once __DIR__ . "/../config/sanitize.php";
require_once __DIR__ . "/../config/validator.php";

header("Content-Type: application/json");

$controller = new UserController();

$action = $_GET['action'] ?? '';

switch ($action) {

    case "register":

        $data = Sanitize::cleanArray($_POST);

        if (!Validator::required($data)) {
            echo json_encode(["status"=>"error","msg"=>"All fields required"]);
            exit;
        }

        if (!Validator::email($data['email'])) {
            echo json_encode(["status"=>"error","msg"=>"Invalid email"]);
            exit;
        }

        if (!Validator::password($data['password'])) {
            echo json_encode(["status"=>"error","msg"=>"Weak password"]);
            exit;
        }

        $result = $controller->register($data);

        echo json_encode([
            "status" => $result === "exists" ? "error" : "success"
        ]);

    break;

    case "login":

        $user = $controller->login($_POST['username'], $_POST['password']);

        if ($user) {
            echo json_encode(["status"=>"success","user"=>$user]);
        } else {
            echo json_encode(["status"=>"error","msg"=>"Invalid login"]);
        }

    break;

    default:
        echo json_encode(["status"=>"error","msg"=>"Invalid action"]);
}