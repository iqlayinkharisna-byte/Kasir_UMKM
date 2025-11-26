<?php 
include 'koneksi.php'; 

// Logic Tambah Produk
if(isset($_POST['add_product'])) {
    $nama = $_POST['nama'];
    $harga = $_POST['harga'];
    $stok = $_POST['stok'];
    $conn->query("INSERT INTO produk (nama_produk, harga, stok) VALUES ('$nama', '$harga', '$stok')");
    header("Location: stok.php");
}

// Logic Update Stok
if(isset($_POST['update_stok'])) {
    $id = $_POST['id'];
    $stok_baru = $_POST['stok_baru'];
    $conn->query("UPDATE produk SET stok = '$stok_baru' WHERE produk_id = '$id'");
    header("Location: stok.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplikasi Kasir - Stok</title>
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
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        
        .main-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            margin: 20px 0;
            overflow: hidden;
        }
        
        .navbar {
            background: var(--primary) !important;
            border-bottom: 3px solid var(--secondary);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .nav-link {
            font-weight: 500;
            border-radius: 8px;
            margin: 0 2px;
            transition: all 0.3s ease;
        }
        
        .nav-link:hover, .nav-link.active {
            background: var(--secondary);
            color: white !important;
        }
        
        .section-title {
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 3px solid var(--secondary);
            position: relative;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -3px;
            left: 0;
            width: 50px;
            height: 3px;
            background: var(--success);
        }
        
        .card-custom {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }
        
        .card-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .btn-primary-custom {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            border: none;
            color: white;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        }
        
        .btn-warning-custom {
            background: linear-gradient(135deg, var(--warning), #e67e22);
            border: none;
            color: white;
            font-weight: 600;
            padding: 6px 12px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        
        .btn-warning-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(243, 156, 18, 0.4);
        }
        
        .btn-success-custom {
            background: linear-gradient(135deg, var(--success), #2ecc71);
            border: none;
            color: white;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-success-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(39, 174, 96, 0.4);
        }
        
        .form-control-custom {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .form-control-custom:focus {
            border-color: var(--secondary);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        
        .table-custom {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        
        .table-custom thead {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
        }
        
        .table-custom th {
            border: none;
            padding: 15px;
            font-weight: 600;
        }
        
        .table-custom td {
            border: none;
            padding: 15px;
            vertical-align: middle;
        }
        
        .table-custom tbody tr {
            transition: all 0.3s ease;
        }
        
        .table-custom tbody tr:hover {
            background-color: #f8f9fa;
            transform: scale(1.01);
        }
        
        .table-custom tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .stock-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .stock-low {
            background-color: #ffeaa7;
            color: #e17055;
        }
        
        .stock-normal {
            background-color: #55efc4;
            color: #00b894;
        }
        
        .stock-empty {
            background-color: #fab1a0;
            color: #d63031;
        }
        
        .stats-card {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0;
        }
        
        .stats-label {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        @media (max-width: 768px) {
            .main-container {
                margin: 10px;
                border-radius: 10px;
            }
            
            .table-responsive {
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="dashboard.php">
                <i class="bi bi-cart-check me-2"></i>KasirPro
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
                        <a class="nav-link active" href="stok.php">
                            <i class="bi bi-box-seam me-1"></i> Stok
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pelanggan.php">
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
        <div class="main-container">
            <div class="p-4">
                <!-- Header dan Statistik -->
                <div class="row mb-4">
                    <div class="col-md-8">
                        <h2 class="section-title">
                            <i class="bi bi-box-seam me-2"></i>MANAJEMEN STOK PRODUK
                        </h2>
                    </div>
                    <div class="col-md-4">
                        <?php
                        $total_products = $conn->query("SELECT COUNT(*) as total FROM produk")->fetch_assoc()['total'];
                        $low_stock = $conn->query("SELECT COUNT(*) as low FROM produk WHERE stok < 10")->fetch_assoc()['low'];
                        $out_of_stock = $conn->query("SELECT COUNT(*) as empty FROM produk WHERE stok = 0")->fetch_assoc()['empty'];
                        ?>
                        <div class="row">
                            <div class="col-4">
                                <div class="stats-card text-center">
                                    <h3 class="stats-number"><?= $total_products ?></h3>
                                    <div class="stats-label">Total Produk</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stats-card text-center" style="background: linear-gradient(135deg, #f093fb, #f5576c);">
                                    <h3 class="stats-number"><?= $low_stock ?></h3>
                                    <div class="stats-label">Stok Rendah</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stats-card text-center" style="background: linear-gradient(135deg, #4facfe, #00f2fe);">
                                    <h3 class="stats-number"><?= $out_of_stock ?></h3>
                                    <div class="stats-label">Stok Habis</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Tambah Produk -->
                <div class="card card-custom mb-5">
                    <div class="card-body">
                        <h5 class="card-title mb-4">
                            <i class="bi bi-plus-circle me-2"></i>TAMBAH PRODUK BARU
                        </h5>
                        <form method="POST" class="row g-3">
                            <div class="col-md-4">
                                <input type="text" name="nama" class="form-control form-control-custom" placeholder="Nama Produk" required>
                            </div>
                            <div class="col-md-3">
                                <input type="number" name="harga" class="form-control form-control-custom" placeholder="Harga" required min="0">
                            </div>
                            <div class="col-md-3">
                                <input type="number" name="stok" class="form-control form-control-custom" placeholder="Stok Awal" required min="0">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" name="add_product" class="btn btn-success-custom w-100">
                                    <i class="bi bi-plus-lg me-2"></i>Tambah
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Daftar Produk -->
                <div class="card card-custom">
                    <div class="card-body">
                        <h5 class="card-title mb-4">
                            <i class="bi bi-list-ul me-2"></i>DAFTAR PRODUK
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-custom">
                                <thead>
                                    <tr>
                                        <th><i class="bi bi-box me-2"></i>Nama Produk</th>
                                        <th><i class="bi bi-currency-dollar me-2"></i>Harga</th>
                                        <th><i class="bi bi-bar-chart me-2"></i>Status Stok</th>
                                        <th><i class="bi bi-gear me-2"></i>Ubah Stok</th>
                                        <th><i class="bi bi-activity me-2"></i>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $result = $conn->query("SELECT * FROM produk ORDER BY produk_id DESC");
                                    while($row = $result->fetch_assoc()):
                                        $stock_class = '';
                                        $stock_text = '';
                                        if ($row['stok'] == 0) {
                                            $stock_class = 'stock-empty';
                                            $stock_text = 'STOK HABIS';
                                        } elseif ($row['stok'] < 10) {
                                            $stock_class = 'stock-low';
                                            $stock_text = 'STOK RENDAH';
                                        } else {
                                            $stock_class = 'stock-normal';
                                            $stock_text = 'STOK NORMAL';
                                        }
                                    ?>
                                    <tr>
                                        <td class="fw-bold"><?= $row['nama_produk'] ?></td>
                                        <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                                        <td>
                                            <span class="stock-badge <?= $stock_class ?>">
                                                <?= $stock_text ?> (<?= $row['stok'] ?>)
                                            </span>
                                        </td>
                                        <form method="POST">
                                            <input type="hidden" name="id" value="<?= $row['produk_id'] ?>">
                                            <td>
                                                <input type="number" name="stok_baru" class="form-control form-control-custom form-control-sm" 
                                                       value="<?= $row['stok'] ?>" min="0" required>
                                            </td>
                                            <td>
                                                <button type="submit" name="update_stok" class="btn btn-warning-custom">
                                                    <i class="bi bi-check-lg me-1"></i>Simpan
                                                </button>
                                            </td>
                                        </form>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Animasi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.card-custom');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 200);
            });
            
            // Notifikasi untuk perubahan stok
            const forms = document.querySelectorAll('form[method="POST"]');
            forms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="bi bi-arrow-repeat spinner"></i> Memproses...';
                    submitBtn.disabled = true;
                    
                    setTimeout(() => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }, 2000);
                });
            });
        });
        
        // Style untuk spinner
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