<?php
$activePage = 'parkir_masuk';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parkir Masuk — SiParkir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php require_once __DIR__ . '/layout/navbar.php'; ?>

<main class="container pb-5">

    <div class="page-head">
        <h1><i class="fa-solid fa-right-to-bracket text-rose-600"></i> Parkir Masuk</h1>
        <p>Catat kendaraan yang masuk ke area parkir.</p>
    </div>

    <div class="row g-4 mb-4">

        <!-- Kolom Form Input -->
        <div class="col-lg-7">
            <div class="prk-card h-100 mb-0">
                <div class="prk-card-head">
                    <h2><i class="fa-solid fa-pen-to-square"></i> Data Kendaraan</h2>
                </div>
                <div class="prk-card-body">
                    <form action="<?= BASE_URL ?>index.php?page=parkir_masuk" method="POST" novalidate>

                        <div class="mb-4">
                            <label class="field-label" for="nomor_plat">Nomor Plat</label>
                            <input
                                type="text"
                                id="nomor_plat"
                                name="nomor_plat"
                                class="form-control plate-input w-100"
                                placeholder="L 1234 AB"
                                oninput="this.value = this.value.toUpperCase()"
                                autocomplete="off"
                                required
                                autofocus
                                maxlength="15"
                            >
                            <div class="field-hint">
                                <i class="fa-solid fa-circle-info fa-xs text-secondary me-1"></i>
                                Gunakan spasi antara kode wilayah, nomor, dan seri. Otomatis huruf kapital.
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="field-label" id="lbl-jenis">Jenis Kendaraan</div>
                            <div class="row g-2" role="radiogroup" aria-labelledby="lbl-jenis">
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="jenis_kendaraan"
                                           id="roda2" value="roda2" checked required>
                                    <label class="vehicle-option" for="roda2">
                                        <i class="fa-solid fa-motorcycle"></i>
                                        <span>
                                            <strong>Roda 2</strong>
                                            <small>Sepeda motor</small>
                                        </span>
                                    </label>
                                </div>
                                <div class="col-6">
                                    <input type="radio" class="btn-check" name="jenis_kendaraan"
                                           id="roda4" value="roda4">
                                    <label class="vehicle-option" for="roda4">
                                        <i class="fa-solid fa-car-side"></i>
                                        <span>
                                            <strong>Roda 4</strong>
                                            <small>Mobil</small>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn-rose w-100">
                            <i class="fa-solid fa-check me-1"></i>
                            Simpan Transaksi Masuk
                        </button>

                    </form>
                </div>
            </div>
        </div>

        <!-- Kolom Informasi Tarif -->
        <div class="col-lg-5">
            <div class="prk-card h-100 mb-0">
                <div class="prk-card-head">
                    <h2><i class="fa-solid fa-receipt"></i> Tarif Parkir</h2>
                </div>
                <div class="prk-card-body">
                    <table class="rate-table">
                        <thead>
                            <tr>
                                <th>Jenis</th>
                                <th>Tarif Awal (≤ 2 jam)</th>
                                <th>Per Jam Berikutnya</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <i class="fa-solid fa-motorcycle me-1" style="color:var(--rose-600);"></i>
                                    Roda 2
                                </td>
                                <td>Rp 2.000</td>
                                <td>+ Rp 1.000</td>
                            </tr>
                            <tr>
                                <td>
                                    <i class="fa-solid fa-car-side me-1" style="color:#2563eb;"></i>
                                    Roda 4
                                </td>
                                <td>Rp 5.000</td>
                                <td>+ Rp 1.000</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="mt-4 p-3 rounded" style="background:var(--sl-50);border:1px solid var(--sl-200);font-size:12.5px;color:var(--sl-500);">
                        <i class="fa-solid fa-circle-question me-1 text-secondary"></i>
                        Durasi parkir dihitung otomatis berdasarkan jam masuk dan jam keluar saat pembayaran di kasir.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Kendaraan Terparkir -->
    <div class="prk-card">
        <div class="prk-card-head">
            <h2>
                <i class="fa-solid fa-list"></i> Daftar Kendaraan Terparkir
            </h2>
            <span style="background:#fffbeb;color:#92400e;border:1px solid #fde68a;border-radius:20px;font-size:12px;font-weight:600;padding:3px 12px;">
                <?= count($data) ?> unit aktif
            </span>
        </div>

        <div class="prk-table-wrap">
            <table class="prk-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width:5%">No</th>
                        <th style="width:20%">No. Plat</th>
                        <th style="width:20%">No. Transaksi</th>
                        <th style="width:15%">Jenis</th>
                        <th class="text-center" style="width:22%">Waktu Masuk</th>
                        <th class="text-center" style="width:18%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data)): ?>
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fa-solid fa-square-parking"></i>
                                <p>Belum ada kendaraan yang terparkir saat ini.</p>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($data as $i => $k): ?>
                    <tr>
                        <td class="text-center" style="color:#94a3b8;"><?= $i + 1 ?></td>
                        <td>
                            <span class="plate-badge">
                                <?= htmlspecialchars($k['nomor_plat']) ?>
                            </span>
                        </td>
                        <td>
                            <span class="id-tag"><?= htmlspecialchars($k['nomor_id']) ?></span>
                        </td>
                        <td>
                            <?php if ($k['jenis_kendaraan'] === 'roda2'): ?>
                            <span class="badge-roda2"><i class="fa-solid fa-motorcycle me-1"></i> Roda 2</span>
                            <?php else: ?>
                            <span class="badge-roda4"><i class="fa-solid fa-car-side me-1"></i> Roda 4</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center" style="font-size:13px;">
                            <?= date('d/m/Y H:i', strtotime($k['waktu_masuk'])) ?> WIB
                        </td>
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="<?= BASE_URL ?>index.php?page=parkir_keluar&cari_plat=<?= urlencode($k['nomor_plat']) ?>"
                                   class="btn-sm-ghost-em">
                                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Keluarkan
                                </a>
                                <button type="button"
                                    class="btn-danger-ghost"
                                    onclick="konfirmasiHapus(<?= $k['id'] ?>, '<?= htmlspecialchars(addslashes($k['nomor_plat'])) ?>')">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="page-footer">SiParkir &copy; <?= date('Y') ?></div>
