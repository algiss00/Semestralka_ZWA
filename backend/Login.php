<?php
if(!isset($_SESSION))
{
    session_start();
}

if (isset($_POST['login'])) {
    require '../dbConnect/dbConn.php';

    $username = $_POST['user'];
    $pass = $_POST['pass'];

    if (empty($username) || empty($pass)) {
        header("Location: ..\html\loginPage.php?error=emptyfields");
        exit();
    } else {
        $sql = "select * from users where username=?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ..\html\loginPage.php?error=sqlerror");
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "s", $username);
            //выполнить
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if ($row = mysqli_fetch_assoc($result)) {
                $passCheck = password_verify($pass, $row['password']);
                if (!$passCheck) {
                    header("Location: ..\html\loginPage.php?error=wrongpass");
                    exit();
                } else {
                    $_SESSION['userId'] = $row['id'];
                    $_SESSION['userUsername'] = $row['username'];

                    header("Location: ../index.php?login=success");
                    exit();
                }
            } else {
                header("Location: ..\html\loginPage.php?error=nouser");
                exit();
            }
        }
    }
} else {
    header("Location: ..\html\loginPage.php");
    exit();
}

