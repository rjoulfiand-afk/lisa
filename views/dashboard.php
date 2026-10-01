<?php
/**
 * View: Dashboard
 * Modern Minimalist & Professional Theme (Distinct & Elegant)
 */
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — Sistem Informasi Parkir</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        :root {
            --pink-primary: #ec4899;
            --pink-hover: #db2777;
            --pink-soft: #fdf2f8;
            --pink-border: #fce7f3;
            --bg-page: #faf6f8;
            --card-border: #f1e4eb;
        }

        body {
            background-color: var(--bg-page);
            color: #334155;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        }

        /* Modern KPI Cards */
        .kpi-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 12px;
            padding: 1.25rem 1.4rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(236, 72, 153, 0.06);
        }
        .kpi-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
        }
        .kpi-val {
            font-size: 1.75rem;
            font-weight: 700;
            color: #1e293b;
            line-height: 1.2;
        }

        /* Table Card Container */
        .main-table-card {
            background: #ffffff;
            border: 1px solid var(--card-border);
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.02);
            overflow: hidden;
        }

        .table-toolbar {
            padding: 1rem 1.4rem;
            background: #ffffff;
            border-bottom: 1px solid var(--card-border);
        }

        /* Modern Custom Table */
        .custom-table thead th {
            background: #faf4f7;
            color: #64748b;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            border-bottom: 1px solid var(--card-border);
            padding: 0.85rem 1rem;
        }
        .custom-table tbody td {
            padding: 0.85rem 1rem;
            vertical-align: middle;
            font-size: 0.875rem;
            color: #334155;
            border-bottom: 1px solid #f8eff3;
        }
        .custom-table tbody tr:hover {
            background-color: #fdfbfd;
        }

        /* Clean Plate Box */
        .plate-box {
            display: inline-block;
            background: #ffffff;
            color: #0f172a;
            font-weight: 700;
            font-size: 0.88rem;
            letter-spacing: 0.5px;
            padding: 0.2rem 0.55rem;
            border-radius: 6px;
            border: 1.5px solid #cbd5e1;
            box-shadow: 0 1px 2px rgba(0,0,0,0.04);
        }

        /* Badges */
        .badge-r2 {
            background-color: #fdf2f8;
            color: #db2777;
            border: 1px solid #fbcfe8;
            font-weight: 600;
            font-size: 0.78rem;
            padding: 0.35rem 0.6rem;
            border-radius: 20px;
        }
        .badge-r4 {
            background-color: #f1f5f9;
            color: #475569;
            border: 1px solid #e2e8f0;
            font-weight: 600;
            font-size: 0.78rem;
            padding: 0.35rem 0.6rem;
            border-radius: 20px;
        }

        /* Buttons */
        .btn-theme-pink {
            background-color: var(--pink-primary);
            color: #ffffff;
            font-weight: 600;
            font-size: 0.84rem;
            border: none;
            padding: 0.45rem 0.95rem;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: all 0.15s ease;
        }
        .btn-theme-pink:hover {
            background-color: var(--pink-hover);
            color: #ffffff;
        }

        .btn-keluar-pink {
            background: #ffffff;
            color: #db2777;
            border: 1px solid #f472b6;
            font-size: 0.8rem;
            font-weight: 600;
            padding: 0.3rem 0.7rem;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.15s ease;
        }
        .btn-keluar-pink:hover {
            background: #db2777;
            color: #ffffff;
            border-color: #db2777;
        }

        .btn-action-del {
            background: #ffffff;
            color: #94a3b8;
            border: 1px solid #e2e8f0;
            font-size: 0.8rem;
            padding: 0.3rem 0.6rem;
            border-radius: 6px;
            transition: all 0.15s ease;
        }
        .btn-action-del:hover {
            color: #e11d48;
            border-color: #fecdd3;
            background: #fff1f2;
        }
    </style>
