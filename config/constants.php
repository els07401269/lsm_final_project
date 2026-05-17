<?php


// APP INFO (NON-SENSITIVE CONSTANTS)

define('APP_NAME', 'ELSA UNIVERSITY');


// USER ROLES

define('ROLE_STUDENT', 'student');
define('ROLE_PROFESSOR', 'professor');


// SESSION KEYS

define('SESSION_USER', 'user');
define('SESSION_SUCCESS', 'success_msg');
define('SESSION_ERROR', 'error_msg');


// MESSAGES

define('MSG_USER_EXISTS', 'Email or Username already exists');
define('MSG_INVALID_LOGIN', 'Invalid login credentials');
define('MSG_REGISTER_SUCCESS', 'Account created successfully!');
define('MSG_JOIN_SUCCESS', 'Successfully joined class!');
define('MSG_INVALID_CLASS', 'Invalid class code');
define('MSG_ALREADY_JOINED', 'You already joined this class');


// ROUTES (AVOID MAGIC PATHS)

define('ROUTE_LOGIN', 'views/auth/login.php');
define('ROUTE_STUDENT_DASH', 'views/student/dashboard.php');
define('ROUTE_PROF_DASH', 'views/professor/dashboard.php');
?>