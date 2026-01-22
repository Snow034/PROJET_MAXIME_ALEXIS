<?php
require_once __DIR__ . "/../includes/config.php";
require_once __DIR__ . "/../includes/functions.php";

checkAdmin();

$success = '';
$error = '';
$edit_mode = false;
$edit_article = null;

// Créer ou modifier article
if (isset($_POST['save_article'])) {
    $article_id = isset($_POST['article_id']) ? intval($_POST['article_id']) : 0;
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $content = isset($_POST['content']) ? $_POST['content'] : ''; // Ne pas trim pour conserver le HTML
    $image = null;
    
    if (empty($title) || empty($content)) {
        $error = "Titre et contenu requis.";
    } else {
        // Upload image si présente
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $image = uploadImage($_FILES['image']);
            if (!$image) {
                $error = "Erreur lors de l'upload de l'image.";
            }
        }
        
        if (empty($error)) {
            try {
                if ($article_id > 0) {
                    // Mode édition
                    if ($image) {
                        // Supprimer ancienne image
                        $stmt = $pdo->prepare("SELECT image FROM articles WHERE id = ?");
                        $stmt->execute([$article_id]);
                        $old = $stmt->fetch();
                        if ($old && $old['image'] && file_exists(__DIR__ . "/../../" . $old['image'])) {
                            unlink(__DIR__ . "/../../" . $old['image']);
                        }
                        
                        $stmt = $pdo->prepare("UPDATE articles SET title = ?, content = ?, image = ?, updated_at = NOW() WHERE id = ?");
                        $stmt->execute([$title, $content, $image, $article_id]);
                    } else {
                        $stmt = $pdo->prepare("UPDATE articles SET title = ?, content = ?, updated_at = NOW() WHERE id = ?");
                        $stmt->execute([$title, $content, $article_id]);
                    }
                    $success = "Article modifié avec succès !";
                    log_msg("Article modifié : $title par " . $_SESSION['username']);
                } else {
                    // Mode création
                    $stmt = $pdo->prepare("INSERT INTO articles (title, content, image, author_id) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$title, $content, $image, $_SESSION['user_id']]);
                    $success = "Article créé avec succès !";
                    log_msg("Article créé : $title par " . $_SESSION['username']);
                }
            } catch (PDOException $e) {
                $error = "Erreur lors de l'enregistrement.";
                log_msg("Erreur sauvegarde article : " . $e->getMessage());
            }
        }
    }
}

// Mode édition
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    try {
        $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
        $stmt->execute([$edit_id]);
        $edit_article = $stmt->fetch();
        if ($edit_article) {
            $edit_mode = true;
        }
    } catch (PDOException $e) {
        $error = "Article non trouvé.";
    }
}

// Supprimer article
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    try {
        $stmt = $pdo->prepare("SELECT title, image FROM articles WHERE id = ?");
        $stmt->execute([$delete_id]);
        $article = $stmt->fetch();
        
        if ($article) {
            $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
            $stmt->execute([$delete_id]);
            
            if ($article['image'] && file_exists(__DIR__ . "/../../" . $article['image'])) {
                unlink(__DIR__ . "/../../" . $article['image']);
            }
            
            $success = "Article supprimé !";
            log_msg("Article supprimé : " . $article['title'] . " par " . $_SESSION['username']);
        }
    } catch (PDOException $e) {
        $error = "Erreur lors de la suppression.";
    }
}

// Changer rôle utilisateur
if (isset($_POST['change_role'])) {
    $user_id = intval($_POST['user_id']);
    $new_role = $_POST['role'];
    
    if ($user_id != $_SESSION['user_id']) {
        try {
            $stmt = $pdo->prepare("UPDATE user SET role = ? WHERE id = ?");
            $stmt->execute([$new_role, $user_id]);
            $success = "Rôle mis à jour !";
            log_msg("Rôle changé pour user ID $user_id vers $new_role par " . $_SESSION['username']);
        } catch (PDOException $e) {
            $error = "Erreur.";
        }
    } else {
        $error = "Vous ne pouvez pas changer votre propre rôle.";
    }
}

// Récupérer tous les articles
$articles = [];
try {
    $stmt = $pdo->query("SELECT a.*, u.username FROM articles a LEFT JOIN user u ON a.author_id = u.id ORDER BY a.created_at DESC");
    $articles = $stmt->fetchAll();
} catch (PDOException $e) {
    log_msg("Erreur récupération articles : " . $e->getMessage());
}

