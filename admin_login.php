<?php
require_once 'session_manager.php';

$sessionManager = getSessionManager();

// Vérifier si déjà connecté
if ($sessionManager->isAdminLoggedIn()) {
    header('Location: liste_demandes.php');
    exit;
}

$erreur = '';
$success = '';

// Gestion des messages d'URL
if (isset($_GET['logout']) && $_GET['logout'] == '1') {
    $success = 'Vous avez été déconnecté avec succès.';
}

if (isset($_GET['expired']) && $_GET['expired'] == '1') {
    $erreur = 'Votre session a expiré. Veuillez vous reconnecter.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $erreur = 'Veuillez remplir tous les champs.';
    } else {
        try {
            if ($sessionManager->loginAdmin($email, $password)) {
                // Redirection vers la page demandée ou liste par défaut
                $redirect = $_GET['redirect'] ?? 'liste_demandes.php';
                
                // Sécuriser la redirection
                $allowedPages = ['liste_demandes.php', 'traiter_demande.php', 'detail_demande.php'];
                if (!in_array($redirect, $allowedPages)) {
                    $redirect = 'liste_demandes.php';
                }
                
                header('Location: ' . $redirect);
                exit;
            }
        } catch (Exception $e) {
            $erreur = $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Admin - Mairie de Khombole</title>
    
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
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: var(--blanc-principal);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: var(--texte-fonce);
        }

        .login-container {
            background: var(--blanc-principal);
            border: 3px solid transparent;
            border-image: linear-gradient(135deg, var(--senegal-vert), var(--senegal-jaune), var(--senegal-rouge)) 1;
            border-radius: 25px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.2);
            padding: 3rem;
            width: 100%;
            max-width: 450px;
            position: relative;
            overflow: hidden;
        }

        .login-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
        }

        .logo-section {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .logo-circle {
            width: 90px;
            height: 90px;
            background: rgba(11, 132, 62, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(10px);
            border: 3px solid rgba(11, 132, 62, 0.2);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            transition: all 0.3s ease;
            margin: 0 auto 1rem;
        }

        .logo-circle:hover {
            transform: scale(1.05);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
            border-color: rgba(11, 132, 62, 0.3);
        }

        .logo-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .login-title {
            color: var(--text-dark);
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .login-subtitle {
            color: #6c757d;
            font-size: 0.95rem;
        }

        .form-floating {
            margin-bottom: 1.5rem;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 15px;
            padding: 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(11, 132, 62, 0.25);
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border: none;
            border-radius: 15px;
            padding: 1rem 2rem;
            font-weight: 600;
            font-size: 1rem;
            width: 100%;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(11, 132, 62, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(11, 132, 62, 0.4);
            color: white;
        }

        .alert-custom {
            border-radius: 15px;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .security-info {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            border-radius: 15px;
            padding: 1.5rem;
            margin-top: 2rem;
            text-align: center;
        }

        .security-info i {
            font-size: 2rem;
            color: var(--accent-color);
            margin-bottom: 1rem;
        }

        .back-link {
            position: absolute;
            top: 20px;
            left: 20px;
            color: white;
            font-size: 1.5rem;
            text-decoration: none;
            background: rgba(255,255,255,0.2);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .back-link:hover {
            background: rgba(255,255,255,0.3);
            color: white;
            transform: scale(1.1);
        }

        @media (max-width: 576px) {
            .login-container {
                padding: 2rem 1.5rem;
                margin: 10px;
            }

            .logo-circle {
                width: 70px;
                height: 70px;
            }
        }

        .floating-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }

        .shape {
            position: absolute;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            width: 60px;
            height: 60px;
            top: 60%;
            right: 10%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            width: 100px;
            height: 100px;
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
    </style>
</head>
<body>
    <!-- Formes flottantes -->
    <div class="floating-shapes">
        <div class="shape"></div>
        <div class="shape"></div>
        <div class="shape"></div>
    </div>

    <!-- Lien de retour -->
    <a href="menu.php" class="back-link" title="Retour au menu">
        <i class="fas fa-arrow-left"></i>
    </a>

    <div class="login-container">
        <!-- Section logo -->
        <div class="logo-section">
            <div class="logo-circle">
                <img src="logo.jpg" alt="Logo Mairie de Khombole" class="logo-img">
            </div>
            <h2 class="login-title">Connexion Administrateur</h2>
            <p class="login-subtitle">Accès sécurisé à la gestion des demandes</p>
        </div>

        <!-- Messages -->
        <?php if (!empty($erreur)): ?>
            <div class="alert alert-danger alert-custom">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?= htmlspecialchars($erreur) ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-custom">
                <i class="fas fa-check-circle me-2"></i>
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <!-- Formulaire de connexion -->
        <form method="POST" class="needs-validation" novalidate>
            <div class="form-floating">
                <input type="email" class="form-control" id="email" name="email" 
                       placeholder="Email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                <label for="email">
                    <i class="fas fa-envelope me-2"></i>Adresse email
                </label>
                <div class="invalid-feedback">
                    Veuillez saisir une adresse email valide.
                </div>
            </div>

            <div class="form-floating">
                <input type="password" class="form-control" id="password" name="password" 
                       placeholder="Mot de passe" required>
                <label for="password">
                    <i class="fas fa-lock me-2"></i>Mot de passe
                </label>
                <div class="invalid-feedback">
                    Veuillez saisir votre mot de passe.
                </div>
            </div>

            <button type="submit" class="btn btn-login">
                <i class="fas fa-sign-in-alt me-2"></i>
                Se connecter
            </button>
        </form>

        <!-- Informations de sécurité -->
        <div class="security-info">
            <i class="fas fa-shield-alt"></i>
            <h6 class="fw-bold text-primary mb-2">Accès Sécurisé</h6>
            <small class="text-muted">
                Cette zone est réservée aux administrateurs de la Mairie de Khombole.
                Toutes les connexions sont enregistrées et surveillées.
            </small>
        </div>
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

        // Animation d'entrée
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.querySelector('.login-container');
            container.style.opacity = '0';
            container.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                container.style.transition = 'all 0.8s ease';
                container.style.opacity = '1';
                container.style.transform = 'translateY(0)';
            }, 100);
        });

        // Focus automatique sur le champ email
        document.getElementById('email').focus();
    </script>
</body>
</html>
