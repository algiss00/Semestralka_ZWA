<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Credentials: true');
header('Content-type: json/application');

if(!isset($_SESSION))
{
    session_start();
}
require "../dbConnect/dbConn.php";

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "POST") {
    addTask($_POST, $conn);
} else if ($method == "GET") {
    if (isset($_GET['id'])) {
        $task_id = $_GET['id'];
        echo getTask($task_id, $conn);
    } else {
        $res = getAllUsersTasks($conn);
        echo $res;
    }
} else if ($method == "PUT") {
    if (isset($_GET['id'])) {
        $task_id = $_GET['id'];
        $res = file_get_contents("php://input", true);
        $task = json_decode($res);
        updateTask($task_id, $task->title, $task->description, $task->deadline, $task->status, $conn);
    } else if (isset($_GET['updateStatus_task_id']) && isset($_GET['status'])) {
        $task_id = $_GET['updateStatus_task_id'];
        $status = $_GET['status'];
        updateStatusTask($task_id, $status, $conn);
    }
} else if ($method == "DELETE") {
    if (isset($_GET['id'])) {
        $task_id = $_GET['id'];
        deleteTask($task_id, $conn);
    }
}


