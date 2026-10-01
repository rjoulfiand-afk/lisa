<?php
/**
 * Model: ParkirModel (Project Teman)
 * Handles semua query CRUD untuk tabel parkir
 *
 * Aturan tarif (sesuai soal):
 *   Roda 2 : Tarif awal Rp 2.000 (sampai 2 jam)
 *            Lebih dari 2 jam: tambah Rp 1.000 per jam
 *   Roda 4 : Tarif awal Rp 5.000 (sampai 2 jam)
 *            Lebih dari 2 jam: tambah Rp 1.000 per jam
 */

require_once __DIR__ . '/../config/database.php';

class ParkirModel {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = Database::getInstance()->getPdo();
    }

    // ============================================================
    //  UTILITY
    // ============================================================

    /**
     * Generate nomor ID unik untuk transaksi baru (Aman dari Duplicate Key)
     * Format: PRK-20260001
     */
    public function generateNomorId(): string {
        $prefix = 'PRK-' . date('Y');
        
        // Ambil nomor transaksi paling terakhir agar aman meskipun ada data yang dihapus
        $stmt = $this->pdo->prepare(
            "SELECT nomor_id FROM parkir WHERE nomor_id LIKE ? ORDER BY id DESC LIMIT 1"
        );
        $stmt->execute([$prefix . '%']);
        $lastId = $stmt->fetchColumn();

        if ($lastId) {
            $lastNumber = (int)substr($lastId, -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Hitung biaya parkir berdasarkan jenis kendaraan & durasi
     */
    public static function hitungBiaya(string $jenis, string $waktuMasuk, string $waktuKeluar): array {
        $dtMasuk  = new DateTime($waktuMasuk);
        $dtKeluar = new DateTime($waktuKeluar);

        // Total selisih detik
        $totalDetik = $dtKeluar->getTimestamp() - $dtMasuk->getTimestamp();

        // Bulatkan ke atas per jam — minimum 1 jam
        $durasiJam = (int)ceil($totalDetik / 3600);
        if ($durasiJam < 1) $durasiJam = 1;

        // Tarif awal 2 jam pertama
        $tarifAwal = ($jenis === 'roda2') ? 2000 : 5000;

        if ($durasiJam <= 2) {
            $totalBayar = $tarifAwal;
        } else {
            // Tambahan Rp 1.000 per jam setelah 2 jam
            $jamTambahan = $durasiJam - 2;
            $totalBayar  = $tarifAwal + ($jamTambahan * 1000);
        }

        return [
            'durasi_jam'  => $durasiJam,
            'total_bayar' => (float)$totalBayar,
        ];
    }

    // ============================================================
    //  CREATE
    // ============================================================

    /**
     * Tambah kendaraan masuk (Parkir Masuk)
     */
    public function tambahMasuk(string $nomorPlat, string $jenisKendaraan): array {
        $nomorPlat = strtoupper(trim($nomorPlat));

        // Cek apakah plat yang sama masih aktif parkir
        $cek = $this->pdo->prepare(
            "SELECT id FROM parkir WHERE nomor_plat = ? AND status = 'parkir' LIMIT 1"
        );
        $cek->execute([$nomorPlat]);
        if ($cek->fetch()) {
            return [
                'success' => false,
                'message' => "Kendaraan dengan plat <strong>{$nomorPlat}</strong> masih terparkir di area!",
            ];
        }

        $nomorId    = $this->generateNomorId();
        $waktuMasuk = date('Y-m-d H:i:s');

        $stmt = $this->pdo->prepare(
            "INSERT INTO parkir (nomor_id, nomor_plat, jenis_kendaraan, waktu_masuk, status, total_bayar)
             VALUES (?, ?, ?, ?, 'parkir', 0.00)"
        );
        $stmt->execute([$nomorId, $nomorPlat, $jenisKendaraan, $waktuMasuk]);

        return [
            'success'  => true,
            'message'  => "Kendaraan <strong>{$nomorPlat}</strong> berhasil dicatat masuk.",
            'nomor_id' => $nomorId,
        ];
    }

    // ============================================================
    //  READ
    // ============================================================

    /**
     * Ambil semua kendaraan yang sedang parkir
     */
    public function getSemuaMasuk(): array {
        $stmt = $this->pdo->query(
            "SELECT * FROM parkir WHERE status = 'parkir' ORDER BY waktu_masuk DESC"
        );
        return $stmt->fetchAll();
    }

    /**
     * Cari kendaraan aktif berdasarkan nomor plat (Pencarian Fleksibel / LIKE)
     */
    public function cariByPlat(string $nomorPlat): ?array {
        $nomorPlat = strtoupper(trim($nomorPlat));
        $cariLike  = '%' . $nomorPlat . '%';

        $stmt = $this->pdo->prepare(
            "SELECT * FROM parkir
             WHERE (nomor_plat = ? OR nomor_plat LIKE ?) AND status = 'parkir'
             ORDER BY waktu_masuk DESC
             LIMIT 1"
        );
        $stmt->execute([$nomorPlat, $cariLike]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Ambil detail transaksi by ID
     */
    public function getById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM parkir WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Ambil data laporan harian
     */
        public function getLaporan(?string $tanggal = null): array {
        if (!empty($tanggal)) {
            $stmt = $this->pdo->prepare(
                "SELECT * FROM parkir
                 WHERE DATE(waktu_masuk) = ? OR DATE(waktu_keluar) = ?
                 ORDER BY waktu_masuk DESC"
            );
            $stmt->execute([$tanggal, $tanggal]);
        } else {
            $stmt = $this->pdo->query(
                "SELECT * FROM parkir ORDER BY waktu_masuk DESC"
            );
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Alias method untuk fleksibilitas controller
     */
    public function getLaporanByTanggal(string $tanggal): array {
        return $this->getLaporan($tanggal);
    }
    /**
     * Total pendapatan pada tanggal tertentu
     */
    public function totalPendapatanByTanggal(string $tanggal): float {
        $stmt = $this->pdo->prepare(
            "SELECT COALESCE(SUM(total_bayar), 0) FROM parkir 
             WHERE (DATE(waktu_keluar) = ? OR (DATE(waktu_masuk) = ? AND status = 'selesai'))
               AND status = 'selesai'"
        );
        $stmt->execute([$tanggal, $tanggal]);
        return (float)$stmt->fetchColumn();
    }
    /**
     * Total kendaraan selesai keluar pada tanggal tertentu
     */
    public function totalSelesaiByTanggal(string $tanggal): int {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM parkir 
             WHERE (DATE(waktu_keluar) = ? OR (DATE(waktu_masuk) = ? AND status = 'selesai'))
               AND status = 'selesai'"
        );
        $stmt->execute([$tanggal, $tanggal]);
        return (int)$stmt->fetchColumn();
    }
    /**
     * Total kendaraan yang masih parkir (belum keluar)
     */
    public function totalMasihByTanggal(string $tanggal): int {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM parkir 
             WHERE DATE(waktu_masuk) = ? AND status = 'parkir'"
        );
        $stmt->execute([$tanggal]);
        return (int)$stmt->fetchColumn();
    }

    // ============================================================
    //  UPDATE (Proses Keluar)
    // ============================================================

    /**
     * Proses kendaraan keluar — hitung biaya, update status jadi 'selesai'
     */
    public function prosesKeluar(int $id): array {
        $parkir = $this->getById($id);

        if (!$parkir) {
            return ['success' => false, 'message' => 'Data transaksi tidak ditemukan.'];
        }

        if ($parkir['status'] !== 'parkir') {
            return ['success' => false, 'message' => 'Kendaraan ini sudah dalam status selesai.'];
        }

        $waktuKeluar = date('Y-m-d H:i:s');
        $kalkulasi   = self::hitungBiaya($parkir['jenis_kendaraan'], $parkir['waktu_masuk'], $waktuKeluar);

        $stmt = $this->pdo->prepare(
            "UPDATE parkir
             SET waktu_keluar = ?,
                 durasi_jam   = ?,
                 total_bayar  = ?,
                 status       = 'selesai'
             WHERE id = ? AND status = 'parkir'"
        );
        $stmt->execute([
            $waktuKeluar,
            $kalkulasi['durasi_jam'],
            $kalkulasi['total_bayar'],
            $id,
        ]);

        if ($stmt->rowCount() === 0) {
            return ['success' => false, 'message' => 'Gagal memproses keluar. Coba lagi.'];
        }

        $nomorPlat = $parkir['nomor_plat'];

        return [
            'success' => true,
            'message' => "Kendaraan <strong>{$nomorPlat}</strong> berhasil diproses keluar.",
            'data'    => array_merge($parkir, [
                'waktu_keluar' => $waktuKeluar,
                'durasi_jam'   => $kalkulasi['durasi_jam'],
                'total_bayar'  => $kalkulasi['total_bayar'],
            ]),
        ];
    }
    public function hapus(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM parkir WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    public function statsMasukHariIni(): int {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM parkir WHERE DATE(waktu_masuk) = CURDATE()"
        );
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function statsAktifParkir(): int {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM parkir WHERE status = 'parkir'");
        return (int)$stmt->fetchColumn();
    }

    public function statsPendapatanHariIni(): float {
        $stmt = $this->pdo->prepare(
            "SELECT COALESCE(SUM(total_bayar), 0)
             FROM parkir
             WHERE DATE(waktu_keluar) = CURDATE() AND status = 'selesai'"
        );
        $stmt->execute();
        return (float)$stmt->fetchColumn();
    }

    public function statsTotalTransaksiSelesai(): int {
        $stmt = $this->pdo->query("SELECT COUNT(*) FROM parkir WHERE status = 'selesai'");
        return (int)$stmt->fetchColumn();
    }
}