//!! ITS JUST A TEMPLATE

<?php
if(!isset($_SESSION))
{
    session_start();
}
$user = 'root';
$password = '';
$dbname = 'testdb';

$db = new mysqli('localhost', $user, $password, $dbname) or die("Unable to connect");
//$sql = "insert into users values(8, '1', '1', 'asd@sad.ry')";
//$db->query($sql);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
<form action="" method="POST">
    <input type="text" placeholder="Username" name="username">
    <input type="password" placeholder="Password" name="password">
    <input type="submit" name="submit">
</form>

<?php
if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $pass = $_POST['password'];

    $users = $db->query("select * from users where username ='$username' and password = '$pass'") or die("Failed to query db " . mysqli_error());
    $row = mysqli_fetch_array($users);
    if ($row['username'] == $username && $row['password'] == $password) {
        echo "Login success! Welcome " . $row['username'];
    } else {
        echo "Fail login";
    }
//    if ($users->num_rows > 0) {
//        $arr = [];
//        while ($user = $users->fetch_assoc()) {
//            array_push($arr, $user);
//        }
//        echo json_encode($arr);
//    } else {
//        echo 'no result';
//    }
}
?>

</body>
</html>