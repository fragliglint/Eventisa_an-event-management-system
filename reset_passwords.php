<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// The correct hash for 'admin123'
$correct_hash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

echo "<h3>Password Reset Tool</h3>";

// Update admin user
$stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
$result1 = $stmt->execute([$correct_hash, 'zahid@eventisa.com']);
echo "Admin update: " . ($result1 ? "SUCCESS" : "FAILED") . "<br>";

// Update regular user  
$result2 = $stmt->execute([$correct_hash, 'john@example.com']);
echo "User update: " . ($result2 ? "SUCCESS" : "FAILED") . "<br>";

// Verify
$check = $db->prepare("SELECT email, password_hash FROM users WHERE email IN (?, ?)");
$check->execute(['zahid@eventisa.com', 'john@example.com']);
$users = $check->fetchAll(PDO::FETCH_ASSOC);

echo "<h4>Updated Users:</h4>";
foreach ($users as $user) {
    echo $user['email'] . " - Hash: " . $user['password_hash'] . "<br>";
    echo "Password verify test: " . (password_verify('admin123', $user['password_hash']) ? "✅ WORKS" : "❌ FAILS") . "<br><br>";
}
?>