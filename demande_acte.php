<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demande d'Acte d'État Civil - Mairie de Khombole</title>
    
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
            --border-color: var(--gris-clair);
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: var(--blanc-principal);
            min-height: 100vh;
            padding: 20px 0;
            color: var(--texte-fonce);
        }

        .header-section {
            /* Drapeau sénégalais en dégradé */
            background: linear-gradient(135deg, 
                var(--senegal-vert) 0%, 
                var(--senegal-vert) 33%, 
                var(--senegal-jaune) 33%, 
                var(--senegal-jaune) 66%, 
                var(--senegal-rouge) 66%, 
                var(--senegal-rouge) 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 30px 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            position: relative;
        }
        
        .header-section::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: var(--blanc-principal);
        }

        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 20px;
            margin-bottom: 1rem;
            flex-shrink: 0;
        }

        .logo-circle {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
            border: 3px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .logo-circle:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }

        .logo-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .form-container {
            background: var(--blanc-principal);
            border: 3px solid transparent;
            border-image: linear-gradient(135deg, var(--senegal-vert), var(--senegal-jaune), var(--senegal-rouge)) 1;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            padding: 2.5rem;
            margin-bottom: 2rem;
        }

        .section-header {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }

        .form-control, .form-select {
            border: 2px solid var(--border-color);
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(11, 132, 62, 0.25);
        }

        .required-field::after {
            content: " *";
            color: #dc3545;
            font-weight: bold;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border: none;
            border-radius: 25px;
            padding: 1rem 2rem;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(11, 132, 62, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(11, 132, 62, 0.4);
        }

        .info-box {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            border-left: 5px solid var(--accent-color);
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .payment-info {
            background: linear-gradient(135deg, #f8f9ff, #e8f4fd);
            border: 2px solid var(--primary-color);
            border-radius: 15px;
            padding: 2rem;
            margin-top: 1rem;
            box-shadow: 0 5px 15px rgba(11, 132, 62, 0.1);
        }

        .payment-method {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 1rem;
            background: white;
            border-radius: 10px;
            margin: 0.5rem 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }

        .payment-method:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .payment-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            font-size: 0.9rem;
        }

        .wave-icon {
            background: linear-gradient(135deg, #ff6b35, #f7931e);
        }

        .orange-icon {
            background: linear-gradient(135deg, #ff8c00, #ffa500);
        }

        .checkbox-container {
            background: var(--bg-light);
            border-radius: 10px;
            padding: 1.5rem;
            margin: 1rem 0;
            border: 2px solid var(--border-color);
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .progress-indicator {
            background: white;
            border-radius: 15px;
            padding: 1rem;
            margin-bottom: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .step {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0.5rem;
        }

        .step-number {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .form-container {
                padding: 1.5rem;
                margin: 0 10px;
            }
            
            .logo-container {
                flex-direction: column;
                gap: 10px;
            }
            
            .logo-img {
                width: 60px;
                height: 60px;
            }
        }

        .floating-label {
            position: relative;
        }

        .floating-label input:focus + label,
        .floating-label input:not(:placeholder-shown) + label {
            transform: translateY(-25px) scale(0.8);
            color: var(--primary-color);
        }

        .floating-label label {
            position: absolute;
            top: 12px;
            left: 12px;
            transition: all 0.3s ease;
            pointer-events: none;
            background: white;
            padding: 0 5px;
        }
        /* Menu de navigation simple */
        .simple-nav {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
            margin-bottom: 2rem;
        }

        .nav-links {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .nav-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .nav-link:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(11, 132, 62, 0.3);
        }

        .nav-link.active {
            background: var(--primary-color);
            color: white;
        }

        @media (max-width: 768px) {
            .nav-links {
                gap: 1rem;
            }
            
            .nav-link {
                padding: 0.4rem 1rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <!-- Menu de navigation simple -->
    <nav class="simple-nav">
        <div class="container">
            <div class="nav-links">
                <a href="menu.php" class="nav-link">
                    <i class="fas fa-home me-1"></i>Accueil
                </a>
                <a href="demande_acte.php" class="nav-link active">
                    <i class="fas fa-plus me-1"></i>Nouvelle Demande
                </a>
                <a href="suivi_demande.php" class="nav-link">
                    <i class="fas fa-search me-1"></i>Suivi Demande
                </a>
            </div>
        </div>
    </nav>

    <!-- Messages d'erreur -->
    <?php if (isset($_SESSION['demande_error'])): ?>
        <div class="container mt-3">
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Erreur :</strong> <?= htmlspecialchars($_SESSION['demande_error']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
        <?php unset($_SESSION['demande_error']); ?>
    <?php endif; ?>

    <!-- Header Section -->
    <div class="header-section">
        <div class="container">
            <div class="logo-container">
                <div class="logo-circle">
                    <img src="logo.jpg" alt="Logo Mairie de Khombole" class="logo-img">
                </div>
                <div class="text-center">
                    <h1 class="mb-0 fw-bold">MAIRIE DE KHOMBOLE</h1>
                    <p class="mb-0 fs-5">République du Sénégal</p>
                </div>
            </div>
            <div class="text-center">
                <h2 class="fw-bold">FORMULAIRE DE DEMANDE D'ACTE D'ÉTAT CIVIL</h2>
                <p class="mb-0 fs-6">Référence: KH-TRA-11-00</p>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Information Box -->
        <div class="info-box">
            <div class="d-flex align-items-start gap-3">
                <i class="fas fa-info-circle fs-4 text-primary mt-1"></i>
                <div>
                    <h5 class="fw-bold text-primary mb-2">Informations importantes</h5>
                    <p class="mb-2">Ce formulaire permet de faire une demande d'acte d'état civil auprès de la Mairie de Khombole.</p>
                    <p class="mb-2">Les informations recueillies sont nécessaires au traitement de votre demande et sont protégées conformément à la <strong>Loi n°2008-12 du 25 janvier 2008</strong> relative à la protection des données personnelles.</p>
                    <p class="mb-0"><strong>Veuillez remplir tous les champs obligatoires avant de soumettre votre demande.</strong></p>
                </div>
            </div>
        </div>

        <!-- Progress Indicator -->
        <div class="progress-indicator">
            <div class="row">
                <div class="col-md-4">
                    <div class="step">
                        <div class="step-number">1</div>
                        <span class="fw-semibold">Type d'acte</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="step">
                        <div class="step-number">2</div>
                        <span class="fw-semibold">Informations personnelles</span>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="step">
                        <div class="step-number">3</div>
                        <span class="fw-semibold">Validation</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Form -->
        <form method="POST" action="traiter_demande.php" class="needs-validation" novalidate>
            <div class="form-container">
                <!-- Section 1: Type d'acte -->
                <div class="section-header">
                    <i class="fas fa-file-alt"></i>
                    <h4 class="mb-0">1. Type d'acte demandé</h4>
                </div>

                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label required-field">Types d'actes demandés</label>
                        <p class="text-muted small mb-3">
                            <i class="fas fa-info-circle me-1"></i>
                            Vous pouvez sélectionner plusieurs types d'actes dans une même demande
                        </p>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="extrait_naissance" name="types_actes[]" value="extrait_naissance">
                                    <label class="form-check-label" for="extrait_naissance">
                                        <strong>Extrait d'acte de naissance</strong>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="copie_litterale_naissance" name="types_actes[]" value="copie_litterale_naissance">
                                    <label class="form-check-label" for="copie_litterale_naissance">
                                        <strong>Copie littérale d'acte de naissance</strong>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="extrait_mariage" name="types_actes[]" value="extrait_mariage">
                                    <label class="form-check-label" for="extrait_mariage">
                                        <strong>Extrait d'acte de mariage</strong>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="certificat_residence" name="types_actes[]" value="certificat_residence">
                                    <label class="form-check-label" for="certificat_residence">
                                        <strong>Certificat de résidence</strong>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="certificat_vie_individuelle" name="types_actes[]" value="certificat_vie_individuelle">
                                    <label class="form-check-label" for="certificat_vie_individuelle">
                                        <strong>Certificat de vie individuelle</strong>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="certificat_vie_collective" name="types_actes[]" value="certificat_vie_collective">
                                    <label class="form-check-label" for="certificat_vie_collective">
                                        <strong>Certificat de vie collective</strong>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="certificat_deces" name="types_actes[]" value="certificat_deces">
                                    <label class="form-check-label" for="certificat_deces">
                                        <strong>Certificat de décès</strong>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="invalid-feedback d-block" id="types_actes_error" style="display: none !important;">
                            Veuillez sélectionner au moins un type d'acte.
                        </div>
                        
                        <!-- Section nombre d'exemplaires par type -->
                        <div class="mt-4 p-3 bg-light rounded" id="exemplaires_section" style="display: none;">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="fas fa-copy me-2"></i>Nombre d'exemplaires par type d'acte
                            </h6>
                            <div id="exemplaires_container" class="row g-3">
                                <!-- Les champs seront ajoutés dynamiquement ici -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-container">
                <!-- Section 2: Informations du demandeur -->
                <div class="section-header">
                    <i class="fas fa-user"></i>
                    <h4 class="mb-0">2. Informations du demandeur</h4>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="nom" class="form-label required-field">Nom</label>
                        <input type="text" class="form-control" id="nom" name="nom" required>
                        <div class="invalid-feedback">
                            Veuillez saisir votre nom.
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="prenoms" class="form-label required-field">Prénom(s)</label>
                        <input type="text" class="form-control" id="prenoms" name="prenoms" required>
                        <div class="invalid-feedback">
                            Veuillez saisir vos prénoms.
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="date_naissance" class="form-label required-field">Date de naissance</label>
                        <input type="date" class="form-control" id="date_naissance" name="date_naissance" required>
                        <div class="invalid-feedback">
                            Veuillez saisir votre date de naissance.
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="lieu_naissance" class="form-label required-field">Lieu de naissance</label>
                        <input type="text" class="form-control" id="lieu_naissance" name="lieu_naissance" value="Khombole" required>
                        <div class="invalid-feedback">
                            Veuillez saisir votre lieu de naissance.
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="prenom_pere" class="form-label required-field">Prénom du père</label>
                        <input type="text" class="form-control" id="prenom_pere" name="prenom_pere" required>
                        <div class="invalid-feedback">
                            Veuillez saisir le prénom du père.
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="nom_pere" class="form-label required-field">Nom du père</label>
                        <input type="text" class="form-control" id="nom_pere" name="nom_pere" required>
                        <div class="invalid-feedback">
                            Veuillez saisir le nom du père.
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="prenom_mere" class="form-label required-field">Prénom de la mère</label>
                        <input type="text" class="form-control" id="prenom_mere" name="prenom_mere" required>
                        <div class="invalid-feedback">
                            Veuillez saisir le prénom de la mère.
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="nom_mere" class="form-label required-field">Nom de la mère</label>
                        <input type="text" class="form-control" id="nom_mere" name="nom_mere" required>
                        <div class="invalid-feedback">
                            Veuillez saisir le nom de la mère.
                        </div>
                    </div>
                    <div class="col-md-6" id="annee_registre_group">
                        <label for="annee_registre" class="form-label required-field">Année du registre</label>
                        <input type="number" class="form-control" id="annee_registre" name="annee_registre" min="1900" max="2025" required>
                        <div class="invalid-feedback">
                            Veuillez saisir l'année du registre.
                        </div>
                    </div>
                    <div class="col-md-6" id="numero_registre_group">
                        <label for="numero_registre" class="form-label required-field">Numéro dans le registre</label>
                        <input type="text" class="form-control" id="numero_registre" name="numero_registre" required>
                        <div class="invalid-feedback">
                            Veuillez saisir le numéro dans le registre.
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="qualite_demandeur" class="form-label required-field">Demandeur</label>
                        <select class="form-select" id="qualite_demandeur" name="qualite_demandeur" required>
                            <option value="">Sélectionnez votre qualité</option>
                            <option value="titulaire">Titulaire de l'acte</option>
                            <option value="parent">Parent</option>
                            <option value="representant_legal">Représentant légal</option>
                        </select>
                        <div class="invalid-feedback">
                            Veuillez sélectionner votre qualité.
                        </div>
                    </div>
                    <div class="col-12">
                        <label for="adresse_actuelle" class="form-label required-field">Adresse actuelle</label>
                        <textarea class="form-control" id="adresse_actuelle" name="adresse_actuelle" rows="3" required></textarea>
                        <div class="invalid-feedback">
                            Veuillez saisir votre adresse actuelle.
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="telephone" class="form-label required-field">Téléphone</label>
                        <input type="tel" class="form-control" id="telephone" name="telephone" pattern="[0-9]{9}" required>
                        <div class="invalid-feedback">
                            Veuillez saisir un numéro de téléphone valide (9 chiffres).
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label required-field">Adresse e-mail</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                        <div class="invalid-feedback">
                            Veuillez saisir une adresse e-mail valide.
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-container">
                <!-- Section 3: Mode de délivrance et paiement -->
                <div class="section-header">
                    <i class="fas fa-cogs"></i>
                    <h4 class="mb-0">3. Mode de délivrance et paiement</h4>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="mode_delivrance" class="form-label required-field">Mode de délivrance souhaité</label>
                        <select class="form-select" id="mode_delivrance" name="mode_delivrance" required>
                            <option value="">Sélectionnez le mode de délivrance</option>
                            <option value="retrait_physique">Retrait physique à la mairie</option>
                            <option value="envoi_electronique">Envoi électronique</option>
                        </select>
                        <div class="invalid-feedback">
                            Veuillez sélectionner un mode de délivrance.
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="mode_paiement" class="form-label required-field">Mode de paiement</label>
                        <select class="form-select" id="mode_paiement" name="mode_paiement" required>
                            <option value="">Sélectionnez le mode de paiement</option>
                            <option value="wave">Par WAVE (781210618)</option>
                            <option value="orange_money">Par Orange Money (781210618)</option>
                        </select>
                        <div class="invalid-feedback">
                            Veuillez sélectionner un mode de paiement.
                        </div>
                    </div>
                </div>

                <div class="payment-info mt-3">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <i class="fas fa-credit-card text-primary"></i>
                        <h6 class="mb-0 fw-bold text-primary">Informations de paiement</h6>
                    </div>
                    
                    <div class="payment-method">
                        <div class="payment-icon wave-icon">
                            WAVE
                        </div>
                        <div>
                            <strong>WAVE</strong><br>
                            <span class="text-muted">Numéro : </span><strong>781210618</strong>
                        </div>
                    </div>
                    
                    <div class="payment-method">
                        <div class="payment-icon orange-icon">
                            OM
                        </div>
                        <div>
                            <strong>Orange Money</strong><br>
                            <span class="text-muted">Numéro : </span><strong>781210618</strong>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mt-3 mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Important :</strong> Effectuez le paiement et conservez la référence de transaction.
                    </div>
                </div>
            </div>

            <div class="form-container">
                <!-- Section 3: Consentements (suite) -->
                <div class="section-header">
                    <i class="fas fa-shield-alt"></i>
                    <h4 class="mb-0">Consentements et validation</h4>
                </div>

                <div class="checkbox-container">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="consentement_donnees" name="consentement_donnees" required>
                        <label class="form-check-label" for="consentement_donnees">
                            <strong>Je donne mon consentement libre et éclairé au traitement de mes données personnelles.</strong>
                        </label>
                        <div class="invalid-feedback">
                            Vous devez donner votre consentement pour continuer.
                        </div>
                    </div>
                    <div class="small text-muted mb-3">
                        En soumettant ce formulaire, je consens à la collecte et au traitement de mes données personnelles destinées exclusivement au traitement de cette présente demande. Ces données ne seront ni vendues ni partagées à des tiers non autorisés et seront conservées dans des conditions sécurisées. Je reconnais avoir été informé(e) de mon droit d'accès, de rectification ou de suppression de mes données.
                    </div>
                </div>

                <div class="checkbox-container">
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="acceptation_clause" name="acceptation_clause" required>
                        <label class="form-check-label" for="acceptation_clause">
                            <strong>J'ai lu, compris et accepte la clause de non-responsabilité de la Mairie de Khombole.</strong>
                        </label>
                        <div class="invalid-feedback">
                            Vous devez accepter la clause de non-responsabilité.
                        </div>
                    </div>
                    <div class="small text-muted">
                        La Mairie de Khombole s'engage à protéger la confidentialité et la sécurité des données collectées. Cependant, elle ne saurait être tenue responsable des utilisations non autorisées, des erreurs externes ou des incidents techniques indépendants de sa volonté. En soumettant ce formulaire, je reconnais avoir pris connaissance de cette clause et dégage la Mairie de toute responsabilité en cas de litige lié à des causes externes au traitement légal de mes données.
                    </div>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-paper-plane me-2"></i>
                        Soumettre ma demande
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Validation du formulaire
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();

        // Animation des sections au scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.form-container').forEach(container => {
            container.style.opacity = '0';
            container.style.transform = 'translateY(20px)';
            container.style.transition = 'all 0.6s ease';
            observer.observe(container);
        });

        // Formatage automatique du téléphone
        document.getElementById('telephone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 9) {
                value = value.substring(0, 9);
            }
            e.target.value = value;
        });

        // Validation de l'année
        document.getElementById('annee_registre').addEventListener('input', function(e) {
            const maxYear = 2025; // Permettre jusqu'à 2025
            const year = parseInt(e.target.value);
            if (year > maxYear) {
                e.target.value = maxYear;
            }
        });

        // Gestion des types d'actes multiples et nombre d'exemplaires
        const typesActes = {
            'extrait_naissance': { label: 'Extrait d\'acte de naissance' },
            'copie_litterale_naissance': { label: 'Copie littérale d\'acte de naissance' },
            'extrait_mariage': { label: 'Extrait d\'acte de mariage' },
            'certificat_residence': { label: 'Certificat de résidence' },
            'certificat_vie_individuelle': { label: 'Certificat de vie individuelle' },
            'certificat_vie_collective': { label: 'Certificat de vie collective' },
            'certificat_deces': { label: 'Certificat de décès' }
        };

        function gererExemplaires() {
            const checkboxes = document.querySelectorAll('input[name="types_actes[]"]:checked');
            const exemplairesSection = document.getElementById('exemplaires_section');
            const exemplairesContainer = document.getElementById('exemplaires_container');
            
            if (checkboxes.length === 0) {
                exemplairesSection.style.display = 'none';
                exemplairesContainer.innerHTML = '';
                return;
            }
            
            let exemplairesHTML = '';
            
            checkboxes.forEach(checkbox => {
                const type = checkbox.value;
                const acte = typesActes[type];
                
                exemplairesHTML += `
                    <div class="col-md-6">
                        <label for="exemplaires_${type}" class="form-label">
                            <strong>${acte.label}</strong>
                        </label>
                        <select class="form-select" id="exemplaires_${type}" name="exemplaires[${type}]" required>
                            <option value="">Choisir le nombre</option>
                            <option value="1">1 exemplaire</option>
                            <option value="2">2 exemplaires</option>
                            <option value="3">3 exemplaires</option>
                            <option value="4">4 exemplaires</option>
                            <option value="5">5 exemplaires</option>
                        </select>
                        <div class="invalid-feedback">
                            Veuillez sélectionner le nombre d'exemplaires.
                        </div>
                    </div>
                `;
            });
            
            exemplairesContainer.innerHTML = exemplairesHTML;
            exemplairesSection.style.display = 'block';
            
            // Faire défiler vers la section des exemplaires
            setTimeout(() => {
                exemplairesSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
            }, 100);
        }

        // Fonction pour gérer les champs optionnels selon le lieu de naissance
        function gererChampsOptionnels() {
            const lieuNaissance = document.getElementById('lieu_naissance');
            
            // Éléments du registre
            const anneeRegistreGroup = document.getElementById('annee_registre_group');
            const numeroRegistreGroup = document.getElementById('numero_registre_group');
            const anneeRegistreInput = document.getElementById('annee_registre');
            const numeroRegistreInput = document.getElementById('numero_registre');
            const anneeRegistreLabel = anneeRegistreGroup.querySelector('label');
            const numeroRegistreLabel = numeroRegistreGroup.querySelector('label');
            
            // Éléments des parents
            const prenomPereInput = document.getElementById('prenom_pere');
            const nomPereInput = document.getElementById('nom_pere');
            const prenomMereInput = document.getElementById('prenom_mere');
            const nomMereInput = document.getElementById('nom_mere');
            const prenomPereLabel = document.querySelector('label[for="prenom_pere"]');
            const nomPereLabel = document.querySelector('label[for="nom_pere"]');
            const prenomMereLabel = document.querySelector('label[for="prenom_mere"]');
            const nomMereLabel = document.querySelector('label[for="nom_mere"]');
            
            // Vérifier si la personne habite à Khombole
            const habiteKhombole = lieuNaissance && lieuNaissance.value.toLowerCase() === 'khombole';
            
            if (habiteKhombole) {
                // Pour les résidents de Khombole : champs parents optionnels, registre obligatoire
                
                // Rendre les champs parents optionnels
                prenomPereInput.removeAttribute('required');
                nomPereInput.removeAttribute('required');
                prenomMereInput.removeAttribute('required');
                nomMereInput.removeAttribute('required');
                prenomPereLabel.classList.remove('required-field');
                nomPereLabel.classList.remove('required-field');
                prenomMereLabel.classList.remove('required-field');
                nomMereLabel.classList.remove('required-field');
                prenomPereLabel.innerHTML = 'Prénom du père <small class="text-muted">(optionnel - résident de Khombole)</small>';
                nomPereLabel.innerHTML = 'Nom du père <small class="text-muted">(optionnel - résident de Khombole)</small>';
                prenomMereLabel.innerHTML = 'Prénom de la mère <small class="text-muted">(optionnel - résident de Khombole)</small>';
                nomMereLabel.innerHTML = 'Nom de la mère <small class="text-muted">(optionnel - résident de Khombole)</small>';
                
                // Rendre les champs registre obligatoires
                anneeRegistreInput.setAttribute('required', 'required');
                numeroRegistreInput.setAttribute('required', 'required');
                anneeRegistreLabel.classList.add('required-field');
                numeroRegistreLabel.classList.add('required-field');
                anneeRegistreLabel.innerHTML = 'Année du registre';
                numeroRegistreLabel.innerHTML = 'Numéro dans le registre';
                
            } else {
                // Pour les non-résidents de Khombole : champs parents obligatoires, registre optionnel
                
                // Rendre les champs parents obligatoires
                prenomPereInput.setAttribute('required', 'required');
                nomPereInput.setAttribute('required', 'required');
                prenomMereInput.setAttribute('required', 'required');
                nomMereInput.setAttribute('required', 'required');
                prenomPereLabel.classList.add('required-field');
                nomPereLabel.classList.add('required-field');
                prenomMereLabel.classList.add('required-field');
                nomMereLabel.classList.add('required-field');
                prenomPereLabel.innerHTML = 'Prénom du père';
                nomPereLabel.innerHTML = 'Nom du père';
                prenomMereLabel.innerHTML = 'Prénom de la mère';
                nomMereLabel.innerHTML = 'Nom de la mère';
                
                // Rendre les champs registre optionnels
                anneeRegistreInput.removeAttribute('required');
                numeroRegistreInput.removeAttribute('required');
                anneeRegistreLabel.classList.remove('required-field');
                numeroRegistreLabel.classList.remove('required-field');
                anneeRegistreLabel.innerHTML = 'Année du registre <small class="text-muted">(optionnel - non-résident de Khombole)</small>';
                numeroRegistreLabel.innerHTML = 'Numéro dans le registre <small class="text-muted">(optionnel - non-résident de Khombole)</small>';
            }
        }

        // Écouter les changements sur les checkboxes et le lieu de naissance
        document.querySelectorAll('input[name="types_actes[]"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                gererExemplaires();
                gererChampsOptionnels();
                
                // Masquer l'erreur dès qu'un type d'acte est sélectionné
                const checkboxes = document.querySelectorAll('input[name="types_actes[]"]:checked');
                const errorDiv = document.getElementById('types_actes_error');
                if (checkboxes.length > 0) {
                    errorDiv.style.display = 'none';
                }
            });
        });

        // Écouter les changements sur le lieu de naissance
        document.getElementById('lieu_naissance').addEventListener('input', gererChampsOptionnels);
        
        // Initialiser les champs au chargement de la page
        gererChampsOptionnels();

        // Validation personnalisée pour les types d'actes et exemplaires
        function validerTypesActes() {
            const checkboxes = document.querySelectorAll('input[name="types_actes[]"]:checked');
            const errorDiv = document.getElementById('types_actes_error');
            
            if (checkboxes.length === 0) {
                errorDiv.style.display = 'block';
                errorDiv.textContent = 'Veuillez sélectionner au moins un type d\'acte.';
                return false;
            } else {
                errorDiv.style.display = 'none';
                
                // Vérifier que chaque type d'acte sélectionné a un nombre d'exemplaires
                let exemplairesValides = true;
                let messageErreur = '';
                
                checkboxes.forEach(checkbox => {
                    const type = checkbox.value;
                    const exemplairesSelect = document.getElementById(`exemplaires_${type}`);
                    if (!exemplairesSelect || !exemplairesSelect.value) {
                        exemplairesValides = false;
                        messageErreur = 'Veuillez sélectionner le nombre d\'exemplaires pour chaque type d\'acte choisi.';
                        if (exemplairesSelect) {
                            exemplairesSelect.classList.add('is-invalid');
                        }
                    } else {
                        if (exemplairesSelect) {
                            exemplairesSelect.classList.remove('is-invalid');
                        }
                    }
                });
                
                if (!exemplairesValides) {
                    errorDiv.style.display = 'block';
                    errorDiv.textContent = messageErreur;
                }
                
                return exemplairesValides;
            }
        }

        // Ajouter la validation personnalisée au formulaire
        document.querySelector('form').addEventListener('submit', function(e) {
            if (!validerTypesActes()) {
                e.preventDefault();
                e.stopPropagation();
                
                // Faire défiler vers la section des types d'actes
                document.querySelector('.form-container').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    </script>
</body>
</html>
