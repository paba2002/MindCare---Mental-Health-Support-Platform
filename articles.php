<?php
session_start();
require 'db.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'upload_article';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $page == 'register_doctor') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $specialization = $_POST['specialization'];  

    $stmt = $pdo->prepare("INSERT INTO doctors (username, password, specialization) VALUES (?, ?, ?)");
    $stmt->execute([$username, $password, $specialization]);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user'])) {
    $user_id = $_POST['user_id'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    header("Location: ?page=user_management");
    exit;
}

if ($page == 'user_management') {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
if ($page == 'doctor_management') {
    $stmt = $pdo->query("SELECT * FROM doctors ORDER BY created_at DESC");
    $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_doctor'])) {
    $doctor_id = $_POST['doctor_id'];
    $stmt = $pdo->prepare("DELETE FROM doctors WHERE doctor_id = ?");
    $stmt->execute([$doctor_id]);
    header("Location: ?page=doctor_management");
    exit;
}

if ($page == 'view_articles') {
    $stmt = $pdo->query("SELECT * FROM articles ORDER BY id DESC");
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $page == 'upload_article') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $image = $_FILES['image'];
    $article_id = isset($_POST['article_id']) ? $_POST['article_id'] : null;

    $upload_dir = 'assets/images/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if (!empty($image['name'])) {
        $image_path = $upload_dir . basename($image['name']);
        move_uploaded_file($image['tmp_name'], $image_path);
    } else {
        $image_path = isset($_POST['existing_image']) ? $_POST['existing_image'] : "";
    }

    if ($article_id) {
        $stmt = $pdo->prepare("UPDATE articles SET title=?, content=?, image_path=? WHERE id=?");
        $stmt->execute([$title, $content, $image_path, $article_id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO articles (title, content, image_path) VALUES (?, ?, ?)");
        $stmt->execute([$title, $content, $image_path]);
    }

    header("Location: ?page=view_articles");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_article'])) {
    $article_id = $_POST['article_id'];
    $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
    $stmt->execute([$article_id]);
    header("Location: ?page=view_articles");
    exit;
}

$edit_article = null;
if ($page == 'upload_article' && isset($_GET['edit_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
    $stmt->execute([$_GET['edit_id']]);
    $edit_article = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            background-color: #f8f9fa;
        }
        .sidebar {
            width: 250px;
            background: #343a40;
            padding: 20px;
            height: 100vh;
            position: fixed;
            color: white;
        }
        .sidebar a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            transition: 0.3s;
        }
        .sidebar a:hover, .sidebar a.active {
            background: #495057;
        }
        .content {
            margin-left: 270px;
            padding: 20px;
            width: 100%;
        }
        .card {
            max-width: 700px;
            margin: auto;
        }
        .article-card {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
            background: #fff;
            margin-bottom: 15px;
            position: relative;
            width: 300px;
            display: inline-block;
            margin-right: 15px;
        }
        .article-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
        }
        .content-preview {
            max-height: 60px;
            overflow: hidden;
            transition: max-height 0.3s ease-in-out;
        }
        .read-more {
            background: none;
            border: none;
            color: blue;
            cursor: pointer;
            font-size: 14px;
            padding: 5px 0;
        }
        .article-actions {
            margin-top: 10px;
        }
        .article-actions button {
            margin-right: 5px;
        }
        .btn-delete {
            background-color: red;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <div class="sidebar">
        <h4>Admin Panel</h4>
        <a href="?page=upload_article" class="<?= $page == 'upload_article' ? 'active' : '' ?>">Upload Articles</a>
        <a href="?page=view_articles" class="<?= $page == 'view_articles' ? 'active' : '' ?>">Manage Articles</a>
        <a href="?page=register_doctor" class="<?= $page == 'register_doctor' ? 'active' : '' ?>">Register a Doctor</a>
        <a href="?page=user_management" class="<?= $page == 'user_management' ? 'active' : '' ?>">User Management</a>
        <a href="?page=doctor_management" class="<?= $page == 'doctor_management' ? 'active' : '' ?>">Doctor Management</a>
        <a href="index.php" class="<?= $page == 'patient_portal' ? 'active' : '' ?>">Patient Portal</a>
    </div>

    <div class="content">
    <?php if ($page == 'upload_article'): ?>
        <div class="card shadow p-4">
            <h2 class="text-center"><?= $edit_article ? "Edit Article" : "Upload Article" ?></h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="article_id" value="<?= $edit_article['id'] ?? '' ?>">
                <input type="hidden" name="existing_image" value="<?= $edit_article['image_path'] ?? '' ?>">

                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($edit_article['title'] ?? '') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Content</label>
                    <textarea name="content" class="form-control" rows="4" required><?= htmlspecialchars($edit_article['content'] ?? '') ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Image</label>
                    <input type="file" name="image" class="form-control">
                    <?php if (!empty($edit_article['image_path'])): ?>
                        <p>Current Image: <img src="<?= htmlspecialchars($edit_article['image_path']) ?>" width="100"></p>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary w-100"><?= $edit_article ? "Update Article" : "Upload Article" ?></button>
            </form>
        </div>
        <?php elseif ($page == 'view_articles'): ?>
    <h2 class="text-center">Manage Published Articles</h2><br>
    <?php foreach ($articles as $article): ?>
        <div class="article-card card shadow p-3 mb-3">
            <img src="<?= htmlspecialchars($article['image_path']) ?>" width="100">
            <h5><?= htmlspecialchars($article['title']) ?></h5>
            
            <?php
            $content = htmlspecialchars($article['content']);
            $short_content = substr($content, 0, 100); 
            $needs_read_more = strlen($content) > 100;
            ?>

            <p>
                <span class="short-content"><?= $short_content ?><?= $needs_read_more ? '...' : '' ?></span>
                <span class="full-content d-none"><?= nl2br($content) ?></span>
            </p>

            <?php if ($needs_read_more): ?>
                <button class="btn btn-link btn-sm toggle-read-more">Read More</button>
            <?php endif; ?>

            <div>
                <a href="?page=upload_article&edit_id=<?= $article['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                    <button type="submit" name="delete_article" class="btn btn-danger btn-sm">Delete</button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
    <?php endif; ?>

    <script>
        document.querySelectorAll('.toggle-read-more').forEach(button => {
            button.addEventListener('click', function () {
                const articleCard = this.closest('.article-card');
                const shortContent = articleCard.querySelector('.short-content');
                const fullContent = articleCard.querySelector('.full-content');

                if (fullContent.classList.contains('d-none')) {
                    fullContent.classList.remove('d-none');
                    shortContent.style.display = 'none';
                    this.textContent = 'Read Less';
                } else {
                    fullContent.classList.add('d-none');
                    shortContent.style.display = 'inline';
                    this.textContent = 'Read More';
                }
            });
        });
    </script>

<?php if ($page == 'register_doctor'): ?>
    <h2 class="text-center mb-4">Register a New Doctor</h2>
    <div class="register-container">
        <form method="POST" class="register-form">
            <div class="input-group">
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="input-group">
                <input type="text" name="specialization" placeholder="Specialization" required>
            </div>
            <div class="input-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="input-group">
                <button type="submit" class="btn-submit">Register</button>
            </div>
        </form>
    </div>
<?php endif; ?>

<?php if ($page == 'user_management'): ?>
            <h2 class="text-center mb-4">User Management</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['id']) ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['created_at']) ?></td>
                            <td>
                                <form method="POST" style="display:inline-block;">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <button type="submit" name="delete_user" class="btn-delete" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?php if ($page == 'doctor_management'): ?>
            <h2 class="text-center mb-4">Doctor Management</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($doctors as $doctor): ?>
                        <tr>
                            <td><?= htmlspecialchars($doctor['doctor_id']) ?></td>
                            <td><?= htmlspecialchars($doctor['username']) ?></td>
                            <td><?= htmlspecialchars($doctor['status']) ?></td>
                            <td><?= htmlspecialchars($doctor['created_at']) ?></td>
                            <td>
                                <form method="POST" style="display:inline-block;">
                                    <input type="hidden" name="doctor_id" value="<?= $doctor['doctor_id'] ?>">
                                    <button type="submit" name="delete_doctor" class="btn-delete" onclick="return confirm('Are you sure you want to delete this doctor?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>

