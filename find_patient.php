<?php
session_start();
require 'db.php';

if (!isset($_SESSION['doctor_id'])) {
    header('Location: login.php');
    exit;
}

$doctor_id = $_SESSION['doctor_id'];
$patients = $pdo->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);

$unreadMessages = [];
foreach ($patients as $patient) {
    $conversation_id = $doctor_id . '_' . $patient['id'];
    $stmt = $pdo->prepare("SELECT COUNT(*) as message_count FROM chats WHERE conversation_id = ?");
    $stmt->execute([$conversation_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['message_count'] > 0) {
        $unreadMessages[$patient['id']] = $result['message_count'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Find a Patient</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            transition: transform 0.3s;
            position: relative;
        }
        .card:hover {
            transform: scale(1.05);
        }
        .patient-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            margin-bottom: 10px;
        }
        .specialization {
            font-size: 14px;
            color: #6c757d;
            margin-top: 5px;
        }
        .badge {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: red;
            color: white;
            padding: 5px 10px;
            border-radius: 50%;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Find a Patient</h1>
        <div class="row">
            <?php foreach ($patients as $patient): ?>
                <div class="col-md-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body text-center">
                            <?php if (isset($unreadMessages[$patient['id']])): ?>
                                <span class="badge">New Chat</span>
                            <?php endif; ?>
                            <img src="Images/user.png" alt="Patient" class="patient-img">
                            <h5 class="card-title"><?= htmlspecialchars($patient['username']) ?></h5>
                            <p class="specialization">Joined on: <?= date('F j, Y', strtotime($patient['created_at'])) ?></p>
                            <a href="chat.php?patient_id=<?= $patient['id'] ?>" class="btn btn-primary">Chat Now</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>