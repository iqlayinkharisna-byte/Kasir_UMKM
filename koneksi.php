<?php
// Konfigurasi database
$host = "localhost";
$username = "root";
$password = "";
$database = "kasir_umkm";

// Membuat koneksi ke MySQL server (tanpa memilih database dulu)
$conn = new mysqli($host, $username, $password);

// Cek koneksi dasar ke MySQL server
if ($conn->connect_error) {
    die("Koneksi MySQL server gagal: " . $conn->connect_error);
}

// Cek dan buat database jika tidak ada
$check_db = $conn->query("CREATE DATABASE IF NOT EXISTS $database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
if ($check_db) {
    // Pilih database setelah berhasil dibuat
    $conn->select_db($database);
} else {
    die("Gagal membuat database: " . $conn->error);
}

// Set charset
$conn->set_charset("utf8mb4");

// Daftar query untuk membuat tabel
$tables = [
    "CREATE TABLE IF NOT EXISTS produk (
        produk_id INT AUTO_INCREMENT PRIMARY KEY,
        nama_produk VARCHAR(255) NOT NULL,
        harga DECIMAL(10,2) NOT NULL,
        stok INT NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB",
    
    "CREATE TABLE IF NOT EXISTS pelanggan (
        pelanggan_id INT AUTO_INCREMENT PRIMARY KEY,
        nama_pelanggan VARCHAR(255) NOT NULL,
        email VARCHAR(255),
        telepon VARCHAR(20),
        alamat TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB",
    
    "CREATE TABLE IF NOT EXISTS penjualan (
        id INT AUTO_INCREMENT PRIMARY KEY,
        pelanggan_id INT DEFAULT 1,
        total DECIMAL(10,2) NOT NULL,
        tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB",
    
    "CREATE TABLE IF NOT EXISTS detail_penjualan (
        id INT AUTO_INCREMENT PRIMARY KEY,
        penjualan_id INT NOT NULL,
        produk_id INT NOT NULL,
        jumlah INT NOT NULL,
        harga DECIMAL(10,2) NOT NULL,
        subtotal DECIMAL(10,2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB"
];

// Eksekusi setiap query untuk membuat tabel
foreach ($tables as $sql) {
    if (!$conn->query($sql)) {
        // Skip error jika tabel sudah ada
        if (strpos($conn->error, "already exists") === false) {
            error_log("Error creating table: " . $conn->error);
        }
    }
}

// Insert data sample untuk produk jika tabel kosong
$check_produk = $conn->query("SELECT COUNT(*) as count FROM produk");
if ($check_produk) {
    $row = $check_produk->fetch_assoc();
    if ($row['count'] == 0) {
        $sample_products = [
            "Buku Tulis" => [5000, 100],
            "Pulpen" => [3000, 50],
            "Pensil" => [2000, 80],
            "Penghapus" => [1500, 60],
            "Penggaris" => [4000, 30]
        ];
        
        foreach ($sample_products as $nama => $data) {
            $harga = $data[0];
            $stok = $data[1];
            $stmt = $conn->prepare("INSERT INTO produk (nama_produk, harga, stok) VALUES (?, ?, ?)");
            $stmt->bind_param("sdi", $nama, $harga, $stok);
            $stmt->execute();
        }
    }
}

// Insert data sample untuk pelanggan jika tabel kosong
$check_pelanggan = $conn->query("SELECT COUNT(*) as count FROM pelanggan");
if ($check_pelanggan) {
    $row = $check_pelanggan->fetch_assoc();
    if ($row['count'] == 0) {
        $sample_pelanggan = [
            ["Pelanggan Umum", "umum@example.com", "-", "-"],
            ["Budi Santoso", "budi@example.com", "08123456789", "Jl. Merdeka No. 123"],
            ["Siti Rahayu", "siti@example.com", "08198765432", "Jl. Sudirman No. 45"]
        ];
        
        foreach ($sample_pelanggan as $data) {
            $stmt = $conn->prepare("INSERT INTO pelanggan (nama_pelanggan, email, telepon, alamat) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $data[0], $data[1], $data[2], $data[3]);
            $stmt->execute();
        }
    }
}

// Cek akhir koneksi
if ($conn->error) {
    error_log("Final database error: " . $conn->error);
}

// Return koneksi yang berhasil
return $conn;
?>