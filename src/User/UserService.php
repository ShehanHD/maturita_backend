<?php


class UserService
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function newUser($BODY){
        if (!strcmp($BODY->password, $BODY->re_password) && filter_var($BODY->email, FILTER_VALIDATE_EMAIL)){
            $this->userRepository->newUser($BODY);
        }
        else{
            HTTP_Response::Send(HTTP_Response::MSG_BAD_REQUEST, HTTP_Response::BAD_REQUEST);
        }
    }

    public function loginUser($BODY)
    {
        $this->userRepository->loginUser($BODY);
    }

    public function addIdentity($id, $BODY)
    {
        $this->userRepository->addIdentity($id, $BODY);
    }

    public function addLicense($id, $BODY)
    {
        $this->userRepository->addLicense($id, $BODY);
    }
}