<?php
if (!isset($_SESSION)) {
    session_start();
}
require "../methodsForApi/dbConnectMethods.php";

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "GET") {
    echo getCurrentUser($conn);
} else if ($method == "PUT") {
    $res = file_get_contents("php://input", true);
    $user = json_decode($res);
    if (isset($_GET['changePassword'])) {
        updateUserPass($user->currentPassword, $user->newPassword, $conn);
    } else {
        updateUser($user->username, $user->email, $user->name, $user->surname, $conn);
    }
} else if ($method == "POST") {
    addUser($_POST, $conn);
}