<?php
/**
 * Export PDF
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

$total_masuk = 0;
$total_keluar = 0;
foreach ($data as $row) {
    if ($row['jenis_transaksi'] === 'masuk') {
        $total_masuk += $row['jumlah'];
    } else {
        $total_keluar += $row['jumlah'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi - <?= date('d-m-Y') ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; line-height: 1.5; color: #333; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #3b82f6; }
        .header h1 { font-size: 24px; margin-bottom: 5px; color: #1e293b; }
        .header p { color: #64748b; }
        .info { margin-bottom: 20px; padding: 15px; background: #f8fafc; border-radius: 5px; }
        .info p { margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #e2e8f0; padding: 10px 8px; text-align: left; }
        th { background: #3b82f6; color: white; font-weight: 600; }
        tr:nth-child(even) { background: #f8fafc; }
        .masuk { color: #22c55e; font-weight: 600; }
        .keluar { color: #ef4444; font-weight: 600; }
        .summary { margin-top: 20px; padding: 15px; background: #eff6ff; border: 1px solid #3b82f6; border-radius: 5px; }
        .summary h3 { margin-bottom: 10px; color: #1e40af; }
        .footer { margin-top: 30px; text-align: center; color: #64748b; font-size: 11px; }
        @media print { .no-print { display: none; } body { padding: 0; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px; text-align: center;">
        <button onclick="window.print()" style="padding: 10px 30px; font-size: 14px; cursor: pointer; background: #3b82f6; color: white; border: none; border-radius: 5px;">
            Cetak / Simpan PDF
        </button>
        <button onclick="window.close()" style="padding: 10px 30px; font-size: 14px; cursor: pointer; margin-left: 10px; background: #e2e8f0; border: none; border-radius: 5px;">
            Tutup
        </button>
    </div>
    
    <div class="header">
        <h1>LAPORAN TRANSAKSI GUDANG</h1>
        <p>Sistem Inventaris Aizah - UKK Level 3</p>
    </div>
    
    <div class="info">
        <p><strong>Periode:</strong> <?= date('d/m/Y', strtotime($tanggal_awal)) ?> - <?= date('d/m/Y', strtotime($tanggal_akhir)) ?></p>
        <p><strong>Jenis:</strong> <?= $jenis ? ucfirst($jenis) : 'Semua Transaksi' ?></p>
        <p><strong>Dicetak:</strong> <?= date('d/m/Y H:i:s') ?></p>
        <p><strong>Total Data:</strong> <?= count($data) ?> transaksi</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 25%;">Barang</th>
                <th style="width: 10%;">Jenis</th>
                <th style="width: 8%;">Jumlah</th>
                <th style="width: 15%;">Harga</th>
                <th style="width: 22%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($data)): ?>
                <tr><td colspan="7" style="text-align: center; padding: 30px; color: #64748b;">Tidak ada data transaksi untuk periode ini</td></tr>
            <?php else: ?>
                <?php $no = 1; foreach ($data as $row): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($row['tanggal_transaksi'])) ?></td>
                    <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                    <td><span class="<?= $row['jenis_transaksi'] ?>"><?= ucfirst($row['jenis_transaksi']) ?></span></td>
                    <td style="text-align: center;"><?= $row['jumlah'] ?></td>
                    <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                    <td><?= htmlspecialchars($row['keterangan'] ?: '-') ?></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <div class="summary">
        <h3>Ringkasan</h3>
        <table style="border: none;">
            <tr style="background: transparent;">
                <td style="border: none; width: 33%; text-align: center;">
                    <strong style="color: #22c55e;">Total Masuk</strong><br>
                    <span style="font-size: 18px; color: #22c55e;"><?= number_format($total_masuk) ?> unit</span>
                </td>
                <td style="border: none; width: 33%; text-align: center;">
                    <strong style="color: #ef4444;">Total Keluar</strong><br>
                    <span style="font-size: 18px; color: #ef4444;"><?= number_format($total_keluar) ?> unit</span>
                </td>
                <td style="border: none; width: 33%; text-align: center;">
                    <strong style="color: #3b82f6;">Selisih</strong><br>
                    <span style="font-size: 18px; color: #3b82f6;">
                        <?= $total_masuk - $total_keluar >= 0 ? '+' : '' ?><?= number_format($total_masuk - $total_keluar) ?> unit
                    </span>
                </td>
            </tr>
        </table>
    </div>
    
    <div class="footer">
        <p>Dokumen ini digenerate oleh Sistem Inventaris Aizah</p>
        <p>UKK Level 3 Web Development</p>
    </div>
</body>
</html>
