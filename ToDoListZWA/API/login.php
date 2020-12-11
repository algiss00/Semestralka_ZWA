<?php
if (!isset($_SESSION)) {
    session_start();
}

$method = $_SERVER['REQUEST_METHOD'];
require "../methodsForApi/dbConnectMethods.php";

if ($method == "POST") {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $sql = "select * from users where username=?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            error("sql error", 500);
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "s", $username);
            //выполнить
            if (!mysqli_stmt_execute($stmt)) {
                error("sql error", 500);
                mysqli_error($conn);
                exit();
            }
            $result = mysqli_stmt_get_result($stmt);
            if ($row = mysqli_fetch_assoc($result)) {
                $passCheck = password_verify($password, $row['password']);
                if (!$passCheck) {
                    error("wrong password", 400);
                    exit();
                } else {
                    $_SESSION['userId'] = $row['id'];
                    $_SESSION['userUsername'] = $row['username'];
                    success("Login success", 200);
                }
            } else {
                error("No user", 400);
                exit();
            }
        }
    }
}
