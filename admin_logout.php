<?php
/**
 * Déconnexion admin
 * Mairie de Khombole
 */

require_once 'session_manager.php';

$sessionManager = getSessionManager();

// Logger la déconnexion
if ($sessionManager->isAdminLoggedIn()) {
    $sessionManager->logAdminAction('Déconnexion admin');
}

// Déconnecter l'admin et détruire la session
$sessionManager->logoutAdmin();
$sessionManager->destroySession();

// Rediriger vers la page de connexion
header('Location: admin_login.php?logout=1');
exit;
?>
