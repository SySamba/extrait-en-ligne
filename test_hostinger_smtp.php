<?php
/**
 * Test SMTP spécifique pour Hostinger
 * Mairie de Khombole
 */

require_once 'config.php';
require_once 'simple_smtp.php';

echo "<h1>📧 Test SMTP Hostinger</h1>";

// Configurations Hostinger à tester
$hostinger_configs = [
    [
        'name' => 'Hostinger SMTP (Recommandé)',
        'host' => 'smtp.hostinger.com',
        'port' => 587,
        'encryption' => 'tls'
    ],
    [
        'name' => 'Hostinger SSL',
        'host' => 'smtp.hostinger.com',
        'port' => 465,
        'encryption' => 'ssl'
    ],
    [
        'name' => 'Hostinger Standard',
        'host' => 'smtp.hostinger.com',
        'port' => 25,
        'encryption' => ''
    ],
    [
        'name' => 'Serveur Mail Hostinger',
        'host' => 'mail.hostinger.com',
        'port' => 587,
        'encryption' => 'tls'
    ]
];

echo "<div style='background: #e3f2fd; padding: 15px; border: 1px solid #2196f3; margin-bottom: 20px;'>";
echo "<h3>ℹ️ Information Hostinger</h3>";
echo "<p><strong>Votre hébergeur :</strong> Hostinger</p>";
echo "<p><strong>Email :</strong> etat.civil@mairiedekhombole.sn</p>";
echo "<p><strong>Important :</strong> L'email doit être créé dans votre panel Hostinger d'abord !</p>";
echo "</div>";

foreach ($hostinger_configs as $index => $config) {
    echo "<h3>" . ($index + 1) . ". " . $config['name'] . "</h3>";
    echo "<p><strong>Serveur:</strong> {$config['host']}:{$config['port']} ({$config['encryption']})</p>";
    
    // Test de connexion
    echo "Test de connexion... ";
    $socket = @fsockopen($config['host'], $config['port'], $errno, $errstr, 10);
    if (!$socket) {
        echo "❌ <strong>Connexion échouée:</strong> $errstr ($errno)<br>";
        
        // Si c'est le serveur principal et qu'il échoue, donner des conseils
        if ($index === 0) {
            echo "<div style='background: #fff3cd; padding: 10px; border: 1px solid #ffc107; margin: 10px 0;'>";
            echo "⚠️ <strong>Le serveur Hostinger n'est pas accessible.</strong><br>";
            echo "Causes possibles :<br>";
            echo "• L'email n'existe pas dans votre panel Hostinger<br>";
            echo "• Le mot de passe est incorrect<br>";
            echo "• Le domaine n'est pas configuré pour les emails<br>";
            echo "</div>";
        }
        
        echo "<br>";
        continue;
    } else {
        echo "✅ <strong>Connexion OK</strong><br>";
        fclose($socket);
    }
    
    // Test SMTP
    echo "Test SMTP... ";
    try {
        $smtp = new SimpleSMTP(
            $config['host'],
            $config['port'],
            SMTP_USERNAME,
            SMTP_PASSWORD,
            $config['encryption']
        );
        
        $resultat = $smtp->sendEmail(
            MAIL_FROM,
            MAIL_FROM_NAME,
            'sambasy837@gmail.com',
            'Test Hostinger ' . ($index + 1) . ' - ' . date('H:i:s'),
            '<h2>🎉 Test Hostinger Réussi !</h2><p>Configuration: ' . $config['name'] . '</p><p>Serveur: ' . $config['host'] . ':' . $config['port'] . '</p>',
            true
        );
        
        if ($resultat) {
            echo "✅ <strong>EMAIL ENVOYÉ !</strong> 🎉<br>";
            echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; margin: 10px 0;'>";
            echo "🎯 <strong>PARFAIT ! Cette configuration fonctionne !</strong><br>";
            echo "📧 Vérifiez votre email : <strong>sambasy837@gmail.com</strong><br>";
            echo "<br><strong>Configuration à utiliser :</strong><br>";
            echo "<code>";
            echo "define('SMTP_HOST', '{$config['host']}');<br>";
            echo "define('SMTP_PORT', {$config['port']});<br>";
            echo "define('SMTP_ENCRYPTION', '{$config['encryption']}');<br>";
            echo "</code>";
            echo "</div>";
            break; // Arrêter dès qu'une config fonctionne
        } else {
            echo "❌ <strong>Envoi échoué</strong><br>";
        }
        
    } catch (Exception $e) {
        echo "❌ <strong>Erreur:</strong> " . $e->getMessage() . "<br>";
        
        // Conseils spécifiques selon l'erreur
        if (strpos($e->getMessage(), 'authentication') !== false || strpos($e->getMessage(), '535') !== false) {
            echo "<div style='background: #f8d7da; padding: 10px; border: 1px solid #dc3545; margin: 10px 0;'>";
            echo "🔐 <strong>Erreur d'authentification</strong><br>";
            echo "• Vérifiez que l'email existe dans Hostinger<br>";
            echo "• Vérifiez le mot de passe<br>";
            echo "• Activez l'authentification SMTP dans Hostinger<br>";
            echo "</div>";
        }
    }
    
    echo "<hr>";
}

echo "<h3>📋 Étapes à suivre dans Hostinger</h3>";
echo "<ol>";
echo "<li><strong>Connectez-vous</strong> à votre panel Hostinger</li>";
echo "<li><strong>Allez dans</strong> 'Emails' ou 'Email Accounts'</li>";
echo "<li><strong>Créez l'email</strong> : etat.civil@mairiedekhombole.sn</li>";
echo "<li><strong>Définissez le mot de passe</strong> : EC@Khombole*1925</li>";
echo "<li><strong>Activez SMTP</strong> si nécessaire</li>";
echo "<li><strong>Retestez</strong> cette page</li>";
echo "</ol>";

echo "<p><a href='test_email.php'>← Retour au test simple</a></p>";
?>
