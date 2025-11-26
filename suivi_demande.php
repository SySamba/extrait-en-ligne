<?php
/**
 * Page de suivi des demandes d'actes
 * Mairie de Khombole
 */

// Connexion à la base de données
require_once 'db_connection.php';

$erreur = null;

if (isset($_POST['numero_registre']) && !empty($_POST['numero_registre'])) {
    try {
        $pdo = createPDOConnection();
        
        // Recherche par numéro de registre uniquement
        $numeroRegistre = $_POST['numero_registre'];
        $sql = "SELECT numero_demande FROM demandes_actes WHERE numero_registre = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$numeroRegistre]);
        $result = $stmt->fetch();
        
        if ($result) {
            // Rediriger vers la page de détail avec le numéro de registre
            header("Location: detail_demande.php?registre=" . urlencode($numeroRegistre));
            exit();
        } else {
            $erreur = "Aucune demande trouvée avec ce numéro de registre.";
        }
        
    } catch (PDOException $e) {
        $erreur = "Erreur de connexion à la base de données.";
    }
}

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suivi de Demande - Mairie de Khombole</title>
    
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
            --bg-light: var(--blanc-principal);
        }

        * {
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: var(--blanc-principal);
            min-height: 100vh;
            padding: 20px 0;
            color: var(--texte-fonce);
        }

        .header-section {
            /* Drapeau sénégalais en dégradé */
            background: linear-gradient(135deg, 
                var(--senegal-vert) 0%, 
                var(--senegal-vert) 33%, 
                var(--senegal-jaune) 33%, 
                var(--senegal-jaune) 66%, 
                var(--senegal-rouge) 66%, 
                var(--senegal-rouge) 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 30px 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            position: relative;
        }
        
        .header-section::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: var(--blanc-principal);
        }

        .search-container {
            background: var(--blanc-principal);
            border: 3px solid transparent;
            border-image: linear-gradient(135deg, var(--senegal-vert), var(--senegal-jaune), var(--senegal-rouge)) 1;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            padding: 2.5rem;
            margin-bottom: 2rem;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(11, 132, 62, 0.25);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border: none;
            border-radius: 25px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(11, 132, 62, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(11, 132, 62, 0.4);
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

        /* Menu de navigation simple */
        .simple-nav {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
            margin-bottom: 2rem;
        }

        .nav-links {
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .nav-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .nav-link:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(11, 132, 62, 0.3);
        }

        .nav-link.active {
            background: var(--primary-color);
            color: white;
        }

        @media (max-width: 768px) {
            .nav-links {
                gap: 1rem;
            }
            
            .nav-link {
                padding: 0.4rem 1rem;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <!-- Menu de navigation simple -->
    <nav class="simple-nav">
        <div class="container">
            <div class="nav-links">
                <a href="menu.php" class="nav-link">
                    <i class="fas fa-home me-1"></i>Accueil
                </a>
                <a href="demande_acte.php" class="nav-link">
                    <i class="fas fa-plus me-1"></i>Nouvelle Demande
                </a>
                <a href="suivi_demande.php" class="nav-link active">
                    <i class="fas fa-search me-1"></i>Suivi Demande
                </a>
            </div>
        </div>
    </nav>

    <!-- Header Section -->
    <div class="header-section">
        <div class="container">
            <div class="logo-container">
                <div class="logo-circle">
                    <img src="logo.jpg" alt="Logo Mairie de Khombole" class="logo-img">
                </div>
                <div class="text-center">
                    <h1 class="mb-0 fw-bold">SUIVI DE DEMANDE D'ACTE</h1>
                    <p class="mb-0">Mairie de Khombole - République du Sénégal</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Formulaire de recherche -->
        <div class="search-container">
            <h4 class="fw-bold text-primary mb-3">
                <i class="fas fa-search me-2"></i>
                Rechercher votre demande
            </h4>
            
            <form method="POST" class="row g-3">
                <div class="col-md-8">
                    <label for="numero_registre" class="form-label">Numéro dans le registre d'état civil</label>
                    <input type="text" class="form-control" id="numero_registre" name="numero_registre" 
                           placeholder="Ex: 125" value="<?= htmlspecialchars($_POST['numero_registre'] ?? '') ?>" required>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>
                        Rechercher
                    </button>
                </div>
            </form>
            
            <div class="mt-3">
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Saisissez votre numéro d'enregistrement dans le registre d'état civil pour suivre votre demande d'acte.
                </small>
            </div>
        </div>

        <!-- Résultats -->
        <?php if ($erreur): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?= htmlspecialchars($erreur) ?>
            </div>
        <?php endif; ?>

        <!-- Lien vers nouvelle demande -->
        <div class="text-center">
            <a href="demande_acte.php" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Faire une nouvelle demande
            </a>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
</body>
</html>