<style>
    body {
        font-family: 'Arial', sans-serif;
        background-color: #f4f7fc;
        margin: 0;
        padding: 0;
    }

    .register-container {
        max-width: 400px;
        margin: 40px auto;
        padding: 30px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .register-form .input-group {
        margin-bottom: 20px;
    }

    .register-form input {
        width: 100%;
        padding: 12px;
        font-size: 16px;
        border-radius: 8px;
        border: 1px solid #ddd;
        background-color: #f9f9f9;
        transition: all 0.3s;
    }

    .register-form input:focus {
        border-color: #007bff;
        background-color: #fff;
    }

    .register-form .btn-submit {
        width: 100%;
        padding: 12px;
        font-size: 18px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .register-form .btn-submit:hover {
        background-color: #0056b3;
    }

    h2 {
        font-size: 24px;
        color: #333;
    }

    .register-form input {
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
</style>

    </div>

    <script>
        function toggleContent(button) {
            let content = button.previousElementSibling;
            if (content.style.maxHeight === "none") {
                content.style.maxHeight = "60px";
                button.innerText = "Read More";
            } else {
                content.style.maxHeight = "none";
                button.innerText = "Read Less";
            }
        }

        function editArticle(id) {
            alert("Edit article with ID: " + id);
        }

        function deleteArticle(id) {
            if (confirm("Are you sure you want to delete this article?")) {
                alert("Delete article with ID: " + id);
            }
        }
    </script>

</body>
</html>
