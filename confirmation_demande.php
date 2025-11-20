<?php
session_start();

// Vérifier si une demande a été soumise
if (!isset($_SESSION['demande_success'])) {
    header('Location: demande_acte.php');
    exit;
}

$demande = $_SESSION['demande_success'];

// Nettoyer la session
unset($_SESSION['demande_success']);

// Types d'actes pour l'affichage
$typesActes = [
    'extrait_naissance' => 'Extrait d\'acte de naissance',
    'copie_litterale_naissance' => 'Copie littérale d\'acte de naissance',
    'extrait_mariage' => 'Extrait d\'acte de mariage',
    'certificat_residence' => 'Certificat de résidence',
    'certificat_vie_individuelle' => 'Certificat de vie individuelle',
    'certificat_vie_collective' => 'Certificat de vie collective',
    'certificat_deces' => 'Certificat de décès'
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation de Demande - Mairie de Khombole</title>
    
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
            --success-color: #28a745;
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

        .confirmation-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            padding: 3rem;
            margin-bottom: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .confirmation-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(135deg, var(--success-color), var(--primary-color));
        }

        .success-icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, var(--success-color), #20c997);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            animation: pulse 2s infinite;
            box-shadow: 0 10px 30px rgba(40, 167, 69, 0.3);
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .success-icon i {
            font-size: 3rem;
            color: white;
        }

        .confirmation-title {
            color: var(--success-color);
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .demande-details {
            background: var(--bg-light);
            border-radius: 15px;
            padding: 2rem;
            margin: 2rem 0;
            border-left: 5px solid var(--primary-color);
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #dee2e6;
        }

        .detail-row:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: var(--text-dark);
        }

        .detail-value {
            color: var(--primary-color);
            font-weight: 500;
        }

        .numero-demande {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            padding: 1rem 2rem;
            border-radius: 25px;
            font-size: 1.2rem;
            font-weight: 700;
            letter-spacing: 1px;
            margin: 1.5rem 0;
            display: inline-block;
            box-shadow: 0 5px 15px rgba(11, 132, 62, 0.3);
        }

        .next-steps {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            border-radius: 15px;
            padding: 2rem;
            margin: 2rem 0;
            text-align: left;
        }

        .step-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 1.5rem;
        }

        .step-item:last-child {
            margin-bottom: 0;
        }

        .step-number {
            width: 35px;
            height: 35px;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
            flex-shrink: 0;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border: none;
            border-radius: 25px;
            padding: 1rem 2rem;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(11, 132, 62, 0.3);
            margin: 0.5rem;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(11, 132, 62, 0.4);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            border-radius: 25px;
            padding: 1rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 0.5rem;
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }

        .contact-info {
            background: linear-gradient(135deg, #fff3cd, #ffeaa7);
            border-radius: 15px;
            padding: 1.5rem;
            margin-top: 2rem;
        }

        @media (max-width: 768px) {
            .confirmation-container {
                padding: 2rem 1.5rem;
                margin: 0 10px;
            }
            
            .detail-row {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.5rem;
            }
            
            .logo-container {
                flex-direction: column;
                gap: 10px;
            }
            
            .numero-demande {
                font-size: 1rem;
                padding: 0.75rem 1.5rem;
            }
        }


        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                background: white;
            }
            
            .confirmation-container {
                box-shadow: none;
                border: 1px solid #ddd;
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
                    <h1 class="mb-0 fw-bold fs-4">MAIRIE DE KHOMBOLE</h1>
                    <p class="mb-0">République du Sénégal</p>
                </div>
            </div>
            <div class="text-center">
                <h2 class="fw-bold fs-5">CONFIRMATION DE DEMANDE D'ACTE</h2>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="confirmation-container">
            <!-- Icône de succès -->
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>

            <!-- Titre de confirmation -->
            <h1 class="confirmation-title">Demande Enregistrée avec Succès !</h1>
            
            <p class="lead text-muted mb-4">
                Votre demande d'acte d'état civil a été soumise et enregistrée dans notre système.
            </p>

            <!-- Message important -->
            <div class="alert alert-warning mb-4">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>TRÈS IMPORTANT :</strong> Notez et conservez précieusement votre numéro de demande ci-dessous. 
                Vous en aurez besoin pour suivre l'évolution de votre demande.
            </div>

            <!-- Numéro de demande -->
            <div class="numero-demande">
                <i class="fas fa-hashtag me-2"></i>
                <?= htmlspecialchars($demande['numero_demande']) ?>
            </div>

            <div class="alert alert-info mt-3">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Comment utiliser ce numéro :</strong><br>
                • Gardez-le dans un endroit sûr<br>
                • Utilisez-le pour suivre votre demande en ligne<br>
                • Présentez-le lors du retrait de votre acte<br>
                • Mentionnez-le dans toute correspondance avec la mairie
            </div>

            <!-- Détails de la demande -->
            <div class="demande-details">
                <h5 class="fw-bold text-primary mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    Détails de votre demande
                </h5>
                
                <div class="detail-row">
                    <span class="detail-label">Demandeur :</span>
                    <span class="detail-value"><?= htmlspecialchars($demande['nom_complet']) ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Types d'actes demandés :</span>
                    <div class="detail-value">
                        <?php if (isset($demande['types_actes']) && is_array($demande['types_actes'])): ?>
                            <?php foreach ($demande['types_actes'] as $type): ?>
                                <?php 
                                $nomType = $typesActes[$type] ?? $type;
                                $nbExemplaires = $demande['exemplaires'][$type] ?? 1;
                                ?>
                                <div class="mb-1">
                                    <strong><?= htmlspecialchars($nomType) ?></strong>
                                    <span class="badge bg-primary ms-2"><?= $nbExemplaires ?> exemplaire<?= $nbExemplaires > 1 ? 's' : '' ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <span>Information non disponible</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Adresse e-mail :</span>
                    <span class="detail-value"><?= htmlspecialchars($demande['email']) ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Date de soumission :</span>
                    <span class="detail-value"><?= date('d/m/Y à H:i') ?></span>
                </div>
                
                <div class="detail-row">
                    <span class="detail-label">Statut :</span>
                    <span class="detail-value">
                        <span class="badge bg-warning text-dark">En attente de traitement</span>
                    </span>
                </div>
            </div>

            <!-- Prochaines étapes -->
            <div class="next-steps">
                <h5 class="fw-bold text-primary mb-3">
                    <i class="fas fa-list-ol me-2"></i>
                    Prochaines étapes
                </h5>
                
                <div class="step-item">
                    <div class="step-number">1</div>
                    <div>
                        <strong>Confirmation par e-mail</strong><br>
                        <small class="text-muted">Un e-mail de confirmation a été envoyé à votre adresse.</small>
                    </div>
                </div>
                
                <div class="step-item">
                    <div class="step-number">2</div>
                    <div>
                        <strong>Traitement de la demande</strong><br>
                        <small class="text-muted">Nos services vont examiner votre demande sous 48-72 heures.</small>
                    </div>
                </div>
                
                <div class="step-item">
                    <div class="step-number">3</div>
                    <div>
                        <strong>Notification de disponibilité</strong><br>
                        <small class="text-muted">Vous recevrez un e-mail dès que votre acte sera prêt.</small>
                    </div>
                </div>
                
                <div class="step-item">
                    <div class="step-number">4</div>
                    <div>
                        <strong>Retrait ou envoi</strong><br>
                        <small class="text-muted">Récupérez votre acte selon le mode de délivrance choisi.</small>
                    </div>
                </div>
            </div>

            <!-- Informations de contact -->
            <div class="contact-info">
                <h6 class="fw-bold text-dark mb-2">
                    <i class="fas fa-phone me-2"></i>
                    Besoin d'aide ?
                </h6>
                <p class="mb-1"><strong>Téléphone :</strong> +221 33 624 52 13 63</p>
                <p class="mb-1"><strong>E-mail :</strong> mairiedekhombole@gmail.com</p>
                <p class="mb-0"><strong>Horaires :</strong> Lundi - Vendredi : 8h00 - 17h00</p>
            </div>

            <!-- Boutons d'action -->
            <div class="mt-4 no-print">
                <a href="demande_acte.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Nouvelle demande
                </a>
                
                
                <a href="suivi_demande.php?numero=<?= urlencode($demande['numero_demande']) ?>" class="btn btn-outline-primary">
                    <i class="fas fa-search me-2"></i>
                    Suivre ma demande
                </a>
            </div>
        </div>
    </div>


    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Animation d'entrée
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.querySelector('.confirmation-container');
            container.style.opacity = '0';
            container.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                container.style.transition = 'all 0.8s ease';
                container.style.opacity = '1';
                container.style.transform = 'translateY(0)';
            }, 100);
        });

        // Copier le numéro de demande
        document.querySelector('.numero-demande').addEventListener('click', function() {
            const numeroText = this.textContent.trim();
            navigator.clipboard.writeText(numeroText).then(function() {
                // Créer une notification temporaire
                const notification = document.createElement('div');
                notification.className = 'alert alert-success position-fixed';
                notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; opacity: 0; transition: opacity 0.3s ease;';
                notification.innerHTML = '<i class="fas fa-check me-2"></i>Numéro copié dans le presse-papiers !';
                
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.style.opacity = '1';
                }, 100);
                
                setTimeout(() => {
                    notification.style.opacity = '0';
                    setTimeout(() => {
                        document.body.removeChild(notification);
                    }, 300);
                }, 3000);
            });
        });

        // Ajouter un tooltip au numéro de demande
        document.querySelector('.numero-demande').title = 'Cliquez pour copier le numéro';
        document.querySelector('.numero-demande').style.cursor = 'pointer';
    </script>
</body>
</html>
