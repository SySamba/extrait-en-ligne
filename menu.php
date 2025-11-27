<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Principal - Mairie de Khombole</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Thème Sénégal - Couleurs douces avec dominance du blanc */
        :root {
            /* Couleurs Sénégal adoucies pour meilleure lisibilité */
            --senegal-vert: #2d5a3d;
            --senegal-jaune: #f4e04d;
            --senegal-rouge: #c8434e;
            --senegal-vert-fonce: #1a3d2e;
            --senegal-jaune-fonce: #d4c043;
            --senegal-rouge-fonce: #a8363f;
            
            /* Blanc dominant pour l'accessibilité */
            --blanc-principal: #ffffff;
            --blanc-casse: #fefefe;
            --gris-tres-clair: #f8f9fa;
            --gris-clair: #e9ecef;
            --texte-fonce: #1a1a1a;
            --texte-visible: #000000;
            
            /* Variables héritées */
            --primary-color: var(--senegal-vert);
            --secondary-color: var(--senegal-jaune);
            --accent-color: var(--senegal-rouge);
            --text-dark: var(--texte-visible);
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: var(--blanc-principal);
            min-height: 100vh;
            padding: 20px 0;
            color: var(--texte-visible);
        }

        h1, h2, h3, h4, h5, h6 {
            color: var(--texte-visible) !important;
            font-weight: 600;
        }
        
        p, span, div {
            color: var(--texte-visible);
        }
        
        .text-white {
            color: #ffffff !important;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }

        .header-section {
            /* Drapeau sénégalais en dégradé avec couleurs adoucies */
            background: linear-gradient(135deg, 
                var(--senegal-vert) 0%, 
                var(--senegal-vert) 33%, 
                var(--senegal-jaune) 33%, 
                var(--senegal-jaune) 66%, 
                var(--senegal-rouge) 66%, 
                var(--senegal-rouge) 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 30px 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            position: relative;
        }
        
        .header-section h1 {
            color: white !important;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            font-weight: 700;
        }
        
        .header-section p {
            color: white !important;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        
        .header-section::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 10px;
            background: var(--blanc-principal);
        }

        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 30px;
            margin-bottom: 2rem;
            flex-shrink: 0;
        }

        .logo-circle {
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
            border: 4px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .logo-circle:hover {
            transform: scale(1.05);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
        }

        .logo-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .menu-container {
            background: var(--blanc-principal);
            border: 3px solid transparent;
            border-image: linear-gradient(135deg, var(--senegal-vert), var(--senegal-jaune), var(--senegal-rouge)) 1;
            border-radius: 25px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 3rem;
            margin-bottom: 2rem;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .menu-card {
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
            border: 2px solid #e9ecef;
            border-radius: 20px;
            padding: 2.5rem;
            text-align: center;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            position: relative;
            overflow: hidden;
        }

        .menu-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .menu-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(11, 132, 62, 0.2);
            border-color: var(--primary-color);
            text-decoration: none;
            color: inherit;
        }

        .menu-card:hover::before {
            transform: scaleX(1);
        }

        .menu-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            color: white;
            transition: all 0.3s ease;
        }
        
        /* Couleurs spécifiques pour chaque icône */
        .menu-icon.demande {
            background: var(--senegal-vert);
            box-shadow: 0 10px 25px rgba(45, 90, 61, 0.3);
        }
        
        .menu-icon.recherche {
            background: var(--senegal-jaune);
            color: #000000;
            box-shadow: 0 10px 25px rgba(244, 224, 77, 0.3);
        }
        
        .menu-icon.admin {
            background: var(--senegal-rouge);
            box-shadow: 0 10px 25px rgba(200, 67, 78, 0.3);
        }
        
        .menu-icon:hover {
            transform: scale(1.1);
        }

        .menu-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 1rem;
        }

        .menu-description {
            color: #6c757d;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .stats-section {
            background: var(--senegal-vert);
            color: white;
            border-radius: 20px;
            padding: 2rem;
            margin-top: 2rem;
            text-align: center;
        }
        
        .stats-section h4 {
            color: white !important;
        }
        
        .stats-section p {
            color: white !important;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .stat-item {
            background: var(--blanc-principal);
            border: 2px solid var(--senegal-vert);
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            color: var(--senegal-vert) !important;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #000000 !important;
            font-weight: 600;
        }

        /* Styles pour la section repliable */
        #types-actes-header {
            transition: all 0.3s ease;
            border-radius: 10px;
            padding: 10px 15px;
            margin: 0 -15px;
        }

        #types-actes-header:hover {
            background: rgba(11, 132, 62, 0.1);
            transform: translateY(-2px);
        }

        #types-actes-arrow {
            transition: transform 0.3s ease;
        }

        @media (max-width: 768px) {
            .menu-container {
                padding: 2rem 1.5rem;
                margin: 0 10px;
            }
            
            .logo-container {
                flex-direction: column;
                gap: 15px;
            }
            
            .logo-circle {
                width: 80px;
                height: 80px;
            }

            .logo-img {
                width: 100%;
                height: 100%;
            }
            
            .menu-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            
            .menu-card {
                padding: 2rem 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header-section">
        <div class="container">
            <div class="logo-container">
                <div class="logo-circle">
                    <img src="logo.jpg" alt="Logo Mairie de Khombole" class="logo-img">
                </div>
                <div class="text-center">
                    <h1 class="mb-0 fw-bold display-4" style="color: #000000 !important; text-shadow: 2px 2px 4px rgba(255,255,255,0.8);">MAIRIE DE KHOMBOLE</h1>
                    <p class="mb-0 fs-4" style="color: #000000 !important; text-shadow: 1px 1px 2px rgba(255,255,255,0.8);">République du Sénégal</p>
                    <p class="mb-0 fs-6" style="color: #000000 !important; text-shadow: 1px 1px 2px rgba(255,255,255,0.8);">Services Numériques d'État Civil</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="menu-container">
            <div class="text-center">
                <h2 class="fw-bold mb-2" style="color: var(--senegal-vert) !important;">Services en Ligne</h2>
                <p class="text-muted fs-5">Accédez facilement à nos services numériques</p>
            </div>

            <div class="menu-grid">
                <!-- Faire une demande -->
                <a href="demande_acte.php" class="menu-card">
                    <div class="menu-icon demande">
                        <i class="fas fa-file-medical"></i>
                    </div>
                    <h4 class="menu-title">Faire une Demande</h4>
                    <p class="menu-description">
                        Demandez un acte d'état civil en ligne. Formulaire sécurisé et traitement rapide.
                    </p>
                </a>

                <!-- Suivre une demande -->
                <a href="suivi_demande.php" class="menu-card">
                    <div class="menu-icon recherche">
                        <i class="fas fa-search-plus"></i>
                    </div>
                    <h4 class="menu-title">Suivre une Demande</h4>
                    <p class="menu-description">
                        Consultez l'état d'avancement de votre demande avec votre numéro de référence.
                    </p>
                </a>

                <!-- Administration -->
                <a href="admin_login.php" class="menu-card">
                    <div class="menu-icon admin">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <h4 class="menu-title">Administration</h4>
                    <p class="menu-description">
                        Accès réservé aux administrateurs pour la gestion des demandes.
                    </p>
                </a>
            </div>

            <!-- Section statistiques -->
            <div class="stats-section">
                <h4 class="fw-bold mb-0" style="color: white !important; font-weight: 700 !important; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">Statistiques du Service</h4>
                <p class="mb-0" style="color: white !important; font-weight: 600 !important; text-shadow: 1px 1px 2px rgba(0,0,0,0.3);">Données en temps réel</p>
                
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-number">
                            <?php
                            // Connexion rapide pour les stats
                            try {
                                $pdo = new PDO("mysql:host=localhost;dbname=u588247422_mairebd;charset=utf8mb4", "u588247422_userbd", "Khombole2021", [
                                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                                ]);
                                $stmt = $pdo->query("SELECT COUNT(*) as total FROM demandes_actes");
                                echo $stmt->fetch()['total'] ?? '0';
                            } catch (Exception $e) {
                                echo '0';
                            }
                            ?>
                        </div>
                        <div class="stat-label">Demandes Total</div>
                    </div>
                    
                    <div class="stat-item">
                        <div class="stat-number">
                            <?php
                            try {
                                $stmt = $pdo->query("SELECT COUNT(*) as total FROM demandes_actes WHERE statut = 'en_attente'");
                                echo $stmt->fetch()['total'] ?? '0';
                            } catch (Exception $e) {
                                echo '0';
                            }
                            ?>
                        </div>
                        <div class="stat-label">En Attente</div>
                    </div>
                    
                    <div class="stat-item">
                        <div class="stat-number">
                            <?php
                            try {
                                $stmt = $pdo->query("SELECT COUNT(*) as total FROM demandes_actes WHERE statut = 'delivre'");
                                echo $stmt->fetch()['total'] ?? '0';
                            } catch (Exception $e) {
                                echo '0';
                            }
                            ?>
                        </div>
                        <div class="stat-label">Délivrés</div>
                    </div>
                    
                    <div class="stat-item">
                        <div class="stat-number">24h</div>
                        <div class="stat-label">Délai Moyen</div>
                    </div>
                </div>
            </div>

            <!-- Section Types d'Actes et Informations -->
            <div class="mt-5">
                <div class="text-center mb-4">
                    <h3 class="fw-bold" style="cursor: pointer; color: var(--senegal-vert) !important;" onclick="toggleTypesActes()" id="types-actes-header">
                        <i class="fas fa-file-alt me-2"></i>Types d'Actes et Informations
                        <i class="fas fa-chevron-down ms-2" id="types-actes-arrow"></i>
                    </h3>
                    <p class="text-muted">Découvrez les différents types d'actes disponibles et les documents requis</p>
                </div>

                <div id="types-actes-content">
                    <!-- Navigation par onglets -->
                    <div class="mb-4">
                        <ul class="nav nav-pills justify-content-center" id="actes-tabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="actes-civils-tab" data-bs-toggle="pill" data-bs-target="#actes-civils" type="button" role="tab">
                                    <i class="fas fa-file-alt me-2"></i>Actes d'État Civil
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="certificats-tab" data-bs-toggle="pill" data-bs-target="#certificats" type="button" role="tab">
                                    <i class="fas fa-certificate me-2"></i>Certificats
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="procedures-tab" data-bs-toggle="pill" data-bs-target="#procedures" type="button" role="tab">
                                    <i class="fas fa-gavel me-2"></i>Procédures Spéciales
                                </button>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content" id="actes-tabContent">
                        <!-- Onglet Actes d'État Civil -->
                        <div class="tab-pane fade show active" id="actes-civils" role="tabpanel">
                            <div class="row g-4">
                                <!-- Extrait de naissance -->
                                <div class="col-lg-6">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-header" style="background: var(--senegal-vert) !important; color: white !important;">
                                            <h6 class="mb-0">
                                                <i class="fas fa-baby me-2"></i>Extrait de naissance ou copie littérale
                                            </h6>
                                        </div>
                                        <div class="card-body" style="background: var(--senegal-vert); color: white !important;">
                                            <h6 style="color: white !important; font-weight: 600;">Documents requis :</h6>
                                            <ul class="list-unstyled" style="color: white !important;">
                                                <li style="color: white !important;"><i class="fas fa-check me-2" style="color: white !important;"></i>Votre ancien extrait ou</li>
                                                <li style="color: white !important;"><i class="fas fa-check me-2" style="color: white !important;"></i>Votre pièce d'identité</li>
                                                <li style="color: white !important;"><i class="fas fa-check me-2" style="color: white !important;"></i>Votre numéro et l'année du registre</li>
                                                <li style="color: white !important;"><i class="fas fa-check me-2" style="color: white !important;"></i>Les frais de timbre</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Célébration de mariage -->
                                <div class="col-lg-6">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-header" style="background: var(--senegal-jaune) !important; color: white !important;">
                                            <h6 class="mb-0">
                                                <i class="fas fa-heart me-2"></i>Célébration d'un mariage
                                            </h6>
                                        </div>
                                        <div class="card-body" style="background: white; color: black !important;">
                                            <h6 style="color: black !important; font-weight: 600;">Documents requis :</h6>
                                            <ul class="list-unstyled" style="color: black !important;">
                                                <li style="color: black !important;"><i class="fas fa-check text-success me-2"></i>Les extraits de naissance des conjoints</li>
                                                <li style="color: black !important;"><i class="fas fa-check text-success me-2"></i>Pièce d'identité (le cas échéant)</li>
                                                <li style="color: black !important;"><i class="fas fa-check text-success me-2"></i>4 témoins avec leurs cartes d'identité</li>
                                                <li style="color: black !important;"><i class="fas fa-check text-success me-2"></i>Cahier de quartier si le mariage n'a pas été célébré à Khombole</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Déclaration de naissance -->
                                <div class="col-lg-6">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-header" style="background: var(--senegal-rouge) !important; color: white !important;">
                                            <h6 class="mb-0">
                                                <i class="fas fa-baby-carriage me-2"></i>Déclaration de naissance
                                            </h6>
                                        </div>
                                        <div class="card-body" style="background: var(--senegal-rouge); color: white !important;">
                                            <div class="alert" style="background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.3); color: white !important;">
                                                <i class="fas fa-clock me-2" style="color: white !important;"></i>
                                                <strong style="color: white !important;">Délai :</strong> <span style="color: white !important;">Dans les 12 mois suivant la naissance</span>
                                            </div>
                                            <h6 style="color: white !important; font-weight: 600;">Documents requis :</h6>
                                            <ul class="list-unstyled" style="color: white !important;">
                                                <li style="color: white !important;"><i class="fas fa-check me-2" style="color: white !important;"></i>Certificat médical de naissance</li>
                                                <li style="color: white !important;"><i class="fas fa-check me-2" style="color: white !important;"></i>Pièces d'identité des parents</li>
                                                <li style="color: white !important;"><i class="fas fa-check me-2" style="color: white !important;"></i>Livret de famille (si existant)</li>
                                                <li style="color: white !important;"><i class="fas fa-check me-2" style="color: white !important;"></i>Cahier de quartier si l'enfant est né hors de Khombole</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Déclaration de décès -->
                                <div class="col-lg-6">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-header" style="background-color: var(--senegal-vert); color: white;">
                                            <h6 class="mb-0" style="color: white !important;">
                                                <i class="fas fa-cross me-2"></i>Déclaration de décès
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <h6 style="color: #000000 !important;">Procédure :</h6>
                                            <ul class="list-unstyled">
                                                <li><i class="fas fa-check text-success me-2"></i>Présentez-vous à la mairie</li>
                                                <li><i class="fas fa-check text-success me-2"></i>Apportez le certificat médical de décès</li>
                                                <li><i class="fas fa-check text-success me-2"></i>Pièce d'identité du défunt</li>
                                                <li><i class="fas fa-check text-success me-2"></i>Pièce d'identité du déclarant</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Onglet Certificats -->
                        <div class="tab-pane fade" id="certificats" role="tabpanel">
                            <div class="row g-4">

                                <!-- Certificats de vie -->
                                <div class="col-lg-6">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-header" style="background: var(--senegal-rouge) !important; color: white !important;">
                                            <h6 class="mb-0">
                                                <i class="fas fa-user-check me-2"></i>Certificats de vie
                                            </h6>
                                        </div>
                                        <div class="card-body" style="background: var(--senegal-rouge); color: white !important;">
                                            <div class="mb-3">
                                                <h6 style="color: white !important; font-weight: 600;">Certificat de vie individuelle :</h6>
                                                <ul class="list-unstyled small" style="color: white !important;">
                                                    <li style="color: white !important;"><i class="fas fa-check me-2" style="color: white !important;"></i>Copie de l'extrait de naissance</li>
                                                    <li style="color: white !important;"><i class="fas fa-check me-2" style="color: white !important;"></i>Payer les frais</li>
                                                </ul>
                                            </div>
                                            <div>
                                                <h6 style="color: white !important; font-weight: 600;">Certificat de vie collective :</h6>
                                                <ul class="list-unstyled small" style="color: white !important;">
                                                    <li style="color: white !important;"><i class="fas fa-check me-2" style="color: white !important;"></i>Copie des extraits de naissance des concernés</li>
                                                    <li style="color: white !important;"><i class="fas fa-check me-2" style="color: white !important;"></i>Payer les frais</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Certificat de résidence -->
                                <div class="col-lg-6">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-header" style="background: var(--senegal-jaune) !important; color: white !important;">
                                            <h6 class="mb-0">
                                                <i class="fas fa-home me-2"></i>Certificat de résidence
                                            </h6>
                                        </div>
                                        <div class="card-body" style="background: var(--senegal-jaune); color: white !important;">
                                            <h6 style="color: white !important; font-weight: 600;">Documents requis :</h6>
                                            <ul class="list-unstyled" style="color: white !important;">
                                                <li style="color: white !important;"><i class="fas fa-check me-2" style="color: white !important;"></i>Carte d'identité ou extrait de naissance</li>
                                                <li style="color: white !important;"><i class="fas fa-check me-2" style="color: white !important;"></i>Certificat de domicile</li>
                                                <li style="color: white !important;"><i class="fas fa-check me-2" style="color: white !important;"></i>Payer les frais</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Onglet Procédures Spéciales -->
                        <div class="tab-pane fade" id="procedures" role="tabpanel">
                            <!-- Certificats de non-inscription -->
                <div class="row g-4 mt-2">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header" style="background: var(--senegal-rouge) !important; color: white !important;">
                                <h6 class="mb-0">
                                    <i class="fas fa-ban me-2"></i>Certificats de non-inscription
                                </h6>
                            </div>
                            <div class="card-body" style="background: var(--senegal-rouge); color: white !important;">
                                <div class="row">
                                    <div class="col-md-4">
                                        <h6 style="color: white !important; font-weight: 600;">Non-inscription de naissance :</h6>
                                        <ul class="list-unstyled small" style="color: white !important;">
                                            <li style="color: white !important;"><i class="fas fa-check me-2" style="color: white !important;"></i>Carte d'identité des parents</li>
                                            <li style="color: white !important;"><i class="fas fa-check me-2" style="color: white !important;"></i>Filiation et date de naissance de l'enfant</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <h6 style="color: white !important; font-weight: 600;">Non-inscription de mariage :</h6>
                                        <ul class="list-unstyled small" style="color: white !important;">
                                            <li style="color: white !important;"><i class="fas fa-check me-2" style="color: white !important;"></i>Extraits de naissances des conjoints</li>
                                            <li style="color: white !important;"><i class="fas fa-check me-2" style="color: white !important;"></i>Date du mariage</li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4">
                                        <h6 style="color: white !important; font-weight: 600;">Non-divorce/non-remariage :</h6>
                                        <ul class="list-unstyled small" style="color: white !important;">
                                            <li style="color: white !important;"><i class="fas fa-check me-2" style="color: white !important;"></i>Certificat du mariage</li>
                                            <li style="color: white !important;"><i class="fas fa-check me-2" style="color: white !important;"></i>Certificat de décès d'un des conjoints</li>
                                            <li style="color: white !important;"><i class="fas fa-check me-2" style="color: white !important;"></i>Carte d'identité des deux (2) témoins</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                            
                            <!-- Horaires et procédures spéciales -->
                <div class="row g-4 mt-2">
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header" style="background: var(--senegal-vert) !important; color: white !important;">
                                <h6 class="mb-0">
                                    <i class="fas fa-clock me-2"></i>Horaires de dépôt et de retrait
                                </h6>
                            </div>
                            <div class="card-body" style="background: var(--senegal-vert); color: white !important;">
                                <div class="row">
                                    <div class="col-6">
                                        <h6 class="fw-bold" style="color: white !important;">Dépôts de documents :</h6>
                                        <ul class="list-unstyled" style="color: white !important;">
                                            <li style="color: white !important;"><i class="fas fa-sun me-2" style="color: white !important;"></i>Matin : 08h00 – 13h00</li>
                                            <li style="color: white !important;"><i class="fas fa-moon me-2" style="color: white !important;"></i>Après-midi : 15h00 – 16h00</li>
                                        </ul>
                                    </div>
                                    <div class="col-6">
                                        <h6 class="fw-bold" style="color: white !important;">Retraits de documents :</h6>
                                        <ul class="list-unstyled" style="color: white !important;">
                                            <li style="color: white !important;"><i class="fas fa-sun me-2" style="color: white !important;"></i>Matinée : 10h30 – 14h00</li>
                                            <li style="color: white !important;"><i class="fas fa-moon me-2" style="color: white !important;"></i>Après-midi : 15h00 – 17h00</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Procédures spéciales -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header" style="background: var(--senegal-jaune) !important; color: white !important;">
                                <h6 class="mb-0">
                                    <i class="fas fa-gavel me-2"></i>Procédures spéciales
                                </h6>
                            </div>
                            <div class="card-body" style="background: var(--senegal-jaune); color: white !important;">
                                <div class="mb-3">
                                    <h6 style="color: white !important; font-weight: 600;">Reconstitution d'acte :</h6>
                                    <ul class="list-unstyled small" style="color: white !important;">
                                        <li style="color: white !important;"><i class="fas fa-check me-2" style="color: white !important;"></i>Photocopie carte d'identité</li>
                                        <li style="color: white !important;"><i class="fas fa-check me-2" style="color: white !important;"></i>L'ancien acte</li>
                                        <li style="color: white !important;"><i class="fas fa-check me-2" style="color: white !important;"></i>Acte de détérioration</li>
                                        <li style="color: white !important;"><i class="fas fa-check me-2" style="color: white !important;"></i>Demande manuscrite au procureur</li>
                                    </ul>
                                </div>
                                <div>
                                    <h6 style="color: white !important; font-weight: 600;">Annulation d'acte :</h6>
                                    <ul class="list-unstyled small" style="color: white !important;">
                                        <li style="color: white !important;"><i class="fas fa-check me-2" style="color: white !important;"></i>Photocopie carte d'identité du demandeur</li>
                                        <li style="color: white !important;"><i class="fas fa-check me-2" style="color: white !important;"></i>Photocopies carte d'identité des parents</li>
                                        <li style="color: white !important;"><i class="fas fa-check me-2" style="color: white !important;"></i>L'acte concerné (type, numéro, date, lieu)</li>
                                        <li style="color: white !important;"><i class="fas fa-check me-2" style="color: white !important;"></i>Le motif d'annulation</li>
                                        <li style="color: white !important;"><i class="fas fa-check me-2" style="color: white !important;"></i>La copie de l'acte concernée</li>
                                        <li style="color: white !important;"><i class="fas fa-check me-2" style="color: white !important;"></i>2 témoins</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations de contact -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3" style="color: var(--senegal-vert) !important;">
                                <i class="fas fa-phone me-2"></i>
                                Contact
                            </h6>
                            <p class="mb-1"><strong>Téléphone :</strong> +221 33 624 52 13 63</p>
                            <p class="mb-1"><strong>Email :</strong> etat.civil@mairiedekhombole.sn</p>
                            <p class="mb-0"><strong>Horaires :</strong> Lun-Ven 8h-17h</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3" style="color: var(--senegal-vert) !important;">
                                <i class="fas fa-credit-card me-2"></i>
                                Paiement
                            </h6>
                            <p class="mb-1"><strong>WAVE :</strong> 781210618</p>
                            <p class="mb-1"><strong>Orange Money :</strong> 781210618</p>
                            <p class="mb-0"><small class="text-muted">Conservez votre référence</small></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Animation des cartes au chargement
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.menu-card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 200);
            });
        });

        // Effet de particules sur les cartes
        document.querySelectorAll('.menu-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-10px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0) scale(1)';
            });
        });

        // Fonction pour basculer l'affichage des types d'actes
        function toggleTypesActes() {
            const content = document.getElementById('types-actes-content');
            const arrow = document.getElementById('types-actes-arrow');
            
            if (content.style.display === 'none') {
                // Afficher le contenu
                content.style.display = 'block';
                arrow.classList.remove('fa-chevron-right');
                arrow.classList.add('fa-chevron-down');
                
                // Animation d'apparition
                content.style.opacity = '0';
                content.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    content.style.transition = 'all 0.5s ease';
                    content.style.opacity = '1';
                    content.style.transform = 'translateY(0)';
                }, 10);
            } else {
                // Masquer le contenu
                content.style.transition = 'all 0.3s ease';
                content.style.opacity = '0';
                content.style.transform = 'translateY(-20px)';
                arrow.classList.remove('fa-chevron-down');
                arrow.classList.add('fa-chevron-right');
                
                setTimeout(() => {
                    content.style.display = 'none';
                }, 300);
            }
        }

        // Initialiser l'état déplié par défaut
        document.addEventListener('DOMContentLoaded', function() {
            const content = document.getElementById('types-actes-content');
            const arrow = document.getElementById('types-actes-arrow');
            
            // Commencer déplié (affiché)
            content.style.display = 'block';
            content.style.opacity = '1';
            content.style.transform = 'translateY(0)';
            arrow.classList.remove('fa-chevron-right');
            arrow.classList.add('fa-chevron-down');
        });
    </script>
</body>
</html>
