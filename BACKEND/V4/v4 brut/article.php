<?php
require_once __DIR__ . "/includes/config.php";
require_once __DIR__ . "/includes/functions.php";

$is_logged_in = isset($_SESSION['user_id']);
$article_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($article_id <= 0) {
    header("Location: /index.php");
    exit;
}

$error = '';
$success = '';

// Récupérer l'article
try {
    $stmt = $pdo->prepare("SELECT a.*, u.username FROM articles a LEFT JOIN user u ON a.author_id = u.id WHERE a.id = ?");
    $stmt->execute([$article_id]);
    $article = $stmt->fetch();
    
    if (!$article) {
        header("Location: /index.php");
        exit;
    }
} catch (PDOException $e) {
    log_msg("Erreur récupération article : " . $e->getMessage());
    header("Location: /index.php");
    exit;
}

// Compter les likes
$likes_count = 0;
$user_liked = false;
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM likes WHERE article_id = ?");
    $stmt->execute([$article_id]);
    $likes_count = $stmt->fetch()['count'];
    
    if ($is_logged_in) {
        $stmt = $pdo->prepare("SELECT id FROM likes WHERE article_id = ? AND user_id = ?");
        $stmt->execute([$article_id, $_SESSION['user_id']]);
        $user_liked = $stmt->fetch() ? true : false;
    }
} catch (PDOException $e) {
    log_msg("Erreur comptage likes : " . $e->getMessage());
}

// Toggle like
if (isset($_POST['toggle_like']) && $is_logged_in) {
    try {
        if ($user_liked) {
            $stmt = $pdo->prepare("DELETE FROM likes WHERE article_id = ? AND user_id = ?");
            $stmt->execute([$article_id, $_SESSION['user_id']]);
            log_msg("Article '" . $article['title'] . "' unliké par " . $_SESSION['username']);
        } else {
            $stmt = $pdo->prepare("INSERT INTO likes (article_id, user_id) VALUES (?, ?)");
            $stmt->execute([$article_id, $_SESSION['user_id']]);
            log_msg("Article '" . $article['title'] . "' liké par " . $_SESSION['username']);
        }
        header("Location: article.php?id=" . $article_id);
        exit;
    } catch (PDOException $e) {
        $error = "Erreur lors de l'action.";
    }
}

// Ajouter commentaire
if (isset($_POST['add_comment']) && $is_logged_in) {
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
    
    if (empty($comment)) {
        $error = "Le commentaire ne peut pas être vide.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO comments (article_id, user_id, comment, created_at) VALUES (?, ?, ?, NOW())");
            $stmt->execute([$article_id, $_SESSION['user_id'], $comment]);
            log_msg("Commentaire ajouté sur '" . $article['title'] . "' par " . $_SESSION['username']);
            header("Location: article.php?id=" . $article_id);
            exit;
        } catch (PDOException $e) {
            $error = "Erreur lors de l'ajout du commentaire.";
        }
    }
}

// Supprimer commentaire
if (isset($_GET['delete_comment']) && $is_logged_in) {
    $comment_id = intval($_GET['delete_comment']);
    try {
        $stmt = $pdo->prepare("SELECT user_id FROM comments WHERE id = ?");
        $stmt->execute([$comment_id]);
        $comment = $stmt->fetch();
        
        if ($comment && ($comment['user_id'] == $_SESSION['user_id'] || $_SESSION['role'] === 'admin')) {
            $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
            $stmt->execute([$comment_id]);
            log_msg("Commentaire supprimé sur '" . $article['title'] . "' par " . $_SESSION['username']);
        }
        header("Location: article.php?id=" . $article_id);
        exit;
    } catch (PDOException $e) {
        $error = "Erreur lors de la suppression.";
    }
}

