<?php
/**
 * Script pour mettre à jour la structure de la base de données
 * pour supporter les types d'actes multiples
 */

require_once 'db_connection.php';

try {
    $pdo = createPDOConnection();
    
    echo "<h1>Mise à jour de la base de données</h1>";
    
    // 1. Modifier le champ type_acte pour accepter du texte (plusieurs types)
    echo "<h2>1. Modification du champ type_acte...</h2>";
    $sql1 = "ALTER TABLE demandes_actes MODIFY COLUMN type_acte TEXT NOT NULL";
    $pdo->exec($sql1);
    echo "<p style='color: green;'>✓ Champ type_acte modifié en TEXT</p>";
    
    // 2. Modifier le champ nombre_exemplaires pour accepter du JSON
    echo "<h2>2. Modification du champ nombre_exemplaires...</h2>";
    $sql2 = "ALTER TABLE demandes_actes MODIFY COLUMN nombre_exemplaires TEXT NOT NULL";
    $pdo->exec($sql2);
    echo "<p style='color: green;'>✓ Champ nombre_exemplaires modifié en TEXT</p>";
    
    // 3. Vérifier la nouvelle structure
    echo "<h2>3. Nouvelle structure :</h2>";
    $stmt = $pdo->query("DESCRIBE demandes_actes");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Champ</th><th>Type</th><th>Null</th><th>Clé</th><th>Défaut</th></tr>";
    
    foreach ($columns as $column) {
        $highlight = '';
        if ($column['Field'] === 'type_acte' || $column['Field'] === 'nombre_exemplaires') {
            $highlight = 'style="background-color: #ffffcc;"';
        }
        
        echo "<tr $highlight>";
        echo "<td>" . htmlspecialchars($column['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Default']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<h2>4. Test d'insertion...</h2>";
    
    // Test d'insertion avec les nouveaux formats
    $testData = [
        'numero_demande' => 'TEST-' . date('Y-m-d-H-i-s'),
        'type_acte' => 'extrait_naissance,certificat_residence',
        'nombre_exemplaires' => json_encode(['extrait_naissance' => 2, 'certificat_residence' => 1]),
        'nom' => 'TEST',
        'prenoms' => 'Utilisateur',
        'date_naissance' => '1990-01-01',
        'lieu_naissance' => 'Test',
        'annee_registre' => 1990,
        'numero_registre' => 'TEST123',
        'qualite_demandeur' => 'titulaire',
        'adresse_actuelle' => 'Adresse de test',
        'telephone' => '771234567',
        'email' => 'test@example.com',
        'mode_delivrance' => 'retrait_physique',
        'mode_paiement' => 'wave',
        'consentement_donnees' => 1,
        'acceptation_clause' => 1,
        'ip_soumission' => '127.0.0.1',
        'user_agent' => 'Test Browser'
    ];
    
    $sql = "INSERT INTO demandes_actes (
        numero_demande, type_acte, nombre_exemplaires, nom, prenoms, date_naissance, lieu_naissance,
        annee_registre, numero_registre, qualite_demandeur, adresse_actuelle,
        telephone, email, mode_delivrance, mode_paiement, consentement_donnees,
        acceptation_clause, ip_soumission, user_agent
    ) VALUES (
        :numero_demande, :type_acte, :nombre_exemplaires, :nom, :prenoms, :date_naissance, :lieu_naissance,
        :annee_registre, :numero_registre, :qualite_demandeur, :adresse_actuelle,
        :telephone, :email, :mode_delivrance, :mode_paiement, :consentement_donnees,
        :acceptation_clause, :ip_soumission, :user_agent
    )";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($testData);
    
    echo "<p style='color: green;'>✓ Test d'insertion réussi avec le numéro : " . htmlspecialchars($testData['numero_demande']) . "</p>";
    
    // Vérifier l'insertion
    $stmt = $pdo->prepare("SELECT * FROM demandes_actes WHERE numero_demande = ?");
    $stmt->execute([$testData['numero_demande']]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "<h3>Données insérées :</h3>";
    echo "<p><strong>Types d'actes :</strong> " . htmlspecialchars($result['type_acte']) . "</p>";
    echo "<p><strong>Exemplaires :</strong> " . htmlspecialchars($result['nombre_exemplaires']) . "</p>";
    
    // Décoder le JSON pour vérifier
    $exemplaires = json_decode($result['nombre_exemplaires'], true);
    if ($exemplaires) {
        echo "<p><strong>Exemplaires décodés :</strong></p>";
        echo "<ul>";
        foreach ($exemplaires as $type => $nombre) {
            echo "<li>" . htmlspecialchars($type) . " : " . htmlspecialchars($nombre) . " exemplaire(s)</li>";
        }
        echo "</ul>";
    }
    
    echo "<h2 style='color: green;'>✅ Mise à jour terminée avec succès !</h2>";
    echo "<p><a href='debug_form.php'>Tester le formulaire maintenant</a></p>";
    
} catch (Exception $e) {
    echo "<h2 style='color: red;'>❌ Erreur lors de la mise à jour :</h2>";
    echo "<p style='color: red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
