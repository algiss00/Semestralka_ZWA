<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="../JS/signUp.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <link rel="stylesheet" type="text/css" href="../style/stylesSignUp.css">
    <title>SignUp page</title>
</head>
<body>
<div class="Signup" id="div">
    <form method="POST" id="Signup_form">
        <h1 id="header">SignUp</h1>
        <p>
            <label><h2 id="nameh2">Name:</h2>
                <input type="text" name="name" id="name" autocomplete="off" autofocus>
            </label>
        </p>

        <p>
            <label for="surname">
                <h2 id="surnameh2">Surname:</h2>
            </label>
            <input type="text" name="surname" id="surname">
        </p>
        <p>
            <label><h2>Username:</h2>
                <input type="text" name="username" id='username' autocomplete="off" autofocus>
            </label>
        </p>
        <p>
            <label for="email">
                <h2 id="emailh2">Email:</h2>
            </label>
            <input type="email" name="email" id="email">
        </p>
        <p>
            <label for="password">
                <h2>Password:</h2>
            </label>
            <input type="password" name="password" id="password">
        </p>

        <a href="loginPage.php">
            <input type="button" value="Back" name="backBut" id="BackSub" class="button">
        </a>
        <input type="button" value="Sign up" name="signupSub" id="signupSub" class="button">
    </form>

    <script>
        init();
    </script>
</div>
</body>
</html>