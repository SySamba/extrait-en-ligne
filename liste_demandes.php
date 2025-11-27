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
    'en_attente' => 'danger',      // Rouge pour En attente
    'en_traitement' => 'warning',  // Orange pour En traitement
    'pret' => 'info',             // Bleu pour Prêt
    'delivre' => 'success',       // Vert pour Délivré
    'rejete' => 'dark'            // Gris foncé pour Rejeté
];

// Calcul des statistiques par statut
$statsParStatut = [];
try {
    $sqlStats = "SELECT statut, COUNT(*) as nombre FROM demandes_actes GROUP BY statut";
    $stmtStats = $pdo->prepare($sqlStats);
    $stmtStats->execute();
    $resultStats = $stmtStats->fetchAll(PDO::FETCH_ASSOC);
    
    // Initialiser tous les statuts à 0
    foreach (array_keys($statutsLabels) as $statut) {
        $statsParStatut[$statut] = 0;
    }
    
    // Remplir avec les vraies valeurs
    foreach ($resultStats as $stat) {
        $statsParStatut[$stat['statut']] = $stat['nombre'];
    }
} catch (Exception $e) {
    // En cas d'erreur, initialiser à 0
    foreach (array_keys($statutsLabels) as $statut) {
        $statsParStatut[$statut] = 0;
    }
}

// Calcul pagination
$totalPages = ceil($totalDemandes / $parPage);

// Définir le titre de la page
$pageTitle = 'Liste des Demandes';

// Header simple sans navigation
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
</head>
<body>
<?php
?>
    
