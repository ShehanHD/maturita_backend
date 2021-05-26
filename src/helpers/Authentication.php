<?php
require_once("./vendor/firebase/php-jwt/src/JWT.php");

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;

class Authentication
{
    private $JWT_KEY;

    public function __construct()
    {
        $this->JWT_KEY = getenv("JWT_KEY");
    }

    public function generateJWT($data): string
    {
        $key = $this->JWT_KEY;
        $payload = array(
            "iss" => "https://wecode.best",
            "aud" => "https://carpool.wecode.best",
            "iat" => time(),
            "nbf" => time() + 10,
            "exp" => time() + 3600,
            "data" => array(
                'user_id' => Authentication::encrypt($data[0]['id'])
            )
        );

        return JWT::encode($payload, $key, 'HS512');
    }

    public static function encrypt($payload)
    {
        return openssl_encrypt($payload, "aes256", getenv("ENCRYPT_KEY"), 0, getenv("IV"));
    }

    public static function decrypt($payload)
    {
        return openssl_decrypt($payload, "aes256", getenv("ENCRYPT_KEY"), 0, getenv("IV"));
    }

    public static function verifyJWT() : bool{
        try {
            $a = new Authentication;
            $token = $a->getToken();
            $now = new DateTimeImmutable();
            $serverName = "https://wecode.best";

            return !($token->iss !== $serverName || $token->nfb > $now->getTimestamp() || $token->exp < $now->getTimestamp());
        }
        catch (ExpiredException|Exception $e){
            return false;
        }
    }

    public static function getId() : string{
        try {
            $a = new Authentication;
            $token = $a->getToken();

            return $a->decrypt($token->data->user_id);
        }
        catch (ExpiredException|Exception $e){
            throw $e;
        }
    }

    /**
     * @throws Exception
     */
    private function getToken() : object{
        if (!preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
            throw new Exception("Invalid Token");
        }

        return JWT::decode($matches[1], $this->JWT_KEY, ['HS512']);
    }
}