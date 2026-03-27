<?php
/**
 * Dashboard
 * Clean design with Heroicons
 */

$page_title = 'Dashboard';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/config/koneksi.php';

// Statistik
$stmt = $pdo->query("SELECT COUNT(*) as total FROM barang");
$total_barang = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT SUM(stok) as total FROM barang");
$total_stok = $stmt->fetch()['total'] ?? 0;

$stmt = $pdo->query("SELECT COUNT(*) as total FROM transaksi WHERE DATE(tanggal_transaksi) = CURDATE()");
$transaksi_hari_ini = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM barang WHERE stok < 10");
$stok_rendah = $stmt->fetch()['total'];

// Chart data
$stmt = $pdo->query("
    SELECT 
        DATE(tanggal_transaksi) as tanggal,
        SUM(CASE WHEN jenis_transaksi = 'masuk' THEN jumlah ELSE 0 END) as masuk,
        SUM(CASE WHEN jenis_transaksi = 'keluar' THEN jumlah ELSE 0 END) as keluar
    FROM transaksi 
    WHERE tanggal_transaksi >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY DATE(tanggal_transaksi)
    ORDER BY tanggal ASC
");
$chart_data = $stmt->fetchAll();

// Transaksi Terbaru
$stmt = $pdo->query("
    SELECT t.*, b.nama_barang 
    FROM transaksi t 
    JOIN barang b ON t.id_barang = b.id 
    ORDER BY t.tanggal_transaksi DESC 
    LIMIT 5
");
$transaksi_terbaru = $stmt->fetchAll();
?>

<div class="page-header">
    <h1>Dashboard</h1>
    <p>Selamat datang, <?= htmlspecialchars($_SESSION['username']) ?>!</p>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                </svg>
            </div>
            <div class="stat-value"><?= number_format($total_barang) ?></div>
            <div class="stat-label">Total Barang</div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon success">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                </svg>
            </div>
            <div class="stat-value"><?= number_format($total_stok) ?></div>
            <div class="stat-label">Total Stok</div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon primary">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                </svg>
            </div>
            <div class="stat-value"><?= number_format($transaksi_hari_ini) ?></div>
            <div class="stat-label">Transaksi Hari Ini</div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon warning">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </div>
            <div class="stat-value"><?= number_format($stok_rendah) ?></div>
            <div class="stat-label">Stok Rendah</div>
        </div>
    </div>
</div>

<!-- Charts & Recent -->
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header border-0 pb-0 pt-4 px-4 bg-transparent d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">Grafik Transaksi 7 Hari Terakhir</h6>
            </div>
            <div class="card-body px-4 pb-4">
                <canvas id="chartTransaksi" height="280"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header border-0 pb-0 pt-4 px-4 bg-transparent d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold">Transaksi Terbaru</h6>
                <a href="<?= $base_url ?>/app/transaksi/riwayat.php" class="btn btn-sm text-primary p-0 fw-medium">Lihat Semua</a>
            </div>
            <div class="card-body p-4">
                <div class="list-group list-group-flush">
                    <?php if (empty($transaksi_terbaru)): ?>
                        <div class="text-center text-muted py-4">
                            Belum ada transaksi
                        </div>
                    <?php else: ?>
                        <?php foreach ($transaksi_terbaru as $t): ?>
                            <div class="list-group-item px-0 py-3 border-bottom border-light">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <div class="d-flex gap-3 align-items-center">
                                        <div class="rounded-circle d-flex justify-content-center align-items-center" style="width: 36px; height: 36px; background: <?= $t['jenis_transaksi'] === 'masuk' ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)' ?>; color: <?= $t['jenis_transaksi'] === 'masuk' ? '#10b981' : '#ef4444' ?>;">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="18" height="18">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="<?= $t['jenis_transaksi'] === 'masuk' ? 'M19.5 13.5 12 21m0 0-7.5-7.5M12 21V3' : 'M4.5 10.5 12 3m0 0 7.5 7.5M12 3v18' ?>" />
                                            </svg>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-dark"><?= htmlspecialchars($t['nama_barang']) ?></div>
                                            <small class="text-muted"><?= date('d M Y, H:i', strtotime($t['tanggal_transaksi'])) ?></small>
                                        </div>
                                    </div>
                                    <span class="badge <?= $t['jenis_transaksi'] === 'masuk' ? 'bg-success' : 'bg-danger' ?> bg-opacity-10 <?= $t['jenis_transaksi'] === 'masuk' ? 'text-success' : 'text-danger' ?> px-2 py-1 rounded-pill fw-bold">
                                        <?= $t['jenis_transaksi'] === 'masuk' ? '+' : '-' ?><?= $t['jumlah'] ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-12">
        <h6 class="fw-bold mb-3 ms-1 text-secondary text-uppercase" style="letter-spacing: 1px; font-size: 13px;">Aksi Cepat</h6>
        <div class="row g-3">
            <div class="col-md-3">
                <a href="<?= $base_url ?>/app/barang/tambah.php" class="btn btn-outline-primary bg-white border-0 shadow-sm w-100 py-3 d-flex flex-column align-items-center gap-2 rounded-3 hover-lift">
                    <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(234, 88, 12, 0.1); color: #ea580c; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="20" height="20">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </div>
                    <span class="fw-medium text-dark">Tambah Barang</span>
                </a>
            </div>
            <div class="col-md-3">
                <a href="<?= $base_url ?>/app/transaksi/masuk.php" class="btn btn-outline-success bg-white border-0 shadow-sm w-100 py-3 d-flex flex-column align-items-center gap-2 rounded-3 hover-lift">
                    <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(16, 185, 129, 0.1); color: #10b981; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="20" height="20">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15m0-3-3-3m0 0-3 3m3-3V15" />
                        </svg>
                    </div>
                    <span class="fw-medium text-dark">Barang Masuk</span>
                </a>
            </div>
            <div class="col-md-3">
                <a href="<?= $base_url ?>/app/transaksi/keluar.php" class="btn btn-outline-danger bg-white border-0 shadow-sm w-100 py-3 d-flex flex-column align-items-center gap-2 rounded-3 hover-lift">
                    <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(239, 68, 68, 0.1); color: #ef4444; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="20" height="20">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15M9 12l3 3m0 0 3-3m-3 3V2.25" />
                        </svg>
                    </div>
                    <span class="fw-medium text-dark">Barang Keluar</span>
                </a>
            </div>
            <div class="col-md-3">
                <a href="<?= $base_url ?>/app/laporan/" class="btn btn-outline-info bg-white border-0 shadow-sm w-100 py-3 d-flex flex-column align-items-center gap-2 rounded-3 hover-lift">
                    <div style="width: 40px; height: 40px; border-radius: 12px; background: rgba(14, 165, 233, 0.1); color: #0ea5e9; display: flex; align-items: center; justify-content: center;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="20" height="20">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                        </svg>
                    </div>
                    <span class="fw-medium text-dark">Lihat Laporan</span>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.hover-lift {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-lift:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
}
</style>

<?php 
// Simpan data chart untuk digunakan setelah footer loaded
$chart_json = json_encode($chart_data);
?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<!-- Chart Script - HARUS setelah footer karena Chart.js dimuat di footer -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartData = <?= $chart_json ?>;
    
    // Jika tidak ada data, tampilkan pesan
    if (chartData.length === 0) {
        document.getElementById('chartTransaksi').parentElement.innerHTML = 
            '<div class="text-center text-muted py-5">Belum ada data transaksi 7 hari terakhir</div>';
        return;
    }
    
    const labels = chartData.map(d => {
        const date = new Date(d.tanggal);
        return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
    });
    const masukData = chartData.map(d => parseInt(d.masuk));
    const keluarData = chartData.map(d => parseInt(d.keluar));
    
    const ctx = document.getElementById('chartTransaksi').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Barang Masuk',
                    data: masukData,
                    backgroundColor: '#10b981', /* Warna Success Green */
                    borderRadius: 4
                },
                {
                    label: 'Barang Keluar',
                    data: keluarData,
                    backgroundColor: '#ef4444', /* Warna Danger Red */
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' }
            },
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, grid: { color: '#e2e8f0' } }
            }
        }
    });
});
</script>
