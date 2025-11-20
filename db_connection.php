<?php
/**
 * Fichier de connexion à la base de données centralisé
 * Mairie de Khombole
 * 
 * Usage: require_once 'db_connection.php';
 * Puis utilisez: $pdo = getDBConnection();
 */

require_once 'config.php';

/**
 * Obtenir une connexion PDO à la base de données
 * Utilise la configuration définie dans config.php
 */
function getDatabaseConnection() {
    return getDBConnection();
}

/**
 * Configuration de base de données (pour compatibilité)
 * Retourne un tableau avec la configuration DB
 */
function getDatabaseConfig() {
    return [
        'host' => DB_HOST,
        'dbname' => DB_NAME,
        'username' => DB_USER,
        'password' => DB_PASS,
        'charset' => DB_CHARSET
    ];
}

/**
 * Créer une connexion PDO simple (pour les pages qui n'utilisent pas config.php)
 */
function createPDOConnection() {
    $config = getDatabaseConfig();
    
    try {
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
        $pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$config['charset']}"
        ]);
        
        return $pdo;
    } catch (PDOException $e) {
        error_log("Erreur de connexion DB: " . $e->getMessage());
        throw new Exception("Erreur de connexion à la base de données");
    }
}
?>
