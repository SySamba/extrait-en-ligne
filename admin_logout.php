<?php
/**
 * Déconnexion admin
 * Mairie de Khombole
 */

require_once 'admin_auth.php';

// Logger la déconnexion
if (estConnecte()) {
    loggerActionAdmin('Déconnexion admin');
}

// Déconnecter l'admin
deconnecterAdmin();

// Détruire la session
session_destroy();

// Rediriger vers la page de connexion
header('Location: admin_login.php?logout=1');
exit;
?>
