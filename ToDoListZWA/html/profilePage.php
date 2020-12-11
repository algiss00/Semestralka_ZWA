<?php
if (!isset($_SESSION)) {
    session_start();
}
if (isset($_SESSION['userId'])) {

} else {
    header("Location: loginPage.php");
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" type="text/css" href="../style/profileStyles.css">
    <link rel="stylesheet" href="../assets/semantic.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="../assets/semantic.min.js"></script>
    <script src="../JS/indexJS.js"></script>
    <script src="../JS/profileJs.js"></script>
    <title>Profile</title>
</head>
<body>
<div class="ui menu">
    <div class="ui item">
        <a href="profilePage.php">
            <div class="ui primary button">Profile</div>
        </a>
    </div>
    <div class="right item">
        <a href="../API/logout.php">
            <button class="ui primary button" id="logoutBut" type="button">
                logout
            </button>
        </a>
    </div>
</div>

<div class="ui equal width form" id="profileForm">
    <h1>Profile</h1>
    <div class="fields">
        <div class="field">
            <label>First name</label>
            <input type="text" id="name" placeholder="First Name">
        </div>
        <div class="field">
            <label>Surname</label>
            <input type="text" id="surname" placeholder="Surname">
        </div>
    </div>
    <div class="fields">
        <div class="field">
            <label>Username</label>
            <input type="text" placeholder="username" id="username">
        </div>
        <div class="field">
            <label>Email</label>
            <input type="email" placeholder="email" id="email">
        </div>
    </div>

    <button class="ui button" type="button" id="deleteProfile">Delete profile</button>
    <button class="ui button" type="button" id="updateBut" name="updateBut">Update</button>
</div>

<div class="ui equal width form" id="changePass">
    <h1>Change password</h1>
    <div class="fields">
        <div class="field">
            <label>Current password</label>
            <input type="password" placeholder="password" id="currPass">
        </div>
        <div class="field">
            <label>New password</label>
            <input type="password" placeholder="new password" id="newPass">
        </div>
    </div>
    <button class="ui button" type="button" id="newPassBut">Change password</button>
</div>
<a href="index.php" id="back2">
    <button class="ui button" type="button">Back</button>
</a>

<script>
    initProfile();
</script>
</body>
</html>