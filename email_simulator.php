<?php
/**
 * Simulateur d'emails pour environnement de développement
 * Mairie de Khombole
 */

require_once 'config.php';

class EmailSimulator {
    
    /**
     * Simule l'envoi d'un email en le sauvegardant dans un fichier
     */
    public static function simulerEnvoi($destinataire, $sujet, $message, $isHtml = true) {
        $timestamp = date('Y-m-d H:i:s');
        $filename = 'emails_simules_' . date('Y-m-d') . '.html';
        $filepath = __DIR__ . '/logs/' . $filename;
        
        // Créer le dossier logs s'il n'existe pas
        if (!is_dir(__DIR__ . '/logs')) {
            mkdir(__DIR__ . '/logs', 0755, true);
        }
        
        $emailContent = "
        <div style='border: 2px solid #0b843e; margin: 20px 0; padding: 20px; background: #f8f9fa;'>
            <h3 style='color: #0b843e; margin-top: 0;'>📧 Email Simulé - $timestamp</h3>
            <p><strong>De :</strong> " . MAIL_FROM_NAME . " &lt;" . MAIL_FROM . "&gt;</p>
            <p><strong>À :</strong> $destinataire</p>
            <p><strong>Sujet :</strong> $sujet</p>
            <p><strong>Type :</strong> " . ($isHtml ? 'HTML' : 'Texte') . "</p>
            <hr>
            <div style='border: 1px solid #ddd; padding: 15px; background: white;'>
                $message
            </div>
        </div>
        ";
        
        // Ajouter au fichier de simulation
        file_put_contents($filepath, $emailContent, FILE_APPEND | LOCK_EX);
        
        // Logger aussi dans les logs PHP
        error_log("EMAIL SIMULÉ - À: $destinataire - Sujet: $sujet");
        
        return true;
    }
    
    /**
     * Affiche tous les emails simulés du jour
     */
    public static function afficherEmailsSimules() {
        $filename = 'emails_simules_' . date('Y-m-d') . '.html';
        $filepath = __DIR__ . '/logs/' . $filename;
        
        if (file_exists($filepath)) {
            echo "<h2>📧 Emails Simulés du " . date('d/m/Y') . "</h2>";
            echo file_get_contents($filepath);
        } else {
            echo "<p>Aucun email simulé aujourd'hui.</p>";
        }
    }
}

// Si appelé directement, afficher les emails simulés
if (basename($_SERVER['PHP_SELF']) === 'email_simulator.php') {
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Emails Simulés - Mairie de Khombole</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .header { background: #0b843e; color: white; padding: 20px; margin-bottom: 20px; }
        </style>
    </head>
    <body>
        <div class="header">
            <h1>📧 Simulateur d'Emails - Mairie de Khombole</h1>
            <p>Visualisation des emails qui auraient été envoyés</p>
        </div>
        
        <?php EmailSimulator::afficherEmailsSimules(); ?>
        
        <p><a href="demande_acte.php">← Retour au formulaire</a></p>
    </body>
    </html>
    <?php
}
?>
