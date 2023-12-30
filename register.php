<?php
include "php-functionality/sessions.php";
include "header.php";
include "menu.php";

function register($username, $display_name, $full_name, $email, $password) {
    $DBC = mysqli_connect(DBHOST, DBUSER, DBPW, DBNAME);
    
    if (mysqli_connect_errno()) {
        echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
        exit; //stop processing the page further
    };

    $query = "INSERT INTO USERS (username, display_name, full_name, email, password) VALUES (?,?,?,?,?);";
    $stmt = mysqli_prepare($DBC,$query);
    mysqli_stmt_bind_param($stmt,'sssss',$username,$display_name,$full_name,$email,$password);
    mysqli_stmt_execute($stmt);
    $new_id = mysqli_insert_id($DBC);
    mysqli_close($DBC);
    
    return $new_id;
}

function isUnique($username) {
    $DBC = mysqli_connect(DBHOST, DBUSER, DBPW, DBNAME);
    
    if (mysqli_connect_errno()) {
        echo "Error: Unable to connect to MySQL. ".mysqli_connect_error() ;
        exit; //stop processing the page further
    };

    $query = "SELECT user_id FROM users WHERE username=?";
    $stmt = mysqli_prepare($DBC,$query);
    mysqli_stmt_bind_param($stmt,'s',$username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $rowcount = mysqli_num_rows($result);
    mysqli_close($DBC);
    if ($rowcount === 0) {
        return true;
    } else {
        return false;
    }
}

$msg = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Input Validation
    $err = 0;

    // Username Validation
    if (isset($_POST['username']) && is_string($_POST['username'])) {
        $un = cleanStr($_POST['username']);
        if (!isUnique($un)) {
            $err++;
            $msg .= 'Username already in use  ';
        }
    } else {
        $err++;
        $msg .= 'Bad username  ';
    }

    // Password Validation
    if (isset($_POST['password']) && is_string($_POST['password'])) {
        if ($_POST['password'] == $_POST['password2']) {
            $rawPw = $_POST['password'];
            if (strlen($rawPw) >= 12 || (strlen($rawPw) >= 8 && preg_match('@[A-Z]@', $rawPw) && preg_match('@[a-z]@', $rawPw) && preg_match('@[0-9]@', $rawPw))) {
                $pw = password_hash($rawPw, PASSWORD_DEFAULT);
            } else {
                $err++;
                $msg .= 'Password complexity requirements not met  ';
            }
        } else {
            $err++;
            $msg.= 'Passwords did not match  ';
        }
    } else {
        $err++;
        $msg .= 'Bad password  ';
    }

    if (!isset($_POST['full_name'])) {
        $fn = '';
    } elseif (is_string($_POST['full_name'])) {
        $fn = cleanStr($_POST['full_name']);
    } else {
        $err++;
        $msg .= 'Bad Name';
    }

    if (!isset($_POST['display_name'])) {
        $dn = $un;
    } elseif (is_string($_POST['display_name'])) {
        $dn = cleanStr($_POST['display_name']);
    } else {
        $err++;
        $msg .= 'Bad Name';
    }
    
    if (isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
        $em = $_POST['email'];
    } else {
        $err++;
        $msg .= 'Invalid email address  ';
    }

    if ($err === 0) {
        $user_id = register($un,$dn,$fn, $em,$pw);
        if ($user_id > 0) {
            $_SESSION['userid'] = $user_id;
            $_SESSION['logged_in'] = 1;
            $_SESSION['role'] = 1;
            $_SESSION['display_name'] = $dn;
            header('Location: '.$_SERVER['PHP_SELF']);
        } else {
            $msg .= 'Sorry, something went wrong - please try again!';
        }
    }
}

?>

<main>
    <div class="main-container">
        <?php
        if (loggedIn()) {
            ?>
            <h2>Account created successfully!</h2>
            <a href="index.php">Return to main page</a>
            <p><em>Did you want to creat a different account? <a href="logout.php">Click here</a> to log out!</em><p>
            <?php
        } else {
            ?>
            <h2>Create an account</h2>
            <aside>Already have an account? <a href="login.php">Log in here</a> instead!</aside>
            <br>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <label for="username">Username:</label><br>
                <input type="text" id="username" name="username" required>
                <br><br>
                <label for="password">Password:</label><br>
                <input type="password" id="password" name="password" required>
                <br><br>
                
                <label for="password2">Password (re-type):</label><br>
                <input type="password" id="password2" name="password2" required>
                <p><em>Needs to be either <strong>12 characters or longer</strong> or <strong>8 characters long and have a minimum one each of upper-case letters, lower-case letters and numbers</strong>.</p>
                <label for="display_name">Display Name (if different from username):</label><br>
                <input type="text" id="display_name" name="display_name">
                <br><br>
                <label for="full_name">Name (optional):</label><br>
                <input type="text" id="full_name" name="full_name">
                <br><br>
                <label for="email">Email address:</label><br>
                <input type="email" id="email" name="email" required>
                <br>
                <br>
                <br>
                <input type="submit" value="Register">
                <input type="Reset">
            </form>
            <p class="error-message"><?=$msg;?></p>
            <?php
        }
        ?>
    </div>
</main>

<?php
include "footer.php";
?>