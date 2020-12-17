<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Credentials: true');
header('Content-type: json/application');

if (!isset($_SESSION)) {
    session_start();
}

$user = 'skriaalg';
$password = 'webove aplikace';
$dbname = 'skriaalg';

//$user = 'root';
//$password = '';
//$dbname = 'todolistdb';

$conn = mysqli_connect('localhost', $user, $password, $dbname)
or die("Unable to connect" . mysqli_connect_error());

function getTask($taskId)
{
    global $conn;
    if (!existTask($taskId)) {
        error("Task not found", 200);
        exit();
    }
    if (isOwnerOfTask($taskId) == false) {
        error("Forbidden", 403);
        exit();
    } else {
        $sql = "select * from task where Id=?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            error("sql error", 500);
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "i", $taskId);
            //выполнить
            if (!mysqli_stmt_execute($stmt)) {
                error("sql error", 500);
                mysqli_error($conn);
                exit();
            }
            //result
            $result = mysqli_stmt_get_result($stmt);
            $res = mysqli_fetch_assoc($result);
            return json_encode($res);
        }
    }
}

function getGroupCategory()
{
    global $conn;
    if (isLogin() === null) {
        exit();
    }
    $user_id = isLogin();
    $sql = "select title, id, count(task_id)
            from category as cat
            left join relation_task_categ rtc
                   on cat.id = rtc.category_id
            where cat.creator_id =?
            GROUP BY title, id
            Order by count(task_id) DESC";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        error("sql error", 500);
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        //выполнить
        if (!mysqli_stmt_execute($stmt)) {
            error("sql error", 500);
            mysqli_error($conn);
            exit();
        }
        $result = mysqli_stmt_get_result($stmt);
        $res = mysqli_fetch_all($result);
        return json_encode($res);
    }
}

function getCategory($categoryId)
{
    global $conn;
    if (!existCategory($categoryId)) {
        error("Category not found", 200);
        exit();
    }
    if (isOwnerOfCategory($categoryId) == false) {
        error("Forbidden", 403);
        exit();
    } else {
        $sql = "select * from category where id=?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            error("sql error", 500);
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "i", $categoryId);
            //выполнить
            if (!mysqli_stmt_execute($stmt)) {
                error("sql error", 500);
                mysqli_error($conn);
                exit();
            }
            //result
            $result = mysqli_stmt_get_result($stmt);
            $res = mysqli_fetch_assoc($result);
            return json_encode($res);
        }
    }
}


function getCurrentUser()
{
    global $conn;
    if (isLogin() === null) {
        exit();
    }
    $user_id = isLogin();
    $sql = "select id, username, email, name, surname  from users where id=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        error("sql error", 500);
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        //выполнить
        if (!mysqli_stmt_execute($stmt)) {
            error("sql error", 500);
            mysqli_error($conn);
            exit();
        }
        //result
        $result = mysqli_stmt_get_result($stmt);
        $res = mysqli_fetch_assoc($result);
        return json_encode($res);
    }
}

function getAllCategoriesOfUser()
{
    global $conn;
    if (isLogin() === null) {
        exit();
    }
    $user_id = isLogin();
    $categories = $conn->query("select * from category
        where creator_id = '$user_id' order by position_list ASC")
    or die(mysqli_error($conn));
    if ($categories->num_rows > 0) {
        $arr = [];
        while ($categ = $categories->fetch_assoc()) {
            array_push($arr, $categ);
        }
        return json_encode($arr);
    } else {
        error("No category", 200);
    }
}

function getAllUsersTasks()
{
    global $conn;
    if (isLogin() === null) {
        exit();
    }
    $user_id = isLogin();
    $tasks = $conn->query("select * from task where creator_id='$user_id' order by deadline ASC ")
    or die(mysqli_error($conn));
    if ($tasks->num_rows > 0) {
        $arr = [];
        while ($tasks2 = $tasks->fetch_assoc()) {
            array_push($arr, $tasks2);
        }
        return json_encode($arr);
    } else {
        error("No tasks", 200);
    }
}

function getAllTaskFromCategory($categId)
{
    global $conn;
    if (!existCategory($categId)) {
        error("Category not found", 200);
        exit();
    }
    if (isOwnerOfCategory($categId) == false) {
        error("Forbidden", 403);
        exit();
    } else {
        $sql = "SELECT relation_task_categ.task_id, relation_task_categ.category_id, task.*
            FROM relation_task_categ, task
            WHERE (relation_task_categ.category_id = ?) AND (task.Id = relation_task_categ.task_id)";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            error("sql error", 500);
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "i", $categId);
            //выполнить
            if (!mysqli_stmt_execute($stmt)) {
                error("sql error", 500);
                mysqli_error($conn);
                exit();
            }
            $result = mysqli_stmt_get_result($stmt);
            if ($result->num_rows > 0) {
                $arr = [];
                while ($res = mysqli_fetch_assoc($result)) {
                    array_push($arr, $res);
                }
                success($arr, 200);
            } else {
                error("No tasks in category", 200);
            }
        }
    }
}

