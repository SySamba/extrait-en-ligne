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
echo "<li><strong>Serveur SMTP :</strong> " . SMTP_HOST . "</li>";
echo "<li><strong>Port :</strong> " . SMTP_PORT . "</li>";
echo "<li><strong>Username :</strong> " . SMTP_USERNAME . "</li>";
echo "<li><strong>Encryption :</strong> " . SMTP_ENCRYPTION . "</li>";
echo "<li><strong>Expéditeur :</strong> " . MAIL_FROM_NAME . " &lt;" . MAIL_FROM . "&gt;</li>";
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
echo "<p>Si l'email ne fonctionne pas :</p>";
echo "<ol>";
echo "<li><strong>Gmail :</strong> Assurez-vous d'avoir activé l'authentification à 2 facteurs et utilisé un mot de passe d'application</li>";
echo "<li><strong>Firewall :</strong> Vérifiez que le port 587 n'est pas bloqué</li>";
echo "<li><strong>Logs :</strong> Consultez les logs d'erreur PHP</li>";
echo "<li><strong>Extensions :</strong> Vérifiez que l'extension OpenSSL est activée</li>";
echo "</ol>";

echo "<p><a href='demande_acte.php'>← Retour au formulaire de demande</a></p>";
?>
