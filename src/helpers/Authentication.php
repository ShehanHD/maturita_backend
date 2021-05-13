<?php
require_once("./vendor/firebase/php-jwt/src/JWT.php");
use Firebase\JWT\JWT;

class Authentication
{
    private $JWT_KEY;

    public function __construct()
    {
        $this->JWT_KEY = getenv("JWT_KEY");
    }

    public function generateJWT($data){
        $key = $this->JWT_KEY;
        $payload = array(
            "iss" => "https://wecode.best",
            "aud" => "https://carpool.wecode.best",
            "iat" => time(),
            "nbf" => time() + 10,
            "exp" => time() + 3600,
            "data" => array(
                'email' => $data[0]['email'],
                'password' => $data[0]['password']
            )
        );

        return JWT::encode($payload, $key);
    }

    public static function encrypt($payload)
    {
        return openssl_encrypt($payload, "aes256", getenv("ENCRYPT_KEY"), 0, getenv("IV"));
    }

    public static function decrypt($payload)
    {
        return openssl_decrypt($payload, "aes256", getenv("ENCRYPT_KEY"), 0, getenv("IV"));
    }
}