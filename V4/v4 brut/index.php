<?php
require_once __DIR__ . "/includes/config.php";
require_once __DIR__ . "/includes/functions.php";

$is_logged_in = isset($_SESSION['user_id']);
$is_admin = $is_logged_in && $_SESSION['role'] === 'admin';

// Récupérer les slides du carrousel actifs
$carousel_slides = [];
try {
    $stmt = $pdo->query("SELECT * FROM carousel WHERE active = 1 ORDER BY position ASC");
    $carousel_slides = $stmt->fetchAll();
} catch (PDOException $e) {
    log_msg("Erreur récupération carrousel : " . $e->getMessage());
}

// Filtrage par date
$filter_year = isset($_GET['year']) ? intval($_GET['year']) : null;
$filter_month = isset($_GET['month']) ? intval($_GET['month']) : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Récupérer tous les articles avec filtres optionnels
$articles = [];
try {
    $sql = "SELECT a.*, u.username FROM articles a LEFT JOIN user u ON a.author_id = u.id";
    $where = [];
    $params = [];
    
    if ($filter_year) {
        $where[] = "YEAR(a.created_at) = ?";
        $params[] = $filter_year;
    }
    
    if ($filter_month) {
        $where[] = "MONTH(a.created_at) = ?";
        $params[] = $filter_month;
    }
    
    if (!empty($search)) {
        $where[] = "(a.title LIKE ? OR a.content LIKE ?)";
        $params[] = '%' . $search . '%';
        $params[] = '%' . $search . '%';
    }
    
    if (!empty($where)) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    
    $sql .= " ORDER BY a.created_at DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $articles = $stmt->fetchAll();
} catch (PDOException $e) {
    log_msg("Erreur récupération articles : " . $e->getMessage());
}

