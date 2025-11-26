<?php
// Data pelanggan sementara (simulasi database)
$pelanggan_data = [
    ['id' => 1, 'nama' => 'Budi Santoso', 'telepon' => '081234567890', 'email' => 'budi@email.com', 'alamat' => 'Jl. Merdeka No. 123, Jakarta', 'status' => 'aktif', 'tanggal_daftar' => '2024-01-15'],
    ['id' => 2, 'nama' => 'Siti Rahayu', 'telepon' => '081298765432', 'email' => 'siti@email.com', 'alamat' => 'Jl. Sudirman No. 45, Bandung', 'status' => 'aktif', 'tanggal_daftar' => '2024-01-20'],
    ['id' => 3, 'nama' => 'Ahmad Wijaya', 'telepon' => '081345678901', 'email' => 'ahmad@email.com', 'alamat' => 'Jl. Gatot Subroto No. 67, Surabaya', 'status' => 'aktif', 'tanggal_daftar' => date('Y-m-d')]
];

// Simulasi CRUD operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] == 'tambah') {
            $new_id = count($pelanggan_data) + 1;
            $pelanggan_data[] = [
                'id' => $new_id,
                'nama' => $_POST['nama_pelanggan'],
                'telepon' => $_POST['telepon'],
                'email' => $_POST['email'],
                'alamat' => $_POST['alamat'],
                'status' => 'aktif',
                'tanggal_daftar' => date('Y-m-d')
            ];
            $message = "Pelanggan berhasil ditambahkan!";
            $message_type = "success";
        }
        elseif ($_POST['action'] == 'edit') {
            $id_to_edit = $_POST['id'];
            foreach ($pelanggan_data as &$pelanggan) {
                if ($pelanggan['id'] == $id_to_edit) {
                    $pelanggan['nama'] = $_POST['nama_pelanggan'];
                    $pelanggan['telepon'] = $_POST['telepon'];
                    $pelanggan['email'] = $_POST['email'];
                    $pelanggan['alamat'] = $_POST['alamat'];
                    break;
                }
            }
            $message = "Pelanggan berhasil diupdate!";
            $message_type = "success";
        }
        elseif ($_POST['action'] == 'delete') {
            $id_to_delete = $_POST['id'];
            $pelanggan_data = array_filter($pelanggan_data, function($p) use ($id_to_delete) {
                return $p['id'] != $id_to_delete;
            });
            $pelanggan_data = array_values($pelanggan_data); // Reset array keys
            $message = "Pelanggan berhasil dihapus!";
            $message_type = "success";
        }
    }
}

// Hitung statistik dengan benar
$total_pelanggan = count($pelanggan_data);
$pelanggan_aktif = count(array_filter($pelanggan_data, function($p) { return $p['status'] === 'aktif'; }));
$pelanggan_baru = count(array_filter($pelanggan_data, function($p) { return $p['tanggal_daftar'] === date('Y-m-d'); }));
$total_transaksi = $total_pelanggan * 3; // Simulasi rata-rata 3 transaksi per pelanggan