<!-- Styles spécifiques à la liste des demandes -->
<style>
        /* Thème Sénégal - Couleurs nationales avec dominance du blanc */
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
            --bg-light: var(--blanc-principal);
        }
        
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background-color: var(--blanc-principal);
            color: var(--texte-visible);
            margin: 0;
            padding: 20px;
        }

        .main-container {
            background: var(--blanc-principal);
            border: 3px solid var(--senegal-vert);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            padding: 2rem;
            margin-bottom: 2rem;
        }
        
        /* Styles pour le header comme les autres pages */
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
        
        @media (max-width: 768px) {
            .logo-container {
                flex-direction: column;
                gap: 15px;
            }
            
            .logo-circle {
                width: 80px;
                height: 80px;
            }
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
            grid-template-columns: repeat(6, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            color: white;
            padding: 1.5rem 1rem;
            border-radius: 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        /* Couleurs spécifiques pour chaque type de KPI - couleurs unies */
        .stat-total {
            background: var(--senegal-vert);
            color: white;
            box-shadow: 0 10px 30px rgba(45, 90, 61, 0.3);
        }

        .stat-total:hover {
            box-shadow: 0 15px 40px rgba(45, 90, 61, 0.4);
        }

        .stat-danger {
            background: var(--senegal-rouge);
            color: white;
            box-shadow: 0 10px 30px rgba(200, 67, 78, 0.3);
        }

        .stat-danger:hover {
            box-shadow: 0 15px 40px rgba(200, 67, 78, 0.4);
        }

        .stat-warning {
            background: var(--senegal-jaune);
            color: #000000;
            box-shadow: 0 10px 30px rgba(244, 224, 77, 0.3);
        }

        .stat-warning:hover {
            box-shadow: 0 15px 40px rgba(244, 224, 77, 0.4);
        }

        .stat-info {
            background: #17a2b8;
            color: white;
            box-shadow: 0 10px 30px rgba(23, 162, 184, 0.3);
        }

        .stat-info:hover {
            box-shadow: 0 15px 40px rgba(23, 162, 184, 0.4);
        }

        .stat-success {
            background: var(--senegal-vert);
            color: white;
            box-shadow: 0 10px 30px rgba(45, 90, 61, 0.3);
        }

        .stat-success:hover {
            box-shadow: 0 15px 40px rgba(45, 90, 61, 0.4);
        }
        
        /* Correction du texte dans les KPI - Assurer la visibilité */
        .stat-number {
            color: inherit !important;
            font-weight: 700;
            font-size: 2.5rem;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        
        .stat-label {
            color: inherit !important;
            font-weight: 600;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        
        .stat-percentage {
            color: inherit !important;
            font-weight: 500;
            font-size: 0.9rem;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        
        /* Assurer que le texte blanc est visible sur les fonds colorés */
        .stat-danger .stat-number,
        .stat-danger .stat-label,
        .stat-danger .stat-percentage {
            color: white !important;
        }

        .stat-dark {
            background: #343a40;
            color: white;
            box-shadow: 0 10px 30px rgba(52, 58, 64, 0.3);
        }

        .stat-dark:hover {
            box-shadow: 0 15px 40px rgba(52, 58, 64, 0.4);
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
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .stat-card i {
            font-size: 1.2rem;
            opacity: 0.8;
            margin-right: 0.5rem;
        }

        .stat-percentage {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-top: 0.5rem;
            font-weight: 500;
        }

        .stat-label {
            font-size: 0.95rem;
            opacity: 0.95;
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

        /* Styles pour la grille de cartes */
        .demandes-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .demande-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            overflow: hidden;
            border: 1px solid #e9ecef;
        }

        .demande-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }

        .demande-card .card-header {
            background: var(--senegal-vert);
            color: white;
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .demande-numero {
            font-weight: 600;
            font-size: 1rem;
            color: white !important;
        }

        .demande-statut .status-badge {
            background: rgba(255, 255, 255, 0.2) !important;
            color: white !important;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        /* Assurer que tout le texte dans les cartes soit visible */
        .demande-card .card-body {
            color: var(--texte-visible) !important;
        }
        
        .demande-card .card-body h6,
        .demande-card .card-body p,
        .demande-card .card-body span {
            color: var(--texte-visible) !important;
        }

        .demande-card .card-body {
            padding: 1.5rem;
        }

        .demandeur-info {
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #e9ecef;
        }

        .demandeur-nom {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }

        .demandeur-email {
            color: #6c757d;
            margin-bottom: 0;
            font-size: 0.9rem;
        }

        .demande-details {
            space-y: 1rem;
        }

        .detail-item {
            margin-bottom: 1rem;
        }

        .detail-label {
            font-weight: 600;
            color: var(--text-dark);
            display: block;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .detail-value {
            display: flex;
            flex-wrap: wrap;
            gap: 0.25rem;
        }

        .detail-value .badge {
            font-size: 0.8rem;
        }

        .demande-card .card-footer {
            background: #f8f9fa;
            padding: 1rem 1.5rem;
            border-top: 1px solid #e9ecef;
        }

        .actions-buttons {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
        }

        .actions-buttons .btn {
            border-radius: 20px;
            font-size: 0.85rem;
            padding: 0.5rem 1rem;
        }

        /* Styles personnalisés pour les badges de statut */
        .status-badge {
            font-weight: 600;
            font-size: 0.85rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Couleurs spécifiques pour chaque statut */
        .bg-danger.status-badge {
            background: linear-gradient(135deg, #dc3545, #c82333) !important;
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
        }

        .bg-warning.status-badge {
            background: linear-gradient(135deg, #ffc107, #e0a800) !important;
            color: #212529 !important;
            box-shadow: 0 2px 8px rgba(255, 193, 7, 0.3);
        }

        .bg-info.status-badge {
            background: linear-gradient(135deg, #17a2b8, #138496) !important;
            box-shadow: 0 2px 8px rgba(23, 162, 184, 0.3);
        }

        .bg-success.status-badge {
            background: linear-gradient(135deg, #28a745, #1e7e34) !important;
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
        }

        .bg-dark.status-badge {
            background: linear-gradient(135deg, #343a40, #23272b) !important;
            box-shadow: 0 2px 8px rgba(52, 58, 64, 0.3);
        }

        /* Responsive pour les cartes */
        @media (max-width: 1200px) {
            .stats-cards {
                grid-template-columns: repeat(3, 1fr);
                gap: 0.75rem;
            }
        }

        @media (max-width: 768px) {
            .stats-cards {
                grid-template-columns: repeat(2, 1fr);
                gap: 0.75rem;
            }
            
            .stat-card {
                padding: 1rem 0.75rem;
            }
            
            .stat-number {
                font-size: 1.5rem;
            }
            
            .stat-label {
                font-size: 0.85rem;
            }
            
            .stat-percentage {
                font-size: 0.8rem;
            }
            
            .demandes-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .demande-card .card-header {
                padding: 0.75rem 1rem;
                flex-direction: column;
                gap: 0.5rem;
                text-align: center;
            }
            
            .demande-card .card-body {
                padding: 1rem;
            }
            
            .demande-card .card-footer {
                padding: 0.75rem 1rem;
            }
            
            .actions-buttons {
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .demandes-grid {
                grid-template-columns: 1fr;
            }
            
            .actions-buttons {
                flex-direction: column;
            }
            
            .actions-buttons .btn {
                width: 100%;
            }
        }
    </style>

<!-- Header simple avec boutons -->
<div class="container mb-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center bg-light p-3 rounded">
                <div>
                    <h2 class="mb-0 text-primary"><i class="fas fa-list me-2"></i>Liste des Demandes</h2>
                    <small class="text-muted">Administration - Mairie de Khombole</small>
                </div>
                <div>
                    <a href="menu.php" class="btn btn-outline-primary me-2">
                        <i class="fas fa-arrow-left me-1"></i>Retour à l'Accueil
                    </a>
                    <a href="admin_logout.php" class="btn btn-outline-danger">
                        <i class="fas fa-sign-out-alt me-1"></i>Déconnexion
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="main-container">
        <!-- KPI - Indicateurs de Performance (en premier) -->
        <div class="stats-cards">
            <!-- Total des demandes -->
            <div class="stat-card stat-total">
                <div class="stat-number"><?= $totalDemandes ?></div>
                <div class="stat-label">
                    <i class="fas fa-file-alt"></i>
                    Demandes Total
                </div>
            </div>
            
            <!-- En attente -->
            <div class="stat-card stat-info">
                <div class="stat-number"><?= $statsParStatut['en_attente'] ?></div>
                <div class="stat-label">
                    <i class="fas fa-clock"></i>
                    En attente
                </div>
                <div class="stat-percentage">
                    <?= $totalDemandes > 0 ? round(($statsParStatut['en_attente'] / $totalDemandes) * 100, 1) : 0 ?>%
                </div>
            </div>
            
            <!-- En traitement -->
            <div class="stat-card stat-warning">
                <div class="stat-number"><?= $statsParStatut['en_traitement'] ?></div>
                <div class="stat-label">
                    <i class="fas fa-cogs"></i>
                    En traitement
                </div>
                <div class="stat-percentage">
                    <?= $totalDemandes > 0 ? round(($statsParStatut['en_traitement'] / $totalDemandes) * 100, 1) : 0 ?>%
                </div>
            </div>
            
            <!-- Prêtes -->
            <div class="stat-card stat-success">
                <div class="stat-number"><?= $statsParStatut['pret'] ?></div>
                <div class="stat-label">
                    <i class="fas fa-check-circle"></i>
                    Prêt
                </div>
                <div class="stat-percentage">
                    <?= $totalDemandes > 0 ? round(($statsParStatut['pret'] / $totalDemandes) * 100, 1) : 0 ?>%
                </div>
            </div>
            
            <!-- Délivrées -->
            <div class="stat-card stat-success">
                <div class="stat-number"><?= $statsParStatut['delivre'] ?></div>
                <div class="stat-label">
                    <i class="fas fa-check-double"></i>
                    Délivré
                </div>
                <div class="stat-percentage">
                    <?= $totalDemandes > 0 ? round(($statsParStatut['delivre'] / $totalDemandes) * 100, 1) : 0 ?>%
                </div>
            </div>
            
            <!-- Rejetées -->
            <div class="stat-card stat-danger">
                <div class="stat-number"><?= $statsParStatut['rejete'] ?></div>
                <div class="stat-label">
                    <i class="fas fa-times-circle"></i>
                    Rejeté
                </div>
                <div class="stat-percentage">
                    <?= $totalDemandes > 0 ? round(($statsParStatut['rejete'] / $totalDemandes) * 100, 1) : 0 ?>%
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
                        <div class="d-flex flex-column align-items-center justify-content-center h-100">
                            <div id="search-indicator" class="text-muted mb-2" style="display: none;">
                                <i class="fas fa-spinner fa-spin me-2"></i>
                                <small>Recherche...</small>
                            </div>
                            <div id="search-info" class="text-success mb-2">
                                <i class="fas fa-magic me-2"></i>
                                <small>Recherche automatique</small>
                            </div>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="clear-filters" title="Effacer tous les filtres">
                                <i class="fas fa-eraser me-1"></i>
                                <small>Effacer</small>
                            </button>
                        </div>
                        <!-- Bouton caché pour la soumission manuelle si nécessaire -->
                        <button type="submit" class="btn btn-primary w-100" style="display: none;" id="manual-submit">
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
                        <div class="d-flex flex-column align-items-center justify-content-center h-100">
                            <div id="search-indicator" class="text-muted mb-2" style="display: none;">
                                <i class="fas fa-spinner fa-spin me-2"></i>
                                <small>Recherche...</small>
                            </div>
                            <div id="search-info" class="text-success mb-2">
                                <i class="fas fa-magic me-2"></i>
                                <small>Recherche automatique</small>
                            </div>
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="clear-filters" title="Effacer tous les filtres">
                                <i class="fas fa-eraser me-1"></i>
                                Effacer
                            </button>
                        </div>
                        <!-- Bouton caché pour la soumission manuelle si nécessaire -->
                        <button type="submit" class="btn btn-primary w-100" style="display: none;" id="manual-submit">
                            <i class="fas fa-search me-1"></i>
                            Filtrer
                        </button>
                    </div>
                </form>
            </div>

            <!-- Liste des demandes en cartes -->
            <?php if (!empty($demandes)): ?>
                <div class="demandes-grid">
                    <?php foreach ($demandes as $demande): ?>
                        <div class="demande-card">
                            <div class="card-header">
                                <div class="demande-numero">
                                    <i class="fas fa-hashtag me-1"></i>
                                    <?= htmlspecialchars($demande['numero_demande']) ?>
                                </div>
                                <div class="demande-statut">
                                    <span class="badge bg-<?= $statutsColors[$demande['statut']] ?> status-badge">
                                        <?= htmlspecialchars($statutsLabels[$demande['statut']] ?? $demande['statut']) ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <div class="demandeur-info">
                                    <h6 class="demandeur-nom">
                                        <i class="fas fa-user me-2"></i>
                                        <?= htmlspecialchars($demande['prenoms'] . ' ' . $demande['nom']) ?>
                                    </h6>
                                    <p class="demandeur-email">
                                        <i class="fas fa-envelope me-2"></i>
                                        <?= htmlspecialchars($demande['email']) ?>
                                    </p>
                                </div>
                                
                                <div class="demande-details">
                                    <div class="detail-item">
                                        <span class="detail-label">Type d'acte :</span>
                                        <div class="detail-value">
                                            <?php 
                                            // Gérer les types multiples
                                            $types = explode(',', $demande['type_acte']);
                                            foreach ($types as $type): 
                                                $type = trim($type);
                                            ?>
                                                <span class="badge bg-light text-dark me-1 mb-1">
                                                    <?= htmlspecialchars($typesActes[$type] ?? $type) ?>
                                                </span>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="detail-item">
                                        <span class="detail-label">Exemplaires :</span>
                                        <div class="detail-value">
                                            <?php 
                                            // Gérer les exemplaires multiples (JSON)
                                            $exemplaires = json_decode($demande['nombre_exemplaires'], true);
                                            if (is_array($exemplaires)): 
                                                foreach ($exemplaires as $type => $nombre):
                                            ?>
                                                <span class="badge bg-info me-1 mb-1">
                                                    <?= htmlspecialchars($typesActes[$type] ?? $type) ?>: <?= $nombre ?>
                                                </span>
                                            <?php 
                                                endforeach;
                                            else: 
                                            ?>
                                                <span class="badge bg-info">
                                                    <?= htmlspecialchars($demande['nombre_exemplaires']) ?> ex.
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="detail-item">
                                        <span class="detail-label">Date de soumission :</span>
                                        <div class="detail-value">
                                            <i class="fas fa-calendar me-1"></i>
                                            <?= date('d/m/Y à H:i', strtotime($demande['date_soumission'])) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="card-footer">
                                <div class="actions-buttons">
                                    <a href="detail_demande.php?numero=<?= urlencode($demande['numero_demande']) ?>&retour=liste" 
                                       class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-eye me-1"></i>Détails
                                    </a>
                                    <button type="button" class="btn btn-outline-success btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#modalStatut"
                                            data-demande-id="<?= $demande['id'] ?>"
                                            data-numero="<?= htmlspecialchars($demande['numero_demande']) ?>"
                                            data-statut-actuel="<?= htmlspecialchars($demande['statut']) ?>">
                                        <i class="fas fa-edit me-1"></i>Modifier
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
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
        
        // Fonction pour soumettre le formulaire avec indication visuelle
        function submitFormWithLoader(form) {
            // Afficher l'indicateur de recherche
            const searchIndicator = document.getElementById('search-indicator');
            const searchInfo = document.getElementById('search-info');
            
            if (searchIndicator && searchInfo) {
                searchIndicator.style.display = 'block';
                searchInfo.style.display = 'none';
            }
            
            // Soumettre le formulaire
            form.submit();
        }
        
        // Fonction pour réinitialiser l'indicateur (au cas où la page ne se recharge pas)
        function resetSearchIndicator() {
            const searchIndicator = document.getElementById('search-indicator');
            const searchInfo = document.getElementById('search-info');
            
            if (searchIndicator && searchInfo) {
                searchIndicator.style.display = 'none';
                searchInfo.style.display = 'block';
            }
        }
        
        // Recherche automatique pour le champ de texte
        document.querySelector('input[name="recherche"]').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                submitFormWithLoader(this.form);
            }, 500); // Réduit de 1000ms à 500ms pour plus de réactivité
        });
        
        // Recherche automatique pour les sélecteurs de statut et type
        document.querySelector('select[name="statut"]').addEventListener('change', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                submitFormWithLoader(this.form);
            }, 100); // Très rapide pour les sélecteurs
        });
        
        document.querySelector('select[name="type"]').addEventListener('change', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                submitFormWithLoader(this.form);
            }, 100); // Très rapide pour les sélecteurs
        });
        
        // Bouton pour effacer tous les filtres
        document.getElementById('clear-filters').addEventListener('click', function() {
            const form = document.querySelector('form[method="GET"]');
            
            // Effacer tous les champs
            form.querySelector('input[name="recherche"]').value = '';
            form.querySelector('select[name="statut"]').value = '';
            form.querySelector('select[name="type"]').value = '';
            
            // Soumettre le formulaire pour afficher tous les résultats
            submitFormWithLoader(form);
        });

        // Gestion du modal de modification de statut
        const modalStatut = document.getElementById('modalStatut');
        const statutsColors = {
            'en_attente': 'danger',      // Rouge pour En attente
            'en_traitement': 'warning',  // Orange pour En traitement
            'pret': 'info',             // Bleu pour Prêt
            'delivre': 'success',       // Vert pour Délivré
            'rejete': 'dark'            // Gris foncé pour Rejeté
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
