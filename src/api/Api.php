<?php

class Api
{
    public function __construct($BASE_CONTROLLER, $REQUEST_METHOD, $PARAMS, $BODY)
    {
        switch ($BASE_CONTROLLER){
            case "user":
                new UserController($REQUEST_METHOD, $PARAMS, $BODY);
                break;
            case "trip":
                new TripController($REQUEST_METHOD, $PARAMS, $BODY);
                break;
            case "reservation":
                new ReservationController($REQUEST_METHOD, $PARAMS, $BODY);
                break;
            case "feedback":
                new FeedbackController($REQUEST_METHOD, $PARAMS, $BODY);
                break;
            default:
                HTTP_Response::Send(HTTP_Response::MSG_NOT_FOUND, HTTP_Response::NOT_FOUND);
                break;
        }
    }

}
