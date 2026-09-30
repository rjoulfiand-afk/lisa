<?php
require_once 'config/database.php';
checkAuth();

$tgl_pilih = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

$stmt = $pdo->prepare("SELECT * FROM parkir WHERE DATE(waktu_keluar) = ? AND status = 'keluar' ORDER BY waktu_keluar DESC");
$stmt->execute([$tgl_pilih]);
$laporan = $stmt->fetchAll();

$total_pendapatan = array_sum(array_column($laporan, 'total_biaya'));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Harian Parkir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">Sistem Parkir Admin</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="dashboard.php">Dashboard</a>
                <a class="nav-link" href="parkir_masuk.php">Parkir Masuk</a>
                <a class="nav-link" href="parkir_keluar.php">Parkir Keluar</a>
                <a class="nav-link active" href="laporan.php">Laporan</a>
                <a class="nav-link text-danger ms-3" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <h4 class="mb-3">Laporan Transaksi Harian</h4>

        <div class="card p-3 mb-4 shadow-sm">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Pilih Tanggal Laporan</label>
                    <input type="date" name="tanggal" class="form-control" value="<?= $tgl_pilih ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
                </div>
            </form>
        </div>

        <div class="alert alert-info d-flex justify-content-between align-items-center">
            <span>Total Transaksi Selesai Tanggal <strong><?= date('d-m-Y', strtotime($tgl_pilih)) ?></strong>: <?= count($laporan) ?> kendaraan</span>
            <span class="fs-5">Total Pendapatan: <strong>Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></strong></span>
        </div>

        <div class="table-responsive bg-white shadow-sm p-3 rounded">
            <table class="table table-bordered align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Plat Nomor</th>
                        <th>Jenis</th>
                        <th>Waktu Masuk</th>
                        <th>Waktu Keluar</th>
                        <th>Durasi</th>
                        <th>Total Biaya</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($laporan)): ?>
                        <tr><td colspan="7" class="text-center text-muted">Tidak ada transaksi parkir selesai pada tanggal ini.</td></tr>
                    <?php else: foreach ($laporan as $i => $row): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><strong><?= htmlspecialchars($row['plat_nomor']) ?></strong></td>
                            <td><?= $row['jenis_kendaraan'] === 'roda_2' ? 'Roda 2' : 'Roda 4' ?></td>
                            <td><?= $row['waktu_masuk'] ?></td>
                            <td><?= $row['waktu_keluar'] ?></td>
                            <td><?= $row['durasi_jam'] ?> Jam</td>
                            <td>Rp <?= number_format($row['total_biaya'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>