<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/functions.php";

checkAdmin();

$success = '';
$error = '';

// Ajouter slides (upload multiple)
if (isset($_POST['add_slides'])) {
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    
    if (empty($title)) {
        $error = "Le titre est requis.";
    } elseif (!isset($_FILES['images']) || empty($_FILES['images']['name'][0])) {
        $error = "Au moins une image est requise.";
    } else {
        try {
            // Obtenir la position de départ
            $stmt = $pdo->query("SELECT MAX(position) as max_pos FROM carousel");
            $max_pos = $stmt->fetch()['max_pos'] ?? 0;
            $position = $max_pos + 1;
            
            $uploaded_count = 0;
            
            // Traiter chaque fichier
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                    // Créer un tableau pour uploadImage
                    $file = [
                        'name' => $_FILES['images']['name'][$key],
                        'type' => $_FILES['images']['type'][$key],
                        'tmp_name' => $_FILES['images']['tmp_name'][$key],
                        'error' => $_FILES['images']['error'][$key],
                        'size' => $_FILES['images']['size'][$key]
                    ];
                    
                    $image = uploadImage($file);
                    
                    if ($image) {
                        // Créer un titre unique si plusieurs images
                        $slide_title = $title;
                        if ($uploaded_count > 0) {
                            $slide_title = $title . " #" . ($uploaded_count + 1);
                        }
                        
                        $stmt = $pdo->prepare("INSERT INTO carousel (title, description, image, position) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$slide_title, $description, $image, $position]);
                        
                        $uploaded_count++;
                        $position++;
                    }
                }
            }
            
            if ($uploaded_count > 0) {
                $success = "$uploaded_count slide(s) ajouté(s) avec succès !";
                log_msg("$uploaded_count slides carrousel créés par " . $_SESSION['username']);
            } else {
                $error = "Aucune image n'a pu être uploadée.";
            }
        } catch (PDOException $e) {
            $error = "Erreur lors de l'ajout des slides.";
            log_msg("Erreur ajout slides : " . $e->getMessage());
        }
    }
}

// Supprimer slide
if (isset($_GET['delete'])) {
    $slide_id = intval($_GET['delete']);
    try {
        $stmt = $pdo->prepare("SELECT image, title FROM carousel WHERE id = ?");
        $stmt->execute([$slide_id]);
        $slide = $stmt->fetch();
        
        if ($slide) {
            $stmt = $pdo->prepare("DELETE FROM carousel WHERE id = ?");
            $stmt->execute([$slide_id]);
            
            if ($slide['image'] && file_exists(__DIR__ . "/../../" . $slide['image'])) {
                unlink(__DIR__ . "/../../" . $slide['image']);
            }
            
            $success = "Slide supprimé !";
            log_msg("Slide carrousel supprimé : " . $slide['title'] . " par " . $_SESSION['username']);
        }
    } catch (PDOException $e) {
        $error = "Erreur lors de la suppression.";
    }
}

// Activer/désactiver
if (isset($_GET['toggle'])) {
    $slide_id = intval($_GET['toggle']);
    try {
        $stmt = $pdo->prepare("UPDATE carousel SET active = NOT active WHERE id = ?");
        $stmt->execute([$slide_id]);
        $success = "Statut mis à jour !";
        log_msg("Statut slide carrousel changé (ID: $slide_id) par " . $_SESSION['username']);
    } catch (PDOException $e) {
        $error = "Erreur.";
    }
}

// Changer position
if (isset($_POST['update_position'])) {
    $slide_id = intval($_POST['slide_id']);
    $new_position = intval($_POST['position']);
    
    try {
        $stmt = $pdo->prepare("UPDATE carousel SET position = ? WHERE id = ?");
        $stmt->execute([$new_position, $slide_id]);
        $success = "Position mise à jour !";
        log_msg("Position slide carrousel changée (ID: $slide_id) par " . $_SESSION['username']);
    } catch (PDOException $e) {
        $error = "Erreur.";
    }
}

