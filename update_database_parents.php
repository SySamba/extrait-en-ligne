<?php
/**
 * Script de mise à jour de la base de données
 * Ajout des champs nom_pere et nom_mere
 */

require_once 'config.php';

echo "<h1>🔄 Mise à jour Base de Données - Champs Parents</h1>";

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    
    echo "<h2>📊 Vérification des colonnes existantes</h2>";
    
    // Vérifier si les colonnes existent déjà
    $stmt = $pdo->query("DESCRIBE demandes_actes");
    $colonnes = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $prenomPereExiste = in_array('prenom_pere', $colonnes);
    $nomPereExiste = in_array('nom_pere', $colonnes);
    $prenomMereExiste = in_array('prenom_mere', $colonnes);
    $nomMereExiste = in_array('nom_mere', $colonnes);
    
    echo "<ul>";
    echo "<li><strong>prenom_pere :</strong> " . ($prenomPereExiste ? "✅ Existe déjà" : "❌ N'existe pas") . "</li>";
    echo "<li><strong>nom_pere :</strong> " . ($nomPereExiste ? "✅ Existe déjà" : "❌ N'existe pas") . "</li>";
    echo "<li><strong>prenom_mere :</strong> " . ($prenomMereExiste ? "✅ Existe déjà" : "❌ N'existe pas") . "</li>";
    echo "<li><strong>nom_mere :</strong> " . ($nomMereExiste ? "✅ Existe déjà" : "❌ N'existe pas") . "</li>";
    echo "</ul>";
    
    $modifications = [];
    
    // Ajouter prenom_pere si nécessaire
    if (!$prenomPereExiste) {
        $pdo->exec("ALTER TABLE demandes_actes ADD COLUMN prenom_pere VARCHAR(100) AFTER lieu_naissance");
        $modifications[] = "Colonne 'prenom_pere' ajoutée";
        echo "<p>✅ <strong>Colonne 'prenom_pere' ajoutée avec succès</strong></p>";
    }
    
    // Ajouter nom_pere si nécessaire
    if (!$nomPereExiste) {
        $pdo->exec("ALTER TABLE demandes_actes ADD COLUMN nom_pere VARCHAR(100) AFTER prenom_pere");
        $modifications[] = "Colonne 'nom_pere' ajoutée";
        echo "<p>✅ <strong>Colonne 'nom_pere' ajoutée avec succès</strong></p>";
    }
    
    // Ajouter prenom_mere si nécessaire
    if (!$prenomMereExiste) {
        $pdo->exec("ALTER TABLE demandes_actes ADD COLUMN prenom_mere VARCHAR(100) AFTER nom_pere");
        $modifications[] = "Colonne 'prenom_mere' ajoutée";
        echo "<p>✅ <strong>Colonne 'prenom_mere' ajoutée avec succès</strong></p>";
    }
    
    // Ajouter nom_mere si nécessaire
    if (!$nomMereExiste) {
        $pdo->exec("ALTER TABLE demandes_actes ADD COLUMN nom_mere VARCHAR(100) AFTER prenom_mere");
        $modifications[] = "Colonne 'nom_mere' ajoutée";
        echo "<p>✅ <strong>Colonne 'nom_mere' ajoutée avec succès</strong></p>";
    }
    
    if (empty($modifications)) {
        echo "<div style='background: #d1ecf1; padding: 15px; border: 1px solid #bee5eb; border-radius: 5px;'>";
        echo "<h3>ℹ️ Aucune modification nécessaire</h3>";
        echo "<p>Les colonnes nom_pere et nom_mere existent déjà dans la base de données.</p>";
        echo "</div>";
    } else {
        echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px;'>";
        echo "<h3>🎉 Mise à jour réussie !</h3>";
        echo "<ul>";
        foreach ($modifications as $modif) {
            echo "<li>$modif</li>";
        }
        echo "</ul>";
        echo "</div>";
    }
    
    // Afficher la structure finale
    echo "<h2>📋 Structure finale de la table</h2>";
    $stmt = $pdo->query("DESCRIBE demandes_actes");
    $colonnes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='width: 100%; border-collapse: collapse;'>";
    echo "<tr style='background: #f8f9fa;'>";
    echo "<th style='padding: 10px;'>Colonne</th>";
    echo "<th style='padding: 10px;'>Type</th>";
    echo "<th style='padding: 10px;'>Null</th>";
    echo "<th style='padding: 10px;'>Défaut</th>";
    echo "</tr>";
    
    foreach ($colonnes as $colonne) {
        $highlight = in_array($colonne['Field'], ['nom_pere', 'nom_mere']) ? 'background: #fff3cd;' : '';
        echo "<tr style='$highlight'>";
        echo "<td style='padding: 8px;'><strong>" . $colonne['Field'] . "</strong></td>";
        echo "<td style='padding: 8px;'>" . $colonne['Type'] . "</td>";
        echo "<td style='padding: 8px;'>" . $colonne['Null'] . "</td>";
        echo "<td style='padding: 8px;'>" . ($colonne['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    echo "<h2>🎯 Prochaines étapes</h2>";
    echo "<ol>";
    echo "<li>✅ Base de données mise à jour</li>";
    echo "<li>🔄 Mettre à jour traiter_demande.php pour gérer les nouveaux champs</li>";
    echo "<li>🧪 Tester le formulaire avec les nouveaux champs</li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border: 1px solid #f5c6cb; border-radius: 5px;'>";
    echo "<h3>❌ Erreur lors de la mise à jour</h3>";
    echo "<p><strong>Message :</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "<div style='margin-top: 30px;'>";
echo "<a href='demande_acte.php' style='background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>📝 Tester le formulaire</a> ";
echo "<a href='admin_dashboard.php' style='background: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>👨‍💼 Admin</a>";
echo "</div>";
?>
