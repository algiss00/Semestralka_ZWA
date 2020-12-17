<?php
if (!isset($_SESSION)) {
    session_start();
}

$method = $_SERVER['REQUEST_METHOD'];
require "../methodsForApi/dbConnectMethods.php";

if ($method == "POST") {
    if (isset($_GET['addTaskToCategory'])) {
        if (isset($_GET['task_id']) && isset($_GET['category'])) {
            $task_id = $_GET['task_id'];
            $category = $_GET['category'];
            /**
             * pridani tasku do category
             */
            addTaskToCategory($task_id, $category);
        }
    } else if (isset($_GET['addCateogry'])) {
        $res = file_get_contents("php://input", true);
        $category = json_decode($res, true);
        /**
         * pridani Category
         */
        addCategory($category['title'], $category['position_list']);
    } else if (isset($_GET['updateCategory'])) {
        if (isset($_GET['id'])) {
            $category_id = $_GET['id'];
            $res = file_get_contents("php://input", true);
            $category = json_decode($res);
            updateCategory($category_id, $category->title, $category->position_list);
        }
    } else if (isset($_GET['deleteCategory'])) {
        if (isset($_GET['id'])) {
            $category_id = $_GET['id'];
            deleteCategory($category_id);
        } else if (isset($_GET['category_id']) && isset($_GET['task_id'])) {
            $category_id = $_GET['category_id'];
            $task_id = $_GET['task_id'];
            deleteTaskFromCategory($category_id, $task_id);
        }
    }
} else if ($method == "GET") {
    if (isset($_GET['task']) && isset($_GET['category_id'])) {
        if ($_GET['task'] == "all") {
            $category_id = $_GET['category_id'];
            /**
             * get All Tasks from category
             */
            echo getAllTaskFromCategory($category_id);
        }
    } else if (isset($_GET['id'])) {
        $category_id = $_GET['id'];
        echo getCategory($category_id);
    } else if (isset($_GET['group'])) {
        /**
         * GET category podle poctu tasku u nej
         */
        echo getGroupCategory();
    } else {
        $res = getAllCategoriesOfUser();
        echo $res;
    }
} //else if ($method == "PUT") {
//    if (isset($_GET['id'])) {
//        $category_id = $_GET['id'];
//        $res = file_get_contents("php://input", true);
//        $category = json_decode($res);
//        updateCategory($category_id, $category->title, $category->position_list);
//    }
//} else if ($method == "DELETE") {
//    if (isset($_GET['id'])) {
//        $category_id = $_GET['id'];
//        deleteCategory($category_id);
//    } else if (isset($_GET['category_id']) && isset($_GET['task_id'])) {
//        $category_id = $_GET['category_id'];
//        $task_id = $_GET['task_id'];
//        deleteTaskFromCategory($category_id, $task_id);
//    }
//}





