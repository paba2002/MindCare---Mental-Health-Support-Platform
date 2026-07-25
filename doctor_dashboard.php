<?php
session_start();
require 'db.php';

if (!isset($_SESSION['doctor_id'])) {
    header('Location: doctor_login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE doctors SET status = ? WHERE doctor_id = ?");
    $stmt->execute([$status, $_SESSION['doctor_id']]);
}

$doctor = $pdo->query("SELECT * FROM doctors WHERE doctor_id = {$_SESSION['doctor_id']}")->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #eef1f5;
            font-family: 'Arial', sans-serif;
        }
        .dashboard-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1 {
            color: #333;
        }
        .form-group {
            margin-top: 20px;
        }
        .btn-update {
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            border: none;
            width: 100%;
            margin-top: 10px;
        }
        .btn-links {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        .btn-links a {
            text-decoration: none;
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            flex: 1;
            margin: 5px;
            text-align: center;
        }
        .btn-links a:nth-child(2) {
            background-color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container dashboard-container">
        <h1>Welcome, Dr. <?= htmlspecialchars($doctor['username']) ?></h1>
        <img src="Images/doctor.png" alt="Dr" class="doctor-img" width=100>
        <form method="POST" class="form-group">
            <label for="status" class="form-label">Status:</label>
            <select name="status" id="status" class="form-control">
                <option value="available" <?= $doctor['status'] == 'available' ? 'selected' : '' ?>>Available</option>
                <option value="non_available" <?= $doctor['status'] == 'non_available' ? 'selected' : '' ?>>Non Available</option>
            </select>
            <button type="submit" class="btn-update">Update Status</button>
        </form>
        <div class="btn-links">
            <a href="find_patient.php">Go to Chats</a>
             
            <a href="https://docs.google.com/forms/d/e/1FAIpQLSeKwtOHPw3z-GMlRchLoLMXvqLbPRIWm-CUU2GSoPqX2elomw/viewform?usp=dialog" target="_blank">Contact Admin</a>
        </div>
    </div>
</body>
</html>
