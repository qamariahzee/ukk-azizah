<?php
/**
 * Transaksi Barang Masuk
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

$stmt = $pdo->query("SELECT id, nama_barang, stok FROM barang ORDER BY nama_barang");
$daftar_barang = $stmt->fetchAll();

$error = '';

// Proses form SEBELUM output HTML
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_barang = (int) ($_POST['id_barang'] ?? 0);
    $jumlah = (int) ($_POST['jumlah'] ?? 0);
    $keterangan = trim($_POST['keterangan'] ?? '');
    
    if ($id_barang <= 0) {
        $error = 'Pilih barang terlebih dahulu!';
    } elseif ($jumlah <= 0) {
        $error = 'Jumlah harus lebih dari 0!';
    } else {
        try {
            $pdo->beginTransaction();
            
            $stmt = $pdo->prepare("INSERT INTO transaksi (id_barang, jenis_transaksi, jumlah, keterangan) VALUES (?, 'masuk', ?, ?)");
            $stmt->execute([$id_barang, $jumlah, $keterangan]);
            
            $stmt = $pdo->prepare("UPDATE barang SET stok = stok + ? WHERE id = ?");
            $stmt->execute([$jumlah, $id_barang]);
            
            $pdo->commit();
            
            $stmt = $pdo->prepare("SELECT nama_barang FROM barang WHERE id = ?");
            $stmt->execute([$id_barang]);
            $barang = $stmt->fetch();
            
            $_SESSION['flash_message'] = "Barang masuk berhasil! {$barang['nama_barang']} +{$jumlah}";
            $_SESSION['flash_type'] = 'success';
            header('Location: riwayat.php');
            exit;
            
        } catch (Exception $e) {
            $pdo->rollback();
            $error = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }
}

// Setelah proses selesai, baru include header
$page_title = 'Barang Masuk';
require_once __DIR__ . '/../includes/header.php';
?>

<div class="page-header">
    <h1>Barang Masuk</h1>
    <p>Catat barang yang masuk ke gudang</p>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header" style="background: #dcfce7; color: #15803d;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="20" height="20" style="display: inline-block; vertical-align: middle; margin-right: 8px;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15m0-3-3-3m0 0-3 3m3-3V15" />
                </svg>
                Barang Masuk
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <?php if (empty($daftar_barang)): ?>
                    <div class="alert alert-warning">
                        Belum ada data barang. <a href="../barang/tambah.php">Tambah barang</a> terlebih dahulu.
                    </div>
                <?php else: ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Pilih Barang <span class="text-danger">*</span></label>
                            <select name="id_barang" class="form-select" required>
                                <option value="">-- Pilih Barang --</option>
                                <?php foreach ($daftar_barang as $b): ?>
                                    <option value="<?= $b['id'] ?>" <?= (isset($_POST['id_barang']) && $_POST['id_barang'] == $b['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($b['nama_barang']) ?> (Stok: <?= $b['stok'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Jumlah Masuk <span class="text-danger">*</span></label>
                            <input type="number" name="jumlah" class="form-control" 
                                   value="<?= htmlspecialchars($_POST['jumlah'] ?? '') ?>"
                                   min="1" required placeholder="Masukkan jumlah">
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="2" placeholder="Contoh: Pembelian dari supplier"><?= htmlspecialchars($_POST['keterangan'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                                Simpan
                            </button>
                            <a href="<?= $base_url ?>/app/dashboard.php" class="btn btn-outline-primary">Kembali</a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Informasi</div>
            <div class="card-body">
                <p class="mb-2"><strong>Logika Barang Masuk:</strong></p>
                <ul class="mb-0 text-muted">
                    <li>Stok akan bertambah otomatis</li>
                    <li>Formula: <code>stok_baru = stok_lama + jumlah</code></li>
                    <li>Keterangan bersifat opsional</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
