<?php
include "php-functionality/sessions.php";
include "header.php";
include "menu.php";
include "php-functionality/posts.php";
include "php-functionality/comments.php";

?>
<main>
    <div id="post-container" class="main-container">
        <?php
            $post_id = $_GET['id'];
            $category_arr = [];

            // Get post
            $DBC = mysqli_connect(DBHOST, DBUSER, DBPW, DBNAME);
    
            if (mysqli_connect_errno()) {
                echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
                exit; //stop processing the page further
            };

            $columns = "posts.post_id AS post_id, posts.user_id AS user_id, users.display_name AS author_name, posts.title AS title, posts.content AS content, posts.publish_time AS publish_time, posts.closed_for_comments AS closed_for_comments";
            $source = "FROM posts RIGHT JOIN users ON posts.user_id = users.user_id";
            $conditions = "WHERE NOW() >= posts.publish_time AND (NOW() < posts.unpublish_time OR posts.unpublish_time IS NULL AND posts.post_id = " . $post_id . ")";

            $result = assemble_query($DBC, $columns,$source, $conditions);

            $row = mysqli_fetch_assoc($result);

            if ($row == null) {
                echo 'Invalid post';
                exit;
            } else {
                $post = new BlogPost($row['post_id'], $row['user_id'], $row['author_name'], [], $row['title'], de_bb_ify($row['content']), strtotime($row['publish_time']), $row['closed_for_comments'], []);
            }

            // Get categories

            $columns = "posts_categories.category_id AS category_id, categories.category_name AS category_name";
            $source = "FROM posts_categories JOIN categories ON posts_categories.category_id = categories.category_id";
            $conditions = "WHERE " . $post->post_id . " = posts_categories.post_id";

            $result = assemble_query($DBC, $columns,$source, $conditions);

            while ($row = mysqli_fetch_assoc($result)) {
                array_push($post->categories, $category_arr[$row->category_id]);
            }

            mysqli_close($DBC);

            $post->display($category_arr);
        ?>
    </div>
</main>
<?php
include "footer.php";
?>