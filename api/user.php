<?php
if(!isset($_SESSION))
{
    session_start();
}
require "../dbConnect/dbConn.php";

$method = $_SERVER['REQUEST_METHOD'];

if ($method == "GET") {
    echo getCurrentUser($conn);
} else if ($method == "PUT") {
    $res = file_get_contents("php://input", true);
    $user = json_decode($res);
    updateUser($user->username, $user->password, $user->email, $conn);
}