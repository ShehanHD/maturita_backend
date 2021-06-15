<?php

function __autoload($class_name): bool
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
    $whiteList = array("https://backend.wecode.best", "http://localhost:3000", null);
    $verified = false;

    foreach ($whiteList as $value) {
        if($value === $ORIGIN){
            $verified = true;
        }
    }

    return $verified;
}

function sendMail($state, $data){
    $email = "";
    switch ($state){
        case "accepted":
            $email = reservationConformMail($data[0]);
            break;
        case "refused":
            $email = reservationRejectMail($data[0]);
            break;
        case "new":
            $email = newReservationMail($data);
            break;
        default:
            HTTP_Response::Send(HTTP_Response::MSG_BAD_REQUEST, HTTP_Response::BAD_REQUEST);
            break;
    }

    $body = [
        "from" => "wecode.best.server@gmail.com",
        "to"=> $data[0]['email'],
        "subject"=> "Verify user registration",
        "text"=> "test mail",
        "html"=> $email
    ];

    $opts = array('http' =>
        array(
            'method'  => 'POST',
            'header'  => "Content-Type: application/json",
            'content' => json_encode($body),
            'timeout' => 60
        )
    );

    $context  = stream_context_create($opts);
    $url = "http://192.168.1.100:40006/api/send";

    return file_get_contents($url, false, $context);
}

