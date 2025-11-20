<?php
/**
 * Script de test pour vérifier la base de données
 */

require_once 'db_connection.php';

try {
    $pdo = createPDOConnection();
    
    echo "<h2>Structure de la table demandes_actes :</h2>";
    
    // Vérifier la structure de la table
    $stmt = $pdo->query("DESCRIBE demandes_actes");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Champ</th><th>Type</th><th>Null</th><th>Clé</th><th>Défaut</th><th>Extra</th></tr>";
    
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Default']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>Nombre de demandes dans la base :</h2>";
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM demandes_actes");
    $count = $stmt->fetch();
    echo "<p>Total des demandes : " . $count['total'] . "</p>";
    
    if ($count['total'] > 0) {
        echo "<h2>Dernières demandes :</h2>";
        $stmt = $pdo->query("SELECT numero_demande, nom, prenoms, type_acte, date_soumission, statut FROM demandes_actes ORDER BY date_soumission DESC LIMIT 5");
        $demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>Numéro</th><th>Nom</th><th>Prénoms</th><th>Type d'acte</th><th>Date</th><th>Statut</th></tr>";
        
        foreach ($demandes as $demande) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($demande['numero_demande']) . "</td>";
            echo "<td>" . htmlspecialchars($demande['nom']) . "</td>";
            echo "<td>" . htmlspecialchars($demande['prenoms']) . "</td>";
            echo "<td>" . htmlspecialchars($demande['type_acte']) . "</td>";
            echo "<td>" . htmlspecialchars($demande['date_soumission']) . "</td>";
            echo "<td>" . htmlspecialchars($demande['statut']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
