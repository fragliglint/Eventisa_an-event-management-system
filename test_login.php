<?php
require_once 'config/database.php';
session_start();

echo "<h3>Login Debug Test</h3>";

if ($_POST) {
    $database = new Database();
    $db = $database->getConnection();
    
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    echo "Trying to login with: <strong>$email</strong><br>";
    
    // Find user by email
    $query = "SELECT * FROM users WHERE email = ? AND status = 'active'";
    $stmt = $db->prepare($query);
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "✅ User found: " . $user['email'] . " (ID: " . $user['id'] . ")<br>";
        echo "Stored password hash: " . $user['password_hash'] . "<br>";
        echo "Input password: <strong>$password</strong><br>";
        
        // Test password verification
        $passwordVerified = password_verify($password, $user['password_hash']);
        echo "Password verification result: " . ($passwordVerified ? "✅ SUCCESS" : "❌ FAILED") . "<br>";
        
        if ($passwordVerified) {
            echo "🎉 LOGIN SUCCESSFUL!<br>";
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            echo "Session variables set!<br>";
        } else {
            echo "❌ Password doesn't match!<br>";
        }
    } else {
        echo "❌ User not found or inactive!<br>";
    }
}
?>

<form method="post" style="margin: 20px; padding: 20px; border: 1px solid #ccc;">
    <h4>Test Login Form</h4>
    Email: <input type="email" name="email" value="zahid@eventisa.com"><br><br>
    Password: <input type="password" name="password" value="admin123"><br><br>
    <input type="submit" value="Test Login">
</form>

<hr>
<h4>Quick Test Links:</h4>
<a href="test_login.php?test=admin">Test Admin Login</a> | 
<a href="test_login.php?test=user">Test User Login</a>

<?php
// Quick test via URL
if (isset($_GET['test'])) {
    if ($_GET['test'] == 'admin') {
        $_POST['email'] = 'zahid@eventisa.com';
        $_POST['password'] = 'admin123';
    } elseif ($_GET['test'] == 'user') {
        $_POST['email'] = 'john@example.com';
        $_POST['password'] = 'admin123';
    }
}
?>