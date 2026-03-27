<?php
/**
 * Export Excel (CSV) untuk Data Barang
 * Format profesional
 */

session_start();
require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

// Ambil semua data barang
$stmt = $pdo->query("SELECT * FROM barang ORDER BY nama_barang ASC");
$data_barang = $stmt->fetchAll();

// Set headers untuk download
$filename = 'data_barang_' . date('Y-m-d_His') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Open output stream
$output = fopen('php://output', 'w');

// UTF-8 BOM for Excel compatibility
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

// Header info
fputcsv($output, ['LAPORAN DATA BARANG']);
fputcsv($output, ['INVENTARIS AZIZAH']);
fputcsv($output, ['']);
fputcsv($output, ['Tanggal Export:', date('d/m/Y H:i:s')]);
fputcsv($output, ['']);

// Column headers
fputcsv($output, ['No', 'Nama Barang', 'Stok', 'Harga Satuan', 'Nilai Total', 'Status Stok', 'Terakhir Update']);

// Data rows
$no = 1;
$total_stok = 0;
$total_nilai = 0;

foreach ($data_barang as $b) {
    $nilai = $b['stok'] * $b['harga'];
    $total_stok += $b['stok'];
    $total_nilai += $nilai;
    
    // Status stok
    if ($b['stok'] > 10) {
        $status = 'Aman';
    } elseif ($b['stok'] > 0) {
        $status = 'Rendah';
    } else {
        $status = 'Habis';
    }
    
    fputcsv($output, [
        $no++,
        $b['nama_barang'],
        $b['stok'],
        $b['harga'],
        $nilai,
        $status,
        date('d/m/Y H:i', strtotime($b['updated_at']))
    ]);
}

// Summary
fputcsv($output, ['']);
fputcsv($output, ['RINGKASAN']);
fputcsv($output, ['Total Jenis Barang:', count($data_barang)]);
fputcsv($output, ['Total Stok:', $total_stok, 'unit']);
fputcsv($output, ['Total Nilai Inventaris:', 'Rp ' . number_format($total_nilai, 0, ',', '.')]);

fclose($output);
exit;
