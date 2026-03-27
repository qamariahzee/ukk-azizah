<?php
/**
 * Halaman Login
 * Menggunakan session untuk autentikasi dasar
 */

// Mulai session jika belum ada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/koneksi.php';

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/app/dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Username dan password harus diisi!';
    } else {
        // Cari user berdasarkan username
        $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        // Verifikasi password pake password_verify (pastikan di DB sudah di-hash)
        if ($user && password_verify($password, $user['password'])) {
            // Login sukses
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Redirect ke dashboard
            header('Location: ' . BASE_URL . '/app/dashboard.php');
            exit;
        } else {
            $error = 'Username atau password salah!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Inventaris Azizah</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= BASE_URL ?>/app/assets/css/vendor/bootstrap.min.css" rel="stylesheet">
    <link href="<?= BASE_URL ?>/app/assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-body-tertiary">
    <div class="d-flex min-vh-100 p-0 m-0">
        <!-- Banner Kiri (Warna Orange) -->
        <div class="d-none d-lg-flex col-lg-5 flex-column justify-content-center align-items-center position-relative text-white" style="background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%); overflow: hidden;">
            <div style="z-index: 10; text-align: center; max-width: 80%;">
                <div class="bg-white text-primary rounded-circle d-inline-flex justify-content-center align-items-center mb-4 shadow" style="width: 80px; height: 80px;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 40px; height: 40px; color: #ea580c;">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                    </svg>
                </div>
                <h1 class="display-5 fw-bold mb-3" style="font-family: inherit;">AZIZAH UKK</h1>
                <p class="fs-5" style="color: rgba(255,255,255,0.85);">Sistem Inventaris Barang Berbasis Web Modern.</p>
            </div>
            
            <!-- Elemen Dekoratif -->
            <div style="position: absolute; top: -50px; left: -50px; width: 300px; height: 300px; background: rgba(255,255,255,0.1); border-radius: 50%;"></div>
            <div style="position: absolute; bottom: -100px; right: -50px; width: 400px; height: 400px; background: rgba(255,255,255,0.05); border-radius: 50%;"></div>
        </div>

        <!-- Area Login Kanan -->
        <div class="col-12 col-lg-7 d-flex justify-content-center align-items-center bg-white">
            <div class="w-100 px-4" style="max-width: 400px;">
                <div class="d-lg-none text-center mb-5">
                    <div class="d-inline-flex justify-content-center align-items-center rounded-3 mb-3" style="width: 60px; height: 60px; background: rgba(234, 88, 12, 0.1);">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width: 32px; height: 32px; color: #ea580c;">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                        </svg>
                    </div>
                    <h2 class="fw-bold" style="color: #0f172a;">Portal UKK</h2>
                </div>

                <div class="mb-5">
                    <h2 class="fw-bold mb-2" style="color: #0f172a;">Sign In</h2>
                    <p class="text-secondary">Akses sistem manajemen inventaris.</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger shadow-sm border-0 d-flex align-items-center gap-2" style="border-radius: 12px; background: #fee2e2; color: #b91c1c;">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" width="20" height="20">
                            <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
                        </svg>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" autocomplete="off">
                    <div class="mb-4">
                        <label class="form-label fw-semibold" style="color: #475569;">Username</label>
                        <input type="text" name="username" class="form-control form-control-lg bg-body-tertiary" 
                               placeholder="admin" 
                               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" 
                               required autofocus style="border-radius: 10px; border-color: #e2e8f0; font-size: 15px; box-shadow: none;">
                    </div>
                    
                    <div class="mb-5">
                        <label class="form-label fw-semibold" style="color: #475569;">Password</label>
                        <input type="password" name="password" class="form-control form-control-lg bg-body-tertiary" 
                               placeholder="••••••••" required style="border-radius: 10px; border-color: #e2e8f0; font-size: 15px; box-shadow: none;">
                    </div>
                    
                    <button type="submit" class="btn btn-lg w-100 fw-bold border-0 shadow-sm text-white" style="border-radius: 10px; padding: 14px 20px; background: #ea580c;">
                        Login ke Sistem
                    </button>
                </form>

                <div class="mt-5 text-center p-3 rounded-3" style="background: #fff7ed; border: 1px dashed #fdba74;">
                    <p class="mb-0 fs-6" style="color: #c2410c;">Demo: <strong>azizah / azizah123</strong></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
