<?php
/**
 * Hapus Barang
 */

session_start();
require_once __DIR__ . '/../config/koneksi.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Ambil ID dari URL
$id = (int) ($_GET['id'] ?? 0);

if ($id > 0) {
    // Cek apakah barang ada
    $stmt = $pdo->prepare("SELECT nama_barang FROM barang WHERE id = ?");
    $stmt->execute([$id]);
    $barang = $stmt->fetch();
    
    if ($barang) {
        // Hapus barang (transaksi terkait akan terhapus otomatis karena ON DELETE CASCADE)
        $stmt = $pdo->prepare("DELETE FROM barang WHERE id = ?");
        $stmt->execute([$id]);
        
        $_SESSION['flash_message'] = "Barang '{$barang['nama_barang']}' berhasil dihapus!";
        $_SESSION['flash_type'] = 'success';
    } else {
        $_SESSION['flash_message'] = 'Barang tidak ditemukan!';
        $_SESSION['flash_type'] = 'error';
    }
} else {
    $_SESSION['flash_message'] = 'ID barang tidak valid!';
    $_SESSION['flash_type'] = 'error';
}

// Redirect kembali ke list
header('Location: index.php');
exit;
