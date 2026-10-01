<?php
/**
 * View: Laporan
 * Vars: $tanggal, $dataLaporan, $totalPendapatan, $totalSelesai, $totalMasih
 */
$activePage = 'laporan';

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php require_once __DIR__ . '/layout/navbar.php'; ?>

<main class="container pb-5">

    <div class="page-head d-flex justify-content-between align-items-end flex-wrap gap-3">
        <div>
            <h1><i class="fa-solid fa-file-lines text-rose-600"></i> Laporan Harian Parkir</h1>
            <p>Periode tanggal: <strong><?= tgl_indo($tanggal) ?></strong></p>
        </div>
        <div class="export-row">
            <a href="<?= BASE_URL ?>index.php?page=cetak_pdf&tanggal=<?= urlencode($tanggal) ?>"
               target="_blank"
               class="btn-export">
                <i class="fa-solid fa-print text-danger"></i> Cetak / PDF
            </a>
            <a href="<?= BASE_URL ?>index.php?page=export_csv&tanggal=<?= urlencode($tanggal) ?>"
               class="btn-export">
                <i class="fa-solid fa-file-excel text-success"></i> Ekspor Excel
            </a>
        </div>
    </div>

    <!-- Card Panel Utama -->
    <div class="prk-card">

        <!-- Toolbar Filter -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 p-3 border-bottom">
            <form action="<?= BASE_URL ?>index.php" method="GET" class="d-flex align-items-center gap-2 flex-wrap m-0">
                <input type="hidden" name="page" value="laporan">
                <span class="small fw-semibold text-secondary">Tanggal:</span>
                <input type="date"
                       name="tanggal"
                       class="form-control form-control-sm"
                       style="width:160px;"
                       value="<?= htmlspecialchars($tanggal) ?>"
                       required>
                <button type="submit" class="btn-slate" style="padding:5px 14px;font-size:13px;">
                    <i class="fa-solid fa-search me-1"></i> Tampilkan
                </button>
                <a href="<?= BASE_URL ?>index.php?page=laporan&tanggal=<?= date('Y-m-d') ?>"
                   class="btn-ghost" style="padding:5px 14px;font-size:13px;">
                    Hari Ini
                </a>
            </form>
        </div>

        <!-- 4 Kotak Ringkasan Angka -->
        <div class="summary-grid">
            <div class="summary-item">
                <div class="lbl">Total Pendapatan</div>
                <div class="val is-green">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></div>
            </div>
            <div class="summary-item">
                <div class="lbl">Total Kendaraan</div>
                <div class="val"><?= count($dataLaporan) ?> <small class="fs-6 fw-normal text-muted">unit</small></div>
            </div>
            <div class="summary-item">
                <div class="lbl">Sudah Selesai</div>
                <div class="val"><?= $totalSelesai ?> <small class="fs-6 fw-normal text-muted">unit</small></div>
            </div>
            <div class="summary-item">
                <div class="lbl">Masih Parkir</div>
                <div class="val is-amber"><?= $totalMasih ?> <small class="fs-6 fw-normal text-muted">unit</small></div>
            </div>
        </div>

        <!-- Tabel Data Laporan -->
        <div class="prk-table-wrap">
            <table class="prk-table">
                <thead>
                    <tr>
                        <th class="text-center" style="width:5%">No</th>
                        <th style="width:14%">No. Plat</th>
                        <th style="width:12%">Jenis</th>
                        <th style="width:15%">Keterangan</th>
                        <th class="text-center" style="width:13%">Waktu Masuk</th>
                        <th class="text-center" style="width:13%">Waktu Keluar</th>
                        <th class="text-center" style="width:8%">Durasi</th>
                        <th class="text-end"    style="width:12%">Total Bayar</th>
                        <th class="text-center" style="width:10%">Status</th>
                        <th class="text-center" style="width:7%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($dataLaporan)): ?>
                    <tr>
                        <td colspan="10">
                            <div class="empty-state">
                                <i class="fa-solid fa-file-lines"></i>
                                <p>Tidak ada transaksi parkir pada tanggal ini.</p>
                            </div>
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($dataLaporan as $i => $row): ?>
                    <tr>
                        <td class="text-center" style="color:#94a3b8;"><?= $i + 1 ?></td>
                        <td>
                            <span class="plate-badge" style="font-size:.82rem;">
                                <?= htmlspecialchars($row['nomor_plat']) ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($row['jenis_kendaraan'] === 'roda2'): ?>
                            <span class="badge-roda2" style="font-size:11px;"><i class="fa-solid fa-motorcycle me-1"></i> Roda 2</span>
                            <?php else: ?>
                            <span class="badge-roda4" style="font-size:11px;"><i class="fa-solid fa-car-side me-1"></i> Roda 4</span>
                            <?php endif; ?>
                        </td>
                        <!-- KETERANGAN: Parkir Masuk / Parkir Keluar -->
                        <td>
                            <?php if ($row['status'] === 'parkir'): ?>
                            <span style="font-size:12px;font-weight:600;color:#92400e;display:inline-flex;align-items:center;gap:4px;">
                                <i class="fa-solid fa-arrow-down fa-xs text-warning"></i> Parkir Masuk
                            </span>
                            <?php else: ?>
                            <span style="font-size:12px;font-weight:600;color:#065f46;display:inline-flex;align-items:center;gap:4px;">
                                <i class="fa-solid fa-arrow-up fa-xs text-success"></i> Parkir Keluar
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center" style="font-size:12.5px;">
                            <?= date('d/m H:i', strtotime($row['waktu_masuk'])) ?>
                        </td>
                        <td class="text-center" style="font-size:12.5px;">
                            <?= $row['waktu_keluar'] ? date('d/m H:i', strtotime($row['waktu_keluar'])) : '<span class="text-muted">—</span>' ?>
                        </td>
                        <td class="text-center">
                            <?= $row['durasi_jam'] ? '<strong>' . $row['durasi_jam'] . '</strong> <span style="color:#94a3b8;font-size:11px;">jam</span>' : '<span class="text-muted">—</span>' ?>
                        </td>
                        <td class="text-end">
                            <?php if ($row['status'] === 'selesai' && $row['total_bayar'] > 0): ?>
                            <strong style="color:var(--em-600);">Rp <?= number_format($row['total_bayar'], 0, ',', '.') ?></strong>
                            <?php else: ?>
                            <span class="text-muted">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if ($row['status'] === 'parkir'): ?>
                            <span class="badge-parkir" style="font-size:11px;"><i class="fa-solid fa-clock fa-xs me-1"></i> Parkir</span>
                            <?php else: ?>
                            <span class="badge-selesai" style="font-size:11px;"><i class="fa-solid fa-check fa-xs me-1"></i> Selesai</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <button type="button"
                                class="btn-danger-ghost"
                                onclick="konfirmasiHapus(<?= $row['id'] ?>, '<?= htmlspecialchars(addslashes($row['nomor_plat'])) ?>', '<?= htmlspecialchars($tanggal) ?>')"
                                title="Hapus Data">
                                <i class="fa-solid fa-trash-can fa-xs"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <?php if (!empty($dataLaporan)): ?>
                <tfoot>
                    <tr>
                        <td colspan="7" class="text-end fw-bold" style="color:var(--sl-700);">TOTAL PENDAPATAN :</td>
                        <td class="text-end fw-bold" style="color:var(--em-600);font-size:14px;">
                            Rp <?= number_format($totalPendapatan, 0, ',', '.') ?>
                        </td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>

    <div class="page-footer">SiParkir &copy; <?= date('Y') ?></div>
</main>

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
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then(function(result) {
        if (result.isConfirmed) {
            window.location.href = '<?= BASE_URL ?>index.php?page=hapus&id=' + id
                + '&from=laporan&tanggal=' + encodeURIComponent(tanggal);
        }
    });
}
</script>
</body>
</html>
