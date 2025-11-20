<?php
/**
 * Page d'affichage de toutes les demandes d'actes - ADMIN SEULEMENT
 * Mairie de Khombole
 */

// Vérifier l'authentification admin
require_once 'session_manager.php';
verifierConnexionAdmin();

// Logger l'accès à la page
loggerActionAdmin('Accès à la liste des demandes');

// Connexion à la base de données
require_once 'db_connection.php';

$demandes = [];
$erreur = null;
$succes = null;
$totalDemandes = 0;

// Traitement de la modification de statut
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'modifier_statut') {
    $demandeId = intval($_POST['demande_id'] ?? 0);
    $nouveauStatut = $_POST['nouveau_statut'] ?? '';
    $commentaire = trim($_POST['commentaire'] ?? '');
    
    // Vérifier le token CSRF
    if (!verifierTokenCSRF($_POST['csrf_token'] ?? '')) {
        $erreur = 'Token de sécurité invalide.';
    } else if ($demandeId > 0 && !empty($nouveauStatut)) {
        try {
            $pdo = createPDOConnection();
            
            // Récupérer l'ancien statut
            $sqlOld = "SELECT statut, numero_demande FROM demandes_actes WHERE id = ?";
            $stmtOld = $pdo->prepare($sqlOld);
            $stmtOld->execute([$demandeId]);
            $ancienneDemande = $stmtOld->fetch();
            
            if ($ancienneDemande) {
                // Mettre à jour le statut
                $sqlUpdate = "UPDATE demandes_actes SET statut = ?, commentaire_admin = ?, date_traitement = NOW() WHERE id = ?";
                $stmtUpdate = $pdo->prepare($sqlUpdate);
                $stmtUpdate->execute([$nouveauStatut, $commentaire, $demandeId]);
                
                // Ajouter à l'historique
                $sqlHist = "INSERT INTO historique_demandes (demande_id, action, ancien_statut, nouveau_statut, commentaire, utilisateur) VALUES (?, ?, ?, ?, ?, ?)";
                $stmtHist = $pdo->prepare($sqlHist);
                $stmtHist->execute([
                    $demandeId,
                    'modification_statut',
                    $ancienneDemande['statut'],
                    $nouveauStatut,
                    $commentaire,
                    $_SESSION['admin_email']
                ]);
                
                $succes = "Statut de la demande {$ancienneDemande['numero_demande']} modifié avec succès.";
                
                // Logger l'action
                loggerActionAdmin('Modification statut', "Demande {$ancienneDemande['numero_demande']}: {$ancienneDemande['statut']} → $nouveauStatut");
            } else {
                $erreur = 'Demande introuvable.';
            }
        } catch (PDOException $e) {
            $erreur = 'Erreur lors de la modification du statut.';
        }
    } else {
        $erreur = 'Données invalides pour la modification.';
    }
}

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$parPage = 10;
$offset = ($page - 1) * $parPage;

// Filtres
$filtreStatut = $_GET['statut'] ?? '';
$filtreType = $_GET['type'] ?? '';
$recherche = $_GET['recherche'] ?? '';

