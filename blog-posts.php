<?php

include "php-functionality/posts.php";

$blog_arr = [];
$category_arr = [];



// Get posts
$DBC = mysqli_connect(DBHOST, DBUSER, DBPW, DBNAME);
    
    if (mysqli_connect_errno()) {
        echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
        exit; //stop processing the page further
    };

$columns = "posts.post_id AS post_id, posts.user_id AS user_id, users.display_name AS author_name, posts.title AS title, posts.content AS content, posts.publish_time AS publish_time, posts.closed_for_comments AS closed_for_comments";
$source = "FROM posts RIGHT JOIN users ON posts.user_id = users.user_id";
$conditions = "WHERE NOW() >= SUBTIME(posts.publish_time, '0 13:0:0.000000')";

$result = assemble_query($DBC, $columns,$source, $conditions);

while ($row = mysqli_fetch_assoc($result)) {
    $post = new BlogPost($row['post_id'], $row['user_id'], $row['author_name'], [], $row['title'], $row['content'], strtotime($row['publish_time']), $row['closed_for_comments'], []);
    array_push($blog_arr, $post);
}

foreach ($blog_arr as $post) {
    
    $columns = "posts_categories.category_id AS category_id, categories.category_name AS category_name";
    $source = "FROM posts_categories JOIN categories ON posts_categories.category_id = categories.category_id";
    $conditions = "WHERE " . $post->post_id . " = posts_categories.post_id";

    $result = assemble_query($DBC, $columns,$source, $conditions);

    while ($row = mysqli_fetch_assoc($result)) {
        array_push($post->categories, $category_arr[$row->category_id]);
    }
    
    $comments = [];

    $columns = "comments.comment_id AS comment_id, comments.post_id AS post_id, comments.reply_to AS reply_to, comments.title AS title, comments.content AS content, comments.user_id AS user_id, users.display_name AS author_name";
    $source = "FROM comments JOIN users ON comments.user_id = users.user_id";
    $conditions = "WHERE " . $post->post_id . " = comments.post_id";

    $result = assemble_query($DBC, $columns,$source, $conditions);

    while ($row = mysqli_fetch_assoc($result)) {
        $comment = new Comment($row['id'], $row['user_id'], $row['author_name'], $row['title'], de_bb_ify($row['content']), [], $row['reply_to']);

        array_push($comments, $comment);
    }

    foreach($comments as $comment) {
        if ($comment->parent <> null) {
            foreach($comments as $parent) {
                if ($parent->id == $comment->parent) {
                    array_push($parent->children, $comment);
                }
            }
        }
    }

    $post->comments = $comments;

}

mysqli_close($DBC);

function compare($a, $b) {
    return ($a->publish_time > $b->publish_time) ? -1 : 1;
}

usort($blog_arr, "compare");

foreach($blog_arr as $post) {
    $post->display($category_arr);
}

?>