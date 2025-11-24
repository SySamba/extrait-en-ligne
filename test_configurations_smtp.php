<?php
/**
 * Test de différentes configurations SMTP
 * Mairie de Khombole
 */

require_once 'config.php';
require_once 'simple_smtp.php';

echo "<h1>🔧 Test de Configurations SMTP</h1>";

// Configurations à tester
$configurations = [
    [
        'name' => 'Configuration actuelle (TLS 587)',
        'host' => 'mail.mairiedekhombole.sn',
        'port' => 587,
        'encryption' => 'tls'
    ],
    [
        'name' => 'SMTP Standard (Port 25)',
        'host' => 'mail.mairiedekhombole.sn',
        'port' => 25,
        'encryption' => ''
    ],
    [
        'name' => 'SMTP SSL (Port 465)',
        'host' => 'mail.mairiedekhombole.sn',
        'port' => 465,
        'encryption' => 'ssl'
    ],
    [
        'name' => 'Serveur alternatif (smtp.)',
        'host' => 'smtp.mairiedekhombole.sn',
        'port' => 587,
        'encryption' => 'tls'
    ],
    [
        'name' => 'Localhost (développement)',
        'host' => 'localhost',
        'port' => 25,
        'encryption' => ''
    ]
];

foreach ($configurations as $index => $config) {
    echo "<h3>" . ($index + 1) . ". " . $config['name'] . "</h3>";
    echo "<p><strong>Serveur:</strong> {$config['host']}:{$config['port']} ({$config['encryption']})</p>";
    
    // Test de connexion
    echo "Test de connexion... ";
    $socket = @fsockopen($config['host'], $config['port'], $errno, $errstr, 5);
    if (!$socket) {
        echo "❌ <strong>Connexion échouée:</strong> $errstr ($errno)<br><br>";
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
            'Test Config ' . ($index + 1) . ' - ' . date('H:i:s'),
            '<h2>Test Configuration ' . ($index + 1) . '</h2><p>Serveur: ' . $config['host'] . ':' . $config['port'] . '</p>',
            true
        );
        
        if ($resultat) {
            echo "✅ <strong>EMAIL ENVOYÉ !</strong> 🎉<br>";
            echo "<div style='background: #d4edda; padding: 10px; border: 1px solid #c3e6cb; margin: 10px 0;'>";
            echo "🎯 <strong>Cette configuration fonctionne !</strong><br>";
            echo "Utilisez ces paramètres dans config.php :<br>";
            echo "<code>";
            echo "define('SMTP_HOST', '{$config['host']}');<br>";
            echo "define('SMTP_PORT', {$config['port']});<br>";
            echo "define('SMTP_ENCRYPTION', '{$config['encryption']}');<br>";
            echo "</code>";
            echo "</div>";
        } else {
            echo "❌ <strong>Envoi échoué</strong><br>";
        }
        
    } catch (Exception $e) {
        echo "❌ <strong>Erreur:</strong> " . $e->getMessage() . "<br>";
    }
    
    echo "<hr>";
}

echo "<h3>📧 Vérifiez votre email</h3>";
echo "<p>Si un test a réussi, vous devriez recevoir un email dans : <strong>sambasy837@gmail.com</strong></p>";

echo "<h3>🔄 Actions suivantes</h3>";
echo "<ul>";
echo "<li>Si aucune configuration ne fonctionne → Contactez votre hébergeur</li>";
echo "<li>Si une configuration fonctionne → Mettez à jour config.php</li>";
echo "<li>En attendant → Le système continue avec la simulation</li>";
echo "</ul>";

echo "<p><a href='test_email.php'>← Retour au test simple</a></p>";
?>
