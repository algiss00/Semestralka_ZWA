<?php
if (!isset($_SESSION)) {
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
    <title>Main Page</title>
    <!--    <link rel="stylesheet" type="text/css" href="../styles/style.css" zde je css>-->
</head>
<body>
<?php
if (isset($_SESSION['userId'])) {
    $userId = $_SESSION['userId'];
    echo '<p>You are logged in</p>';
} else {
    echo '<p>You are logged out</p>';
}
?>
<a href="../ConnectDB/backend/Logout.php">
    <button type="button" id="logoutSubmit" name="logout-submit">Logout</button>
</a>
<div class="row post-list">

</div>
<div>
    <div>
        <label for="title">TITLE</label>
        <input type="text" id="title">
    </div>
    <div>
        <label for="description">descriptioin</label>
        <textarea id="description"></textarea>
    </div>
    <div>
        <label for="deadline">deadline</label>
        <textarea id="deadline"></textarea>
    </div>
    <div>
        <button onclick="addTask(${userId})">Add task</button>
    </div>
</div>

<script src="main.js"></script>
</body>
</html>