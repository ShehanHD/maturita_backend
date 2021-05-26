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
            HTTP_Response::SendWithBody(HTTP_Response::MSG_BAD_REQUEST,["error_msg" => "Password o email sbagliato!"], HTTP_Response::BAD_REQUEST);
        }
    }

    public function loginUser($BODY)
    {
        $this->userRepository->loginUser($BODY);
    }

    public function addLicense($BODY)
    {
        Authentication::verifyJWT()
            ? $this->userRepository->addLicense(Authentication::getId(), $BODY)
            : HTTP_Response::Send(HTTP_Response::MSG_UNAUTHORIZED, HTTP_Response::UNAUTHORIZED);
    }

    public function addVehicle($BODY)
    {
        Authentication::verifyJWT()
            ? $this->userRepository->addVehicle(Authentication::getId(), $BODY)
            : HTTP_Response::Send(HTTP_Response::MSG_UNAUTHORIZED, HTTP_Response::UNAUTHORIZED);
    }
}