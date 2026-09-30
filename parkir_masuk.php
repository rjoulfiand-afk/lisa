<?php
require_once 'config/database.php';
checkAuth();

$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_parkir'])) {
    $plat_nomor = strtoupper(trim($_POST['plat_nomor']));
    $jenis_kendaraan = $_POST['jenis_kendaraan'];
    $waktu_masuk = date('Y-m-d H:i:s');

    $check = $pdo->prepare("SELECT id FROM parkir WHERE plat_nomor = ? AND status = 'masuk'");
    $check->execute([$plat_nomor]);
    
    if ($check->rowCount() > 0) {
        $msg = '<div class="alert alert-danger">Kendaraan dengan plat nomor tersebut masih terparkir!</div>';
    } else {
        $stmt = $pdo->prepare("INSERT INTO parkir (plat_nomor, jenis_kendaraan, waktu_masuk, status) VALUES (?, ?, ?, 'masuk')");
        $stmt->execute([$plat_nomor, $jenis_kendaraan, $waktu_masuk]);
        $msg = '<div class="alert alert-success">Data kendaraan berhasil dicatat!</div>';
    }
}

$kendaraan = $pdo->query("SELECT * FROM parkir WHERE status = 'masuk' ORDER BY waktu_masuk DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Parkir Masuk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="dashboard.php">Sistem Parkir Admin</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="dashboard.php">Dashboard</a>
                <a class="nav-link active" href="parkir_masuk.php">Parkir Masuk</a>
                <a class="nav-link" href="parkir_keluar.php">Parkir Keluar</a>
                <a class="nav-link" href="laporan.php">Laporan</a>
                <a class="nav-link text-danger ms-3" href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container my-4">
        <h4 class="mb-3">Input Kendaraan Masuk</h4>
        <?= $msg ?>
        
        <div class="card p-3 mb-4 shadow-sm">
            <form method="POST" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Nomor Plat Kendaraan</label>
                    <input type="text" name="plat_nomor" class="form-control" placeholder="Contoh: B 1234 ABC" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Jenis Kendaraan</label>
                    <select name="jenis_kendaraan" class="form-select" required>
                        <option value="roda_2">Roda 2 (Motor)</option>
                        <option value="roda_4">Roda 4 (Mobil)</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <button type="submit" name="tambah_parkir" class="btn btn-primary w-100">Simpan Kendaraan Masuk</button>
                </div>
            </form>
        </div>

        <h5 class="mb-3">Daftar Kendaraan Terparkir</h5>
        <div class="table-responsive bg-white shadow-sm p-3 rounded">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Plat Nomor</th>
                        <th>Jenis Kendaraan</th>
                        <th>Waktu Masuk</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($kendaraan)): ?>
                        <tr><td colspan="5" class="text-center text-muted">Belum ada kendaraan di area parkir.</td></tr>
                    <?php else: foreach ($kendaraan as $i => $k): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><strong><?= htmlspecialchars($k['plat_nomor']) ?></strong></td>
                            <td><?= $k['jenis_kendaraan'] === 'roda_2' ? 'Roda 2' : 'Roda 4' ?></td>
                            <td><?= $k['waktu_masuk'] ?></td>
                            <td><span class="badge bg-warning text-dark">Terparkir</span></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>