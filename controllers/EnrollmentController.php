<?php
require_once __DIR__ . "/../models/EnrollmentModel.php";

class EnrollmentController {

    private $model;

    public function __construct() {
        $this->model = new EnrollmentModel();
    }

    
    // JOIN CLASS
    public function join($class_id, $student_id) {
        return $this->model->joinClass($class_id, $student_id);
    }

    
    // GET MY CLASSES (STUDENT)
    // NOTE: dapat updated na ang MODEL para may prof_name + block
    public function getMyClasses($student_id) {
        return $this->model->getStudentClasses($student_id);
    }

    
    // GET STUDENTS PER CLASS
    public function getStudents($class_id) {
        return $this->model->getClassStudents($class_id);
    }

    
    // LEAVE CLASS
    public function leave($class_id, $student_id) {
        return $this->model->leaveClass($class_id, $student_id);
    }

    
    // CHECK IF ALREADY ENROLLED
    public function isEnrolled($class_id, $student_id) {
        return $this->model->checkEnrollment($class_id, $student_id);
    }

    
    // OPTIONAL: helper (para consistent ang naming sa view)
    public function formatClassData($classes) {
        foreach ($classes as &$class) {

            // fallback safety
            $class['block'] = $class['block'] ?? $class['section'] ?? 'No Block Assigned';
            $class['prof_name'] = $class['prof_name'] ?? 'Unknown Professor';
        }

        return $classes;
    }
}