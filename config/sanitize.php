<?php
class Sanitize {

    // CLEAN SINGLE VALUE
    public static function clean($data) {
        return htmlspecialchars(strip_tags(trim($data)));
    }

    // CLEAN ARRAY
    public static function cleanArray($array) {
        $clean = [];
    
        foreach ($array as $k => $v) {
            $clean[$k] = self::clean($v);
        }

        return $clean;
    }
}