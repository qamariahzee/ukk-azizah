<?php
/**
 * Header Template - Sidebar Navigation
 * Dengan Heroicons dan warna soft
 */

// Mulai session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Load config untuk BASE_URL dinamis
require_once __DIR__ . '/../config/config.php';

// Cek apakah sudah login (kecuali halaman login)
$current_file = basename($_SERVER['PHP_SELF']);
if ($current_file !== 'login.php' && !isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . '/app/auth/login.php');
    exit;
}

// Base URL - gunakan konstanta dari config.php
$base_url = BASE_URL;

// Ambil informasi halaman aktif
$current_page = basename($_SERVER['PHP_SELF'], '.php');
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Inventaris Azizah' ?> | UKK</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?= $base_url ?>/app/assets/css/vendor/bootstrap.min.css" rel="stylesheet">
    <link href="<?= $base_url ?>/app/assets/css/vendor/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="<?= $base_url ?>/app/assets/css/vendor/sweetalert2.min.css" rel="stylesheet">
    <link href="<?= $base_url ?>/app/assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-body-custom">
    <div class="app-layout">
        <!-- Sidebar -->
        <aside class="app-sidebar">
            <div class="sidebar-brand">
                <div class="brand-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                    </svg>
                </div>
                <span class="brand-text">Azizah<strong>App</strong></span>
            </div>
            
            <nav class="sidebar-menu">
                <p class="menu-label">Main Menu</p>
                <a href="<?= $base_url ?>/app/dashboard.php" class="menu-item <?= ($current_page === 'dashboard') ? 'active' : '' ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z" /></svg>
                    <span>Dashboard</span>
                </a>
                
                <a href="<?= $base_url ?>/app/barang/" class="menu-item <?= ($current_dir === 'barang') ? 'active' : '' ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" /></svg>
                    <span>Data Barang</span>
                </a>
                
                <p class="menu-label mt-4">Transaksi</p>
                <a href="<?= $base_url ?>/app/transaksi/masuk.php" class="menu-item <?= ($current_page === 'masuk') ? 'active' : '' ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15m0-3-3-3m0 0-3 3m3-3V15" /></svg>
                    <span>Barang Masuk</span>
                </a>
                
                <a href="<?= $base_url ?>/app/transaksi/keluar.php" class="menu-item <?= ($current_page === 'keluar') ? 'active' : '' ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 8.25H7.5a2.25 2.25 0 0 0-2.25 2.25v9a2.25 2.25 0 0 0 2.25 2.25h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25H15M9 12l3 3m0 0 3-3m-3 3V2.25" /></svg>
                    <span>Barang Keluar</span>
                </a>
                
                <a href="<?= $base_url ?>/app/transaksi/riwayat.php" class="menu-item <?= ($current_page === 'riwayat') ? 'active' : '' ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    <span>Riwayat Transaksi</span>
                </a>
                
                <p class="menu-label mt-4">Analitik</p>
                <a href="<?= $base_url ?>/app/laporan/" class="menu-item <?= ($current_dir === 'laporan') ? 'active' : '' ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" /></svg>
                    <span>Laporan</span>
                </a>
            </nav>
        </aside>

        <!-- Container Sebelah Kanan (Topbar + Main Content) -->
        <div class="app-main">
            <!-- Topbar (User Profile dipindah ke sini jadinya BEDA BANGET) -->
            <header class="app-topbar">
                <div class="topbar-left">
                    <h5 class="mb-0 fw-bold d-none d-md-block" style="color: var(--text-primary);"><?= $page_title ?? 'Dashboard' ?></h5>
                </div>
                <div class="topbar-right">
                    <div class="user-profile-badge">
                        <div class="avatar-circle">
                            <?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?>
                        </div>
                        <div class="user-info-top">
                            <span class="fw-bold"><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></span>
                            <span class="text-muted" style="font-size: 11px;">Admin</span>
                        </div>
                    </div>
                    <div style="width: 1px; height: 30px; background: var(--border-color); margin: 0 10px;"></div>
                    <a href="<?= $base_url ?>/app/auth/logout.php" class="btn-logout-icon" title="Logout">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" width="20" height="20">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                        </svg>
                    </a>
                </div>
            </header>

            <main class="app-content-area">
