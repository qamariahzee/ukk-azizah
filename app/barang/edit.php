<?php
/**
 * Edit Barang
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

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['flash_message'] = 'ID barang tidak valid!';
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM barang WHERE id = ?");
$stmt->execute([$id]);
$barang = $stmt->fetch();

if (!$barang) {
    $_SESSION['flash_message'] = 'Barang tidak ditemukan!';
    $_SESSION['flash_type'] = 'error';
    header('Location: index.php');
    exit;
}

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
        $stmt = $pdo->prepare("UPDATE barang SET nama_barang = ?, stok = ?, harga = ? WHERE id = ?");
        
        if ($stmt->execute([$nama_barang, $stok, $harga, $id])) {
            $_SESSION['flash_message'] = 'Barang berhasil diupdate!';
            $_SESSION['flash_type'] = 'success';
            header('Location: index.php');
            exit;
        } else {
            $error = 'Gagal mengupdate data!';
        }
    }
}

// Setelah proses selesai, baru include header
$page_title = 'Edit Barang';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1>Edit Barang</h1>
    <p>Ubah data barang</p>
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
                               value="<?= htmlspecialchars($barang['nama_barang']) ?>"
                               required autofocus>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Stok</label>
                        <input type="number" name="stok" class="form-control" 
                               value="<?= $barang['stok'] ?>" min="0">
                        <small class="text-muted">Untuk mengubah stok, gunakan transaksi masuk/keluar</small>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">Harga (Rp)</label>
                        <input type="number" name="harga" class="form-control" 
                               value="<?= $barang['harga'] ?>" min="0">
                    </div>
                    
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            Update
                        </button>
                        <a href="index.php" class="btn btn-outline-primary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
