<?php
require_once __DIR__ . "/includes/config.php";
require_once __DIR__ . "/includes/functions.php";

$is_logged_in = isset($_SESSION['user_id']);
$profile_id = isset($_GET['id']) ? intval($_GET['id']) : ($is_logged_in ? $_SESSION['user_id'] : 0);

if ($profile_id <= 0) {
    header("Location: /index.php");
    exit;
}

$error = '';
$success = '';
$is_own_profile = $is_logged_in && $profile_id == $_SESSION['user_id'];

// Récupérer les informations
try {
    $stmt = $pdo->prepare("SELECT id, username, email, bio, created_at FROM user WHERE id = ?");
    $stmt->execute([$profile_id]);
    $user = $stmt->fetch();
    
    if (!$user) {
        header("Location: /index.php");
        exit;
    }
} catch (PDOException $e) {
    log_msg("Erreur récupération profil : " . $e->getMessage());
    header("Location: /index.php");
    exit;
}

// Mise à jour du profil
if (isset($_POST['update_profile']) && $is_own_profile) {
    $new_bio = isset($_POST['bio']) ? trim($_POST['bio']) : '';
    
    try {
        $stmt = $pdo->prepare("UPDATE user SET bio = ? WHERE id = ?");
        $stmt->execute([$new_bio, $profile_id]);
        $success = "Profil mis à jour avec succès !";
        $user['bio'] = $new_bio;
        log_msg("Profil mis à jour par " . $_SESSION['username']);
    } catch (PDOException $e) {
        $error = "Erreur lors de la mise à jour.";
    }
}

// Statistiques
$stats = ['articles_count' => 0, 'comments_count' => 0, 'likes_count' => 0];
try {
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM articles WHERE author_id = ?");
    $stmt->execute([$profile_id]);
    $stats['articles_count'] = $stmt->fetch()['count'];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM comments WHERE user_id = ?");
    $stmt->execute([$profile_id]);
    $stats['comments_count'] = $stmt->fetch()['count'];
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM likes WHERE user_id = ?");
    $stmt->execute([$profile_id]);
    $stats['likes_count'] = $stmt->fetch()['count'];
} catch (PDOException $e) {
    log_msg("Erreur stats : " . $e->getMessage());
}

// Articles aimés
$liked_articles = [];
try {
    $stmt = $pdo->prepare("SELECT a.*, u.username, l.created_at as liked_at FROM likes l JOIN articles a ON l.article_id = a.id LEFT JOIN user u ON a.author_id = u.id WHERE l.user_id = ? ORDER BY l.created_at DESC LIMIT 10");
    $stmt->execute([$profile_id]);
    $liked_articles = $stmt->fetchAll();
} catch (PDOException $e) {
    log_msg("Erreur articles aimés : " . $e->getMessage());
}

// Commentaires récents
$recent_comments = [];
try {
    $stmt = $pdo->prepare("SELECT c.*, a.title as article_title, a.id as article_id FROM comments c JOIN articles a ON c.article_id = a.id WHERE c.user_id = ? ORDER BY c.created_at DESC LIMIT 10");
    $stmt->execute([$profile_id]);
    $recent_comments = $stmt->fetchAll();
} catch (PDOException $e) {
    log_msg("Erreur commentaires : " . $e->getMessage());
}