</head>
<body>

    <!-- Include Navbar -->
    <?php require_once __DIR__ . '/layout/navbar.php'; ?>

    <div class="container py-4">

        <!-- Flash Alert -->
        <?php if (!empty($_SESSION['flash_sukses'])): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: '<?= addslashes($_SESSION['flash_sukses']) ?>',
                timer: 2000,
                showConfirmButton: false
            });
        </script>
        <?php unset($_SESSION['flash_sukses']); endif; ?>

        <!-- 3 KPI Summary Cards (Modern SaaS Minimalist Style) -->
        <div class="row g-3 mb-4">
            <!-- 1. Parkir Aktif -->
            <div class="col-md-4">
                <div class="kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Kendaraan Aktif</span>
                        <div class="kpi-icon" style="background-color: #fdf2f8; color: #db2777;">
                            <i class="fa-solid fa-car-side"></i>
                        </div>
                    </div>
                    <div class="kpi-val"><?= (int)($statsAktif ?? count($kendaraanAktif ?? [])) ?> <span class="fs-6 text-muted fw-normal">Unit</span></div>
                    <div class="small text-muted mt-1"><i class="fa-solid fa-circle text-success me-1" style="font-size: 7px;"></i>Sedang berada di area parkir</div>
                </div>
            </div>

            <!-- 2. Transaksi Selesai -->
            <div class="col-md-4">
                <div class="kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Kendaraan Keluar</span>
                        <div class="kpi-icon" style="background-color: #ecfdf5; color: #059669;">
                            <i class="fa-solid fa-clipboard-check"></i>
                        </div>
                    </div>
                    <div class="kpi-val"><?= (int)($statsSelesai ?? 0) ?> <span class="fs-6 text-muted fw-normal">Unit</span></div>
                    <div class="small text-muted mt-1">Transaksi selesai hari ini</div>
                </div>
            </div>

            <!-- 3. Total Pendapatan -->
            <div class="col-md-4">
                <div class="kpi-card">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted small fw-semibold">Penerimaan Kas</span>
                        <div class="kpi-icon" style="background-color: #fffbeb; color: #d97706;">
                            <i class="fa-solid fa-coins"></i>
                        </div>
                    </div>
                    <div class="kpi-val text-dark">Rp <?= number_format((float)($statsPendapatan ?? 0), 0, ',', '.') ?></div>
                    <div class="small text-muted mt-1">Total pendapatan hari ini</div>
                </div>
            </div>
        </div>

        <!-- Tabel Kendaraan Aktif Card -->
        <div class="main-table-card">
            <!-- Toolbar: Judul, Search Real-time & Tombol Tambah -->
            <div class="table-toolbar d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <h6 class="fw-bold mb-0 text-dark">Daftar Kendaraan Terparkir</h6>
                    
                </div>

                <div class="d-flex align-items-center gap-2">
                    <!-- Live Search Box Plat -->
                    <div class="input-group input-group-sm" style="width: 210px;">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </span>
                        <input type="text" id="cariPlatInput" class="form-control border-start-0 ps-0" placeholder="Cari plat nomor...">
                    </div>

                    <!-- Tombol Catat Masuk -->
                    <a href="index.php?page=parkir_masuk" class="btn-theme-pink">
                        <i class="fa-solid fa-plus"></i> Parkir Masuk
                    </a>
                </div>
            </div>

            <!-- Tabel Data -->
            <div class="table-responsive">
                <table class="table custom-table mb-0 align-middle" id="tabelAktif">
                    <thead>
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th>No. Tiket</th>
                            <th>Nomor Plat</th>
                            <th>Jenis Kendaraan</th>
                            <th>Waktu Masuk</th>
                            <th class="text-end" style="width: 170px;">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($kendaraanAktif)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-square-parking fa-2x mb-2 text-secondary opacity-25 d-block"></i>
                                    Tidak ada kendaraan yang sedang parkir saat ini.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php 
                            $no = 1;
                            foreach ($kendaraanAktif as $row): 
                                $idParkir = $row['id'] ?? $row['id_parkir'] ?? 0;
                                $isRoda2  = in_array(strtolower(str_replace(' ', '', $row['jenis_kendaraan'])), ['roda2', 'motor']);
                            ?>
                            <tr>
                                <td class="text-center text-muted small"><?= $no++ ?></td>
                                <td>
                                    <span class="text-secondary font-monospace small">
                                        <?= htmlspecialchars($row['nomor_id'] ?? '-') ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="plate-box">
                                        <?= htmlspecialchars($row['nomor_plat']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($isRoda2): ?>
                                        <span class="badge-r2">
                                            <i class="fa-solid fa-motorcycle me-1"></i> Roda 2
                                        </span>
                                    <?php else: ?>
                                        <span class="badge-r4">
                                            <i class="fa-solid fa-car me-1"></i> Roda 4
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="text-secondary small">
                                        <i class="fa-regular fa-clock me-1 text-muted"></i><?= date('d/m/Y · H:i', strtotime($row['waktu_masuk'])) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex align-items-center gap-1">
                                        <a href="index.php?page=parkir_keluar&cari_plat=<?= urlencode($row['nomor_plat']) ?>" 
                                           class="btn-keluar-pink" title="Proses kendaraan keluar">
                                            <i class="fa-solid fa-arrow-right-from-bracket me-1"></i> Keluar
                                        </a>
                                        <button type="button" 
                                                onclick="konfirmasiHapus(<?= (int)$idParkir ?>, '<?= htmlspecialchars($row['nomor_plat']) ?>')" 
                                                class="btn-action-del" title="Hapus catatan">
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

    </div>


    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script Live Filter & Konfirmasi Hapus -->
    <script>
    // Live Search Plat Nomor
    document.getElementById('cariPlatInput').addEventListener('keyup', function() {
        const query = this.value.toUpperCase();
        const rows = document.querySelectorAll('#tabelAktif tbody tr');

        rows.forEach(row => {
            const plateElem = row.querySelector('.plate-box');
            if (plateElem) {
                const text = plateElem.textContent || plateElem.innerText;
                row.style.display = (text.toUpperCase().indexOf(query) > -1) ? '' : 'none';
            }
        });
    });

    // Konfirmasi Hapus SweetAlert2
    function konfirmasiHapus(id, plat) {
        Swal.fire({
            title: 'Hapus Kendaraan?',
            html: 'Data plat <strong>' + plat + '</strong> akan dihapus permanen dari sistem.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#94a3b8',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'index.php?page=hapus&id=' + id + '&from=dashboard';
            }
        });
    }
    </script>
</body>
</html>