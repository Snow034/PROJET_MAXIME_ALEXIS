<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin' ?> - Conférences S. A. Chopplet</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Dancing+Script:wght@600&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">

    <style>
        :root {
            /* Palette Premium Literary */
            --primary-color: #1a4a3b;
            /* Vert Bibliothèque Profond */
            --primary-light: #2d5a4e;
            /* Vert plus doux */
            --accent-color: #c5a065;
            /* Or Vieilli / Bronze */
            --bg-color: #fcfbf8;
            /* Papier Vélin */
            --text-main: #242424;
            /* Encre Noire */
            --text-light: #595959;
            /* Gris Anthracite */
            --sidebar-width: 280px;
        }

        /* Texture Grain */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)' opacity='0.03'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 9999;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-main);
            overflow-x: hidden;
            display: flex;
            min-height: 100vh;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .navbar-brand,
        .serif-font {
            font-family: 'Playfair Display', serif;
            color: var(--primary-color);
        }

        .sidebar {
            width: var(--sidebar-width);
            background: var(--primary-color);
            color: white;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            border-right: 1px solid var(--accent-color);
            transition: all 0.3s;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid rgba(197, 160, 101, 0.3);
            text-align: center;
        }

        .sidebar-brand {
            font-family: 'Playfair Display', serif;
            font-size: 1.5rem;
            color: #fff;
            text-decoration: none;
            font-weight: 700;
        }

        .sidebar a {
            color: rgba(255, 255, 255, 0.85);
            text-decoration: none;
            padding: 1rem 1.5rem;
            display: block;
            border-left: 4px solid transparent;
            transition: all 0.3s;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: rgba(255, 255, 255, 0.05);
            color: var(--accent-color);
            border-left-color: var(--accent-color);
        }

        .sidebar a i {
            width: 25px;
            text-align: center;
            margin-right: 10px;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            width: calc(100% - var(--sidebar-width));
            padding: 3rem;
            transition: all 0.3s;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                width: 100%;
                padding: 1.5rem;
            }
        }

        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            background: #fff;
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.5rem;
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--primary-color) !important;
        }

        .text-primary {
            color: var(--primary-color) !important;
        }

        .text-success {
            color: var(--primary-light) !important;
        }

        .text-info {
            color: var(--accent-color) !important;
        }

        .text-warning {
            color: var(--accent-color) !important;
        }

        .text-gray-800 {
            color: var(--text-main) !important;
            font-family: 'Playfair Display', serif;
        }

        .text-gray-300 {
            color: #e3e6f0 !important;
            opacity: 0.3;
        }

        /* Background Colors */
        .bg-primary {
            background-color: var(--primary-color) !important;
        }

        .bg-success {
            background-color: var(--primary-light) !important;
        }

        /* Borders */
        .border-primary {
            border-color: var(--primary-color) !important;
        }

        .border-success {
            border-color: var(--primary-light) !important;
        }

        .border-info {
            border-color: var(--accent-color) !important;
        }

        .border-warning {
            border-color: var(--accent-color) !important;
        }

        .border-secondary {
            border-color: var(--primary-light) !important;
        }

        .text-secondary {
            color: var(--primary-light) !important;
        }

        /* Buttons Outline */
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: #fff;
        }

        /* Buttons Standard */
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: #fff;
        }

        .btn-primary:hover,
        .btn-primary:active,
        .btn-primary:focus {
            background-color: var(--primary-light) !important;
            border-color: var(--primary-light) !important;
            color: #fff;
        }

        /* Buttons Actions (Edit) */
        .btn-info {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
            color: #fff;
        }

        .btn-info:hover,
        .btn-info:active {
            background-color: #b08d55 !important;
            border-color: #b08d55 !important;
            color: #fff;
        }

        /* Buttons Secondary (View) */
        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        /* Table Styling */
        .table thead th {
            border-bottom: 2px solid var(--accent-color);
            color: var(--primary-color);
            font-family: 'Playfair Display', serif;
        }

        /* Pagination */
        .page-link {
            color: var(--primary-color);
        }

        .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Fix Summernote Fullscreen Z-Index */
        .note-editor.note-frame.fullscreen {
            z-index: 10000 !important;
            background-color: white;
            /* Ensure background is opaque */
        }

        /* Hide text noise overlay in fullscreen to prevent visual glitches */
        .note-editor.note-frame.fullscreen~body::before {
            display: none;
        }
    </style>
</head>

<body>


    <div class="sidebar d-flex flex-column" id="sidebar">
        <div class="sidebar-header">
            <a href="/" class="sidebar-brand">
                <span class="d-block text-uppercase small"
                    style="letter-spacing: 2px; font-family: 'Inter', sans-serif; font-size: 0.8rem; color: var(--accent-color);">Administration</span>
                S. A. Chopplet
            </a>
        </div>

        <div class="flex-grow-1 py-4">
            <a href="/admin" class="<?= $_SERVER['REQUEST_URI'] === '/admin' ? 'active' : '' ?>">
                <i class="fas fa-tachometer-alt"></i> Tableau de bord
            </a>
            <a href="/admin/stats" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/stats') === 0 ? 'active' : '' ?>">
                <i class="fas fa-chart-line"></i> Statistiques
            </a>
            <a href="/admin/contacts"
                class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/contacts') === 0 ? 'active' : '' ?>">
                <i class="fas fa-envelope"></i> Messagerie
                <?php
                $contactModel = new \App\Model\Contact();
                $unreadCount = $contactModel->getUnreadCount();
                if ($unreadCount > 0):
                    ?>
                    <span class="badge bg-danger rounded-pill float-end"><?= $unreadCount ?></span>
                <?php endif; ?>
            </a>
            <a href="/admin/articles"
                class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/articles') === 0 || strpos($_SERVER['REQUEST_URI'], '/admin/article') === 0 ? 'active' : '' ?>">
                <i class="fas fa-feather-alt"></i> Articles
            </a>
            <a href="/admin/users" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/users') === 0 ? 'active' : '' ?>">
                <i class="fas fa-users"></i> Utilisateurs
            </a>
            <a href="/admin/settings"
                class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/settings') === 0 ? 'active' : '' ?>">
                <i class="fas fa-cogs"></i> Configuration
            </a>
            <a href="/admin/logs" class="<?= strpos($_SERVER['REQUEST_URI'], '/admin/logs') === 0 ? 'active' : '' ?>">
                <i class="fas fa-list-ul"></i> Journaux (Logs)
            </a>
        </div>

        <div class="p-4 border-top border-secondary">
            <a href="/" target="_blank" class="text-white small mb-3 p-0 ps-3 border-0">
                <i class="fas fa-external-link-alt"></i> Voir le site
            </a>
            <a href="/logout" class="text-danger small p-0 ps-3 border-0 mt-3 d-flex align-items-center">
                <i class="fas fa-sign-out-alt"></i> Déconnexion
            </a>
        </div>
    </div>


    <div class="main-content">

        <button class="btn btn-outline-dark d-md-none mb-3"
            onclick="document.getElementById('sidebar').classList.toggle('show')">
            <i class="fas fa-bars"></i> Menu
        </button>

        <?= $content ?>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#summernote').summernote({
                placeholder: 'Écrivez votre contenu ici avec élégance...',
                tabsize: 2,
                height: 400,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['fontname', ['fontname']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ],
                fontNames: ['Inter', 'Playfair Display', 'Arial', 'Times New Roman'],
                fontNamesIgnoreCheck: ['Inter', 'Playfair Display']
            });
        });
    </script>
</body>

</html>