function reservationConformMail($data): string
{
    $tripId = $data["id"];
    $partenza = $data["partenza"];
    $destinazione = $data["destinazione"];
    $data_di_partenza = $data["data_di_partenza"];
    $durata = $data["durata"];
    $contributo = $data["contributo"];
    $soste = $data["soste"];
    $bagagli = $data["bagagli"] ? "Yes" : "No";
    $animali = $data["animali"] ? "Yes" : "No";
    $cognome = $data["cognome"];
    $nome = $data["nome"];
    $email = $data["email"];
    $telefono = $data["telefono"];

    return ("
            <html>
            
            <head>
              <style>
                h1 {
                  color: rgb(247, 247, 247);
                  text-align: center;
                  font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
                  background-color: #334a5a;
                  border-radius: 0 2em;
                  padding: 1vh;
                }
            
                p {
                  color: rgb(0, 0, 0);
                  text-align: center;
                }
            
                .content {
                  display: flex;
                  justify-content: center !important;
                }
            
                .footer {
                  background-color: #334a5a;
                  padding: 1vh;
                }
            
                .footer>p {
                  color: white;
                }
            
                .footer>p>a {
                  color: #a0e9f1;
                }
            
                .footer>p>a:visited {
                  color: #a0e9f1;
                }
            
                img {
                  width: 15vw;
                }
              </style>
            </head>
            
            <body>
              <h1>Carpool</h1>
              <p>Your request has been <b>accepted</b></p>
            
              <div class='content'>
                <img src='https://www.seekpng.com/png/full/373-3737336_uber-clipart.png'>
                <ul>
                  <li>Id trip: $tripId</li>
                  <li>Departure from: $partenza</li>
                  <li>Destination to: $destinazione</li>
                  <li>Departure at: $data_di_partenza</li>
                  <li>Estimated duration: $durata</li>
                  <li>Contribution: $contributo</li>
                  <li>Stops: $soste</li>
                  <li>Luggages: $bagagli</li>
                  <li>Pets: $animali</li>
                </ul>
              </div>
              <div class='footer'>
                <p>For more information you can contact you driver <b>$cognome $nome</b></p>
                <p>email <a href='mailto:$email'>$email</a> and Telephone <a href='tel:+$telefono'>$telefono</a></p>
              </div>
            </body>
            
            </html>
        ");
}

function reservationRejectMail($data) : string
{
    $tripId = $data["id"];
    $partenza = $data["partenza"];
    $destinazione = $data["destinazione"];
    $cognome = $data["cognome"];
    $nome = $data["nome"];
    $email = $data["email"];
    $telefono = $data["telefono"];

    return ("
            <html>
            
            <head>
              <style>
                h1 {
                  color: rgb(247, 247, 247);
                  text-align: center;
                  font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
                  background-color: #334a5a;
                  border-radius: 0 2em;
                  padding: 1vh;
                }
            
                p {
                  color: rgb(0, 0, 0);
                  text-align: center;
                }
            
                .content {
                  display: flex;
                  justify-content: center !important;
                }
            
                .footer {
                  background-color: #334a5a;
                  padding: 1vh;
                }
            
                .footer>p {
                  color: white;
                }
            
                .footer>p>a {
                  color: #a0e9f1;
                }
            
                .footer>p>a:visited {
                  color: #a0e9f1;
                }
            
                img {
                  width: 15vw;
                }
              </style>
            </head>
            
            <body>
            
              <h1>Carpool</h1>
              <p>Your request has been <b>rejected</b></p>
            
                <div class='content'>
                    <img src='https://www.seekpng.com/png/full/373-3737336_uber-clipart.png'>
                    <ul>
                      <li>Id trip: $tripId</li>
                      <li>Departure from: $partenza</li>
                      <li>Destination to: $destinazione</li>
                    </ul>
                </div>
              
              <div class='footer'>
                <p>For more information you can contact you driver <b>$cognome $nome</b></p>
                <p>email <a href='mailto:$email'>$email</a> and Telephone <a href='tel:+$telefono'>$telefono</a></p>
              </div>
            </body>
            
            </html>
        ");
}

function newReservationMail($data) : string{
    $tripId = $data[0]["trip_id"];
    $partenza = $data[0]["partenza"];
    $destinazione = $data[0]["destinazione"];
    $cognome = $data[0]["cognome"];
    $nome = $data[0]["nome"];
    $email = $data[0]["email"];
    $telefono = $data[0]["telefono"];

    function feedbacks($data)
    {
        $str = "";

        foreach ($data as $row) {
            $n = $row['nome_autista'];
            $c = $row['cognome_autista'];
            $g = $row['giudizio'];
            $v = $row['voto'];
               $str .= "<ul>
                            <li>From: $n $c</li>
                            <li>Feedback: $g</li>
                            <li>Voto: $v</li>
                       </ul>";
        }

        return $str;
    }

    return ("
            <html>
            
            <head>
              <style>
                h1 {
                  color: rgb(247, 247, 247);
                  text-align: center;
                  font-family: 'Lucida Sans', 'Lucida Sans Regular', 'Lucida Grande', 'Lucida Sans Unicode', Geneva, Verdana, sans-serif;
                  background-color: #334a5a;
                  border-radius: 0 2em;
                  padding: 1vh;
                }
            
                p {
                  color: rgb(0, 0, 0);
                  text-align: center;
                }
            
                .content {
                  display: flex;
                  justify-content: center !important;
                }
            
                .footer {
                  background-color: #334a5a;
                  padding: 1vh;
                }
            
                .footer>p {
                  color: white;
                }
            
                .footer>p>a {
                  color: #a0e9f1;
                }
            
                .footer>p>a:visited {
                  color: #a0e9f1;
                }
            
                img {
                  width: 15vw;
                }
                
                .feedbacks{
                    border-top: solid #334a5a 2px;
                }
              </style>
            </head>
            <body>
            
              <h1>Carpool</h1>
              <p>New reservation on your Trip ($tripId)</p>
            
                <div class='content'>
                    <img src='https://www.seekpng.com/png/full/373-3737336_uber-clipart.png'>
                    <ul>
                      <li>Departure from: $partenza</li>
                      <li>Destination to: $destinazione</li>
                      <li>Passanger: $cognome $nome</li>
                       <li>e-mail <a href='mailto:$email'>$email</a></li>
                       <li>Telephone number <a href='tel:+$telefono'>$telefono</a></li>
                    </ul>
                </div>
                
                <div class='feedbacks'>
                <h3>Feedbacks from other drivers</h3>
                ".feedbacks($data)."
                </div>
                
                <div class='footer'>
                <p><a href='https://carpool.wecode.best'>carpoolig web site</a></p>
                <p>contact us for any information. email <a href='mailto:wecode.best.server@gmail.com'>click here!</a></p>
              </div>
            </body>
            
            </html>
        ");
}
