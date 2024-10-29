<?php

class User
{
    private $db;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    public function create($username, $hashedPassword, $userType = 'client')
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO users (name, password, user_type) VALUES (:name, :password, :user_type)");
            $stmt->bindParam(':name', $username);
            $stmt->bindParam(':password', $hashedPassword);

            $stmt->bindParam(':user_type', $userType);

            $stmt->execute();
            return ['ok' => true, 'message' => 'User created successfully.'];
        } catch (PDOException $e) {
            return [
                'ok' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ];
        }
    }

    public function findByUsername($username)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE name = :name LIMIT 1");
            $stmt->bindParam(':name', $username);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [
                'ok' => false,
                'error' => 'Database error: ' . $e->getMessage()
            ];
        }
    }


    public function exists($username)
    {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE name = :name");
            $stmt->bindParam(':name', $username);
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function isAuthenticated($username, $password)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE name = :username");

        $stmt->bindParam(':username', $username);

        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            return true;
        }

        return false;
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
