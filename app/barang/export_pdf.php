<?php
/**
 * Export PDF untuk Data Barang
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

// Hitung total
$total_items = count($data_barang);
$total_stok = array_sum(array_column($data_barang, 'stok'));
$total_nilai = 0;
foreach ($data_barang as $b) {
    $total_nilai += $b['stok'] * $b['harga'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Data Barang - <?= date('d-m-Y') ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body { 
            font-family: 'Segoe UI', Arial, sans-serif; 
            font-size: 11px; 
            line-height: 1.4; 
            color: #333; 
            background: white;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Header */
        .report-header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 3px solid #3b82f6;
            margin-bottom: 25px;
        }
        
        .report-header h1 {
            font-size: 22px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 5px;
        }
        
        .report-header .subtitle {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 15px;
        }
        
        .report-header .company {
            font-size: 16px;
            font-weight: 600;
            color: #3b82f6;
        }
        
        /* Info Box */
        .info-box {
            display: flex;
            justify-content: space-between;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .info-item {
            text-align: center;
        }
        
        .info-item .label {
            font-size: 10px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-item .value {
            font-size: 18px;
            font-weight: 700;
            color: #1e293b;
        }
        
        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        th {
            background: #3b82f6;
            color: white;
            font-weight: 600;
            text-align: left;
            padding: 12px 10px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        th:first-child { border-radius: 6px 0 0 0; }
        th:last-child { border-radius: 0 6px 0 0; }
        
        td {
            padding: 10px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        tr:nth-child(even) {
            background: #f8fafc;
        }
        
        tr:hover {
            background: #eff6ff;
        }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .stok-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 10px;
        }
        
        .stok-high {
            background: #dcfce7;
            color: #15803d;
        }
        
        .stok-medium {
            background: #fef3c7;
            color: #b45309;
        }
        
        .stok-low {
            background: #fee2e2;
            color: #dc2626;
        }
        
        /* Summary */
        .summary {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 1px solid #3b82f6;
            border-radius: 8px;
            padding: 20px;
            margin-top: 20px;
        }
        
        .summary h3 {
            color: #1e40af;
            margin-bottom: 15px;
            font-size: 14px;
        }
        
        .summary-grid {
            display: flex;
            justify-content: space-around;
        }
        
        .summary-item {
            text-align: center;
        }
        
        .summary-item .value {
            font-size: 20px;
            font-weight: 700;
            color: #1e40af;
        }
        
        .summary-item .label {
            font-size: 11px;
            color: #3b82f6;
        }
        
        /* Footer */
        .report-footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #94a3b8;
            font-size: 10px;
        }
        
        /* Print Styles */
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
            .container { max-width: 100%; padding: 15px; }
        }
        
        /* Action Buttons */
        .action-buttons {
            text-align: center;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8fafc;
            border-radius: 8px;
        }
        
        .action-buttons button {
            padding: 10px 25px;
            font-size: 13px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin: 0 5px;
            font-weight: 500;
        }
        
        .btn-print {
            background: #3b82f6;
            color: white;
        }
        
        .btn-close {
            background: #e2e8f0;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="action-buttons no-print">
            <button class="btn-print" onclick="window.print()">Cetak / Simpan PDF</button>
            <button class="btn-close" onclick="window.close()">Tutup</button>
        </div>
        
        <div class="report-header">
            <div class="company">INVENTARIS AZIZAH</div>
            <h1>LAPORAN DATA BARANG</h1>
            <div class="subtitle">Dicetak pada: <?= date('d F Y, H:i') ?> WIB</div>
        </div>
        
        <div class="info-box">
            <div class="info-item">
                <div class="label">Total Jenis Barang</div>
                <div class="value"><?= number_format($total_items) ?></div>
            </div>
            <div class="info-item">
                <div class="label">Total Stok</div>
                <div class="value"><?= number_format($total_stok) ?></div>
            </div>
            <div class="info-item">
                <div class="label">Total Nilai Inventaris</div>
                <div class="value">Rp <?= number_format($total_nilai, 0, ',', '.') ?></div>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 35%;">Nama Barang</th>
                    <th style="width: 12%;" class="text-center">Stok</th>
                    <th style="width: 20%;" class="text-right">Harga Satuan</th>
                    <th style="width: 20%;" class="text-right">Nilai Total</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($data_barang as $b): 
                    $nilai = $b['stok'] * $b['harga'];
                    if ($b['stok'] > 10) {
                        $stok_class = 'stok-high';
                    } elseif ($b['stok'] > 0) {
                        $stok_class = 'stok-medium';
                    } else {
                        $stok_class = 'stok-low';
                    }
                ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td><?= htmlspecialchars($b['nama_barang']) ?></td>
                    <td class="text-center">
                        <span class="stok-badge <?= $stok_class ?>"><?= $b['stok'] ?></span>
                    </td>
                    <td class="text-right">Rp <?= number_format($b['harga'], 0, ',', '.') ?></td>
                    <td class="text-right">Rp <?= number_format($nilai, 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="summary">
            <h3>RINGKASAN INVENTARIS</h3>
            <div class="summary-grid">
                <div class="summary-item">
                    <div class="value"><?= number_format($total_items) ?></div>
                    <div class="label">Jenis Barang</div>
                </div>
                <div class="summary-item">
                    <div class="value"><?= number_format($total_stok) ?></div>
                    <div class="label">Total Unit</div>
                </div>
                <div class="summary-item">
                    <div class="value">Rp <?= number_format($total_nilai, 0, ',', '.') ?></div>
                    <div class="label">Nilai Inventaris</div>
                </div>
            </div>
        </div>
        
        <div class="report-footer">
            <p>Dokumen ini digenerate oleh Sistem Inventaris Azizah</p>
            <p>UKK Level 3 Web Development &bull; <?= date('Y') ?></p>
        </div>
    </div>
</body>
</html>
