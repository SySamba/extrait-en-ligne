<?php
/**
 * Page d'administration pour traiter les demandes - ADMIN SEULEMENT
 * Mairie de Khombole
 */

// Vérifier l'authentification admin
require_once 'session_manager.php';
verifierConnexionAdmin();

// Logger l'accès à la page
loggerActionAdmin('Accès à la page de traitement des demandes');

require_once 'config.php';

$message = '';
$erreur = '';
$demande = null;

// Récupération de la demande à traiter
if (isset($_GET['id'])) {
    $demandeId = (int)$_GET['id'];
    
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT * FROM demandes_actes WHERE id = ?");
        $stmt->execute([$demandeId]);
        $demande = $stmt->fetch();
        
        if (!$demande) {
            $erreur = "Demande non trouvée.";
        }
    } catch (Exception $e) {
        $erreur = "Erreur lors de la récupération de la demande.";
        logActivity("Erreur récupération demande ID $demandeId: " . $e->getMessage(), 'ERROR');
    }
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $demande) {
    $action = $_POST['action'] ?? '';
    $commentaire = trim($_POST['commentaire'] ?? '');
    
    // Vérifier le token CSRF
    if (!verifierTokenCSRF($_POST['csrf_token'] ?? '')) {
        $erreur = "Token de sécurité invalide.";
    } else {
        try {
            $pdo = getDBConnection();
            
            // Inclure le gestionnaire d'emails
            require_once 'email_manager.php';
            $emailManager = new EmailManager();
            
            switch ($action) {
                case 'accepter':
                    $stmt = $pdo->prepare("UPDATE demandes_actes SET statut = 'en_traitement', commentaire_admin = ?, date_traitement = NOW() WHERE id = ?");
                    $stmt->execute([$commentaire, $demandeId]);
                    $message = "Demande acceptée et mise en traitement.";
                    loggerActionAdmin("Demande acceptée", "ID: $demandeId");
                    
                    // Envoyer email de validation
                    $emailEnvoye = $emailManager->envoyerValidationDemande($demande, $commentaire);
                    if ($emailEnvoye) {
                        error_log("Email de validation envoyé pour demande ID: $demandeId");
                    } else {
                        error_log("Erreur envoi email de validation pour demande ID: $demandeId");
                    }
                    break;
                    
                case 'terminer':
                    $stmt = $pdo->prepare("UPDATE demandes_actes SET statut = 'pret', commentaire_admin = ?, date_traitement = NOW() WHERE id = ?");
                    $stmt->execute([$commentaire, $demandeId]);
                    $message = "Demande terminée et prête pour retrait.";
                    loggerActionAdmin("Demande terminée", "ID: $demandeId");
                    
                    // Envoyer email de demande prête
                    $emailEnvoye = $emailManager->envoyerDemandePrete($demande, $commentaire);
                    if ($emailEnvoye) {
                        error_log("Email de demande prête envoyé pour demande ID: $demandeId");
                    } else {
                        error_log("Erreur envoi email de demande prête pour demande ID: $demandeId");
                    }
                    break;
                    
                case 'livrer':
                    $stmt = $pdo->prepare("UPDATE demandes_actes SET statut = 'delivre', commentaire_admin = ?, date_livraison = NOW() WHERE id = ?");
                    $stmt->execute([$commentaire, $demandeId]);
                    $message = "Demande marquée comme livrée.";
                    loggerActionAdmin("Demande livrée", "ID: $demandeId");
                    break;
                    
                case 'rejeter':
                    if (empty($commentaire)) {
                        $erreur = "Un motif de rejet est obligatoire.";
                    } else {
                        $stmt = $pdo->prepare("UPDATE demandes_actes SET statut = 'rejete', commentaire_admin = ?, date_traitement = NOW() WHERE id = ?");
                        $stmt->execute([$commentaire, $demandeId]);
                        $message = "Demande rejetée.";
                        loggerActionAdmin("Demande rejetée", "ID: $demandeId - Motif: $commentaire");
                        
                        // Envoyer email de rejet
                        $emailEnvoye = $emailManager->envoyerRejetDemande($demande, $commentaire);
                        if ($emailEnvoye) {
                            error_log("Email de rejet envoyé pour demande ID: $demandeId");
                        } else {
                            error_log("Erreur envoi email de rejet pour demande ID: $demandeId");
                        }
                    }
                    break;
                    
                default:
                    $erreur = "Action non reconnue.";
            }
            
            // Recharger la demande après modification
            if (empty($erreur)) {
                $stmt = $pdo->prepare("SELECT * FROM demandes_actes WHERE id = ?");
                $stmt->execute([$demandeId]);
                $demande = $stmt->fetch();
            }
            
        } catch (Exception $e) {
            $erreur = "Erreur lors du traitement de la demande.";
            logActivity("Erreur traitement demande ID $demandeId: " . $e->getMessage(), 'ERROR');
        }
    }
}

