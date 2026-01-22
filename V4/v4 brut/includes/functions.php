<?php
// Vérification admin sécurisée
function checkAdmin() {
    // Vérifier que la session est démarrée
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        $_SESSION['error'] = "Vous n'avez pas la permission pour cette action.";
        header("Location: /index.php");
        exit;
    }
}

// Vérifier si un utilisateur est connecté
function checkLogin() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['user_id'])) {
        header("Location: /login.php");
        exit;
    }
}

// Upload image sécurisé avec validation stricte
function uploadImage($file) {
    global $LOGS;
    
    // Vérifier que le fichier existe et qu'il n'y a pas d'erreur
    if (!$file || !isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    // Extensions autorisées (sécurité)
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $allowed_mime_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    
    // Vérifier l'extension
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed_extensions)) {
        log_msg("Tentative d'upload avec extension non autorisée : " . $ext);
        return null;
    }
    
    // Vérifier le type MIME (double vérification)
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mime, $allowed_mime_types)) {
        log_msg("Tentative d'upload avec type MIME non autorisé : " . $mime);
        return null;
    }
    
    // Vérifier la taille (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        log_msg("Fichier trop volumineux : " . $file['size'] . " bytes");
        return null;
    }
    
    // Générer un nom unique et sécurisé
    $name = uniqid('img_', true) . '.' . $ext;
    $upload_path = __DIR__ . "/../uploads/" . $name;
    
    // Créer le dossier si nécessaire
    $upload_dir = dirname($upload_path);
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Déplacer le fichier
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        log_msg("Image uploadée : " . $name);
        return "uploads/" . $name;
    }
    
    log_msg("Échec du déplacement du fichier uploadé");
    return null;
}

// Nettoyer les données utilisateur (protection XSS)
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Vérifier si un email est valide
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Redirection sécurisée
function redirect($url) {
    header("Location: " . $url);
    exit;
}
?>