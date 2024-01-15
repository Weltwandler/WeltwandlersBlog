<?php
include "php-functionality/config.php";
include "php-functionality/sharedqueries.php";

function refreshRole() {
    if (isset($_SESSION['role'])) {
        switch ($_SESSION['role']) {
            case 3:
                $userStyle = "./css/admin.css";
                break;
            case 2:
                $userStyle = "./css/author.css";
                break;
            case 1:
                $userStyle = "./css/user.css";
                break;
            default:
                $userStyle = "./css/guest.css";
        }
    } else {
        $userStyle = "./css/guest.css";
    }
    return $userStyle;
}

function refreshTheme() {
    if (isset($_SESSION['theme_choice'])) {
        switch ($_SESSION['theme_choice']) {
            case 1:
                $themeStyle="./css/default.css";
                break;
            // Add more options here
            default:
                $themeStyle="./css/default.css";
        }
    } else {
        $themeStyle="./css/default.css";
    }
    return $themeStyle;
}

$userStyle = refreshRole();
$themeStyle = refreshTheme();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Weltwandlers Blog</title>
    <link rel="stylesheet" type="text/css" href="./css/main.css">
    <link rel="stylesheet" type="text/css" href="<?=$themeStyle;?>">
    <link rel="stylesheet" type="text/css" href="<?=$userStyle;?>">
    
</head>
<body>
    <div id="wrapper">
        <header id="header">
            <h1>Weltwandlers Blog</h1>
            <div id="subtitle">
                <aside class="subtitle">A journey across worlds</aside>
            </div>
            <?php
            if (loggedIn()) {
                echo "<p>Hello, " . $_SESSION['display_name'] . "</p>";
            }
            ?>