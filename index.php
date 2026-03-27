<?php
/**
 * Index - Entry Point
 * Redirect ke dashboard atau login
 */

session_start();

// Jika sudah login, ke dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: app/dashboard.php');
} else {
    // Belum login, ke login page
    header('Location: app/auth/login.php');
}
exit;
