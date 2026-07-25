<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$articles = $pdo->query("SELECT * FROM articles ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            height: 100vh;
            background-color: #f4f4f4;
        }
        .sidebar {
            width: 250px;
            background: #343a40;
            color: white;
            padding: 60px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .sidebar h2 {
            text-align: center;
            font-size: 20px;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
            transition: 0.3s;
            border-radius: 5px;
        }
        .sidebar a:hover {
            background: #495057;
        }
        .main-content {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }
        .article-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            transition: transform 0.3s ease-in-out;
        }
        .article-card:hover {
            transform: scale(1.02);
        }
        .article-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 5px;
            margin-top: 10px;
        }
        .article-card p {
            color: #555;
            font-size: 14px;
        }
        .read-more {
            text-decoration: none;
            color: #007BFF;
            font-weight: bold;
            display: inline-block;
            margin-top: 10px;
        }
        .read-more:hover {
            text-decoration: underline;
        }
        .share-btn {
            background: #007BFF;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }
        .share-btn:hover {
            background: #0056b3;
        }
        .social-icons {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        .social-icons a {
            text-decoration: none;
            font-size: 20px;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
        }
        .facebook { background: #3b5998; }
        .twitter { background: #1DA1F2; }
        .linkedin { background: #0077B5; }
        .whatsapp { background: #25D366; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div>
            <h2>Dashboard</h2>
            <a href="#">Home</a>
            <a href="find_doctor.php">Find a Doctor</a>
              
           
            <a href="https://docs.google.com/forms/d/e/1FAIpQLSeKwtOHPw3z-GMlRchLoLMXvqLbPRIWm-CUU2GSoPqX2elomw/viewform?usp=dialog" target="_blank">Contact Admin</a>
            <a href="support_us.php">Support Us</a>
       
        </div>
        <a href="#" onclick="confirmLogout()" style="color: red; text-align: center;">Logout</a>
    </div>
    <div class="main-content">
        <h3><strong>Welcome, <?= htmlspecialchars($_SESSION['username']) ?></strong></h3>
        <h4 class="mt-3">Latest Articles</h4>
        <div class="row">
            <?php foreach ($articles as $article): ?>
                <div class="col-md-6">
                <div class="article-card">
    <h3><?= htmlspecialchars($article['title']) ?></h3>
    <img src="<?= htmlspecialchars($article['image_path']) ?>" alt="<?= htmlspecialchars($article['title']) ?>">
    <p class="article-preview"><?= substr(htmlspecialchars($article['content']), 0, 100) ?>...</p>
    <p class="article-content" style="display: none;"><?= htmlspecialchars($article['content']) ?></p>
    <a href="javascript:void(0);" class="read-more" onclick="toggleContent(this)">Read More</a>
    
    <!-- Social Media Sharing -->
    <div class="social-icons">
        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode("https://yourwebsite.com/article_details.php?id=".$article['id']) ?>" class="facebook" target="_blank">F</a>
        <a href="https://twitter.com/intent/tweet?url=<?= urlencode("https://yourwebsite.com/article_details.php?id=".$article['id']) ?>&text=<?= urlencode($article['title']) ?>" class="twitter" target="_blank">X</a>
        <a href="https://www.linkedin.com/shareArticle?url=<?= urlencode("https://yourwebsite.com/article_details.php?id=".$article['id']) ?>&title=<?= urlencode($article['title']) ?>" class="linkedin" target="_blank">L</a>
        <a href="https://api.whatsapp.com/send?text=<?= urlencode($article['title']." - Read more: https://yourwebsite.com/article_details.php?id=".$article['id']) ?>" class="whatsapp" target="_blank">W</a>
    </div>
</div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>

function toggleContent(link) {
    const articleCard = link.closest('.article-card');
    const fullContent = articleCard.querySelector('.article-content');
    const previewText = articleCard.querySelector('.article-preview');

    if (fullContent.style.display === 'none') {
        fullContent.style.display = 'block';
        previewText.style.display = 'none';
        link.textContent = "Read Less";
    } else {
        fullContent.style.display = 'none';
        previewText.style.display = 'block';
        link.textContent = "Read More";
    }
}

        function confirmLogout() {
    if (confirm("Are you sure you want to logout?")) {
        window.location.href = "logout.php";
    }
}
        function shareArticle(title, image, content, id) {
            const url = `https://yourwebsite.com/article_details.php?id=${id}`;
            
            if (navigator.share) {
                navigator.share({
                    title: title,
                    text: content.substring(0, 100) + "...",
                    url: url
                }).then(() => {
                    console.log('Shared successfully');
                }).catch((error) => {
                    console.log('Error sharing', error);
                });
            } else {
                alert("Sharing not supported in this browser. You can copy the link: " + url);
            }
        }
    </script>
</body>
</html>
