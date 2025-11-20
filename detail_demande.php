<?php
/**
 * Page de détail d'une demande d'acte
 * Mairie de Khombole
 */

// Connexion à la base de données
require_once 'db_connection.php';

$demande = null;
$erreur = null;

// Récupération de la demande par numéro de demande ou numéro de registre
if (isset($_GET['numero']) || isset($_GET['registre'])) {
    try {
        $pdo = createPDOConnection();
        
        if (isset($_GET['registre'])) {
            // Recherche par numéro de registre
            $sql = "SELECT * FROM demandes_actes WHERE numero_registre = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$_GET['registre']]);
        } else {
            // Recherche par numéro de demande
            $sql = "SELECT * FROM demandes_actes WHERE numero_demande = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$_GET['numero']]);
        }
        
        $demande = $stmt->fetch();
        
        if (!$demande) {
            $erreur = "Demande introuvable.";
        }
        
    } catch (PDOException $e) {
        $erreur = "Erreur de connexion à la base de données.";
    }
} else {
    $erreur = "Aucun identifiant de demande fourni.";
}

// Types d'actes
$typesActes = [
    'extrait_naissance' => 'Extrait d\'acte de naissance',
    'copie_litterale_naissance' => 'Copie littérale d\'acte de naissance',
    'extrait_mariage' => 'Extrait d\'acte de mariage',
    'certificat_residence' => 'Certificat de résidence',
    'certificat_vie_individuelle' => 'Certificat de vie individuelle',
    'certificat_vie_collective' => 'Certificat de vie collective',
    'certificat_deces' => 'Certificat de décès'
];

// Statuts
$statutsLabels = [
    'en_attente' => 'En attente de traitement',
    'en_traitement' => 'En cours de traitement',
    'pret' => 'Prêt pour retrait',
    'delivre' => 'Délivré',
    'rejete' => 'Rejeté'
];

$statutsColors = [
    'en_attente' => 'warning',
    'en_traitement' => 'info',
    'pret' => 'success',
    'delivre' => 'primary',
    'rejete' => 'danger'
];

