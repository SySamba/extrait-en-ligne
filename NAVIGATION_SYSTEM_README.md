# Système de Navigation Centralisé - Mairie de Khombole

## Vue d'ensemble

Le système de navigation centralisé élimine le besoin d'utiliser les flèches de retour du navigateur en fournissant une navigation cohérente et intuitive sur toutes les pages de l'application.

## Fichiers du système

### Fichiers de navigation
- `admin_header.php` - Header et navigation pour les pages d'administration
- `admin_footer.php` - Footer pour les pages d'administration
- `public_header.php` - Header et navigation pour les pages publiques
- `public_footer.php` - Footer pour les pages publiques

### Exemples d'implémentation
- `exemple_liste_demandes_avec_navigation.php` - Page admin avec navigation
- `exemple_suivi_avec_navigation.php` - Page publique avec navigation

## Utilisation pour les pages ADMIN

### Structure de base
```php
<?php
// Configuration de la page
$pageTitle = 'Titre de la page';
$breadcrumbs = [
    ['title' => 'Section', 'url' => 'lien.php'],
    ['title' => 'Page actuelle'] // Pas d'URL pour la page actuelle
];

// CSS/JS additionnels (optionnel)
$additionalCSS = '<style>/* CSS personnalisé */</style>';
$additionalJS = '<script>/* JS personnalisé */</script>';

// Inclure le header
require_once 'admin_header.php';
?>

<!-- Votre contenu ici -->
<h1>Contenu de la page</h1>

<?php
// Inclure le footer
require_once 'admin_footer.php';
?>
```

### Variables disponibles pour les pages admin

#### Variables obligatoires
- `$pageTitle` - Titre de la page (affiché dans l'onglet)

#### Variables optionnelles
- `$breadcrumbs` - Fil d'Ariane (tableau d'éléments)
- `$additionalCSS` - CSS personnalisé
- `$additionalJS` - JavaScript personnalisé
- `$autoRefresh` - Actualisation automatique (true/false)

#### Exemple complet
```php
<?php
require_once 'session_manager.php';
verifierConnexionAdmin();

$pageTitle = 'Gestion des Demandes';
$breadcrumbs = [
    ['title' => 'Administration', 'url' => 'liste_demandes.php'],
    ['title' => 'Demandes', 'url' => 'liste_demandes.php'],
    ['title' => 'Traitement']
];

$additionalCSS = '
<style>
.custom-table { border-radius: 10px; }
</style>';

require_once 'admin_header.php';
?>

<div class="row">
    <div class="col-12">
        <h2>Ma page d'administration</h2>
        <!-- Contenu -->
    </div>
</div>

<?php require_once 'admin_footer.php'; ?>
```

## Utilisation pour les pages PUBLIQUES

### Structure de base
```php
<?php
// Configuration de la page
$pageTitle = 'Titre de la page';
$breadcrumbs = [
    ['title' => 'Services', 'url' => 'menu.php#services'],
    ['title' => 'Page actuelle']
];

// Section hero (optionnel)
$showHero = true;
$heroTitle = 'Titre principal';
$heroSubtitle = 'Sous-titre descriptif';
$heroButton = [
    'text' => 'Action principale',
    'url' => 'lien.php',
    'icon' => 'fas fa-arrow-right'
];

// Inclure le header
require_once 'public_header.php';
?>

<!-- Votre contenu ici -->
<h1>Contenu de la page</h1>

<?php
// Inclure le footer
require_once 'public_footer.php';
?>
```

### Variables disponibles pour les pages publiques

#### Variables obligatoires
- `$pageTitle` - Titre de la page

#### Variables optionnelles
- `$breadcrumbs` - Fil d'Ariane
- `$showHero` - Afficher la section hero (true/false)
- `$heroTitle` - Titre de la section hero
- `$heroSubtitle` - Sous-titre de la section hero
- `$heroButton` - Bouton d'action principal
- `$additionalCSS` - CSS personnalisé
- `$additionalJS` - JavaScript personnalisé

## Fonctionnalités incluses

### Navigation admin
- **Menu principal** avec liens vers toutes les sections
- **Dropdown de traitement** avec filtres rapides
- **Menu administration** avec outils avancés
- **Informations utilisateur** avec profil et déconnexion
- **Raccourcis clavier** (Ctrl+H pour accueil, Ctrl+L pour déconnexion)

### Navigation publique
- **Menu principal** avec services principaux
- **Dropdown informations** avec liens utiles
- **Bouton accès admin** toujours visible
- **Section hero** configurable
- **Raccourcis clavier** (Ctrl+H, Ctrl+N, Ctrl+S)

