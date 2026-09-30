<?php
require_once 'config/database.php';
checkAuth();

$today = date('Y-m-d');

$stmt1 = $pdo->prepare("SELECT COUNT(*) FROM parkir WHERE DATE(waktu_masuk) = ?");
$stmt1->execute([$today]);
$total_masuk = $stmt1->fetchColumn();

$stmt2 = $pdo->query("SELECT COUNT(*) FROM parkir WHERE status = 'masuk'");
$total_aktif = $stmt2->fetchColumn();

$stmt3 = $pdo->prepare("SELECT SUM(total_biaya) FROM parkir WHERE DATE(waktu_keluar) = ? AND status = 'keluar'");
$stmt3->execute([$today]);
$total_pendapatan = $stmt3->fetchColumn() ?: 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin Parkir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">Sistem Parkir Admin</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link active" href="dashboard.php">Dashboard</a>
                <a class="nav-link" href="parkir_masuk.php">Parkir Masuk</a>
                <a class="nav-link" href="parkir_keluar.php">Parkir Keluar</a>
                <a class="nav-link" href="laporan.php">Laporan</a>
                <a class="nav-link text-danger ms-3" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <h3 class="mb-4">Selamat Datang, <?= htmlspecialchars($_SESSION['admin_nama']) ?></h3>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white p-3 shadow-sm">
                    <h5>Kendaraan Masuk Hari Ini</h5>
                    <h2><?= $total_masuk ?> unit</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-warning text-dark p-3 shadow-sm">
                    <h5>Kendaraan Masih Terparkir</h5>
                    <h2><?= $total_aktif ?> unit</h2>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white p-3 shadow-sm">
                    <h5>Pendapatan Hari Ini</h5>
                    <h2>Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></h2>
                </div>
            </div>
        </div>
    </div>
</body>
</html>