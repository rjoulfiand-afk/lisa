<?php

require_once __DIR__ . '/../config/database.php';

class AdminModel {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    public function findByUsername(string $username): ?array {
        $stmt = $this->pdo->prepare(
            "SELECT id, username, password, nama FROM admin WHERE username = ? LIMIT 1"
        );
        $stmt->execute([trim($username)]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function verifyPassword(string $inputPassword, string $hashedPassword): bool {
        // Cek jika password ter-hash bcrypt
        if (password_verify($inputPassword, $hashedPassword)) {
            return true;
        }

        return $inputPassword === $hashedPassword;
    }
}