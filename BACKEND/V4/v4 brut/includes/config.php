<?php
// Démarrage de session (OBLIGATOIRE en premier)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Définir le fuseau horaire (Paris)
date_default_timezone_set('Europe/Paris');

// Configuration erreurs pour développement (à désactiver en production)
ini_set('display_errors', 0); // Mettre à 0 sur InfinityFree
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Connexion base de données
try {
    $pdo = new PDO(
        "mysql:host=sql100.infinityfree.com;port=3306;dbname=if0_40940109_bdd;charset=utf8mb4",
        "if0_40940109",
        "oSplGzwp7ACsL",
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
    
    // Définir le fuseau horaire MySQL sur Paris
    $pdo->exec("SET time_zone = '+01:00'");
    
} catch (PDOException $e) {
    // Ne jamais afficher les détails de connexion en production
    die("Erreur de connexion à la base de données. Veuillez réessayer plus tard.");
}

// Initialisation du tableau de logs
$LOGS = [];

// Fonction de logging sécurisée
function log_msg($message) {
    global $LOGS;
    $log_entry = "[" . date("Y-m-d H:i:s") . "] " . $message;
    $LOGS[] = $log_entry;
    
    // Optionnel : écrire dans un fichier de log
    $log_file = __DIR__ . "/../logs/app.log";
    if (is_writable(dirname($log_file))) {
        file_put_contents($log_file, $log_entry . PHP_EOL, FILE_APPEND);
    }
}

// Création automatique du dossier uploads si nécessaire
$uploads_dir = __DIR__ . "/../uploads";
if (!is_dir($uploads_dir)) {
    mkdir($uploads_dir, 0755, true);
}

// Création automatique du dossier logs si nécessaire
$logs_dir = __DIR__ . "/../logs";
if (!is_dir($logs_dir)) {
    mkdir($logs_dir, 0755, true);
}
?>