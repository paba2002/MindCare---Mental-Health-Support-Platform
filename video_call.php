<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) && !isset($_SESSION['doctor_id'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Call</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Video Call</h1>
        <video id="localVideo" autoplay muted></video>
        <video id="remoteVideo" autoplay></video>
        <button onclick="startCall()">Start Call</button>
        <button onclick="endCall()">End Call</button>
    </div>
    <script>
        function startCall() {
        }

        function endCall() {
        }
    </script>
</body>
</html>