<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['username'] = $admin['username'];
        header('Location: articles.php');
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="login-card">
            <h1>Admin Login</h1>
            <?php if (isset($error)): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required aria-label="Username">
                <input type="password" name="password" placeholder="Password" required aria-label="Password">
                <button type="submit" class="login-btn">Login</button>
            </form>
        </div>
    </div>
</body>
</html>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg,rgb(255, 255, 255),rgb(255, 255, 255));
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .container {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        width: 100%;
        max-width: 450px;
        background: rgba(255, 255, 255, 0); 
    }

    .login-card {
        background: rgba(255, 255, 255, 0.9);
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        width: 100%;
        text-align: center;
        transition: all 0.3s ease-in-out;
    }

    .login-card:hover {
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
    }

    h1 {
        font-size: 2.2rem;
        margin-bottom: 25px;
        color: #333;
        font-weight: 600;
    }

    input {
        width: 100%;
        padding: 15px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 1.1rem;
        background: #f9f9f9;
        transition: all 0.3s ease;
    }

    input:focus {
        border-color: #007bff;
        box-shadow: 0 0 8px rgba(0, 123, 255, 0.5);
        outline: none;
    }

    .login-btn {
        width: 100%;
        padding: 15px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 1.1rem;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .login-btn:hover {
        background-color: #0056b3;
    }

    .error {
        color: #d9534f;
        font-size: 0.9rem;
        margin-bottom: 15px;
    }

    @keyframes bounceIn {
        0% {
            transform: scale(0.8);
            opacity: 0;
        }
        50% {
            transform: scale(1.05);
            opacity: 0.5;
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    .login-card {
        animation: bounceIn 0.7s ease-out;
    }

    @media (max-width: 600px) {
        .login-card {
            padding: 30px;
        }
    }
</style>
<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['username'] = $admin['username'];
        header('Location: articles.php');
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="login-card">
            <h1>Admin Login</h1>
            <?php if (isset($error)): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required aria-label="Username">
                <input type="password" name="password" placeholder="Password" required aria-label="Password">
                <button type="submit" class="login-btn">Login</button>
            </form>
        </div>
    </div>
</body>
</html>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg,rgb(255, 255, 255),rgb(255, 255, 255));
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .container {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        width: 100%;
        max-width: 450px;
        background: rgba(255, 255, 255, 0); 
    }

    .login-card {
        background: rgba(255, 255, 255, 0.9);
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        width: 100%;
        text-align: center;
        transition: all 0.3s ease-in-out;
    }

    .login-card:hover {
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
    }

    h1 {
        font-size: 2.2rem;
        margin-bottom: 25px;
        color: #333;
        font-weight: 600;
    }

    input {
        width: 100%;
        padding: 15px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 1.1rem;
        background: #f9f9f9;
        transition: all 0.3s ease;
    }

    input:focus {
        border-color: #007bff;
        box-shadow: 0 0 8px rgba(0, 123, 255, 0.5);
        outline: none;
    }

    .login-btn {
        width: 100%;
        padding: 15px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 1.1rem;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .login-btn:hover {
        background-color: #0056b3;
    }

    .error {
        color: #d9534f;
        font-size: 0.9rem;
        margin-bottom: 15px;
    }

    @keyframes bounceIn {
        0% {
            transform: scale(0.8);
            opacity: 0;
        }
        50% {
            transform: scale(1.05);
            opacity: 0.5;
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    .login-card {
        animation: bounceIn 0.7s ease-out;
    }

    @media (max-width: 600px) {
        .login-card {
            padding: 30px;
        }
    }
</style>
<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['username'] = $admin['username'];
        header('Location: articles.php');
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="login-card">
            <h1>Admin Login</h1>
            <?php if (isset($error)): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <form method="POST">
                <input type="text" name="username" placeholder="Username" required aria-label="Username">
                <input type="password" name="password" placeholder="Password" required aria-label="Password">
                <button type="submit" class="login-btn">Login</button>
            </form>
        </div>
    </div>
</body>
</html>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Poppins', sans-serif;
        background: linear-gradient(135deg,rgb(255, 255, 255),rgb(255, 255, 255));
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }

    .container {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        width: 100%;
        max-width: 450px;
        background: rgba(255, 255, 255, 0); 
    }

    .login-card {
        background: rgba(255, 255, 255, 0.9);
        padding: 40px;
        border-radius: 15px;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        width: 100%;
        text-align: center;
        transition: all 0.3s ease-in-out;
    }

    .login-card:hover {
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.2);
    }

    h1 {
        font-size: 2.2rem;
        margin-bottom: 25px;
        color: #333;
        font-weight: 600;
    }

    input {
        width: 100%;
        padding: 15px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 1.1rem;
        background: #f9f9f9;
        transition: all 0.3s ease;
    }

    input:focus {
        border-color: #007bff;
        box-shadow: 0 0 8px rgba(0, 123, 255, 0.5);
        outline: none;
    }

    .login-btn {
        width: 100%;
        padding: 15px;
        background-color: #007bff;
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 1.1rem;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .login-btn:hover {
        background-color: #0056b3;
    }

    .error {
        color: #d9534f;
        font-size: 0.9rem;
        margin-bottom: 15px;
    }

    @keyframes bounceIn {
        0% {
            transform: scale(0.8);
            opacity: 0;
        }
        50% {
            transform: scale(1.05);
            opacity: 0.5;
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    .login-card {
        animation: bounceIn 0.7s ease-out;
    }

    @media (max-width: 600px) {
        .login-card {
            padding: 30px;
        }
    }
</style>
