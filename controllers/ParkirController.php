<?php

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/ParkirModel.php';

class ParkirController {
    private ParkirModel $model;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) session_start();
        checkAuth();
        $this->model = new ParkirModel();
    }
    public function dashboard(): void {
        $statsMasuk      = $this->model->statsMasukHariIni();
        $statsAktif      = $this->model->statsAktifParkir();
        $statsPendapatan = $this->model->statsPendapatanHariIni();
        $statsSelesai    = $this->model->statsTotalTransaksiSelesai();
        $kendaraanAktif  = $this->model->getSemuaMasuk();

        require_once __DIR__ . '/../views/dashboard.php';
    }

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

    public function parkirKeluar(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->prosesKeluar();
            return;
        }

        $kendaraan  = null;
        $estimasi   = null;
        $cariPlat   = '';

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

    public function laporan(): void {
        $tanggal          = $_GET['tanggal'] ?? date('Y-m-d');
        $dataLaporan      = $this->model->getLaporan($tanggal);
        $totalPendapatan  = 0;
        $totalSelesai     = 0;
        $totalMasih       = 0;

        foreach ($dataLaporan as $row) {
            if ($row['status'] === 'selesai') {
                $totalPendapatan += $row['total_bayar'];
                $totalSelesai++;
            } else {
                $totalMasih++;
            }
        }

        require_once __DIR__ . '/../views/laporan.php';
    }

    public function cetak_pdf(): void {
        $tanggal          = $_GET['tanggal'] ?? date('Y-m-d');
        $dataLaporan      = $this->model->getLaporan($tanggal);
        $totalPendapatan  = 0;
        $totalSelesai     = 0;
        $totalMasih       = 0;

        foreach ($dataLaporan as $row) {
            if ($row['status'] === 'selesai') {
                $totalPendapatan += $row['total_bayar'];
                $totalSelesai++;
            } else {
                $totalMasih++;
            }
        }

        require_once __DIR__ . '/../views/cetak_pdf.php';
    }

    public function export_csv(): void {
        $tanggal          = $_GET['tanggal'] ?? date('Y-m-d');
        $baris            = $this->model->getLaporan($tanggal);
        $namaPetugas      = $_SESSION['admin_nama'] ?? 'Petugas Parkir';

        $totalPendapatan = 0;
        foreach ($baris as $b) {
            if ($b['status'] === 'selesai') $totalPendapatan += $b['total_bayar'];
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
            .th-head { background-color: #1e293b; color: #ffffff; border: 1px solid #000000; padding: 8px 10px; font-weight: bold; text-align: center; }
            .td-data { border: 1px solid #000000; padding: 6px 10px; vertical-align: middle; }
            .td-even { background-color: #f8fafc; }
            .td-total { background-color: #f1f5f9; font-weight: bold; }
            .text-center { text-align: center; }
            .text-right  { text-align: right; }
            .text-green  { color: #16a34a; font-weight: bold; }
        </style>';
        echo '</head><body>';

        echo '<table>';
        echo '<colgroup>
            <col width="50"><col width="140"><col width="130">
            <col width="140"><col width="140"><col width="110"><col width="160">
        </colgroup>';

        echo '<tr><td colspan="7" style="font-size:13pt;font-weight:bold;text-align:center;border:none;padding:6px 0;">LAPORAN TRANSAKSI PARKIR HARIAN</td></tr>';
        echo '<tr><td colspan="7" style="text-align:center;border:none;font-size:9.5pt;color:#475569;">Tanggal Periode: ' . date('d F Y', strtotime($tanggal)) . '</td></tr>';
        echo '<tr><td colspan="7" style="text-align:center;border:none;font-size:9pt;color:#64748b;">Petugas: ' . htmlspecialchars($namaPetugas) . ' &nbsp;|&nbsp; Waktu Unduh: ' . date('d/m/Y H:i') . ' WIB</td></tr>';
        echo '<tr><td colspan="7" style="border:none;">&nbsp;</td></tr>';

        echo '<tr>
            <td class="th-head">No</td>
            <td class="th-head">Nomor Plat</td>
            <td class="th-head">Jenis Kendaraan</td>
            <td class="th-head">Waktu Masuk</td>
            <td class="th-head">Waktu Keluar</td>
            <td class="th-head">Status</td>
            <td class="th-head">Total Bayar (Rp)</td>
        </tr>';

        $no = 1;
        foreach ($baris as $row) {
            $even       = ($no % 2 === 0) ? ' td-even' : '';
            $jenis      = $row['jenis_kendaraan'] === 'roda2' ? 'Roda 2 (Motor)' : 'Roda 4 (Mobil)';
            $wMasuk     = date('H:i', strtotime($row['waktu_masuk']));
            $wKeluar    = $row['waktu_keluar'] ? date('H:i', strtotime($row['waktu_keluar'])) : '-';
            $status     = $row['status'] === 'selesai' ? 'Selesai' : 'Parkir';
            $totalBayar = $row['status'] === 'selesai' ? number_format($row['total_bayar'], 0, ',', '.') : '0';

            echo '<tr>
                <td class="td-data text-center' . $even . '">' . $no++ . '</td>
                <td class="td-data text-center' . $even . '" style="font-weight:bold;">' . htmlspecialchars($row['nomor_plat']) . '</td>
                <td class="td-data text-center' . $even . '">' . $jenis . '</td>
                <td class="td-data text-center' . $even . '">' . $wMasuk . '</td>
                <td class="td-data text-center' . $even . '">' . $wKeluar . '</td>
                <td class="td-data text-center' . $even . '">' . $status . '</td>
                <td class="td-data text-right' . $even . '">' . $totalBayar . '</td>
            </tr>';
        }

        echo '<tr>
            <td colspan="6" class="td-total text-right">TOTAL PENDAPATAN :</td>
            <td class="td-total text-right text-green">Rp ' . number_format($totalPendapatan, 0, ',', '.') . '</td>
        </tr>';

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