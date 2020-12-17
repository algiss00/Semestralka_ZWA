<?php
if (!isset($_SESSION)) {
    session_start();
}

$method = $_SERVER['REQUEST_METHOD'];
require "../methodsForApi/dbConnectMethods.php";

if ($method == "GET") {
    echo getCurrentUser();
} //else if ($method == "PUT") {
//    $res = file_get_contents("php://input", true);
//    $user = json_decode($res, true);
//    if (isset($_GET['changePassword'])) {
//        /**
//         * zmena hesla usera
//         */
//        updateUserPass($user["currentPassword"], $user["newPassword"]);
//    } else {
//        updateUser($user["username"], $user["email"], $user["name"], $user["surname"]);
//    }
//}
else if ($method == "POST") {
    if (isset($_GET['addUser'])) {
        $res = file_get_contents("php://input", true);
        $user = json_decode($res, true);
        addUser($user["username"], $user["password"], $user["name"], $user["surname"], $user["email"]);
    } else if (isset($_GET['updateUser'])) {
        $res = file_get_contents("php://input", true);
        $user = json_decode($res, true);
        if (isset($_GET['changePassword'])) {
            /**
             * zmena hesla usera
             */
            updateUserPass($user["currentPassword"], $user["newPassword"]);
        } else {
            updateUser($user["username"], $user["email"], $user["name"], $user["surname"]);
        }
    } else if (isset($_GET['deleteUser'])) {
        deleteUser();
    }

} //else if ($method == "DELETE") {
//    deleteUser();
//}
