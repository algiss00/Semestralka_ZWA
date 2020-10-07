<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Methods: *');
header('Access-Control-Allow-Credentials: true');
header('Content-type: json/application');

if (!isset($_SESSION)) {
    session_start();
}

$user = 'root';
$password = '';
$dbname = 'todolistdb';

$conn = mysqli_connect('localhost', $user, $password, $dbname)
or die("Unable to connect" . mysqli_connect_error());

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
        "message" => "$message"
    ];
    echo json_encode($res);
}


function isLogin()
{
    if (isset($_SESSION['userId'])) {
        $user_id = $_SESSION['userId'];
        return $user_id;
    } else {
        error("Not login", 403);
        return null;
    }
}

function isOwnerOfTask($taskId, $connect)
{
    if (isLogin() === null) {
        exit();
    }
    $user_id = isLogin();
    $sql = "select creator_id from task where Id='$taskId'";
    $result = $connect->query($sql);
    $row = mysqli_fetch_array($result);
    if ($row['creator_id'] == $user_id) {
        return true;
    } else {
        return false;
    }
}

function isOwnerOfCategory($categoryId, $connect)
{
    if (isLogin() === null) {
        exit();
    }
    $user_id = isLogin();
    $sql = "select creator_id from category where id='$categoryId'";
    $result = $connect->query($sql);
    $row = mysqli_fetch_array($result);
    if ($row['creator_id'] == $user_id) {
        return true;
    } else {
        return false;
    }
}

function getTask($taskId, $connect)
{
    if (!existTask($taskId, $connect)) {
        error("Task not found", 404);
        exit();
    }
    if (isOwnerOfTask($taskId, $connect) == false) {
        error("Forbidden", 403);
        exit();
    } else if (isOwnerOfTask($taskId, $connect) == true) {
        $sql = "select * from task where Id=?";
        $stmt = mysqli_stmt_init($connect);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            error("sql error", 400);
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "i", $taskId);
            //выполнить
            if (!mysqli_stmt_execute($stmt)) {
                error("sql error", 400);
                mysqli_error($connect);
                exit();
            }
            //result
            $result = mysqli_stmt_get_result($stmt);
            $res = mysqli_fetch_assoc($result);
            return json_encode($res);
        }
    }
}

function getCategory($categoryId, $connect)
{
    if (!existCategory($categoryId, $connect)) {
        error("Category not found", 404);
        exit();
    }
    if (isOwnerOfCategory($categoryId, $connect) == false) {
        error("Forbidden", 403);
        exit();
    } else if (isOwnerOfCategory($categoryId, $connect) == true) {
        $sql = "select * from category where id=?";
        $stmt = mysqli_stmt_init($connect);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            error("sql error", 400);
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "i", $categoryId);
            //выполнить
            if (!mysqli_stmt_execute($stmt)) {
                error("sql error", 400);
                mysqli_error($connect);
                exit();
            }
            //result
            $result = mysqli_stmt_get_result($stmt);
            $res = mysqli_fetch_assoc($result);
            return json_encode($res);
        }
    }
}


function getCurrentUser($connect)
{
    if (isLogin() === null) {
        exit();
    }
    $user_id = isLogin();
    $sql = "select * from users where id=?";
    $stmt = mysqli_stmt_init($connect);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        error("sql error", 400);
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        //выполнить
        if (!mysqli_stmt_execute($stmt)) {
            error("sql error", 400);
            mysqli_error($connect);
            exit();
        }
        //result
        $result = mysqli_stmt_get_result($stmt);
        $res = mysqli_fetch_assoc($result);
        return json_encode($res);
    }
}

function getAllCategoriesOfUser($connect)
{
    if (isLogin() === null) {
        exit();
    }
    $user_id = isLogin();
    $categories = $connect->query("select * from category
        where creator_id = '$user_id' order by position_list ASC")
    or die(mysqli_error($connect));
    if ($categories->num_rows > 0) {
        $arr = [];
        while ($categ = $categories->fetch_assoc()) {
            array_push($arr, $categ);
        }
        return json_encode($arr);
    } else {
        error("No category", 404);
    }
}

function getAllUsersTasks($connect)
{
    if (isLogin() === null) {
        exit();
    }
    $user_id = isLogin();
    $tasks = $connect->query("select * from task where creator_id='$user_id' order by deadline ASC ")
    or die(mysqli_error($connect));
    if ($tasks->num_rows > 0) {
        $arr = [];
        while ($tasks2 = $tasks->fetch_assoc()) {
            array_push($arr, $tasks2);
        }
        return json_encode($arr);
    } else {
        error("No tasks", 404);
    }
}

function getAllTaskFromCategory($categId, $connect)
{
    if (!existCategory($categId, $connect)) {
        error("Category not found", 404);
        exit();
    }
    if (isOwnerOfCategory($categId, $connect) == false) {
        error("Forbidden", 403);
        exit();
    } else if (isOwnerOfCategory($categId, $connect) == true) {
        $sql = "SELECT relation_task_categ.task_id, relation_task_categ.category_id, task.*
            FROM relation_task_categ, task
            WHERE (relation_task_categ.category_id = ?) AND (task.Id = relation_task_categ.task_id)";
        $stmt = mysqli_stmt_init($connect);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            error("sql error", 400);
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "i", $categId);
            //выполнить
            if (!mysqli_stmt_execute($stmt)) {
                error("sql error", 400);
                mysqli_error($connect);
                exit();
            }
            $result = mysqli_stmt_get_result($stmt);
            if ($result->num_rows > 0) {
                $arr = [];
                while ($res = mysqli_fetch_assoc($result)) {
                    array_push($arr, $res);
                }
                return json_encode($arr);
            } else {
                error("No tasks in category", 404);
            }
        }
    }
}

