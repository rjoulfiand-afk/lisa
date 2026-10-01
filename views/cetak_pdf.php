<?php
/**
 * View: Cetak PDF / Print Laporan Harian
 * Clean & Professional A4 Print Layout
 */
if (session_status() === PHP_SESSION_NONE) session_start();

$tanggal  = $_GET['tanggal'] ?? date('Y-m-d');
$petugas  = $_SESSION['admin_nama'] ?? $_SESSION['nama_petugas'] ?? 'Petugas Parkir';

$totalPendapatan = 0;
$jmlSelesai = 0;
foreach ($dataLaporan as $b) {
    if ($b['status'] === 'selesai') {
        $totalPendapatan += (float)$b['total_bayar'];
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

/* Toolbar (hanya di layar) */
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
.btn-cetak { background: #0f172a; color: #fff; }
.btn-tutup { background: #e2e8f0; color: #334155; }
.no-print span { font-size: 8.5pt; color: #64748b; }

/* Kertas A4 */
.sheet {
    width: 210mm;
    margin: 14px auto;
    padding: 15mm;
    background: #fff;
    border: 1px solid #e2e8f0;
}

table { width: 100%; border-collapse: collapse; }

/* Kop */
.kop-title {
    font-size: 14pt;
    font-weight: bold;
    text-transform: uppercase;
    text-align: center;
    padding-bottom: 2px;
}
.kop-sub {
    font-size: 9pt;
    text-align: center;
    padding-bottom: 10px;
    border-bottom: 2px solid #000;
}

/* Meta */
.meta-table { margin: 12px 0; font-size: 9pt; }
.meta-table td { padding: 2.5px 0; border: none; vertical-align: top; }

/* Tabel Data */
.data-table { margin-top: 5px; }
.data-table th {
    background-color: #f2f2f2 !important;
    font-weight: bold;
    font-size: 8.5pt;
    text-align: center;
    padding: 7px 8px;
    border: 1px solid #000;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}
.data-table td {
    font-size: 9pt;
    padding: 6px 8px;
    border: 1px solid #000;
    vertical-align: middle;
}

.text-center { text-align: center; }
.text-right  { text-align: right; }
.fw-bold     { font-weight: bold; }

@page { size: A4 portrait; margin: 0 !important; }

@media print {
    body { background: #fff !important; margin: 0 !important; }
    .no-print { display: none !important; }
    .sheet { width: 100% !important; margin: 0 !important; border: none !important; }
}
</style>
</head>
<body>

<div class="no-print">
    <button class="btn-cetak" onclick="window.print()">Cetak / Simpan PDF</button>
    <button class="btn-tutup" onclick="window.close()">Tutup Jendela</button>
    <span>Pilih <strong>Destination: Save as PDF</strong> pada dialog cetak.</span>
</div>

<div class="sheet">
    <!-- Kop Laporan -->
    <table>
        <tr><td class="kop-title">Laporan Rekapitulasi Parkir Harian</td></tr>
        <tr><td class="kop-sub">Sistem Informasi Pengelolaan Parkir Kendaraan Bermotor</td></tr>
    </table>

    <!-- Meta Info -->
    <table class="meta-table">
        <tr>
            <td style="width:18%;">Tanggal Laporan</td>
            <td style="width:2%;">:</td>
            <td style="width:44%;"><strong><?= tgl_indo_pdf($tanggal) ?></strong></td>
            <td style="width:16%;">Waktu Cetak</td>
            <td style="width:2%;">:</td>
            <td style="width:18%;"><?= date('d/m/Y H:i') ?> WIB</td>
        </tr>
        <tr>
            <td>Total Kendaraan</td>
            <td>:</td>
            <td><?= $jmlTotal ?> unit (<?= $jmlSelesai ?> selesai, <?= $jmlParkir ?> masih parkir)</td>
            <td>Petugas</td>
            <td>:</td>
            <td><?= htmlspecialchars($petugas) ?></td>
        </tr>
    </table>

    <!-- Tabel Data Transaksi -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width:6%;">No</th>
                <th style="width:17%;">Nomor Plat</th>
                <th style="width:14%;">Jenis Kendaraan</th>
                <th style="width:15%;">Waktu Masuk</th>
                <th style="width:15%;">Waktu Keluar</th>
                <th style="width:9%;">Durasi</th>
                <th style="width:12%;">Status</th>
                <th style="width:12%;">Total Bayar</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($dataLaporan)): ?>
            <tr>
                <td colspan="8" class="text-center" style="padding:20px; color:#666;">
                    Tidak ada transaksi kendaraan pada tanggal ini.
                </td>
            </tr>
            <?php else: ?>
            <?php foreach ($dataLaporan as $i => $row): ?>
            <tr>
                <td class="text-center"><?= $i + 1 ?></td>
                <td class="text-center fw-bold"><?= htmlspecialchars($row['nomor_plat']) ?></td>
                <td class="text-center"><?= $row['jenis_kendaraan'] === 'roda2' ? 'Roda 2' : 'Roda 4' ?></td>
                <td class="text-center"><?= date('d/m/Y H:i', strtotime($row['waktu_masuk'])) ?></td>
                <td class="text-center"><?= !empty($row['waktu_keluar']) ? date('d/m/Y H:i', strtotime($row['waktu_keluar'])) : '-' ?></td>
                <td class="text-center"><?= !empty($row['durasi_jam']) ? $row['durasi_jam'] . ' jam' : '-' ?></td>
                <td class="text-center"><?= $row['status'] === 'selesai' ? 'Selesai' : 'Parkir' ?></td>
                <td class="text-right">
                    <?= ($row['status'] === 'selesai' && $row['total_bayar'] > 0) 
                        ? 'Rp ' . number_format($row['total_bayar'], 0, ',', '.') 
                        : '-' ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <!-- Baris Total -->
            <tr style="background-color:#f2f2f2; font-weight:bold;">
                <td colspan="7" class="text-right">TOTAL PENDAPATAN :</td>
                <td class="text-right">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></td>
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