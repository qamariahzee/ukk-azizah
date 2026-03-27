<?php
/**
 * Setup Admin Account
 * Buat user baru: azizah / azizah123
 */

// Konfigurasi database langsung
$host = 'localhost';
$dbname = 'inventaris_azizah';
$username = 'root';
$password = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "<h2>🔧 Setup Admin Account</h2>";
    
    // Generate password hash
    $new_username = 'azizah';
    $new_password = 'azizah123';
    $password_hash = password_hash($new_password, PASSWORD_BCRYPT);
    
    echo "<p>Username: <strong>$new_username</strong></p>";
    echo "<p>Password: <strong>$new_password</strong></p>";
    echo "<p>Hash: <code>$password_hash</code></p>";
    
    // Delete existing user if any
    $pdo->exec("DELETE FROM users WHERE username = '$new_username'");
    
    // Insert new user
    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $result = $stmt->execute([$new_username, $password_hash]);
    
    if ($result) {
        echo "<div style='background: #22c55e; color: white; padding: 20px; border-radius: 10px; margin-top: 20px;'>";
        echo "<h3>✅ User Created Successfully!</h3>";
        echo "<p>Username: <strong>gudang</strong></p>";
        echo "<p>Password: <strong>gudang123</strong></p>";
        echo "<p><a href='app/auth/login.php' style='color: white;'>➡️ Go to Login Page</a></p>";
        echo "</div>";
    }
    
    // Test verify
    echo "<h3>🧪 Verification Test:</h3>";
    $stmt = $pdo->query("SELECT * FROM users WHERE username = 'gudang'");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        $verify = password_verify('gudang123', $user['password']);
        echo "<p>Password verify result: " . ($verify ? "✅ SUCCESS" : "❌ FAILED") . "</p>";
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
