<?php
include "php-functionality/sessions.php";
include "header.php";
include "menu.php";

function login($username, $password) {
    $DBC = mysqli_connect(DBHOST, DBUSER, DBPW, DBNAME);
    
    if (mysqli_connect_errno()) {
        echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
        exit; //stop processing the page further
    };

    $query = "SELECT user_id, display_name, password, role_id, theme_id FROM users WHERE username = ?;";
    $stmt = mysqli_prepare($DBC,$query);
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    mysqli_close($DBC);

    if (isset($row['password'])) {
        if (password_verify($password, $row['password'])) {
            $user = new User($row['role_id'], $row['user_id'], $row['username'], $row['display_name']);
        return $user;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Input Validation
    $err = 0;

    if (isset($_POST['username']) && is_string($_POST['username'])) {
        $un = cleanStr($_POST['username']);
    } else {
        $err++;
        $msg .= "Bad username ";
    }

    if (isset($_POST['password']) && is_string($_POST['password'])) {
        $pw = $_POST['password'];
        if (strlen($pw) < 8 || (strlen($pw) < 12 && (!preg_match('@[A-Z]@', $pw) || !preg_match('@[a-z]@', $pw) || !preg_match('@[0-9]@', $pw)))) {
            $err++;
            $msg .= 'Password complexity requirements not met  ';
        }
    } else {
        $err++;
        $msg .= "Bad password ";
    }

    if ($err === 0) {
        $user = login($un, $pw);
        if ($user) {
            $_SESSION['logged_in'] = 1;
            $_SESSION['role'] = $user->role;
            $_SESSION['userid'] = $user->userid;
            $_SESSION['display_name'] = $user->display_name;
            header('Location: '.$_SERVER['PHP_SELF']);
        } else {
            $msg .= "Invalid login information specified - please try again";
        }
    } 
}
?>

<main>
    <div class="main-container">
        <?php
        if (loggedIn()) {
            ?>
            <h2>Login successful!</h2>
            <a href="index.php">Return to main page</a>
            <?php
        } else {
            ?>
            <h2>Login</h2>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <label for="username">Username: </label><br>
                <input type="text" id="username" name="username">
                <br><br>
                <label for="password">Password: </label><br>
                <input type="password" id="password" name="password">
                <p><em>Password needs to be either <strong>12 characters or longer</strong> or <strong>8 characters long and have a minimum one each of upper-case letters, lower-case letters and numbers</strong>.</p>
                <br>
                <input type="submit" value="Log In!">
                <input type="reset">
            </form>
            <p class="error-message"><?=$msg;?></p>
            <p>No account yet? <s>Sign up <a href="">here</a> instead!</s>
            <p><em>Account creation is not enabled yet - please keep an eye on this</em></p>
            <?php
        }
        ?>
    </div>
</main>

<?php  
include "footer.php";
?>