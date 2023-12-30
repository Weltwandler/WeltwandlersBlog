<?php
class BlogPost {
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

        function display($category_arr) {
            echo '<article class="blog-post">';
            echo '<a href="display-article.php?id=' . $this->post_id . '" class="blog-title"><h2>' . $this->title . '</h2></a>';
            echo '<p class="byline">Posted by <span class="author">' . $this->author_name . '</span></p>';
            foreach ($this->categories as $cat_id) {
                echo '<aside class="category">' . $category_arr[$cat_id]->display() . '</aside>';
            }
            echo '<p class="post-body">' . $this->content . '</p>';
            $publish_time = DateTime::createFromFormat("d/m/Y H:i:s", date("d/m/Y H:i:s", $this->publish_time));
            $publish_time->setTimezone(new DateTimeZone('Pacific/Auckland'));
            echo '<p class="publish-time">Published at <time>' . $publish_time->format("d/m/Y H:i:s") . '</time></p>';
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