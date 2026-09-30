<?php
session_start();

// Gunakan 127.0.0.1 alih-alih localhost untuk memastikan koneksi TCP/IP pada port custom berjalan lancar
$host = '127.0.0.1';
$db   = 'db_parkir';
$user = 'root';
$port = '3307';
$pass = '';

try {
    // Tambahkan parameter port=$port ke dalam string koneksi PDO di bawah ini
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Koneksi Database Gagal: " . $e->getMessage());
}

function checkAuth() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header("Location: login.php");
        exit;
    }
}
?>