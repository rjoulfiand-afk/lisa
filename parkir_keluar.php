<?php
require_once 'config/database.php';
checkAuth();

$target = null;
$msg = '';

if (isset($_GET['search'])) {
    $plat = strtoupper(trim($_GET['plat_nomor']));
    $stmt = $pdo->prepare("SELECT * FROM parkir WHERE plat_nomor = ? AND status = 'masuk'");
    $stmt->execute([$plat]);
    $target = $stmt->fetch();

    if (!$target) {
        $msg = '<div class="alert alert-warning">Kendaraan dengan plat tersebut tidak ditemukan atau sudah keluar!</div>';
    }
}

function hitungBiaya($jenis, $waktu_masuk, $waktu_keluar) {
    $start = new DateTime($waktu_masuk);
    $end = new DateTime($waktu_keluar);
    $diff = $start->diff($end);

    $durasi_jam = ($diff->days * 24) + $diff->h;
    if ($diff->i > 0 || $diff->s > 0) {
        $durasi_jam += 1;
    }
    if ($durasi_jam == 0) $durasi_jam = 1;

    $tarif_awal = ($jenis === 'roda_2') ? 2000 : 5000;
    
    if ($durasi_jam <= 2) {
        $total = $tarif_awal;
    } else {
        $total = $tarif_awal + (($durasi_jam - 2) * 1000);
    }

    return ['durasi' => $durasi_jam, 'biaya' => $total];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proses_keluar'])) {
    $id = $_POST['id'];
    $waktu_keluar = date('Y-m-d H:i:s');
    
    $stmt = $pdo->prepare("SELECT * FROM parkir WHERE id = ?");
    $stmt->execute([$id]);
    $data = $stmt->fetch();

    if ($data) {
        $kalkulasi = hitungBiaya($data['jenis_kendaraan'], $data['waktu_masuk'], $waktu_keluar);
        
        $update = $pdo->prepare("UPDATE parkir SET waktu_keluar = ?, durasi_jam = ?, total_biaya = ?, status = 'keluar' WHERE id = ?");
        $update->execute([$waktu_keluar, $kalkulasi['durasi'], $kalkulasi['biaya'], $id]);
        
        $msg = '<div class="alert alert-success">Pembayaran berhasil! Kendaraan telah resmi keluar. Total Biaya: <strong>Rp ' . number_format($kalkulasi['biaya'], 0, ',', '.') . '</strong></div>';
        $target = null;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Parkir Keluar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">Sistem Parkir Admin</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="dashboard.php">Dashboard</a>
                <a class="nav-link" href="parkir_masuk.php">Parkir Masuk</a>
                <a class="nav-link active" href="parkir_keluar.php">Parkir Keluar</a>
                <a class="nav-link" href="laporan.php">Laporan</a>
                <a class="nav-link text-danger ms-3" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <h4 class="mb-3">Proses Kendaraan Keluar</h4>
        <?= $msg ?>

        <div class="card p-3 mb-4 shadow-sm">
            <form method="GET" class="row g-3">
                <div class="col-md-8">
                    <input type="text" name="plat_nomor" class="form-control" placeholder="Cari Plat Nomor (contoh: B 1234 ABC)" value="<?= isset($_GET['plat_nomor']) ? htmlspecialchars($_GET['plat_nomor']) : '' ?>" required>
                </div>
                <div class="col-md-4">
                    <button type="submit" name="search" class="btn btn-primary w-100">Cari Kendaraan</button>
                </div>
            </form>
        </div>

        <?php if ($target): 
            $sekarang = date('Y-m-d H:i:s');
            $estimasi = hitungBiaya($target['jenis_kendaraan'], $target['waktu_masuk'], $sekarang);
        ?>
            <div class="card p-4 shadow-sm border-primary">
                <h5 class="text-primary mb-3">Rincian Pembayaran Parkir</h5>
                <table class="table table-borderless">
                    <tr><th width="200">Plat Nomor</th><td>: <strong><?= htmlspecialchars($target['plat_nomor']) ?></strong></td></tr>
                    <tr><th>Jenis Kendaraan</th><td>: <?= $target['jenis_kendaraan'] === 'roda_2' ? 'Roda 2 (Motor)' : 'Roda 4 (Mobil)' ?></td></tr>
                    <tr><th>Waktu Masuk</th><td>: <?= $target['waktu_masuk'] ?></td></tr>
                    <tr><th>Waktu Keluar (Sekarang)</th><td>: <?= $sekarang ?></td></tr>
                    <tr><th>Durasi Parkir</th><td>: <?= $estimasi['durasi'] ?> Jam</td></tr>
                    <tr><th>Total Biaya</th><td>: <span class="fs-4 fw-bold text-success">Rp <?= number_format($estimasi['biaya'], 0, ',', '.') ?></span></td></tr>
                </table>
                <form method="POST">
                    <input type="hidden" name="id" value="<?= $target['id'] ?>">
                    <button type="submit" name="proses_keluar" class="btn btn-success btn-lg w-100 mt-2">Konfirmasi Keluar & Bayar</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>