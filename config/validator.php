<?php
class Validator {

//tinitingnan kung may empty na input
    public static function required($data) {
        foreach ($data as $value) {
            if (empty($value)) return false;
        }
        return true;
    }

    public static function email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function password($password) {
        return strlen($password) >= 6;
    }
}