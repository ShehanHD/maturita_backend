<?php

class HTTP_Response{
    const OK = 200;
    const MSG_OK = "Ok";
    const CREATED = 201;
    const MSG_CREATED = "Successfully created";
    const ACCEPTED = 202;
    const NO_CONTENT = 204;
    const NOT_MODIFIED = 304;
    const BAD_REQUEST = 400;
    const MSG_BAD_REQUEST = "Bad request";
    const UNAUTHORIZED = 401;
    const MSG_UNAUTHORIZED = "Unauthorized entry, Access Denied!";
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const MSG_NOT_FOUND = "Please check you request path!";
    const METHOD_NOT_ALLOWED = 405;
    const NOT_ACCEPTABLE = 406;
    const INTERNAL_SERVER_ERROR = 500;
    const MSG_INTERNAL_SERVER_ERROR = "Internal server error";
    const NOT_IMPLEMENTED = 501;

    public static function Send($msg, $code){
        http_response_code($code);
        echo json_encode(array("message" => $msg));
    }

    public static function SendWithBody($msg, $body, $code){
        http_response_code($code);
        echo json_encode(array(
            "message" => $msg,
            "body" => $body
        ));
    }
}