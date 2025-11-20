<?php
/**
 * Page de test pour le formulaire de demande
 */
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h2>Données reçues du formulaire :</h2>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    // Vérifier les champs requis
    echo "<h2>Vérification des champs :</h2>";
    
    $champsRequis = ['types_actes', 'exemplaires', 'nom', 'prenoms', 'email'];
    
    foreach ($champsRequis as $champ) {
        if (isset($_POST[$champ]) && !empty($_POST[$champ])) {
            echo "<p style='color: green;'>✓ $champ : OK</p>";
        } else {
            echo "<p style='color: red;'>✗ $champ : MANQUANT</p>";
        }
    }
    
    // Tester la connexion à la base de données
    echo "<h2>Test de connexion à la base de données :</h2>";
    try {
        require_once 'db_connection.php';
        $pdo = createPDOConnection();
        echo "<p style='color: green;'>✓ Connexion à la base de données : OK</p>";
        
        // Tester une requête simple
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM demandes_actes");
        $result = $stmt->fetch();
        echo "<p>Nombre de demandes existantes : " . $result['total'] . "</p>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>✗ Erreur de base de données : " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    echo "<hr>";
    echo "<a href='test_form.php'>← Retour au formulaire de test</a>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Formulaire - Mairie de Khombole</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Test du Formulaire de Demande</h1>
        
        <form method="POST" action="test_form.php">
            <div class="mb-3">
                <label class="form-label">Types d'actes (sélectionnez au moins un) :</label>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="types_actes[]" value="extrait_naissance" id="test_extrait">
                            <label class="form-check-label" for="test_extrait">Extrait de naissance</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="types_actes[]" value="certificat_residence" id="test_certificat">
                            <label class="form-check-label" for="test_certificat">Certificat de résidence</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Exemplaires :</label>
                <div class="row">
                    <div class="col-md-6">
                        <label for="exemplaires_extrait">Extrait de naissance :</label>
                        <select class="form-select" name="exemplaires[extrait_naissance]" id="exemplaires_extrait">
                            <option value="">Choisir</option>
                            <option value="1">1 exemplaire</option>
                            <option value="2">2 exemplaires</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="exemplaires_certificat">Certificat de résidence :</label>
                        <select class="form-select" name="exemplaires[certificat_residence]" id="exemplaires_certificat">
                            <option value="">Choisir</option>
                            <option value="1">1 exemplaire</option>
                            <option value="2">2 exemplaires</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom :</label>
                        <input type="text" class="form-control" name="nom" id="nom" value="DIOP" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="prenoms" class="form-label">Prénoms :</label>
                        <input type="text" class="form-control" name="prenoms" id="prenoms" value="Amadou" required>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="date_naissance" class="form-label">Date de naissance :</label>
                        <input type="date" class="form-control" name="date_naissance" id="date_naissance" value="1990-01-01" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="lieu_naissance" class="form-label">Lieu de naissance :</label>
                        <input type="text" class="form-control" name="lieu_naissance" id="lieu_naissance" value="Khombole" required>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="annee_registre" class="form-label">Année du registre :</label>
                        <input type="number" class="form-control" name="annee_registre" id="annee_registre" value="1990" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="numero_registre" class="form-label">Numéro dans le registre :</label>
                        <input type="text" class="form-control" name="numero_registre" id="numero_registre" value="123" required>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="qualite_demandeur" class="form-label">Qualité du demandeur :</label>
                <select class="form-select" name="qualite_demandeur" id="qualite_demandeur" required>
                    <option value="">Sélectionner</option>
                    <option value="titulaire" selected>Titulaire de l'acte</option>
                    <option value="parent">Parent</option>
                </select>
            </div>
            
            <div class="mb-3">
                <label for="adresse_actuelle" class="form-label">Adresse actuelle :</label>
                <textarea class="form-control" name="adresse_actuelle" id="adresse_actuelle" required>Khombole, Sénégal</textarea>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="telephone" class="form-label">Téléphone :</label>
                        <input type="tel" class="form-control" name="telephone" id="telephone" value="771234567" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email :</label>
                        <input type="email" class="form-control" name="email" id="email" value="test@example.com" required>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="mode_delivrance" class="form-label">Mode de délivrance :</label>
                        <select class="form-select" name="mode_delivrance" id="mode_delivrance" required>
                            <option value="">Sélectionner</option>
                            <option value="retrait_physique" selected>Retrait physique</option>
                            <option value="envoi_electronique">Envoi électronique</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="mode_paiement" class="form-label">Mode de paiement :</label>
                        <select class="form-select" name="mode_paiement" id="mode_paiement" required>
                            <option value="">Sélectionner</option>
                            <option value="wave" selected>WAVE</option>
                            <option value="orange_money">Orange Money</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="consentement_donnees" id="consentement" value="1" required>
                    <label class="form-check-label" for="consentement">
                        Je donne mon consentement au traitement des données
                    </label>
                </div>
            </div>
            
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="acceptation_clause" id="acceptation" value="1" required>
                    <label class="form-check-label" for="acceptation">
                        J'accepte la clause de non-responsabilité
                    </label>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Tester l'envoi</button>
        </form>
        
        <hr class="mt-5">
        <h3>Actions de test :</h3>
        <a href="test_db.php" class="btn btn-info">Vérifier la base de données</a>
        <a href="demande_acte.php" class="btn btn-success">Aller au vrai formulaire</a>
    </div>
</body>
</html>
