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
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }

        .header-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            color: white;
            padding: 3rem 0;
            margin-bottom: 3rem;
            border-radius: 0 0 30px 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
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
            background: white;
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
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2rem;
            color: white;
            box-shadow: 0 10px 25px rgba(11, 132, 62, 0.3);
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
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            color: white;
            border-radius: 20px;
            padding: 2rem;
            margin-top: 2rem;
            text-align: center;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .stat-item {
            background: rgba(255,255,255,0.1);
            border-radius: 15px;
            padding: 1.5rem;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
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
                    <h1 class="mb-0 fw-bold display-4">MAIRIE DE KHOMBOLE</h1>
                    <p class="mb-0 fs-4">République du Sénégal</p>
                    <p class="mb-0 fs-6 opacity-75">Services Numériques d'État Civil</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="menu-container">
            <div class="text-center">
                <h2 class="fw-bold text-primary mb-2">Services en Ligne</h2>
                <p class="text-muted fs-5">Accédez facilement à nos services numériques</p>
            </div>

            <div class="menu-grid">
                <!-- Faire une demande -->
                <a href="demande_acte.php" class="menu-card">
                    <div class="menu-icon">
                        <i class="fas fa-file-plus"></i>
                    </div>
                    <h4 class="menu-title">Faire une Demande</h4>
                    <p class="menu-description">
                        Demandez un acte d'état civil en ligne. Formulaire sécurisé et traitement rapide.
                    </p>
                </a>

                <!-- Suivre une demande -->
                <a href="suivi_demande.php" class="menu-card">
                    <div class="menu-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <h4 class="menu-title">Suivre une Demande</h4>
                    <p class="menu-description">
                        Consultez l'état d'avancement de votre demande avec votre numéro de référence.
                    </p>
                </a>

                <!-- Administration -->
                <a href="admin_login.php" class="menu-card">
                    <div class="menu-icon">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <h4 class="menu-title">Administration</h4>
                    <p class="menu-description">
                        Accès réservé aux administrateurs pour la gestion des demandes.
                    </p>
                </a>
            </div>

            <!-- Section statistiques -->
            <div class="stats-section">
                <h4 class="fw-bold mb-0">Statistiques du Service</h4>
                <p class="opacity-75 mb-0">Données en temps réel</p>
                
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-number">
                            <?php
                            // Connexion rapide pour les stats
                            try {
                                $pdo = new PDO("mysql:host=localhost;dbname=mairie_khombole;charset=utf8mb4", "root", "", [
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

            <!-- Informations de contact -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="fas fa-phone me-2"></i>
                                Contact
                            </h6>
                            <p class="mb-1"><strong>Téléphone :</strong> +221 33 624 52 13 63</p>
                            <p class="mb-1"><strong>Email :</strong> mairiedekhombole@gmail.com</p>
                            <p class="mb-0"><strong>Horaires :</strong> Lun-Ven 8h-17h</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="fw-bold text-primary mb-3">
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
    </script>
</body>
</html>
