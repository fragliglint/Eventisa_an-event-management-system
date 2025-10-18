<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

echo "<h3>Generate Proper Password Hashes</h3>";

// Test different passwords to find the right one
$test_passwords = ['admin123', 'password', '123456', 'admin', '1234'];

foreach ($test_passwords as $test_pwd) {
    $hash = password_hash($test_pwd, PASSWORD_BCRYPT);
    $verify_current = password_verify($test_pwd, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
    
    echo "Testing: <strong>$test_pwd</strong><br>";
    echo "Hash: $hash<br>";
    echo "Verifies with current hash: " . ($verify_current ? "✅ YES" : "❌ NO") . "<br><br>";
}

// Generate new proper hash for admin123
$new_hash = password_hash('admin123', PASSWORD_BCRYPT);
echo "<h4>New proper hash for 'admin123':</h4>";
echo "<code>$new_hash</code><br>";

// Update users with the new correct hash
$stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE email IN (?, ?)");
$result = $stmt->execute([$new_hash, 'zahid@eventisa.com', 'john@example.com']);

echo "<h4>Update result: " . ($result ? "SUCCESS" : "FAILED") . "</h4>";

// Verify
$check = $db->prepare("SELECT email, password_hash FROM users WHERE email IN (?, ?)");
$check->execute(['zahid@eventisa.com', 'john@example.com']);
$users = $check->fetchAll(PDO::FETCH_ASSOC);

echo "<h4>Final Verification:</h4>";
foreach ($users as $user) {
    $works = password_verify('admin123', $user['password_hash']);
    echo $user['email'] . ": " . ($works ? "✅ LOGIN WILL WORK" : "❌ STILL BROKEN") . "<br>";
}
?>