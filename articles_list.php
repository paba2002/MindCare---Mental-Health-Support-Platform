<?php
session_start();
require 'db.php';

$articles = $pdo->query("SELECT * FROM articles ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Articles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <h2 class="text-center mb-4">Uploaded Articles</h2>
        <div class="row">
            <?php foreach ($articles as $article): ?>
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <img src="<?= htmlspecialchars($article['image_path']) ?>" class="card-img-top" alt="<?= htmlspecialchars($article['title']) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($article['title']) ?></h5>
                            <p class="card-text"><?= htmlspecialchars(substr($article['content'], 0, 100)) ?>...</p>
                            <a href="#" class="btn btn-primary">Read More</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="mt-3 text-center">
            <a href="articles.php" class="btn btn-outline-secondary">Upload New Article</a>
        </div>
    </div>
</body>
</html>
