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
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            $DBC = mysqli_connect(DBHOST, DBUSER, DBPW, DBNAME);
            
            if (mysqli_connect_errno()) {
                echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
                exit; //stop processing the page further
            };
            
            $columns = 'posts.post_id AS post_id, posts.user_id AS user_id, users.display_name AS author_name, posts.title AS title, posts.content AS content, posts.publish_time AS publish_time, posts.closed_for_comments AS closed_for_comments';
            $source = 'FROM posts RIGHT JOIN users ON posts.user_id = users.user_id';
            $conditions = "WHERE posts.post_id = ? AND NOW() >= SUBTIME(posts.publish_time, '0 13:0:0.000000')";
            
            $query = 'SELECT ' . $columns . ' ' . $source . ' ' . $conditions;
            
            //$query = 'SELECT * FROM posts WHERE post_id = ?';
            
            $stmt = mysqli_stmt_init($DBC);
            mysqli_stmt_prepare($stmt, $query);
            mysqli_stmt_bind_param($stmt,'i',$post_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
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
            $conditions = "WHERE posts_categories.post_id = ?";
            
            $query = "SELECT " . $columns . " " . $source . " " . $conditions;
            
            $stmt = mysqli_prepare($DBC, $query);
            mysqli_stmt_bind_param($stmt,'i',$post_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
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