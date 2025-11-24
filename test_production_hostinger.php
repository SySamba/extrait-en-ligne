<?php
/**
 * Test SMTP Production Hostinger
 * Configuration réelle avec contact@test.sencouche.com
 */

require_once 'config.php';
require_once 'simple_smtp.php';

echo "<h1>🚀 Test Production Hostinger</h1>";

echo "<div style='background: #e3f2fd; padding: 15px; border: 1px solid #2196f3; margin-bottom: 20px;'>";
echo "<h3>📧 Configuration Production</h3>";
echo "<ul>";
echo "<li><strong>Email :</strong> " . SMTP_USERNAME . "</li>";
echo "<li><strong>Serveur :</strong> " . SMTP_HOST . ":" . SMTP_PORT . "</li>";
echo "<li><strong>Encryption :</strong> " . SMTP_ENCRYPTION . "</li>";
echo "<li><strong>Status :</strong> 🔴 PRODUCTION LIVE</li>";
echo "</ul>";
echo "</div>";

// Test 1: Connexion au serveur
echo "<h3>1. Test de connexion Hostinger</h3>";
$socket = @fsockopen(SMTP_HOST, SMTP_PORT, $errno, $errstr, 10);
if (!$socket) {
    echo "❌ <strong>ERREUR:</strong> Impossible de se connecter à " . SMTP_HOST . ":" . SMTP_PORT . "<br>";
    echo "Erreur: $errstr ($errno)<br>";
    exit;
} else {
    echo "✅ <strong>Connexion OK:</strong> Serveur Hostinger accessible<br>";
    fclose($socket);
}

// Test 2: Authentification SMTP
echo "<h3>2. Test d'authentification SMTP</h3>";
try {
    $smtp = new SimpleSMTP(
        SMTP_HOST,
        SMTP_PORT,
        SMTP_USERNAME,
        SMTP_PASSWORD,
        SMTP_ENCRYPTION
    );
    
    echo "Test d'envoi d'email réel...<br>";
    
    $resultat = $smtp->sendEmail(
        MAIL_FROM,
        MAIL_FROM_NAME,
        'sambasy837@gmail.com', // Votre email de test
        'TEST PRODUCTION - ' . date('Y-m-d H:i:s'),
        '
        <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
            <div style="background: #0b843e; color: white; padding: 20px; text-align: center;">
                <h1>🏛️ Mairie de Khombole</h1>
                <h2>Test Production Réussi !</h2>
            </div>
            <div style="padding: 20px; background: #f8f9fa;">
                <h3>✅ Email de Production Fonctionnel</h3>
                <p><strong>Serveur :</strong> ' . SMTP_HOST . '</p>
                <p><strong>Email :</strong> ' . SMTP_USERNAME . '</p>
                <p><strong>Date :</strong> ' . date('d/m/Y à H:i:s') . '</p>
                <p><strong>Status :</strong> 🎉 PRODUCTION ACTIVE</p>
                
                <div style="background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; margin: 20px 0;">
                    <h4>🎯 Système d\'emails opérationnel !</h4>
                    <p>Les utilisateurs vont maintenant recevoir leurs notifications automatiquement.</p>
                </div>
            </div>
            <div style="background: #6c757d; color: white; padding: 10px; text-align: center; font-size: 12px;">
                Mairie de Khombole - Service État Civil<br>
                Email envoyé automatiquement depuis ' . SMTP_USERNAME . '
            </div>
        </div>
        ',
        true
    );
    
    if ($resultat) {
        echo "<div style='background: #d4edda; padding: 20px; border: 2px solid #28a745; margin: 20px 0;'>";
        echo "<h3>🎉 <strong>SUCCÈS TOTAL !</strong></h3>";
        echo "<p>✅ <strong>Email envoyé avec succès !</strong></p>";
        echo "<p>📧 <strong>Vérifiez :</strong> sambasy837@gmail.com</p>";
        echo "<p>🚀 <strong>Production :</strong> Le système est maintenant opérationnel !</p>";
        echo "<p>👥 <strong>Utilisateurs :</strong> Vont recevoir leurs emails automatiquement</p>";
        echo "</div>";
        
        echo "<h3>📊 Prochaines étapes</h3>";
        echo "<ol>";
        echo "<li>✅ <strong>Emails fonctionnels</strong> - Configuration réussie</li>";
        echo "<li>🧪 <strong>Testez une vraie demande</strong> sur votre site</li>";
        echo "<li>📧 <strong>Vérifiez la réception</strong> des emails</li>";
        echo "<li>🎯 <strong>Informez vos utilisateurs</strong> que le système est opérationnel</li>";
        echo "</ol>";
        
    } else {
        echo "<div style='background: #f8d7da; padding: 20px; border: 2px solid #dc3545;'>";
        echo "<h3>❌ <strong>Échec d'envoi</strong></h3>";
        echo "<p>La connexion fonctionne mais l'envoi a échoué.</p>";
        echo "<p>Vérifiez les paramètres dans le panel Hostinger.</p>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 20px; border: 2px solid #dc3545;'>";
    echo "<h3>❌ <strong>Erreur SMTP:</strong></h3>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    
    if (strpos($e->getMessage(), '535') !== false || strpos($e->getMessage(), 'authentication') !== false) {
        echo "<h4>🔐 Problème d'authentification</h4>";
        echo "<p>Vérifiez dans votre panel Hostinger :</p>";
        echo "<ul>";
        echo "<li>L'email <strong>contact@test.sencouche.com</strong> existe</li>";
        echo "<li>Le mot de passe est <strong>Khombole2021@</strong></li>";
        echo "<li>L'authentification SMTP est activée</li>";
        echo "</ul>";
    }
    echo "</div>";
}

echo "<h3>🔄 Actions</h3>";
echo "<p>";
echo "<a href='test_email.php' style='background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>← Test simple</a> ";
echo "<a href='demande_acte.php' style='background: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>📝 Tester une demande</a>";
echo "</p>";
?>
