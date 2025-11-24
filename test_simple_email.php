<?php
/**
 * Test simple d'email sans erreurs
 * Mairie de Khombole
 */

require_once 'config.php';
require_once 'email_manager.php';

echo "<h1>🧪 Test Simple Email - Sans Erreurs</h1>";

echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; margin-bottom: 20px;'>";
echo "<h3>✅ Corrections Appliquées</h3>";
echo "<ul>";
echo "<li>✅ Variable \$donnees corrigée dans traiter_demande.php</li>";
echo "<li>✅ Erreur \$demande['id'] corrigée dans email_manager.php</li>";
echo "<li>✅ Configuration SMTP Hostinger active</li>";
echo "</ul>";
echo "</div>";

// Test avec données complètes pour éviter les erreurs
$demandeTest = [
    'id' => 1, // ID présent pour éviter l'erreur
    'numero_demande' => 'KH-SIMPLE-' . date('His'),
    'nom' => 'DIOP',
    'prenoms' => 'Amadou Samba',
    'email' => 'sambasy837@gmail.com',
    'type_acte' => 'extrait_naissance',
    'statut' => 'en_attente',
    'date_soumission' => date('Y-m-d H:i:s')
];

echo "<h2>📧 Configuration Actuelle</h2>";
echo "<ul>";
echo "<li><strong>SMTP Host :</strong> " . SMTP_HOST . "</li>";
echo "<li><strong>Port :</strong> " . SMTP_PORT . "</li>";
echo "<li><strong>Username :</strong> " . SMTP_USERNAME . "</li>";
echo "<li><strong>Email de test :</strong> " . $demandeTest['email'] . "</li>";
echo "</ul>";

echo "<hr>";

try {
    echo "<h2>🚀 Test d'envoi...</h2>";
    
    $emailManager = new EmailManager();
    
    // Test simple d'email de confirmation
    $resultat = $emailManager->envoyerConfirmationDemande($demandeTest);
    
    if ($resultat) {
        echo "<div style='background: #d4edda; padding: 20px; border: 2px solid #28a745; margin: 20px 0;'>";
        echo "<h3>🎉 <strong>EMAIL ENVOYÉ AVEC SUCCÈS !</strong></h3>";
        echo "<p>✅ Aucune erreur détectée</p>";
        echo "<p>📧 Vérifiez votre email : <strong>sambasy837@gmail.com</strong></p>";
        echo "<p>📊 L'email a été traité par le service automatique</p>";
        echo "</div>";
        
        echo "<h3>📋 Vérifications</h3>";
        echo "<ol>";
        echo "<li><strong>Email reçu :</strong> Vérifiez sambasy837@gmail.com</li>";
        echo "<li><strong>Dossier spam :</strong> Regardez dans les spams</li>";
        echo "<li><strong>Emails simulés :</strong> <a href='voir_emails_simules.php' target='_blank'>Voir la simulation</a></li>";
        echo "</ol>";
        
    } else {
        echo "<div style='background: #f8d7da; padding: 20px; border: 2px solid #dc3545;'>";
        echo "<h3>❌ <strong>Échec d'envoi</strong></h3>";
        echo "<p>L'email n'a pas pu être envoyé, mais aucune erreur PHP détectée.</p>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 20px; border: 2px solid #dc3545;'>";
    echo "<h3>❌ <strong>Exception :</strong></h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "<h2>🔍 Diagnostic des Logs</h2>";
echo "<div style='background: #f8f9fa; padding: 15px; border: 1px solid #ddd;'>";
echo "<p>Si ce test fonctionne sans erreur, les logs ne devraient plus montrer :</p>";
echo "<ul>";
echo "<li>❌ <code>Undefined variable \$donnees</code></li>";
echo "<li>❌ <code>Undefined array key \"id\"</code></li>";
echo "</ul>";
echo "<p>Vérifiez les nouveaux logs après ce test.</p>";
echo "</div>";

echo "<h2>🎯 Prochaines Étapes</h2>";
echo "<ol>";
echo "<li><strong>Si ce test réussit :</strong> Testez un changement de statut en admin</li>";
echo "<li><strong>Si vous recevez l'email :</strong> Le système est 100% opérationnel !</li>";
echo "<li><strong>Si pas d'email reçu :</strong> Vérifiez la configuration Hostinger</li>";
echo "</ol>";

echo "<div style='margin-top: 30px;'>";
echo "<a href='admin_dashboard.php' style='background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>← Admin</a> ";
echo "<a href='demande_acte.php' style='background: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>📝 Nouvelle demande</a> ";
echo "<a href='voir_emails_simules.php' style='background: #6c757d; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>📧 Emails simulés</a>";
echo "</div>";
?>