// Récupérer tous les utilisateurs
$users = [];
try {
    $stmt = $pdo->query("SELECT * FROM user ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    log_msg("Erreur récupération users : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
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

        input[type="text"],
        input[type="file"] {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-family: inherit;
            font-size: 1rem;
        }

        input:focus {
            outline: none;
            border-color: #22c55e;
        }

        /* Éditeur de texte enrichi */
        .editor-toolbar {
            background: #f7fafc;
            border: 2px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
            border-radius: 8px 8px 0 0;
            padding: 0.5rem;
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .editor-btn {
            padding: 0.5rem 0.75rem;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
            color: #4a5568;
            transition: all 0.2s;
        }

        .editor-btn:hover {
            background: #22c55e;
            color: white;
            border-color: #22c55e;
        }

        .editor-btn.active {
            background: #22c55e;
            color: white;
            border-color: #16a34a;
            box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.2);
        }

        #content {
            width: 100%;
            min-height: 400px;
            padding: 1rem;
            border: 2px solid #e2e8f0;
            border-top: none;
            border-radius: 0 0 8px 8px;
            font-family: inherit;
            font-size: 1rem;
            line-height: 1.6;
        }

        #content:focus {
            outline: none;
            border-color: #22c55e;
        }

        /* Styles pour le contenu formaté */
        #content h1 {
            font-size: 2rem;
            font-weight: 700;
            margin: 1rem 0;
            color: #2d3748;
        }

        #content h2 {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0.875rem 0;
            color: #2d3748;
        }

        #content h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0.75rem 0;
            color: #2d3748;
        }

        #content ul, #content ol {
            margin-left: 2rem;
            margin: 1rem 0;
        }

        #content li {
            margin: 0.5rem 0;
        }

        #content a {
            color: #22c55e;
            text-decoration: underline;
        }

        #content strong {
            font-weight: 700;
        }

        #content em {
            font-style: italic;
        }

        #content u {
            text-decoration: underline;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #22c55e;
            color: white;
        }

        .btn-primary:hover {
            background: #16a34a;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #4a5568;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
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

        .actions {
            display: flex;
            gap: 0.5rem;
        }

        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .badge-admin {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-user {
            background: #d1fae5;
            color: #065f46;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Dashboard Admin</h1>
        <p class="subtitle">Bienvenue <?php echo htmlspecialchars($_SESSION['username']); ?></p>

        <div class="nav-links">
            <a href="/index.php">Voir le site</a>
            <a href="/admin/carousel.php">Gérer le carrousel</a>
            <a href="/admin/logs.php">Voir les logs</a>
            <a href="/logout.php">Déconnexion</a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="card">
            <h2><?php echo $edit_mode ? 'Modifier l\'article' : 'Créer un article'; ?></h2>
            <form method="post" enctype="multipart/form-data">
                <?php if ($edit_mode): ?>
                    <input type="hidden" name="article_id" value="<?php echo $edit_article['id']; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label>Titre *</label>
                    <input type="text" name="title" required 
                           value="<?php echo $edit_mode ? htmlspecialchars($edit_article['title']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label>Contenu * (formatage disponible)</label>
                    <div class="editor-toolbar">
                        <button type="button" class="editor-btn" onclick="formatText('bold')"><strong>Gras</strong></button>
                        <button type="button" class="editor-btn" onclick="formatText('italic')"><em>Italique</em></button>
                        <button type="button" class="editor-btn" onclick="formatText('underline')"><u>Souligné</u></button>
                        <button type="button" class="editor-btn" onclick="formatText('h1')">Titre 1</button>
                        <button type="button" class="editor-btn" onclick="formatText('h2')">Titre 2</button>
                        <button type="button" class="editor-btn" onclick="formatText('h3')">Titre 3</button>
                        <button type="button" class="editor-btn" onclick="insertList('ul')">• Liste</button>
                        <button type="button" class="editor-btn" onclick="insertList('ol')">1. Liste</button>
                        <button type="button" class="editor-btn" onclick="insertLink()">🔗 Lien</button>
                    </div>
                    <div id="content" contenteditable="true"><?php echo $edit_mode ? $edit_article['content'] : ''; ?></div>
                    <input type="hidden" name="content" id="content-hidden">
                </div>

                <div class="form-group">
                    <label>Image <?php echo $edit_mode ? '(laisser vide pour conserver l\'actuelle)' : ''; ?></label>
                    <input type="file" name="image" accept="image/*">
                    <?php if ($edit_mode && $edit_article['image']): ?>
                        <p style="margin-top: 0.5rem; color: #a0aec0; font-size: 0.9rem;">Image actuelle: <?php echo htmlspecialchars($edit_article['image']); ?></p>
                    <?php endif; ?>
                </div>

                <button type="submit" name="save_article" class="btn btn-primary" onclick="saveContent()">
                    <?php echo $edit_mode ? 'Mettre à jour' : 'Publier'; ?>
                </button>
                
                <?php if ($edit_mode): ?>
                    <a href="/admin/dashboard.php" class="btn btn-secondary">Annuler</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="card">
            <h2>Tous les articles (<?php echo count($articles); ?>)</h2>
            
            <?php if (empty($articles)): ?>
                <p style="text-align: center; color: #a0aec0; padding: 2rem;">Aucun article.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Titre</th>
                            <th>Auteur</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($articles as $article): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($article['title']); ?></td>
                                <td><?php echo htmlspecialchars($article['username']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($article['created_at'])); ?></td>
                                <td>
                                    <div class="actions">
                                        <a href="?edit=<?php echo $article['id']; ?>" class="btn btn-secondary" style="padding: 0.4rem 1rem; font-size: 0.9rem;">Modifier</a>
                                        <a href="?delete=<?php echo $article['id']; ?>" 
                                           class="btn btn-danger" 
                                           style="padding: 0.4rem 1rem; font-size: 0.9rem;"
                                           onclick="return confirm('Supprimer cet article ?');">
                                            Supprimer
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2>Gestion des utilisateurs (<?php echo count($users); ?>)</h2>
            
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Inscrit le</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="badge <?php echo $user['role'] === 'admin' ? 'badge-admin' : 'badge-user'; ?>">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </td>
                            <td><?php echo date('d/m/Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <form method="post" style="display: inline;">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <select name="role" style="padding: 0.4rem; border-radius: 4px; border: 1px solid #e2e8f0;">
                                            <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                                            <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                        </select>
                                        <button type="submit" name="change_role" class="btn btn-primary" style="padding: 0.4rem 1rem; font-size: 0.9rem;">Modifier</button>
                                    </form>
                                <?php else: ?>
                                    <span style="color: #a0aec0;">Vous</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Sauvegarder le contenu HTML avant soumission
        function saveContent() {
            const content = document.getElementById('content').innerHTML;
            document.getElementById('content-hidden').value = content;
        }

        // Formater le texte avec toggle
        function formatText(command) {
            if (command === 'h1' || command === 'h2' || command === 'h3') {
                // Pour les titres, utiliser formatBlock
                document.execCommand('formatBlock', false, command);
            } else {
                // Pour gras, italique, souligné - toggle automatique
                document.execCommand(command, false, null);
            }
            document.getElementById('content').focus();
            updateButtonStates();
        }

        // Insérer une liste
        function insertList(type) {
            document.execCommand(type === 'ul' ? 'insertUnorderedList' : 'insertOrderedList', false, null);
            document.getElementById('content').focus();
            updateButtonStates();
        }

        // Insérer un lien
        function insertLink() {
            const url = prompt('Entrez l\'URL du lien:');
            if (url) {
                document.execCommand('createLink', false, url);
            }
            document.getElementById('content').focus();
            updateButtonStates();
        }

        // Mettre à jour l'état visuel des boutons
        function updateButtonStates() {
            // Récupérer tous les boutons
            const buttons = document.querySelectorAll('.editor-btn');
            
            // Retirer la classe active de tous les boutons
            buttons.forEach(btn => btn.classList.remove('active'));
            
            // Vérifier l'état de chaque commande
            if (document.queryCommandState('bold')) {
                document.querySelector('[onclick*="bold"]').classList.add('active');
            }
            
            if (document.queryCommandState('italic')) {
                document.querySelector('[onclick*="italic"]').classList.add('active');
            }
            
            if (document.queryCommandState('underline')) {
                document.querySelector('[onclick*="underline"]').classList.add('active');
            }
            
            if (document.queryCommandState('insertUnorderedList')) {
                document.querySelector('[onclick*="insertList(\'ul\')"]').classList.add('active');
            }
            
            if (document.queryCommandState('insertOrderedList')) {
                document.querySelector('[onclick*="insertList(\'ol\')"]').classList.add('active');
            }
            
            // Vérifier les titres
            const selection = window.getSelection();
            if (selection.rangeCount > 0) {
                const parent = selection.getRangeAt(0).commonAncestorContainer.parentElement;
                if (parent) {
                    if (parent.tagName === 'H1' || parent.closest('h1')) {
                        const h1Btn = Array.from(buttons).find(btn => btn.textContent.includes('Titre 1'));
                        if (h1Btn) h1Btn.classList.add('active');
                    }
                    if (parent.tagName === 'H2' || parent.closest('h2')) {
                        const h2Btn = Array.from(buttons).find(btn => btn.textContent.includes('Titre 2'));
                        if (h2Btn) h2Btn.classList.add('active');
                    }
                    if (parent.tagName === 'H3' || parent.closest('h3')) {
                        const h3Btn = Array.from(buttons).find(btn => btn.textContent.includes('Titre 3'));
                        if (h3Btn) h3Btn.classList.add('active');
                    }
                }
            }
        }

        // Écouter les changements de sélection et les clics
        document.getElementById('content').addEventListener('mouseup', updateButtonStates);
        document.getElementById('content').addEventListener('keyup', updateButtonStates);
        document.getElementById('content').addEventListener('focus', updateButtonStates);

        // Auto-save au submit
        document.querySelector('form').addEventListener('submit', saveContent);
    </script>
</body>
</html>