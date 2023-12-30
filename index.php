<?php
include "php-functionality/sessions.php";
include "header.php";
include "menu.php";

?>
<main>
    <div id="blog-container" class="main-container">
        <?php
            include "blog-posts.php";
        ?>
    </div>
</main>
<?php
include "footer.php";
?>