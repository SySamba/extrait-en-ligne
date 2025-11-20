# Connexion Base de Données Centralisée - Mairie de Khombole

## Vue d'ensemble

Le système de connexion à la base de données a été centralisé pour éviter la duplication de code et faciliter la maintenance. Toute la configuration est maintenant gérée dans un seul endroit.

## Fichiers du système

### Fichier principal
- `db_connection.php` - Gestionnaire de connexion centralisé
- `config.php` - Configuration globale (contient les constantes DB)

### Fichiers mis à jour
- `suivi_demande.php` ✅
- `detail_demande.php` ✅
- `liste_demandes.php` ✅
- `traiter_demande.php` ✅

## Utilisation

### Méthode recommandée (avec config.php)

```php
<?php
require_once 'config.php';

// Utiliser la fonction existante de config.php
$pdo = getDBConnection();

// Votre code ici...
?>
```

### Méthode alternative (sans config.php)

```php
<?php
require_once 'db_connection.php';

// Utiliser la fonction centralisée
$pdo = createPDOConnection();

// Votre code ici...
?>
```

### Obtenir la configuration

```php
<?php
require_once 'db_connection.php';

// Obtenir la configuration sous forme de tableau
$config = getDatabaseConfig();
echo $config['host']; // localhost
echo $config['dbname']; // mairie_khombole
?>
```

## Fonctions disponibles

### `getDBConnection()`
- **Source** : `config.php`
- **Description** : Fonction principale recommandée
- **Retour** : Instance PDO configurée
- **Avantages** : Gestion d'erreurs complète, logging automatique

### `createPDOConnection()`
- **Source** : `db_connection.php`
- **Description** : Alternative simple
- **Retour** : Instance PDO configurée
- **Usage** : Pages qui n'incluent pas config.php

### `getDatabaseConnection()`
- **Source** : `db_connection.php`
- **Description** : Alias pour getDBConnection()
- **Retour** : Instance PDO configurée

### `getDatabaseConfig()`
- **Source** : `db_connection.php`
- **Description** : Retourne la configuration DB
- **Retour** : Tableau associatif avec la config

## Configuration

La configuration est centralisée dans `config.php` :

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'mairie_khombole');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
```

## Migration depuis l'ancien système

### Avant (code dupliqué)
```php
<?php
$config = [
    'host' => 'localhost',
    'dbname' => 'mairie_khombole',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4'
];

$dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
$pdo = new PDO($dsn, $config['username'], $config['password'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);
?>
```

### Après (centralisé)
```php
<?php
require_once 'db_connection.php';
$pdo = createPDOConnection();
?>
```

## Avantages

### ✅ Maintenance simplifiée
- Un seul endroit pour changer la configuration
- Pas de duplication de code
- Cohérence garantie

### ✅ Sécurité renforcée
- Configuration centralisée et sécurisée
- Gestion d'erreurs standardisée
- Logging automatique des erreurs

### ✅ Performance
- Connexion réutilisable
- Configuration optimisée
- Gestion de la mémoire

### ✅ Flexibilité
- Plusieurs méthodes d'accès
- Compatible avec l'ancien code
- Facile à étendre

## Exemples d'utilisation

### Exemple 1 : Page simple
```php
<?php
require_once 'db_connection.php';

try {
    $pdo = createPDOConnection();
    
    $stmt = $pdo->prepare("SELECT * FROM demandes_actes WHERE id = ?");
    $stmt->execute([1]);
    $demande = $stmt->fetch();
    
    print_r($demande);
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
```

### Exemple 2 : Page avec config.php
```php
<?php
require_once 'config.php';

try {
    $pdo = getDBConnection();
    
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM demandes_actes");
    $stmt->execute();
    $result = $stmt->fetch();
    
    echo "Total demandes : " . $result['total'];
} catch (Exception $e) {
    logActivity("Erreur DB : " . $e->getMessage(), 'ERROR');
    echo "Erreur de base de données";
}
?>
```

### Exemple 3 : Classe avec injection
```php
<?php
require_once 'db_connection.php';

class MonService {
    private $pdo;
    
    public function __construct() {
        $this->pdo = createPDOConnection();
    }
    
    public function obtenirDemandes() {
        $stmt = $this->pdo->prepare("SELECT * FROM demandes_actes");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
?>
```

## Dépannage

### Erreur "Call to undefined function"
- Vérifiez que vous incluez bien `db_connection.php` ou `config.php`
- Vérifiez l'ordre des includes

### Erreur de connexion
- Vérifiez la configuration dans `config.php`
- Vérifiez que MySQL est démarré
- Consultez les logs dans `logs/app_*.log`

### Performance lente
- La connexion est créée à chaque appel
- Pour les pages complexes, stockez la connexion dans une variable

## Bonnes pratiques

### ✅ À faire
- Utilisez `getDBConnection()` quand possible
- Gérez les exceptions
- Fermez les statements après usage
- Utilisez les requêtes préparées

### ❌ À éviter
- Ne pas dupliquer la configuration
- Ne pas ignorer les erreurs
- Ne pas utiliser de requêtes directes
- Ne pas oublier les includes

## Support

Pour toute question sur le système de connexion :
1. Consultez les logs dans `logs/`
2. Vérifiez la configuration dans `config.php`
3. Testez avec un script simple
4. Contactez l'équipe de développement
