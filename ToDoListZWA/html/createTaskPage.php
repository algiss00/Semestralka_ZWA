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
    <link rel="stylesheet" type="text/css" href="../style/createTask.css">
    <link rel="stylesheet" href="../assets/semantic.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="../assets/semantic.min.js"></script>
    <script src="../JS/indexJS.js"></script>
    <script src="../JS/createTask.js"></script>
    <title>Create task</title>
</head>
<body>
<div class="ui menu">
    <div class="ui item">
        <a href="profilePage.php">
            <div class="ui primary button">Profile</div>
        </a>
    </div>
    <div class="right item">
        <button class="ui primary button" id="logoutBut" type="button">
            logout
        </button>
    </div>
</div>

<form class="ui form equal width form" id="createTask" name="formTask" method="post">
    <h1>Create task</h1>
    <div class="fields">
        <div class="field">
            <label>Title</label>
            <input type="text" name="title" placeholder="title" id="title">
        </div>
        <div class="field">
            <label>Deadline</label>
            <input type="date" name="deadline" placeholder="deadline" id="deadline">
        </div>
        <div class="field">
            <label for="status">Status</label>
            <select name="status" id="status">
                <option value="Active">Active</option>
                <option value="Done">Done</option>
                <option value="Cancel">Cancel</option>
            </select>
        </div>
        <div class="field">
            <label>Category</label>
            <input type="text" name="category" placeholder="category" id="category"
                   value='<?php echo htmlspecialchars($_GET["category"], ENT_QUOTES) ?>'>
        </div>
    </div>
    <div class="ui form">
        <div class="field">
            <label>Description</label>
            <textarea rows="5" name="description" id="description"></textarea>
        </div>
    </div>
    <a href="index.php" id="back">
        <button class="ui button" type="button">Back</button>
    </a>
    <button class="ui button" type="button" id="createTaskSub">Create</button>
</form>
<script>
    initCreateTask();
</script>
</body>
</html>