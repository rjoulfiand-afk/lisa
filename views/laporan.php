<?php
/**
 * View: Laporan Transaksi Parkir
 * Clean, Connected & Accurate
 */
if (session_status() === PHP_SESSION_NONE) session_start();
$baseUrl = defined('BASE_URL') ? BASE_URL : '';

if (!function_exists('tgl_indo')) {
    function tgl_indo(string $t): string {
        $bln = [1=>'Januari','Februari','Maret','April','Mei','Juni',
                'Juli','Agustus','September','Oktober','November','Desember'];
        $ts  = strtotime($t);
        return date('j', $ts) . ' ' . $bln[(int)date('n', $ts)] . ' ' . date('Y', $ts);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Transaksi — SiParkir</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background-color: #faf6f8;
            color: #334155;
            font-family: system-ui, -apple-system, sans-serif;
        }

        .btn-pink {
            background-color: #ec4899;
            color: #ffffff;
            border: none;
        }
        .btn-pink:hover {
            background-color: #db2777;
            color: #ffffff;
        }
    </style>
</head>
<body>

    <!-- Include Navbar -->
    <?php require_once __DIR__ . '/layout/navbar.php'; ?>

    <main class="container py-4">

        <!-- Baris Header Halaman & Tombol Ekspor -->
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <h5 class="fw-bold mb-1 text-dark">
                    <i class="fa-solid fa-file-lines me-2 text-secondary"></i>Laporan Transaksi Parkir
                </h5>
                <span class="text-muted small">Periode: <strong><?= tgl_indo($tanggal) ?></strong></span>
            </div>

            <!-- Tombol Cetak PDF & Excel -->
            <div class="d-flex align-items-center gap-2">
                <a href="<?= $baseUrl ?>index.php?page=cetak_pdf&tanggal=<?= urlencode($tanggal) ?>"
                   target="_blank"
                   class="btn btn-sm btn-outline-danger fw-semibold">
                    <i class="fa-solid fa-print me-1"></i> Cetak PDF
                </a>
                <a href="<?= $baseUrl ?>index.php?page=export_csv&tanggal=<?= urlencode($tanggal) ?>"
                   class="btn btn-sm btn-outline-success fw-semibold">
                    <i class="fa-solid fa-file-excel me-1"></i> Ekspor Excel
                </a>
            </div>
        </div>

        <!-- 4 Kartu Ringkasan Metrik -->
        <div class="row g-3 mb-4">
            <!-- 1. Total Pendapatan -->
            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="text-muted small fw-semibold">Total Pendapatan</div>
                        <div class="fs-4 fw-bold text-success mt-1">
                            Rp <?= number_format($totalPendapatan, 0, ',', '.') ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Total Transaksi -->
            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="text-muted small fw-semibold">Total Transaksi</div>
                        <div class="fs-4 fw-bold text-dark mt-1">
                            <?= count($dataLaporan) ?> <small class="fs-6 text-muted fw-normal">unit</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Selesai (Keluar) -->
            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="text-muted small fw-semibold">Sudah Keluar</div>
                        <div class="fs-4 fw-bold text-primary mt-1">
                            <?= (int)$totalSelesai ?> <small class="fs-6 text-muted fw-normal">unit</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Masih Parkir -->
            <div class="col-sm-6 col-lg-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-3">
                        <div class="text-muted small fw-semibold">Masih Parkir</div>
                        <div class="fs-4 fw-bold text-warning mt-1">
                            <?= (int)$totalMasih ?> <small class="fs-6 text-muted fw-normal">unit</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kartu Tabel Utama & Filter Tanggal -->
        <div class="card border-0 shadow-sm">
            
            <!-- Toolbar Filter Tanggal -->
            <div class="card-header bg-white py-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                <form action="<?= $baseUrl ?>index.php" method="GET" class="d-flex align-items-center gap-2 flex-wrap m-0">
                    <input type="hidden" name="page" value="laporan">
                    <span class="small fw-semibold text-muted">Pilih Tanggal:</span>
                    <input type="date"
                           name="tanggal"
                           class="form-control form-control-sm"
                           style="width: 160px;"
                           value="<?= htmlspecialchars($tanggal) ?>"
                           required>
                    <button type="submit" class="btn btn-sm btn-pink fw-semibold">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Tampilkan
                    </button>
                    <a href="<?= $baseUrl ?>index.php?page=laporan&tanggal=<?= date('Y-m-d') ?>"
                       class="btn btn-sm btn-outline-secondary">
                        Hari Ini
                    </a>
                </form>

                <span class="small text-muted">
                    Menampilkan <strong><?= count($dataLaporan) ?></strong> catatan
                </span>
            </div>

            <!-- Tabel Data Transaksi -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light text-center">
                        <tr>
                            <th style="width: 50px;">No</th>
                            <th>No. Plat</th>
                            <th>Jenis</th>
                            <th>Waktu Masuk</th>
                            <th>Waktu Keluar</th>
                            <th>Durasi</th>
                            <th class="text-end">Biaya Parkir</th>
                            <th>Status</th>
                            <th style="width: 60px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        <?php if (empty($dataLaporan)): ?>
                            <tr>
                                <td colspan="9" class="text-muted py-4">
                                    <i class="fa-solid fa-file-circle-xmark fa-2x mb-2 text-secondary opacity-50 d-block"></i>
                                    Tidak ada transaksi parkir pada tanggal ini.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($dataLaporan as $i => $row): 
                                $isR2 = ($row['jenis_kendaraan'] === 'roda2');
                            ?>
                            <tr>
                                <td class="text-muted"><?= $i + 1 ?></td>
                                <td class="fw-bold text-dark">
                                    <?= htmlspecialchars($row['nomor_plat']) ?>
                                </td>
                                <td>
                                    <?php if ($isR2): ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Roda 2</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Roda 4</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= date('d/m/Y H:i', strtotime($row['waktu_masuk'])) ?>
                                </td>
                                <td>
                                    <?= !empty($row['waktu_keluar']) ? date('d/m/Y H:i', strtotime($row['waktu_keluar'])) : '<span class="text-muted">—</span>' ?>
                                </td>
                                <td>
                                    <?= !empty($row['durasi_jam']) ? '<strong>' . $row['durasi_jam'] . '</strong> jam' : '<span class="text-muted">—</span>' ?>
                                </td>
                                <td class="text-end">
                                    <?php if ($row['status'] === 'selesai' && $row['total_bayar'] > 0): ?>
                                        <strong class="text-success">Rp <?= number_format($row['total_bayar'], 0, ',', '.') ?></strong>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($row['status'] === 'parkir'): ?>
                                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">Parkir</span>
                                    <?php else: ?>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">Selesai</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        style="padding: 2px 8px;"
                                        onclick="konfirmasiHapus(<?= $row['id'] ?>, '<?= htmlspecialchars(addslashes($row['nomor_plat'])) ?>', '<?= htmlspecialchars($tanggal) ?>')"
                                        title="Hapus Data">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>

                    <!-- Total Pendapatan Footer Tabel -->
                    <?php if (!empty($dataLaporan)): ?>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="6" class="text-end fw-bold">TOTAL PENDAPATAN :</td>
                            <td class="text-end fw-bold text-success fs-6">
                                Rp <?= number_format($totalPendapatan, 0, ',', '.') ?>
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>

        </div>

    </main>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert Notifikasi & Konfirmasi Hapus -->
    <script>
    <?php if (isset($_GET['status']) && $_GET['status'] === 'hapus_sukses'): ?>
    Swal.fire({
        icon: 'success',
        title: 'Data Dihapus',
        text: 'Data parkir berhasil dihapus dari sistem.',
        timer: 2000,
        showConfirmButton: false
    });
    <?php endif; ?>

    function konfirmasiHapus(id, plat, tanggal) {
        Swal.fire({
            title: 'Hapus data parkir?',
            html: 'Data kendaraan plat <strong>' + plat + '</strong> akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then(function(result) {
            if (result.isConfirmed) {
                window.location.href = '<?= $baseUrl ?>index.php?page=hapus&id=' + id
                    + '&from=laporan&tanggal=' + encodeURIComponent(tanggal);
            }
        });
    }
    </script>
</body>
</html>