<?php
// Test koneksi dan password
require_once __DIR__ . '/app/config/koneksi.php';

echo "<h2>Test Database Connection</h2>";

// Check user
$stmt = $pdo->query("SELECT * FROM users WHERE username = 'azizah'");
$user = $stmt->fetch();

if ($user) {
    echo "<p>✅ User 'azizah' found!</p>";
    echo "<p>Password hash: <code>" . htmlspecialchars($user['password']) . "</code></p>";
    
    // Test password
    $test_password = 'azizah123';
    $verify = password_verify($test_password, $user['password']);
    
    echo "<p>Testing password 'azizah123': " . ($verify ? "✅ MATCH" : "❌ NOT MATCH") . "</p>";
    
    if (!$verify) {
        // Generate new hash
        $new_hash = password_hash('azizah123', PASSWORD_BCRYPT);
        echo "<p>New hash for 'azizah123': <code>" . $new_hash . "</code></p>";
        
        // Update password
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE username = 'azizah'");
        $result = $stmt->execute([$new_hash]);
        
        if ($result) {
            echo "<p style='color: green; font-weight: bold;'>✅ Password updated! Try login again.</p>";
        }
    }
} else {
    echo "<p>❌ User 'admin' not found!</p>";
}