// Qualités du demandeur
$qualites = [
    'titulaire' => 'Titulaire de l\'acte',
    'parent' => 'Parent',
    'representant_legal' => 'Représentant légal'
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail de la Demande - Mairie de Khombole</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #0b843e;
            --secondary-color: #f4e93d;
            --accent-color: #1e3a8a;
            --text-dark: #2c3e50;
            --bg-light: #f8f9fa;
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }

        .header-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 30px 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .detail-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            padding: 3rem;
            margin-bottom: 2rem;
        }

        .section-card {
            background: var(--bg-light);
            border-radius: 15px;
            padding: 2rem;
            margin: 1.5rem 0;
            border-left: 5px solid var(--primary-color);
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        .section-title {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 0.75rem;
            font-size: 1.5rem;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e9ecef;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: var(--text-dark);
            flex: 0 0 40%;
        }

        .info-value {
            flex: 1;
            text-align: right;
            color: #495057;
        }

        .status-badge {
            font-size: 1rem;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .timeline {
            position: relative;
            padding-left: 2rem;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 3px;
            background: #dee2e6;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 2.5rem;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -24px;
            top: 5px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #dee2e6;
            border: 4px solid white;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .timeline-item.active::before {
            background: var(--primary-color);
        }

        .timeline-item.completed::before {
            background: #28a745;
        }

        .timeline-content {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .btn-back {
            background: linear-gradient(135deg, #6c757d, #495057);
            border: none;
            border-radius: 25px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.3);
        }

        .btn-back:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(108, 117, 125, 0.4);
            color: white;
        }

        .alert-custom {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 1.5rem;
        }

        .highlight-badge {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 1.1rem;
        }

        /* Logo et en-tête */
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
                    <h1 class="mb-0 fw-bold">DÉTAIL DE LA DEMANDE</h1>
                    <p class="mb-0">Mairie de Khombole - République du Sénégal</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Bouton retour -->
        <div class="mb-4">
            <?php if (isset($_GET['retour']) && $_GET['retour'] === 'liste'): ?>
                <a href="liste_demandes.php" class="btn-back">
                    <i class="fas fa-arrow-left me-2"></i>
                    Retour à la liste des demandes
                </a>
            <?php else: ?>
                <a href="suivi_demande.php" class="btn-back">
                    <i class="fas fa-arrow-left me-2"></i>
                    Retour à la recherche
                </a>
            <?php endif; ?>
        </div>

        <?php if ($erreur): ?>
            <div class="alert alert-danger alert-custom">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?= htmlspecialchars($erreur) ?>
            </div>
        <?php endif; ?>

        <?php if ($demande): ?>
            <div class="detail-container">
                <!-- En-tête de la demande -->
                <div class="text-center mb-4">
                    <h2 class="fw-bold text-primary mb-2">
                        <?= htmlspecialchars($typesActes[$demande['type_acte']] ?? $demande['type_acte']) ?>
                    </h2>
                    <div class="mb-3">
                        <span class="badge bg-<?= $statutsColors[$demande['statut']] ?> status-badge">
                            <?= htmlspecialchars($statutsLabels[$demande['statut']] ?? $demande['statut']) ?>
                        </span>
                    </div>
                </div>

                <!-- 1. Type d'acte et nombre d'exemplaires -->
                <div class="section-card">
                    <h3 class="section-title">
                        <i class="fas fa-file-alt"></i>
                        Type d'acte
                    </h3>
                    <div class="info-row">
                        <span class="info-label">Type d'acte :</span>
                        <span class="info-value fw-bold"><?= htmlspecialchars($typesActes[$demande['type_acte']] ?? $demande['type_acte']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Nombre d'exemplaires :</span>
                        <span class="info-value">
                            <span class="highlight-badge"><?= htmlspecialchars($demande['nombre_exemplaires']) ?> exemplaire<?= $demande['nombre_exemplaires'] > 1 ? 's' : '' ?></span>
                        </span>
                    </div>
                </div>

                <!-- 2. Informations du demandeur -->
                <div class="section-card">
                    <h3 class="section-title">
                        <i class="fas fa-user"></i>
                        Informations du demandeur
                    </h3>
                    <div class="info-row">
                        <span class="info-label">Nom :</span>
                        <span class="info-value fw-bold"><?= htmlspecialchars($demande['nom']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Prénom(s) :</span>
                        <span class="info-value fw-bold"><?= htmlspecialchars($demande['prenoms']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Date de naissance :</span>
                        <span class="info-value"><?= date('d/m/Y', strtotime($demande['date_naissance'])) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Lieu de naissance :</span>
                        <span class="info-value"><?= htmlspecialchars($demande['lieu_naissance']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Année du registre :</span>
                        <span class="info-value"><?= htmlspecialchars($demande['annee_registre']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Numéro dans le registre :</span>
                        <span class="info-value">
                            <span class="highlight-badge"><?= htmlspecialchars($demande['numero_registre']) ?></span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Demandeur :</span>
                        <span class="info-value"><?= htmlspecialchars($qualites[$demande['qualite_demandeur']] ?? $demande['qualite_demandeur']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Adresse actuelle :</span>
                        <span class="info-value"><?= htmlspecialchars($demande['adresse_actuelle']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Téléphone :</span>
                        <span class="info-value"><?= htmlspecialchars($demande['telephone']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Adresse e-mail :</span>
                        <span class="info-value"><?= htmlspecialchars($demande['email']) ?></span>
                    </div>
                </div>

                <!-- 3. Mode de délivrance et paiement -->
                <div class="section-card">
                    <h3 class="section-title">
                        <i class="fas fa-credit-card"></i>
                        Mode de délivrance et paiement
                    </h3>
                    <div class="info-row">
                        <span class="info-label">Mode de délivrance souhaité :</span>
                        <span class="info-value">
                            <?= $demande['mode_delivrance'] === 'retrait_physique' ? 'Retrait physique à la mairie' : 'Envoi électronique' ?>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Mode de paiement :</span>
                        <span class="info-value">
                            <?php if ($demande['mode_paiement'] === 'wave'): ?>
                                <i class="fas fa-mobile-alt me-2 text-primary"></i>
                                Par WAVE (781210618)
                            <?php else: ?>
                                <i class="fas fa-mobile-alt me-2 text-warning"></i>
                                Par Orange Money (781210618)
                            <?php endif; ?>
                        </span>
                    </div>
                </div>

                <!-- Informations de suivi -->
                <div class="section-card">
                    <h3 class="section-title">
                        <i class="fas fa-info-circle"></i>
                        Informations de suivi
                    </h3>
                    <div class="info-row">
                        <span class="info-label">Numéro de demande :</span>
                        <span class="info-value">
                            <span class="highlight-badge"><?= htmlspecialchars($demande['numero_demande']) ?></span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Date de soumission :</span>
                        <span class="info-value"><?= date('d/m/Y à H:i', strtotime($demande['date_soumission'])) ?></span>
                    </div>
                    <?php if ($demande['date_traitement']): ?>
                    <div class="info-row">
                        <span class="info-label">Date de traitement :</span>
                        <span class="info-value"><?= date('d/m/Y à H:i', strtotime($demande['date_traitement'])) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if ($demande['date_delivrance']): ?>
                    <div class="info-row">
                        <span class="info-label">Date de délivrance :</span>
                        <span class="info-value"><?= date('d/m/Y à H:i', strtotime($demande['date_delivrance'])) ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Timeline du traitement -->
                <div class="section-card">
                    <h3 class="section-title">
                        <i class="fas fa-clock"></i>
                        Suivi du traitement
                    </h3>

                    <div class="timeline">
                        <div class="timeline-item completed">
                            <div class="timeline-content">
                                <h6 class="fw-bold text-success mb-2">Demande soumise</h6>
                                <p class="text-muted mb-1">Votre demande a été enregistrée dans notre système</p>
                                <small class="text-muted"><?= date('d/m/Y à H:i', strtotime($demande['date_soumission'])) ?></small>
                            </div>
                        </div>

                        <div class="timeline-item <?= in_array($demande['statut'], ['en_traitement', 'pret', 'delivre']) ? 'completed' : ($demande['statut'] === 'en_attente' ? 'active' : '') ?>">
                            <div class="timeline-content">
                                <h6 class="fw-bold <?= $demande['statut'] === 'en_attente' ? 'text-warning' : 'text-success' ?> mb-2">
                                    Examen de la demande
                                </h6>
                                <p class="text-muted mb-1">Vérification des informations et des documents</p>
                                <?php if ($demande['statut'] !== 'en_attente'): ?>
                                    <small class="text-muted">Traitement effectué</small>
                                <?php else: ?>
                                    <small class="text-warning">En cours...</small>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if ($demande['statut'] !== 'rejete'): ?>
                            <div class="timeline-item <?= in_array($demande['statut'], ['pret', 'delivre']) ? 'completed' : ($demande['statut'] === 'en_traitement' ? 'active' : '') ?>">
                                <div class="timeline-content">
                                    <h6 class="fw-bold <?= $demande['statut'] === 'en_traitement' ? 'text-info' : (in_array($demande['statut'], ['pret', 'delivre']) ? 'text-success' : 'text-muted') ?> mb-2">
                                        Préparation de l'acte
                                    </h6>
                                    <p class="text-muted mb-1">Génération et validation du document</p>
                                    <?php if (in_array($demande['statut'], ['pret', 'delivre'])): ?>
                                        <small class="text-muted">Document préparé</small>
                                    <?php elseif ($demande['statut'] === 'en_traitement'): ?>
                                        <small class="text-info">En cours...</small>
                                    <?php else: ?>
                                        <small class="text-muted">En attente</small>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="timeline-item <?= $demande['statut'] === 'delivre' ? 'completed' : ($demande['statut'] === 'pret' ? 'active' : '') ?>">
                                <div class="timeline-content">
                                    <h6 class="fw-bold <?= $demande['statut'] === 'pret' ? 'text-success' : ($demande['statut'] === 'delivre' ? 'text-success' : 'text-muted') ?> mb-2">
                                        <?= $demande['mode_delivrance'] === 'retrait_physique' ? 'Prêt pour retrait' : 'Envoi électronique' ?>
                                    </h6>
                                    <p class="text-muted mb-1">
                                        <?= $demande['mode_delivrance'] === 'retrait_physique' ? 'Document disponible à la mairie' : 'Document envoyé par e-mail' ?>
                                    </p>
                                    <?php if ($demande['statut'] === 'delivre'): ?>
                                        <small class="text-muted">
                                            <?= $demande['date_delivrance'] ? date('d/m/Y à H:i', strtotime($demande['date_delivrance'])) : 'Délivré' ?>
                                        </small>
                                    <?php elseif ($demande['statut'] === 'pret'): ?>
                                        <small class="text-success">Disponible maintenant</small>
                                    <?php else: ?>
                                        <small class="text-muted">En attente</small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="timeline-item completed">
                                <div class="timeline-content">
                                    <h6 class="fw-bold text-danger mb-2">Demande rejetée</h6>
                                    <p class="text-muted mb-1">Votre demande n'a pas pu être traitée</p>
                                    <?php if ($demande['commentaire_admin']): ?>
                                        <div class="alert alert-danger mt-2">
                                            <strong>Motif :</strong> <?= htmlspecialchars($demande['commentaire_admin']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Actions disponibles -->
                <?php if ($demande['statut'] === 'pret' && $demande['mode_delivrance'] === 'retrait_physique'): ?>
                    <div class="alert alert-success alert-custom">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Votre acte est prêt !</strong> Vous pouvez venir le retirer à la mairie aux heures d'ouverture.
                    </div>
                <?php elseif ($demande['statut'] === 'delivre'): ?>
                    <div class="alert alert-info alert-custom">
                        <i class="fas fa-info-circle me-2"></i>
                        Votre acte a été délivré. Si vous ne l'avez pas reçu, contactez-nous.
                    </div>
                <?php elseif ($demande['statut'] === 'rejete'): ?>
                    <div class="alert alert-danger alert-custom">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Votre demande a été rejetée. Vous pouvez soumettre une nouvelle demande avec les corrections nécessaires.
                    </div>
                <?php else: ?>
                    <div class="alert alert-info alert-custom">
                        <i class="fas fa-clock me-2"></i>
                        Votre demande est en cours de traitement. Vous recevrez une notification par e-mail dès qu'elle sera prête.
                    </div>
                <?php endif; ?>

                <!-- Informations de contact -->
                <div class="section-card">
                    <h3 class="section-title">
                        <i class="fas fa-phone"></i>
                        Besoin d'aide ?
                    </h3>
                    <div class="info-row">
                        <span class="info-label">Téléphone :</span>
                        <span class="info-value">+221 33 624 52 13 63</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">E-mail :</span>
                        <span class="info-value">mairiedekhombole@gmail.com</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Horaires :</span>
                        <span class="info-value">Lundi - Vendredi : 8h00 - 17h00</span>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