// Récupérer tous les slides
$slides = [];
try {
    $stmt = $pdo->query("SELECT * FROM carousel ORDER BY position ASC");
    $slides = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Erreur lors du chargement.";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Carrousel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #f0f4f0;
            padding: 2rem;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        h1 {
            font-size: 2rem;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .subtitle {
            color: #a0aec0;
            margin-bottom: 2rem;
        }

        .nav-links {
            margin-bottom: 2rem;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .nav-links a {
            color: #22c55e;
            text-decoration: none;
            font-weight: 600;
            padding: 0.5rem 1rem;
            background: white;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .nav-links a:hover {
            background: #22c55e;
            color: white;
        }

        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .card h2 {
            color: #2d3748;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f4f0;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        input, textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-family: inherit;
            font-size: 1rem;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: #22c55e;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.3s;
            font-size: 0.9rem;
        }

        .btn-primary {
            background: #22c55e;
            color: white;
        }

        .btn-primary:hover {
            background: #16a34a;
        }

        .btn-small {
            padding: 0.4rem 1rem;
            font-size: 0.85rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        th {
            background: #f0f4f0;
            font-weight: 600;
            color: #2d3748;
        }

        .slide-preview {
            max-width: 200px;
            max-height: 100px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            cursor: pointer;
        }

        .slide-preview:hover {
            transform: scale(1.05);
            transition: transform 0.3s;
        }

        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
        }

        .badge-active {
            background: #d1fae5;
            color: #065f46;
        }

        .badge-inactive {
            background: #fee2e2;
            color: #991b1b;
        }

        .actions {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .actions a {
            color: #22c55e;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .actions a:hover {
            text-decoration: underline;
        }

        .actions a.delete {
            color: #ef4444;
        }

        .position-form {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .position-form input {
            width: 70px;
            padding: 0.4rem;
        }

        .helper-text {
            font-size: 0.85rem;
            color: #a0aec0;
            margin-top: 0.25rem;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #a0aec0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestion du Carrousel</h1>
        <p class="subtitle">Gérez les slides affichés sur la page d'accueil</p>

        <div class="nav-links">
            <a href="/admin/dashboard.php">← Retour au dashboard</a>
            <a href="/index.php">Voir le site</a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="card">
            <h2>Ajouter des nouveaux slides</h2>
            <form method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Titre *</label>
                    <input type="text" name="title" required placeholder="Titre du/des slide(s)">
                    <p class="helper-text">Si plusieurs images, elles seront numérotées automatiquement</p>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" placeholder="Description affichée sur le carrousel (optionnel)"></textarea>
                </div>

                <div class="form-group">
                    <label>Images * (sélection multiple)</label>
                    <input type="file" name="images[]" accept="image/*" multiple required>
                    <p class="helper-text">Maintenez Ctrl (ou Cmd sur Mac) pour sélectionner plusieurs images. Format A4 recommandé.</p>
                </div>

                <button type="submit" name="add_slides" class="btn btn-primary">Ajouter les slides</button>
            </form>
        </div>

        <div class="card">
            <h2>Slides existants (<?php echo count($slides); ?>)</h2>
            
            <?php if (empty($slides)): ?>
                <div class="empty-state">
                    <p>Aucun slide pour le moment.</p>
                    <p style="margin-top: 0.5rem; font-size: 0.9rem;">Ajoutez vos premiers slides ci-dessus pour commencer.</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Position</th>
                            <th>Aperçu</th>
                            <th>Titre</th>
                            <th>Description</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($slides as $slide): ?>
                            <tr>
                                <td>
                                    <form method="post" class="position-form">
                                        <input type="hidden" name="slide_id" value="<?php echo $slide['id']; ?>">
                                        <input type="number" name="position" value="<?php echo $slide['position']; ?>" min="0">
                                        <button type="submit" name="update_position" class="btn btn-primary btn-small">✓</button>
                                    </form>
                                </td>
                                <td>
                                    <a href="/slide.php?id=<?php echo $slide['id']; ?>" target="_blank">
                                        <img src="/<?php echo htmlspecialchars($slide['image']); ?>" class="slide-preview" alt="<?php echo htmlspecialchars($slide['title']); ?>">
                                    </a>
                                </td>
                                <td>
                                    <strong style="color: #2d3748;"><?php echo htmlspecialchars($slide['title']); ?></strong>
                                </td>
                                <td>
                                    <span style="color: #a0aec0; font-size: 0.9rem;">
                                        <?php echo htmlspecialchars(substr($slide['description'] ?? 'Aucune description', 0, 60)) . (strlen($slide['description'] ?? '') > 60 ? '...' : ''); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge <?php echo $slide['active'] ? 'badge-active' : 'badge-inactive'; ?>">
                                        <?php echo $slide['active'] ? 'Actif' : 'Inactif'; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="actions">
                                        <a href="/slide.php?id=<?php echo $slide['id']; ?>" target="_blank">Voir</a>
                                        <a href="?toggle=<?php echo $slide['id']; ?>">
                                            <?php echo $slide['active'] ? 'Désactiver' : 'Activer'; ?>
                                        </a>
                                        <a href="?delete=<?php echo $slide['id']; ?>" 
                                           class="delete"
                                           onclick="return confirm('Supprimer ce slide ?\n\nCette action est irréversible.');">
                                            Supprimer
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <div style="margin-top: 1.5rem; padding: 1rem; background: #f0f4f0; border-radius: 8px;">
                    <p style="color: #4a5568; font-size: 0.9rem;">
                        <strong>Conseils:</strong><br>
                        • Cliquez sur un slide pour le voir en plein format<br>
                        • Les utilisateurs pourront cliquer sur le carrousel pour voir l'image complète<br>
                        • Les slides sont affichés par ordre de position croissante<br>
                        • Seuls les slides actifs apparaissent sur le site<br>
                        • Le carrousel défile automatiquement toutes les 5 secondes
                    </p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>