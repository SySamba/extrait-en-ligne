<?php
/**
 * Test d'envoi d'email - Mairie de Khombole
 * Testez ce fichier pour vérifier que les emails fonctionnent
 */

require_once 'config.php';
require_once 'email_manager.php';

// Données de test pour simuler une demande
$demandeTest = [
    'id' => 999,
    'numero_demande' => 'KH-TEST-001',
    'nom' => 'DIOP',
    'prenoms' => 'Amadou Samba',
    'email' => 'sambasy837@gmail.com', // Votre email pour recevoir le test
    'type_acte' => 'extrait_naissance',
    'date_soumission' => date('Y-m-d H:i:s')
];

echo "<h1>🧪 Test d'Envoi d'Email - Mairie de Khombole</h1>";
echo "<p><strong>Configuration actuelle :</strong></p>";
echo "<ul>";
echo "<li><strong>Méthode d'envoi :</strong> Fonction mail() PHP native</li>";
echo "<li><strong>Expéditeur :</strong> " . MAIL_FROM_NAME . " &lt;" . MAIL_FROM . "&gt;</li>";
echo "<li><strong>Répondre à :</strong> " . MAIL_REPLY_TO . "</li>";
echo "</ul>";

echo "<hr>";

try {
    echo "<h2>📧 Test d'envoi d'email de confirmation...</h2>";
    
    $emailManager = new EmailManager();
    $resultat = $emailManager->envoyerConfirmationDemande($demandeTest);
    
    if ($resultat) {
        echo "<div style='color: green; padding: 10px; border: 1px solid green; background: #f0fff0;'>";
        echo "✅ <strong>SUCCESS !</strong> Email de test envoyé avec succès à : " . $demandeTest['email'];
        echo "<br>📬 Vérifiez votre boîte de réception (et les spams)";
        echo "</div>";
    } else {
        echo "<div style='color: red; padding: 10px; border: 1px solid red; background: #fff0f0;'>";
        echo "❌ <strong>ERREUR !</strong> L'email n'a pas pu être envoyé.";
        echo "<br>🔍 Vérifiez les logs d'erreur PHP pour plus de détails.";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='color: red; padding: 10px; border: 1px solid red; background: #fff0f0;'>";
    echo "❌ <strong>EXCEPTION :</strong> " . $e->getMessage();
    echo "</div>";
}

echo "<hr>";
echo "<h3>🔧 Dépannage</h3>";
echo "<p>Si l'email ne fonctionne pas avec mail() native :</p>";
echo "<ol>";
echo "<li><strong>Serveur local :</strong> XAMPP/WAMP n'ont pas de serveur mail configuré par défaut</li>";
echo "<li><strong>Sendmail :</strong> Configurez sendmail dans php.ini</li>";
echo "<li><strong>Logs :</strong> Consultez les logs d'erreur PHP</li>";
echo "<li><strong>Alternative :</strong> Les emails seront loggés dans les fichiers de log même s'ils ne sont pas envoyés</li>";
echo "</ol>";

echo "<div style='background: #fff3cd; border: 1px solid #ffeaa7; padding: 10px; margin: 10px 0;'>";
echo "<strong>ℹ️ Note :</strong> En environnement de développement local (XAMPP), les emails peuvent ne pas être envoyés réellement, ";
echo "mais le système fonctionne et les emails seront envoyés en production avec un serveur mail configuré.";
echo "</div>";

echo "<p><a href='demande_acte.php'>← Retour au formulaire de demande</a></p>";
?>
