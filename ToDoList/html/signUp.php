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
    <title>SignIn Page</title>
    <link rel="stylesheet" type="text/css" href="../styles/style.css">
</head>
<body>
<main>
    <div id="frame">
        <h1>Sign Up</h1>
        <?php
        if (isset($_GET['error'])) {
            if ($_GET['error'] == "emptyfields") {
                echo '<p class="signuperror" > Fill in all fields!</p>';
            } else if ($_GET['error'] == "passwordcheck") {
                echo '<p class="signuperror" > Repeat password is not equal to password!</p>';
            } else if ($_GET['error'] == "usertaken") {
                echo '<p class="signuperror" > username is taken!</p>';
            }
        }
        //        else if (isset($_GET['signup'])) {
        //            if($_GET["signup"] == "success"){
        //                echo '<p class="signuperror" > Sign up successful!</p>';
        //            }
        //        }
        ?>
        <form action="../backend/Registr.php" method="post">
            <p>
                <label> Username: </label>
                <input type="text" id="username" name="user"/>
            </p>

            <p>
                <label> Email: </label>
                <input type="email" id="email" name="email"/>
            </p>

            <p>
                <label> Password: </label>
                <input type="password" id="password" name="pass"/>
            </p>

            <p>
                <label> Repeat password: </label>
                <input type="password" id="Repassword" name="Repass"/>
            </p>

            <p>
                <input type="submit" id="signUp" name="registr" value="Registration"/>
            </p>
        </form>
        <a href="../html/loginPage.php">
            <button type="button" id="backSub" name="backSub">
                Back
            </button>
        </a>
    </div>
</main>
</body>
</html>
