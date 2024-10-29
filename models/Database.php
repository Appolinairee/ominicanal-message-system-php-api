<?php

class Database
{
    private $host;
    private $dbname;
    private $user;
    private $password;
    private $connection;

    public function __construct($config)
    {
        $this->host = $config['host'];
        $this->dbname = $config['dbname'];
        $this->user = $config['user'];
        $this->password = $config['password'];
    }

    public function connect()
    {
        try {
            $this->connection = new PDO("pgsql:host={$this->host};dbname={$this->dbname}", $this->user, $this->password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'ok' => false,
                'error' => 'Connection failed: ' . $e->getMessage()
            ]);
            exit;
        }
        return $this->connection;
    }
}
