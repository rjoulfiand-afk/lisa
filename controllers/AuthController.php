<?php
/**
 * Controller: AuthController
 * Menangani login dan logout admin
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/AdminModel.php';

class AuthController {
    private AdminModel $adminModel;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $this->adminModel = new AdminModel();
    }

    public function showLogin(): void {
        if (!empty($_SESSION['admin_logged_in'])) {
            redirect(BASE_URL . 'index.php?page=dashboard');
        }
        require_once __DIR__ . '/../views/login.php';
    }

    /**
     * Proses verifikasi login admin
     */
    public function prosesLogin(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(BASE_URL . 'index.php?page=login');
        }

        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($username) || empty($password)) {
            redirect(BASE_URL . 'index.php?page=login&status=error&msg=' . urlencode('Username dan password wajib diisi!'));
        }

        $admin = $this->adminModel->findByUsername($username);

        // Verifikasi password (Mendukung hash bcrypt & plain text fallback)
        $isPasswordValid = false;
        if ($admin) {
            if ($this->adminModel->verifyPassword($password, $admin['password'])) {
                $isPasswordValid = true;
            } elseif ($password === $admin['password']) {
                $isPasswordValid = true;
            }
        }

        if ($admin && $isPasswordValid) {
            session_regenerate_id(true);
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id']        = $admin['id'];
            $_SESSION['admin_nama']      = $admin['nama'];
            $_SESSION['admin_username']  = $admin['username'];

            redirect(BASE_URL . 'index.php?page=dashboard&status=welcome&nama=' . urlencode($admin['nama']));
        } else {
            redirect(BASE_URL . 'index.php?page=login&status=error&msg=' . urlencode('Username atau password salah. Silakan coba lagi.'));
        }
    }

    public function proseLogin(): void {
        $this->prosesLogin();
    }

        public function logout(): void {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
        redirect(BASE_URL . 'index.php?page=login&status=logout');
    }
}