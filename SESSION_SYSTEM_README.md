# Système de Gestion de Session - Mairie de Khombole

## Vue d'ensemble

Ce système de gestion de session centralisé a été créé pour sécuriser l'accès aux pages d'administration de l'application Mairie de Khombole. Il remplace l'ancien système d'authentification et offre une sécurité renforcée.

## Fichiers créés/modifiés

### Nouveaux fichiers :
- `session_manager.php` - Gestionnaire de sessions centralisé (classe principale)
- `require_admin.php` - Fichier d'inclusion simple pour les pages admin
- `admin_traiter_demande.php` - Page d'administration pour traiter les demandes
- `create_admin.php` - Interface pour créer de nouveaux administrateurs
- `setup_admin.php` - Script de configuration initiale

### Fichiers modifiés :
- `admin_login.php` - Mise à jour pour utiliser le nouveau système
- `admin_logout.php` - Mise à jour pour utiliser le nouveau système
- `liste_demandes.php` - Mise à jour pour utiliser le nouveau système

## Fonctionnalités du système

### Sécurité renforcée
- **Hachage des mots de passe** : Utilisation de `password_hash()` et `password_verify()`
- **Protection CSRF** : Tokens CSRF pour tous les formulaires admin
- **Limitation des tentatives** : Verrouillage temporaire après 5 tentatives échouées
- **Régénération d'ID de session** : Automatique toutes les 5 minutes
- **Détection de hijacking** : Vérification de l'User-Agent
- **Sessions sécurisées** : Configuration optimale des cookies

### Gestion des sessions
- **Timeout automatique** : Sessions expirées après 2 heures d'inactivité
- **Logging complet** : Toutes les actions admin sont enregistrées
- **Gestion centralisée** : Une seule classe pour toute la gestion des sessions

## Installation et configuration

### 1. Configuration initiale

Exécutez le script de configuration pour créer les tables nécessaires :

```
http://votre-domaine/mairie-khombole/setup_admin.php
```

Ce script va :
- Créer la table `admins`
- Créer la table `admin_sessions`
- Créer l'administrateur par défaut

### 2. Identifiants par défaut

Après l'installation :
- **Email** : `mariedekhombole@gmail.com`
- **Mot de passe** : `Khombole2025@#`

⚠️ **IMPORTANT** : Changez ce mot de passe après la première connexion !

### 3. Utilisation dans vos pages

Pour protéger une page admin, ajoutez simplement en haut du fichier :

```php
<?php
require_once 'require_admin.php';
// Votre code ici...
?>
```

Ou pour plus de contrôle :

```php
<?php
require_once 'session_manager.php';
verifierConnexionAdmin();
// Votre code ici...
?>
```

## API du SessionManager

### Méthodes principales

```php
$sessionManager = getSessionManager();

// Connexion
$sessionManager->loginAdmin($email, $password);

// Vérification de connexion
if ($sessionManager->isAdminLoggedIn()) {
    // Admin connecté
}

// Redirection si non connecté
$sessionManager->requireAdminLogin();

// Déconnexion
$sessionManager->logoutAdmin();

// Destruction complète de session
$sessionManager->destroySession();

// Informations admin
$adminInfo = $sessionManager->getAdminInfo();

// Tokens CSRF
$token = $sessionManager->generateAdminCSRFToken();
$isValid = $sessionManager->verifyAdminCSRFToken($token);

// Logging
$sessionManager->logAdminAction('Action effectuée', 'Détails optionnels');
```

### Fonctions de compatibilité

Pour maintenir la compatibilité avec l'ancien code :

```php
verifierConnexionAdmin();    // Redirige si non connecté
estConnecte();              // Retourne true/false
deconnecterAdmin();         // Déconnecte l'admin
getAdminInfo();             // Infos de l'admin connecté
loggerActionAdmin($action); // Log une action
genererTokenCSRF();         // Génère un token CSRF
verifierTokenCSRF($token);  // Vérifie un token CSRF
```

## Structure de la base de données

### Table `admins`
```sql
CREATE TABLE admins (
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
);
```

### Table `admin_sessions`
```sql
CREATE TABLE admin_sessions (
    id VARCHAR(128) PRIMARY KEY,
    admin_id INT NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_activity TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    is_active BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE
);
```

## Logging et monitoring

### Fichiers de log
- `logs/admin_YYYY-MM-DD.log` - Actions des administrateurs
- `logs/app_YYYY-MM-DD.log` - Logs généraux de l'application

### Types de logs
- **INFO** : Actions normales
- **WARNING** : Tentatives de connexion échouées
- **ERROR** : Erreurs système
- **CRITICAL** : Tentatives de hijacking détectées

## Sécurité

### Mesures implémentées
1. **Hachage sécurisé** des mots de passe
2. **Protection CSRF** sur tous les formulaires
3. **Limitation des tentatives** de connexion
4. **Verrouillage temporaire** des comptes
5. **Détection de hijacking** de session
6. **Logging complet** des actions
7. **Expiration automatique** des sessions
8. **Régénération d'ID** de session

### Recommandations
- Utilisez HTTPS en production
- Changez les mots de passe par défaut
- Surveillez les logs régulièrement
- Mettez à jour les mots de passe périodiquement

## Maintenance

### Nettoyage des logs
Les logs s'accumulent dans le dossier `logs/`. Pensez à les archiver ou supprimer périodiquement.

### Nettoyage des sessions
Les sessions inactives sont automatiquement nettoyées, mais vous pouvez ajouter un script cron pour nettoyer la table `admin_sessions`.

## Dépannage

### Problèmes courants

1. **"Session expirée"** : Normal après 2h d'inactivité
2. **"Compte verrouillé"** : Attendez 15 minutes ou contactez un admin
3. **"Token CSRF invalide"** : Rechargez la page et réessayez
4. **Erreurs de connexion DB** : Vérifiez la configuration dans `config.php`

### Debug
Activez le mode debug en ajoutant dans `config.php` :
```php
define('DEBUG_MODE', true);
```

## Migration depuis l'ancien système

L'ancien système (`admin_auth.php`) est toujours compatible grâce aux fonctions de compatibilité. Vous pouvez migrer progressivement vos pages en remplaçant :

```php
require_once 'admin_auth.php';
```

par :

```php
require_once 'require_admin.php';
```

## Support

Pour toute question ou problème, consultez les logs dans le dossier `logs/` ou contactez l'équipe de développement.