### Fonctionnalités communes
- **Fil d'Ariane** automatique
- **Messages flash** avec auto-masquage
- **Indicateurs de chargement** sur les formulaires
- **Validation en temps réel** des formulaires
- **Design responsive** pour mobile
- **Confirmation** pour actions dangereuses

## Personnalisation

### Couleurs CSS
```css
:root {
    --primary-color: #0b843e;    /* Vert principal */
    --secondary-color: #f4e93d;  /* Jaune secondaire */
    --accent-color: #1e3a8a;     /* Bleu accent */
    --text-dark: #2c3e50;        /* Texte sombre */
    --bg-light: #f8f9fa;         /* Arrière-plan clair */
}
```

### Ajout de liens de navigation

#### Menu admin
Modifier `admin_header.php` dans la section `<ul class="navbar-nav me-auto">` :

```php
<li class="nav-item">
    <a class="nav-link <?= $currentPage === 'ma_page.php' ? 'active' : '' ?>" 
       href="ma_page.php">
        <i class="fas fa-icon me-1"></i>Mon lien
    </a>
</li>
```

#### Menu public
Modifier `public_header.php` dans la section `<ul class="navbar-nav me-auto ms-4">` :

```php
<li class="nav-item">
    <a class="nav-link <?= $currentPage === 'ma_page.php' ? 'active' : '' ?>" 
       href="ma_page.php">
        <i class="fas fa-icon me-1"></i>Mon lien
    </a>
</li>
```

## Messages flash

### Définir un message
```php
// Message de succès
$_SESSION['success_message'] = 'Opération réussie !';

// Message d'erreur
$_SESSION['error_message'] = 'Une erreur est survenue.';

// Redirection
header('Location: ma_page.php');
exit;
```

### Affichage automatique
Les messages sont automatiquement affichés et supprimés par les headers.

## Raccourcis clavier

### Pages admin
- **Ctrl+H** : Retour à l'accueil admin
- **Ctrl+L** : Déconnexion (avec confirmation)

### Pages publiques
- **Ctrl+H** : Retour à l'accueil
- **Ctrl+N** : Nouvelle demande
- **Ctrl+S** : Suivi de demande

## Responsive Design

Le système est entièrement responsive :
- **Desktop** : Navigation horizontale complète
- **Tablet** : Navigation adaptée avec menus déroulants
- **Mobile** : Menu hamburger avec navigation verticale

## Intégration avec l'existant

### Migration d'une page existante

1. **Sauvegarder** votre page actuelle
2. **Extraire** le contenu principal (entre `<body>` et `</body>`)
3. **Configurer** les variables de navigation
4. **Inclure** les headers/footers
5. **Tester** la navigation

### Exemple de migration
```php
// AVANT
<!DOCTYPE html>
<html>
<head><title>Ma page</title></head>
<body>
    <h1>Contenu</h1>
</body>
</html>

// APRÈS
<?php
$pageTitle = 'Ma page';
require_once 'public_header.php';
?>

<h1>Contenu</h1>

<?php require_once 'public_footer.php'; ?>
```

## Avantages du système

### ✅ Navigation intuitive
- Menus cohérents sur toutes les pages
- Liens contextuels selon la section
- Fil d'Ariane automatique

### ✅ Expérience utilisateur améliorée
- Plus besoin des flèches du navigateur
- Navigation rapide entre les sections
- Raccourcis clavier pour les power users

### ✅ Maintenance simplifiée
- Un seul endroit pour modifier la navigation
- Cohérence garantie sur toutes les pages
- Ajout facile de nouveaux liens

### ✅ Design moderne
- Interface responsive
- Animations et transitions fluides
- Messages flash élégants

## Dépannage

### Navigation ne s'affiche pas
- Vérifiez l'inclusion des fichiers header/footer
- Vérifiez les chemins des fichiers CSS/JS
- Consultez la console du navigateur

### Liens inactifs
- Vérifiez la variable `$currentPage`
- Vérifiez les URLs dans les menus
- Testez les permissions de fichiers

### Styles cassés
- Vérifiez l'inclusion de Bootstrap 5
- Vérifiez les chemins des ressources
- Testez avec les outils de développement

## Support

Pour toute question sur le système de navigation :
1. Consultez les exemples fournis
2. Testez avec les pages d'exemple
3. Vérifiez la console du navigateur
4. Contactez l'équipe de développement

## Évolutions futures

- Système de permissions pour les menus
- Navigation par onglets pour les sections complexes
- Historique de navigation personnalisé
- Favoris et raccourcis personnalisables
