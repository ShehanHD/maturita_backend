<?php

class UserController extends Rest
{
    private UserService $userService;

    /**
     * UserController constructor.
     * @param $REQUEST_METHOD
     * @param $PARAMS
     * @param $BODY
     */
    public function __construct($REQUEST_METHOD, $PARAMS, $BODY)
    {
        $this->userService = new UserService();
        parent::__construct($REQUEST_METHOD, $PARAMS, $BODY);
    }

    /**
     * @param $PARAMS
     * @param $BODY
     */
    function getMapping($PARAMS, $BODY)
    {
        switch ($PARAMS[0]){
            case "login":
                $this->userService->loginUser($PARAMS[1], $PARAMS[2]);
                break;
            default:
                HTTP_Response::Send(HTTP_Response::MSG_NOT_FOUND, HTTP_Response::NOT_FOUND);
                break;
        }
    }

    /**
     * @param $PARAMS
     * @param $BODY
     */
    function postMapping($PARAMS, $BODY)
    {
        switch ($PARAMS[0]){
            case "register":
                $this->userService->newUser($BODY);
                break;
            case 'add_license':
                $this->userService->addLicense($BODY);
                break;
            case 'add_vehicle':
                $this->userService->addVehicle($BODY);
                break;
            default:
                HTTP_Response::Send(HTTP_Response::MSG_NOT_FOUND, HTTP_Response::NOT_FOUND);
                break;
        }
    }
}