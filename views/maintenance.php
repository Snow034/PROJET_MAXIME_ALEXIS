<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance - S. A. Chopplet</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Inter:wght@300;400;600&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #1a4a3b;
            color: #ffffff;
            height: 100vh;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }
        .container {
            max-width: 600px;
            padding: 2rem;
        }
        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #c5a065;
        }
        p {
            font-size: 1.2rem;
            opacity: 0.8;
            line-height: 1.6;
        }
        .divider {
            width: 100px;
            height: 2px;
            background-color: #c5a065;
            margin: 2rem auto;
        }
        .login-link {
            display: inline-block;
            margin-top: 3rem;
            color: rgba(255, 255, 255, 0.3);
            text-decoration: none;
            font-size: 0.9rem;
        }
        .login-link:hover {
            color: #c5a065;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Site en Maintenance</h1>
        <div class="divider"></div>
        <p>Le site est actuellement indisponible pour une courte période de maintenance.</p>
        <p>Nous revenons très vite.</p>
        <a href="/connexion" class="login-link">Accès Administrateur</a>
    </div>
</body>
</html>