function addTask($data, $connect)
{
    if (isLogin() === null) {
        exit();
    }
    $user_id = isLogin();
    $title = $data['title'];
    $description = $data['description'];
    $deadline = $data['deadline'];

    $sql = "insert into task(title, description, deadline, creator_id) values (?, ?, ?, ?)";
    $stmt = mysqli_stmt_init($connect);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        error("sql error", 400);
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, "sssi", $title, $description, $deadline, $user_id);
        //выполнить
        if (!mysqli_stmt_execute($stmt)) {
            error("sql error", 400);
            mysqli_error($connect);
            exit();
        }
        success("create success", 201);
    }
}

function addToRelationTaskCateg($task_id, $category_id, $connect)
{
    if (!(existTask($task_id, $connect)) || !(existCategory($category_id, $connect))) {
        error("Task or Category not found", 404);
        exit();
    }
    if (isOwnerOfCategory($category_id, $connect) == false || isOwnerOfTask($task_id, $connect) == false) {
        error("Forbidden", 403);
        exit();
    } else if (isOwnerOfCategory($category_id, $connect) == true && isOwnerOfTask($task_id, $connect) == true) {
        $sql = "SELECT task_id, category_id
            FROM relation_task_categ
            WHERE category_id = ? AND task_id = ?";
        $stmt = mysqli_stmt_init($connect);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            error("sql error", 400);
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "ii", $category_id, $task_id);
            //выполнить
            if (!mysqli_stmt_execute($stmt)) {
                error("sql error", 400);
                mysqli_error($connect);
                exit();
            }
            $result = mysqli_stmt_get_result($stmt);
            if ($result->num_rows > 0) {
                error("Same relation exist", 400);
                exit();
            } else {
                $sql = "insert into relation_task_categ(task_id, category_id) values (?, ?)";
                $stmt = mysqli_stmt_init($connect);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    error("sql error", 400);
                    exit();
                } else {
                    mysqli_stmt_bind_param($stmt, "ii", $task_id, $category_id);
                    //выполнить
                    if (!mysqli_stmt_execute($stmt)) {
                        error("sql error", 400);
                        mysqli_error($connect);
                        exit();
                    }
                    success("Add task to category success", 200);
                }
            }
        }
    }
}

function addCategory($title, $position, $connect)
{
    if (isLogin() === null) {
        exit();
    }
    $user_id = isLogin();
    $sql = "select title from category where title=?";
    $stmt = mysqli_stmt_init($connect);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        error("sql error", 400);
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, "s", $title);
        //выполнить
        if (!mysqli_stmt_execute($stmt)) {
            error("sql error", 400);
            mysqli_error($connect);
            exit();
        }
        //result
        mysqli_stmt_store_result($stmt);
        $resultcheck = mysqli_stmt_num_rows($stmt);
        if ($resultcheck > 0) {
            error("Category exist", 400);
            exit();
        } else {
            $sql = "insert into category(title, position_list, creator_id) values (?, ?, ?)";
            $stmt = mysqli_stmt_init($connect);
            if (!mysqli_stmt_prepare($stmt, $sql)) {
                error("sql error", 400);
                exit();
            } else {
                mysqli_stmt_bind_param($stmt, "sii", $title, $position, $user_id);
                //выполнить
                if (!mysqli_stmt_execute($stmt)) {
                    error("sql error", 400);
                    mysqli_error($connect);
                    exit();
                }
                success("Create category success", 201);
            }
        }
    }
}

function updateUser($username, $password, $email, $connect)
{
    if (isLogin() === null) {
        exit();
    }
    $user_id = isLogin();
    $sql = "UPDATE users
            SET username = ?, password= ?, email=?
            WHERE id = ?";
    $stmt = mysqli_stmt_init($connect);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        error("sql error", 400);
        exit();
    } else {
        $hashedPwd = password_hash($password, PASSWORD_DEFAULT);
        mysqli_stmt_bind_param($stmt, "sssi", $username, $hashedPwd, $email, $user_id);
        //выполнить
        if (!mysqli_stmt_execute($stmt)) {
            error("sql error", 400);
            mysqli_error($connect);
            exit();
        }
        success("Update current user success", 200);
    }
}


