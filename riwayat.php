<?php
// Include koneksi database
$conn = include 'koneksi.php';

// Set default filter
$start_date = $_GET['start_date'] ?? date('Y-m-01'); // Tanggal awal bulan ini
$end_date = $_GET['end_date'] ?? date('Y-m-d'); // Tanggal hari ini
$search = $_GET['search'] ?? '';

// Query untuk riwayat transaksi
$where_conditions = ["DATE(p.tanggal) BETWEEN '$start_date' AND '$end_date'"];

if (!empty($search)) {
    $where_conditions[] = "(pl.nama_pelanggan LIKE '%$search%' OR p.id = '$search')";
}

$where_clause = implode(' AND ', $where_conditions);

$sql = "SELECT p.*, pl.nama_pelanggan, 
               (SELECT COUNT(*) FROM detail_penjualan dp WHERE dp.penjualan_id = p.id) as jumlah_item
        FROM penjualan p 
        LEFT JOIN pelanggan pl ON p.pelanggan_id = pl.pelanggan_id 
        WHERE $where_clause 
        ORDER BY p.tanggal DESC, p.id DESC";

$result_penjualan = $conn->query($sql);

// Hitung total untuk summary
$sql_total = "SELECT 
    COUNT(*) as total_transaksi,
    COALESCE(SUM(p.total), 0) as total_pendapatan,
    COALESCE(SUM(dp.jumlah), 0) as total_produk_terjual
    FROM penjualan p 
    LEFT JOIN detail_penjualan dp ON p.id = dp.penjualan_id 
    WHERE $where_clause";

