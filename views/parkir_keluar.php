<?php
/**
 * View: Parkir Keluar
 * Vars: $kendaraan, $estimasi, $cariPlat
 */
$activePage = 'parkir_keluar';
$sekarang   = date('Y-m-d H:i:s');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parkir Keluar — SiParkir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php require_once __DIR__ . '/layout/navbar.php'; ?>

<main class="container pb-5">

    <div class="page-head">
        <h1><i class="fa-solid fa-right-from-bracket text-rose-600"></i> Parkir Keluar</h1>
        <p>Cari kendaraan berdasarkan nomor plat, lalu proses pembayaran.</p>
    </div>

    <?php
    $status = $_GET['status'] ?? '';
    $bayar  = (int)($_GET['bayar'] ?? 0);
    $plat   = htmlspecialchars(addslashes($_GET['plat'] ?? ''));
    ?>

    <div class="row g-4">

        <!-- Kolom Pencarian -->
        <div class="col-lg-5">
            <div class="prk-card">
                <div class="prk-card-head">
                    <h2><i class="fa-solid fa-magnifying-glass"></i> Cari Kendaraan</h2>
                </div>
                <div class="prk-card-body">
                    <form action="<?= BASE_URL ?>index.php" method="GET">
                        <input type="hidden" name="page" value="parkir_keluar">

                        <label class="field-label" for="cari_plat">Nomor Plat Kendaraan</label>
                        <div class="d-flex gap-2">
                            <input type="text"
                                   id="cari_plat"
                                   name="cari_plat"
                                   class="form-control"
                                   style="text-transform:uppercase;font-weight:700;font-size:15px;letter-spacing:.05em;"
                                   placeholder="Contoh: L 1234 AB"
                                   value="<?= htmlspecialchars($cariPlat ?? '') ?>"
                                   oninput="this.value=this.value.toUpperCase()"
                                   autocomplete="off"
                                   required
                                   autofocus>
                            <button type="submit" class="btn-slate text-nowrap">
                                <i class="fa-solid fa-search me-1"></i> Cari
                            </button>
                        </div>
                        <div class="field-hint mt-2">
                            Ketikkan nomor plat kendaraan yang akan keluar.
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Kolom Hasil Rincian -->
        <div class="col-lg-7">
            <?php if (!empty($cariPlat)): ?>

                <?php if ($kendaraan && $estimasi): ?>
                <div class="prk-card">
                    <div class="prk-card-head">
                        <h2><i class="fa-solid fa-receipt"></i> Rincian Pembayaran</h2>
                        <span class="id-tag">Transaksi <?= htmlspecialchars($kendaraan['nomor_id']) ?></span>
                    </div>
                    <div class="prk-card-body">

                        <dl class="detail-list">
                            <div class="detail-row">
                                <dt class="detail-dt">Nomor Plat</dt>
                                <dd class="detail-dd">
                                    <span class="plate-badge"><?= htmlspecialchars($kendaraan['nomor_plat']) ?></span>
                                </dd>
                            </div>
                            <div class="detail-row">
                                <dt class="detail-dt">Jenis Kendaraan</dt>
                                <dd class="detail-dd">
                                    <?php if ($kendaraan['jenis_kendaraan'] === 'roda2'): ?>
                                    <span class="badge-roda2"><i class="fa-solid fa-motorcycle me-1"></i> Roda 2 (Motor)</span>
                                    <?php else: ?>
                                    <span class="badge-roda4"><i class="fa-solid fa-car-side me-1"></i> Roda 4 (Mobil)</span>
                                    <?php endif; ?>
                                </dd>
                            </div>
                            <div class="detail-row">
                                <dt class="detail-dt">Waktu Masuk</dt>
                                <dd class="detail-dd"><?= date('d/m/Y H:i', strtotime($kendaraan['waktu_masuk'])) ?> WIB</dd>
                            </div>
                            <div class="detail-row">
                                <dt class="detail-dt">Waktu Keluar (Sekarang)</dt>
                                <dd class="detail-dd"><?= date('d/m/Y H:i', strtotime($sekarang)) ?> WIB</dd>
                            </div>
                            <div class="detail-row">
                                <dt class="detail-dt">Durasi Parkir</dt>
                                <dd class="detail-dd"><?= $estimasi['durasi_jam'] ?> Jam</dd>
                            </div>
                        </dl>

                        <?php
                        $tarifAwal = $kendaraan['jenis_kendaraan'] === 'roda2' ? 2000 : 5000;
                        $jam = $estimasi['durasi_jam'];
                        ?>
                        <div style="background:var(--sl-50);border:1px solid var(--sl-200);border-radius:6px;padding:10px 14px;margin:14px 0;font-size:12.5px;color:var(--sl-600);">
                            <i class="fa-solid fa-calculator me-1 text-secondary"></i>
                            Rp <?= number_format($tarifAwal, 0, ',', '.') ?> (tarif awal ≤ 2 jam)
                            <?php if ($jam > 2): ?>
                            + <?= $jam - 2 ?> jam &times; Rp 1.000 = Rp <?= number_format(($jam - 2) * 1000, 0, ',', '.') ?>
                            <?php endif; ?>
                        </div>

                        <div class="total-box">
                            <span class="lbl">Total Biaya Parkir</span>
                            <strong class="val">Rp <?= number_format($estimasi['total_bayar'], 0, ',', '.') ?></strong>
                        </div>

                        <form action="<?= BASE_URL ?>index.php?page=parkir_keluar" method="POST">
                            <input type="hidden" name="id" value="<?= $kendaraan['id'] ?>">
                            <button type="submit" class="btn-em w-100 py-3 fs-6">
                                <i class="fa-solid fa-circle-check me-1"></i>
                                Terima Pembayaran &amp; Keluarkan Kendaraan
                            </button>
                        </form>
                    </div>
                </div>

                <?php else: ?>
                <div class="prk-notice">
                    <i class="fa-solid fa-circle-exclamation fs-5"></i>
                    <div>
                        Kendaraan dengan plat <strong><?= htmlspecialchars(strtoupper($cariPlat)) ?></strong>
                        tidak ditemukan di daftar parkir aktif.
                        Periksa kembali spasi atau penulisan nomor plat, lalu coba lagi.
                    </div>
                </div>
                <?php endif; ?>

            <?php else: ?>
            <div class="prk-card">
                <div class="prk-card-body">
                    <div class="empty-state">
                        <i class="fa-solid fa-receipt"></i>
                        <p>Masukkan nomor plat kendaraan di sebelah kiri untuk melihat durasi dan total biaya parkir.</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

    </div>

    <div class="page-footer">SiParkir &copy; <?= date('Y') ?></div>
</main>

<script>
<?php if ($status === 'sukses_keluar' && $bayar > 0): ?>
Swal.fire({
    icon: 'success',
    title: 'Pembayaran Selesai!',
    html: 'Kendaraan plat <strong><?= $plat ?></strong> telah resmi keluar.<br><div class="mt-2 fs-5 fw-bold text-success">Total Bayar: Rp <?= number_format($bayar, 0, ',', '.') ?></div>',
    confirmButtonColor: '#059669',
    confirmButtonText: 'Selesai'
});
<?php elseif ($status === 'error'): ?>
Swal.fire({
    icon: 'error',
    title: 'Gagal',
    text: 'Terjadi kesalahan saat memproses keluar. Silakan coba lagi.',
    confirmButtonColor: '#e11d48'
});
<?php endif; ?>
</script>
</body>
</html>
