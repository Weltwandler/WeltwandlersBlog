<?php

include "user.php";
include "posted-content.php";

session_start();
checkSession();

function loggedIn() {
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === 1) {
        return true;
    } else {
        return false;
    }
}

function isAdmin() {
    if ($_SESSION['role'] === 3) {
        return true;
    } else {
        return false;
    }
}

function isAuthor() {
    if ($_SESSION['role'] >= 2) {
        return true;
    } else {
        return false;
    }
}

function canEdit($posted_content) {
    if ($posted_content instanceof Iposted_content && loggedIn()) {
        if ($_SESSION['userid'] == $posted_content->getUserId()) {
            return true;
        }
    }
    return false;
}

function checkSession() {
    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] == 0) {
        $_SESSION['logged_in'] = 0;
        $_SESSION['role'] = 0;
        $_SESSION['userid'] = 0;
        // $_SESSION['theme_choice'] = 1;
    }

}

/* Session variables to be set by login / logout form. Variables will be:
    - logged_in (1 for yes, 0 for no)
    - role (equal to roleId from the database or 0 for not logged in)
    - userid (equal to userId from the database)
    - firstname
    - theme_choice
    */
?>