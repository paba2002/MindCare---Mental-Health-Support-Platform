<?php
session_start();
require 'db.php';

function generateMeaningfulUsername() {
    $adjectives = ['Brave', 'Happy', 'Clever', 'Wise', 'Strong', 'Swift', 'Bold', 'Gentle', 'Healthy', 'Calm'];
    $nouns = ['Tiger', 'Lion', 'Eagle', 'Phoenix', 'Bear', 'Shark', 'Wolf', 'Hawk', 'Dragon', 'Leopard'];

    $random_adjective = $adjectives[array_rand($adjectives)];
    $random_noun = $nouns[array_rand($nouns)];

    return $random_adjective . $random_noun;
}

function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = generateMeaningfulUsername();
    $ip_address = getUserIP();
    $battery_percentage = isset($_POST['battery']) ? intval($_POST['battery']) : null;

    $stmt = $pdo->prepare("INSERT INTO users (username, ip_address, battery_percentage) VALUES (?, ?, ?)");
    $stmt->execute([$username, $ip_address, $battery_percentage]);

    $_SESSION['user_id'] = $pdo->lastInsertId();
    $_SESSION['username'] = $username;

    header('Location: testai.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Mental Health Care</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: #fff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 350px;
        }
        .logo {
            width: 80px;
            margin-bottom: 20px;
        }
        h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }
        button {
            background: #007BFF;
            color: #fff;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: 0.3s;
            width: 100%;
        }
        button:hover {
            background: #0056b3;
        }
        a {
            display: block;
            margin-top: 15px;
            color: #007BFF;
            text-decoration: none;
            font-size: 14px;
            transition: 0.3s;
            cursor: pointer;
        }
        a:hover {
            color: #0056b3;
        }
    </style>
    <script>
        function getBatteryLevel() {
            if (navigator.getBattery) {
                navigator.getBattery().then(function(battery) {
                    document.getElementById('battery').value = Math.round(battery.level * 100);
                    document.getElementById('loginForm').submit();
                });
            } else {
                document.getElementById('loginForm').submit();
            }
        }
        
        function confirmDoctorRegistration() {
            let confirmAction = confirm("Do you want to register as a doctor?");
            if (confirmAction) {
                window.location.href = "https://docs.google.com/forms/d/e/1FAIpQLSeh7qek-zoSRrXQmNchlyHIY56LmortAADuDmPlb21C5q9ujQ/viewform?usp=dialog";
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <img src="Images/logo.png" alt="Mental Health Care Logo" class="logo">
        <h1>Welcome to Mental Health Care</h1>
        <form id="loginForm" method="POST">
            <input type="hidden" name="battery" id="battery">
            <button type="button" onclick="getBatteryLevel()">Get Started</button>
        </form>
        <a href="#" onclick="confirmDoctorRegistration()">Register as Doctor</a>
        <a href="doctor_login.php">Doctor Login</a>
        <a href="admin_login.php" style="color:rgb(255, 0, 25);">Administrator Portal</a>
    </div>
</body>
</html>