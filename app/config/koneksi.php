<?php
/**
 * File Koneksi Database
 * Menggunakan PDO untuk keamanan lebih baik
 */

// Konfigurasi database
$host = 'localhost';
$dbname = 'inventaris_azizah';
$username = 'root';
$password = '';

try {
    // Buat koneksi PDO
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            // Mode error: lempar exception jika ada error
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            
            // Fetch mode default: array asosiatif
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            
            // Matikan emulasi prepared statements
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
