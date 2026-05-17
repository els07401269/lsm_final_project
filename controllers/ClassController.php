<?php

require_once __DIR__ . "/../models/ClassModel.php";

/*
CLASS CONTROLLER
- Middle layer ng MVC
- Dito dumadaan ang logic bago model/database
*/
class ClassController {

    private $model;

    public function __construct() {
        $this->model = new ClassModel();
    }

    /*
    CREATE CLASS
    */
    public function create($data) {
        return $this->model->createClass($data);
    }

    /*
    GET CLASSES BY PROFESSOR
    */
    public function getByProfessor($id) {
        return $this->model->getByProfessor($id);
    }

    /*
    GET CLASS BY CODE (student join)
    */
    public function getByCode($code) {
        return $this->model->getByCode($code);
    }

    /*
    CHECK DUPLICATE CLASS
    */
    public function exists($className, $profId) {
        return $this->model->classExists($className, $profId);
    }

    /*
    GET CLASS BY ID
    (🔥 ADDED - ginagamit sa view_class.php, grading, etc.)
    */
    public function getById($id) {
        return $this->model->getById($id);
    }

    /*
    DELETE CLASS
    (🔥 ADDED - para sa professor my classes)
    */
    public function delete($id) {
        return $this->model->deleteClass($id);
    }

    /*
    UPDATE CLASS
    (🔥 ADDED - optional edit feature)
    */
    public function update($id, $data) {
        return $this->model->updateClass($id, $data);
    }
}