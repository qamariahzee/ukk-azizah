<?php
/**
 * Riwayat Transaksi dengan Search dan Pagination (Max 10)
 */

$page_title = 'Riwayat Transaksi';
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../config/koneksi.php';

$filter_jenis = $_GET['jenis'] ?? '';
$filter_dari = $_GET['dari'] ?? date('Y-m-01');
$filter_sampai = $_GET['sampai'] ?? date('Y-m-d');

$sql = "SELECT t.*, b.nama_barang FROM transaksi t JOIN barang b ON t.id_barang = b.id WHERE DATE(t.tanggal_transaksi) BETWEEN ? AND ?";
$params = [$filter_dari, $filter_sampai];

if ($filter_jenis) {
    $sql .= " AND t.jenis_transaksi = ?";
    $params[] = $filter_jenis;
}

$sql .= " ORDER BY t.tanggal_transaksi DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$data_transaksi = $stmt->fetchAll();

$total_masuk = 0;
$total_keluar = 0;
foreach ($data_transaksi as $t) {
    if ($t['jenis_transaksi'] === 'masuk') {
        $total_masuk += $t['jumlah'];
    } else {
        $total_keluar += $t['jumlah'];
    }
}
?>

<div class="page-header">
    <h1>Riwayat Transaksi</h1>
    <p>Catatan semua transaksi barang</p>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label">Jenis</label>
                <select name="jenis" class="form-select">
                    <option value="">Semua</option>
                    <option value="masuk" <?= $filter_jenis === 'masuk' ? 'selected' : '' ?>>Masuk</option>
                    <option value="keluar" <?= $filter_jenis === 'keluar' ? 'selected' : '' ?>>Keluar</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Dari</label>
                <input type="date" name="dari" class="form-control" value="<?= $filter_dari ?>">
            </div>
            <div class="col-md-3">
                <label class="form-label">Sampai</label>
                <input type="date" name="sampai" class="form-control" value="<?= $filter_sampai ?>">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Summary -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card" style="background: #dcfce7;">
            <div class="card-body text-center">
                <h6 style="color: #15803d;">Total Masuk</h6>
                <h3 style="color: #15803d;">+<?= number_format($total_masuk) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card" style="background: #fee2e2;">
            <div class="card-body text-center">
                <h6 style="color: #dc2626;">Total Keluar</h6>
                <h3 style="color: #dc2626;">-<?= number_format($total_keluar) ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card" style="background: #eff6ff;">
            <div class="card-body text-center">
                <h6 style="color: #3b82f6;">Selisih</h6>
                <h3 style="color: <?= ($total_masuk - $total_keluar) >= 0 ? '#15803d' : '#dc2626' ?>;">
                    <?= ($total_masuk - $total_keluar) >= 0 ? '+' : '' ?><?= number_format($total_masuk - $total_keluar) ?>
                </h3>
            </div>
        </div>
    </div>
</div>

<!-- Table -->
<div class="card">
    <div class="card-header">
        <span>Data Transaksi (<?= count($data_transaksi) ?> records)</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="tabelTransaksi" class="table table-hover">
                <thead>
                    <tr>
                        <th width="5%">No</th>
                        <th width="18%">Tanggal</th>
                        <th>Barang</th>
                        <th width="12%">Jenis</th>
                        <th width="10%">Jumlah</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data_transaksi)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data</td></tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($data_transaksi as $t): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($t['tanggal_transaksi'])) ?></td>
                            <td><strong><?= htmlspecialchars($t['nama_barang']) ?></strong></td>
                            <td>
                                <span class="badge <?= $t['jenis_transaksi'] === 'masuk' ? 'bg-success' : 'bg-danger' ?>">
                                    <?= ucfirst($t['jenis_transaksi']) ?>
                                </span>
                            </td>
                            <td class="<?= $t['jenis_transaksi'] === 'masuk' ? 'text-success' : 'text-danger' ?> fw-bold">
                                <?= $t['jenis_transaksi'] === 'masuk' ? '+' : '-' ?><?= $t['jumlah'] ?>
                            </td>
                            <td><small class="text-muted"><?= htmlspecialchars($t['keterangan'] ?: '-') ?></small></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<!-- DataTables Init - HARUS setelah footer karena jQuery & DataTables dimuat di footer -->
<script>
$(document).ready(function() {
    $('#tabelTransaksi').DataTable({
        language: { 
            url: 'https://cdn.datatables.net/plug-ins/1.13.7/i18n/id.json',
            search: "Cari:",
            lengthMenu: "Tampilkan _MENU_ data",
            info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
            paginate: {
                first: "Pertama",
                last: "Terakhir",
                next: "Selanjutnya",
                previous: "Sebelumnya"
            }
        },
        order: [[1, 'desc']],
        pageLength: 10,
        lengthMenu: [[5, 10, 25], [5, 10, 25]],
        searching: true,
        paging: true,
        info: true
    });
});
</script>
