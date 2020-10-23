<?php

if (!isset($_SESSION)) {
    session_start();
}
session_unset();
session_destroy();

http_response_code(200);
$res = [
    "status" => true,
    "message" => "Logout success"
];
echo json_encode($res);
