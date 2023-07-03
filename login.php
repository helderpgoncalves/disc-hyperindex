<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>DISC | Bitsapiens</title>
    <link rel="stylesheet" href="css/login.css" />
    <script src="js/script.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <!-- favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="images/favicon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon.png">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon.png">
</head>

<body id="body-pd">
    <?php
    require('db.php');
    session_start();
    // If form submitted, insert values into the database.
    if (isset($_POST['username'])) {
        // removes backslashes
        $username = stripslashes($_REQUEST['username']);
        //escapes special characters in a string
        $username = mysqli_real_escape_string($con, $username);
        $password = stripslashes($_REQUEST['password']);
        $password = mysqli_real_escape_string($con, $password);
        //Checking is user existing in the database or not 
        $query = "SELECT * FROM `users` WHERE username='$username'
and password='" . md5($password) . "'";
        $result = mysqli_query($con, $query) or die(mysql_error());
        $rows = mysqli_num_rows($result);

        // set if user is admin or not
        $row = mysqli_fetch_assoc($result);
        $_SESSION['admin'] = $row['admin'];

        if ($rows == 1) {
            $_SESSION['username'] = $username;

            // Redirect user to index.php
            header("Location: index.php");
        } else {
            // echo the error
            echo "<div class='form' style='text-align:center; padding-top: 100px;'>
<h3>Username/password is incorrect.</h3>
<br/>Click here to <a href='login.php'>Login</a></div>";
        }
    } else {
    ?>
        <div class="container">
            <form class="login-form" action="" method="post" name="login">
                <h1>Login</h1>
                <label for="username">Username</label>
                <input type="text" name="username" required>
                <label for="password">Password</label>
                <input type="password" name="password" required>
                <input type="submit" value="Login">
            </form>
        </div>
    <?php } ?>
</body>

</html>