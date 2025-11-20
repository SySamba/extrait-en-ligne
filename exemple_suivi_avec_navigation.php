<?php
/**
 * Exemple d'utilisation du système de navigation
 * Page de suivi des demandes d'actes - VERSION PUBLIQUE
 * Mairie de Khombole
 */

// Configuration de la page
$pageTitle = 'Suivi de Demande';
$breadcrumbs = [
    ['title' => 'Services', 'url' => 'menu.php#services'],
    ['title' => 'Suivi de demande']
];

// Inclure le header public
require_once 'public_header.php';

// Connexion à la base de données
require_once 'db_connection.php';

$erreur = null;
$demande = null;

if (isset($_POST['numero_registre']) && !empty($_POST['numero_registre'])) {
    try {
        $pdo = createPDOConnection();
        
        // Recherche par numéro de registre uniquement
        $numeroRegistre = $_POST['numero_registre'];
        $sql = "SELECT * FROM demandes_actes WHERE numero_registre = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$numeroRegistre]);
        $demande = $stmt->fetch();
        
        if (!$demande) {
            $erreur = "Aucune demande trouvée avec ce numéro de registre.";
        }
        
    } catch (PDOException $e) {
        $erreur = "Erreur de connexion à la base de données.";
    }
}

// Charger les configurations
require_once 'config.php';
$typesActes = getTypesActes();
$statutsLabels = getStatutsLabels();
$statutsColors = getStatutsColors();
?>

