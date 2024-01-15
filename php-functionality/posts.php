<?php

include "comments.php";

class BlogPost implements Iposted_content {
        public $post_id;
        public $author_user;
        public $author_name;
        public $categories;
        public $title;
        public $content;
        public $publish_time;
        public $closed_for_comments;
        public $closed_for_public_comments;
        public $comments;

        function __construct($post_id, $author_user, $author_name, $categories, $title, $content, $publish_time, $closed_for_comments, $comments) {
            $this->post_id = $post_id;
            $this->author_user = $author_user;
            $this->author_name = $author_name;
            $this->categories = $categories;
            $this->title = $title;
            $this->content = $content;
            $this->publish_time = $publish_time;
            $this->closed_for_comments = $closed_for_comments;
            $this->comments = $comments;

            $age = (time()-$publish_time) / (60 * 60 );
            $closed_for_public_comments = $age >= 24;
        }

        function getUserId() {
            return $this-> author_user;
        }

        function display($category_arr) {
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $err = 0;
                $msg = '';

                if (isset($_POST['title']) && is_string($_POST['title'])) {
                    $new_title = cleanStr($_POST['title']);
                } else {
                    $err++;
                    $msg .= 'No valid title found ';
                }

                if (isset($_POST['content']) && is_string($_POST['content'])) {
                    $new_content = bb_ify($_POST['content']);
                } else {
                    $err++;
                    $msg .= 'Content invalid ';
                }

                if (isset($_POST['id']) && is_int((int)$_POST['id'])) {
                    $update_id = $_POST['id'];
                } else {
                    $err++;
                    $msg .= 'Invalid ID ';
                }

                if ($err == 0) {
                    $DBC = mysqli_connect(DBHOST, DBUSER, DBPW, DBNAME);

                    if (mysqli_connect_errno()) {
                        echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
                        exit; //stop processing the page further
                    };

                    $query = "UPDATE posts SET title=?, content=? WHERE post_id=?";
                    $stmt = mysqli_prepare($DBC, $query);
                    mysqli_stmt_bind_param($stmt, "ssi", $new_title, $new_content, $update_id);
                    $res = mysqli_stmt_execute($stmt);
                    mysqli_close($DBC);
                    if (!$res) {
                        $err++;
                        $msg .= 'Error updating post  ';
                    } else {
                        header("Refresh:0");
                    }
                }

                if ($err > 0) {
                    echo '<p class="error-message">' . $msg . '</p>';
                    exit;
                }
            
            }
            
            echo '<article class="blog-post">';
            echo '<a href="display-article.php?id=' . $this->post_id . '" class="blog-title"><h2>' . $this->title . '</h2></a>';
            echo '<p class="byline">Posted by <span class="author-name">' . $this->author_name . '</span></p>';
            foreach ($this->categories as $cat_id) {
                echo '<aside class="category">' . $category_arr[$cat_id]->display() . '</aside>';
            }
            if (canEdit($this)) {
                ?>
                <button class="edit-button" onclick="toggle('edit-window<?php echo $this->post_id ?>');">Edit</button>
                <div class="edit-window" id="edit-window<?php echo $this->post_id ?>">
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                        <input id="id" name="id" type="number" style="display:none;" value="<?php echo $this->post_id ?>">
                        <label for="title">Title:</label><br>
                        <input type="text" id="title" name="title" value="<?php echo $this->title ?>" required>
                        <br><br>
                        <label for="content">Post:</label><br>
                        <textarea id="content" name="content" rows="8" cols="50"><?php echo strip_nl($this->content)?></textarea><br>
                        <input type="submit" value="Publish">
                        <input type="reset" value="Cancel" onclick="toggle('edit-window<?php echo $this->post_id ?>');">
                    </form>
                </div>
                <?php
            }
            echo '<p class="post-body">' . de_bb_ify($this->content) . '</p>';
            $publish_time = DateTime::createFromFormat("d/m/Y H:i:s", date("d/m/Y H:i:s", $this->publish_time));
            $publish_time->setTimezone(new DateTimeZone('Pacific/Auckland'));
            echo '<p class="publish-time">Published at <time>' . $publish_time->format("d/m/Y H:i:s") . '</time> NZDT</p>';
            foreach ($this->comments as $comment) {
                if ($comment->parent == null) {
                    $comment->display();
                }
            }
            /*
            // Comment function to be added
            if (loggedIn()) {
                echo '<p><span class="comment-button" onClick="toggle(\'comment-form\');">New Comment</span></p>';
                ?>
                <form id="comment-form" style="display:none;">
                    <label for="comment-title">Title:</label><br>
                    <input type="text" id="comment-title" name="comment-title">
                    <br><br>
                    <label for="comment-body">Post:</label><br>
                    <textarea id="comment-body" name="comment-body" rows="5" columns="30"></textarea>
                    <br><br>
                    <input type="submit" value="Post">
                    <input type="reset" value="Clear">
                </form>
                <?php
            }
            */
            echo '<br>';
            echo '</article>';          
        }
    }
?>