// Récupérer les types d'actes et statuts
$typesActes = getTypesActes();
$statutsLabels = getStatutsLabels();
$statutsColors = getStatutsColors();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Traiter Demande - Admin Mairie de Khombole</title>
    
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
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background-color: #f8f9fa;
        }

        .admin-header {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            padding: 1rem 0;
            margin-bottom: 2rem;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .btn-admin {
            border-radius: 10px;
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <!-- Header Admin -->
    <div class="admin-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="mb-0">
                        <i class="fas fa-tasks me-2"></i>
                        Traitement des Demandes
                    </h2>
                </div>
                <div class="col-md-6 text-end">
                    <a href="liste_demandes.php" class="btn btn-light btn-admin me-2">
                        <i class="fas fa-list me-1"></i>Liste des demandes
                    </a>
                    <a href="admin_logout.php" class="btn btn-outline-light btn-admin">
                        <i class="fas fa-sign-out-alt me-1"></i>Déconnexion
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Messages -->
        <?php if (!empty($message)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>
                <?= htmlspecialchars($message) ?>
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

        <?php if (!$demande): ?>
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4>Aucune demande sélectionnée</h4>
                    <p class="text-muted">Veuillez sélectionner une demande à traiter depuis la liste.</p>
                    <a href="liste_demandes.php" class="btn btn-primary btn-admin">
                        <i class="fas fa-list me-1"></i>Voir la liste des demandes
                    </a>
                </div>
            </div>
        <?php else: ?>
            <!-- Détails de la demande -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">
                                <i class="fas fa-file-alt me-2"></i>
                                Demande #<?= htmlspecialchars($demande['numero_demande']) ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Type d'acte :</strong> <?= htmlspecialchars($typesActes[$demande['type_acte']]['label'] ?? $demande['type_acte']) ?></p>
                                    <p><strong>Demandeur :</strong> <?= htmlspecialchars($demande['nom_complet']) ?></p>
                                    <p><strong>Email :</strong> <?= htmlspecialchars($demande['email']) ?></p>
                                    <p><strong>Téléphone :</strong> <?= htmlspecialchars($demande['telephone']) ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Date de soumission :</strong> <?= date('d/m/Y H:i', strtotime($demande['date_soumission'])) ?></p>
                                    <p><strong>Statut actuel :</strong> 
                                        <span class="status-badge bg-<?= $statutsColors[$demande['statut']] ?> text-white">
                                            <?= htmlspecialchars($statutsLabels[$demande['statut']] ?? $demande['statut']) ?>
                                        </span>
                                    </p>
                                    <p><strong>Montant :</strong> <?= number_format($demande['montant'], 0, ',', ' ') ?> FCFA</p>
                                </div>
                            </div>
                            
                            <?php if ($demande['commentaire_admin']): ?>
                                <div class="alert alert-info mt-3">
                                    <strong>Commentaire précédent :</strong><br>
                                    <?= nl2br(htmlspecialchars($demande['commentaire_admin'])) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-cogs me-2"></i>Actions
                            </h6>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="csrf_token" value="<?= genererTokenCSRF() ?>">
                                
                                <div class="mb-3">
                                    <label for="commentaire" class="form-label">Commentaire</label>
                                    <textarea class="form-control" id="commentaire" name="commentaire" rows="4" 
                                              placeholder="Ajoutez un commentaire..."></textarea>
                                </div>

                                <div class="d-grid gap-2">
                                    <?php if ($demande['statut'] === 'en_attente'): ?>
                                        <button type="submit" name="action" value="accepter" class="btn btn-success btn-admin">
                                            <i class="fas fa-check me-1"></i>Accepter
                                        </button>
                                    <?php endif; ?>

                                    <?php if (in_array($demande['statut'], ['en_attente', 'en_traitement'])): ?>
                                        <button type="submit" name="action" value="terminer" class="btn btn-info btn-admin">
                                            <i class="fas fa-flag-checkered me-1"></i>Terminer
                                        </button>
                                    <?php endif; ?>

                                    <?php if ($demande['statut'] === 'pret'): ?>
                                        <button type="submit" name="action" value="livrer" class="btn btn-primary btn-admin">
                                            <i class="fas fa-truck me-1"></i>Marquer comme livré
                                        </button>
                                    <?php endif; ?>

                                    <?php if (!in_array($demande['statut'], ['delivre', 'rejete'])): ?>
                                        <button type="submit" name="action" value="rejeter" class="btn btn-danger btn-admin"
                                                onclick="return confirm('Êtes-vous sûr de vouloir rejeter cette demande ?')">
                                            <i class="fas fa-times me-1"></i>Rejeter
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
