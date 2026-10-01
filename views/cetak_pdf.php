<?php
/**
 * View: Cetak PDF
 * Ditampilkan saat index.php?page=cetak_pdf
 */
if (session_status() === PHP_SESSION_NONE) session_start();
checkAuth();

$tanggal     = $_GET['tanggal'] ?? date('Y-m-d');
$petugas     = $_SESSION['admin_nama'] ?? 'Petugas Parkir';

$totalPendapatan = 0;
$jmlSelesai = 0;
foreach ($dataLaporan as $b) {
    if ($b['status'] === 'selesai') { 
        $totalPendapatan += $b['total_bayar']; 
        $jmlSelesai++; 
    }
}
$jmlTotal  = count($dataLaporan);
$jmlParkir = $jmlTotal - $jmlSelesai;

if (!function_exists('tgl_indo_pdf')) {
    function tgl_indo_pdf(string $t): string {
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
<title>Laporan Parkir — <?= tgl_indo_pdf($tanggal) ?></title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    background: #f1f5f9;
    font-family: Arial, Helvetica, sans-serif;
    font-size: 10pt;
    color: #000;
    line-height: 1.35;
}

/* Toolbar navigasi hanya di layar */
.no-print {
    background: #fff;
    border-bottom: 1px solid #e2e8f0;
    padding: 10px 20px;
    display: flex;
    align-items: center;
    gap: 12px;
}
.no-print button {
    padding: 6px 18px;
    font-size: 9.5pt;
    font-weight: 700;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
.btn-cetak { background: #be185d; color: white; }
.btn-tutup { background: #e2e8f0; color: #334155; }
.no-print span { font-size: 8.5pt; color: #64748b; }

/* Kertas cetak A4 */
.sheet {
    width: 210mm;
    margin: 14px auto;
    padding: 14mm 14mm 12mm;
    background: #fff;
    border: 1px solid #e2e8f0;
}

table { width: 100%; border-collapse: collapse; }

/* Kop surat */
.kop-logo {
    width: 34pt; height: 34pt;
    background: #be185d;
    border-radius: 8pt;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 17pt;
    font-weight: 700;
    vertical-align: middle;
    margin-right: 10pt;
}
.kop-title { font-size: 14pt; font-weight: 700; text-transform: uppercase; color: #be185d; }
.kop-sub   { font-size: 9pt; color: #475569; }
.kop-divider { border-top: 2px solid #be185d; margin: 8pt 0 10pt; }

/* Meta */
.meta-table td {
    font-size: 9pt; padding: 2.5pt 0; border: none; vertical-align: top;
}

/* Data Table */
.data-table { margin-top: 6pt; }
.data-table th {
    background: #1e293b !important;
    color: white !important;
    font-size: 8.5pt;
    font-weight: 700;
    text-align: center;
    padding: 7pt 8pt;
    border: 1px solid #334155;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}
.data-table td {
    font-size: 9pt;
    padding: 6pt 8pt;
    border: 1px solid #cbd5e1;
    vertical-align: middle;
}
.data-table tbody tr:nth-child(even) td {
    background: #fdf2f8;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}
.data-table tfoot td {
    background: #fce7f3 !important;
    font-weight: 700;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}

.text-center { text-align: center; }
.text-right  { text-align: right; }
.fw-bold     { font-weight: 700; }
.text-rose   { color: #be185d; }

/* Badge inline */
.kbadge {
    display: inline-block;
    font-size: 8pt;
    font-weight: 700;
    padding: 1.5pt 7pt;
    border-radius: 10pt;
}
.kbadge-parkir  { background: #fffbeb; color: #92400e; }
.kbadge-selesai { background: #ecfdf5; color: #065f46; }

@page { size: A4 portrait; margin: 0 !important; }

@media print {
    body { background: white !important; margin: 0 !important; }
    .no-print { display: none !important; }
    .sheet { width: 100% !important; margin: 0 !important; border: none !important; }
}
</style>
</head>
<body>

<div class="no-print">
    <button class="btn-cetak" onclick="window.print()">
        &#128438; Cetak / Simpan PDF
    </button>
    <button class="btn-tutup" onclick="window.close()">Tutup Jendela</button>
    <span>Pilih <strong>Destination: Save as PDF</strong> pada dialog cetak.</span>
</div>

<div class="sheet">
    <!-- Kop Laporan -->
    <table style="margin-bottom:2pt;">
        <tr>
            <td>
                <span class="kop-logo">P</span>
                <span class="kop-title">SiParkir</span>
                <div class="kop-sub" style="margin-left:44pt;">Laporan Rekapitulasi Parkir Harian</div>
            </td>
        </tr>
    </table>
    <hr class="kop-divider">

    <!-- Meta Info -->
    <table class="meta-table" style="margin-bottom:10pt;">
        <tr>
            <td style="width:22%;">Tanggal Laporan</td>
            <td style="width:2%;">:</td>
            <td style="width:40%;"><strong><?= tgl_indo_pdf($tanggal) ?></strong></td>
            <td style="width:16%;">Waktu Cetak</td>
            <td style="width:2%;">:</td>
            <td><?= date('d/m/Y H:i') ?> WIB</td>
        </tr>
        <tr>
            <td>Total Kendaraan</td>
            <td>:</td>
            <td><?= $jmlTotal ?> unit (<?= $jmlSelesai ?> selesai, <?= $jmlParkir ?> masih parkir)</td>
            <td>Petugas</td>
            <td>:</td>
            <td><?= htmlspecialchars($petugas) ?></td>
        </tr>
        <tr>
            <td>Total Pendapatan</td>
            <td>:</td>
            <td><strong class="text-rose">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></strong></td>
            <td></td><td></td><td></td>
        </tr>
    </table>

    <!-- Tabel Data -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:5%">No</th>
                <th style="width:16%">Nomor Plat</th>
                <th style="width:14%">Jenis</th>
                <th style="width:15%">Keterangan</th>
                <th style="width:13%">Waktu Masuk</th>
                <th style="width:13%">Waktu Keluar</th>
                <th style="width:8%">Durasi</th>
                <th style="width:16%">Total Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($dataLaporan)): ?>
            <tr>
                <td colspan="8" class="text-center" style="padding:18pt;color:#64748b;">
                    Tidak ada transaksi kendaraan pada tanggal ini.
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($dataLaporan as $i => $row): ?>
            <tr>
                <td class="text-center"><?= $i + 1 ?></td>
                <td class="text-center fw-bold"><?= htmlspecialchars($row['nomor_plat']) ?></td>
                <td class="text-center"><?= $row['jenis_kendaraan'] === 'roda2' ? 'Roda 2' : 'Roda 4' ?></td>
                <td class="text-center">
                    <?php if ($row['status'] === 'parkir'): ?>
                    <span class="kbadge kbadge-parkir">&darr; Parkir Masuk</span>
                    <?php else: ?>
                    <span class="kbadge kbadge-selesai">&uarr; Parkir Keluar</span>
                    <?php endif; ?>
                </td>
                <td class="text-center"><?= date('d/m/Y H:i', strtotime($row['waktu_masuk'])) ?></td>
                <td class="text-center">
                    <?= $row['waktu_keluar'] ? date('d/m/Y H:i', strtotime($row['waktu_keluar'])) : '&mdash;' ?>
                </td>
                <td class="text-center">
                    <?= $row['durasi_jam'] ? $row['durasi_jam'] . ' jam' : '&mdash;' ?>
                </td>
                <td class="text-right">
                    <?= $row['status'] === 'selesai' && $row['total_bayar'] > 0
                        ? 'Rp ' . number_format($row['total_bayar'], 0, ',', '.')
                        : '&mdash;' ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="7" class="text-right fw-bold">TOTAL PENDAPATAN :</td>
                <td class="text-right fw-bold text-rose">
                    Rp <?= number_format($totalPendapatan, 0, ',', '.') ?>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
window.addEventListener('load', function() {
    setTimeout(function() { window.print(); }, 500);
});
</script>
</body>
</html>
