<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Credentials: true');
header('Content-type: json/application');

if (!isset($_SESSION)) {
    session_start();
}

$method = $_SERVER['REQUEST_METHOD'];
require "../methodsForApi/dbConnectMethods.php";

if ($method == "POST") {
    $res = file_get_contents("php://input", true);
    $task = json_decode($res, true);
    addTask($task['title'], $task['description'], $task['deadline'], $task['status'], $task['category']);
} else if ($method == "GET") {
    if (isset($_GET['id'])) {
        $task_id = $_GET['id'];
        echo getTask($task_id);
    } else {
        $res = getAllUsersTasks();
        echo $res;
    }
} else if ($method == "PUT") {
    if (isset($_GET['id'])) {
        $task_id = $_GET['id'];
        $res = file_get_contents("php://input", true);
        $task = json_decode($res);
        updateTask($task_id, $task->title, $task->description, $task->deadline, $task->status);
    }
} else if ($method == "DELETE") {
    if (isset($_GET['id'])) {
        $task_id = $_GET['id'];
        deleteTask($task_id);
    }
}