function getIdOfCategory($title, $creatorId)
{
    global $conn;
    $sql = "select id from category where title=? and creator_id=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        error("sql error", 500);
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, "si", $title, $creatorId);
        //выполнить
        if (!mysqli_stmt_execute($stmt)) {
            error("sql error", 500);
            mysqli_error($conn);
            exit();
        }
        //result
        $result = mysqli_stmt_get_result($stmt);
        if ($result->num_rows > 0) {
            $res = mysqli_fetch_assoc($result);
            return json_encode($res);
        } else {
            return null;
        }
    }
}

function addUser($username, $password, $name, $surname, $email)
{
    global $conn;
    if (isUserExist($username, $email) == true) {
        error("username or email is taken", 409);
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        error("email is not valid", 400);
        exit();
    }
    $sql = "insert into users(username, password, email, name, surname) values (?, ?, ?, ?, ?)";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        error("sql error 1", 500);
        exit();
    } else {
        $hashedPwd = password_hash($password, PASSWORD_DEFAULT);
        mysqli_stmt_bind_param($stmt, "sssss", $username, $hashedPwd, $email, $name, $surname);
        if (!mysqli_stmt_execute($stmt)) {
            error("sql error 2", 500);
            mysqli_error($conn);
            exit();
        }
        $last_id = $conn->insert_id;
        createDefaultCategory($last_id);
        postSuccess("Create user success", $last_id);
    }
}

function validateDate($date, $format = 'Y-m-d')
{
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}

function addTask($title, $description, $deadline, $status, $category)
{
    global $conn;
    //validate deadline, knihovny
    if (isLogin() === null) {
        exit();
    }
    if (!validateDate($deadline)) {
        error("Deadline is not valid, it must be in format YYYY-MM-DD", 400);
        exit();
    }
    $user_id = isLogin();
    if (existCategoryTitle($category, $user_id) === false) {
        error("Category dont exists", 200);
        exit();
    }
    $sql = "insert into task(title, description, deadline, creator_id, status) values (?, ?, ?, ?, ?)";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        error("sql error", 500);
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, "sssis", $title, $description, $deadline, $user_id, $status);
        //выполнить
        if (!mysqli_stmt_execute($stmt)) {
            error("sql error", 500);
            mysqli_error($conn);
            exit();
        }
        $last_id = $conn->insert_id;
        //addTaskToCategory($last_id, $cId->{"id"});
        postSuccess("create task success", $last_id);
    }
}

function addTaskToCategory($task_id, $category)
{
    global $conn;
    if (isLogin() === null) {
        exit();
    }
    $user_id = isLogin();
    $catId = getIdOfCategory($category, $user_id);
    $category_id = json_decode($catId);
    if ($catId === null) {
        error("Not exist category", 200);
        exit();
    }
    if (!(existTask($task_id)) || !(existCategory($category_id->{"id"}))) {
        error("Task or Category not found", 200);
        exit();
    }
    if (isOwnerOfCategory($category_id->{"id"}) == false || isOwnerOfTask($task_id) == false) {
        error("Forbidden", 403);
        exit();
    } else if (isOwnerOfCategory($category_id->{"id"}) == true && isOwnerOfTask($task_id) == true) {
        if (existSameRelation($task_id) === true) {
            error("Same relation exist", 409);
            exit();
        }
        $sql = "insert into relation_task_categ(task_id, category_id) values (?, ?)";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            error("sql error", 500);
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "ii", $task_id, $category_id->{"id"});
            //выполнить
            if (!mysqli_stmt_execute($stmt)) {
                error("sql error", 500);
                mysqli_error($conn);
                exit();
            }
            success("Add task to category success", 200);
        }
    }
}

