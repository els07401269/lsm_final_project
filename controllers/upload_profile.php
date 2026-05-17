<?php

// START SESSION (para ma-access ang login data ng user)
session_start();

// I-import ang UserController (dito dumadaan ang logic ng user actions)
require_once __DIR__ . "/UserController.php";

/*
CHECK IF USER IS LOGGED IN
- Kapag walang session user, ibabalik sa login page
*/
if (!isset($_SESSION['user'])) {
    header("Location: ../views/auth/login.php");
    exit;
}

// Gumawa ng instance ng UserController
$controller = new UserController();

/*
CHECK IF MAY NA-UPLOAD NA FILE (PROFILE PICTURE)
- $_FILES['profile'] = uploaded image ng user
*/
if (isset($_FILES['profile'])) {

    // Kunin ang ID at role ng naka-login na user
    $userId = $_SESSION['user']['id'];
    $role = $_SESSION['user']['role']; // role: student or professor

    /*
    UPDATE PROFILE PICTURE
    - Ipapa-process sa controller ang upload
    - Ibabalik ang file name/path kung successful
    */
    $result = $controller->updateProfile($userId, $_FILES['profile']);

    if ($result) {

        // SUCCESS MESSAGE
        $_SESSION['success'] = "Profile updated successfully!";

        /*
        UPDATE SESSION PROFILE PICTURE ONLY
        - Hindi nire-reset buong session
        - In-update lang ang profile_pic field
        */
        $_SESSION['user']['profile_pic'] = $result;

    } else {

        // ERROR MESSAGE kapag failed ang upload
        $_SESSION['error'] = "Upload failed!";
    }

    /*
    ROLE-BASED REDIRECT
    - Kung professor → balik sa professor page
    - Kung student → balik sa student page
    */
    if ($role === 'professor') {
        header("Location: ../views/professor/change_profile.php");
        exit;
    }

    header("Location: ../views/student/change_profile.php");
    exit;
}