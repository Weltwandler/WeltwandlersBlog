<?php

include "php-functionality/sessions.php";
include "header.php";
include "menu.php";

$_SESSION['logged_in'] = 0;
$_SESSION['role'] = 0;
$_SESSION['userid'] = 0;
$_SESSION['display_name'] = "";

header("refresh: 3; url=index.php");

?>

<main>
    <h2>Logout successful!</h2>
    <p>This page will redirect you to the main page in 3 seconds.</p>
    <a href="index.php">Redirect not working or impatient? Click here!</a>
</main>

<?php
include "footer.php";
?>