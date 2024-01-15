<?php
class Comment implements Iposted_content {
        public $id;
        public $author_user;
        public $author_name;
        public $title;
        public $content;
        public $replies;
        public $parent;

        function __construct($id, $author_user, $author_name, $title, $content, $replies = [], $parent = false) {
            $this->id = $id;
            $this->author_user = $author_user;
            $this->author_name = $author_name;
            $this->title = $title;
            $this->content = $content;
            $this->replies = $replies;
            $this->parent = $parent;
        }

        function getUserId() {
            return $this->author_user;
        }

        function display($indent=0) {
            echo '<section class="comment" style="padding-left: ' . $indent . 'em;">';
            echo '<h3 class="comment-title">' . $this->title . '</h3>';
            echo '<p class="comment-author">' . $this->author_name . '</p>';
            echo '<p class="comment-body">' . $this->content . '</p>';
            if (count($this->replies) > 0) {
                echo '<p class="replies-title">Replies:</p>';
                foreach ($this->replies as $reply) {
                    $reply->display($indent+1);
                }
            }
        }
    }

    ?>