<!-- Section Hero -->
<div class="hero-section bg-gradient text-white py-5 mb-5">
    <div class="container text-center">
        <h1 class="display-5 fw-bold mb-3">
            <i class="fas fa-search me-3"></i>Suivi de votre demande
        </h1>
        <p class="lead">
            Saisissez votre numéro de registre pour connaître l'état d'avancement de votre demande d'acte.
        </p>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <!-- Formulaire de recherche -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-search me-2"></i>Rechercher votre demande
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" class="needs-validation" novalidate>
                    <div class="mb-3">
                        <label for="numero_registre" class="form-label">
                            <i class="fas fa-hashtag me-1"></i>Numéro de registre *
                        </label>
                        <input type="text" class="form-control form-control-lg" id="numero_registre" 
                               name="numero_registre" required
                               value="<?= htmlspecialchars($_POST['numero_registre'] ?? '') ?>"
                               placeholder="Ex: REG-2024-001">
                        <div class="form-text">
                            <i class="fas fa-info-circle me-1"></i>
                            Ce numéro vous a été communiqué lors de votre demande initiale.
                        </div>
                        <div class="invalid-feedback">
                            Veuillez saisir votre numéro de registre.
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-search me-2"></i>Rechercher ma demande
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Messages -->
        <?php if (!empty($erreur)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?= htmlspecialchars($erreur) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Résultats de la recherche -->
        <?php if ($demande): ?>
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>
                        Demande trouvée : <?= htmlspecialchars($demande['numero_demande']) ?>
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Informations générales -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary">
                                <i class="fas fa-user me-1"></i>Informations de la demande
                            </h6>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>Type d'acte :</strong></td>
                                    <td><?= htmlspecialchars($typesActes[$demande['type_acte']]['label'] ?? $demande['type_acte']) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Demandeur :</strong></td>
                                    <td><?= htmlspecialchars($demande['nom_complet']) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Date de soumission :</strong></td>
                                    <td><?= date('d/m/Y à H:i', strtotime($demande['date_soumission'])) ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Montant :</strong></td>
                                    <td><strong><?= number_format($demande['montant'], 0, ',', ' ') ?> FCFA</strong></td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary">
                                <i class="fas fa-info-circle me-1"></i>Statut actuel
                            </h6>
                            <div class="text-center p-4 bg-light rounded">
                                <span class="badge bg-<?= $statutsColors[$demande['statut']] ?> fs-6 p-3">
                                    <?= htmlspecialchars($statutsLabels[$demande['statut']] ?? $demande['statut']) ?>
                                </span>
                                
                                <?php if ($demande['statut'] === 'pret'): ?>
                                    <div class="mt-3">
                                        <div class="alert alert-success">
                                            <i class="fas fa-check-circle me-2"></i>
                                            <strong>Votre document est prêt !</strong><br>
                                            Vous pouvez venir le retirer à la mairie.
                                        </div>
                                    </div>
                                <?php elseif ($demande['statut'] === 'delivre'): ?>
                                    <div class="mt-3">
                                        <div class="alert alert-info">
                                            <i class="fas fa-handshake me-2"></i>
                                            <strong>Document remis</strong><br>
                                            Votre demande a été finalisée.
                                        </div>
                                    </div>
                                <?php elseif ($demande['statut'] === 'rejete'): ?>
                                    <div class="mt-3">
                                        <div class="alert alert-danger">
                                            <i class="fas fa-times-circle me-2"></i>
                                            <strong>Demande rejetée</strong>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Timeline du traitement -->
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="fas fa-history me-1"></i>Historique du traitement
                    </h6>
                    
                    <div class="timeline">
                        <!-- Soumission -->
                        <div class="timeline-item completed">
                            <div class="timeline-marker bg-success">
                                <i class="fas fa-paper-plane text-white"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="fw-bold text-success mb-1">Demande soumise</h6>
                                <p class="text-muted mb-1">Votre demande a été enregistrée dans notre système</p>
                                <small class="text-muted">
                                    <?= date('d/m/Y à H:i', strtotime($demande['date_soumission'])) ?>
                                </small>
                            </div>
                        </div>

                        <!-- En traitement -->
                        <?php if (in_array($demande['statut'], ['en_traitement', 'pret', 'delivre'])): ?>
                            <div class="timeline-item completed">
                                <div class="timeline-marker bg-info">
                                    <i class="fas fa-cog text-white"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="fw-bold text-info mb-1">En cours de traitement</h6>
                                    <p class="text-muted mb-1">Votre demande est en cours de traitement par nos services</p>
                                    <?php if ($demande['date_traitement']): ?>
                                        <small class="text-muted">
                                            <?= date('d/m/Y à H:i', strtotime($demande['date_traitement'])) ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="timeline-item">
                                <div class="timeline-marker bg-light">
                                    <i class="fas fa-cog text-muted"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="fw-bold text-muted mb-1">En attente de traitement</h6>
                                    <p class="text-muted mb-1">Votre demande sera traitée prochainement</p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Prêt -->
                        <?php if (in_array($demande['statut'], ['pret', 'delivre'])): ?>
                            <div class="timeline-item completed">
                                <div class="timeline-marker bg-warning">
                                    <i class="fas fa-check text-white"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="fw-bold text-warning mb-1">Document prêt</h6>
                                    <p class="text-muted mb-1">Votre document est prêt pour retrait</p>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Livré -->
                        <?php if ($demande['statut'] === 'delivre'): ?>
                            <div class="timeline-item completed">
                                <div class="timeline-marker bg-primary">
                                    <i class="fas fa-handshake text-white"></i>
                                </div>
                                <div class="timeline-content">
                                    <h6 class="fw-bold text-primary mb-1">Document remis</h6>
                                    <p class="text-muted mb-1">Votre document vous a été remis</p>
                                    <?php if ($demande['date_livraison']): ?>
                                        <small class="text-muted">
                                            <?= date('d/m/Y à H:i', strtotime($demande['date_livraison'])) ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Rejeté -->
                        <?php if ($demande['statut'] === 'rejete'): ?>
                            <div class="timeline-item completed">
                                <div class="timeline-marker bg-danger">
                                    <i class="fas fa-times text-white"></i>
                                </div>
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

                    <!-- Actions -->
                    <div class="mt-4 text-center">
                        <a href="suivi_demande.php" class="btn btn-outline-primary me-2">
                            <i class="fas fa-search me-1"></i>Nouvelle recherche
                        </a>
                        
                        <?php if ($demande['statut'] === 'pret'): ?>
                            <a href="menu.php#contact" class="btn btn-success">
                                <i class="fas fa-map-marker-alt me-1"></i>Informations de retrait
                            </a>
                        <?php endif; ?>
                        
                        <a href="detail_demande.php?registre=<?= urlencode($demande['numero_registre']) ?>" 
                           class="btn btn-outline-info">
                            <i class="fas fa-eye me-1"></i>Voir tous les détails
                        </a>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Aide et informations -->
        <div class="card mt-4 border-0 bg-light">
            <div class="card-body text-center">
                <h6 class="fw-bold mb-3">
                    <i class="fas fa-question-circle me-2"></i>Besoin d'aide ?
                </h6>
                <p class="text-muted mb-3">
                    Si vous ne trouvez pas votre demande ou si vous avez des questions, 
                    n'hésitez pas à nous contacter.
                </p>
                <div class="row">
                    <div class="col-md-4">
                        <a href="menu.php#contact" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-phone me-1"></i>Nous contacter
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="demande_acte.php" class="btn btn-outline-success btn-sm">
                            <i class="fas fa-plus me-1"></i>Nouvelle demande
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="menu.php#faq" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-question me-1"></i>FAQ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 2rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 1rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 2rem;
}

.timeline-marker {
    position: absolute;
    left: -2rem;
    top: 0;
    width: 2rem;
    height: 2rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 3px solid white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.timeline-content {
    background: white;
    padding: 1rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    border-left: 3px solid #dee2e6;
}

.timeline-item.completed .timeline-content {
    border-left-color: var(--bs-success);
}

.hero-section {
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
}
</style>

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

// Focus automatique sur le champ de recherche
document.getElementById('numero_registre').focus();
</script>

<?php
// Inclure le footer public
require_once 'public_footer.php';
?>
