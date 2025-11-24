<?php
/**
 * Visualisation des emails simulés
 * Mairie de Khombole
 */

require_once 'config.php';

$filename = 'emails_simules_' . date('Y-m-d') . '.html';
$filepath = __DIR__ . '/logs/' . $filename;

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emails Simulés - Mairie de Khombole</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .header { 
            background: linear-gradient(135deg, #0b843e, #1e3a8a); 
            color: white; 
            padding: 20px; 
            margin-bottom: 20px; 
        }
        .email-container {
            max-height: 70vh;
            overflow-y: auto;
            border: 1px solid #ddd;
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <h1>📧 Emails Simulés - <?= date('d/m/Y') ?></h1>
            <p>Visualisation des emails qui auraient été envoyés</p>
        </div>
    </div>
    
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>Emails du jour</h5>
                        <small class="text-muted">Fichier : <?= $filename ?></small>
                    </div>
                    <div class="card-body">
                        <?php if (file_exists($filepath) && filesize($filepath) > 0): ?>
                            <div class="email-container">
                                <?= file_get_contents($filepath) ?>
                            </div>
                            
                            <div class="mt-3">
                                <a href="<?= 'logs/' . $filename ?>" target="_blank" class="btn btn-primary">
                                    📄 Ouvrir le fichier complet
                                </a>
                                <button onclick="location.reload()" class="btn btn-secondary">
                                    🔄 Actualiser
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <h6>Aucun email simulé aujourd'hui</h6>
                                <p>Les emails simulés apparaîtront ici quand :</p>
                                <ul>
                                    <li>Une demande est soumise</li>
                                    <li>Un admin change le statut d'une demande</li>
                                    <li>Le test d'email est exécuté</li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h5>Actions</h5>
                    <a href="test_email.php" class="btn btn-success">
                        🧪 Tester l'envoi d'email
                    </a>
                    <a href="demande_acte.php" class="btn btn-primary">
                        📝 Faire une demande test
                    </a>
                    <a href="index.php" class="btn btn-secondary">
                        🏠 Retour à l'accueil
                    </a>
                </div>
                
                <div class="mt-4">
                    <div class="alert alert-warning">
                        <h6>ℹ️ Information</h6>
                        <p>Ces emails sont simulés car :</p>
                        <ul>
                            <li>Votre serveur n'a pas de configuration SMTP</li>
                            <li>La fonction mail() PHP n'est pas configurée</li>
                            <li>C'est un environnement de développement</li>
                        </ul>
                        <p><strong>En production</strong>, avec un serveur mail configuré, ces emails seront envoyés réellement.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
