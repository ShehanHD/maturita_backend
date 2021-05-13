<?php

function __autoload($class_name)
{
    $folders = array(
        "api",
        "helpers",
        "User",
        "Configuration",
        "User",
        "Passenger",
        "Trip",
        "Reservation",
        "Feedback"
    );
    $filename = $class_name . '.php';

    $file = $filename;

    foreach ($folders as $folder) {
        if (file_exists("./src/$folder/" . $file)) {
            require_once("./src/$folder/$file");
            return true;
        }
    }
    return false;
}

function controlOrigin($ORIGIN): bool
{
    $whiteList = array("https://maturita.wecode.best", "http://localhost:3000", null);
    $verified = false;

    foreach ($whiteList as $value) {
        if($value === $ORIGIN){
            $verified = true;
        }
    }

    return $verified;
}

function sendMail($body, $https_server ="192.168.1.100:40006/api/send"){
    $opts = array('http' =>
        array(
            'method'  => 'POST',
            'header'  => "Content-Type: application/json",
            'content' => json_encode($body),
            'timeout' => 60
        )
    );

    $context  = stream_context_create($opts);
    $url = "http://".$https_server;

    return file_get_contents($url, false, $context =$context);
}

/*
 $body = [
            "from" => "wecode.best.server@gmail.com",
            "to"=> "sathsaranifernando001@gmail.com ",
            "subject"=> "Verify user registration",
            "text"=> "test mail",
            "html"=> $this->prepareMail("Moda Yakaaaaaaaaaaaa")
            ];

        $response = sendMail($body);
        echo $response;

public function prepareMail($code): string
{
    return ("
        <!DOCTYPE html>
        <html lang='en'>
        <head>
        <style>
        h1 {
          color: rgb(247, 247, 247);
          text-align: center;
          font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
        background-color: blue;
        }
        p  {
          color: rgb(0, 0, 0);
          font-family: courier,serif;
          font-size: 160%;
        }
        </style>
        <title>Carpool</title>
        </head>
        <body>

        <h1>Carpool</h1>
        <p>Your verification code is : <b>$code</b></p>

        </body>
        </html>
        ");
}
 */