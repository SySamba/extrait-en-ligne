<?php
/**
 * Test de diagnostic SMTP détaillé
 * Mairie de Khombole
 */

require_once 'config.php';
require_once 'simple_smtp.php';

echo "<h1>🔍 Diagnostic SMTP - Mairie de Khombole</h1>";

echo "<h2>📋 Configuration</h2>";
echo "<ul>";
echo "<li><strong>Serveur :</strong> " . SMTP_HOST . "</li>";
echo "<li><strong>Port :</strong> " . SMTP_PORT . "</li>";
echo "<li><strong>Username :</strong> " . SMTP_USERNAME . "</li>";
echo "<li><strong>Password :</strong> " . str_repeat('*', strlen(SMTP_PASSWORD)) . "</li>";
echo "<li><strong>Encryption :</strong> " . SMTP_ENCRYPTION . "</li>";
echo "</ul>";

echo "<h2>🔧 Test de connexion</h2>";

// Test 1: Résolution DNS
echo "<h3>1. Test de résolution DNS</h3>";
$ip = gethostbyname(SMTP_HOST);
if ($ip === SMTP_HOST) {
    echo "❌ <strong>ERREUR:</strong> Impossible de résoudre " . SMTP_HOST . "<br>";
} else {
    echo "✅ <strong>DNS OK:</strong> " . SMTP_HOST . " → $ip<br>";
}

// Test 2: Connexion socket
echo "<h3>2. Test de connexion socket</h3>";
$socket = @fsockopen(SMTP_HOST, SMTP_PORT, $errno, $errstr, 10);
if (!$socket) {
    echo "❌ <strong>ERREUR:</strong> Impossible de se connecter à " . SMTP_HOST . ":" . SMTP_PORT . "<br>";
    echo "Erreur: $errstr ($errno)<br>";
} else {
    echo "✅ <strong>Connexion OK:</strong> Port " . SMTP_PORT . " accessible<br>";
    fclose($socket);
}

// Test 3: Test SMTP complet
echo "<h3>3. Test SMTP complet</h3>";
try {
    $smtp = new SimpleSMTP(
        SMTP_HOST,
        SMTP_PORT,
        SMTP_USERNAME,
        SMTP_PASSWORD,
        SMTP_ENCRYPTION
    );
    
    echo "Tentative d'envoi d'email de test...<br>";
    
    $resultat = $smtp->sendEmail(
        MAIL_FROM,
        MAIL_FROM_NAME,
        'sambasy837@gmail.com',
        'Test SMTP - ' . date('Y-m-d H:i:s'),
        '<h1>Test SMTP</h1><p>Ceci est un test d\'envoi via SMTP.</p>',
        true
    );
    
    if ($resultat) {
        echo "✅ <strong>SMTP OK:</strong> Email envoyé avec succès !<br>";
        echo "📧 Vérifiez votre boîte email : sambasy837@gmail.com<br>";
    } else {
        echo "❌ <strong>SMTP ÉCHEC:</strong> L'envoi a échoué<br>";
    }
    
} catch (Exception $e) {
    echo "❌ <strong>EXCEPTION:</strong> " . $e->getMessage() . "<br>";
}

// Test 4: Configurations alternatives
echo "<h3>4. Configurations alternatives à tester</h3>";
echo "<div style='background: #f8f9fa; padding: 15px; border: 1px solid #ddd;'>";
echo "<h4>Si le serveur actuel ne fonctionne pas, essayez :</h4>";
echo "<ul>";
echo "<li><strong>Port 25</strong> (SMTP standard)</li>";
echo "<li><strong>Port 465</strong> (SMTP SSL)</li>";
echo "<li><strong>Sans encryption</strong> (pas de TLS/SSL)</li>";
echo "<li><strong>Serveur alternatif :</strong> smtp.mairiedekhombole.sn</li>";
echo "</ul>";
echo "</div>";

// Test 5: Informations système
echo "<h3>5. Informations système</h3>";
echo "<ul>";
echo "<li><strong>PHP Version :</strong> " . phpversion() . "</li>";
echo "<li><strong>OpenSSL :</strong> " . (extension_loaded('openssl') ? '✅ Activé' : '❌ Désactivé') . "</li>";
echo "<li><strong>Sockets :</strong> " . (extension_loaded('sockets') ? '✅ Activé' : '❌ Désactivé') . "</li>";
echo "<li><strong>Allow URL fopen :</strong> " . (ini_get('allow_url_fopen') ? '✅ Activé' : '❌ Désactivé') . "</li>";
echo "</ul>";

echo "<h3>6. Logs d'erreur récents</h3>";
$logFile = __DIR__ . '/logs/app_' . date('Y-m-d') . '.log';
if (file_exists($logFile)) {
    $logs = file_get_contents($logFile);
    $recentLogs = array_slice(explode("\n", $logs), -20); // 20 dernières lignes
    echo "<pre style='background: #f8f9fa; padding: 10px; max-height: 300px; overflow-y: auto;'>";
    echo htmlspecialchars(implode("\n", $recentLogs));
    echo "</pre>";
} else {
    echo "Aucun log trouvé pour aujourd'hui.<br>";
}

echo "<p><a href='test_email.php'>← Retour au test simple</a></p>";
?>
