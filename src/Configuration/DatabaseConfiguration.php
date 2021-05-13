<?php


class DatabaseConfiguration
{
    private string $user = "";
    private string $password = "";
    private string $dsn = "";
    private string $dbname;

    public function __construct()
    {
        $host = getenv('DB_HOST');
        $port = getenv('DB_PORT');
        $this->user = getenv('DB_USER');
        $this->password = getenv('DB_PASSWORD');
        $this->dsn = getenv('DB_HOST');
        $this->dbname = getenv("DB_NAME");

        $this->dsn = 'mysql:host=' . $host . ';dbname=' . $this->dbname . ';port=' . $port;
    }

    public function connection(): PDO
    {
        try {
            $pdo = new PDO($this->dsn, $this->user, $this->password);

            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $pdo;
        } catch (PDOException $e) {
            throw $e;
        }
    }
}