// Récupérer les années/mois disponibles pour le filtre
$available_dates = [];
try {
    $stmt = $pdo->query("SELECT DISTINCT YEAR(created_at) as year, MONTH(created_at) as month FROM articles ORDER BY year DESC, month DESC");
    $available_dates = $stmt->fetchAll();
} catch (PDOException $e) {
    log_msg("Erreur récupération dates : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conférences de Solange Anastasia Chopplet - Accueil</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f0f4f0;
            min-height: 100vh;
        }

        .navbar {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 1.2rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: #2d5016;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            gap: 1rem;
            align-items: center;
        }

        .nav-links a {
            text-decoration: none;
            color: #4a5568;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .nav-links a:hover {
            background: #f0f4f0;
            color: #2d5016;
        }

        .user-avatar {
            display: inline-block;
            width: 32px;
            height: 32px;
            background: #4ade80;
            border-radius: 50%;
            text-align: center;
            line-height: 32px;
            font-size: 1.2rem;
        }

        .btn {
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.9rem;
        }

        .btn-primary {
            background: #22c55e;
            color: white;
        }

        .btn-primary:hover {
            background: #16a34a;
            transform: translateY(-2px);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid #22c55e;
            color: #22c55e;
        }

        .btn-outline:hover {
            background: #22c55e;
            color: white;
        }

        /* CARROUSEL */
        .carousel-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .carousel {
            position: relative;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .carousel-inner {
            position: relative;
            width: 100%;
            height: 400px;
        }

        .carousel-item {
            display: none;
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .carousel-item.active {
            display: block;
        }

        .carousel-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .carousel-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent);
            color: white;
            padding: 2rem;
        }

        .carousel-caption h3 {
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }

        .carousel-caption p {
            font-size: 1rem;
            opacity: 0.9;
        }

        .carousel-controls {
            position: absolute;
            top: 50%;
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 1rem;
            transform: translateY(-50%);
        }

        .carousel-btn {
            background: rgba(255, 255, 255, 0.9);
            border: none;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
            font-size: 1.2rem;
            transition: all 0.3s;
        }

        .carousel-btn:hover {
            background: white;
        }

        .carousel-indicators {
            position: absolute;
            bottom: 1rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 0.5rem;
        }

        .indicator {
            width: 10px;
            height: 10px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s;
        }

        .indicator.active {
            background: white;
            width: 30px;
            border-radius: 5px;
        }

        /* FILTRES PAR DATE */
        .filters {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        .filters-bar {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .filters-bar label {
            font-weight: 600;
            color: #2d3748;
        }

        .filters-bar select {
            padding: 0.5rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.9rem;
            cursor: pointer;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .articles-list {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .article-item {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            display: flex;
            transition: all 0.3s;
            height: 220px;
        }

        .article-item:hover {
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .article-image-container {
            width: 300px;
            height: 220px;
            flex-shrink: 0;
        }

        .article-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .article-body {
            padding: 1.5rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
        }

        .article-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.5rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .article-meta {
            font-size: 0.85rem;
            color: #a0aec0;
            margin-bottom: 1rem;
        }

        .article-meta a {
            color: #22c55e;
            text-decoration: none;
            font-weight: 500;
        }

        .article-excerpt {
            color: #4a5568;
            line-height: 1.6;
            margin-bottom: 1rem;
            flex: 1;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }

        .article-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .read-more {
            color: #22c55e;
            font-weight: 600;
            text-decoration: none;
        }

        .read-more:hover {
            text-decoration: underline;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        @media (max-width: 768px) {
            .article-item {
                flex-direction: column;
                height: auto;
            }

            .article-image-container {
                width: 100%;
                height: 200px;
            }

            .carousel-inner {
                height: 250px;
            }

            .nav-container {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="/index.php" class="logo">Conférences de Solange Anastasia Chopplet</a>
            <div class="nav-links">
                <?php if ($is_logged_in): ?>
                    <a href="/profile.php">
                        <span class="user-avatar">👤</span>
                        <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </a>
                    <?php if ($is_admin): ?>
                        <a href="/admin/dashboard.php" class="btn btn-primary">Admin</a>
                    <?php endif; ?>
                    <a href="/logout.php" class="btn btn-outline">Déconnexion</a>
                <?php else: ?>
                    <a href="/login.php" class="btn btn-outline">Connexion</a>
                    <a href="/register.php" class="btn btn-primary">Inscription</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <?php if (!empty($carousel_slides)): ?>
    <div class="carousel-container">
        <div class="carousel">
            <div class="carousel-inner">
                <?php foreach ($carousel_slides as $index => $slide): ?>
                    <div class="carousel-item <?php echo $index === 0 ? 'active' : ''; ?>" data-slide="<?php echo $index; ?>">
                        <a href="/slide.php?id=<?php echo $slide['id']; ?>" style="display: block; cursor: pointer;">
                            <img src="/<?php echo htmlspecialchars($slide['image']); ?>" alt="<?php echo htmlspecialchars($slide['title']); ?>">
                            <div class="carousel-caption">
                                <h3><?php echo htmlspecialchars($slide['title']); ?></h3>
                                <?php if ($slide['description']): ?>
                                    <p><?php echo htmlspecialchars($slide['description']); ?></p>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (count($carousel_slides) > 1): ?>
                <div class="carousel-controls">
                    <button class="carousel-btn" onclick="previousSlide()">‹</button>
                    <button class="carousel-btn" onclick="nextSlide()">›</button>
                </div>

                <div class="carousel-indicators">
                    <?php foreach ($carousel_slides as $index => $slide): ?>
                        <div class="indicator <?php echo $index === 0 ? 'active' : ''; ?>" onclick="goToSlide(<?php echo $index; ?>)"></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        let currentSlide = 0;
        const slides = document.querySelectorAll('.carousel-item');
        const indicators = document.querySelectorAll('.indicator');
        const totalSlides = slides.length;

        function showSlide(n) {
            slides.forEach(slide => slide.classList.remove('active'));
            indicators.forEach(ind => ind.classList.remove('active'));
            
            currentSlide = (n + totalSlides) % totalSlides;
            slides[currentSlide].classList.add('active');
            indicators[currentSlide].classList.add('active');
        }

        function nextSlide() {
            showSlide(currentSlide + 1);
        }

        function previousSlide() {
            showSlide(currentSlide - 1);
        }

        function goToSlide(n) {
            showSlide(n);
        }

        <?php if (count($carousel_slides) > 1): ?>
        setInterval(() => {
            nextSlide();
        }, 5000);
        <?php endif; ?>
    </script>
    <?php endif; ?>

    <div class="filters">
        <form method="get" class="filters-bar">
            <label>Rechercher :</label>
            <input type="text" name="search" placeholder="Mots-clés..." 
                   value="<?php echo htmlspecialchars($search); ?>"
                   style="padding: 0.5rem 1rem; border: 2px solid #e2e8f0; border-radius: 8px; font-size: 0.9rem; min-width: 250px;">
            
            <label>Filtrer par date :</label>
            <select name="year" onchange="this.form.submit()">
                <option value="">Toutes les années</option>
                <?php
                $years = array_unique(array_column($available_dates, 'year'));
                foreach ($years as $year):
                ?>
                    <option value="<?php echo $year; ?>" <?php echo $filter_year == $year ? 'selected' : ''; ?>>
                        <?php echo $year; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="month" onchange="this.form.submit()">
                <option value="">Tous les mois</option>
                <?php
                $months = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                for ($m = 1; $m <= 12; $m++):
                ?>
                    <option value="<?php echo $m; ?>" <?php echo $filter_month == $m ? 'selected' : ''; ?>>
                        <?php echo $months[$m]; ?>
                    </option>
                <?php endfor; ?>
            </select>

            <button type="submit" class="btn btn-primary" style="padding: 0.5rem 1.5rem; font-size: 0.9rem;">Rechercher</button>

            <?php if ($filter_year || $filter_month || !empty($search)): ?>
                <a href="/index.php" class="btn btn-outline">Réinitialiser</a>
            <?php endif; ?>
        </form>
    </div>

    <div class="container">
        <?php if (!empty($search) || $filter_year || $filter_month): ?>
            <div style="background: white; padding: 1rem 1.5rem; border-radius: 10px; margin-bottom: 1.5rem; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);">
                <span style="color: #4a5568; font-weight: 600;">
                    <?php echo count($articles); ?> article(s) trouvé(s)
                    <?php if (!empty($search)): ?>
                        pour "<?php echo htmlspecialchars($search); ?>"
                    <?php endif; ?>
                    <?php if ($filter_year || $filter_month): ?>
                        <?php if (!empty($search)): ?>et<?php endif; ?>
                        <?php if ($filter_month): ?>
                            en <?php echo ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'][$filter_month]; ?>
                        <?php endif; ?>
                        <?php if ($filter_year): ?>
                            <?php echo $filter_year; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                </span>
            </div>
        <?php endif; ?>

        <?php if (empty($articles)): ?>
            <div class="empty-state">
                <h3>Aucun article trouvé</h3>
                <?php if (!empty($search)): ?>
                    <p>Aucun résultat pour la recherche "<?php echo htmlspecialchars($search); ?>".</p>
                <?php elseif ($filter_year || $filter_month): ?>
                    <p>Aucun article ne correspond à vos critères de date.</p>
                <?php else: ?>
                    <p>Aucun article publié pour le moment.</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="articles-list">
                <?php foreach ($articles as $article): ?>
                    <article class="article-item">
                        <div class="article-image-container">
                            <?php if ($article['image']): ?>
                                <img src="/<?php echo htmlspecialchars($article['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($article['title']); ?>" 
                                     class="article-image">
                            <?php else: ?>
                                <div class="article-image" style="background: linear-gradient(135deg, #22c55e, #16a34a);"></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="article-body">
                            <div>
                                <h3 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h3>
                                <div class="article-meta">
                                    Par <a href="/profile.php?id=<?php echo $article['author_id']; ?>">
                                        <?php echo htmlspecialchars($article['username'] ?? 'Inconnu'); ?>
                                    </a> • 
                                    <?php echo date('d/m/Y à H:i', strtotime($article['created_at'])); ?>
                                </div>
                                <p class="article-excerpt">
                                    <?php echo htmlspecialchars(substr(strip_tags($article['content']), 0, 200)) . '...'; ?>
                                </p>
                            </div>
                            <div class="article-footer">
                                <a href="/article.php?id=<?php echo $article['id']; ?>" class="read-more">
                                    Lire la suite →
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>