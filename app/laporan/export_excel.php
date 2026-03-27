<?php
/**
 * Export Excel (CSV)
 */

session_start();
require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit;
}

$tanggal_awal = $_GET['dari'] ?? date('Y-m-01');
$tanggal_akhir = $_GET['sampai'] ?? date('Y-m-d');
$jenis = $_GET['jenis'] ?? '';

$sql = "SELECT t.*, b.nama_barang, b.harga FROM transaksi t JOIN barang b ON t.id_barang = b.id WHERE DATE(t.tanggal_transaksi) BETWEEN ? AND ?";
$params = [$tanggal_awal, $tanggal_akhir];

if ($jenis) {
    $sql .= " AND t.jenis_transaksi = ?";
    $params[] = $jenis;
}

$sql .= " ORDER BY t.tanggal_transaksi DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data = $stmt->fetchAll();

$filename = 'laporan_transaksi_' . date('Y-m-d_His') . '.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

fputcsv($output, ['LAPORAN TRANSAKSI GUDANG']);
fputcsv($output, ['Periode: ' . date('d/m/Y', strtotime($tanggal_awal)) . ' - ' . date('d/m/Y', strtotime($tanggal_akhir))]);
fputcsv($output, ['Dicetak: ' . date('d/m/Y H:i:s')]);
fputcsv($output, ['']);

fputcsv($output, ['No', 'Tanggal', 'Jam', 'Nama Barang', 'Jenis Transaksi', 'Jumlah', 'Harga Satuan', 'Total Nilai', 'Keterangan']);

$no = 1;
$total_masuk = 0;
$total_keluar = 0;
$nilai_masuk = 0;
$nilai_keluar = 0;

foreach ($data as $row) {
    $total_nilai = $row['jumlah'] * $row['harga'];
    
    if ($row['jenis_transaksi'] === 'masuk') {
        $total_masuk += $row['jumlah'];
        $nilai_masuk += $total_nilai;
    } else {
        $total_keluar += $row['jumlah'];
        $nilai_keluar += $total_nilai;
    }
    
    fputcsv($output, [
        $no++,
        date('d/m/Y', strtotime($row['tanggal_transaksi'])),
        date('H:i:s', strtotime($row['tanggal_transaksi'])),
        $row['nama_barang'],
        ucfirst($row['jenis_transaksi']),
        $row['jumlah'],
        $row['harga'],
        $total_nilai,
        $row['keterangan'] ?: '-'
    ]);
}

fputcsv($output, ['']);
fputcsv($output, ['RINGKASAN']);
fputcsv($output, ['Total Barang Masuk', $total_masuk, 'unit']);
fputcsv($output, ['Total Barang Keluar', $total_keluar, 'unit']);
fputcsv($output, ['Selisih', $total_masuk - $total_keluar, 'unit']);
fputcsv($output, ['']);
fputcsv($output, ['Nilai Barang Masuk', 'Rp ' . number_format($nilai_masuk, 0, ',', '.')]);
fputcsv($output, ['Nilai Barang Keluar', 'Rp ' . number_format($nilai_keluar, 0, ',', '.')]);

fclose($output);
exit;
