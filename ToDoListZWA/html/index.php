<?php
session_start();

if (isset($_SESSION['userUsername'])) {

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
    <link rel="stylesheet" type="text/css" href="../style/stylesIndex.css">
    <link rel="stylesheet" href="../assets/semantic.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="../JS/indexJS.js"></script>
    <script src="../assets/semantic.min.js"></script>
    <title>My tasks</title>
</head>
<body>
<div class="ui menu">
    <div class="ui item">
        <a href="profilePage.php">
            <div class="ui primary button">Profile</div>
        </a>
    </div>
    <div class="right item">
        <div class="ui primary button" id="logoutButton">
            logout
        </div>
    </div>
</div>


<div class="ui grid padded stackable">
    <div class="sixteen wide column">
        <h1>All tasks</h1>
        <div class="ui primary button" id="newCat">new category</div>
    </div>
    <div class="four wide column">
        <div class="ui vertical menu" id="verticalMenu">
        </div>
    </div>
    <div class="twelve wide column equal width ">
        <div class="ui cards" id="cats">

        </div>
    </div>
    <div class="ui buttons" id="showMore">
        <button class="ui disabled button" id="left">&lt;</button>
        <button class="ui button" id="right">&gt;</button>
    </div>
</div>

<div class="ui modal" id="modalNewCategory">
    <div class="header">
        Create category
    </div>
    <div class="content">
        <div class="description">
            <input type="text" id="modalCategory">
        </div>
    </div>
    <div class="actions">
        <div class="ui black deny button">
            Cancel
        </div>
        <div class="ui positive right labeled icon button" id="createCatBut">
            Create
            <i class="checkmark icon"></i>
        </div>
    </div>
</div>

<div class="ui modal" id="modalCategoryUpdate">
    <div class="header ui form">
        Update category
    </div>
    <div class="content ui form">
        <div class="description">
            <input type="text" id="categTitle" placeholder="Category title">
            <input type="text" id="categPosition" placeholder="Category position">
        </div>
    </div>
    <div class="actions">
        <div class="ui black deny button">
            Cancel
        </div>
        <div class="ui positive right labeled icon button" id="updateCategBut">
            Update
            <i class="refresh icon"></i>
        </div>
    </div>
</div>


<div class="ui modal" id="modalTask">
    <div class="header ui form">
        <input type="text" id="taskTitle" placeholder="Task title">
    </div>
    <div class="content ui form">
        <div class="description">
            <label for="descriptionModal">Description</label>
            <textarea rows="5" name="description" id="descriptionModal"></textarea>
            <label for="deadlineModal">Deadline YYYY-MM-DD</label>
            <input type="date" name="deadline" id="deadlineModal">
            <label for="statusTaskModal">Status</label>
            <select name="status" id="statusTaskModal">
                <option value="Active">Active</option>
                <option value="Done">Done</option>
                <option value="Cancel">Cancel</option>
            </select>
        </div>
    </div>
    <div class="actions">
        <div class="ui black deny button">
            Cancel
        </div>
        <div class="ui negative right labeled icon button" id="deleteTaskModal">
            Delete Task
            <i class="x icon"></i>
        </div>
        <div class="ui positive right labeled icon button" id="updateTask">
            Update
            <i class="checkmark icon"></i>
        </div>
    </div>
</div>
<script>
    init();
</script>
</body>
</html>