<?php
if(!isset($_SESSION))
{
    session_start();
}

if (isset($_POST['registr'])) {
    require '../dbConnect/dbConn.php';

    $username = $_POST['user'];
    $pass = $_POST['pass'];
    $repass = $_POST['Repass'];
    $email = $_POST['email'];

    if (empty($username) || empty($pass) || empty(repass) || empty($email)) {
        header("Location: ..\html\signUp.php?error=emptyfields&uid=" . $username . "&mail=" . $email);
        exit();
    } else if ($pass !== $repass) {
        header("Location: ..\html\signUp.php?error=passwordcheck&uid=" . $username . "&mail=" . $email);
        exit();
    } else {
        $sql = "select username from users where username=?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ..\html\signUp.php?error=sqlerror");
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "s", $username);
            //выполнить
            mysqli_stmt_execute($stmt);
            //result
            mysqli_stmt_store_result($stmt);
            $resultcheck = mysqli_stmt_num_rows($stmt);
            if ($resultcheck > 0) {
                header("Location: ..\html\signUp.php?error=usertaken&mail=" . $email);
                exit();
            } else {
                $sql = "insert into users(username, password, email) values (?, ?, ?)";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    header("Location: ..\html\signUp.php?error=sqlerror");
                    exit();
                } else {
                    $hashedPwd = password_hash($pass, PASSWORD_DEFAULT);

                    mysqli_stmt_bind_param($stmt, "sss", $username, $hashedPwd, $email);
                    //выполнить
                    mysqli_stmt_execute($stmt);
                    header("Location: ..\html\loginPage.php?signup=success");
                    exit();
                }
            }
        }
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

} else {
    header("Location: ..\html\signUp.php");
    exit();
}