// Get data for editing
$edit_data = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    foreach ($pelanggan_data as $pelanggan) {
        if ($pelanggan['id'] == $edit_id) {
            $edit_data = $pelanggan;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Pelanggan - Aplikasi Kasir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
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
        
        .btn-warning-custom {
            background: linear-gradient(135deg, var(--warning), #e67e22);
            border: none;
            color: white;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .btn-warning-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(243, 156, 18, 0.4);
        }
        
        .btn-danger-custom {
            background: linear-gradient(135deg, var(--danger), #c0392b);
            border: none;
            color: white;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .btn-danger-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(231, 76, 60, 0.4);
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
            font-size: 2.8rem;
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
        
        .demo-warning {
            background: linear-gradient(135deg, #ffeaa7, #fab1a0);
            border: none;
            border-radius: 12px;
            padding: 20px 25px;
            margin-bottom: 30px;
            color: #2d3436;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-left: 5px solid var(--warning);
        }
        
        .search-box {
            max-width: 350px;
            border-radius: 12px;
            border: 2px solid #e9ecef;
            background: rgba(255, 255, 255, 0.9);
            transition: all 0.3s ease;
        }
        
        .search-box:focus-within {
            border-color: var(--secondary);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.2);
            transform: translateY(-2px);
        }
        
        .badge-status {
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8rem;
        }
        
        .badge-aktif {
            background: linear-gradient(135deg, var(--success), #2ecc71);
            color: white;
        }
        
        .badge-demo {
            background: linear-gradient(135deg, var(--pink), var(--purple));
            color: white;
        }
        
        .customer-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
            margin-right: 12px;
        }
        
        .edit-form-highlight {
            background: linear-gradient(135deg, rgba(255, 234, 167, 0.3), rgba(162, 155, 254, 0.3));
            border: 2px solid var(--warning);
            box-shadow: 0 0 25px rgba(243, 156, 18, 0.2);
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
        
        @media (max-width: 768px) {
            .main-container {
                margin: 10px;
                border-radius: 15px;
            }
            
            .stats-number {
                font-size: 2.2rem;
            }
            
            .floating-action {
                bottom: 20px;
                right: 20px;
            }
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
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="bi bi-people me-2"></i>KasirPro
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
                        <a class="nav-link active" href="pelanggan.php">
                            <i class="bi bi-people me-1"></i> Pelanggan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="riwayat.php">
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
                <!-- Warning Demo Mode -->
                <div class="demo-warning d-flex align-items-center">
                    <i class="bi bi-info-circle-fill me-3 fs-3 text-warning"></i>
                    <div class="flex-grow-1">
                        <strong class="d-block fs-6">Mode Demo Aktif</strong>
                        <span class="small">Data pelanggan disimpan sementara. Untuk penyimpanan permanen, pastikan database MySQL terhubung.</span>
                    </div>
                    <span class="badge badge-demo badge-status ms-3">DEMO</span>
                </div>

                <!-- Notifikasi -->
                <?php if (isset($message)): ?>
                    <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show animate-fade-in" role="alert">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-<?php echo $message_type == 'success' ? 'check-circle-fill' : 'exclamation-triangle-fill'; ?> me-2 fs-5"></i>
                            <span class="fw-semibold"><?php echo $message; ?></span>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Header Section -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <h1 class="section-title">
                            <i class="bi bi-people-fill me-3"></i>MANAJEMEN PELANGGAN
                        </h1>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="btn-group">
                            <button class="btn btn-outline-primary" onclick="exportData()">
                                <i class="bi bi-download me-2"></i>Export Data
                            </button>
                            <button class="btn btn-outline-success" onclick="printData()">
                                <i class="bi bi-printer me-2"></i>Print
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Statistik Pelanggan -->
                <div class="row mb-5">
                    <!-- Total Pelanggan -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stats-card pulse">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="stats-number"><?php echo number_format($total_pelanggan); ?></div>
                                        <div class="stats-label">Total Pelanggan</div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-people-fill stats-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pelanggan Aktif -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stats-card" style="background: linear-gradient(135deg, var(--success), #2ecc71);">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="stats-number"><?php echo number_format($pelanggan_aktif); ?></div>
                                        <div class="stats-label">Pelanggan Aktif</div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-person-check stats-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pelanggan Baru -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stats-card" style="background: linear-gradient(135deg, var(--warning), #e67e22);">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="stats-number"><?php echo number_format($pelanggan_baru); ?></div>
                                        <div class="stats-label">Pelanggan Baru</div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-person-plus stats-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Transaksi -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="stats-card" style="background: linear-gradient(135deg, var(--info), #0984e3);">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="stats-number"><?php echo number_format($total_transaksi); ?></div>
                                        <div class="stats-label">Total Transaksi</div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <i class="bi bi-cart-check stats-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Form Tambah/Edit Pelanggan -->
                    <div class="col-lg-4 mb-4">
                        <div class="card card-custom h-100 <?php echo $edit_data ? 'edit-form-highlight' : ''; ?>">
                            <div class="card-header-custom">
                                <h5 class="mb-0">
                                    <i class="bi bi-<?php echo $edit_data ? 'pencil-square' : 'person-plus'; ?> me-2"></i>
                                    <?php echo $edit_data ? 'EDIT PELANGGAN' : 'TAMBAH PELANGGAN BARU'; ?>
                                </h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" id="pelangganForm">
                                    <input type="hidden" name="action" value="<?php echo $edit_data ? 'edit' : 'tambah'; ?>">
                                    <?php if ($edit_data): ?>
                                        <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                                    <?php endif; ?>
                                    
                                    <div class="mb-4">
                                        <label for="nama_pelanggan" class="form-label fw-semibold">Nama Pelanggan <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-custom" id="nama_pelanggan" name="nama_pelanggan" 
                                               placeholder="Masukkan nama lengkap" 
                                               value="<?php echo $edit_data ? htmlspecialchars($edit_data['nama']) : ''; ?>" 
                                               required>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="telepon" class="form-label fw-semibold">Nomor Telepon <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control form-control-custom" id="telepon" name="telepon" 
                                               placeholder="Contoh: 081234567890" 
                                               value="<?php echo $edit_data ? htmlspecialchars($edit_data['telepon']) : ''; ?>" 
                                               required>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="email" class="form-label fw-semibold">Alamat Email</label>
                                        <input type="email" class="form-control form-control-custom" id="email" name="email" 
                                               placeholder="nama@email.com"
                                               value="<?php echo $edit_data ? htmlspecialchars($edit_data['email']) : ''; ?>">
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="alamat" class="form-label fw-semibold">Alamat Lengkap</label>
                                        <textarea class="form-control form-control-custom" id="alamat" name="alamat" rows="3" 
                                                  placeholder="Masukkan alamat lengkap pelanggan"><?php echo $edit_data ? htmlspecialchars($edit_data['alamat']) : ''; ?></textarea>
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary-custom btn-lg">
                                            <i class="bi bi-<?php echo $edit_data ? 'check-lg' : 'person-plus'; ?> me-2"></i>
                                            <?php echo $edit_data ? 'UPDATE PELANGGAN' : 'TAMBAH PELANGGAN'; ?>
                                        </button>
                                        <?php if ($edit_data): ?>
                                            <a href="pelanggan.php" class="btn btn-outline-secondary">
                                                <i class="bi bi-x-circle me-2"></i>Batal Edit
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Daftar Pelanggan -->
                    <div class="col-lg-8">
                        <div class="card card-custom">
                            <div class="card-header-custom d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="bi bi-list-ul me-2"></i>DAFTAR PELANGGAN
                                </h5>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="input-group search-box">
                                        <span class="input-group-text bg-white border-end-0">
                                            <i class="bi bi-search text-muted"></i>
                                        </span>
                                        <input type="text" class="form-control border-start-0" id="searchInput" placeholder="Cari pelanggan...">
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-custom mb-0" id="pelangganTable">
                                        <thead>
                                            <tr>
                                                <th width="80">#</th>
                                                <th>Pelanggan</th>
                                                <th width="160">Kontak</th>
                                                <th width="200">Email</th>
                                                <th>Status</th>
                                                <th width="140" class="text-center">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (count($pelanggan_data) > 0): ?>
                                                <?php $no = 1; ?>
                                                <?php foreach ($pelanggan_data as $pelanggan): ?>
                                                    <tr class="animate-fade-in">
                                                        <td class="fw-bold text-muted"><?php echo $no++; ?></td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="customer-avatar">
                                                                    <?php echo strtoupper(substr($pelanggan['nama'], 0, 1)); ?>
                                                                </div>
                                                                <div>
                                                                    <div class="fw-bold text-dark"><?php echo htmlspecialchars($pelanggan['nama']); ?></div>
                                                                    <small class="text-muted">ID: <?php echo $pelanggan['id']; ?></small>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <i class="bi bi-telephone text-primary me-2"></i>
                                                                <span class="fw-semibold"><?php echo htmlspecialchars($pelanggan['telepon']); ?></span>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <?php if ($pelanggan['email']): ?>
                                                                <div class="text-truncate" style="max-width: 180px;" title="<?php echo htmlspecialchars($pelanggan['email']); ?>">
                                                                    <i class="bi bi-envelope text-muted me-2"></i>
                                                                    <?php echo htmlspecialchars($pelanggan['email']); ?>
                                                                </div>
                                                            <?php else: ?>
                                                                <span class="text-muted">-</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <span class="badge-status badge-aktif">
                                                                <i class="bi bi-check-circle me-1"></i>
                                                                <?php echo strtoupper($pelanggan['status']); ?>
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="btn-group btn-group-sm">
                                                                <a href="?edit=<?php echo $pelanggan['id']; ?>" class="btn btn-warning-custom btn-action" title="Edit">
                                                                    <i class="bi bi-pencil"></i>
                                                                </a>
                                                                <form method="POST" class="d-inline" onsubmit="return confirmDelete()">
                                                                    <input type="hidden" name="action" value="delete">
                                                                    <input type="hidden" name="id" value="<?php echo $pelanggan['id']; ?>">
                                                                    <button type="submit" class="btn btn-danger-custom btn-action" title="Hapus">
                                                                        <i class="bi bi-trash"></i>
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="6" class="text-center py-5">
                                                        <div class="text-muted">
                                                            <i class="bi bi-people display-1 d-block mb-3 opacity-25"></i>
                                                            <h5 class="fw-semibold">Belum Ada Data Pelanggan</h5>
                                                            <p class="mb-0">Mulai dengan menambahkan pelanggan baru</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-top-0 pt-4 pb-3">
                                <div class="row align-items-center">
                                    <div class="col-md-6">
                                        <small class="text-muted fw-semibold">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Menampilkan <strong><?php echo count($pelanggan_data); ?></strong> pelanggan
                                        </small>
                                    </div>
                                    <div class="col-md-6 text-md-end">
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            Terakhir update: <?php echo date('H:i:s'); ?>
                                        </small>
                                    </div>
                                </div>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Fungsi pencarian real-time
        document.getElementById('searchInput').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#pelangganTable tbody tr');
            let visibleCount = 0;
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                const isVisible = text.includes(searchTerm);
                row.style.display = isVisible ? '' : 'none';
                if (isVisible) visibleCount++;
            });
        });

        // Fungsi export data
        function exportData() {
            Swal.fire({
                title: 'Export Data Pelanggan',
                text: 'Data akan diexport dalam format Excel',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#3498db',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-download me-2"></i>Export Sekarang',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Simulasi export
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Data berhasil diexport',
                        icon: 'success',
                        confirmButtonColor: '#27ae60'
                    });
                }
            });
        }

        // Fungsi print
        function printData() {
            window.print();
        }

        // Konfirmasi hapus dengan SweetAlert2
        function confirmDelete() {
            return Swal.fire({
                title: 'Hapus Pelanggan?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#3498db',
                confirmButtonText: '<i class="bi bi-trash me-2"></i>Ya, Hapus!',
                cancelButtonText: '<i class="bi bi-x me-2"></i>Batal',
                background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                color: 'white'
            }).then((result) => {
                return result.isConfirmed;
            });
        }

        // Validasi form
        document.getElementById('pelangganForm').addEventListener('submit', function(e) {
            const telepon = document.getElementById('telepon').value;
            const email = document.getElementById('email').value;
            
            if (!/^[0-9+\-\s()]{10,15}$/.test(telepon)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Format Telepon Salah',
                    text: 'Nomor telepon harus 10-15 digit dan hanya boleh berisi angka',
                    confirmButtonText: 'Mengerti',
                    confirmButtonColor: '#e74c3c'
                });
                e.preventDefault();
                return;
            }
            
            if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Format Email Salah',
                    text: 'Format email tidak valid. Contoh: nama@email.com',
                    confirmButtonText: 'Mengerti',
                    confirmButtonColor: '#e74c3c'
                });
                e.preventDefault();
                return;
            }
        });

        // Auto-format nomor telepon
        document.getElementById('telepon').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9+\-\s()]/g, '');
        });

        // Scroll to top function
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // Scroll to form when editing
        <?php if ($edit_data): ?>
            document.addEventListener('DOMContentLoaded', function() {
                const editForm = document.querySelector('.edit-form-highlight');
                if (editForm) {
                    editForm.scrollIntoView({ 
                        behavior: 'smooth',
                        block: 'start'
                    });
                    
                    // Add highlight animation
                    editForm.style.animation = 'pulse 2s ease-in-out';
                }
            });
        <?php endif; ?>

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
    </script>
</body>
</html>