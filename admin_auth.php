<?php
/**
 * Gestion de l'authentification admin
 * Mairie de Khombole
 */

session_start();

/**
 * Vérifier si l'admin est connecté
 */
function verifierConnexionAdmin() {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        // Rediriger vers la page de connexion avec l'URL de retour
        $currentPage = basename($_SERVER['PHP_SELF']);
        header('Location: admin_login.php?redirect=' . urlencode($currentPage));
        exit;
    }
    
    // Vérifier l'expiration de session (2 heures)
    if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > 7200) {
        deconnecterAdmin();
        header('Location: admin_login.php?expired=1');
        exit;
    }
    
    // Mettre à jour le timestamp de dernière activité
    $_SESSION['last_activity'] = time();
}

/**
 * Déconnecter l'admin
 */
function deconnecterAdmin() {
    unset($_SESSION['admin_logged_in']);
    unset($_SESSION['admin_email']);
    unset($_SESSION['login_time']);
    unset($_SESSION['last_activity']);
}

/**
 * Obtenir les informations de l'admin connecté
 */
function getAdminInfo() {
    if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
        return [
            'email' => $_SESSION['admin_email'] ?? '',
            'login_time' => $_SESSION['login_time'] ?? 0,
            'last_activity' => $_SESSION['last_activity'] ?? 0
        ];
    }
    return null;
}

/**
 * Vérifier si l'admin est connecté (sans redirection)
 */
function estConnecte() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Logger les actions admin
 */
function loggerActionAdmin($action, $details = '') {
    if (!estConnecte()) return;
    
    $logFile = __DIR__ . '/logs/admin_' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    $email = $_SESSION['admin_email'] ?? 'Unknown';
    
    $logEntry = "[$timestamp] [$ip] [$email] $action";
    if (!empty($details)) {
        $logEntry .= " - $details";
    }
    $logEntry .= PHP_EOL;
    
    // Créer le dossier logs s'il n'existe pas
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

/**
 * Générer un token CSRF pour les formulaires admin
 */
function genererTokenCSRF() {
    if (!isset($_SESSION['csrf_token_admin'])) {
        $_SESSION['csrf_token_admin'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token_admin'];
}

/**
 * Vérifier le token CSRF
 */
function verifierTokenCSRF($token) {
    return isset($_SESSION['csrf_token_admin']) && 
           hash_equals($_SESSION['csrf_token_admin'], $token);
}
?>
