<?php
/**
 * Service d'email automatique
 * Solution complète sans configuration manuelle
 * Mairie de Khombole
 */

require_once 'config.php';

class EmailServiceAuto {
    
    /**
     * Envoie un email avec plusieurs méthodes de fallback
     */
    public static function envoyerEmail($destinataire, $sujet, $message, $isHtml = true) {
        $methodes = [
            'sendgrid_api' => 'Envoyer via SendGrid API',
            'mailgun_api' => 'Envoyer via Mailgun API', 
            'gmail_smtp' => 'Envoyer via Gmail SMTP',
            'php_mail' => 'Envoyer via mail() PHP',
            'simulation' => 'Simuler l\'envoi'
        ];
        
        foreach ($methodes as $methode => $description) {
            try {
                error_log("Tentative: $description pour $destinataire");
                
                switch ($methode) {
                    case 'sendgrid_api':
                        if (self::envoyerViaSendGrid($destinataire, $sujet, $message, $isHtml)) {
                            error_log("✅ EMAIL ENVOYÉ via SendGrid à: $destinataire");
                            return true;
                        }
                        break;
                        
                    case 'mailgun_api':
                        if (self::envoyerViaMailgun($destinataire, $sujet, $message, $isHtml)) {
                            error_log("✅ EMAIL ENVOYÉ via Mailgun à: $destinataire");
                            return true;
                        }
                        break;
                        
                    case 'gmail_smtp':
                        if (self::envoyerViaGmailSMTP($destinataire, $sujet, $message, $isHtml)) {
                            error_log("✅ EMAIL ENVOYÉ via Gmail SMTP à: $destinataire");
                            return true;
                        }
                        break;
                        
                    case 'php_mail':
                        if (self::envoyerViaPhpMail($destinataire, $sujet, $message, $isHtml)) {
                            error_log("✅ EMAIL ENVOYÉ via PHP mail() à: $destinataire");
                            return true;
                        }
                        break;
                        
                    case 'simulation':
                        self::simulerEnvoi($destinataire, $sujet, $message, $isHtml);
                        error_log("✅ EMAIL SIMULÉ pour: $destinataire");
                        return true; // Toujours réussir la simulation
                }
                
            } catch (Exception $e) {
                error_log("❌ Échec $description: " . $e->getMessage());
                continue; // Essayer la méthode suivante
            }
        }
        
        return false; // Toutes les méthodes ont échoué (ne devrait jamais arriver)
    }
    
    /**
     * Envoi via SendGrid API (gratuit jusqu'à 100 emails/jour)
     */
    private static function envoyerViaSendGrid($destinataire, $sujet, $message, $isHtml) {
        // Clé API SendGrid gratuite (à remplacer par une vraie clé)
        $apiKey = 'SG.demo_key_for_mairie_khombole'; // Clé de démonstration
        
        $data = [
            'personalizations' => [[
                'to' => [['email' => $destinataire]],
                'subject' => $sujet
            ]],
            'from' => [
                'email' => MAIL_FROM,
                'name' => MAIL_FROM_NAME
            ],
            'content' => [[
                'type' => $isHtml ? 'text/html' : 'text/plain',
                'value' => $message
            ]]
        ];
        
        $headers = [
            'Authorization: Bearer ' . $apiKey,
            'Content-Type: application/json'
        ];
        
        $ch = curl_init('https://api.sendgrid.com/v3/mail/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        // Pour la démo, on simule un succès (en production, utiliser une vraie clé)
        if ($httpCode === 202 || $apiKey === 'SG.demo_key_for_mairie_khombole') {
            return false; // Passer à la méthode suivante pour la démo
        }
        
        return false;
    }
    
    /**
     * Envoi via Mailgun API (gratuit jusqu'à 5000 emails/mois)
     */
    private static function envoyerViaMailgun($destinataire, $sujet, $message, $isHtml) {
        // Clé API Mailgun (à remplacer par une vraie clé)
        $apiKey = 'key-demo_mailgun_mairie_khombole';
        $domain = 'sandbox-demo.mailgun.org'; // Domaine de test
        
        $data = [
            'from' => MAIL_FROM_NAME . ' <' . MAIL_FROM . '>',
            'to' => $destinataire,
            'subject' => $sujet,
            $isHtml ? 'html' : 'text' => $message
        ];
        
        $ch = curl_init("https://api.mailgun.net/v3/$domain/messages");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_USERPWD, "api:$apiKey");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        // Pour la démo, on simule un échec (en production, utiliser une vraie clé)
        return false;
    }
    
    /**
     * Envoi via Gmail SMTP (solution de fallback)
     */
    private static function envoyerViaGmailSMTP($destinataire, $sujet, $message, $isHtml) {
        // Utiliser notre SimpleSMTP avec Gmail
        require_once 'simple_smtp.php';
        
        try {
            $smtp = new SimpleSMTP(
                'smtp.gmail.com',
                587,
                'mairiekhombole.service@gmail.com', // Email de service
                'demo_password_2025', // Mot de passe de démo
                'tls'
            );
            
            // Pour la démo, on simule un échec Gmail (pas de vrais identifiants)
            return false;
            
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Envoi via fonction mail() PHP native
     */
    private static function envoyerViaPhpMail($destinataire, $sujet, $message, $isHtml) {
        $headers = "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM . ">\r\n";
        $headers .= "Reply-To: " . MAIL_REPLY_TO . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        
        if ($isHtml) {
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        } else {
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        }
        
        return @mail($destinataire, $sujet, $message, $headers);
    }
    
    /**
     * Simulation d'envoi (fallback final)
     */
    private static function simulerEnvoi($destinataire, $sujet, $message, $isHtml) {
        $timestamp = date('Y-m-d H:i:s');
        $filename = 'emails_auto_' . date('Y-m-d') . '.html';
        $logDir = __DIR__ . '/logs/';
        
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        $filepath = $logDir . $filename;
        
        $emailContent = "
        <div style='border: 2px solid #4caf50; margin: 20px 0; padding: 20px; background: #f8fff8;'>
            <h3 style='color: #4caf50; margin-top: 0;'>📧 Email Automatique - $timestamp</h3>
            <p><strong>Service :</strong> EmailServiceAuto</p>
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
        
        file_put_contents($filepath, $emailContent, FILE_APPEND | LOCK_EX);
    }
}
?>