try {
    $pdo = createPDOConnection();
    
    // Construction de la requête avec filtres
    $whereConditions = [];
    $params = [];
    
    if (!empty($filtreStatut)) {
        $whereConditions[] = "statut = ?";
        $params[] = $filtreStatut;
    }
    
    if (!empty($filtreType)) {
        $whereConditions[] = "type_acte = ?";
        $params[] = $filtreType;
    }
    
    if (!empty($recherche)) {
        $whereConditions[] = "(nom LIKE ? OR prenoms LIKE ? OR numero_registre LIKE ?)";
        $params[] = "%$recherche%";
        $params[] = "%$recherche%";
        $params[] = "%$recherche%";
    }
    
    $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';
    
    // Compter le total
    $sqlCount = "SELECT COUNT(*) as total FROM demandes_actes $whereClause";
    $stmtCount = $pdo->prepare($sqlCount);
    $stmtCount->execute($params);
    $totalDemandes = $stmtCount->fetch()['total'];
    
    // Récupérer les demandes avec pagination
    $sql = "SELECT * FROM demandes_actes $whereClause ORDER BY date_soumission DESC LIMIT $parPage OFFSET $offset";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $demandes = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $erreur = "Erreur de connexion à la base de données.";
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
    'en_attente' => 'En attente',
    'en_traitement' => 'En traitement',
    'pret' => 'Prêt',
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

// Calcul pagination
$totalPages = ceil($totalDemandes / $parPage);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Demandes - Mairie de Khombole</title>
    
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

        .main-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .filters-section {
            background: var(--bg-light);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid #dee2e6;
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            padding: 2rem;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(11, 132, 62, 0.3);
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(11, 132, 62, 0.4);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: rotate(45deg);
        }

        .stat-card > * {
            position: relative;
            z-index: 1;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .stat-card i {
            font-size: 1.2rem;
            opacity: 0.8;
            margin-right: 0.5rem;
        }

        .table-container {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            border: none;
            padding: 1rem;
            font-weight: 600;
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
            border-color: #f1f3f4;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border: none;
            border-radius: 25px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(11, 132, 62, 0.4);
        }

        .pagination {
            justify-content: center;
            margin-top: 2rem;
        }

        .page-link {
            border-radius: 10px;
            margin: 0 2px;
            border: none;
            color: var(--primary-color);
        }

        .page-link:hover, .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            padding-left: 3rem;
            border-radius: 25px;
            border: 2px solid #e9ecef;
        }

        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        /* Logo et en-tête */
        .logo-container {
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
        }

        .logo-circle i {
            font-size: 1.8rem;
            color: white;
        }

        .logo-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .admin-section {
            margin-left: 2rem;
        }

        .admin-btn {
            background: rgba(255, 255, 255, 0.95) !important;
            border: none !important;
            border-radius: 25px !important;
            padding: 0.75rem 1.5rem !important;
            font-weight: 500 !important;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1) !important;
            transition: all 0.3s ease !important;
        }

        .admin-btn:hover {
            background: white !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
        }

        .admin-email {
            color: var(--text-dark);
            font-size: 0.9rem;
        }

        /* Amélioration du design général */
        .header-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            position: relative;
            overflow: hidden;
        }

        .header-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .header-section > .container {
            position: relative;
            z-index: 1;
        }

        @media (max-width: 768px) {
            .main-container {
                padding: 1rem;
                margin: 0 10px;
            }
            
            .table-responsive {
                font-size: 0.9rem;
            }

            .logo-circle {
                width: 65px;
                height: 65px;
            }

            .logo-circle i {
                font-size: 1.4rem;
            }

            .logo-img {
                width: 100%;
                height: 100%;
            }

            .admin-section {
                margin-left: 1rem;
            }

            .admin-email {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header-section">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <!-- Logo de la mairie -->
                    <div class="logo-container me-4">
                        <div class="logo-circle">
                            <img src="logo.jpg" alt="Logo Mairie de Khombole" class="logo-img">
                        </div>
                    </div>
                    <div>
                        <h1 class="mb-0 fw-bold">ADMINISTRATION - DEMANDES D'ACTES</h1>
                        <p class="mb-0 opacity-75">Mairie de Khombole - République du Sénégal</p>
                    </div>
                </div>
                <div class="admin-section">
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle admin-btn" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-shield me-2"></i>
                            <span class="admin-email"><?= htmlspecialchars($_SESSION['admin_email']) ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="admin_logout.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="main-container">
            <!-- Statistiques -->
            <div class="stats-cards">
                <div class="stat-card">
                    <div class="stat-number"><?= $totalDemandes ?></div>
                    <div>
                        <i class="fas fa-file-alt"></i>
                        Total des demandes
                    </div>
                </div>
            </div>

            <!-- Filtres et recherche -->
            <div class="filters-section">
                <h5 class="fw-bold text-primary mb-3">
                    <i class="fas fa-filter me-2"></i>
                    Filtres et recherche
                </h5>
                
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" class="form-control" name="recherche" 
                                   placeholder="Rechercher par nom, prénom ou numéro de registre..." 
                                   value="<?= htmlspecialchars($recherche) ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="statut">
                            <option value="">Tous les statuts</option>
                            <?php foreach ($statutsLabels as $statut => $label): ?>
                                <option value="<?= $statut ?>" <?= $filtreStatut === $statut ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-select" name="type">
                            <option value="">Tous les types</option>
                            <?php foreach ($typesActes as $type => $label): ?>
                                <option value="<?= $type ?>" <?= $filtreType === $type ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i>
                            Filtrer
                        </button>
                    </div>
                </form>
            </div>

            <!-- Messages -->
            <?php if ($erreur): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <?= htmlspecialchars($erreur) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($succes): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i>
                    <?= htmlspecialchars($succes) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Tableau des demandes -->
            <?php if (!empty($demandes)): ?>
                <div class="table-container">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Numéro</th>
                                    <th>Demandeur</th>
                                    <th>Type d'acte</th>
                                    <th>Exemplaires</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($demandes as $demande): ?>
                                    <tr>
                                        <td>
                                            <strong class="text-primary"><?= htmlspecialchars($demande['numero_demande']) ?></strong>
                                        </td>
                                        <td>
                                            <div>
                                                <strong><?= htmlspecialchars($demande['prenoms'] . ' ' . $demande['nom']) ?></strong><br>
                                                <small class="text-muted"><?= htmlspecialchars($demande['email']) ?></small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">
                                                <?= htmlspecialchars($typesActes[$demande['type_acte']] ?? $demande['type_acte']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?= $demande['nombre_exemplaires'] ?> ex.
                                            </span>
                                        </td>
                                        <td>
                                            <?= date('d/m/Y', strtotime($demande['date_soumission'])) ?><br>
                                            <small class="text-muted"><?= date('H:i', strtotime($demande['date_soumission'])) ?></small>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?= $statutsColors[$demande['statut']] ?> status-badge">
                                                <?= htmlspecialchars($statutsLabels[$demande['statut']] ?? $demande['statut']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="detail_demande.php?numero=<?= urlencode($demande['numero_demande']) ?>&retour=liste" 
                                                   class="btn btn-sm btn-outline-primary" title="Voir détails">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-success" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#modalStatut"
                                                        data-demande-id="<?= $demande['id'] ?>"
                                                        data-numero="<?= htmlspecialchars($demande['numero_demande']) ?>"
                                                        data-statut-actuel="<?= htmlspecialchars($demande['statut']) ?>"
                                                        title="Modifier statut">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav>
                        <ul class="pagination">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $page - 1 ?>&statut=<?= urlencode($filtreStatut) ?>&type=<?= urlencode($filtreType) ?>&recherche=<?= urlencode($recherche) ?>">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                                    <a class="page-link" href="?page=<?= $i ?>&statut=<?= urlencode($filtreStatut) ?>&type=<?= urlencode($filtreType) ?>&recherche=<?= urlencode($recherche) ?>">
                                        <?= $i ?>
                                    </a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?= $page + 1 ?>&statut=<?= urlencode($filtreStatut) ?>&type=<?= urlencode($filtreType) ?>&recherche=<?= urlencode($recherche) ?>">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>

            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucune demande trouvée</h5>
                    <p class="text-muted">Il n'y a pas de demandes correspondant à vos critères de recherche (nom, prénom ou numéro de registre).</p>
                    <small class="text-muted">Modifiez vos critères de recherche pour afficher d'autres résultats.</small>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- Modal de modification de statut -->
    <div class="modal fade" id="modalStatut" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit me-2"></i>
                        Modifier le statut
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="modifier_statut">
                        <input type="hidden" name="demande_id" id="modal_demande_id">
                        <input type="hidden" name="csrf_token" value="<?= genererTokenCSRF() ?>">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Demande :</label>
                            <span id="modal_numero_demande" class="text-primary"></span>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Statut actuel :</label>
                            <span id="modal_statut_actuel" class="badge"></span>
                        </div>
                        
                        <div class="mb-3">
                            <label for="nouveau_statut" class="form-label fw-bold">Nouveau statut :</label>
                            <select class="form-select" name="nouveau_statut" id="nouveau_statut" required>
                                <option value="">Sélectionner un statut</option>
                                <option value="en_attente">En attente</option>
                                <option value="en_traitement">En traitement</option>
                                <option value="pret">Prêt</option>
                                <option value="delivre">Délivré</option>
                                <option value="rejete">Rejeté</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="commentaire" class="form-label">Commentaire (optionnel) :</label>
                            <textarea class="form-control" name="commentaire" id="commentaire" rows="3" 
                                      placeholder="Ajouter un commentaire sur cette modification..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Annuler
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Animation des cartes au chargement
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.stat-card, .main-container');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                
                setTimeout(() => {
                    card.style.transition = 'all 0.6s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });

        // Auto-submit du formulaire de recherche avec délai
        let searchTimeout;
        document.querySelector('input[name="recherche"]').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.form.submit();
            }, 1000);
        });

        // Gestion du modal de modification de statut
        const modalStatut = document.getElementById('modalStatut');
        const statutsColors = {
            'en_attente': 'warning',
            'en_traitement': 'info',
            'pret': 'success',
            'delivre': 'primary',
            'rejete': 'danger'
        };
        
        const statutsLabels = {
            'en_attente': 'En attente',
            'en_traitement': 'En traitement',
            'pret': 'Prêt',
            'delivre': 'Délivré',
            'rejete': 'Rejeté'
        };

        modalStatut.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const demandeId = button.getAttribute('data-demande-id');
            const numero = button.getAttribute('data-numero');
            const statutActuel = button.getAttribute('data-statut-actuel');
            
            // Remplir les champs du modal
            document.getElementById('modal_demande_id').value = demandeId;
            document.getElementById('modal_numero_demande').textContent = numero;
            
            const badgeStatut = document.getElementById('modal_statut_actuel');
            badgeStatut.textContent = statutsLabels[statutActuel] || statutActuel;
            badgeStatut.className = `badge bg-${statutsColors[statutActuel] || 'secondary'}`;
            
            // Réinitialiser le formulaire
            document.getElementById('nouveau_statut').value = '';
            document.getElementById('commentaire').value = '';
        });
    </script>
</body>
</html>
