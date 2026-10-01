<?php
/**
 * Controller: ParkirController
 * Menangani: dashboard, parkir_masuk, parkir_keluar, laporan, cetak_pdf, export_csv, hapus
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/ParkirModel.php';

class ParkirController {
    private ParkirModel $model;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        checkAuth();
        $this->model = new ParkirModel();
    }

    // ============================================================
    //  DASHBOARD
    // ============================================================
    public function dashboard(): void {
        $statsMasuk      = $this->model->statsMasukHariIni();
        $statsAktif      = $this->model->statsAktifParkir();
        $statsPendapatan = $this->model->statsPendapatanHariIni();
        $statsSelesai    = $this->model->statsTotalTransaksiSelesai();
        $kendaraanAktif  = $this->model->getSemuaMasuk();

        require_once __DIR__ . '/../views/dashboard.php';
    }

    // ============================================================
    //  PARKIR MASUK
    // ============================================================
    public function parkirMasuk(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->simpanMasuk();
            return;
        }

        $data = $this->model->getSemuaMasuk();
        require_once __DIR__ . '/../views/parkir_masuk.php';
    }

    private function simpanMasuk(): void {
        $nomorPlat      = strtoupper(trim($_POST['nomor_plat'] ?? ''));
        $jenisKendaraan = $_POST['jenis_kendaraan'] ?? '';

        if (empty($nomorPlat) || !in_array($jenisKendaraan, ['roda2', 'roda4'], true)) {
            redirect(BASE_URL . 'index.php?page=parkir_masuk&status=error&msg=' . urlencode('Data tidak valid. Lengkapi nomor plat dan jenis kendaraan.'));
        }

        $result = $this->model->tambahMasuk($nomorPlat, $jenisKendaraan);

        if ($result['success']) {
            redirect(BASE_URL . 'index.php?page=parkir_masuk&status=sukses_masuk&plat=' . urlencode($nomorPlat));
        } else {
            redirect(BASE_URL . 'index.php?page=parkir_masuk&status=error&msg=' . urlencode(strip_tags($result['message'])));
        }
    }

    // ============================================================
    //  PARKIR KELUAR
    // ============================================================
    public function parkirKeluar(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->prosesKeluar();
            return;
        }

        $kendaraan = null;
        $estimasi  = null;
        $cariPlat  = '';

        if (isset($_GET['cari_plat']) && trim($_GET['cari_plat']) !== '') {
            $cariPlat  = strtoupper(trim($_GET['cari_plat']));
            $kendaraan = $this->model->cariByPlat($cariPlat);
            if ($kendaraan) {
                $sekarang = date('Y-m-d H:i:s');
                $estimasi = ParkirModel::hitungBiaya($kendaraan['jenis_kendaraan'], $kendaraan['waktu_masuk'], $sekarang);
            }
        }

        require_once __DIR__ . '/../views/parkir_keluar.php';
    }

    private function prosesKeluar(): void {
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            redirect(BASE_URL . 'index.php?page=parkir_keluar&status=error');
        }

        $result = $this->model->prosesKeluar($id);

        if ($result['success']) {
            $bayar = (int)($result['data']['total_bayar'] ?? 0);
            $plat  = urlencode($result['data']['nomor_plat'] ?? '');
            redirect(BASE_URL . 'index.php?page=parkir_keluar&status=sukses_keluar&bayar=' . $bayar . '&plat=' . $plat);
        } else {
            redirect(BASE_URL . 'index.php?page=parkir_keluar&status=error');
        }
    }

    // ============================================================
    //  LAPORAN
    // ============================================================
    public function laporan(): void {
        $tanggal     = $_GET['tanggal'] ?? date('Y-m-d');
        $dataLaporan = $this->model->getLaporan($tanggal);

        $totalPendapatan = 0;
        $totalSelesai    = 0;
        $totalMasih      = 0;

        foreach ($dataLaporan as $row) {
            if ($row['status'] === 'selesai') {
                $totalPendapatan += (float)$row['total_bayar'];
                $totalSelesai++;
            } else {
                $totalMasih++;
            }
        }

        require_once __DIR__ . '/../views/laporan.php';
    }

    // ============================================================
    //  CETAK PDF
    // ============================================================
    public function cetak_pdf(): void {
        $tanggal     = $_GET['tanggal'] ?? date('Y-m-d');
        $dataLaporan = $this->model->getLaporan($tanggal);

        $totalPendapatan = 0;
        $totalSelesai    = 0;
        $totalMasih      = 0;

        foreach ($dataLaporan as $row) {
            if ($row['status'] === 'selesai') {
                $totalPendapatan += (float)$row['total_bayar'];
                $totalSelesai++;
            } else {
                $totalMasih++;
            }
        }

        require_once __DIR__ . '/../views/cetak_pdf.php';
    }

        // ============================================================
    //  EXPORT EXCEL (.xls)
    // ============================================================
    public function export_csv(): void {
        $tanggal     = $_GET['tanggal'] ?? date('Y-m-d');
        $baris       = $this->model->getLaporan($tanggal);
        $namaPetugas = $_SESSION['admin_nama'] ?? $_SESSION['nama_petugas'] ?? 'Petugas Parkir';

        $totalPendapatan = 0;
        foreach ($baris as $b) {
            if ($b['status'] === 'selesai') $totalPendapatan += (float)$b['total_bayar'];
        }

        $filename = 'Laporan_Parkir_' . str_replace('-', '', $tanggal) . '.xls';

        header('Content-Type: application/vnd.ms-excel; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
        echo '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">';
        echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>';
        echo '<x:Name>Laporan Harian</x:Name>';
        echo '<x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions>';
        echo '</x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
        echo '<style>
            table { border-collapse: collapse; font-family: Arial, sans-serif; font-size: 10pt; }
            th { background-color: #1e293b; color: #ffffff; border: 1px solid #000; padding: 8px 10px; font-weight: bold; text-align: center; }
            td { border: 1px solid #000; padding: 6px 10px; vertical-align: middle; }
            .text-center { text-align: center; }
            .text-right  { text-align: right; }
            .total { background-color: #f1f5f9; font-weight: bold; }
        </style>';
        echo '</head><body>';

        echo '<table>';
        // Lebar kolom anti ####
        echo '<colgroup>';
        echo '<col width="50">';   // No
        echo '<col width="130">';  // Nomor Plat
        echo '<col width="130">';  // Jenis Kendaraan
        echo '<col width="80">';   // Waktu Masuk (jam saja)
        echo '<col width="80">';   // Waktu Keluar (jam saja)
        echo '<col width="70">';   // Durasi
        echo '<col width="90">';   // Status
        echo '<col width="150">';  // Total Bayar
        echo '</colgroup>';

        // Header Laporan
        echo '<tr><td colspan="8" style="font-size:13pt;font-weight:bold;text-align:center;border:none;">LAPORAN TRANSAKSI PARKIR HARIAN</td></tr>';
        echo '<tr><td colspan="8" style="text-align:center;border:none;">Tanggal Periode: ' . date('d/m/Y', strtotime($tanggal)) . '</td></tr>';
        echo '<tr><td colspan="8" style="text-align:center;border:none;font-size:9pt;color:#555;">Petugas: ' . htmlspecialchars($namaPetugas) . ' | Waktu Unduh: ' . date('d/m/Y H:i') . ' WIB</td></tr>';
        echo '<tr><td colspan="8" style="border:none;">&nbsp;</td></tr>';

        // Header Tabel
        echo '<tr>';
        echo '<th>No</th>';
        echo '<th>Nomor Plat</th>';
        echo '<th>Jenis Kendaraan</th>';
        echo '<th>Waktu Masuk</th>';
        echo '<th>Waktu Keluar</th>';
        echo '<th>Durasi</th>';
        echo '<th>Status</th>';
        echo '<th>Total Bayar (Rp)</th>';
        echo '</tr>';

        $no = 1;
        foreach ($baris as $row) {
            $jenis   = $row['jenis_kendaraan'] === 'roda2' ? 'Roda 2' : 'Roda 4';
            $wMasuk  = date('H:i', strtotime($row['waktu_masuk']));
            $wKeluar = !empty($row['waktu_keluar']) ? date('H:i', strtotime($row['waktu_keluar'])) : '-';
            $durasi  = !empty($row['durasi_jam']) ? $row['durasi_jam'] . ' jam' : '-';
            $status  = $row['status'] === 'selesai' ? 'Selesai' : 'Parkir';
            $bayar   = ($row['status'] === 'selesai' && $row['total_bayar'] > 0)
                       ? 'Rp ' . number_format($row['total_bayar'], 0, ',', '.') : '-';

            echo '<tr>';
            echo '<td class="text-center">' . $no++ . '</td>';
            echo '<td class="text-center" style="font-weight:bold;">' . htmlspecialchars($row['nomor_plat']) . '</td>';
            echo '<td class="text-center">' . $jenis . '</td>';
            echo '<td class="text-center">' . $wMasuk . '</td>';
            echo '<td class="text-center">' . $wKeluar . '</td>';
            echo '<td class="text-center">' . $durasi . '</td>';
            echo '<td class="text-center">' . $status . '</td>';
            echo '<td class="text-right">' . $bayar . '</td>';
            echo '</tr>';
        }

        // Baris Total
        echo '<tr class="total">';
        echo '<td colspan="7" class="text-right">TOTAL PENDAPATAN :</td>';
        echo '<td class="text-right">Rp ' . number_format($totalPendapatan, 0, ',', '.') . '</td>';
        echo '</tr>';

        echo '</table></body></html>';
        exit;
    }

    public function hapus(): void {
        $id      = (int)($_GET['id'] ?? 0);
        $from    = $_GET['from'] ?? 'dashboard';
        $tanggal = $_GET['tanggal'] ?? date('Y-m-d');

        if ($id > 0) {
            $this->model->hapus($id);
        }

        if ($from === 'laporan') {
            redirect(BASE_URL . 'index.php?page=laporan&tanggal=' . urlencode($tanggal) . '&status=hapus_sukses');
        } elseif ($from === 'parkir_masuk') {
            redirect(BASE_URL . 'index.php?page=parkir_masuk&status=hapus_sukses');
        } else {
            redirect(BASE_URL . 'index.php?page=dashboard&status=hapus_sukses');
        }
    }
}