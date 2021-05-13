<?php

class Environment
{
    public function __construct()
    {
        putenv("DB_HOST=192.168.1.100");
        putenv("DB_USER=client");
        putenv("DB_NAME=carpool");
        putenv("DB_PASSWORD=user123");
        putenv("DB_PORT=3307");
        putenv("ENCRYPT_KEY=encrypt_key");
        putenv("IV=@wecode19931214@");
        putenv("JWT_KEY=mysecretkeyinsecretkey");
    }
}
