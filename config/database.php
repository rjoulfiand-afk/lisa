<?php
date_default_timezone_set('Asia/Jakarta');

define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'sasparkir'); 
define('DB_USER', 'root');
define('DB_PASS', '');

class Database {
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct() {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die('<div style="font-family:sans-serif;color:#c0392b;padding:20px;background:#fde8e8;border:1px solid #f8b4b4;border-radius:6px;margin:20px;">
                <strong>Koneksi Database Gagal:</strong><br>' . htmlspecialchars($e->getMessage()) .
                '<br><br><small>Pastikan MySQL XAMPP sudah berjalan dan database <strong>sasparkir</strong> sudah dibuat di phpMyAdmin.</small>
            </div>');
        }
    }

    public static function getInstance(): Database {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getPdo(): PDO {
        return $this->pdo;
    }
}

function checkAuth(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: ' . BASE_URL . 'index.php?page=login');
        exit;
    }
}

function redirect(string $url): void {
    header('Location: ' . $url);
    exit;
}

define('BASE_URL', '/joki/Parkir/');