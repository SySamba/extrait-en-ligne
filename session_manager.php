<?php
/**
 * Gestionnaire de sessions centralisé
 * Mairie de Khombole - Système de demande d'actes
 */

require_once 'config.php';

class SessionManager {
    
    private static $instance = null;
    private $sessionStarted = false;
    
    // Configuration des sessions
    const SESSION_TIMEOUT = 7200; // 2 heures
    const REGENERATE_INTERVAL = 300; // 5 minutes
    const MAX_LOGIN_ATTEMPTS = 5;
    const LOCKOUT_TIME = 900; // 15 minutes
    
    /**
     * Singleton pattern
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructeur privé pour le pattern singleton
     */
    private function __construct() {
        $this->startSecureSession();
    }
    
    /**
     * Démarrage sécurisé de la session
     */
    private function startSecureSession() {
        if (session_status() === PHP_SESSION_NONE && !$this->sessionStarted) {
            // Configuration sécurisée des cookies de session
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) ? 1 : 0);
            ini_set('session.use_strict_mode', 1);
            ini_set('session.cookie_samesite', 'Strict');
            
            // Nom de session personnalisé
            session_name('MAIRIE_KHOMBOLE_SESSION');
            
            session_start();
            $this->sessionStarted = true;
            
            // Régénération périodique de l'ID de session
            $this->regenerateSessionId();
            
