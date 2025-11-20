<?php
/**
 * Script de configuration initiale des administrateurs
 * Mairie de Khombole
 * 
 * À exécuter une seule fois pour créer la table des admins et l'admin par défaut
 */

require_once 'config.php';

try {
    $pdo = getDBConnection();
    
    // Créer la table des administrateurs si elle n'existe pas
    $createTableSQL = "
        CREATE TABLE IF NOT EXISTS admins (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(255) UNIQUE NOT NULL,
            password_hash VARCHAR(255) NOT NULL,
            nom VARCHAR(100) NOT NULL,
            prenom VARCHAR(100) NOT NULL,
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            last_login TIMESTAMP NULL,
            login_attempts INT DEFAULT 0,
            locked_until TIMESTAMP NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    $pdo->exec($createTableSQL);
    echo "✅ Table 'admins' créée avec succès.<br>";
    
    // Vérifier si l'admin par défaut existe déjà
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE email = ?");
    $stmt->execute(['mariedekhombole@gmail.com']);
    
    if ($stmt->fetchColumn() == 0) {
        // Créer l'admin par défaut
        $email = 'mariedekhombole@gmail.com';
        $password = 'Khombole2025@#';
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("
            INSERT INTO admins (email, password_hash, nom, prenom) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$email, $passwordHash, 'Admin', 'Mairie']);
        
        echo "✅ Administrateur par défaut créé avec succès.<br>";
        echo "📧 Email: $email<br>";
        echo "🔑 Mot de passe: $password<br>";
        echo "<br><strong>⚠️ IMPORTANT: Changez ce mot de passe après la première connexion!</strong><br>";
    } else {
        echo "ℹ️ L'administrateur par défaut existe déjà.<br>";
    }
    
    // Créer la table des sessions si elle n'existe pas
    $createSessionTableSQL = "
        CREATE TABLE IF NOT EXISTS admin_sessions (
            id VARCHAR(128) PRIMARY KEY,
            admin_id INT NOT NULL,
            ip_address VARCHAR(45) NOT NULL,
            user_agent TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            is_active BOOLEAN DEFAULT TRUE,
            FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ";
    
    $pdo->exec($createSessionTableSQL);
    echo "✅ Table 'admin_sessions' créée avec succès.<br>";
    
    echo "<br>🎉 Configuration terminée avec succès!<br>";
    echo "<br><a href='admin_login.php'>🔗 Aller à la page de connexion</a>";
    
} catch (Exception $e) {
    echo "❌ Erreur lors de la configuration: " . $e->getMessage();
    logActivity("Erreur setup admin: " . $e->getMessage(), 'ERROR');
}
?>