$result_total = $conn->query($sql_total);
$summary = $result_total->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - Aplikasi Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #3498db;
            --success: #27ae60;
            --danger: #e74c3c;
            --warning: #f39c12;
            --info: #17a2b8;
            --purple: #8e44ad;
            --pink: #e84393;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        
        .main-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }
        
        .navbar {
            background: var(--primary) !important;
            border-bottom: 3px solid var(--secondary);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .nav-link {
            font-weight: 500;
            border-radius: 10px;
            margin: 0 3px;
            transition: all 0.3s ease;
            padding: 8px 16px !important;
        }
        
        .nav-link:hover, .nav-link.active {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            color: white !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
        }
        
        .section-title {
            color: var(--primary);
            font-weight: 800;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 3px solid var(--secondary);
            position: relative;
            font-size: 1.8rem;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 80px;
            height: 4px;
            background: linear-gradient(135deg, var(--success), var(--info));
            border-radius: 2px;
        }
        
        .card-custom {
            border: none;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }
        
        .card-custom:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }
        
        .card-header-custom {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            color: white;
            border-radius: 16px 16px 0 0 !important;
            border: none;
            padding: 1.5rem;
            font-weight: 600;
        }
        
        .stats-card {
            background: linear-gradient(135deg, var(--secondary), var(--purple));
            color: white;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
            border: none;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        }
        
        .stats-label {
            font-size: 1rem;
            opacity: 0.9;
            font-weight: 500;
        }
        
        .stats-icon {
            font-size: 3rem;
            opacity: 0.9;
            filter: drop-shadow(2px 2px 4px rgba(0,0,0,0.2));
        }
        
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            border: none;
            color: white;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(52, 152, 219, 0.4);
        }
        
        .btn-success-custom {
            background: linear-gradient(135deg, var(--success), #2ecc71);
            border: none;
            color: white;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 12px;
            transition: all 0.3s ease;
        }
        
        .btn-success-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(39, 174, 96, 0.4);
        }
        
        .btn-outline-primary-custom {
            border: 2px solid var(--secondary);
            color: var(--secondary);
            background: transparent;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .btn-outline-primary-custom:hover {
            background: var(--secondary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(52, 152, 219, 0.3);
        }
        
        .form-control-custom {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 12px 16px;
            font-weight: 500;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }
        
        .form-control-custom:focus {
            border-color: var(--secondary);
            box-shadow: 0 0 0 0.3rem rgba(52, 152, 219, 0.2);
            transform: translateY(-2px);
        }
        
        .table-custom {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .table-custom thead {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
        }
        
        .table-custom th {
            border: none;
            padding: 18px 16px;
            font-weight: 600;
            font-size: 0.95rem;
        }
        
        .table-custom td {
            border: none;
            padding: 16px;
            vertical-align: middle;
            background: rgba(255, 255, 255, 0.9);
        }
        
        .table-custom tbody tr {
            transition: all 0.3s ease;
        }
        
        .table-custom tbody tr:hover {
            background: linear-gradient(135deg, rgba(52, 152, 219, 0.1), rgba(142, 68, 173, 0.1));
            transform: scale(1.01);
        }
        
        .badge-custom {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .badge-primary {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            color: white;
        }
        
        .badge-success {
            background: linear-gradient(135deg, var(--success), #2ecc71);
            color: white;
        }
        
        .badge-info {
            background: linear-gradient(135deg, var(--info), #0984e3);
            color: white;
        }
        
        .badge-warning {
            background: linear-gradient(135deg, var(--warning), #e67e22);
            color: white;
        }
        
        .transaction-id {
            font-family: 'Courier New', monospace;
            font-weight: 700;
            color: var(--primary);
        }
        
        .amount-highlight {
            font-weight: 700;
            font-size: 1.1rem;
        }
        
        .filter-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9), rgba(248, 249, 250, 0.9));
            backdrop-filter: blur(10px);
        }
        
        .floating-action {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
        }
        
        .btn-floating {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--success), var(--info));
            border: none;
            color: white;
            font-size: 1.5rem;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }
        
        .btn-floating:hover {
            transform: scale(1.1) rotate(10deg);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }
        
        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .animate-fade-in {
            animation: fadeInUp 0.6s ease-out;
        }
        
        .pulse {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .empty-state {
            padding: 4rem 2rem;
            text-align: center;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
        
        @media (max-width: 768px) {
            .main-container {
                margin: 10px;
                border-radius: 15px;
            }
            
            .stats-number {
                font-size: 2rem;
            }
            
            .floating-action {
                bottom: 20px;
                right: 20px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="bi bi-graph-up me-2"></i>KasirPro
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
                            <i class="bi bi-cash me-1"></i> Kasir
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="stok.php">
                            <i class="bi bi-box-seam me-1"></i> Stok
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pelanggan.php">
                            <i class="bi bi-people me-1"></i> Pelanggan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="riwayat.php">
                            <i class="bi bi-graph-up me-1"></i> Riwayat
                        </a>
                    </li>
                    <!-- Tambahan Logout -->
                    <li class="nav-item">
                        <a class="nav-link nav-link-logout" href="logout.php">
                            <i class="bi bi-box-arrow-right me-1"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="main-container animate-fade-in">
            <div class="p-4">
                <!-- Header Section -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <h1 class="section-title">
                            <i class="bi bi-clock-history me-3"></i>RIWAYAT TRANSAKSI
                        </h1>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="btn-group">
                            <button class="btn btn-outline-primary-custom" onclick="printRiwayat()">
                                <i class="bi bi-printer me-2"></i>Cetak Laporan
                            </button>
                            <button class="btn btn-success-custom" onclick="exportToExcel()">
                                <i class="bi bi-file-earmark-excel me-2"></i>Export Excel
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Filter Section -->
                <div class="card card-custom filter-card mb-4">
                    <div class="card-header-custom">
                        <h5 class="mb-0">
                            <i class="bi bi-funnel me-2"></i>FILTER TRANSAKSI
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="GET" action="">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Tanggal Mulai</label>
                                    <input type="date" class="form-control form-control-custom" name="start_date" value="<?= $start_date ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold">Tanggal Akhir</label>
                                    <input type="date" class="form-control form-control-custom" name="end_date" value="<?= $end_date ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Cari Transaksi</label>
                                    <input type="text" class="form-control form-control-custom" name="search" value="<?= htmlspecialchars($search) ?>" 
                                           placeholder="Cari berdasarkan ID transaksi atau nama pelanggan...">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-semibold">&nbsp;</label>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary-custom">
                                            <i class="bi bi-search me-2"></i> Terapkan
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="stats-card pulse">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="stats-number"><?= number_format($summary['total_transaksi']) ?></div>
                                        <div class="stats-label">Total Transaksi</div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-receipt stats-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card" style="background: linear-gradient(135deg, var(--success), #2ecc71);">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="stats-number">Rp <?= number_format($summary['total_pendapatan'], 0, ',', '.') ?></div>
                                        <div class="stats-label">Total Pendapatan</div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-currency-dollar stats-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stats-card" style="background: linear-gradient(135deg, var(--warning), #e67e22);">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="stats-number"><?= number_format($summary['total_produk_terjual']) ?></div>
                                        <div class="stats-label">Produk Terjual</div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-box stats-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Riwayat Transaksi -->
                <div class="card card-custom">
                    <div class="card-header-custom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-list-ul me-2"></i>DAFTAR TRANSAKSI
                        </h5>
                        <span class="badge-custom badge-primary">
                            <i class="bi bi-check-circle me-1"></i>
                            <?= $result_penjualan->num_rows ?> transaksi ditemukan
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <?php if ($result_penjualan->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-custom mb-0" id="tableRiwayat">
                                    <thead>
                                        <tr>
                                            <th width="60">#</th>
                                            <th>ID TRANSAKSI</th>
                                            <th width="150">TANGGAL & WAKTU</th>
                                            <th>PELANGGAN</th>
                                            <th width="120">ITEM</th>
                                            <th width="150">TOTAL</th>
                                            <th width="140" class="text-center">AKSI</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $no = 1;
                                        while($row = $result_penjualan->fetch_assoc()):
                                            $waktu = date('H:i', strtotime($row['tanggal']));
                                            $tanggal = date('d/m/Y', strtotime($row['tanggal']));
                                        ?>
                                            <tr class="animate-fade-in">
                                                <td class="fw-bold text-muted"><?= $no++ ?></td>
                                                <td>
                                                    <div class="transaction-id">
                                                        #<?= str_pad($row['id'], 6, '0', STR_PAD_LEFT) ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold"><?= $tanggal ?></div>
                                                    <small class="text-muted">
                                                        <i class="bi bi-clock me-1"></i><?= $waktu ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold">
                                                        <?= $row['nama_pelanggan'] ?: 'Pelanggan Umum' ?>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge-custom badge-info">
                                                        <i class="bi bi-box me-1"></i>
                                                        <?= $row['jumlah_item'] ?> item
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="amount-highlight text-success">
                                                        Rp <?= number_format($row['total'], 0, ',', '.') ?>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-primary-custom btn-sm" 
                                                                onclick="lihatDetail(<?= $row['id'] ?>)"
                                                                title="Lihat Detail">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                        <button class="btn btn-outline-primary-custom btn-sm" 
                                                                onclick="printStrukSingle(<?= $row['id'] ?>)"
                                                                title="Cetak Struk">
                                                            <i class="bi bi-printer"></i>
                                                        </button>
                                                        <button class="btn btn-outline-danger btn-sm" 
                                                                onclick="hapusTransaksi(<?= $row['id'] ?>)"
                                                                title="Hapus Transaksi">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="empty-state">
                                <i class="bi bi-receipt-cutoff"></i>
                                <h4 class="fw-semibold mt-3">Tidak Ada Transaksi</h4>
                                <p class="text-muted mb-4">Tidak ditemukan transaksi pada periode yang dipilih.</p>
                                <a href="index.php" class="btn btn-primary-custom">
                                    <i class="bi bi-cart-plus me-2"></i> Mulai Transaksi Baru
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer bg-white border-top-0 pt-4 pb-3">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <small class="text-muted fw-semibold">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Menampilkan <strong><?= $result_penjualan->num_rows ?></strong> transaksi
                                    (<?= date('d/m/Y', strtotime($start_date)) ?> - <?= date('d/m/Y', strtotime($end_date)) ?>)
                                </small>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <small class="text-muted">
                                    <i class="bi bi-clock me-1"></i>
                                    Terakhir update: <?= date('H:i:s') ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Action Button -->
    <div class="floating-action">
        <button class="btn btn-floating" onclick="scrollToTop()" title="Kembali ke atas">
            <i class="bi bi-arrow-up"></i>
        </button>
    </div>

    <!-- Modal Detail Transaksi -->
    <div class="modal fade" id="detailModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-receipt me-2"></i>
                        Detail Transaksi #<span id="modalTransactionId"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailContent">
                    <!-- Content will be loaded via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x me-2"></i>Tutup
                    </button>
                    <button type="button" class="btn btn-primary-custom" onclick="printStruk()">
                        <i class="bi bi-printer me-2"></i> Cetak Struk
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Fungsi untuk lihat detail transaksi
        function lihatDetail(transaksiId) {
            // Show loading state
            document.getElementById('detailContent').innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat detail transaksi...</p>
                </div>
            `;
            
            const modal = new bootstrap.Modal(document.getElementById('detailModal'));
            modal.show();
            
            fetch(`get_detail_transaksi.php?id=${transaksiId}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.status === 'success') {
                        document.getElementById('modalTransactionId').textContent = 
                            String(data.transaksi.id).padStart(6, '0');
                        
                        const detailContent = document.getElementById('detailContent');
                        detailContent.innerHTML = `
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title fw-semibold">
                                                <i class="bi bi-info-circle me-2"></i>Informasi Transaksi
                                            </h6>
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <td width="40%"><strong>ID Transaksi</strong></td>
                                                    <td>#${String(data.transaksi.id).padStart(6, '0')}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Tanggal/Waktu</strong></td>
                                                    <td>${new Date(data.transaksi.tanggal).toLocaleString('id-ID')}</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Pelanggan</strong></td>
                                                    <td>${data.transaksi.nama_pelanggan || 'Pelanggan Umum'}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body">
                                            <h6 class="card-title fw-semibold">
                                                <i class="bi bi-currency-dollar me-2"></i>Ringkasan Pembayaran
                                            </h6>
                                            <table class="table table-sm table-borderless">
                                                <tr>
                                                    <td width="40%"><strong>Total Item</strong></td>
                                                    <td><span class="badge-custom badge-info">${data.details.length} item</span></td>
                                                </tr>
                                                <tr>
                                                    <td><strong>Total Belanja</strong></td>
                                                    <td class="text-success fw-bold fs-5">Rp ${parseFloat(data.transaksi.total).toLocaleString('id-ID')}</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <h6 class="fw-semibold mb-3">
                                <i class="bi bi-cart me-2"></i>Detail Produk
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-striped">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Produk</th>
                                            <th class="text-center">Harga</th>
                                            <th class="text-center">Qty</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${data.details.map(item => `
                                            <tr>
                                                <td>${item.nama_produk}</td>
                                                <td class="text-center">Rp ${parseFloat(item.harga).toLocaleString('id-ID')}</td>
                                                <td class="text-center">${item.jumlah}</td>
                                                <td class="text-end fw-semibold">Rp ${parseFloat(item.subtotal).toLocaleString('id-ID')}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                    <tfoot class="table-light">
                                        <tr>
                                            <td colspan="3" class="text-end fw-bold">Total:</td>
                                            <td class="text-end fw-bold text-success">Rp ${parseFloat(data.transaksi.total).toLocaleString('id-ID')}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        `;
                    } else {
                        document.getElementById('detailContent').innerHTML = `
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Gagal memuat detail transaksi: ${data.message || 'Data tidak ditemukan'}
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('detailContent').innerHTML = `
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Terjadi kesalahan saat memuat detail transaksi. Pastikan file get_detail_transaksi.php tersedia.
                        </div>
                    `;
                });
        }

        // Fungsi untuk hapus transaksi
        function hapusTransaksi(id) {
            Swal.fire({
                title: 'Hapus Transaksi?',
                text: "Transaksi yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#3498db',
                confirmButtonText: '<i class="bi bi-trash me-2"></i>Ya, Hapus!',
                cancelButtonText: '<i class="bi bi-x me-2"></i>Batal',
                background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                color: 'white'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Menghapus...',
                        text: 'Sedang menghapus transaksi',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Simulasi hapus transaksi (ganti dengan AJAX ke hapus_transaksi.php jika ada)
                    setTimeout(() => {
                        Swal.fire({
                            title: 'Fitur Hapus Transaksi',
                            text: 'Fitur hapus transaksi membutuhkan file hapus_transaksi.php untuk diimplementasikan.',
                            icon: 'info',
                            confirmButtonColor: '#3498db'
                        });
                    }, 1500);
                }
            });
        }

        // Fungsi untuk print riwayat
        function printRiwayat() {
            const printContent = document.querySelector('.main-container').innerHTML;
            const originalContent = document.body.innerHTML;
            
            document.body.innerHTML = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Laporan Riwayat Transaksi</title>
                    <style>
                        body { font-family: Arial, sans-serif; font-size: 12px; padding: 20px; }
                        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
                        .table { width: 100%; border-collapse: collapse; margin: 10px 0; }
                        .table th, .table td { padding: 8px; border: 1px solid #ddd; text-align: left; }
                        .table th { background-color: #f8f9fa; font-weight: bold; }
                        .text-right { text-align: right; }
                        .text-center { text-align: center; }
                        .total { font-weight: bold; font-size: 14px; border-top: 2px solid #333; }
                        .footer { text-align: center; margin-top: 20px; font-size: 10px; color: #666; }
                        @media print { 
                            body { margin: 0; padding: 10px; }
                            .no-print { display: none; }
                        }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h2>LAPORAN RIWAYAT TRANSAKSI</h2>
                        <p>KasirPro - Sistem Kasir Modern</p>
                        <p>Periode: <?= date('d/m/Y', strtotime($start_date)) ?> - <?= date('d/m/Y', strtotime($end_date)) ?></p>
                        <p>Tanggal Cetak: ${new Date().toLocaleString('id-ID')}</p>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>ID Transaksi</th>
                                    <th>Tanggal & Waktu</th>
                                    <th>Pelanggan</th>
                                    <th>Item</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $result_penjualan->data_seek(0); // Reset pointer
                                while($row = $result_penjualan->fetch_assoc()):
                                    $waktu = date('H:i', strtotime($row['tanggal']));
                                    $tanggal = date('d/m/Y', strtotime($row['tanggal']));
                                ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td>#<?= str_pad($row['id'], 6, '0', STR_PAD_LEFT) ?></td>
                                        <td><?= $tanggal ?> <?= $waktu ?></td>
                                        <td><?= $row['nama_pelanggan'] ?: 'Pelanggan Umum' ?></td>
                                        <td><?= $row['jumlah_item'] ?> item</td>
                                        <td>Rp <?= number_format($row['total'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="footer">
                        <p>Total Transaksi: <?= $summary['total_transaksi'] ?> | Total Pendapatan: Rp <?= number_format($summary['total_pendapatan'], 0, ',', '.') ?></p>
                        <p>*** Laporan ini dicetak secara otomatis ***</p>
                    </div>
                    <div class="no-print" style="margin-top: 20px; text-align: center;">
                        <button onclick="window.print()" style="padding: 10px 20px; margin: 5px;">Cetak Laporan</button>
                        <button onclick="window.close()" style="padding: 10px 20px; margin: 5px;">Tutup</button>
                    </div>
                </body>
                </html>
            `;
            
            window.print();
            document.body.innerHTML = originalContent;
        }

        // Fungsi untuk export ke Excel
        function exportToExcel() {
            try {
                // Create workbook
                const wb = XLSX.utils.book_new();
                
                // Prepare data for export
                const data = [
                    ['LAPORAN RIWAYAT TRANSAKSI'],
                    ['KasirPro - Sistem Kasir Modern'],
                    ['Periode: <?= date('d/m/Y', strtotime($start_date)) ?> - <?= date('d/m/Y', strtotime($end_date)) ?>'],
                    ['Tanggal Export: ' + new Date().toLocaleString('id-ID')],
                    [''], // Empty row
                    ['No', 'ID Transaksi', 'Tanggal', 'Waktu', 'Pelanggan', 'Jumlah Item', 'Total']
                ];

                // Add transaction data
                <?php
                $result_penjualan->data_seek(0); // Reset pointer
                $no = 1;
                while($row = $result_penjualan->fetch_assoc()):
                    $waktu = date('H:i', strtotime($row['tanggal']));
                    $tanggal = date('d/m/Y', strtotime($row['tanggal']));
                ?>
                    data.push([
                        <?= $no++ ?>,
                        '#<?= str_pad($row['id'], 6, '0', STR_PAD_LEFT) ?>',
                        '<?= $tanggal ?>',
                        '<?= $waktu ?>',
                        '<?= $row['nama_pelanggan'] ?: 'Pelanggan Umum' ?>',
                        <?= $row['jumlah_item'] ?>,
                        <?= $row['total'] ?>
                    ]);
                <?php endwhile; ?>

                // Add summary
                data.push(['']);
                data.push(['SUMMARY']);
                data.push(['Total Transaksi:', <?= $summary['total_transaksi'] ?>]);
                data.push(['Total Pendapatan:', <?= $summary['total_pendapatan'] ?>]);
                data.push(['Total Produk Terjual:', <?= $summary['total_produk_terjual'] ?>]);

                // Create worksheet
                const ws = XLSX.utils.aoa_to_sheet(data);
                
                // Add workbook properties
                wb.Props = {
                    Title: "Laporan Riwayat Transaksi",
                    Subject: "Riwayat Transaksi KasirPro",
                    Author: "KasirPro",
                    CreatedDate: new Date()
                };

                // Add worksheet to workbook
                XLSX.utils.book_append_sheet(wb, ws, "Riwayat Transaksi");

                // Export to Excel
                XLSX.writeFile(wb, `riwayat-transaksi-${new Date().toISOString().split('T')[0]}.xlsx`);
                
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Data berhasil diexport ke Excel',
                    icon: 'success',
                    confirmButtonColor: '#27ae60',
                    timer: 2000
                });
            } catch (error) {
                console.error('Export error:', error);
                Swal.fire({
                    title: 'Error!',
                    text: 'Gagal mengexport data: ' + error.message,
                    icon: 'error',
                    confirmButtonColor: '#e74c3c'
                });
            }
        }

        // Fungsi untuk print struk dari modal
        function printStruk() {
            const detailContent = document.getElementById('detailContent').innerHTML;
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Struk Transaksi</title>
                    <style>
                        body { font-family: Arial, sans-serif; font-size: 12px; padding: 20px; }
                        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
                        .table { width: 100%; border-collapse: collapse; margin: 10px 0; }
                        .table th, .table td { padding: 8px; border-bottom: 1px solid #ddd; text-align: left; }
                        .table th { background-color: #f8f9fa; }
                        .text-right { text-align: right; }
                        .text-center { text-align: center; }
                        .total { font-weight: bold; font-size: 14px; border-top: 2px solid #333; }
                        .footer { text-align: center; margin-top: 20px; font-size: 10px; color: #666; }
                        @media print { 
                            .no-print { display: none; } 
                            body { margin: 0; padding: 10px; }
                        }
                    </style>
                </head>
                <body>
                    <div class="header">
                        <h2>STRUK TRANSAKSI</h2>
                        <p>KasirPro - Sistem Kasir Modern</p>
                        <p>${new Date().toLocaleString('id-ID')}</p>
                    </div>
                    ${detailContent}
                    <div class="footer">
                        <p>Terima kasih atas kunjungan Anda</p>
                        <p>*** Semoga hari Anda menyenangkan ***</p>
                    </div>
                    <div class="no-print" style="margin-top: 20px; text-align: center;">
                        <button onclick="window.print()" style="padding: 10px 20px; margin: 5px;">Cetak Struk</button>
                        <button onclick="window.close()" style="padding: 10px 20px; margin: 5px;">Tutup</button>
                    </div>
                </body>
                </html>
            `);
            printWindow.document.close();
        }

        // Fungsi untuk print struk langsung
        function printStrukSingle(transaksiId) {
            Swal.fire({
                title: 'Cetak Struk',
                text: 'Mencetak struk untuk transaksi #' + String(transaksiId).padStart(6, '0'),
                icon: 'info',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3498db'
            });
        }

        // Scroll to top function
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Inisialisasi DataTable
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof $ != 'undefined' && $.fn.DataTable) {
                $('#tableRiwayat').DataTable({
                    language: {
                        search: "Cari:",
                        lengthMenu: "Tampilkan _MENU_ transaksi",
                        info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ transaksi",
                        paginate: {
                            first: "Pertama",
                            last: "Terakhir",
                            next: "Berikutnya",
                            previous: "Sebelumnya"
                        }
                    },
                    order: [[0, 'desc']],
                    responsive: true
                });
            }
        });

        // Add loading animation to buttons
        document.querySelectorAll('button[type="submit"]').forEach(button => {
            button.addEventListener('click', function() {
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="bi bi-arrow-repeat spinner me-2"></i>Memproses...';
                this.disabled = true;
                
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.disabled = false;
                }, 2000);
            });
        });

        // Add spinner animation
        const style = document.createElement('style');
        style.textContent = `
            .spinner {
                animation: spin 1s linear infinite;
            }
            @keyframes spin {
                from { transform: rotate(0deg); }
                to { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);

        // Error handling untuk link "Mulai Transaksi Baru"
        document.addEventListener('DOMContentLoaded', function() {
            const mulaiTransaksiBtn = document.querySelector('a[href="index.php"]');
            if (mulaiTransaksiBtn) {
                mulaiTransaksiBtn.addEventListener('click', function(e) {
                    // Cek jika halaman index.php tidak ada
                    fetch('index.php')
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Halaman kasir tidak ditemukan');
                            }
                        })
                        .catch(error => {
                            e.preventDefault();
                            Swal.fire({
                                title: 'Halaman Kasir Tidak Ditemukan',
                                text: 'File index.php tidak ditemukan. Pastikan file tersebut ada di direktori yang sama.',
                                icon: 'warning',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#3498db'
                            });
                        });
                });
            }
        });
    </script>
</body>
</html>