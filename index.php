<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kasir</title>
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
            --light: #ecf0f1;
            --dark: #2c3e50;
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
        
        .dashboard-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .stat-number {
            font-size: 2.2rem;
            font-weight: 800;
        }
        
        .product-card {
            border: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            background: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }
        
        .product-header {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            color: white;
            padding: 15px;
            text-align: center;
        }
        
        .product-body {
            padding: 20px;
        }
        
        .product-name {
            font-weight: 700;
            color: var(--dark);
            font-size: 1.1rem;
            margin-bottom: 0.5rem;
        }
        
        .product-price {
            color: var(--success);
            font-weight: 800;
            font-size: 1.2rem;
        }
        
        .product-stock {
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .low-stock {
            color: var(--danger);
            font-weight: 700;
        }
        
        .out-of-stock {
            color: var(--danger);
            font-weight: 700;
            background-color: #ffe6e6;
            padding: 2px 8px;
            border-radius: 4px;
        }
        
        .cart-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            height: 100%;
        }
        
        .cart-item {
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 0;
            transition: all 0.3s ease;
        }
        
        .cart-item:hover {
            background: #f8f9fa;
        }
        
        .cart-item:last-child {
            border-bottom: none;
        }
        
        .cart-total {
            background: linear-gradient(135deg, var(--success), #2ecc71);
            color: white;
            border-radius: 10px;
            padding: 1.5rem;
            font-weight: 700;
            box-shadow: 0 4px 15px rgba(39, 174, 96, 0.3);
        }
        
        .btn-pay {
            background: linear-gradient(135deg, var(--success), #2ecc71);
            border: none;
            color: white;
            font-weight: 700;
            padding: 12px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-pay:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(39, 174, 96, 0.4);
        }
        
        .btn-clear {
            background: linear-gradient(135deg, var(--danger), #e74c3c);
            border: none;
            color: white;
            font-weight: 700;
            padding: 12px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-clear:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.4);
        }
        
        .filter-btn {
            border: 2px solid var(--secondary);
            background: white;
            color: var(--secondary);
            font-weight: 600;
            border-radius: 25px;
            margin: 0 5px 10px 0;
            transition: all 0.3s ease;
        }
        
        .filter-btn.active, .filter-btn:hover {
            background: var(--secondary);
            color: white;
            transform: translateY(-2px);
        }
        
        .quantity-control {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .quantity-control button {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--secondary);
            background: white;
            color: var(--secondary);
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .quantity-control button:hover {
            background: var(--secondary);
            color: white;
        }
        
        .receipt {
            font-family: 'Courier New', monospace;
            font-size: 14px;
            line-height: 1.2;
        }
        
        .receipt-header, .receipt-footer {
            text-align: center;
        }
        
        .receipt-item {
            display: flex;
            justify-content: space-between;
        }
        
        .empty-cart {
            text-align: center;
            padding: 3rem 1rem;
            color: #6c757d;
        }
        
        .empty-cart i {
            font-size: 4rem;
            margin-bottom: 1rem;
            color: #dee2e6;
        }
        
        .customer-select {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 10px;
            font-weight: 600;
        }
        
        .modal-header {
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            color: white;
        }
        
        .modal-header .btn-close {
            filter: invert(1);
        }
        
        .btn-add-cart:disabled {
            background-color: #6c757d;
            border-color: #6c757d;
            cursor: not-allowed;
        }
        
        @media (max-width: 768px) {
            .main-container {
                margin: 10px;
                border-radius: 10px;
            }
            
            .product-card {
                margin-bottom: 1rem;
            }
            
            .stat-number {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-cart-check me-2"></i>KasirPro
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="bi bi-speedometer2 me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="kasir.php">
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
                <!-- Statistik Kasir -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card dashboard-card text-white" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                            <div class="card-body text-center">
                                <i class="bi bi-cart-check fs-1"></i>
                                <h3 class="stat-number mt-2" id="todayTransactions">15</h3>
                                <p class="mb-0">Transaksi Hari Ini</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card dashboard-card text-white" style="background: linear-gradient(135deg, #f093fb, #f5576c);">
                            <div class="card-body text-center">
                                <i class="bi bi-currency-dollar fs-1"></i>
                                <h3 class="stat-number mt-2">Rp 850K</h3>
                                <p class="mb-0">Pendapatan Hari Ini</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card dashboard-card text-white" style="background: linear-gradient(135deg, #4facfe, #00f2fe);">
                            <div class="card-body text-center">
                                <i class="bi bi-exclamation-triangle fs-1"></i>
                                <h3 class="stat-number mt-2" id="lowStockCount">8</h3>
                                <p class="mb-0">Stok Rendah</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card dashboard-card text-white" style="background: linear-gradient(135deg, #43e97b, #38f9d7);">
                            <div class="card-body text-center">
                                <i class="bi bi-box-seam fs-1"></i>
                                <h3 class="stat-number mt-2">42</h3>
                                <p class="mb-0">Total Produk</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Kolom Kiri: Daftar Produk -->
                    <div class="col-md-8">
                        <div class="card border-0 mb-4">
                            <div class="card-body">
                                <h2 class="section-title">DAFTAR PRODUK</h2>
                                
                                <!-- Filter Produk -->
                                <div class="mb-4">
                                    <div class="d-flex flex-wrap">
                                        <button type="button" class="btn filter-btn active" data-filter="all">Semua Produk</button>
                                        <button type="button" class="btn filter-btn" data-filter="low-stock">Stok Rendah</button>
                                        <button type="button" class="btn filter-btn" data-filter="Alat Tulis">Alat Tulis</button>
                                    </div>
                                </div>
                                
                                <!-- Daftar Produk -->
                                <div class="row" id="productList">
                                    <!-- Produk akan dimuat melalui JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Kolom Kanan: Keranjang Belanja -->
                    <div class="col-md-4">
                        <div class="cart-container p-4">
                            <h2 class="section-title">KERANJANG BELANJA</h2>
                            
                            <!-- Pilih Pelanggan -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">Pilih Pelanggan</label>
                                <select class="form-select customer-select" id="customerSelect">
                                    <option selected>Pelanggan Umum</option>
                                    <option>Pelanggan Umum</option>
                                    <option>Pelanggan VIP</option>
                                    <option>Pelanggan 3</option>
                                </select>
                            </div>
                            
                            <!-- Daftar Item di Keranjang -->
                            <div id="cartItems" class="mb-4" style="max-height: 400px; overflow-y: auto;">
                                <div class="empty-cart">
                                    <i class="bi bi-cart-x"></i>
                                    <h5>Keranjang Kosong</h5>
                                    <p class="text-muted">Tambahkan produk dari daftar di samping</p>
                                </div>
                            </div>
                            
                            <!-- Total Pembayaran -->
                            <div class="cart-total mb-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fs-5">Total Pembayaran:</span>
                                    <span class="fs-4" id="cartTotal">Rp 0</span>
                                </div>
                            </div>
                            
                            <!-- Tombol Aksi -->
                            <div class="d-grid gap-3">
                                <button class="btn btn-pay" data-bs-toggle="modal" data-bs-target="#paymentModal">
                                    <i class="bi bi-credit-card me-2"></i> PROSES PEMBAYARAN
                                </button>
                                <button class="btn btn-clear" onclick="clearCart()">
                                    <i class="bi bi-trash me-2"></i> KOSONGKAN KERANJANG
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Pembayaran -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="paymentModalLabel"><i class="bi bi-credit-card me-2"></i> Konfirmasi Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Total Pembayaran</label>
                        <input type="text" class="form-control form-control-lg fw-bold text-success" id="totalPayment" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Metode Pembayaran</label>
                        <select class="form-select" id="paymentMethod">
                            <option value="cash">Tunai</option>
                            <option value="debit">Kartu Debit</option>
                            <option value="credit">Kartu Kredit</option>
                            <option value="transfer">Transfer Bank</option>
                        </select>
                    </div>
                    <div class="mb-3" id="cashPaymentSection">
                        <label class="form-label fw-bold">Jumlah Uang Diberikan</label>
                        <input type="number" class="form-control" id="cashGiven" min="0" placeholder="Masukkan jumlah uang">
                        <div class="mt-2 p-2 bg-light rounded">
                            <strong>Kembalian: <span id="changeAmount" class="text-success">Rp 0</span></strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Catatan (Opsional)</label>
                        <textarea class="form-control" id="paymentNote" rows="2" placeholder="Tambahkan catatan transaksi..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-success" onclick="completePayment()">
                        <i class="bi bi-check-circle me-2"></i> Selesaikan Pembayaran
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Struk -->
    <div class="modal fade" id="receiptModal" tabindex="-1" aria-labelledby="receiptModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="receiptModalLabel"><i class="bi bi-receipt me-2"></i> Struk Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body receipt" id="receiptContent">
                    <!-- Struk akan diisi oleh JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="button" class="btn btn-primary" onclick="printReceipt()">
                        <i class="bi bi-printer me-2"></i> Cetak Struk
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Data produk dari database (dalam contoh ini menggunakan array)
        let products = [
            {id: 1, name: "Buku Tulis", price: 5000, stock: 100, category: "Alat Tulis"},
            {id: 2, name: "Buku Gambar A4", price: 7000, stock: 80, category: "Alat Tulis"},
            {id: 3, name: "Bolpoin", price: 3000, stock: 24, category: "Alat Tulis"},
            {id: 4, name: "Penghapus", price: 1000, stock: 32, category: "Alat Tulis"},
            {id: 5, name: "Spidol", price: 7000, stock: 20, category: "Alat Tulis"},
            {id: 6, name: "Pensil", price: 2000, stock: 50, category: "Alat Tulis"},
            {id: 7, name: "Cutter", price: 6500, stock: 30, category: "Alat Tulis"},
            {id: 8, name: "Penggaris", price: 3000, stock: 20, category: "Alat Tulis"},
            {id: 9, name: "Tipe X", price: 4000, stock: 18, category: "Alat Tulis"},
        ];

        // Data keranjang
        let cart = [];
        let cartTotal = 0;
        let transactionCount = 15; // Counter untuk transaksi hari ini

        // Fungsi untuk memuat produk ke halaman
        function loadProducts() {
            const productList = document.getElementById('productList');
            productList.innerHTML = '';
            
            products.forEach(product => {
                const stockClass = product.stock < 5 ? 'low-stock' : '';
                const isOutOfStock = product.stock === 0;
                const stockText = isOutOfStock ? 
                    '<span class="out-of-stock">STOK HABIS</span>' : 
                    `<span class="${stockClass}">Stok: ${product.stock}</span>`;
                
                const buttonHTML = isOutOfStock ? 
                    '<button class="btn btn-secondary mt-auto w-100 btn-add-cart" disabled>' +
                    '<i class="bi bi-cart-x me-2"></i> Stok Habis</button>' :
                    `<button class="btn btn-primary mt-auto w-100 btn-add-cart" onclick="addToCart(${product.id})">` +
                    '<i class="bi bi-cart-plus me-2"></i> Tambah ke Keranjang</button>';
                
                const productHTML = `
                    <div class="col-md-6 col-lg-4 mb-3 product-item" data-category="${product.category}" data-stock="${product.stock}">
                        <div class="product-card h-100">
                            <div class="product-header">
                                <div class="product-name">${product.name}</div>
                            </div>
                            <div class="product-body d-flex flex-column">
                                <div class="product-price mb-2">Rp ${product.price.toLocaleString('id-ID')}</div>
                                <div class="product-stock mb-3">${stockText}</div>
                                ${buttonHTML}
                            </div>
                        </div>
                    </div>
                `;
                
                productList.innerHTML += productHTML;
            });
            
            updateLowStockCount();
        }

        // Fungsi untuk memperbarui jumlah produk stok rendah
        function updateLowStockCount() {
            const lowStockCount = products.filter(product => product.stock < 5 && product.stock > 0).length;
            document.getElementById('lowStockCount').textContent = lowStockCount;
        }

        // Fungsi untuk menambah item ke keranjang
        function addToCart(productId) {
            const product = products.find(p => p.id === productId);
            
            if (!product) {
                showNotification('Produk tidak ditemukan!', 'danger');
                return;
            }
            
            if (product.stock <= 0) {
                showNotification('Stok produk habis!', 'danger');
                return;
            }
            
            // Cek apakah produk sudah ada di keranjang
            const existingItem = cart.find(item => item.id === productId);
            
            if (existingItem) {
                // Cek apakah stok masih mencukupi
                if (existingItem.quantity + 1 > product.stock) {
                    showNotification(`Stok ${product.name} tidak mencukupi! Stok tersisa: ${product.stock}`, 'warning');
                    return;
                }
                existingItem.quantity += 1;
            } else {
                cart.push({
                    id: product.id,
                    name: product.name,
                    price: product.price,
                    quantity: 1
                });
            }
            
            // Kurangi stok produk
            product.stock -= 1;
            
            updateCartDisplay();
            loadProducts(); // Memperbarui tampilan produk untuk memperlihatkan stok terbaru
            
            // Tampilkan notifikasi
            showNotification(`${product.name} berhasil ditambahkan ke keranjang`, 'success');
        }
        
        // Fungsi untuk memperbarui tampilan keranjang
        function updateCartDisplay() {
            const cartItemsContainer = document.getElementById('cartItems');
            const cartTotalElement = document.getElementById('cartTotal');
            
            // Hitung total
            cartTotal = cart.reduce((total, item) => total + (item.price * item.quantity), 0);
            
            // Update tampilan total
            cartTotalElement.textContent = 'Rp ' + cartTotal.toLocaleString('id-ID');
            
            // Update daftar item
            if (cart.length === 0) {
                cartItemsContainer.innerHTML = `
                    <div class="empty-cart">
                        <i class="bi bi-cart-x"></i>
                        <h5>Keranjang Kosong</h5>
                        <p class="text-muted">Tambahkan produk dari daftar di samping</p>
                    </div>
                `;
            } else {
                let cartHTML = '';
                cart.forEach(item => {
                    cartHTML += `
                        <div class="cart-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="fw-bold">${item.name}</div>
                                    <div class="text-muted small">Rp ${item.price.toLocaleString('id-ID')}</div>
                                </div>
                                <div class="d-flex flex-column align-items-end">
                                    <div class="quantity-control mb-2">
                                        <button class="btn btn-sm" onclick="updateQuantity(${item.id}, ${item.quantity - 1})">-</button>
                                        <span class="mx-2 fw-bold">${item.quantity}</span>
                                        <button class="btn btn-sm" onclick="updateQuantity(${item.id}, ${item.quantity + 1})">+</button>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <span class="fw-bold me-2">Rp ${(item.price * item.quantity).toLocaleString('id-ID')}</span>
                                        <button class="btn btn-sm btn-outline-danger" onclick="removeFromCart(${item.id})">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                cartItemsContainer.innerHTML = cartHTML;
            }
        }
        
        // Fungsi untuk mengupdate jumlah item
        function updateQuantity(productId, newQuantity) {
            if (newQuantity <= 0) {
                removeFromCart(productId);
                return;
            }
            
            const item = cart.find(item => item.id === productId);
            const product = products.find(p => p.id === productId);
            
            if (item && product) {
                // Hitung perubahan jumlah
                const quantityChange = newQuantity - item.quantity;
                
                // Cek apakah stok mencukupi
                if (quantityChange > 0 && product.stock < quantityChange) {
                    showNotification(`Stok ${product.name} tidak mencukupi! Stok tersisa: ${product.stock}`, 'warning');
                    return;
                }
                
                // Update stok produk
                product.stock -= quantityChange;
                
                // Update jumlah item di keranjang
                item.quantity = newQuantity;
                
                updateCartDisplay();
                loadProducts(); // Memperbarui tampilan produk
            }
        }
        
        // Fungsi untuk menghapus item dari keranjang
        function removeFromCart(productId) {
            const item = cart.find(item => item.id === productId);
            const product = products.find(p => p.id === productId);
            
            if (item && product) {
                // Kembalikan stok produk
                product.stock += item.quantity;
                
                // Hapus dari keranjang
                cart = cart.filter(item => item.id !== productId);
                
                updateCartDisplay();
                loadProducts(); // Memperbarui tampilan produk
                showNotification(`${item.name} dihapus dari keranjang`, 'warning');
            }
        }
        
        // Fungsi untuk mengosongkan keranjang
        function clearCart() {
            if (cart.length === 0) {
                showNotification('Keranjang sudah kosong!', 'info');
                return;
            }
            
            if (confirm('Apakah Anda yakin ingin mengosongkan keranjang?')) {
                // Kembalikan semua stok produk
                cart.forEach(item => {
                    const product = products.find(p => p.id === item.id);
                    if (product) {
                        product.stock += item.quantity;
                    }
                });
                
                cart = [];
                updateCartDisplay();
                loadProducts(); // Memperbarui tampilan produk
                showNotification('Keranjang berhasil dikosongkan', 'info');
            }
        }
        
        // Fungsi untuk memproses pembayaran
        function processPayment() {
            if (cart.length === 0) {
                showNotification('Keranjang belanja kosong! Tambahkan produk terlebih dahulu.', 'warning');
                return;
            }
            
            // Tampilkan modal pembayaran
            const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal'));
            document.getElementById('totalPayment').value = 'Rp ' + cartTotal.toLocaleString('id-ID');
            document.getElementById('cashGiven').value = '';
            document.getElementById('changeAmount').textContent = 'Rp 0';
            paymentModal.show();
        }
        
        // Fungsi untuk menghitung kembalian
        function calculateChange() {
            const cashGiven = parseFloat(document.getElementById('cashGiven').value) || 0;
            const change = cashGiven - cartTotal;
            
            if (change >= 0) {
                document.getElementById('changeAmount').textContent = 'Rp ' + change.toLocaleString('id-ID');
            } else {
                document.getElementById('changeAmount').textContent = 'Rp 0';
            }
        }
        
        // Fungsi untuk menyelesaikan pembayaran
        function completePayment() {
            const paymentMethod = document.getElementById('paymentMethod').value;
            const cashGiven = parseFloat(document.getElementById('cashGiven').value) || 0;
            const change = cashGiven - cartTotal;
            const note = document.getElementById('paymentNote').value;
            
            // Validasi untuk pembayaran tunai
            if (paymentMethod === 'cash' && cashGiven < cartTotal) {
                showNotification('Jumlah uang yang diberikan kurang!', 'danger');
                return;
            }
            
            // Simpan data transaksi
            const transaction = {
                id: 'TRX' + Date.now(),
                customer: document.getElementById('customerSelect').value,
                items: [...cart],
                total: cartTotal,
                paymentMethod: paymentMethod,
                cashGiven: paymentMethod === 'cash' ? cashGiven : null,
                change: paymentMethod === 'cash' ? change : null,
                note: note,
                date: new Date().toLocaleString('id-ID')
            };
            
            // Tampilkan struk
            showReceipt(transaction);
            
            // Update counter transaksi
            transactionCount += 1;
            document.getElementById('todayTransactions').textContent = transactionCount;
            
            // Reset keranjang (stok sudah dikurangi sebelumnya)
            cart = [];
            updateCartDisplay();
            
            // Tutup modal pembayaran
            const paymentModal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
            paymentModal.hide();
            
            showNotification('Pembayaran berhasil diproses!', 'success');
        }
        
        // Fungsi untuk menampilkan struk
        function showReceipt(transaction) {
            let receiptHTML = `
                <div class="receipt-header mb-3">
                    <h5>TOKO ALAT TULIS</h5>
                    <p>Jl. Contoh No. 123<br>Telp: (021) 123456</p>
                </div>
                <div class="mb-3">
                    <div class="receipt-item">
                        <span>No. Transaksi:</span>
                        <span>${transaction.id}</span>
                    </div>
                    <div class="receipt-item">
                        <span>Tanggal:</span>
                        <span>${transaction.date}</span>
                    </div>
                    <div class="receipt-item">
                        <span>Kasir:</span>
                        <span>Admin</span>
                    </div>
                    <div class="receipt-item">
                        <span>Pelanggan:</span>
                        <span>${transaction.customer}</span>
                    </div>
                </div>
                <hr>
                <div class="mb-3">
            `;
            
            transaction.items.forEach(item => {
                receiptHTML += `
                    <div class="receipt-item">
                        <span>${item.name}</span>
                        <span>${item.quantity}x</span>
                    </div>
                    <div class="receipt-item">
                        <span></span>
                        <span>Rp ${(item.price * item.quantity).toLocaleString('id-ID')}</span>
                    </div>
                `;
            });
            
            receiptHTML += `
                </div>
                <hr>
                <div class="mb-3">
                    <div class="receipt-item">
                        <span>Total:</span>
                        <span>Rp ${transaction.total.toLocaleString('id-ID')}</span>
                    </div>
                    <div class="receipt-item">
                        <span>Metode:</span>
                        <span>${getPaymentMethodName(transaction.paymentMethod)}</span>
                    </div>
            `;
            
            if (transaction.paymentMethod === 'cash') {
                receiptHTML += `
                    <div class="receipt-item">
                        <span>Tunai:</span>
                        <span>Rp ${transaction.cashGiven.toLocaleString('id-ID')}</span>
                    </div>
                    <div class="receipt-item">
                        <span>Kembali:</span>
                        <span>Rp ${transaction.change.toLocaleString('id-ID')}</span>
                    </div>
                `;
            }
            
            receiptHTML += `
                </div>
                <hr>
                <div class="receipt-footer mt-3">
                    <p>Terima kasih atas kunjungan Anda</p>
                    <p>*** ${transaction.note || 'Semoga hari Anda menyenangkan'} ***</p>
                </div>
            `;
            
            document.getElementById('receiptContent').innerHTML = receiptHTML;
            
            // Tampilkan modal struk
            const receiptModal = new bootstrap.Modal(document.getElementById('receiptModal'));
            receiptModal.show();
        }
        
        // Fungsi untuk mendapatkan nama metode pembayaran
        function getPaymentMethodName(method) {
            const methods = {
                'cash': 'Tunai',
                'debit': 'Kartu Debit',
                'credit': 'Kartu Kredit',
                'transfer': 'Transfer Bank'
            };
            return methods[method] || method;
        }
        
        // Fungsi untuk mencetak struk
        function printReceipt() {
            const receiptContent = document.getElementById('receiptContent').innerHTML;
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Struk Pembayaran</title>
                    <style>
                        body { 
                            font-family: 'Courier New', monospace; 
                            font-size: 14px; 
                            line-height: 1.2;
                            padding: 10px;
                        }
                        .receipt-header, .receipt-footer { text-align: center; }
                        .receipt-item { display: flex; justify-content: space-between; }
                        hr { border-top: 1px dashed #000; }
                        @media print {
                            body { margin: 0; }
                        }
                    </style>
                </head>
                <body>
                    ${receiptContent}
                    <script>
                        window.onload = function() {
                            window.print();
                            setTimeout(function() {
                                window.close();
                            }, 500);
                        };
                    <\/script>
                </body>
                </html>
            `);
            printWindow.document.close();
        }
        
        // Fungsi untuk menampilkan notifikasi
        function showNotification(message, type) {
            // Buat elemen notifikasi
            const notification = document.createElement('div');
            notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 1050; min-width: 300px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            
            // Tambahkan ke body
            document.body.appendChild(notification);
            
            // Hapus otomatis setelah 3 detik
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 3000);
        }
        
        // Event listener untuk filter produk
        document.querySelectorAll('.filter-btn').forEach(button => {
            button.addEventListener('click', function() {
                // Hapus kelas active dari semua tombol
                document.querySelectorAll('.filter-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                
                // Tambahkan kelas active ke tombol yang diklik
                this.classList.add('active');
                
                // Filter produk berdasarkan kategori
                const filter = this.getAttribute('data-filter');
                filterProducts(filter);
            });
        });
        
        // Fungsi untuk memfilter produk
        function filterProducts(filter) {
            const productItems = document.querySelectorAll('.product-item');
            
            productItems.forEach(item => {
                const category = item.getAttribute('data-category');
                const stock = parseInt(item.getAttribute('data-stock'));
                
                switch(filter) {
                    case 'all':
                        item.style.display = 'block';
                        break;
                    case 'low-stock':
                        item.style.display = stock < 5 ? 'block' : 'none';
                        break;
                    case 'Alat Tulis':
                        item.style.display = category === 'Alat Tulis' ? 'block' : 'none';
                        break;
                }
            });
        }
        
        // Event listener untuk menghitung kembalian
        document.getElementById('cashGiven').addEventListener('input', calculateChange);
        
        // Event listener untuk menampilkan/menyembunyikan input tunai
        document.getElementById('paymentMethod').addEventListener('change', function() {
            const cashSection = document.getElementById('cashPaymentSection');
            cashSection.style.display = this.value === 'cash' ? 'block' : 'none';
        });
        
        // Event listener untuk tombol bayar
        document.querySelector('.btn-pay').addEventListener('click', processPayment);
        
        // Inisialisasi
        document.addEventListener('DOMContentLoaded', function() {
            loadProducts();
            
            // Tambahkan animasi saat halaman dimuat
            const cards = document.querySelectorAll('.dashboard-card, .product-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });
    </script>
</body>
</html>