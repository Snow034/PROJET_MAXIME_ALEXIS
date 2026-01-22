<?php
require_once __DIR__ . "/includes/config.php";
require_once __DIR__ . "/includes/functions.php";

$slide_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($slide_id <= 0) {
    header("Location: /index.php");
    exit;
}

// Récupérer le slide
try {
    $stmt = $pdo->prepare("SELECT * FROM carousel WHERE id = ?");
    $stmt->execute([$slide_id]);
    $slide = $stmt->fetch();
    
    if (!$slide) {
        header("Location: /index.php");
        exit;
    }
} catch (PDOException $e) {
    log_msg("Erreur récupération slide : " . $e->getMessage());
    header("Location: /index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($slide['title']); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #2d3748;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .back-button {
            position: fixed;
            top: 2rem;
            left: 2rem;
            background: rgba(255, 255, 255, 0.9);
            color: #2d3748;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            z-index: 100;
        }

        .back-button:hover {
            background: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .slide-container {
            max-width: 1200px;
            width: 100%;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            overflow: hidden;
        }

        .slide-image {
            width: 100%;
            height: auto;
            display: block;
        }

        .slide-content {
            padding: 2.5rem;
        }

        .slide-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 1rem;
        }

        .slide-description {
            font-size: 1.2rem;
            line-height: 1.8;
            color: #4a5568;
        }

        .slide-meta {
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 2px solid #f0f4f0;
            color: #a0aec0;
            font-size: 0.9rem;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .back-button {
                display: none;
            }

            .slide-container {
                box-shadow: none;
                border-radius: 0;
                max-width: 100%;
            }
        }

        @media (max-width: 768px) {
            .back-button {
                top: 1rem;
                left: 1rem;
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }

            body {
                padding: 1rem;
            }

            .slide-content {
                padding: 1.5rem;
            }

            .slide-title {
                font-size: 1.8rem;
            }

            .slide-description {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <a href="/index.php" class="back-button">← Retour</a>

    <div class="slide-container">
        <img src="/<?php echo htmlspecialchars($slide['image']); ?>" 
             alt="<?php echo htmlspecialchars($slide['title']); ?>" 
             class="slide-image">
        
        <?php if ($slide['title'] || $slide['description']): ?>
        <div class="slide-content">
            <?php if ($slide['title']): ?>
                <h1 class="slide-title"><?php echo htmlspecialchars($slide['title']); ?></h1>
            <?php endif; ?>
            
            <?php if ($slide['description']): ?>
                <div class="slide-description">
                    <?php echo nl2br(htmlspecialchars($slide['description'])); ?>
                </div>
            <?php endif; ?>

            <div class="slide-meta">
                Publié le <?php echo date('d/m/Y', strtotime($slide['created_at'])); ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>