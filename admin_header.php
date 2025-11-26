<?php
/**
 * Header et navigation pour les pages d'administration
 * Mairie de Khombole
 */

// Vérifier que l'admin est connecté
if (!function_exists('estConnecte') || !estConnecte()) {
    header('Location: admin_login.php');
    exit;
}

// Obtenir les informations de l'admin connecté
$adminInfo = getAdminInfo();
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Administration' ?> - Mairie de Khombole</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Thème Sénégal -->
    <link href="assets/css/senegal-theme.css" rel="stylesheet">
    
    <style>
        /* Variables héritées du thème Sénégal */
        :root {
            --primary-color: var(--senegal-vert);
            --secondary-color: var(--senegal-jaune);
            --accent-color: var(--senegal-rouge);
            --text-dark: var(--texte-fonce);
            --bg-light: var(--blanc-principal);
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: var(--blanc-principal);
            padding-top: 76px; /* Hauteur de la navbar fixe */
            color: var(--texte-fonce);
        }

        .admin-navbar {
            /* Drapeau sénégalais en dégradé */
            background: linear-gradient(135deg, 
                var(--senegal-vert) 0%, 
                var(--senegal-vert) 33%, 
                var(--senegal-jaune) 33%, 
                var(--senegal-jaune) 66%, 
                var(--senegal-rouge) 66%, 
                var(--senegal-rouge) 100%);
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            padding: 0.5rem 0;
            border-bottom: 3px solid var(--blanc-principal);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.2rem;
        }

        .navbar-nav .nav-link {
            color: rgba(255,255,255,0.9) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            border-radius: 8px;
            margin: 0 0.2rem;
            transition: all 0.3s ease;
        }

        .navbar-nav .nav-link:hover {
            background-color: rgba(255,255,255,0.1);
            color: white !important;
            transform: translateY(-1px);
        }

        .navbar-nav .nav-link.active {
            background-color: rgba(255,255,255,0.2);
            color: white !important;
            font-weight: 600;
        }

        .dropdown-menu {
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-radius: 10px;
        }

        .dropdown-item {
            padding: 0.7rem 1.2rem;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .user-info {
            background-color: rgba(255,255,255,0.1);
            border-radius: 20px;
            padding: 0.3rem 1rem;
            margin-left: 1rem;
        }

        .breadcrumb-nav {
            background-color: white;
            padding: 1rem 0;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #e9ecef;
        }

        .breadcrumb {
            background: none;
            margin-bottom: 0;
        }

        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .breadcrumb-item a:hover {
            text-decoration: underline;
        }

        .main-content {
            min-height: calc(100vh - 76px);
            padding-bottom: 2rem;
        }

        @media (max-width: 768px) {
            .user-info {
                margin-left: 0;
                margin-top: 0.5rem;
            }
            
            .navbar-nav {
                margin-top: 1rem;
            }
        }
    </style>
    
    <?php if (isset($additionalCSS)): ?>
        <?= $additionalCSS ?>
    <?php endif; ?>
</head>
<body>
    <!-- Navigation Admin -->
    <nav class="navbar navbar-expand-lg navbar-dark admin-navbar fixed-top">
        <div class="container-fluid">
            <!-- Logo et titre -->
            <a class="navbar-brand d-flex align-items-center" href="liste_demandes.php">
                <img src="logo.jpg" alt="Logo" width="40" height="40" class="rounded-circle me-2">
                <span>Admin Mairie</span>
            </a>

            <!-- Toggle pour mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu principal -->
            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'liste_demandes.php' ? 'active' : '' ?>" 
                           href="liste_demandes.php">
                            <i class="fas fa-list me-1"></i>Demandes
                        </a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?= in_array($currentPage, ['admin_traiter_demande.php', 'detail_demande.php']) ? 'active' : '' ?>" 
                           href="#" id="traitementDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-tasks me-1"></i>Traitement
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="liste_demandes.php?statut=en_attente">
                                <i class="fas fa-clock me-2"></i>En attente
                            </a></li>
                            <li><a class="dropdown-item" href="liste_demandes.php?statut=en_traitement">
                                <i class="fas fa-cog me-2"></i>En traitement
                            </a></li>
                            <li><a class="dropdown-item" href="liste_demandes.php?statut=pret">
                                <i class="fas fa-check-circle me-2"></i>Prêtes
                            </a></li>
                        </ul>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-cogs me-1"></i>Administration
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="create_admin.php">
                                <i class="fas fa-user-plus me-2"></i>Nouvel admin
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="liste_demandes.php?export=excel">
                                <i class="fas fa-file-excel me-2"></i>Export Excel
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="showStats()">
                                <i class="fas fa-chart-bar me-2"></i>Statistiques
                            </a></li>
                        </ul>
                    </li>
                </ul>

                <!-- Informations utilisateur -->
                <div class="d-flex align-items-center">
                    <div class="user-info text-white">
                        <i class="fas fa-user-circle me-2"></i>
                        <span class="d-none d-md-inline"><?= htmlspecialchars($adminInfo['email'] ?? 'Admin') ?></span>
                    </div>
                    
                    <div class="dropdown ms-2">
                        <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-cog"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="menu.php">
                                <i class="fas fa-home me-2"></i>Accueil public
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="admin_logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Fil d'Ariane -->
    <?php if (isset($breadcrumbs) && !empty($breadcrumbs)): ?>
    <div class="breadcrumb-nav">
        <div class="container-fluid">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="liste_demandes.php"><i class="fas fa-home me-1"></i>Accueil Admin</a>
                    </li>
                    <?php foreach ($breadcrumbs as $crumb): ?>
                        <?php if (isset($crumb['url'])): ?>
                            <li class="breadcrumb-item">
                                <a href="<?= htmlspecialchars($crumb['url']) ?>">
                                    <?= htmlspecialchars($crumb['title']) ?>
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="breadcrumb-item active" aria-current="page">
                                <?= htmlspecialchars($crumb['title']) ?>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ol>
            </nav>
        </div>
    </div>
    <?php endif; ?>

    <!-- Contenu principal -->
    <div class="main-content">
        <div class="container-fluid">
            <!-- Messages globaux -->
            <div id="globalMessages"></div>
            
            <?php
            // Afficher les messages de session s'ils existent
            if (isset($_SESSION['success_message'])) {
                echo '<div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i>' . 
                        htmlspecialchars($_SESSION['success_message']) . 
                        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                      </div>';
                unset($_SESSION['success_message']);
            }
            
            if (isset($_SESSION['error_message'])) {
                echo '<div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-triangle me-2"></i>' . 
                        htmlspecialchars($_SESSION['error_message']) . 
                        '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                      </div>';
                unset($_SESSION['error_message']);
            }
            ?>

    <!-- Le contenu de la page sera inséré ici -->
