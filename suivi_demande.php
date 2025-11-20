<?php
/**
 * Page de suivi des demandes d'actes
 * Mairie de Khombole
 */

// Configuration de la base de données
$config = [
    'host' => 'localhost',
    'dbname' => 'mairie_khombole',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8mb4'
];

$erreur = null;

if (isset($_POST['numero_registre']) && !empty($_POST['numero_registre'])) {
    try {
        $dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";
        $pdo = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        
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
    
    <style>
        :root {
            --primary-color: #0b843e;
            --secondary-color: #f4e93d;
            --accent-color: #1e3a8a;
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

        .search-container, .result-container {
            background: white;
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

        .timeline {
            position: relative;
            padding-left: 2rem;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 2rem;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -23px;
            top: 5px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #dee2e6;
            border: 3px solid white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .timeline-item.active::before {
            background: var(--primary-color);
        }

        .timeline-item.completed::before {
            background: #28a745;
        }

        .status-badge {
            font-size: 0.9rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
        }

        .info-card {
            background: var(--bg-light);
            border-radius: 15px;
            padding: 1.5rem;
            margin: 1rem 0;
            border-left: 5px solid var(--primary-color);
        }

        .alert-custom {
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        /* Logo et en-tête */
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
            <div class="alert alert-danger alert-custom">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?= htmlspecialchars($erreur) ?>
            </div>
        <?php endif; ?>


        <!-- Lien vers nouvelle demande -->
        <?php if (!$erreur): ?>
            <div class="text-center">
                <a href="demande_acte.php" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Faire une nouvelle demande
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
</body>
</html>
