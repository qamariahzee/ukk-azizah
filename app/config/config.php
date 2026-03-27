<?php
/**
 * Konfigurasi Global Aplikasi
 * File ini mendeteksi base URL secara OTOMATIS
 * Jadi bisa dipakai di folder manapun!
 */

// Deteksi base URL otomatis berdasarkan lokasi file ini
$script_path = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));

// Cari root folder project (folder yang berisi folder 'app')
// Ini akan bekerja dari folder manapun di dalam project
$path_parts = explode('/', trim($script_path, '/'));

// Cek apakah ada 'app' dalam path, berarti kita di dalam subfolder
$base_parts = [];
foreach ($path_parts as $part) {
    if ($part === 'app') {
        break; // Stop sebelum 'app'
    }
    $base_parts[] = $part;
}

// Base URL - ini akan otomatis sesuai nama folder project
$base_url = '/' . implode('/', $base_parts);

// Kalau kosong (di root), set ke '/'
if ($base_url === '/') {
    $base_url = '';
}

// Simpan ke variabel global
define('BASE_URL', $base_url);

// Debug (uncomment untuk cek)
// echo "BASE_URL: " . BASE_URL;
