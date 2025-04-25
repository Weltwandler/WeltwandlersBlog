<?php
include "php-functionality/sessions.php";
include "header.php";
include "menu.php";
include "php-functionality/posts.php";

if (!isAuthor()) {
    http_response_code(403);
    echo '<p class="error-message">You need to be logged in and an author to add a post</p>';
    exit;
}

function addPost($post, $unpublish_time=null) {
    $DBC = mysqli_connect(DBHOST, DBUSER, DBPW, DBNAME);
    
    if (mysqli_connect_errno()) {
        echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
        exit; //stop processing the page further
    };

    
    $publish_time_stamp = date("Y-m-d H:i:s", $post->publish_time);
    if ($unpublish_time == false) {
        $query = "INSERT INTO posts (user_id, publish_time, title, content) VALUES (?,?,?,?)";
        $stmt = mysqli_prepare($DBC,$query);
        mysqli_stmt_bind_param($stmt,'isss',$post->author_user,$publish_time_stamp,$post->title,$post->content);
    } else {
        $query = "INSERT INTO posts (user_id, publish_time, unpublish_time, title, content) VALUES (?,?,?,?,?)";
        $stmt = mysqli_prepare($DBC,$query);
        $unpublish_stamp = date("Y-m-d H:i:s", $unpublish_time);
        mysqli_stmt_bind_param($stmt,'issss',$post->author_user,$publish_time_stamp,$unpublish_time,$post->title,$post->content);
    }
    mysqli_stmt_execute($stmt);
    $new_id = mysqli_insert_id($DBC);
    mysqli_close($DBC);
    return $new_id;
}

$msg = '';
$format = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Input Validation
    $err = 0;

    // User validation
    if (!isAuthor()) {
        $err++;
        $msg.= 'Not authorised ';
    }

    // Title validation
    if (isset($_POST['title']) && is_string($_POST['title'])) {
        $title = cleanStr($_POST['title']);
    } else {
        $err++;
        $msg .= 'No valid title found ';
    }
    
    
    // Content validation
    if (isset($_POST['content']) && is_string($_POST['content'])) {
        $content = bb_ify($_POST['content']);
    } else {
        $err++;
        $msg .= 'Content invalid ';
    }
    
    // Publish Date validation
    if (isset($_POST['publish_time']) && $_POST['publish_time'] != null) {
        // $publish_time = strtotime($_POST['publish_time']);
        $publish_time = new DateTime($_POST['publish_time']);
        if ($publish_time === false) {
            $err++;
            $msg .= 'Invalid publish time ';
        } else {
            $publish_time = $publish_time->setTimezone(new DateTimeZone('Pacific/Auckland'));
        }
    } else {
        $publish_time = time();
        $publish_time = DateTime::createFromFormat("d/m/Y H:i:s", date("d/m/Y H:i:s", $publish_time));
    }

    // Unpublish time validation
    if (isset($_POST['unpublish_time']) && $_POST['unpublish_time'] != null) {
        $unpublish_time = DateTime::createFromFormat(DateTimeInterface::ISO8601, $_POST['unpublish_time']);
        if ($unpublish_time === false) {
            $err++;
            $msg .= 'Invalid unpublish time ';
        }
    } else {
        $unpublish_time = null;
    }
    
    if ($err == 0) {
        $publish_time = $publish_time->setTimezone(new DateTimeZone('UTC'));
        $post = new BlogPost(-1, $_SESSION['userid'], $_SESSION['display_name'], [], $title, $content, $publish_time->getTimestamp(), false, []);
        addPost($post, $unpublish_time);
    }
    
}

?>
<main>
    <p class="error-message"><?=$msg;?></p>    
    <div id="blog-container" class="main-container">
        
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <label for="title">Title:</label><br>
            <input type="text" id="title" name="title" required>
            <br><br>
            <label for="content">Post:</label><br>
            <textarea id="content" name="content" rows="20" cols="100"></textarea>
            <br><br>
            <label for="publish_time">Publish on (leave blank to publish immediately): </label><br>
            <input type="datetime-local" id="publish_time" name="publish_time">
            <br><br>
            <label for="unpublish_time">Remove automatically on (leave blank for indefinite posts): </label>
            <br><br>
            <input type="datetime-local" id="unpublish_time" name="unpublish_time">
            <br><br>
            <input type="submit" value="Publish">
            <input type="reset" value="Cancel">
        </form>
    </div>
</main>
<?php
include "footer.php";
?>