// Récupérer les commentaires
$comments = [];
try {
    $stmt = $pdo->prepare("SELECT c.*, u.username FROM comments c LEFT JOIN user u ON c.user_id = u.id WHERE c.article_id = ? ORDER BY c.created_at DESC");
    $stmt->execute([$article_id]);
    $comments = $stmt->fetchAll();
} catch (PDOException $e) {
    log_msg("Erreur récupération commentaires : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['title']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f0f4f0;
            min-height: 100vh;
            padding-bottom: 50px;
        }

        .navbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 1.2rem 2rem;
        }

        .nav-container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .back-link {
            color: #22c55e;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: gap 0.3s;
        }

        .back-link:hover {
            gap: 0.7rem;
        }

        .container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .alert {
            background: white;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }

        .alert-error {
            background: #fee;
            color: #c33;
            border: 2px solid #fcc;
        }

        .article-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }

        .article-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            background: linear-gradient(135deg, #22c55e, #16a34a);
        }

        .article-content {
            padding: 2.5rem;
        }

        .article-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 1rem;
            line-height: 1.2;
        }

        .article-meta {
            display: flex;
            gap: 1.5rem;
            color: #a0aec0;
            font-size: 0.9rem;
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid #f0f4f0;
        }

        .article-meta a {
            color: #22c55e;
            text-decoration: none;
            font-weight: 600;
        }

        .article-text {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #4a5568;
            margin-bottom: 2rem;
        }

        .article-text h1 {
            font-size: 2rem;
            font-weight: 700;
            margin: 1.5rem 0 1rem 0;
            color: #2d3748;
        }

        .article-text h2 {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 1.25rem 0 0.875rem 0;
            color: #2d3748;
        }

        .article-text h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 1rem 0 0.75rem 0;
            color: #2d3748;
        }

        .article-text ul, .article-text ol {
            margin: 1rem 0 1rem 2rem;
        }

        .article-text li {
            margin: 0.5rem 0;
        }

        .article-text a {
            color: #22c55e;
            text-decoration: underline;
            font-weight: 600;
        }

        .article-text a:hover {
            color: #16a34a;
        }

        .article-text strong {
            font-weight: 700;
            color: #2d3748;
        }

        .article-text em {
            font-style: italic;
        }

        .article-text u {
            text-decoration: underline;
        }

        .like-section {
            padding: 1.5rem;
            background: #f0f4f0;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .like-count {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2d3748;
        }

        .like-btn {
            padding: 0.7rem 1.5rem;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            background: white;
            border: 2px solid #e2e8f0;
            color: #4a5568;
        }

        .like-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .like-btn.liked {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
            border-color: transparent;
        }

        .comments-section {
            background: white;
            border-radius: 15px;
            padding: 2.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .comments-header {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 2rem;
        }

        .comment-form {
            background: #f0f4f0;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .comment-form textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-family: inherit;
            font-size: 1rem;
            resize: vertical;
            min-height: 100px;
        }

        .comment-form textarea:focus {
            outline: none;
            border-color: #22c55e;
        }

        .btn-primary {
            margin-top: 1rem;
            padding: 0.7rem 2rem;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(34, 197, 94, 0.4);
        }

        .comment {
            background: #f0f4f0;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .comment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .comment-author {
            font-weight: 600;
            color: #22c55e;
            text-decoration: none;
        }

        .comment-date {
            font-size: 0.85rem;
            color: #a0aec0;
        }

        .comment-text {
            color: #4a5568;
            line-height: 1.6;
        }

        .comment-delete {
            color: #ef4444;
            text-decoration: none;
            font-size: 0.9rem;
            margin-top: 0.5rem;
            display: inline-block;
        }

        .login-prompt {
            text-align: center;
            padding: 2rem;
            background: #f0f4f0;
            border-radius: 10px;
            color: #718096;
        }

        .login-prompt a {
            color: #22c55e;
            font-weight: 600;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="/index.php" class="back-link">← Retour aux articles</a>
        </div>
    </nav>

    <div class="container">
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <article class="article-container">
            <?php if ($article['image']): ?>
                <img src="/<?php echo htmlspecialchars($article['image']); ?>" class="article-image" alt="<?php echo htmlspecialchars($article['title']); ?>">
            <?php else: ?>
                <div class="article-image"></div>
            <?php endif; ?>

            <div class="article-content">
                <h1 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h1>
                
                <div class="article-meta">
                    <span>Par <a href="/profile.php?id=<?php echo $article['author_id']; ?>"><?php echo htmlspecialchars($article['username'] ?? 'Inconnu'); ?></a></span>
                    <span><?php echo date('d/m/Y à H:i', strtotime($article['created_at'])); ?></span>
                </div>

                <div class="article-text">
                    <?php echo $article['content']; ?>
                </div>

                <div class="like-section">
                    <span class="like-count"><?php echo $likes_count; ?> J'aime</span>
                    
                    <?php if ($is_logged_in): ?>
                        <form method="post" style="display: inline;">
                            <button type="submit" name="toggle_like" class="like-btn <?php echo $user_liked ? 'liked' : ''; ?>">
                                <?php echo $user_liked ? 'Ne plus aimer' : 'J\'aime'; ?>
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </article>

        <div class="comments-section">
            <h2 class="comments-header">Commentaires (<?php echo count($comments); ?>)</h2>

            <?php if ($is_logged_in): ?>
                <form method="post" class="comment-form">
                    <textarea name="comment" required placeholder="Partagez votre avis..."></textarea>
                    <button type="submit" name="add_comment" class="btn-primary">Publier le commentaire</button>
                </form>
            <?php else: ?>
                <div class="login-prompt">
                    <a href="/login.php">Connectez-vous</a> pour aimer cet article et laisser un commentaire
                </div>
            <?php endif; ?>

            <?php if (empty($comments)): ?>
                <p style="text-align: center; color: #a0aec0; padding: 2rem;">Aucun commentaire pour le moment. Soyez le premier !</p>
            <?php else: ?>
                <?php foreach ($comments as $comment): ?>
                    <div class="comment">
                        <div class="comment-header">
                            <div>
                                <a href="/profile.php?id=<?php echo $comment['user_id']; ?>" class="comment-author">
                                    <?php echo htmlspecialchars($comment['username']); ?>
                                </a>
                            </div>
                            <span class="comment-date"><?php echo date('d/m/Y à H:i', strtotime($comment['created_at'])); ?></span>
                        </div>
                        <p class="comment-text"><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></p>
                        
                        <?php if ($is_logged_in && ($comment['user_id'] == $_SESSION['user_id'] || $_SESSION['role'] === 'admin')): ?>
                            <a href="?id=<?php echo $article_id; ?>&delete_comment=<?php echo $comment['id']; ?>" 
                               class="comment-delete"
                               onclick="return confirm('Supprimer ce commentaire ?');">
                                Supprimer
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>