function updateTask($taskId, $title, $description, $deadline, $status, $connect)
{
    if (!existTask($taskId, $connect)) {
        error("Task not found", 404);
        exit();
    }
    if (isOwnerOfTask($taskId, $connect) == false) {
        error("Forbidden", 403);
        exit();
    } else if (isOwnerOfTask($taskId, $connect) == true) {
        $sql = "UPDATE task
            SET title = ?, description= ?, deadline=?, status=?
            WHERE Id = ?";
        $stmt = mysqli_stmt_init($connect);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            error("sql error", 400);
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "ssssi", $title, $description, $deadline, $status, $taskId);
            //выполнить
            if (!mysqli_stmt_execute($stmt)) {
                error("sql error", 400);
                mysqli_error($connect);
                exit();
            }
            success("Update task success", 200);
        }
    }
}

function updateStatusTask($taskId, $status, $connect)
{
    if (!existTask($taskId, $connect)) {
        error("Task not found", 404);
        exit();
    }
    if (isOwnerOfTask($taskId, $connect) == false) {
        error("Forbidden", 403);
        exit();
    } else if (isOwnerOfTask($taskId, $connect) == true) {
        $sql = "UPDATE task
            SET status=?
            WHERE Id = ?";
        $stmt = mysqli_stmt_init($connect);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            error("sql error", 400);
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "si", $status, $taskId);
            //выполнить
            if (!mysqli_stmt_execute($stmt)) {
                error("sql error", 400);
                mysqli_error($connect);
                exit();
            }
            success("Update status of task success", 200);
        }
    }
}

function updateCategory($category_id, $title, $position_list, $connect)
{
    if (isOwnerOfCategory($category_id, $connect) == false) {
        error("Forbidden", 403);
        exit();
    } else if (isOwnerOfCategory($category_id, $connect) == true) {
        $sql = "UPDATE category
            SET title = ?, position_list= ?
            WHERE id = ?";
        $stmt = mysqli_stmt_init($connect);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            error("sql error", 400);
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "sii", $title, $position_list, $category_id);
            //выполнить
            if (!mysqli_stmt_execute($stmt)) {
                error("sql error", 400);
                mysqli_error($connect);
                exit();
            }
            success("Update category success", 200);
        }
    }
}

function deleteCategory($category_id, $connect)
{
    if (!existCategory($category_id, $connect)) {
        error("Category not found", 404);
        exit();
    }
    if (isOwnerOfCategory($category_id, $connect) == false) {
        error("Forbidden", 403);
        exit();
    } else if (isOwnerOfCategory($category_id, $connect) == true) {
        $sql = "DELETE FROM category WHERE id=?";
        $stmt = mysqli_stmt_init($connect);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            error("sql error", 400);
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "i", $category_id);
            //выполнить
            if (!mysqli_stmt_execute($stmt)) {
                error("sql error", 400);
                mysqli_error($connect);
                exit();
            }
            success("Delete success", 200);
        }
    }
}

function deleteTaskFromCategory($category_id, $task_id, $connect)
{
    if (!(existTask($task_id, $connect)) || !(existCategory($category_id, $connect))) {
        error("Task or Category not found", 404);
        exit();
    }
    if (isOwnerOfTask($task_id, $connect) == false) {
        error("Forbidden", 403);
        exit();
    } else if (isOwnerOfTask($task_id, $connect) == true) {
        $sql = "DELETE FROM relation_task_categ WHERE category_id=? AND task_id=?";
        $stmt = mysqli_stmt_init($connect);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            error("sql error", 400);
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "ii", $category_id, $task_id);
            //выполнить
            if (!mysqli_stmt_execute($stmt)) {
                error("sql error", 400);
                mysqli_error($connect);
                exit();
            }
            success("Delete success", 200);
        }
    }
}

function deleteTask($taskId, $connect)
{
    if (!existTask($taskId, $connect)) {
        error("Task not found", 404);
        exit();
    }
    if (isOwnerOfTask($taskId, $connect) == false) {
        error("Forbidden", 403);
        exit();
    } else if (isOwnerOfTask($taskId, $connect) == true) {
        $sql = "DELETE FROM task WHERE Id=?";
        $stmt = mysqli_stmt_init($connect);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            error("sql error", 400);
            exit();
        } else {
            mysqli_stmt_bind_param($stmt, "i", $taskId);
            //выполнить
            if (!mysqli_stmt_execute($stmt)) {
                error("sql error", 400);
                mysqli_error($connect);
                exit();
            }
            success("Delete success", 200);
        }
    }
}

function existTask($taskId, $connect)
{
    $sql = "select * from task where Id=?";
    $stmt = mysqli_stmt_init($connect);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        error("sql error", 400);
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, "i", $taskId);
        //выполнить
        if (!mysqli_stmt_execute($stmt)) {
            error("sql error", 400);
            mysqli_error($connect);
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

function existCategory($catId, $connect)
{
    $sql = "select * from category where id=?";
    $stmt = mysqli_stmt_init($connect);
    if (!mysqli_stmt_prepare($stmt, $sql)) {
        error("sql error", 400);
        exit();
    } else {
        mysqli_stmt_bind_param($stmt, "i", $catId);
        //выполнить
        if (!mysqli_stmt_execute($stmt)) {
            error("sql error", 400);
            mysqli_error($connect);
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

