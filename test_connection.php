<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

if ($db) {
    echo "✅ Database connected successfully!<br>";
    
    // Test if users exist
    $query = "SELECT * FROM users WHERE email IN ('zahid@eventisa.com', 'john@example.com')";
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($users) > 0) {
        echo "✅ Users found in database:<br>";
        foreach ($users as $user) {
            echo " - " . $user['email'] . " (" . $user['role'] . ")<br>";
        }
    } else {
        echo "❌ No users found in database<br>";
    }
} else {
    echo "❌ Database connection failed!";
}
?>