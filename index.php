<?php
require_once("./src/helpers/common.php");
require_once("./src/api/HttpMaping.php");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Authorization, Content-Type,Accept, Origin");
    exit(0);
}

$HTTP_ORIGIN = $_SERVER['HTTP_ORIGIN'];

if (controlOrigin($HTTP_ORIGIN)) {
    header("Access-Control-Allow-Origin: $HTTP_ORIGIN");
    new Environment;

    $URL = preg_split('@/@', $_SERVER['REQUEST_URI'], -1, PREG_SPLIT_NO_EMPTY);
    $REQUEST_METHOD = $_SERVER['REQUEST_METHOD'];
    $BASE_CONTROLLER = $URL[2] ?? "";
    $PARAMS = array_slice($URL, 3) ?? "";
    $BODY = json_decode(file_get_contents('php://input')) ?? [];

    if ($URL[0] === "v1" && $URL[1] === "api") {
        new Api($BASE_CONTROLLER, $REQUEST_METHOD, $PARAMS, $BODY);
    }
    else{
        HTTP_Response::Send(HTTP_Response::MSG_NOT_FOUND, HTTP_Response::NOT_FOUND);
    }
} else {
    HTTP_Response::Send(HTTP_Response::MSG_UNAUTHORIZED, HTTP_Response::UNAUTHORIZED);
}

