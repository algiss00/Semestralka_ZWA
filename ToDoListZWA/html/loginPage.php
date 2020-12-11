<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="../style/stylesLogin.css">
    <script src="../JS/login.js"></script>
    <title>Login</title>
</head>
<body>
<div class="login">
    <form method="POST" id="login_form">
        <h1 id="header">Login</h1>
        <p>
            <label><h2>Username:</h2>
                <input type="text" name="username" id="username" autocomplete="off" autofocus>
            </label>
        </p>

        <p>
            <label for="password">
                <h2>Password:</h2>
            </label>
            <input type="password" name="password" id="password">
        </p>

        <input type="button" value="Login" name="loginSub" id="loginBut">
        <a href="signUpPage.php">
            <input type="button" value="Sign up" name="SignUpSub" id="signUpBut">
        </a>
    </form>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
    initLogin();
</script>
</body>
</html>