            logActivity("Session démarrée - IP: " . $this->getClientIP());
        }
    }
    
    /**
     * Régénération de l'ID de session
     */
    private function regenerateSessionId() {
        if (!isset($_SESSION['last_regeneration'])) {
            $_SESSION['last_regeneration'] = time();
        } elseif (time() - $_SESSION['last_regeneration'] > self::REGENERATE_INTERVAL) {
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
            logActivity("ID de session régénéré");
        }
    }
    
    /**
     * Connexion administrateur
     */
    public function loginAdmin($email, $password) {
        // Vérifier les tentatives de connexion
        if ($this->isAccountLocked($email)) {
            $remainingTime = $this->getLockoutRemainingTime($email);
            throw new Exception("Compte temporairement verrouillé. Réessayez dans " . ceil($remainingTime / 60) . " minutes.");
        }
        
        // Vérifier les identifiants
        if ($this->verifyAdminCredentials($email, $password)) {
            // Connexion réussie
            $this->setAdminSession($email);
            $this->clearLoginAttempts($email);
            
            logActivity("Connexion admin réussie - Email: $email");
            return true;
        } else {
            // Connexion échouée
            $this->recordFailedAttempt($email);
            logActivity("Tentative de connexion échouée - Email: $email", 'WARNING');
            throw new Exception("Email ou mot de passe incorrect.");
        }
    }
    
    /**
     * Vérification des identifiants administrateur
     */
    private function verifyAdminCredentials($email, $password) {
        try {
            $pdo = getDBConnection();
            $stmt = $pdo->prepare("SELECT id, email, password_hash, is_active FROM admins WHERE email = ? AND is_active = 1");
            $stmt->execute([$email]);
            $admin = $stmt->fetch();
            
            if ($admin && password_verify($password, $admin['password_hash'])) {
                return $admin;
            }
            
            // Fallback pour l'admin par défaut (à supprimer en production)
            if ($email === 'mariedekhombole@gmail.com' && $password === 'Khombole2025@#') {
                return ['id' => 1, 'email' => $email, 'is_active' => 1];
            }
            
            return false;
        } catch (Exception $e) {
            logActivity("Erreur lors de la vérification des identifiants: " . $e->getMessage(), 'ERROR');
            
            // Fallback pour l'admin par défaut
            if ($email === 'mariedekhombole@gmail.com' && $password === 'Khombole2025@#') {
                return ['id' => 1, 'email' => $email, 'is_active' => 1];
            }
            
            return false;
        }
    }
    
    /**
     * Configuration de la session administrateur
     */
    private function setAdminSession($email) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_email'] = $email;
        $_SESSION['admin_id'] = 1; // À adapter selon la base de données
        $_SESSION['login_time'] = time();
        $_SESSION['last_activity'] = time();
        $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $_SESSION['login_ip'] = $this->getClientIP();
        
        // Générer un token CSRF pour l'admin
        $_SESSION['csrf_token_admin'] = bin2hex(random_bytes(32));
    }
    
    /**
     * Vérification de la connexion administrateur
     */
    public function isAdminLoggedIn() {
        if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
            return false;
        }
        
        // Vérifier l'expiration de session
        if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > self::SESSION_TIMEOUT) {
            $this->logoutAdmin();
            return false;
        }
        
        // Vérifier la cohérence de l'user agent (sécurité)
        if (isset($_SESSION['user_agent']) && $_SESSION['user_agent'] !== ($_SERVER['HTTP_USER_AGENT'] ?? '')) {
            logActivity("Tentative de hijacking de session détectée", 'CRITICAL');
            $this->logoutAdmin();
            return false;
        }
        
        // Mettre à jour la dernière activité
        $_SESSION['last_activity'] = time();
        
        return true;
    }
    
    /**
     * Redirection si non connecté
     */
    public function requireAdminLogin($redirectUrl = null) {
        if (!$this->isAdminLoggedIn()) {
            $currentPage = basename($_SERVER['PHP_SELF']);
            $redirect = $redirectUrl ?? $currentPage;
            
            header('Location: admin_login.php?redirect=' . urlencode($redirect));
            exit;
        }
    }
    
    /**
     * Déconnexion administrateur
     */
    public function logoutAdmin() {
        if (isset($_SESSION['admin_email'])) {
            logActivity("Déconnexion admin - Email: " . $_SESSION['admin_email']);
        }
        
        // Nettoyer les variables de session admin
        unset($_SESSION['admin_logged_in']);
        unset($_SESSION['admin_email']);
        unset($_SESSION['admin_id']);
        unset($_SESSION['login_time']);
        unset($_SESSION['last_activity']);
        unset($_SESSION['user_agent']);
        unset($_SESSION['login_ip']);
        unset($_SESSION['csrf_token_admin']);
    }
    
    /**
     * Destruction complète de la session
     */
    public function destroySession() {
        if ($this->sessionStarted) {
            session_unset();
            session_destroy();
            
            // Supprimer le cookie de session
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            
            $this->sessionStarted = false;
            logActivity("Session détruite");
        }
    }
    
    /**
     * Obtenir les informations de l'admin connecté
     */
    public function getAdminInfo() {
        if ($this->isAdminLoggedIn()) {
            return [
                'id' => $_SESSION['admin_id'] ?? null,
                'email' => $_SESSION['admin_email'] ?? '',
                'login_time' => $_SESSION['login_time'] ?? 0,
                'last_activity' => $_SESSION['last_activity'] ?? 0,
                'login_ip' => $_SESSION['login_ip'] ?? ''
            ];
        }
        return null;
    }
    
    /**
     * Gestion des tentatives de connexion échouées
     */
    private function recordFailedAttempt($email) {
        $key = 'login_attempts_' . md5($email . $this->getClientIP());
        
        if (!isset($_SESSION[$key])) {
            $_SESSION[$key] = ['count' => 0, 'last_attempt' => 0];
        }
        
        $_SESSION[$key]['count']++;
        $_SESSION[$key]['last_attempt'] = time();
    }
    
    /**
     * Vérifier si le compte est verrouillé
     */
    private function isAccountLocked($email) {
        $key = 'login_attempts_' . md5($email . $this->getClientIP());
        
        if (!isset($_SESSION[$key])) {
            return false;
        }
        
        $attempts = $_SESSION[$key];
        
        if ($attempts['count'] >= self::MAX_LOGIN_ATTEMPTS) {
            $timeSinceLastAttempt = time() - $attempts['last_attempt'];
            return $timeSinceLastAttempt < self::LOCKOUT_TIME;
        }
        
        return false;
    }
    
    /**
     * Temps restant de verrouillage
     */
    private function getLockoutRemainingTime($email) {
        $key = 'login_attempts_' . md5($email . $this->getClientIP());
        
        if (isset($_SESSION[$key])) {
            $timeSinceLastAttempt = time() - $_SESSION[$key]['last_attempt'];
            return max(0, self::LOCKOUT_TIME - $timeSinceLastAttempt);
        }
        
        return 0;
    }
    
    /**
     * Effacer les tentatives de connexion
     */
    private function clearLoginAttempts($email) {
        $key = 'login_attempts_' . md5($email . $this->getClientIP());
        unset($_SESSION[$key]);
    }
    
    /**
     * Générer un token CSRF
     */
    public function generateCSRFToken() {
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Vérifier un token CSRF
     */
    public function verifyCSRFToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Générer un token CSRF pour l'admin
     */
    public function generateAdminCSRFToken() {
        if (!isset($_SESSION['csrf_token_admin'])) {
            $_SESSION['csrf_token_admin'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token_admin'];
    }
    
    /**
     * Vérifier un token CSRF admin
     */
    public function verifyAdminCSRFToken($token) {
        return isset($_SESSION['csrf_token_admin']) && hash_equals($_SESSION['csrf_token_admin'], $token);
    }
    
    /**
     * Logger une action admin
     */
    public function logAdminAction($action, $details = '') {
        if (!$this->isAdminLoggedIn()) return;
        
        $adminInfo = $this->getAdminInfo();
        $logFile = LOG_PATH . 'admin_' . date('Y-m-d') . '.log';
        $timestamp = date('Y-m-d H:i:s');
        $ip = $this->getClientIP();
        $email = $adminInfo['email'] ?? 'Unknown';
        
        $logEntry = "[$timestamp] [$ip] [$email] $action";
        if (!empty($details)) {
            $logEntry .= " - $details";
        }
        $logEntry .= PHP_EOL;
        
        file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Obtenir l'adresse IP du client
     */
    private function getClientIP() {
        $ipKeys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 
                   'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'];
        
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                $ip = $_SERVER[$key];
                if (strpos($ip, ',') !== false) {
                    $ip = explode(',', $ip)[0];
                }
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    }
}

// Fonctions globales pour la compatibilité avec l'ancien code
function getSessionManager() {
    return SessionManager::getInstance();
}

function verifierConnexionAdmin() {
    getSessionManager()->requireAdminLogin();
}

function estConnecte() {
    return getSessionManager()->isAdminLoggedIn();
}

function deconnecterAdmin() {
    getSessionManager()->logoutAdmin();
}

function getAdminInfo() {
    return getSessionManager()->getAdminInfo();
}

function loggerActionAdmin($action, $details = '') {
    getSessionManager()->logAdminAction($action, $details);
}

function genererTokenCSRF() {
    return getSessionManager()->generateAdminCSRFToken();
}

function verifierTokenCSRF($token) {
    return getSessionManager()->verifyAdminCSRFToken($token);
}

// Initialisation automatique
$sessionManager = SessionManager::getInstance();
?>
