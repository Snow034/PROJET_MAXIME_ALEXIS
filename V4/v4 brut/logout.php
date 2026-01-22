<?php
session_start();

// Log avant destruction
if (isset($_SESSION['username'])) {
    require_once __DIR__ . "/includes/config.php";
    log_msg("Déconnexion : " . $_SESSION['username']);
}

// Détruire la session
$_SESSION = [];
session_destroy();

// Redirection
header("Location: /login.php");
exit;
?>