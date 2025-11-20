<?php
/**
 * Exemple d'utilisation du système de navigation
 * Page d'affichage de toutes les demandes d'actes - ADMIN SEULEMENT
 * Mairie de Khombole
 */

// Vérifier l'authentification admin
require_once 'session_manager.php';
verifierConnexionAdmin();

// Configuration de la page
$pageTitle = 'Liste des Demandes';
$breadcrumbs = [
    ['title' => 'Demandes', 'url' => 'liste_demandes.php'],
    ['title' => 'Liste complète']
];

// Inclure le header admin
require_once 'admin_header.php';

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
                
                $succes = "Statut de la demande #{$ancienneDemande['numero_demande']} mis à jour avec succès.";
                loggerActionAdmin("Modification statut demande", "ID: $demandeId, Ancien: {$ancienneDemande['statut']}, Nouveau: $nouveauStatut");
            } else {
                $erreur = "Demande non trouvée.";
            }
        } catch (Exception $e) {
            $erreur = "Erreur lors de la mise à jour du statut.";
            logActivity("Erreur modification statut: " . $e->getMessage(), 'ERROR');
        }
    } else {
        $erreur = "Données invalides pour la modification du statut.";
    }
}

// Pagination et filtres
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
        $whereConditions[] = "(numero_demande LIKE ? OR nom_complet LIKE ? OR email LIKE ?)";
        $searchTerm = "%$recherche%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
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
    
} catch (Exception $e) {
    $erreur = "Erreur lors de la récupération des demandes.";
    logActivity("Erreur récupération demandes: " . $e->getMessage(), 'ERROR');
}

// Calculer la pagination
$totalPages = ceil($totalDemandes / $parPage);

// Types d'actes et statuts pour les filtres
require_once 'config.php';
$typesActes = getTypesActes();
$statutsLabels = getStatutsLabels();
$statutsColors = getStatutsColors();
?>

<!-- Messages -->
<?php if (!empty($succes)): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i>
        <?= htmlspecialchars($succes) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (!empty($erreur)): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <?= htmlspecialchars($erreur) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Statistiques rapides -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card border-0 bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-0"><?= $totalDemandes ?></h4>
                        <small>Total Demandes</small>
                    </div>
                    <i class="fas fa-file-alt fa-2x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    <!-- Ajouter d'autres statistiques ici -->
</div>

<!-- Filtres et recherche -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-filter me-2"></i>Filtres et Recherche
        </h5>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="recherche" class="form-label">Recherche</label>
                <input type="text" class="form-control" id="recherche" name="recherche" 
                       value="<?= htmlspecialchars($recherche) ?>" 
                       placeholder="Numéro, nom, email...">
            </div>
            
            <div class="col-md-3">
                <label for="statut" class="form-label">Statut</label>
                <select class="form-select" id="statut" name="statut">
                    <option value="">Tous les statuts</option>
                    <?php foreach ($statutsLabels as $statut => $label): ?>
                        <option value="<?= $statut ?>" <?= $filtreStatut === $statut ? 'selected' : '' ?>>
                            <?= htmlspecialchars($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="type" class="form-label">Type d'acte</label>
                <select class="form-select" id="type" name="type">
                    <option value="">Tous les types</option>
                    <?php foreach ($typesActes as $type => $info): ?>
                        <option value="<?= $type ?>" <?= $filtreType === $type ? 'selected' : '' ?>>
                            <?= htmlspecialchars($info['label']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-search me-1"></i>Filtrer
                </button>
                <a href="liste_demandes.php" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i>Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Actions rapides -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Liste des Demandes (<?= $totalDemandes ?>)</h4>
    <div>
        <a href="admin_traiter_demande.php" class="btn btn-success">
            <i class="fas fa-plus me-1"></i>Traiter une demande
        </a>
        <a href="?export=excel" class="btn btn-outline-primary">
            <i class="fas fa-file-excel me-1"></i>Export Excel
        </a>
    </div>
</div>

<!-- Tableau des demandes -->
<div class="card">
    <div class="card-body p-0">
        <?php if (empty($demandes)): ?>
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                <h5>Aucune demande trouvée</h5>
                <p class="text-muted">Aucune demande ne correspond aux critères de recherche.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Numéro</th>
                            <th>Demandeur</th>
                            <th>Type d'acte</th>
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Montant</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($demandes as $demande): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($demande['numero_demande']) ?></strong>
                                </td>
                                <td>
                                    <div>
                                        <strong><?= htmlspecialchars($demande['nom_complet']) ?></strong><br>
                                        <small class="text-muted"><?= htmlspecialchars($demande['email']) ?></small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">
                                        <?= htmlspecialchars($typesActes[$demande['type_acte']]['label'] ?? $demande['type_acte']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?= date('d/m/Y H:i', strtotime($demande['date_soumission'])) ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $statutsColors[$demande['statut']] ?>">
                                        <?= htmlspecialchars($statutsLabels[$demande['statut']] ?? $demande['statut']) ?>
                                    </span>
                                </td>
                                <td>
                                    <strong><?= number_format($demande['montant'], 0, ',', ' ') ?> FCFA</strong>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="admin_traiter_demande.php?id=<?= $demande['id'] ?>" 
                                           class="btn btn-outline-primary" title="Traiter">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="detail_demande.php?numero=<?= urlencode($demande['numero_demande']) ?>" 
                                           class="btn btn-outline-info" title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Pagination -->
<?php if ($totalPages > 1): ?>
    <nav aria-label="Pagination" class="mt-4">
        <ul class="pagination justify-content-center">
            <?php if ($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page - 1 ?><?= !empty($filtreStatut) ? '&statut=' . urlencode($filtreStatut) : '' ?><?= !empty($filtreType) ? '&type=' . urlencode($filtreType) : '' ?><?= !empty($recherche) ? '&recherche=' . urlencode($recherche) : '' ?>">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </li>
            <?php endif; ?>
            
            <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?><?= !empty($filtreStatut) ? '&statut=' . urlencode($filtreStatut) : '' ?><?= !empty($filtreType) ? '&type=' . urlencode($filtreType) : '' ?><?= !empty($recherche) ? '&recherche=' . urlencode($recherche) : '' ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>
            
            <?php if ($page < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $page + 1 ?><?= !empty($filtreStatut) ? '&statut=' . urlencode($filtreStatut) : '' ?><?= !empty($filtreType) ? '&type=' . urlencode($filtreType) : '' ?><?= !empty($recherche) ? '&recherche=' . urlencode($recherche) : '' ?>">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>

<?php
// Inclure le footer admin
require_once 'admin_footer.php';
?>
