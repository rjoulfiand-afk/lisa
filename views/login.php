<?php
/**
 * View: Login — SiParkir (Desain Bersih & Elegan)
 */
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Petugas — SiParkir</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #fff5f6;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 380px;
        }

        .login-card {
            background: #ffffff;
            border: 1px solid #fecdd3;
            border-radius: 12px;
            padding: 36px 30px;
            box-shadow: 0 10px 25px -5px rgba(225, 29, 72, 0.08);
        }

        .brand-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-bottom: 4px;
        }

        .brand-subtitle {
            font-size: 0.85rem;
            color: #64748b;
            text-align: center;
            margin-bottom: 26px;
        }

        .form-label {
            font-size: 0.82rem;
            font-weight: 600;
            color: #334155;
            margin-bottom: 6px;
        }

        .form-control-custom {
            width: 100%;
            padding: 10px 14px;
            font-size: 0.92rem;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            background-color: #ffffff;
        }

        .form-control-custom:focus {
            border-color: #f43f5e;
            box-shadow: 0 0 0 3px rgba(244, 63, 94, 0.15);
        }

        .password-wrap {
            position: relative;
        }

        .btn-eye {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px;
        }

        .btn-eye:hover {
            color: #f43f5e;
        }

        .btn-submit {
            background-color: #f43f5e;
            color: #ffffff;
            border: none;
            border-radius: 8px;
            padding: 11px;
            font-weight: 600;
            font-size: 0.95rem;
            width: 100%;
            margin-top: 10px;
            transition: background-color 0.2s, transform 0.1s;
        }

        .btn-submit:hover {
            background-color: #e11d48;
        }

        .footer-text {
            text-align: center;
            font-size: 0.78rem;
            color: #94a3b8;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-card">
        <!-- Judul Bersih Tanpa Huruf P Konyol -->
        <div class="brand-title">
            <i class="fa-solid fa-square-parking text-danger fs-3"></i>
            <span>SiParkir</span>
        </div>
        <div class="brand-subtitle">
            Sistem Informasi Pengelolaan Parkir
        </div>

        <form method="POST" action="<?= BASE_URL ?>index.php?page=login" novalidate>
            <div class="mb-3">
                <label class="form-label" for="username">Username</label>
                <input type="text" 
                       id="username" 
                       name="username"
                       class="form-control-custom" 
                       placeholder="Masukkan username..."
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                       required 
                       autofocus 
                       autocomplete="username">
            </div>

            <div class="mb-4">
                <label class="form-label" for="password">Kata Sandi</label>
                <div class="password-wrap">
                    <input type="password" 
                           id="password" 
                           name="password"
                           class="form-control-custom pe-5" 
                           placeholder="Masukkan kata sandi..."
                           required 
                           autocomplete="current-password">
                    <button type="button" class="btn-eye" onclick="togglePass()" aria-label="Lihat password">
                        <i class="fa-solid fa-eye" id="pass-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-arrow-right-to-bracket me-1"></i> Masuk ke Sistem
            </button>
        </form>
    </div>

    <div class="footer-text">
        SiParkir &copy; <?= date('Y') ?> &bull; SMK Rekayasa Perangkat Lunak
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function togglePass() {
    const inp = document.getElementById('password');
    const eye = document.getElementById('pass-eye');
    if (inp.type === 'password') {
        inp.type = 'text';
        eye.className = 'fa-solid fa-eye-slash';
    } else {
        inp.type = 'password';
        eye.className = 'fa-solid fa-eye';
    }
}

<?php
$status = $_GET['status'] ?? '';
$msg    = htmlspecialchars($_GET['msg'] ?? '');
if ($status === 'error' && $msg): ?>
Swal.fire({
    icon: 'error',
    title: 'Login Gagal',
    text: '<?= addslashes($msg) ?>',
    confirmButtonColor: '#f43f5e'
});
<?php elseif ($status === 'logout'): ?>
Swal.fire({
    toast: true,
    position: 'top-end',
    icon: 'info',
    title: 'Anda telah berhasil keluar.',
    showConfirmButton: false,
    timer: 2500
});
<?php endif; ?>
</script>

</body>
</html>