// Articles écrits
$written_articles = [];
if ($stats['articles_count'] > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM articles WHERE author_id = ? ORDER BY created_at DESC");
        $stmt->execute([$profile_id]);
        $written_articles = $stmt->fetchAll();
    } catch (PDOException $e) {
        log_msg("Erreur articles écrits : " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - <?php echo htmlspecialchars($user['username']); ?></title>
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
            max-width: 1200px;
            margin: 0 auto;
        }

        .back-link {
            color: #22c55e;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .alert {
            background: white;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 2px solid #6ee7b7;
        }

        .profile-header {
            background: white;
            border-radius: 15px;
            padding: 2.5rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }

        .profile-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 1rem;
        }

        .profile-meta {
            color: #a0aec0;
            font-size: 0.9rem;
            margin-bottom: 2rem;
        }

        .bio-section {
            background: #f0f4f0;
            padding: 1.5rem;
            border-radius: 10px;
        }

        .bio-section h3 {
            font-size: 1.2rem;
            color: #2d3748;
            margin-bottom: 1rem;
        }

        textarea {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-family: inherit;
            font-size: 1rem;
            resize: vertical;
            min-height: 100px;
        }

        textarea:focus {
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

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.12);
        }

        .stat-value {
            font-size: 3rem;
            font-weight: 700;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .stat-label {
            color: #a0aec0;
            font-size: 0.9rem;
            margin-top: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .section {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f4f0;
        }

        .article-mini {
            padding: 1rem;
            background: #f0f4f0;
            border-radius: 10px;
            margin-bottom: 1rem;
            transition: all 0.3s;
        }

        .article-mini:hover {
            background: #e5f3e9;
        }

        .article-mini h4 {
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .article-mini h4 a {
            color: #22c55e;
            text-decoration: none;
        }

        .article-mini h4 a:hover {
            text-decoration: underline;
        }

        .article-mini .meta {
            font-size: 0.85rem;
            color: #a0aec0;
        }

        .comment-mini {
            padding: 1rem;
            background: #f0f4f0;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .comment-mini .article-link {
            font-weight: 600;
            color: #22c55e;
            text-decoration: none;
            font-size: 0.95rem;
        }

        .comment-mini .article-link:hover {
            text-decoration: underline;
        }

        .comment-mini p {
            margin: 0.5rem 0;
            color: #4a5568;
            font-size: 0.9rem;
        }

        .comment-mini .date {
            font-size: 0.8rem;
            color: #a0aec0;
        }

        .empty {
            text-align: center;
            padding: 2rem;
            color: #a0aec0;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="/index.php" class="back-link">← Retour à l'accueil</a>
        </div>
    </nav>

    <div class="container">
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="profile-header">
            <h1 class="profile-title"><?php echo htmlspecialchars($user['username']); ?></h1>
            <?php if ($is_own_profile): ?>
                <p class="profile-meta">Email: <?php echo htmlspecialchars($user['email']); ?></p>
            <?php endif; ?>
            <p class="profile-meta">Membre depuis le <?php echo date('d/m/Y', strtotime($user['created_at'])); ?></p>
            
            <div class="bio-section">
                <h3>Biographie</h3>
                <?php if ($is_own_profile): ?>
                    <form method="post">
                        <textarea name="bio"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                        <button type="submit" name="update_profile" class="btn-primary">Mettre à jour ma bio</button>
                    </form>
                <?php else: ?>
                    <p><?php echo $user['bio'] ? nl2br(htmlspecialchars($user['bio'])) : 'Aucune biographie'; ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?php echo $stats['articles_count']; ?></div>
                <div class="stat-label">Articles écrits</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $stats['comments_count']; ?></div>
                <div class="stat-label">Commentaires</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo $stats['likes_count']; ?></div>
                <div class="stat-label">Articles aimés</div>
            </div>
        </div>

        <?php if (!empty($written_articles)): ?>
        <div class="section">
            <h2 class="section-title">Articles écrits (<?php echo count($written_articles); ?>)</h2>
            <?php foreach ($written_articles as $article): ?>
                <div class="article-mini">
                    <h4><a href="/article.php?id=<?php echo $article['id']; ?>"><?php echo htmlspecialchars($article['title']); ?></a></h4>
                    <div class="meta"><?php echo date('d/m/Y à H:i', strtotime($article['created_at'])); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($liked_articles)): ?>
        <div class="section">
            <h2 class="section-title">Articles aimés (<?php echo count($liked_articles); ?>)</h2>
            <?php foreach ($liked_articles as $article): ?>
                <div class="article-mini">
                    <h4><a href="/article.php?id=<?php echo $article['id']; ?>"><?php echo htmlspecialchars($article['title']); ?></a></h4>
                    <div class="meta">
                        Par <?php echo htmlspecialchars($article['username']); ?> • 
                        Aimé le <?php echo date('d/m/Y', strtotime($article['liked_at'])); ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($recent_comments)): ?>
        <div class="section">
            <h2 class="section-title">Commentaires récents (<?php echo count($recent_comments); ?>)</h2>
            <?php foreach ($recent_comments as $comment): ?>
                <div class="comment-mini">
                    <a href="/article.php?id=<?php echo $comment['article_id']; ?>" class="article-link">
                        Sur: <?php echo htmlspecialchars($comment['article_title']); ?>
                    </a>
                    <p><?php echo htmlspecialchars(substr($comment['comment'], 0, 100)) . (strlen($comment['comment']) > 100 ? '...' : ''); ?></p>
                    <div class="date"><?php echo date('d/m/Y à H:i', strtotime($comment['created_at'])); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (empty($written_articles) && empty($liked_articles) && empty($recent_comments)): ?>
        <div class="section">
            <p class="empty">Aucune activité pour le moment.</p>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>