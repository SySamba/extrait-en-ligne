<?php
/**
 * Configuration de l'application
 * Mairie de Khombole - Système de demande d'actes
 */

// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'mairie_khombole');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Configuration de l'application
define('APP_NAME', 'Mairie de Khombole - Demandes d\'Actes');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/mairie-khombole/');

// Configuration des emails
define('MAIL_FROM', 'sambasy837@gmail.com');
define('MAIL_FROM_NAME', 'Mairie de Khombole - État Civil');
define('MAIL_REPLY_TO', 'sambasy837@gmail.com');

// Configuration SMTP Gmail
define('SMTP_HOST', 'smtp.gmail.com'); // Serveur SMTP Gmail
define('SMTP_PORT', 587); // Port SMTP pour TLS
define('SMTP_USERNAME', 'sambasy837@gmail.com');
define('SMTP_PASSWORD', 'Khombole2025@#');
define('SMTP_ENCRYPTION', 'tls'); // TLS pour Gmail

// Configuration des paiements
define('WAVE_NUMBER', '781210618');
define('ORANGE_MONEY_NUMBER', '781210618');

// Tarifs (en FCFA)
define('TARIF_EXTRAIT_NAISSANCE', 500);
define('TARIF_COPIE_LITTERALE', 1000);
define('TARIF_CERTIFICAT_RESIDENCE', 500);
define('TARIF_CERTIFICAT_VIE', 500);
define('TARIF_CERTIFICAT_DECES', 500);

// Délais de traitement (en jours)
define('DELAI_TRAITEMENT_STANDARD', 3);
define('DELAI_TRAITEMENT_URGENT', 1);

// Sécurité
define('SESSION_TIMEOUT', 3600); // 1 heure
define('MAX_LOGIN_ATTEMPTS', 5);

// Chemins des fichiers
define('UPLOAD_PATH', __DIR__ . '/uploads/');
define('LOG_PATH', __DIR__ . '/logs/');

// Créer les dossiers s'ils n'existent pas
if (!file_exists(UPLOAD_PATH)) {
    mkdir(UPLOAD_PATH, 0755, true);
}

if (!file_exists(LOG_PATH)) {
    mkdir(LOG_PATH, 0755, true);
}

// Fonction de connexion à la base de données
function getDBConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ]);
        } catch (PDOException $e) {
            error_log("Erreur de connexion DB: " . $e->getMessage());
            throw new Exception("Erreur de connexion à la base de données");
        }
    }
    
    return $pdo;
}

// Fonction de logging
function logActivity($message, $level = 'INFO') {
    $logFile = LOG_PATH . 'app_' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
    $logEntry = "[$timestamp] [$level] [$ip] $message" . PHP_EOL;
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

// Fonction de nettoyage des données
function cleanInput($data) {
    if (is_array($data)) {
        return array_map('cleanInput', $data);
    }
    return trim(htmlspecialchars($data, ENT_QUOTES, 'UTF-8'));
}

// Fonction de validation d'email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Fonction de validation de téléphone sénégalais
function isValidSenegalPhone($phone) {
    // Format: 7XXXXXXXX (9 chiffres commençant par 7)
    return preg_match('/^7[0-9]{8}$/', $phone);
}

// Fonction de génération de token CSRF
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Fonction de vérification de token CSRF
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Démarrage de session sécurisé
function startSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
        ini_set('session.use_strict_mode', 1);
        session_start();
        
        // Régénérer l'ID de session périodiquement
        if (!isset($_SESSION['last_regeneration'])) {
            $_SESSION['last_regeneration'] = time();
        } elseif (time() - $_SESSION['last_regeneration'] > 300) { // 5 minutes
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
    }
}

// Types d'actes disponibles
function getTypesActes() {
    return [
        'extrait_naissance' => [
            'label' => 'Extrait d\'acte de naissance',
            'tarif' => TARIF_EXTRAIT_NAISSANCE,
            'delai' => DELAI_TRAITEMENT_STANDARD
        ],
        'copie_litterale_naissance' => [
            'label' => 'Copie littérale d\'acte de naissance',
            'tarif' => TARIF_COPIE_LITTERALE,
            'delai' => DELAI_TRAITEMENT_STANDARD
        ],
        'extrait_mariage' => [
            'label' => 'Extrait d\'acte de mariage',
            'tarif' => TARIF_EXTRAIT_NAISSANCE,
            'delai' => DELAI_TRAITEMENT_STANDARD
        ],
        'certificat_residence' => [
            'label' => 'Certificat de résidence',
            'tarif' => TARIF_CERTIFICAT_RESIDENCE,
            'delai' => DELAI_TRAITEMENT_STANDARD
        ],
        'certificat_vie_individuelle' => [
            'label' => 'Certificat de vie individuelle',
            'tarif' => TARIF_CERTIFICAT_VIE,
            'delai' => DELAI_TRAITEMENT_STANDARD
        ],
        'certificat_vie_collective' => [
            'label' => 'Certificat de vie collective',
            'tarif' => TARIF_CERTIFICAT_VIE,
            'delai' => DELAI_TRAITEMENT_STANDARD
        ],
        'certificat_deces' => [
            'label' => 'Certificat de décès',
            'tarif' => TARIF_CERTIFICAT_DECES,
            'delai' => DELAI_TRAITEMENT_STANDARD
        ]
    ];
}

// Statuts des demandes
function getStatutsLabels() {
    return [
        'en_attente' => 'En attente de traitement',
        'en_traitement' => 'En cours de traitement',
        'pret' => 'Prêt pour retrait/envoi',
        'delivre' => 'Délivré',
        'rejete' => 'Rejeté'
    ];
}

// Couleurs des statuts pour l'affichage
function getStatutsColors() {
    return [
        'en_attente' => 'warning',
        'en_traitement' => 'info',
        'pret' => 'success',
        'delivre' => 'primary',
        'rejete' => 'danger'
    ];
}

// Gestion des erreurs globales
set_error_handler(function($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }
    
    $errorMsg = "Erreur PHP: $message dans $file ligne $line";
    logActivity($errorMsg, 'ERROR');
    
    // En production, ne pas afficher les erreurs
    if (defined('PRODUCTION') && PRODUCTION) {
        return true;
    }
    
    return false;
});

// Gestion des exceptions non capturées
set_exception_handler(function($exception) {
    $errorMsg = "Exception non capturée: " . $exception->getMessage() . 
                " dans " . $exception->getFile() . 
                " ligne " . $exception->getLine();
    logActivity($errorMsg, 'CRITICAL');
    
    // En production, afficher une page d'erreur générique
    if (defined('PRODUCTION') && PRODUCTION) {
        http_response_code(500);
        include 'error_500.php';
        exit;
    } else {
        echo "<h1>Erreur système</h1>";
        echo "<p>" . htmlspecialchars($exception->getMessage()) . "</p>";
        echo "<pre>" . htmlspecialchars($exception->getTraceAsString()) . "</pre>";
    }
});

// Initialisation
startSecureSession();
logActivity("Application initialisée");
?>
