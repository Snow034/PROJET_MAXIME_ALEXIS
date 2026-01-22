<?php
require_once __DIR__ . "/includes/config.php";
require_once __DIR__ . "/includes/functions.php";

$error = '';
$success = '';

// INSCRIPTION
if (isset($_POST['register'])) {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : '';
    $bio = isset($_POST['bio']) ? trim($_POST['bio']) : '';
    
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Tous les champs obligatoires sont requis.";
    } elseif (!is_valid_email($email)) {
        $error = "Email invalide.";
    } elseif (strlen($password) < 6) {
        $error = "Le mot de passe doit contenir au moins 6 caractères.";
    } elseif (strlen($username) < 3) {
        $error = "Le nom d'utilisateur doit contenir au moins 3 caractères.";
    } elseif ($password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id FROM user WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $error = "Cet email est déjà utilisé.";
            } else {
                $stmt = $pdo->prepare("SELECT id FROM user WHERE username = ?");
                $stmt->execute([$username]);
                
                if ($stmt->fetch()) {
                    $error = "Ce nom d'utilisateur est déjà pris.";
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("INSERT INTO user (username, email, password, bio, role) VALUES (?, ?, ?, ?, 'user')");
                    $stmt->execute([$username, $email, $hash, $bio]);
                    
                    log_msg("Nouvel utilisateur créé : $username / $email");
                    $success = "Compte créé avec succès ! Vous pouvez maintenant vous connecter.";
                }
            }
        } catch (PDOException $e) {
            $error = "Erreur lors de la création du compte.";
            log_msg("Erreur inscription : " . $e->getMessage());
        }
    }
}

if (isset($_SESSION['user_id'])) {
    redirect('/index.php');
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .register-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
        }

        .register-header {
            background: linear-gradient(135deg, #22c55e, #16a34a);
            padding: 2.5rem 2rem;
            text-align: center;
            color: white;
        }

        .register-header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .register-header p {
            opacity: 0.9;
        }

        .register-body {
            padding: 2.5rem 2rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }

        .alert-error {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 1px solid #6ee7b7;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        label .optional {
            font-weight: 400;
            color: #a0aec0;
            font-size: 0.85rem;
        }

        input, textarea {
            width: 100%;
            padding: 0.9rem;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s;
            font-family: inherit;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: #22c55e;
            box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1);
        }

        .btn {
            width: 100%;
            padding: 1rem;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 1rem;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(34, 197, 94, 0.4);
        }

        .divider {
            text-align: center;
            margin: 2rem 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e2e8f0;
        }

        .divider span {
            background: white;
            padding: 0 1rem;
            color: #a0aec0;
            font-size: 0.9rem;
            position: relative;
        }

        .link {
            text-align: center;
            margin-top: 1.5rem;
        }

        .link a {
            color: #22c55e;
            text-decoration: none;
            font-weight: 600;
        }

        .link a:hover {
            text-decoration: underline;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 1rem;
            color: #22c55e;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1>Inscription</h1>
            <p>Créez votre compte en quelques secondes</p>
        </div>

        <div class="register-body">
            <a href="/index.php" class="back-link">← Retour à l'accueil</a>

            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                    <div style="margin-top: 1rem;">
                        <a href="/login.php" style="color: #065f46; font-weight: 600;">Se connecter maintenant →</a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (!$success): ?>
            <form method="post">
                <div class="form-group">
                    <label for="username">Nom d'utilisateur</label>
                    <input type="text" id="username" name="username" required minlength="3" 
                           placeholder="john_doe" 
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="email">Adresse email</label>
                    <input type="email" id="email" name="email" required 
                           placeholder="votre@email.com"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>

                <div class="form-group">
                    <label for="bio">Biographie <span class="optional">(optionnel)</span></label>
                    <textarea id="bio" name="bio" placeholder="Parlez-nous de vous..."><?php echo isset($_POST['bio']) ? htmlspecialchars($_POST['bio']) : ''; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required minlength="6" 
                           placeholder="Minimum 6 caractères">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirmer le mot de passe</label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="6" 
                           placeholder="Retapez votre mot de passe">
                </div>

                <button type="submit" name="register" class="btn">Créer mon compte</button>
            </form>

            <div class="divider">
                <span>Vous avez déjà un compte ?</span>
            </div>

            <div class="link">
                <a href="/login.php">Se connecter</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>