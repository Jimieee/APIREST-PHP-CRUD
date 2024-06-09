<?php

namespace App\Models;

use App\Config\Database;
use PDO;

class User
{
    private $pdo;

    public function __construct()
    {
        $db = new Database();
        $this->pdo = $db->connect();
    }

    public function create($username, $email, $password) {
        $sql = "INSERT INTO users (username, email, password, role_id) VALUES (:username, :email, :password, :role_id)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_BCRYPT),
            'role_id' => 1
        ]);
    }

    public function findByEmail($email)
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['email' => $email]);
        return $stmt->fetch();
    }

    public function findByUsername($username)
    {
        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['username' => $username]);
        return $stmt->fetch();
    }

    public function getAllUsers()
    {
        $sql = "SELECT * FROM users";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll();
    }   

    public function read($id)
    {
        $sql = "SELECT * FROM users WHERE user_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    public function update($id, $data)
    {
        $sql = "UPDATE users SET username = :username, email = :email, password = :password WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_BCRYPT),
            'id' => $id
        ]);
        return $stmt->rowCount();
    }

    public function delete($id)
    {
        $sql = "DELETE FROM users WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->rowCount();
    }
}