function addCategory($title, $position)
{
    global $conn;
    if (isLogin() === null) {
        exit();
    }
    if (!is_int($position)) {
        error("position isnt Integer", 400);
        exit();
    }
    $user_id = isLogin();
    if (existCategoryTitle($title, $user_id)) {
        error("Category exist", 409);
        exit();
    } else {
        $sql = "insert into category(title, position_list, creator_id) values (?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            error("sql error", 500);
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "sii", $title, $position, $user_id);
            //выполнить
            if (!mysqli_stmt_execute($stmt)) {
                error("sql error", 500);
                mysqli_error($conn);
                exit();
            }
            $last_id = $conn->insert_id;
            postSuccess("Create category success", $last_id);
        }
    }
}

function updateUser($username, $email, $name, $surname)
{
    global $conn;
    if (isLogin() === null) {
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        error("email is not valid", 400);
        exit();
    }
    $user_id = isLogin();
    $userEmail = getCurrentUser();
    $arr = (array)json_decode($userEmail, true);

    if ($arr["email"] !== $email) {
        if (emailExist($email) == true) {
            error("email is taken", 409);
            exit();
        }
    }
    if ($arr["username"] !== $username) {
        if (usernameExist($username) == true) {
            error("username is taken", 409);
            exit();
        }
    }

    $sql = "UPDATE users
            SET username = ?, email=?, name=?, surname=?
            WHERE id = ?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        error("sql error", 500);
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, "ssssi", $username, $email, $name, $surname, $user_id);
        //выполнить
        if (!mysqli_stmt_execute($stmt)) {
            error("sql error", 500);
            mysqli_error($conn);
            exit();
        }
        success("Update current user success", 200);
    }
}

function updateUserPass($currnetPass, $newPass)
{
    global $conn;
    if (isLogin() === null) {
        exit();
    }
    $user_id = isLogin();
    if (!checkPass($currnetPass, $user_id)) {
        exit();
    }
    $sql = "UPDATE users
            SET password = ?
            WHERE id = ?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        error("sql error", 500);
        exit();
    } else {
        $hashedPwd = password_hash($newPass, PASSWORD_DEFAULT);
        mysqli_stmt_bind_param($stmt, "si", $hashedPwd, $user_id);
        //выполнить
        if (!mysqli_stmt_execute($stmt)) {
            error("sql error", 500);
            mysqli_error($conn);
            exit();
        }
        success("Change password success", 200);
    }
}


function updateTask($taskId, $title, $description, $deadline, $status)
{
    global $conn;
    if (!existTask($taskId)) {
        error("Task not found", 200);
        exit();
    }
    if (!validateDate($deadline)) {
        error("Deadline is not valid, it must be in format YYYY-MM-DD", 400);
        exit();
    }
    if (isOwnerOfTask($taskId) == false) {
        error("Forbidden", 403);
        exit();
    } else if (isOwnerOfTask($taskId) == true) {
        $sql = "UPDATE task
            SET title = ?, description= ?, deadline=?, status=?
            WHERE Id = ?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            error("sql error", 500);
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "ssssi", $title, $description, $deadline, $status, $taskId);
            //выполнить
            if (!mysqli_stmt_execute($stmt)) {
                error("sql error", 500);
                mysqli_error($conn);
                exit();
            }
            success("Update task success", 200);
        }
    }
}

function updateCategory($category_id, $title, $position_list)
{
    global $conn;
    if (!is_int($position_list)) {
        error("position isnt Integer", 400);
        exit();
    }
    if (isOwnerOfCategory($category_id) == false) {
        error("Forbidden", 403);
        exit();
    } else if (isOwnerOfCategory($category_id) == true) {
        $sql = "UPDATE category
            SET title = ?, position_list= ?
            WHERE id = ?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            error("sql error", 500);
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "sii", $title, $position_list, $category_id);
            //выполнить
            if (!mysqli_stmt_execute($stmt)) {
                error("sql error", 500);
                mysqli_error($conn);
                exit();
            }
            success("Update category success", 200);
        }
    }
}

