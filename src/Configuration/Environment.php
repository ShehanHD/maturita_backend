<?php

class Environment
{
    public function __construct()
    {
        putenv("DB_HOST=...");
        putenv("DB_USER=...");
        putenv("DB_NAME=...");
        putenv("DB_PASSWORD=...");
        putenv("DB_PORT=3307");
        putenv("ENCRYPT_KEY=encrypt_key");
        putenv("IV=...");
        putenv("JWT_KEY=mysecretkeyinsecretkey");
    }
}
