<?php
/**
 * Master Router: index.php (Project Teman)
 * Pola MVC Front Controller yang Bersih & Anti Error Cookies
 */

if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/controllers/AuthController.php';

$page = $_GET['page'] ?? '';

// Jika belum login dan bukan halaman login/logout, paksa ke login
$authPages = ['login', 'logout'];
if (empty($_SESSION['admin_logged_in']) && !in_array($page, $authPages, true)) {
    $auth = new AuthController();
    $auth->showLogin();
    exit;
}

// Jika sudah login dan tidak ada query page, arahkan ke dashboard
if (empty($page)) {
    $page = 'dashboard';
}

// Router Switch (Lazy Loading Controller)
switch ($page) {
    case 'login':
        $auth = new AuthController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth->prosesLogin();
        } else {
            $auth->showLogin();
        }
        break;

    case 'logout':
        $auth = new AuthController();
        $auth->logout();
        break;

    case 'dashboard':
        require_once __DIR__ . '/controllers/ParkirController.php';
        $parkir = new ParkirController();
        $parkir->dashboard();
        break;

    case 'parkir_masuk':
        require_once __DIR__ . '/controllers/ParkirController.php';
        $parkir = new ParkirController();
        $parkir->parkirMasuk();
        break;

    case 'parkir_keluar':
        require_once __DIR__ . '/controllers/ParkirController.php';
        $parkir = new ParkirController();
        $parkir->parkirKeluar();
        break;

    case 'laporan':
        require_once __DIR__ . '/controllers/ParkirController.php';
        $parkir = new ParkirController();
        $parkir->laporan();
        break;

    case 'cetak_pdf':
        require_once __DIR__ . '/controllers/ParkirController.php';
        $parkir = new ParkirController();
        $parkir->cetak_pdf();
        break;

    case 'export_csv':
    case 'export_excel':
        require_once __DIR__ . '/controllers/ParkirController.php';
        $parkir = new ParkirController();
        $parkir->export_csv();
        break;

    case 'hapus':
        require_once __DIR__ . '/controllers/ParkirController.php';
        $parkir = new ParkirController();
        $parkir->hapus();
        break;

    default:
        require_once __DIR__ . '/controllers/ParkirController.php';
        $parkir = new ParkirController();
        $parkir->dashboard();
        break;
}