function deleteCategory($category_id)
{
    global $conn;
    if (!existCategory($category_id)) {
        error("Category not found", 200);
        exit();
    }
    if (isOwnerOfCategory($category_id) == false) {
        error("Forbidden", 403);
        exit();
    } else {
        deleteAllTasksFromCategory($category_id);
        $sql = "DELETE FROM category WHERE id=?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            error("sql error", 500);
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "i", $category_id);
            //выполнить
            if (!mysqli_stmt_execute($stmt)) {
                error("sql error", 500);
                mysqli_error($conn);
                exit();
            }
            success("Delete success", 200);
        }
    }
}

function deleteTaskFromCategory($category_id, $task_id)
{
    global $conn;
    if (!(existTask($task_id)) || !(existCategory($category_id))) {
        error("Task or Category not found", 200);
        exit();
    }
    if (isOwnerOfTask($task_id) == false) {
        error("Forbidden", 403);
        exit();
    } else if (isOwnerOfTask($task_id) == true) {
        $sql = "DELETE FROM relation_task_categ WHERE category_id=? AND task_id=?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            error("sql error", 500);
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "ii", $category_id, $task_id);
            //выполнить
            if (!mysqli_stmt_execute($stmt)) {
                error("sql error", 500);
                mysqli_error($conn);
                exit();
            }
            success("Delete success", 200);
        }
    }
}

function deleteAllTasksFromCategory($categoryId)
{
    global $conn;
    $sql = "delete t1 from task as t1 left join relation_task_categ as r1 on r1.task_id = t1.Id where r1.category_id=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        error("sql error", 500);
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, "i", $categoryId);
        //выполнить
        if (!mysqli_stmt_execute($stmt)) {
            error("sql error", 500);
            mysqli_error($conn);
            exit();
        }
        success("Delete all tasks of category success", 200);
    }

}

function deleteTask($taskId)
{
    global $conn;
    if (!existTask($taskId)) {
        error("Task not found", 200);
        exit();
    }
    if (isOwnerOfTask($taskId) == false) {
        error("Forbidden", 403);
        exit();
    } else {
        $sql = "DELETE FROM task WHERE Id=?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            error("sql error", 500);
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "i", $taskId);
            //выполнить
            if (!mysqli_stmt_execute($stmt)) {
                error("sql error", 500);
                mysqli_error($conn);
                exit();
            }
            success("Delete success", 200);
        }
    }
}

function deleteUser()
{
    global $conn;
    if (isLogin() === null) {
        exit();
    }
    $user_id = isLogin();
    $sql = "DELETE FROM users WHERE id=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        error("sql error", 500);
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        //выполнить
        if (!mysqli_stmt_execute($stmt)) {
            error("sql error", 500);
            mysqli_error($conn);
            exit();
        }
        success("Delete success", 200);
        session_unset();
        session_destroy();
    }
}

function existTask($taskId)
{
    global $conn;
    $sql = "select * from task where Id=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        error("sql error", 500);
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, "i", $taskId);
        //выполнить
        if (!mysqli_stmt_execute($stmt)) {
            error("sql error", 500);
            mysqli_error($conn);
            exit();
        }
        //result
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) === 0) {
            return false;
        }
        return true;
    }
}

function existCategory($catId)
{
    global $conn;
    $sql = "select * from category where id=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        error("sql error", 500);
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, "i", $catId);
        //выполнить
        if (!mysqli_stmt_execute($stmt)) {
            error("sql error", 500);
            mysqli_error($conn);
            exit();
        }
        //result
        $result = mysqli_stmt_get_result($stmt);
        if (mysqli_num_rows($result) === 0) {
            return false;
        }
        return true;
    }
}

function existCategoryTitle($title, $creatorId)
{
    global $conn;
    $sql = "select title from category where title=? and creator_id=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        error("sql error", 500);
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, "si", $title, $creatorId);
        if (!mysqli_stmt_execute($stmt)) {
            error("sql error", 500);
            mysqli_error($conn);
            exit();
        }
        mysqli_stmt_store_result($stmt);
        $resultcheck = mysqli_stmt_num_rows($stmt);
        if ($resultcheck > 0) {
            return true;
        }
        return false;
    }
}

/**
 * pro nove uzivatele vzdy se pridaji 3 category
 */
