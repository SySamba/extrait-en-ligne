<?php
/**
 * Script pour créer un nouvel administrateur
 * Mairie de Khombole
 * 
 * Nécessite une authentification admin existante
 */

require_once 'require_admin.php';

$message = '';
$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    
    // Vérifier le token CSRF
    if (!verifierTokenCSRF($_POST['csrf_token'] ?? '')) {
        $erreur = "Token de sécurité invalide.";
    } elseif (empty($email) || empty($password) || empty($nom) || empty($prenom)) {
        $erreur = "Tous les champs sont obligatoires.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "Adresse email invalide.";
    } elseif (strlen($password) < 8) {
        $erreur = "Le mot de passe doit contenir au moins 8 caractères.";
    } elseif ($password !== $confirmPassword) {
        $erreur = "Les mots de passe ne correspondent pas.";
    } else {
        try {
            $pdo = getDBConnection();
            
            // Vérifier si l'email existe déjà
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetchColumn() > 0) {
                $erreur = "Un administrateur avec cet email existe déjà.";
            } else {
                // Créer le nouvel admin
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare("
                    INSERT INTO admins (email, password_hash, nom, prenom) 
                    VALUES (?, ?, ?, ?)
                ");
                $stmt->execute([$email, $passwordHash, $nom, $prenom]);
                
                $message = "Administrateur créé avec succès.";
                loggerActionAdmin("Nouvel admin créé", "Email: $email, Nom: $prenom $nom");
                
                // Réinitialiser le formulaire
                $_POST = [];
            }
        } catch (Exception $e) {
            $erreur = "Erreur lors de la création de l'administrateur.";
            logActivity("Erreur création admin: " . $e->getMessage(), 'ERROR');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un Administrateur - Mairie de Khombole</title>
    
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
    </style>
</head>
<body>
    <!-- Header Admin -->
    <div class="admin-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h2 class="mb-0">
                        <i class="fas fa-user-plus me-2"></i>
                        Créer un Administrateur
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
        <div class="row justify-content-center">
            <div class="col-md-8">
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

                <!-- Formulaire -->
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-user-shield me-2"></i>
                            Informations du nouvel administrateur
                        </h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" class="needs-validation" novalidate>
                            <input type="hidden" name="csrf_token" value="<?= genererTokenCSRF() ?>">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="prenom" class="form-label">Prénom *</label>
                                        <input type="text" class="form-control" id="prenom" name="prenom" 
                                               value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>" required>
                                        <div class="invalid-feedback">
                                            Veuillez saisir le prénom.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="nom" class="form-label">Nom *</label>
                                        <input type="text" class="form-control" id="nom" name="nom" 
                                               value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
                                        <div class="invalid-feedback">
                                            Veuillez saisir le nom.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Adresse email *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                                <div class="invalid-feedback">
                                    Veuillez saisir une adresse email valide.
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Mot de passe *</label>
                                        <input type="password" class="form-control" id="password" name="password" 
                                               minlength="8" required>
                                        <div class="form-text">Minimum 8 caractères</div>
                                        <div class="invalid-feedback">
                                            Le mot de passe doit contenir au moins 8 caractères.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="confirm_password" class="form-label">Confirmer le mot de passe *</label>
                                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                               minlength="8" required>
                                        <div class="invalid-feedback">
                                            Veuillez confirmer le mot de passe.
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="liste_demandes.php" class="btn btn-secondary btn-admin me-md-2">
                                    <i class="fas fa-times me-1"></i>Annuler
                                </a>
                                <button type="submit" class="btn btn-primary btn-admin">
                                    <i class="fas fa-user-plus me-1"></i>Créer l'administrateur
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
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
                        // Vérifier que les mots de passe correspondent
                        var password = document.getElementById('password').value;
                        var confirmPassword = document.getElementById('confirm_password').value;
                        
                        if (password !== confirmPassword) {
                            document.getElementById('confirm_password').setCustomValidity('Les mots de passe ne correspondent pas');
                        } else {
                            document.getElementById('confirm_password').setCustomValidity('');
                        }
                        
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();

        // Vérification en temps réel des mots de passe
        document.getElementById('confirm_password').addEventListener('input', function() {
            var password = document.getElementById('password').value;
            var confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('Les mots de passe ne correspondent pas');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>
