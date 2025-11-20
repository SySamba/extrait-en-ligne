# Système de Demande d'Actes d'État Civil - Mairie de Khombole

## Description

Application web professionnelle permettant aux citoyens de faire des demandes d'actes d'état civil en ligne auprès de la Mairie de Khombole. Le système comprend un formulaire moderne, un système de suivi et une interface d'administration.

## Fonctionnalités

### Pour les Citoyens
- ✅ Formulaire de demande d'acte moderne et responsive
- ✅ Types d'actes supportés :
  - Extrait d'acte de naissance
  - Copie littérale d'acte de naissance
  - Extrait d'acte de mariage
  - Certificat de résidence
  - Certificat de vie individuelle
  - Certificat de vie collective
  - Certificat de décès
- ✅ Modes de paiement : WAVE et Orange Money
- ✅ Modes de délivrance : Retrait physique ou envoi électronique
- ✅ Système de suivi en temps réel
- ✅ Notifications par email
- ✅ Interface responsive (mobile-friendly)

### Sécurité et Conformité
- ✅ Protection des données personnelles (Loi n°2008-12)
- ✅ Consentement explicite RGPD
- ✅ Validation côté client et serveur
- ✅ Protection CSRF
- ✅ Logging des activités
- ✅ Gestion sécurisée des sessions

## Installation

### Prérequis
- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Serveur web (Apache/Nginx)
- Extension PHP : PDO, PDO_MySQL

### Étapes d'installation

1. **Cloner/Télécharger les fichiers**
   ```bash
   # Placer les fichiers dans le dossier web
   # Ex: C:\xampp\htdocs\mairie-khombole\
   ```

2. **Créer la base de données**
   ```sql
   -- Importer le fichier database.sql dans phpMyAdmin
   -- ou exécuter via ligne de commande :
   mysql -u root -p < database.sql
   ```

3. **Configuration**
   - Vérifier les paramètres dans `config.php`
   - Modifier si nécessaire :
     - Identifiants de base de données
     - Numéros de paiement WAVE/Orange Money
     - Configuration email

4. **Permissions**
   ```bash
   # Donner les permissions d'écriture aux dossiers
   chmod 755 uploads/
   chmod 755 logs/
   ```

5. **Test de l'installation**
   - Accéder à : `http://localhost/mairie-khombole/demande_acte.php`
   - Tester une demande complète

## Structure des Fichiers

```
mairie-khombole/
├── demande_acte.php          # Formulaire principal
├── traiter_demande.php       # Traitement des demandes
├── confirmation_demande.php  # Page de confirmation
├── suivi_demande.php         # Suivi des demandes
├── config.php                # Configuration
├── database.sql              # Structure de la base de données
├── README.md                 # Documentation
├── logo.jpg                  # Logo de la mairie
├── maire-khombole.HEIC       # Image de la mairie
├── uploads/                  # Dossier pour les fichiers uploadés
└── logs/                     # Logs de l'application
```

## Base de Données

### Tables principales

#### `demandes_actes`
Stocke toutes les demandes d'actes avec :
- Informations du demandeur
- Type d'acte demandé
- Statut de traitement
- Modes de paiement et délivrance
- Consentements RGPD

#### `historique_demandes`
Trace toutes les actions sur les demandes :
- Changements de statut
- Actions administratives
- Horodatage des événements

## Utilisation

### Pour les Citoyens

1. **Faire une demande**
   - Aller sur `demande_acte.php`
   - Remplir le formulaire complet
   - Accepter les conditions
   - Soumettre la demande

2. **Suivre une demande**
   - Aller sur `suivi_demande.php`
   - Saisir le numéro de demande
   - Consulter le statut et l'historique

### Workflow de Traitement

1. **Soumission** → Demande enregistrée (statut: `en_attente`)
2. **Examen** → Vérification des informations (statut: `en_traitement`)
3. **Préparation** → Génération du document (statut: `pret`)
4. **Délivrance** → Remise/Envoi (statut: `delivre`)

## Sécurité

### Mesures Implémentées
- Validation stricte des données
- Protection contre les injections SQL (PDO)
- Protection XSS (htmlspecialchars)
- Tokens CSRF
- Sessions sécurisées
- Logging des activités
- Gestion des erreurs

### Données Personnelles
- Collecte minimale nécessaire
- Consentement explicite
- Droit d'accès et de rectification
- Conservation sécurisée
- Non-partage avec des tiers

## Configuration des Paiements

### WAVE
- Numéro : 781210618
- Les utilisateurs effectuent le paiement
- Conservent la référence de transaction

### Orange Money
- Numéro : 781210618
- Même processus que WAVE

## Personnalisation

### Couleurs du Thème
```css
:root {
    --primary-color: #0b843e;    /* Vert Sénégal */
    --secondary-color: #f4e93d;   /* Jaune Sénégal */
    --accent-color: #1e3a8a;      /* Bleu */
}
```

### Tarifs
Modifiables dans `config.php` :
```php
define('TARIF_EXTRAIT_NAISSANCE', 500);
define('TARIF_COPIE_LITTERALE', 1000);
// etc...
```

## Support et Maintenance

### Logs
- Fichiers de log dans `/logs/`
- Format : `app_YYYY-MM-DD.log`
- Niveaux : INFO, WARNING, ERROR, CRITICAL

### Sauvegarde
Sauvegarder régulièrement :
- Base de données MySQL
- Dossier `/uploads/`
- Fichiers de configuration

### Mise à jour
1. Sauvegarder les données
2. Remplacer les fichiers PHP
3. Exécuter les migrations DB si nécessaire
4. Tester le fonctionnement

## Contact Technique

Pour le support technique de cette application :
- Développeur : SySamba
- Email : sambasy837@gmail.com

## Licence

Application développée pour la Mairie de Khombole.
Tous droits réservés.

---

**Version :** 1.0.0  
**Date :** Novembre 2024  
**Référence :** KH-TRA-11-00