function createDefaultCategory($userId)
{
    global $conn;
    $sql1 = "insert into category(title, position_list, creator_id) values ('Today', 1, $userId)";
    $sql2 = "insert into category(title, position_list, creator_id) values ('Tomorrow', 2, $userId)";
    $sql3 = "insert into category(title, position_list, creator_id) values ('SomeDay', 3, $userId)";

    if ($conn->query($sql1) === False) {
        error("Error insert Default category Today", 500);
        exit();
    } else if ($conn->query($sql2) === False) {
        error("Error insert Default category Tomorrow", 500);
        exit();
    } else if ($conn->query($sql3) === False) {
        error("Error insert Default category Someday", 500);
        exit();
    }
}

/**
 * jiz existuje urcity task uvnitr urciteho category
 */
function existSameRelation($task_id)
{
    global $conn;
    $sql = "SELECT task_id, category_id
            FROM relation_task_categ
            WHERE task_id = ?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        error("sql error", 500);
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, "i", $task_id);
        //выполнить
        if (!mysqli_stmt_execute($stmt)) {
            error("sql error", 500);
            mysqli_error($conn);
            exit();
        }
        $result = mysqli_stmt_get_result($stmt);
        if ($result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }
}

/**
 * Prihlasen li klient
 */
function isLogin()
{
    if (isset($_SESSION['userId'])) {
        return $_SESSION['userId'];
    } else {
        error("Not login", 409);
        return null;
    }
}

function isUserExist($username, $email)
{
    global $conn;
    $sql = "select username, email from users where username=? or email=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        error("sql error", 500);
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, "ss", $username, $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $resultcheck = mysqli_stmt_num_rows($stmt);
        if ($resultcheck > 0) {
            return true;
        } else {
            return false;
        }
    }
}

function emailExist($email)
{
    global $conn;
    $sql = "select email from users where email=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        error("sql error", 500);
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $resultcheck = mysqli_stmt_num_rows($stmt);
        if ($resultcheck > 0) {
            return true;
        } else {
            return false;
        }
    }
}

function usernameExist($username)
{
    global $conn;
    $sql = "select username from users where username=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        error("sql error", 500);
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        $resultcheck = mysqli_stmt_num_rows($stmt);
        if ($resultcheck > 0) {
            return true;
        } else {
            return false;
        }
    }
}

/**
 * Current user je tvurcem tasku
 */
function isOwnerOfTask($taskId)
{
    global $conn;
    if (isLogin() === null) {
        exit();
    }
    $user_id = isLogin();
    $sql = "select creator_id from task where Id='$taskId'";
    $result = $conn->query($sql);
    $row = mysqli_fetch_array($result);
    if ($row['creator_id'] == $user_id) {
        return true;
    } else {
        return false;
    }
}

/**
 * Current user je tvurcem category
 */
function isOwnerOfCategory($categoryId)
{
    global $conn;
    if (isLogin() === null) {
        exit();
    }
    $user_id = isLogin();
    $sql = "select creator_id from category where id='$categoryId'";
    $result = $conn->query($sql);
    $row = mysqli_fetch_array($result);
    if ($row['creator_id'] == $user_id) {
        return true;
    } else {
        return false;
    }
}

function checkPass($currnetPass, $user_id)
{
    global $conn;
    $sql = "select password from users where id=?";
    $stmt = mysqli_stmt_init($conn);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        error("sql error", 500);
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        //выполнить
        if (!mysqli_stmt_execute($stmt)) {
            error("sql error", 500);
            mysqli_error($conn);
            exit();
        }
        $result = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($result)) {
            $passCheck = password_verify($currnetPass, $row['password']);
            if (!$passCheck) {
                error("Current password is wrong", 400);
                return false;
            } else {
                return true;
            }
        }
        error("No user", 200);
        return false;
    }
}

function error($message, $res_code)
{
    http_response_code($res_code);
    $res = [
        "status" => false,
        "message" => "$message"
    ];
    echo json_encode($res);
}

function success($message, $res_code)
{
    http_response_code($res_code);
    $res = [
        "status" => true,
        "message" => $message
    ];
    echo json_encode($res);
}

function postSuccess($message, $object_id)
{
    http_response_code(201);
    $res = [
        "status" => true,
        "message" => "$message",
        "id" => "$object_id"
    ];
    echo json_encode($res);
}