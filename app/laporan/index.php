<?php
/**
 * Halaman Laporan
 */

$page_title = 'Laporan';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/koneksi.php';

$filter_dari = $_GET['dari'] ?? date('Y-m-01');
$filter_sampai = $_GET['sampai'] ?? date('Y-m-d');

$stmt = $pdo->prepare("SELECT t.*, b.nama_barang, b.harga FROM transaksi t JOIN barang b ON t.id_barang = b.id WHERE DATE(t.tanggal_transaksi) BETWEEN ? AND ? ORDER BY t.tanggal_transaksi DESC");
$stmt->execute([$filter_dari, $filter_sampai]);
$data_transaksi = $stmt->fetchAll();

$total_masuk = 0;
$total_keluar = 0;
$nilai_masuk = 0;
$nilai_keluar = 0;

foreach ($data_transaksi as $t) {
    if ($t['jenis_transaksi'] === 'masuk') {
        $total_masuk += $t['jumlah'];
        $nilai_masuk += $t['jumlah'] * $t['harga'];
    } else {
        $total_keluar += $t['jumlah'];
        $nilai_keluar += $t['jumlah'] * $t['harga'];
    }
}

$stmt = $pdo->prepare("SELECT b.nama_barang, SUM(t.jumlah) as total_keluar FROM transaksi t JOIN barang b ON t.id_barang = b.id WHERE t.jenis_transaksi = 'keluar' AND DATE(t.tanggal_transaksi) BETWEEN ? AND ? GROUP BY t.id_barang ORDER BY total_keluar DESC LIMIT 5");
$stmt->execute([$filter_dari, $filter_sampai]);
$barang_terlaris = $stmt->fetchAll();
?>

<div class="page-header">
    <h1>Laporan</h1>
    <p>Ringkasan transaksi dan statistik gudang</p>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Dari Tanggal</label>
                <input type="date" name="dari" class="form-control" value="<?= $filter_dari ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Sampai Tanggal</label>
                <input type="date" name="sampai" class="form-control" value="<?= $filter_sampai ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
            </div>
        </form>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon success">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15m0-3-3-3m0 0-3 3m3-3V15" />
                </svg>
            </div>
            <div class="stat-value"><?= number_format($total_masuk) ?></div>
            <div class="stat-label">Total Masuk</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon danger">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15M9 12l3 3m0 0 3-3m-3 3V2.25" />
                </svg>
            </div>
            <div class="stat-value"><?= number_format($total_keluar) ?></div>
            <div class="stat-label">Total Keluar</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <div class="stat-value" style="font-size: 18px;">Rp <?= number_format($nilai_masuk, 0, ',', '.') ?></div>
            <div class="stat-label">Nilai Masuk</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon warning">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <div class="stat-value" style="font-size: 18px;">Rp <?= number_format($nilai_keluar, 0, ',', '.') ?></div>
            <div class="stat-label">Nilai Keluar</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Export -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header">Export Laporan</div>
            <div class="card-body">
                <p class="text-muted">Periode:</p>
                <p class="fw-bold mb-4"><?= date('d/m/Y', strtotime($filter_dari)) ?> - <?= date('d/m/Y', strtotime($filter_sampai)) ?></p>
                
                <div class="d-grid gap-2">
                    <a href="export_pdf.php?dari=<?= $filter_dari ?>&sampai=<?= $filter_sampai ?>" class="btn btn-danger" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                        Download PDF
                    </a>
                    <a href="export_excel.php?dari=<?= $filter_dari ?>&sampai=<?= $filter_sampai ?>" class="btn btn-success">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="18" height="18">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.375 19.5h17.25m-17.25 0a1.125 1.125 0 0 1-1.125-1.125M3.375 19.5h7.5c.621 0 1.125-.504 1.125-1.125m-9.75 0V5.625m0 12.75v-1.5c0-.621.504-1.125 1.125-1.125m18.375 2.625V5.625m0 12.75c0 .621-.504 1.125-1.125 1.125m1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125m0 3.75h-7.5A1.125 1.125 0 0 1 12 18.375m9.75-12.75c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125m19.5 0v1.5c0 .621-.504 1.125-1.125 1.125M2.25 5.625v1.5c0 .621.504 1.125 1.125 1.125m0 0h17.25m-17.25 0h7.5c.621 0 1.125.504 1.125 1.125M3.375 8.25c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125m17.25-3.75h-7.5c-.621 0-1.125.504-1.125 1.125m8.625-1.125c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125M12 10.875v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 10.875c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125M13.125 12h7.5m-7.5 0c-.621 0-1.125.504-1.125 1.125M20.625 12c.621 0 1.125.504 1.125 1.125v1.5c0 .621-.504 1.125-1.125 1.125m-17.25 0h7.5M12 14.625v-1.5m0 1.5c0 .621-.504 1.125-1.125 1.125M12 14.625c0 .621.504 1.125 1.125 1.125m-2.25 0c.621 0 1.125.504 1.125 1.125m0 1.5v-1.5m0 0c0-.621.504-1.125 1.125-1.125m0 0h7.5" />
                        </svg>
                        Download Excel
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Top Items -->
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header">Barang Paling Banyak Keluar</div>
            <div class="card-body">
                <?php if (empty($barang_terlaris)): ?>
                    <p class="text-muted text-center py-4">Belum ada data</p>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Barang</th>
                                <th>Total Keluar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $rank = 1; foreach ($barang_terlaris as $b): ?>
                            <tr>
                                <td><?= $rank++ ?></td>
                                <td><?= htmlspecialchars($b['nama_barang']) ?></td>
                                <td><span class="badge bg-danger"><?= number_format($b['total_keluar']) ?> unit</span></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
