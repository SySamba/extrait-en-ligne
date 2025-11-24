<?php
/**
 * Debug du traitement admin - Vérifier si les emails sont appelés
 * Mairie de Khombole
 */

require_once 'config.php';

echo "<h1>🔍 Debug Admin Traitement - Emails</h1>";

echo "<div style='background: #fff3cd; padding: 15px; border: 1px solid #ffc107; margin-bottom: 20px;'>";
echo "<h3>⚠️ Diagnostic</h3>";
echo "<p>Les logs ne montrent aucune tentative d'envoi d'email lors des changements de statut.</p>";
echo "<p>Vérifions si le problème vient du fichier admin_traiter_demande.php</p>";
echo "</div>";

// Vérifier si le fichier admin_traiter_demande.php existe
$fichierAdmin = 'admin_traiter_demande.php';
if (!file_exists($fichierAdmin)) {
    echo "<div style='background: #f8d7da; padding: 20px; border: 2px solid #dc3545;'>";
    echo "<h3>❌ Fichier manquant !</h3>";
    echo "<p>Le fichier <strong>admin_traiter_demande.php</strong> n'existe pas !</p>";
    echo "</div>";
    exit;
}

echo "<h2>📁 Vérification du Fichier Admin</h2>";

// Lire le contenu du fichier
$contenu = file_get_contents($fichierAdmin);

// Vérifications importantes
$verifications = [
    'require_once \'email_manager.php\'' => 'Inclusion EmailManager',
    '$emailManager = new EmailManager()' => 'Instanciation EmailManager', 
    'envoyerValidationDemande' => 'Méthode validation',
    'envoyerDemandePrete' => 'Méthode demande prête',
    'envoyerRejetDemande' => 'Méthode rejet',
    '$demandeMAJ' => 'Variable demande mise à jour'
];

echo "<table border='1' style='width: 100%; border-collapse: collapse;'>";
echo "<tr style='background: #f8f9fa;'>";
echo "<th style='padding: 10px;'>Vérification</th>";
echo "<th style='padding: 10px;'>Status</th>";
echo "</tr>";

foreach ($verifications as $recherche => $description) {
    $trouve = strpos($contenu, $recherche) !== false;
    $status = $trouve ? '✅ Trouvé' : '❌ Manquant';
    $couleur = $trouve ? '#d4edda' : '#f8d7da';
    
    echo "<tr style='background: $couleur;'>";
    echo "<td style='padding: 10px;'><strong>$description</strong><br><code>$recherche</code></td>";
    echo "<td style='padding: 10px;'>$status</td>";
    echo "</tr>";
}

echo "</table>";

// Vérifier les logs d'email spécifiquement
echo "<h2>📊 Recherche dans les Logs</h2>";

$logFile = 'logs/app_' . date('Y-m-d') . '.log';
if (file_exists($logFile)) {
    $logs = file_get_contents($logFile);
    
    $recherchesLogs = [
        'Email de validation envoyé' => 'Log validation',
        'Email de demande prête envoyé' => 'Log demande prête', 
        'Email de rejet envoyé' => 'Log rejet',
        'Erreur envoi email' => 'Log erreur email',
        'EmailManager' => 'Mention EmailManager'
    ];
    
    echo "<table border='1' style='width: 100%; border-collapse: collapse;'>";
    echo "<tr style='background: #f8f9fa;'>";
    echo "<th style='padding: 10px;'>Recherche dans les logs</th>";
    echo "<th style='padding: 10px;'>Résultat</th>";
    echo "</tr>";
    
    foreach ($recherchesLogs as $recherche => $description) {
        $count = substr_count($logs, $recherche);
        $status = $count > 0 ? "✅ Trouvé ($count fois)" : '❌ Aucune occurrence';
        $couleur = $count > 0 ? '#d4edda' : '#f8d7da';
        
        echo "<tr style='background: $couleur;'>";
        echo "<td style='padding: 10px;'><strong>$description</strong><br><code>$recherche</code></td>";
        echo "<td style='padding: 10px;'>$status</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>❌ Fichier de log non trouvé : $logFile</p>";
}

// Test manuel d'envoi d'email
echo "<h2>🧪 Test Manuel d'Email</h2>";

try {
    require_once 'email_manager.php';
    
    // Données de test
    $demandeTest = [
        'id' => 999,
        'numero_demande' => 'DEBUG-' . date('His'),
        'nom' => 'TEST',
        'prenoms' => 'Debug',
        'email' => 'sambasy837@gmail.com',
        'type_acte' => 'extrait_naissance',
        'statut' => 'en_traitement'
    ];
    
    echo "<p>Test d'envoi d'email de validation...</p>";
    
    $emailManager = new EmailManager();
    $resultat = $emailManager->envoyerValidationDemande($demandeTest, "Test de validation depuis le debug");
    
    if ($resultat) {
        echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb;'>";
        echo "<h4>✅ Email de test envoyé avec succès !</h4>";
        echo "<p>Si vous ne recevez pas cet email, le problème vient de la configuration SMTP.</p>";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; padding: 15px; border: 1px solid #f5c6cb;'>";
        echo "<h4>❌ Échec de l'envoi de l'email de test</h4>";
        echo "<p>Le problème vient soit de la configuration SMTP, soit du code EmailManager.</p>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border: 1px solid #f5c6cb;'>";
    echo "<h4>❌ Erreur lors du test :</h4>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "<h2>🎯 Conclusions</h2>";
echo "<div style='background: #e2e3e5; padding: 15px; border: 1px solid #d6d8db;'>";
echo "<h4>Étapes de diagnostic :</h4>";
echo "<ol>";
echo "<li><strong>Si admin_traiter_demande.php manque des éléments :</strong> Le fichier n'a pas été mis à jour</li>";
echo "<li><strong>Si aucun log d'email :</strong> Les méthodes ne sont pas appelées</li>";
echo "<li><strong>Si le test manuel échoue :</strong> Problème de configuration SMTP</li>";
echo "<li><strong>Si le test manuel réussit :</strong> Problème dans admin_traiter_demande.php</li>";
echo "</ol>";
echo "</div>";

echo "<div style='margin-top: 30px;'>";
echo "<a href='admin_dashboard.php' style='background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>← Admin</a> ";
echo "<a href='test_simple_email.php' style='background: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>🧪 Test Simple</a>";
echo "</div>";
?>
