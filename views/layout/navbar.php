<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$halamanAktif = $_GET['page'] ?? 'dashboard';
$namaPetugas  = $_SESSION['admin_nama'] ?? $_SESSION['nama_petugas'] ?? $_SESSION['admin_username'] ?? 'Petugas';
$inisialPetugas = strtoupper(substr($namaPetugas, 0, 1));

$menus = [
    'dashboard'     => ['label' => 'Dashboard',     'icon' => 'fa-table-columns'],
    'parkir_masuk'  => ['label' => 'Parkir Masuk',  'icon' => 'fa-arrow-right-to-bracket'],
    'parkir_keluar' => ['label' => 'Parkir Keluar', 'icon' => 'fa-arrow-right-from-bracket'],
    'laporan'       => ['label' => 'Laporan',       'icon' => 'fa-chart-line'],
];
?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* Reset & Font Dasar */
    body {
        font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
        background-color: #faf6f8; /* Putih dengan sentuhan pink soft yang sangat tenang */
        color: #334155;
    }

    /* Navbar Pink Manis & Elegan */
    .app-navbar {
        background: linear-gradient(135deg, #ec4899 0%, #f472b6 100%); /* 100% Asli Pink */
        box-shadow: 0 2px 10px rgba(236, 72, 153, 0.2);
        padding: 0.65rem 0;
        position: sticky;
        top: 0;
        z-index: 1020;
    }

    .app-navbar .navbar-brand {
        color: #ffffff;
        font-weight: 700;
        font-size: 1.15rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        letter-spacing: -0.3px;
    }

    .app-navbar .navbar-brand:hover {
        color: #ffffff;
    }

    .app-navbar .brand-icon {
        width: 32px;
        height: 32px;
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
    }

    /* Link Menu */
    .app-navbar .nav-link {
        color: rgba(255, 255, 255, 0.9) !important;
        font-weight: 500;
        font-size: 0.92rem;
        padding: 0.45rem 0.85rem !important;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        transition: all 0.2s ease;
    }

    .app-navbar .nav-link:hover {
        color: #ffffff !important;
        background-color: rgba(255, 255, 255, 0.18);
    }

    .app-navbar .nav-link.active {
        color: #db2777 !important;
        background-color: #ffffff !important;
        font-weight: 600;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }

    /* User Profile & Logout */
    .nav-user {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding-left: 1rem;
        border-left: 1px solid rgba(255, 255, 255, 0.25);
    }

    .nav-avatar {
        width: 32px;
        height: 32px;
        background: #ffffff;
        color: #ec4899;
        font-weight: 700;
        font-size: 0.85rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .nav-username {
        color: #ffffff;
        font-size: 0.88rem;
        font-weight: 600;
    }

    .btn-nav-logout {
        background: rgba(255, 255, 255, 0.2);
        color: #ffffff;
        border: 1px solid rgba(255, 255, 255, 0.35);
        font-size: 0.82rem;
        font-weight: 500;
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        text-decoration: none;
        transition: all 0.2s;
    }

    .btn-nav-logout:hover {
        background: #ffffff;
        color: #e11d48;
    }
</style>

<nav class="navbar navbar-expand-lg app-navbar">
    <div class="container-fluid px-4">
        <a class="navbar-brand" href="index.php?page=dashboard">
            <span class="brand-icon"><i class="fa-solid fa-square-parking"></i></span>
            <span>SiParkir</span>
        </a>

        <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <i class="fa-solid fa-bars"></i>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3 gap-1">
                <?php foreach ($menus as $key => $item): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($halamanAktif === $key) ? 'active' : '' ?>" href="index.php?page=<?= $key ?>">
                            <i class="fa-solid <?= $item['icon'] ?>"></i>
                            <?= $item['label'] ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="d-flex align-items-center">
                <div class="nav-user">
                    <div class="nav-avatar"><?= $inisialPetugas ?></div>
                    <span class="nav-username d-none d-sm-inline"><?= htmlspecialchars($namaPetugas) ?></span>
                    <a href="index.php?page=logout" class="btn-nav-logout ms-2" onclick="return confirm('Keluar dari aplikasi?')">
                        <i class="fa-solid fa-arrow-right-from-bracket me-1"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>