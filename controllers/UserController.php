<?php

require_once __DIR__ . '/../models/Database.php';
require_once __DIR__ . '/../models/User.php';

class UserController
{
    private $db;

    public function __construct()
    {
        $config = require './config.php';
        $database = new Database($config['db']);
        $this->db = $database->connect();
    }

    public function login($username, $password)
    {
        header('Content-Type: application/json');

        if (empty($username) || strlen($username) < 3) {
            echo json_encode(['ok' => false, 'message' => 'Nom d\'utilisateur invalide.']);
            return;
        }

        if (empty($password) || strlen($password) < 6) {
            echo json_encode(['ok' => false, 'message' => 'Mot de passe invalide.']);
            return;
        }

        $userModel = new User($this->db);

        $user = $userModel->findByUsername($username);
        if (!$user) {
            echo json_encode(['ok' => false, 'message' => 'Nom d\'utilisateur ou mot de passe invalide.']);
            return;
        }

        if (!password_verify($password, $user['password'])) {
            echo json_encode(['ok' => false, 'message' => 'Nom d\'utilisateur ou mot de passe invalide.']);
            return;
        }

        $encodedCredentials = base64_encode("$username:$password");

        echo json_encode(['ok' => true, 'message' => 'Connexion réussie.', 'data' => [
            'user' => $user,
            'credentials' => $encodedCredentials
        ]]);
    }


    public function register($username, $password, $userType)
    {
        header('Content-Type: application/json');

        if (empty($username) || strlen($username) < 3) {
            echo json_encode(['ok' => false, 'message' => 'Nom d\'utilisateur invalide.']);
            return;
        }

        if (empty($password) || strlen($password) < 6) {
            echo json_encode(['ok' => false, 'message' => 'Mot de passe invalide.']);
            return;
        }

        if (!in_array($userType, ['client', 'agent'])) {
            echo json_encode(['ok' => false, 'message' => 'Type d\'utilisateur invalide.']);
            return;
        }

        $userModel = new User($this->db);
        if ($userModel->exists($username)) {
            echo json_encode(['ok' => false, 'message' => 'Ce nom d\'utilisateur est déjà pris.']);
            return;
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $result = $userModel->create($username, $hashedPassword, $userType);

        echo json_encode($result);
    }
}
