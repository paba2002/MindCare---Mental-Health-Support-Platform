<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) && !isset($_SESSION['doctor_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'] ?? null;
$doctor_id = $_SESSION['doctor_id'] ?? null;
$stmt = $pdo->prepare("DELETE FROM chats WHERE user_id = ? OR doctor_id = ?");
$stmt->execute([$user_id, $doctor_id]);

unset($_SESSION['user_id']);
unset($_SESSION['doctor_id']);
header('Location: login.php');
exit;
?>