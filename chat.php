<?php
session_start();
require 'db.php';

define('ENCRYPTION_KEY', getenv('MINDCARE_ENCRYPTION_KEY') ?: '');
define('ENCRYPTION_IV', getenv('MINDCARE_ENCRYPTION_IV') ?: '');

define('ENCRYPTION_METHOD', 'AES-256-CBC');

function encryptMessage($message) {
    return openssl_encrypt($message, ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, ENCRYPTION_IV);
}

function decryptMessage($encryptedMessage) {
    return openssl_decrypt($encryptedMessage, ENCRYPTION_METHOD, ENCRYPTION_KEY, 0, ENCRYPTION_IV);
}


if (!isset($_SESSION['user_id']) && !isset($_SESSION['doctor_id'])) {
    header('Location: login.php');
    exit;
}

if (isset($_SESSION['doctor_id']) && isset($_GET['patient_id'])) {
    $doctor_id = $_SESSION['doctor_id'];
    $patient_id = $_GET['patient_id']; 
    $conversation_id = $doctor_id < $patient_id ? $doctor_id . "_" . $patient_id : $patient_id . "_" . $doctor_id;
} else {
    $user_id = $_SESSION['user_id'];
    $doctor_id = $_SESSION['doctor_id'] ?? null;
    $conversation_id = (min($user_id, $doctor_id) . "_" . max($user_id, $doctor_id));
}

$stmt = $pdo->prepare("SELECT COUNT(*) FROM chats WHERE conversation_id = ?");
$stmt->execute([$conversation_id]);
$conversation_exists = $stmt->fetchColumn() > 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $message = trim($_POST['message']);
    if (!empty($message)) {
        if ($conversation_exists) {
            $stmt = $pdo->prepare("INSERT INTO chats (user_id, doctor_id, message, conversation_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user_id ?? null, $doctor_id ?? null, encryptMessage($message), $conversation_id]);
            unset($_POST['message']);
        } else {
            $stmt = $pdo->prepare("INSERT INTO chats (user_id, doctor_id, message, conversation_id) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user_id ?? null, $doctor_id ?? null, encryptMessage($message), $conversation_id]);
            $conversation_exists = true; 
        }
    }
}

$chats = $pdo->prepare("SELECT * FROM chats WHERE conversation_id = ? ORDER BY sent_at ASC");
$chats->execute([$conversation_id]);
$chats = $chats->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Virtual Chat</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f7fc;
            color: #333;
        }
        .container {
            max-width: 800px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-top: 50px;
        }
        .chat-container {
            display: flex;
            flex-direction: column;
            height: 80vh;
        }
        .chat-header {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.5rem;
            font-weight: 600;
            color: #007bff;
        }
        .chat-box {
            flex-grow: 1;
            overflow-y: auto;
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 10px;
            background-color: #f8f9fa;
        }
        .message {
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            max-width: 70%;
        }
        .user-message {
            background-color: #d1ecf1;
            align-self: flex-end;
        }
        .doctor-message {
            background-color: #e2e3e5;
            align-self: flex-start;
        }
        .input-group {
            display: flex;
            justify-content: space-between;
        }
        .form-control {
            border-radius: 30px;
            border: 1px solid #ccc;
            padding: 10px;
        }
        .send-button {
            background-color: #007bff;
            color: white;
            border-radius: 30px;
            border: none;
            padding: 10px 15px;
            cursor: pointer;
        }
        .send-button:hover {
            background-color: #0056b3;
        }
        .video-call-button,
        .end-button {
            background-color: #28a745;
            border: none;
            color: white;
            border-radius: 30px;
            padding: 10px 20px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }
        .video-call-button:hover,
        .end-button:hover {
            background-color: #218838;
        }
        .message strong {
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="container chat-container">
        <h2 class="chat-header">Virtual Chat</h2>
        <p class="text-center mb-4"><strong>Chat ID: <?= htmlspecialchars($conversation_id) ?></strong></p>
        
        <div class="chat-box">
            <?php foreach ($chats as $chat): ?>
                <div class="message <?= $chat['user_id'] ? 'user-message' : 'doctor-message' ?>">
                    <strong><?= htmlspecialchars($chat['user_id'] ? 'User' : 'Doctor') ?>:</strong><br>
                    <?php
function makeLinksClickable($text) {
    $text = htmlspecialchars(decryptMessage($text)); 
    return preg_replace(
        '/(https?:\/\/[^\s]+)/',
        '<a href="$1" target="_blank">$1</a>',
        $text
    );
}
?>

<?= makeLinksClickable($chat['message']) ?>

                </div>
            <?php endforeach; ?>
        </div>
        
        <form method="POST" class="input-group">
            <input type="text" name="message" class="form-control" placeholder="Type your message..." required>
            <button type="submit" class="send-button">Send</button>
        </form>
        
        <button class="video-call-button" id="start-video-call">Start Video Call</button>
        
        <button class="end-button" onclick="endChat()">End Chat</button>
    </div>

    <script src="https://meet.jit.si/external_api.js"></script>

    <script>
         document.addEventListener("DOMContentLoaded", function() {
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    });
        function endChat() {
            if (confirm('Are you sure you want to end the chat?')) {
                window.location.href = 'end_chat.php';
            }
        }

        document.getElementById("start-video-call").addEventListener("click", function(event) {
    event.preventDefault(); 

    var roomName = "mhcsessionchat-" + <?= json_encode($conversation_id) ?>;  
    var domain = "meet.jit.si";  
    var meetLink = "https://meet.jit.si/" + roomName;  

    var videoContainer = document.getElementById("video-call-container");
    if (!videoContainer) {
        videoContainer = document.createElement("div");
        videoContainer.id = "video-call-container";
        videoContainer.style.width = "100%";
        videoContainer.style.height = "500px";
        videoContainer.style.marginTop = "20px";
        document.body.appendChild(videoContainer);
    }

    var options = {
        roomName: roomName,
        width: "100%",
        height: "100%",
        parentNode: videoContainer, 
        configOverwrite: { 
            startWithAudioMuted: true, 
            startWithVideoMuted: true 
        },
        interfaceConfigOverwrite: {
            TOOLBAR_BUTTONS: ['microphone', 'camera', 'hangup', 'chat', 'sharedvideo', 'settings']
        }
    };

    var api = new JitsiMeetExternalAPI(domain, options);
    api.executeCommand('displayName', 'User/Doctor');

    sendMessage(meetLink);
});

function sendMessage(message) {
    fetch(window.location.href, {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded",
        },
        body: "message=" + encodeURIComponent(message)
    }).then(response => {
        if (response.ok) {
            console.log("Message sent successfully");
        } else {
            console.error("Failed to send message");
        }
    }).catch(error => {
        console.error("Error:", error);
    });
}


    </script>
</body>
</html>

