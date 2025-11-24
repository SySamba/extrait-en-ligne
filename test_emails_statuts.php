<?php
/**
 * Test des emails automatiques lors des changements de statut
 * Mairie de Khombole
 */

require_once 'config.php';
require_once 'email_manager.php';

echo "<h1>🧪 Test Emails Automatiques - Changements de Statut</h1>";

echo "<div style='background: #fff3cd; padding: 15px; border: 1px solid #ffc107; margin-bottom: 20px;'>";
echo "<h3>⚠️ Test de Diagnostic</h3>";
echo "<p>Ce test simule l'envoi d'emails lors des changements de statut sans modifier la base de données.</p>";
echo "</div>";

// Données de test simulant une demande
$demandeTest = [
    'id' => 999,
    'numero_demande' => 'KH-TEST-STATUT-' . date('His'),
    'nom' => 'DIOP',
    'prenoms' => 'Amadou Samba',
    'email' => 'sambasy837@gmail.com',
    'type_acte' => 'extrait_naissance',
    'statut' => 'en_attente',
    'date_soumission' => date('Y-m-d H:i:s'),
    'commentaire_admin' => ''
];

$emailManager = new EmailManager();

echo "<h2>📧 Test des 4 Types d'Emails Automatiques</h2>";

// Test 1: Email de validation
echo "<h3>1. 📨 Test Email de Validation (Accepter)</h3>";
$demandeTest['statut'] = 'en_traitement';
$commentaire = "Votre demande a été acceptée et est maintenant en cours de traitement.";

try {
    $resultat = $emailManager->envoyerValidationDemande($demandeTest, $commentaire);
    if ($resultat) {
        echo "✅ <strong>Email de validation envoyé avec succès</strong><br>";
    } else {
        echo "❌ <strong>Échec envoi email de validation</strong><br>";
    }
} catch (Exception $e) {
    echo "❌ <strong>Erreur:</strong> " . htmlspecialchars($e->getMessage()) . "<br>";
}

echo "<hr>";

// Test 2: Email demande prête
echo "<h3>2. 🎉 Test Email Demande Prête (Terminer)</h3>";
$demandeTest['statut'] = 'pret';
$commentaire = "Votre acte est prêt ! Vous pouvez venir le récupérer aux heures d'ouverture.";

try {
    $resultat = $emailManager->envoyerDemandePrete($demandeTest, $commentaire);
    if ($resultat) {
        echo "✅ <strong>Email demande prête envoyé avec succès</strong><br>";
    } else {
        echo "❌ <strong>Échec envoi email demande prête</strong><br>";
    }
} catch (Exception $e) {
    echo "❌ <strong>Erreur:</strong> " . htmlspecialchars($e->getMessage()) . "<br>";
}

echo "<hr>";

// Test 3: Email de rejet
echo "<h3>3. ❌ Test Email de Rejet</h3>";
$demandeTest['statut'] = 'rejete';
$commentaire = "Votre demande a été rejetée car les documents fournis sont incomplets.";

try {
    $resultat = $emailManager->envoyerRejetDemande($demandeTest, $commentaire);
    if ($resultat) {
        echo "✅ <strong>Email de rejet envoyé avec succès</strong><br>";
    } else {
        echo "❌ <strong>Échec envoi email de rejet</strong><br>";
    }
} catch (Exception $e) {
    echo "❌ <strong>Erreur:</strong> " . htmlspecialchars($e->getMessage()) . "<br>";
}

echo "<hr>";

// Test 4: Email de confirmation (pour comparaison)
echo "<h3>4. 📧 Test Email de Confirmation (Référence)</h3>";
$demandeTest['statut'] = 'en_attente';

try {
    $resultat = $emailManager->envoyerConfirmationDemande($demandeTest);
    if ($resultat) {
        echo "✅ <strong>Email de confirmation envoyé avec succès</strong><br>";
    } else {
        echo "❌ <strong>Échec envoi email de confirmation</strong><br>";
    }
} catch (Exception $e) {
    echo "❌ <strong>Erreur:</strong> " . htmlspecialchars($e->getMessage()) . "<br>";
}

echo "<h2>🔍 Vérification</h2>";
echo "<div style='background: #f8f9fa; padding: 15px; border: 1px solid #ddd;'>";
echo "<h4>Si les tests sont réussis, vérifiez :</h4>";
echo "<ol>";
echo "<li><strong>Votre email :</strong> sambasy837@gmail.com (4 nouveaux emails)</li>";
echo "<li><strong>Dossier spam :</strong> Vérifiez les spams</li>";
echo "<li><strong>Emails simulés :</strong> <a href='voir_emails_simules.php' target='_blank'>Voir la page de simulation</a></li>";
echo "</ol>";
echo "</div>";

echo "<h2>🔧 Diagnostic des Problèmes</h2>";
echo "<div style='background: #fff3cd; padding: 15px; border: 1px solid #ffc107;'>";
echo "<h4>Si les emails ne passent pas lors des changements de statut :</h4>";
echo "<ul>";
echo "<li><strong>Vérifiez les logs :</strong> <a href='logs/app_" . date('Y-m-d') . ".log' target='_blank'>Logs du jour</a></li>";
echo "<li><strong>Testez manuellement :</strong> Ce test fonctionne-t-il ?</li>";
echo "<li><strong>Vérifiez admin_traiter_demande.php :</strong> Les méthodes sont-elles appelées ?</li>";
echo "<li><strong>Problème de données :</strong> La variable \$demande est-elle correcte ?</li>";
echo "</ul>";
echo "</div>";

echo "<h2>📊 Prochaines Étapes</h2>";
echo "<ol>";
echo "<li>Si ce test fonctionne → Le problème vient de admin_traiter_demande.php</li>";
echo "<li>Si ce test échoue → Le problème vient d'EmailManager</li>";
echo "<li>Vérifiez les logs pour voir les erreurs exactes</li>";
echo "<li>Testez un vrai changement de statut après ces corrections</li>";
echo "</ol>";

echo "<div style='margin-top: 30px;'>";
echo "<a href='admin_dashboard.php' style='background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>← Retour Admin</a> ";
echo "<a href='voir_emails_simules.php' style='background: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>📧 Voir Emails</a>";
echo "</div>";
?>
