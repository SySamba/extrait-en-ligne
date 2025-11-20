<?php
/**
 * Script de debug pour voir exactement ce qui est envoyé par le formulaire
 */
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h1>DEBUG - Données reçues du formulaire</h1>";
    
    echo "<h2>Données POST complètes :</h2>";
    echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 5px;'>";
    print_r($_POST);
    echo "</pre>";
    
    echo "<h2>Vérification spécifique :</h2>";
    
    // Vérifier types_actes
    if (isset($_POST['types_actes'])) {
        echo "<p><strong>✓ types_actes trouvé :</strong></p>";
        echo "<pre>";
        print_r($_POST['types_actes']);
        echo "</pre>";
    } else {
        echo "<p style='color: red;'><strong>✗ types_actes NON trouvé</strong></p>";
    }
    
    // Vérifier exemplaires
    if (isset($_POST['exemplaires'])) {
        echo "<p><strong>✓ exemplaires trouvé :</strong></p>";
        echo "<pre>";
        print_r($_POST['exemplaires']);
        echo "</pre>";
    } else {
        echo "<p style='color: red;'><strong>✗ exemplaires NON trouvé</strong></p>";
    }
    
    echo "<hr>";
    echo "<h2>Test de traitement :</h2>";
    
    try {
        require_once 'db_connection.php';
        require_once 'traiter_demande.php';
        
        $demandeActe = new DemandeActe();
        $resultat = $demandeActe->enregistrerDemande($_POST);
        
        echo "<p style='color: green;'><strong>✓ Succès !</strong></p>";
        echo "<p>Numéro de demande : " . htmlspecialchars($resultat['numero_demande']) . "</p>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'><strong>✗ Erreur :</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    echo "<hr>";
    echo "<a href='debug_form.php'>← Retour au formulaire de debug</a>";
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Formulaire - Mairie de Khombole</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1><i class="fas fa-bug me-2"></i>Debug du Formulaire de Demande</h1>
        <p class="text-muted">Ce formulaire utilise exactement la même structure que le vrai formulaire</p>
        
        <form method="POST" action="debug_form.php">
            <!-- Types d'actes -->
            <div class="mb-4">
                <label class="form-label fw-bold">Types d'actes demandés :</label>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="types_actes[]" value="extrait_naissance" id="debug_extrait">
                            <label class="form-check-label" for="debug_extrait">Extrait de naissance</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="types_actes[]" value="certificat_residence" id="debug_certificat">
                            <label class="form-check-label" for="debug_certificat">Certificat de résidence</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Exemplaires -->
            <div class="mb-4" id="exemplaires_section">
                <label class="form-label fw-bold">Nombre d'exemplaires :</label>
                <div class="row">
                    <div class="col-md-6">
                        <label for="debug_exemplaires_extrait">Extrait de naissance :</label>
                        <select class="form-select" name="exemplaires[extrait_naissance]" id="debug_exemplaires_extrait">
                            <option value="">Choisir</option>
                            <option value="1">1 exemplaire</option>
                            <option value="2" selected>2 exemplaires</option>
                            <option value="3">3 exemplaires</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="debug_exemplaires_certificat">Certificat de résidence :</label>
                        <select class="form-select" name="exemplaires[certificat_residence]" id="debug_exemplaires_certificat">
                            <option value="">Choisir</option>
                            <option value="1">1 exemplaire</option>
                            <option value="2" selected>2 exemplaires</option>
                            <option value="3">3 exemplaires</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Autres champs obligatoires -->
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
                    <option value="titulaire" selected>Titulaire de l'acte</option>
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
                            <option value="retrait_physique" selected>Retrait physique</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="mode_paiement" class="form-label">Mode de paiement :</label>
                        <select class="form-select" name="mode_paiement" id="mode_paiement" required>
                            <option value="wave" selected>WAVE</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="consentement_donnees" id="consentement" value="1" checked required>
                    <label class="form-check-label" for="consentement">
                        Je donne mon consentement au traitement des données
                    </label>
                </div>
            </div>
            
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="acceptation_clause" id="acceptation" value="1" checked required>
                    <label class="form-check-label" for="acceptation">
                        J'accepte la clause de non-responsabilité
                    </label>
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-paper-plane me-2"></i>Tester l'envoi
            </button>
        </form>
        
        <hr class="mt-5">
        <div class="row">
            <div class="col-md-4">
                <a href="demande_acte.php" class="btn btn-success w-100">
                    <i class="fas fa-file-alt me-2"></i>Vrai formulaire
                </a>
            </div>
            <div class="col-md-4">
                <a href="test_db.php" class="btn btn-info w-100">
                    <i class="fas fa-database me-2"></i>Vérifier la DB
                </a>
            </div>
            <div class="col-md-4">
                <a href="menu.php" class="btn btn-secondary w-100">
                    <i class="fas fa-home me-2"></i>Page d'accueil
                </a>
            </div>
        </div>
    </div>
    
    <script>
        // Cocher automatiquement les types d'actes pour le test
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('debug_extrait').checked = true;
            document.getElementById('debug_certificat').checked = true;
        });
    </script>
</body>
</html>
