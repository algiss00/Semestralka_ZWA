<?php
if (!isset($_SESSION)) {
    session_start();
}
require "../methodsForApi/dbConnectMethods.php";

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "POST") {
    if (isset($_GET['task_id']) && isset($_GET['category_id'])) {
        $task_id = $_GET['task_id'];
        $category_id = $_GET['category_id'];
        addToRelationTaskCateg($task_id, $category_id, $conn);
    } else {
        $res = file_get_contents("php://input", true);
        $category = json_decode($res);
        addCategory($category->title, $category->position_list, $conn);
    }
} else if ($method == "GET") {
    if (isset($_GET['task']) && isset($_GET['category_id'])) {
        if ($_GET['task'] == "all") {
            $category_id = $_GET['category_id'];
            echo getAllTaskFromCategory($category_id, $conn);
        }
    } else if (isset($_GET['id'])) {
        $category_id = $_GET['id'];
        echo getCategory($category_id, $conn);
    } else {
        $res = getAllCategoriesOfUser($conn);
        echo $res;
    }
} else if ($method == "PUT") {
    if (isset($_GET['id'])) {
        $category_id = $_GET['id'];
        $res = file_get_contents("php://input", true);
        $category = json_decode($res);
        updateCategory($category_id, $category->title, $category->position_list, $conn);
    }
} else if ($method == "DELETE") {
    if (isset($_GET['id'])) {
        $category_id = $_GET['id'];
        deleteCategory($category_id, $conn);
    } else if (isset($_GET['category_id']) && isset($_GET['task_id'])) {
        $category_id = $_GET['category_id'];
        $task_id = $_GET['task_id'];
        deleteTaskFromCategory($category_id, $task_id, $conn);
    }
}





