<?php
/**
 * Fichier d'inclusion pour les pages nécessitant une authentification admin
 * Mairie de Khombole
 * 
 * Usage: require_once 'require_admin.php'; au début de vos pages admin
 */

require_once 'session_manager.php';

// Vérifier l'authentification admin
verifierConnexionAdmin();

// Logger l'accès à la page (optionnel)
$currentPage = basename($_SERVER['PHP_SELF']);
loggerActionAdmin("Accès à la page: $currentPage");
?>