</main>

<script>
<?php
$status = $_GET['status'] ?? '';
$plat   = htmlspecialchars(addslashes($_GET['plat'] ?? ''));
$msg    = htmlspecialchars(addslashes($_GET['msg'] ?? ''));
?>
<?php if ($status === 'sukses_masuk'): ?>
Swal.fire({
    icon: 'success',
    title: 'Berhasil Dicatat!',
    html: 'Kendaraan dengan plat <strong><?= $plat ?></strong> telah disimpan.',
    timer: 2500,
    showConfirmButton: false
});
<?php elseif ($status === 'hapus_sukses'): ?>
Swal.fire({
    icon: 'success',
    title: 'Data Dihapus',
    text: 'Data transaksi berhasil dihapus dari sistem.',
    timer: 2000,
    showConfirmButton: false
});
<?php elseif ($status === 'error'): ?>
Swal.fire({
    icon: 'error',
    title: 'Gagal',
    text: '<?= $msg ?>',
    confirmButtonColor: '#e11d48'
});
<?php endif; ?>

function konfirmasiHapus(id, plat) {
    Swal.fire({
        title: 'Hapus data parkir?',
        html: 'Plat <strong>' + plat + '</strong> akan dihapus permanen dari sistem.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e11d48',
        cancelButtonColor: '#64748b',
        confirmButtonText: '<i class="fa-solid fa-trash-can me-1"></i> Ya, Hapus',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then(function(result) {
        if (result.isConfirmed) {
            window.location.href = '<?= BASE_URL ?>index.php?page=hapus&id=' + id + '&from=parkir_masuk';
        }
    });
}
</script>
</body>
</html>
