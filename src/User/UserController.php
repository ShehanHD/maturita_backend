<?php

class UserController extends Rest implements HttpMapping
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
     * @return mixed
     */
    function getMapping($PARAMS, $BODY)
    {
        switch ($PARAMS[0]){
            default:
                HTTP_Response::Send(HTTP_Response::MSG_NOT_FOUND, HTTP_Response::NOT_FOUND);
        }
    }

    /**
     * @param $PARAMS
     * @param $BODY
     * @return mixed
     */
    function postMapping($PARAMS, $BODY)
    {
        switch ($PARAMS[0]){
            case "register":
                $this->userService->newUser($BODY);
                break;
            case "login":
                $this->userService->loginUser($BODY);
                break;
            default:
                HTTP_Response::Send(HTTP_Response::MSG_NOT_FOUND, HTTP_Response::NOT_FOUND);
        }
    }

    /**
     * @param $PARAMS
     * @param $BODY
     * @return mixed
     */
    function deleteMapping($PARAMS, $BODY)
    {
    }

    /**
     * @param $PARAMS
     * @param $BODY
     * @return mixed
     */
    function patchMapping($PARAMS, $BODY)
    {
        switch ($PARAMS[0]){
            case 'add_id':
                $this->userService->addIdentity($PARAMS[1], $BODY);
                break;
            case 'add_license':
                $this->userService->addLicense($PARAMS[1], $BODY);
                break;
            default:
                HTTP_Response::Send(HTTP_Response::MSG_NOT_FOUND, HTTP_Response::NOT_FOUND);
        }
    }
}