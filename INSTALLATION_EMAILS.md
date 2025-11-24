# Installation du Système d'Emails - Mairie de Khombole

## 📧 Configuration Email Complète

Votre système d'emails est maintenant configuré pour utiliser `etat.civil@mairiedekhombole.sn` et envoyer des notifications automatiques.

## 🚀 Étapes d'Installation

### 1. Configuration SMTP
Modifiez le fichier `config.php` avec vos paramètres SMTP :

```php
// Configuration SMTP
define('SMTP_HOST', 'mail.mairiedekhombole.sn');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'etat.civil@mairiedekhombole.sn');
define('SMTP_PASSWORD', 'VOTRE_MOT_DE_PASSE'); // À définir
define('SMTP_ENCRYPTION', 'tls');
```

### 2. Installation de PHPMailer (Recommandé)
Pour un envoi d'emails plus fiable, installez PHPMailer :

```bash
# Via Composer
composer require phpmailer/phpmailer

# Ou téléchargez manuellement depuis GitHub
```

Puis décommentez les lignes dans `email_manager.php` :
```php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
```

### 3. Création des Tables de Suivi
Exécutez le script SQL pour créer les tables de suivi :

```sql
-- Exécuter le fichier suivi_emails.sql
source suivi_emails.sql;
```

### 4. Test du Système
Testez l'envoi d'emails en soumettant une demande test.

## 📨 Types d'Emails Automatiques

Le système envoie automatiquement ces emails :

### ✅ Email de Confirmation (Réception)
- **Quand** : Dès qu'une demande est soumise
- **Template** : Confirmation avec détails de la demande
- **Statut** : "En attente de traitement"

### 🔄 Email de Validation (Acceptation)
- **Quand** : Quand l'admin accepte la demande
- **Template** : Demande validée et en traitement
- **Statut** : "En cours de traitement"

### ✅ Email de Demande Prête
- **Quand** : Quand l'admin termine la demande
- **Template** : Acte prêt pour retrait
- **Statut** : "Prêt pour retrait"
- **Info** : Horaires de retrait

### ❌ Email de Rejet
- **Quand** : Quand l'admin rejette la demande
- **Template** : Demande rejetée avec motif
- **Statut** : "Rejetée"
- **Obligatoire** : Motif de rejet

## 🔧 Configuration Serveur Email

### Option 1 : Serveur SMTP Local
Si vous avez un serveur mail local :
```php
define('SMTP_HOST', 'localhost');
define('SMTP_PORT', 25);
define('SMTP_USERNAME', '');
define('SMTP_PASSWORD', '');
define('SMTP_ENCRYPTION', '');
```

### Option 2 : Gmail SMTP (Test)
Pour les tests avec Gmail :
```php
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'votre-email@gmail.com');
define('SMTP_PASSWORD', 'mot-de-passe-app');
define('SMTP_ENCRYPTION', 'tls');
```

### Option 3 : Serveur Mail Professionnel
Pour un serveur mail professionnel :
```php
define('SMTP_HOST', 'mail.mairiedekhombole.sn');
define('SMTP_PORT', 587); // ou 465 pour SSL
define('SMTP_USERNAME', 'etat.civil@mairiedekhombole.sn');
define('SMTP_PASSWORD', 'MOT_DE_PASSE_SECURISE');
define('SMTP_ENCRYPTION', 'tls'); // ou 'ssl'
```

## 📊 Suivi des Emails

### Table `suivi_emails`
Enregistre tous les emails envoyés avec :
- ID de la demande
- Destinataire
- Type d'email
- Statut d'envoi
- Date d'envoi
- Erreurs éventuelles

### Consultation des Logs
Les emails sont également loggés dans les fichiers de log :
```
Email confirmé envoyé à : user@email.com - Demande : KH-2024-001
```

## 🛡️ Sécurité

### Mot de Passe SMTP
**IMPORTANT** : Ne jamais commiter le mot de passe SMTP dans le code.

Créez un fichier `smtp_config.php` séparé :
```php
<?php
// smtp_config.php - À ne pas commiter
define('SMTP_PASSWORD', 'votre_mot_de_passe_securise');
?>
```

Puis incluez-le dans `config.php` :
```php
if (file_exists('smtp_config.php')) {
    require_once 'smtp_config.php';
}
```

## 🔍 Dépannage

### Emails non reçus
1. Vérifiez les logs d'erreur PHP
2. Testez la connexion SMTP
3. Vérifiez les paramètres du serveur mail
4. Contrôlez les filtres anti-spam

### Erreurs communes
- **Connexion refusée** : Vérifiez host/port
- **Authentification échouée** : Vérifiez username/password
- **Certificat SSL** : Vérifiez l'encryption (tls/ssl)

## 📈 Fonctionnalités Avancées

### Tracking des Emails Lus
Le système est prêt pour le tracking des emails lus (pixel de suivi).

### Templates Personnalisables
Les templates HTML sont dans la base de données et peuvent être modifiés.

### Statistiques
Une vue `vue_stats_emails` fournit des statistiques d'envoi.

## ✅ Vérification du Fonctionnement

1. **Soumettez une demande test** → Email de confirmation
2. **Acceptez la demande** → Email de validation  
3. **Terminez la demande** → Email de demande prête
4. **Rejetez une demande** → Email de rejet

Tous les emails doivent être envoyés depuis `etat.civil@mairiedekhombole.sn`.

---

**Support** : En cas de problème, vérifiez les logs PHP et la table `suivi_emails`.
