<?php
/**
 * View: Parkir Keluar
 * Simple, Clean Centered Terminal (Consistent with Parkir Masuk)
 */
if (session_status() === PHP_SESSION_NONE) session_start();
$baseUrl  = defined('BASE_URL') ? BASE_URL : '';
$sekarang = date('Y-m-d H:i:s');

$status = $_GET['status'] ?? '';
$bayar  = (int)($_GET['bayar'] ?? 0);
$plat   = htmlspecialchars(addslashes($_GET['plat'] ?? ''));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parkir Keluar — SiParkir</title>
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

        /* Tombol Pink */
        .btn-pink {
            background-color: #ec4899;
            color: #ffffff;
            border: none;
        }
        .btn-pink:hover {
            background-color: #db2777;
            color: #ffffff;
        }

        /* Kotak Total Pembayaran */
        .box-total {
            background-color: #fdf2f8;
            border: 1px solid #fce7f3;
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
        }
    </style>
</head>
<body>

    <!-- Include Navbar -->
    <?php require_once __DIR__ . '/layout/navbar.php'; ?>

    <main class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-6">

                <!-- Kartu Terpusat Parkir Keluar -->
                <div class="card border-0 shadow-sm">
                    
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="mb-0 fw-bold text-dark">
                            <i class="fa-solid fa-arrow-right-from-bracket me-2 text-secondary"></i>Parkir Keluar & Pembayaran
                        </h6>
                    </div>

                    <div class="card-body p-4">

                        <!-- Form Pencarian Plat Nomor -->
                        <form action="<?= $baseUrl ?>index.php" method="GET" class="mb-3">
                            <input type="hidden" name="page" value="parkir_keluar">
                            <label for="cari_plat" class="form-label small fw-semibold text-muted">Nomor Plat Kendaraan</label>
                            
                            <div class="input-group">
                                <input
                                    type="text"
                                    id="cari_plat"
                                    name="cari_plat"
                                    class="form-control fw-bold"
                                    placeholder="Contoh: L 1234 AB"
                                    value="<?= htmlspecialchars($cariPlat ?? '') ?>"
                                    oninput="this.value = this.value.toUpperCase()"
                                    autocomplete="off"
                                    required
                                    autofocus
                                >
                                <button type="submit" class="btn btn-pink px-4 fw-semibold">
                                    <i class="fa-solid fa-magnifying-glass me-1"></i> Cari
                                </button>
                            </div>
                        </form>

                        <!-- Area Hasil Pencarian & Rincian Pembayaran -->
                        <?php if (!empty($cariPlat)): ?>

                            <?php if ($kendaraan && $estimasi): ?>
                                <?php
                                $isR2 = in_array(strtolower(str_replace(' ', '', $kendaraan['jenis_kendaraan'])), ['roda2', 'motor']);
                                $tarifAwal     = $isR2 ? 2000 : 5000;
                                $durasiJam     = (int)$estimasi['durasi_jam'];
                                $jamTambahan   = max(0, $durasiJam - 2);
                                $biayaTambahan = $jamTambahan * 1000;
                                ?>

                                <hr class="my-3 text-muted">

                                <!-- Rincian Kendaraan -->
                                <table class="table table-sm table-borderless small mb-3">
                                    <tbody>
                                        <tr>
                                            <td class="text-muted" style="width: 40%;">No. Tiket</td>
                                            <td class="fw-semibold font-monospace"><?= htmlspecialchars($kendaraan['nomor_id'] ?? '-') ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Nomor Plat</td>
                                            <td class="fw-bold fs-6"><?= htmlspecialchars($kendaraan['nomor_plat']) ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Jenis Kendaraan</td>
                                            <td>
                                                <?php if ($isR2): ?>
                                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Roda 2 (Motor)</span>
                                                <?php else: ?>
                                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Roda 4 (Mobil)</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Waktu Masuk</td>
                                            <td><?= date('d/m/Y H:i', strtotime($kendaraan['waktu_masuk'])) ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Waktu Keluar</td>
                                            <td><?= date('d/m/Y H:i', strtotime($sekarang)) ?></td>
                                        </tr>
                                        <tr>
                                            <td class="text-muted">Durasi Parkir</td>
                                            <td class="fw-bold"><?= $durasiJam ?> Jam</td>
                                        </tr>
                                    </tbody>
                                </table>

                                <!-- Kotak Total Biaya -->
                                <div class="box-total mb-3">
                                    <div class="small text-muted mb-1">Total Biaya Parkir</div>
                                    <div class="fs-3 fw-bold" style="color: #db2777;">
                                        Rp <?= number_format($estimasi['total_bayar'], 0, ',', '.') ?>
                                    </div>
                                    <div class="small text-muted mt-1">
                                        Rp <?= number_format($tarifAwal, 0, ',', '.') ?> (≤ 2 jam)
                                        <?php if ($jamTambahan > 0): ?>
                                            + Rp <?= number_format($biayaTambahan, 0, ',', '.') ?> (<?= $jamTambahan ?> jam tambahan)
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Form Konfirmasi Bayar & Keluar -->
                                <form action="<?= $baseUrl ?>index.php?page=parkir_keluar" method="POST">
                                    <input type="hidden" name="id" value="<?= $kendaraan['id'] ?>">
                                    <button type="submit" class="btn btn-pink w-100 py-2 fw-semibold">
                                        <i class="fa-solid fa-circle-check me-1"></i> Proses Pembayaran & Selesai
                                    </button>
                                </form>

                            <?php else: ?>
                                <!-- Alert Kendaraan Tidak Ditemukan -->
                                <div class="alert alert-warning border-0 small mt-3 mb-0">
                                    <i class="fa-solid fa-circle-exclamation me-1"></i>
                                    Kendaraan dengan plat <strong><?= htmlspecialchars(strtoupper($cariPlat)) ?></strong> tidak ditemukan di daftar parkir aktif.
                                </div>
                            <?php endif; ?>

                        <?php endif; ?>

                    </div>
                </div>

            </div>
        </div>
    </main>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert Notifikasi -->
    <script>
    <?php if ($status === 'sukses_keluar' && $bayar > 0): ?>
    Swal.fire({
        icon: 'success',
        title: 'Pembayaran Berhasil',
        html: 'Kendaraan plat <strong><?= $plat ?></strong> telah keluar.<br><div class="fs-4 fw-bold mt-2" style="color: #db2777;">Rp <?= number_format($bayar, 0, ',', '.') ?></div>',
        confirmButtonColor: '#ec4899',
        confirmButtonText: 'Selesai'
    });
    <?php elseif ($status === 'error'): ?>
    Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: 'Terjadi kesalahan saat memproses keluar.',
        confirmButtonColor: '#ec4899'
    });
    <?php endif; ?>
    </script>
</body>
</html>