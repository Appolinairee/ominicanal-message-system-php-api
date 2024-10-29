<?php

require_once __DIR__ . '/../models/User.php';

class AuthMiddleware
{
    private $db;

    public function __construct()
    {
        $config = require './config.php';
        $database = new Database($config['db']);
        $this->db = $database->connect();
    }

    public function handle($request)
    {
        if (!isset($request['HTTP_AUTHORIZATION'])) {
            http_response_code(401);
            echo json_encode([
                'ok' => false,
                'error' => 'Authorization header missing'
            ]);
            exit;
        }

        list($username, $password) = $this->getCredentials($request['HTTP_AUTHORIZATION']);

        $userModel = new User($this->db);

        if (!$userModel->isAuthenticated($username, $password)) {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
            exit;
        }
    }

    private function getCredentials($authHeader)
    {
        if (preg_match('/Basic (.+)/', $authHeader, $matches)) {
            $credentials = base64_decode($matches[1], true);

            if ($credentials === false) {
                return [null, null];
            }

            return explode(':', $credentials);
        }

        return [null, null];
    }
}
