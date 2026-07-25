<?php
session_start();
require 'db.php';

if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

$doctors = $pdo->query("SELECT * FROM doctors")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Admin Panel</h1>
        <h2>Doctors</h2>
        <ul>
            <?php foreach ($doctors as $doctor): ?>
                <li><?= htmlspecialchars($doctor['username']) ?></li>
            <?php endforeach; ?>
        </ul>
        <a href="articles.php">Manage Articles</a>
    </div>
</body>
</html>