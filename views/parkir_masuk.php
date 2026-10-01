<?php
/**
 * View: Parkir Masuk
 * Simple & Clean Bootstrap Form with Complete Rates
 */
if (session_status() === PHP_SESSION_NONE) session_start();
$baseUrl = defined('BASE_URL') ? BASE_URL : '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parkir Masuk — SiParkir</title>
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

        /* Pilihan Jenis Kendaraan Sederhana */
        .pilihan-kendaraan {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 10px 8px;
            text-align: center;
            cursor: pointer;
            background: #ffffff;
            transition: all 0.15s ease;
            height: 100%;
        }
        .pilihan-kendaraan:hover {
            background: #f8fafc;
        }
        .btn-check:checked + .pilihan-kendaraan {
            border-color: #ec4899;
            background-color: #fdf2f8;
        }
        .btn-check:checked + .pilihan-kendaraan .title-kendaraan {
            color: #db2777;
        }
    </style>
</head>
<body>

    <!-- Include Navbar -->
    <?php require_once __DIR__ . '/layout/navbar.php'; ?>

    <main class="container py-4">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">

                <!-- Kartu Form Sederhana -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 border-bottom">
                        <h6 class="mb-0 fw-bold text-dark">
                            <i class="fa-solid fa-arrow-right-to-bracket me-2 text-secondary"></i>Parkir Masuk
                        </h6>
                    </div>
                    
                    <div class="card-body p-4">
                        <form action="<?= $baseUrl ?>index.php?page=parkir_masuk" method="POST">
                            
                            <!-- Nomor Plat -->
                            <div class="mb-3">
                                <label for="nomor_plat" class="form-label small fw-semibold text-muted">Nomor Plat</label>
                                <input
                                    type="text"
                                    id="nomor_plat"
                                    name="nomor_plat"
                                    class="form-control"
                                    placeholder="Contoh: L 1234 AB"
                                    oninput="this.value = this.value.toUpperCase()"
                                    autocomplete="off"
                                    required
                                    autofocus
                                    maxlength="15"
                                >
                            </div>

                            <!-- Jenis Kendaraan dengan Keterangan Jam Lengkap -->
                            <div class="mb-4">
                                <label class="form-label small fw-semibold text-muted">Jenis Kendaraan</label>
                                <div class="row g-2">
                                    <!-- Roda 2 -->
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="jenis_kendaraan" id="roda2" value="roda2" checked>
                                        <label class="pilihan-kendaraan d-block" for="roda2">
                                            <div class="fw-bold title-kendaraan">Roda 2</div>
                                            <div class="fw-semibold text-dark mt-1" style="font-size: 0.9rem;">
                                                Rp 2.000 <small class="text-muted fw-normal">(≤ 2 jam)</small>
                                            </div>
                                            <div class="text-muted" style="font-size: 0.72rem;">+ Rp 1.000 / jam berikutnya</div>
                                        </label>
                                    </div>

                                    <!-- Roda 4 -->
                                    <div class="col-6">
                                        <input type="radio" class="btn-check" name="jenis_kendaraan" id="roda4" value="roda4">
                                        <label class="pilihan-kendaraan d-block" for="roda4">
                                            <div class="fw-bold title-kendaraan">Roda 4</div>
                                            <div class="fw-semibold text-dark mt-1" style="font-size: 0.9rem;">
                                                Rp 5.000 <small class="text-muted fw-normal">(≤ 2 jam)</small>
                                            </div>
                                            <div class="text-muted" style="font-size: 0.72rem;">+ Rp 1.000 / jam berikutnya</div>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Tombol Simpan -->
                            <button type="submit" class="btn btn-pink w-100 py-2 fw-semibold">
                                Simpan Kendaraan Masuk
                            </button>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert Notifikasi -->
    <script>
    <?php
    $status = $_GET['status'] ?? $_GET['pesan'] ?? '';
    $plat   = htmlspecialchars(addslashes($_GET['plat'] ?? ''));
    $msg    = htmlspecialchars(addslashes($_GET['msg'] ?? ''));
    ?>
    <?php if ($status === 'sukses_masuk'): ?>
    Swal.fire({
        icon: 'success',
        title: 'Berhasil',
        text: 'Data kendaraan masuk telah disimpan.',
        timer: 2000,
        showConfirmButton: false
    });
    <?php elseif ($status === 'error'): ?>
    Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: '<?= $msg ?: "Terjadi kesalahan saat memproses data." ?>',
        confirmButtonColor: '#ec4899'
    });
    <?php endif; ?>
    </script>
</body>
</html>