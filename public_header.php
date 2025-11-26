<?php
/**
 * Header et navigation pour les pages publiques
 * Mairie de Khombole
 */

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Mairie de Khombole' ?> - Demandes d'Actes en Ligne</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Thème Sénégal - Couleurs nationales avec dominance du blanc */
        :root {
            /* Couleurs officielles du Sénégal */
            --senegal-vert: #00853f;
            --senegal-jaune: #fdef42;
            --senegal-rouge: #e31e24;
            --senegal-vert-fonce: #006b33;
            --senegal-jaune-fonce: #e6d000;
            --senegal-rouge-fonce: #c41e3a;
            
            /* Blanc dominant pour l'accessibilité */
            --blanc-principal: #ffffff;
            --blanc-casse: #fefefe;
            --gris-tres-clair: #f8f9fa;
            --gris-clair: #e9ecef;
            --texte-fonce: #212529;
            
            /* Variables héritées */
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
        
        /* Styles pour l'accessibilité */
        *:focus-visible {
            outline: 3px solid var(--senegal-jaune-fonce);
            outline-offset: 2px;
        }
        
        .btn-primary {
            background-color: var(--senegal-vert);
            border-color: var(--senegal-vert);
            color: var(--blanc-principal);
            font-weight: 600;
            border-radius: 25px;
            padding: 0.75rem 2rem;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: var(--senegal-vert-fonce);
            border-color: var(--senegal-vert-fonce);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 133, 63, 0.3);
        }
        
        .btn-success {
            background-color: var(--senegal-vert);
            border-color: var(--senegal-vert);
        }
        
        .btn-warning {
            background-color: var(--senegal-jaune-fonce);
            border-color: var(--senegal-jaune-fonce);
            color: var(--texte-fonce);
        }
        
        .btn-danger {
            background-color: var(--senegal-rouge);
            border-color: var(--senegal-rouge);
        }
        
        .form-control {
            border: 2px solid var(--gris-clair);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--senegal-vert);
            box-shadow: 0 0 0 0.2rem rgba(0, 133, 63, 0.25);
        }
        
        .alert-success {
            background-color: rgba(0, 133, 63, 0.1);
            border-color: var(--senegal-vert);
            color: var(--senegal-vert-fonce);
        }
        
        .alert-warning {
            background-color: rgba(230, 208, 0, 0.1);
            border-color: var(--senegal-jaune-fonce);
            color: #8b6914;
        }
        
        .alert-danger {
            background-color: rgba(227, 30, 36, 0.1);
            border-color: var(--senegal-rouge);
            color: var(--senegal-rouge-fonce);
        }
        
        .card {
            background-color: var(--blanc-principal);
            border: 1px solid var(--gris-clair);
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .card-header.bg-primary {
            background: linear-gradient(135deg, var(--senegal-vert), var(--senegal-vert-fonce)) !important;
            color: var(--blanc-principal);
        }
        
        .card-header.bg-success {
            background: linear-gradient(135deg, var(--senegal-vert), var(--senegal-vert-fonce)) !important;
            color: var(--blanc-principal);
        }
        
        .card-header.bg-warning {
            background: linear-gradient(135deg, var(--senegal-jaune), var(--senegal-jaune-fonce)) !important;
            color: var(--texte-fonce);
        }
        
        .card-header.bg-danger {
            background: linear-gradient(135deg, var(--senegal-rouge), var(--senegal-rouge-fonce)) !important;
            color: var(--blanc-principal);
        }

        .public-navbar {
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
            font-size: 1.3rem;
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

        .btn-admin-access {
            background-color: var(--secondary-color);
            color: var(--text-dark);
            border: none;
            font-weight: 600;
            padding: 0.5rem 1.2rem;
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .btn-admin-access:hover {
            background-color: #f1e635;
            color: var(--text-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
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

        .hero-section {
            background: linear-gradient(135deg, rgba(11, 132, 62, 0.9), rgba(30, 58, 138, 0.9));
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
        }

        @media (max-width: 768px) {
            .navbar-nav {
                margin-top: 1rem;
            }
            
            .btn-admin-access {
                margin-top: 0.5rem;
                width: 100%;
            }
        }
    </style>
    
    <?php if (isset($additionalCSS)): ?>
        <?= $additionalCSS ?>
    <?php endif; ?>
</head>
<body>
    <!-- Navigation Publique -->
    <nav class="navbar navbar-expand-lg navbar-dark public-navbar fixed-top">
        <div class="container">
            <!-- Logo et titre -->
            <a class="navbar-brand d-flex align-items-center" href="menu.php">
                <img src="logo.jpg" alt="Logo Mairie" width="45" height="45" class="rounded-circle me-3">
                <div>
                    <div style="font-size: 1.1rem; line-height: 1.2;">Mairie de Khombole</div>
                    <small style="font-size: 0.75rem; opacity: 0.9;">Demandes d'Actes en Ligne</small>
                </div>
            </a>

            <!-- Toggle pour mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#publicNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menu principal -->
            <div class="collapse navbar-collapse" id="publicNavbar">
                <ul class="navbar-nav me-auto ms-4">
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'menu.php' ? 'active' : '' ?>" 
                           href="menu.php">
                            <i class="fas fa-home me-1"></i>Accueil
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'demande_acte.php' ? 'active' : '' ?>" 
                           href="demande_acte.php">
                            <i class="fas fa-file-alt me-1"></i>Nouvelle Demande
                        </a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link <?= $currentPage === 'suivi_demande.php' ? 'active' : '' ?>" 
                           href="suivi_demande.php">
                            <i class="fas fa-search me-1"></i>Suivi Demande
                        </a>
                    </li>

                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="servicesDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-info-circle me-1"></i>Informations
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="menu.php#tarifs">
                                <i class="fas fa-euro-sign me-2"></i>Tarifs
                            </a></li>
                            <li><a class="dropdown-item" href="menu.php#delais">
                                <i class="fas fa-clock me-2"></i>Délais
                            </a></li>
                            <li><a class="dropdown-item" href="menu.php#documents">
                                <i class="fas fa-folder me-2"></i>Documents requis
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="menu.php#contact">
                                <i class="fas fa-phone me-2"></i>Contact
                            </a></li>
                        </ul>
                    </li>
                </ul>

                <!-- Bouton accès admin -->
                <div class="d-flex">
                    <a href="admin_login.php" class="btn btn-admin-access">
                        <i class="fas fa-user-shield me-1"></i>
                        Espace Admin
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Fil d'Ariane -->
    <?php if (isset($breadcrumbs) && !empty($breadcrumbs)): ?>
    <div class="breadcrumb-nav">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="menu.php"><i class="fas fa-home me-1"></i>Accueil</a>
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

    <!-- Section Hero (optionnelle) -->
    <?php if (isset($showHero) && $showHero): ?>
    <div class="hero-section">
        <div class="container text-center">
            <h1 class="display-5 fw-bold mb-3">
                <?= $heroTitle ?? 'Bienvenue sur le portail de la Mairie de Khombole' ?>
            </h1>
            <p class="lead">
                <?= $heroSubtitle ?? 'Effectuez vos demandes d\'actes d\'état civil en ligne, rapidement et en toute sécurité.' ?>
            </p>
            <?php if (isset($heroButton)): ?>
                <a href="<?= $heroButton['url'] ?>" class="btn btn-light btn-lg mt-3">
                    <i class="<?= $heroButton['icon'] ?> me-2"></i>
                    <?= $heroButton['text'] ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Contenu principal -->
    <div class="main-content">
        <div class="container">
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
