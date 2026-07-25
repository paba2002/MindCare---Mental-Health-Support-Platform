<?php
$host = 'sql305.infinityfree.com'; // Check this from your hosting panel
$dbname = 'if0_38266121_mental_health_care'; // Use the actual database name from InfinityFree
$username = 'if0_38266121'; // Use the actual username from InfinityFree
$password = 'Y5qVCWI05CXSjr5'; // Use the correct password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false // Helps prevent SQL injection
    ]);
    
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
