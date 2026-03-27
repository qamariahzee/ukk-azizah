-- =====================================================
-- DATABASE: inventaris_azizah
-- Sistem Inventaris azizah Sederhana - UKK Level 3
-- =====================================================

-- Buat database
CREATE DATABASE IF NOT EXISTS inventaris_azizah;
USE inventaris_azizah;

-- =====================================================
-- TABEL: users
-- Untuk menyimpan data admin/user yang bisa login
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,  -- Disimpan dengan bcrypt
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABEL: barang
-- Untuk menyimpan data barang di azizah
-- =====================================================
CREATE TABLE IF NOT EXISTS barang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(100) NOT NULL,
    stok INT DEFAULT 0,
    harga DECIMAL(12,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- TABEL: transaksi
-- Untuk mencatat barang masuk dan keluar
-- =====================================================
CREATE TABLE IF NOT EXISTS transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_barang INT NOT NULL,
    jenis_transaksi ENUM('masuk', 'keluar') NOT NULL,
    jumlah INT NOT NULL,
    tanggal_transaksi DATETIME DEFAULT CURRENT_TIMESTAMP,
    keterangan TEXT,
    FOREIGN KEY (id_barang) REFERENCES barang(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =====================================================
-- DATA AWAL: Admin User
-- Password: admin123 (sudah di-hash dengan bcrypt)
-- =====================================================
INSERT INTO users (username, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- =====================================================
-- DATA CONTOH: Barang
-- =====================================================
INSERT INTO barang (nama_barang, stok, harga) VALUES 
('Laptop ASUS ROG', 10, 15000000.00),
('Mouse Logitech G502', 25, 850000.00),
('Keyboard Mechanical', 15, 1200000.00),
('Monitor Samsung 24"', 8, 2500000.00),
('Headset Gaming', 20, 450000.00);

-- =====================================================
-- DATA CONTOH: Transaksi
-- =====================================================
INSERT INTO transaksi (id_barang, jenis_transaksi, jumlah, keterangan) VALUES 
(1, 'masuk', 5, 'Pembelian dari supplier'),
(2, 'masuk', 10, 'Restok bulanan'),
(1, 'keluar', 2, 'Penjualan ke customer'),
(3, 'masuk', 8, 'Barang baru dari distributor'),
(4, 'keluar', 3, 'Pengiriman ke cabang');
