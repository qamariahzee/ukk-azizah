<?php
/**
 * Tambah Barang
 * Proses form SEBELUM include header
 */

session_start();
date_default_timezone_set('Asia/Jakarta');

// Cek login
require_once __DIR__ . '/../config/config.php';
if (!isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/app/auth/login.php');
    exit;
}

require_once __DIR__ . '/../config/koneksi.php';

$error = '';

// Proses form SEBELUM output HTML
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_barang = trim($_POST['nama_barang'] ?? '');
    $stok = (int) ($_POST['stok'] ?? 0);
    $harga = (float) ($_POST['harga'] ?? 0);
    
    if (empty($nama_barang)) {
        $error = 'Nama barang tidak boleh kosong!';
    } elseif ($stok < 0) {
        $error = 'Stok tidak boleh negatif!';
    } elseif ($harga < 0) {
        $error = 'Harga tidak boleh negatif!';
    } else {
        $stmt = $pdo->prepare("INSERT INTO barang (nama_barang, stok, harga) VALUES (?, ?, ?)");
        
        if ($stmt->execute([$nama_barang, $stok, $harga])) {
            $_SESSION['flash_message'] = 'Barang berhasil ditambahkan!';
            $_SESSION['flash_type'] = 'success';
            header('Location: index.php');
            exit;
        } else {
            $error = 'Gagal menyimpan data!';
        }
    }
}

// Setelah proses selesai, baru include header
$page_title = 'Tambah Barang';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1>Tambah Barang Baru</h1>
    <p>Tambahkan data barang baru ke gudang</p>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Nama Barang <span class="text-danger">*</span></label>
                        <input type="text" name="nama_barang" class="form-control" 
                               value="<?= htmlspecialchars($_POST['nama_barang'] ?? '') ?>"
                               placeholder="Contoh: Laptop ASUS ROG" required autofocus>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Stok Awal</label>
                        <input type="number" name="stok" class="form-control" 
                               value="<?= htmlspecialchars($_POST['stok'] ?? '0') ?>"
                               min="0" placeholder="0">
                        <small class="text-muted">Stok awal barang (bisa 0)</small>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Harga (Rp)</label>
                        <input type="number" name="harga" class="form-control" 
                               value="<?= htmlspecialchars($_POST['harga'] ?? '0') ?>"
                               min="0" placeholder="0">
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            Simpan
                        </button>
                        <a href="index.php" class="btn btn-outline-primary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
                    