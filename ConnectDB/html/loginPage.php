<?php
if(!isset($_SESSION))
{
    session_start();
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login Page</title>
    <link rel="stylesheet" type="text/css" href="../styles/style.css">
</head>
<body>

<div id="frame">
    <form action="../backend/Login.php" method="post">
        <?php
        if (isset($_GET['error'])) {
            if ($_GET['error'] == "emptyfields") {
                echo '<p class="signuperror" > Fill in all fields!</p>';
            } else if ($_GET['error'] == "wrongpass") {
                echo '<p class="signuperror" > Wrong password or username!</p>';
            }
        }
        ?>
        <p>
            <label> Username: </label>
            <input type="text" id="username" name="user"/>
        </p>
        <p>
            <label> Password: </label>
            <input type="password" id="password" name="pass"/>
        </p>
        <p>
            <input type="submit" id="submit" name="login" value="   Login   "/>
        </p>
    </form>
    <!--    <a href="signUp.php">Signup</a>-->
    <a href="signUp.php">
        <button type="button" id="btn" name="signUp">
            Sign up
        </button>
    </a>
</div>

</body>
</html>