<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Conférences de Solange Anastasia Chopplet' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Dancing+Script:wght@600&display=swap"
        rel="stylesheet">
    <style>
        :root {
            /* Palette Premium Literary */
            --primary-color: #1a4a3b;
            /* Vert Bibliothèque Profond (Updated) */
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
            --card-shadow: 0 12px 40px rgba(22, 50, 42, 0.08);
            --glass-bg: rgba(252, 251, 248, 0.92);
            --border-radius: 4px;
            /* Angles plus nets pour aspect "Livre" */
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
            line-height: 1.8;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
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
        .script-font {
            font-family: 'Dancing Script', cursive;
        }
        /* Navbar Sophistiquée - Version Dark */
        .navbar {
            background-color: #1a4a3b !important;
            /* Forces Dark Green */
            border-bottom: none;
            padding: 1.2rem 0;
            transition: all 0.4s ease;
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.4rem;
            letter-spacing: 0.5px;
            color: #ffffff !important;
            /* White Text */
        }
        .nav-link {
            color: rgba(255, 255, 255, 0.85) !important;
            /* Light Text */
            font-weight: 500;
            margin: 0 12px;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            position: relative;
            transition: color 0.3s ease;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 1px;
            bottom: 2px;
            left: 50%;
            background-color: var(--accent-color);
            transition: all 0.4s cubic-bezier(0.25, 1, 0.5, 1);
            transform: translateX(-50%);
        }
        .nav-link:hover {
            color: var(--accent-color) !important;
            /* Gold on Hover */
            opacity: 1;
        }
        .nav-link:hover::after {
            width: 80%;
        }
        /* Boutons Premium */
        /* Boutons Premium */
        .btn-primary {
            background: linear-gradient(135deg, #c5a065, #d4a373) !important;
            border: none !important;
            padding: 0.8rem 2.5rem;
            border-radius: 50px;
            font-family: 'Playfair Display', serif;
            font-style: italic;
            font-size: 1.2rem;
            color: #1a3c34 !important;
            /* Dark Text on Gold */
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2) !important;
            opacity: 1 !important;
            position: relative;
            z-index: 10;
            cursor: pointer !important;
        }
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.25);
            background: linear-gradient(135deg, #d4a373, var(--accent-color));
            color: #1a3c34;
        }
        .btn-outline-primary {
            border-color: var(--primary-color);
            color: var(--primary-color);
            border-radius: 2px;
        }
        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: #fff;
        }
        /* Footer */
        footer {
            background-color: #152e28 !important;
            color: #e0e0e0 !important;
            border-top: 4px solid var(--accent-color);
            margin-top: auto;
        }
        footer a {
            color: #a0a0a0;
            text-decoration: none;
            transition: color 0.3s;
        }
        footer a:hover {
            color: var(--accent-color);
        }
        /* Animations */
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        main {
            animation: fadeUp 1s cubic-bezier(0.215, 0.610, 0.355, 1.000);
            min-height: 80vh;
        }
        /* Card Styling Override */
        .card {
            border: none;
            background: #fff;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            transition: all 0.4s ease;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.02);
        }
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(22, 50, 42, 0.12);
        }
        /* Horizontal Card Styling */
        .card-horizontal {
            border: none;
            background: #fff;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            transition: all 0.4s ease;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.03);
            display: flex;
            flex-direction: row;
            height: 280px;
        }
        .card-horizontal .img-wrapper {
            width: 40%;
            height: 100%;
            overflow: hidden;
            position: relative;
        }
        .card-horizontal .img-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s ease;
        }
        .card-horizontal:hover .img-wrapper img {
            transform: scale(1.05);
        }
        .card-horizontal .card-body {
            width: 60%;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .card-horizontal:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(22, 50, 42, 0.12);
        }
        @media (max-width: 768px) {
            .card-horizontal {
                flex-direction: column;
                height: auto;
            }
            .card-horizontal .img-wrapper {
                width: 100%;
                height: 200px;
            }
            .card-horizontal .card-body {
                width: 100%;
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="/">
                <span class="d-none d-lg-block serif-font">Conférences S. A. Chopplet</span>
                <span class="d-lg-none serif-font">S. A. Chopplet</span>
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon" style="filter: invert(1);"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="/">Accueil</a></li>
                    <?php if (isset($_SESSION['user'])): ?>
                        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                            <li class="nav-item"><a class="nav-link" href="/admin">Administration</a></li>
                        <?php endif; ?>
                        <li class="nav-item"><a class="nav-link" href="/mes-messages">Messagerie</a></li>
                        <li class="nav-item"><a class="nav-link" href="/logout"
                                style="color: #ff6b6b !important;">Déconnexion</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a class="nav-link" href="/connexion">Connexion</a></li>
                        <li class="nav-item ms-3">
                            <a class="btn btn-primary" href="/inscription">Rejoindre</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <main>
        <?= $content ?>
    </main>
    <footer class="py-5">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h4 class="h5 serif-font text-white mb-3">S. A. Chopplet</h4>
                    <p class="small opacity-75">
                        Agregée de lettres modernes.<br>
                        Conférencière passionnée par la transmission du savoir et l'analyse littéraire.
                    </p>
                </div>
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <h5 class="h6 text-uppercase fw-bold text-white-50 mb-3">Navigation</h5>
                    <ul class="list-unstyled small">
                        <li class="mb-2"><a href="/">Accueil</a></li>
                        <li class="mb-2"><a href="#">Archives</a></li>
                        <li class="mb-2"><a href="/contact">Contact</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h5 class="h6 text-uppercase fw-bold text-white-50 mb-3">Mentions</h5>
                    <p class="small opacity-50">
                        &copy; <?= date('Y') ?> Conférences de Solange Anastasia Chopplet.<br>
                        Tous droits réservés.
                    </p>
                </div>
            </div>
        </div>
    </footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</body>
</html>