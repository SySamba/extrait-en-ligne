<?php
/**
 * Gestionnaire d'emails pour la Mairie de Khombole
 * Utilise PHPMailer pour l'envoi d'emails
 */

require_once 'config.php';

// Import des classes PHPMailer (si PHPMailer est installé)
// Décommentez ces lignes si vous avez installé PHPMailer via Composer
// use PHPMailer\PHPMailer\PHPMailer;
// use PHPMailer\PHPMailer\SMTP;
// use PHPMailer\PHPMailer\Exception;

class EmailManager {
    private $usePhpMailer;
    private $pdo;
    
    public function __construct() {
        $this->usePhpMailer = class_exists('PHPMailer\PHPMailer\PHPMailer');
        
        // Connexion à la base de données pour le suivi
        try {
            require_once 'db_connection.php';
            $this->pdo = createPDOConnection();
        } catch (Exception $e) {
            error_log("Erreur connexion DB dans EmailManager: " . $e->getMessage());
            $this->pdo = null;
        }
    }
    
    /**
     * Envoie un email
     */
    public function envoyerEmail($destinataire, $sujet, $message, $isHtml = true) {
        try {
            // Toujours utiliser la fonction mail() native pour plus de simplicité
            return $this->envoyerAvecMailNative($destinataire, $sujet, $message, $isHtml);
        } catch (Exception $e) {
            error_log("Erreur envoi email à $destinataire: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Envoi avec PHPMailer (recommandé)
     */
    private function envoyerAvecPhpMailer($destinataire, $sujet, $message, $isHtml) {
        $mail = new PHPMailer(true);
        
        try {
            // Configuration du serveur
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = SMTP_ENCRYPTION;
            $mail->Port = SMTP_PORT;
            $mail->CharSet = 'UTF-8';
            
            // Expéditeur
            $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
            $mail->addReplyTo(MAIL_REPLY_TO, MAIL_FROM_NAME);
            
            // Destinataire
            $mail->addAddress($destinataire);
            
            // Contenu
            $mail->isHTML($isHtml);
            $mail->Subject = $sujet;
            $mail->Body = $message;
            
            if ($isHtml) {
                $mail->AltBody = strip_tags($message);
            }
            
            $mail->send();
            return true;
            
        } catch (Exception $e) {
            error_log("Erreur PHPMailer: " . $mail->ErrorInfo);
            return false;
        }
    }
    
    /**
     * Envoi avec fonction mail() native (fallback)
     */
    private function envoyerAvecMailNative($destinataire, $sujet, $message, $isHtml) {
        $headers = "From: " . MAIL_FROM_NAME . " <" . MAIL_FROM . ">\r\n";
        $headers .= "Reply-To: " . MAIL_REPLY_TO . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        
        if ($isHtml) {
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        } else {
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
        }
        
        return mail($destinataire, $sujet, $message, $headers);
    }
    
    /**
     * Enregistre le suivi d'un email dans la base de données
     */
    private function enregistrerSuiviEmail($demandeId, $destinataire, $sujet, $typeEmail, $statutEnvoi, $erreur = null) {
        if (!$this->pdo) {
            return false;
        }
        
        try {
            $sql = "INSERT INTO suivi_emails (demande_id, email_destinataire, sujet, type_email, statut_envoi, erreur_envoi) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$demandeId, $destinataire, $sujet, $typeEmail, $statutEnvoi, $erreur]);
        } catch (Exception $e) {
            error_log("Erreur enregistrement suivi email: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Email de confirmation de réception de demande
     */
    public function envoyerConfirmationDemande($demande) {
        $typesActes = [
            'extrait_naissance' => 'Extrait d\'acte de naissance',
            'copie_litterale_naissance' => 'Copie littérale d\'acte de naissance',
            'extrait_mariage' => 'Extrait d\'acte de mariage',
            'certificat_residence' => 'Certificat de résidence',
            'certificat_vie_individuelle' => 'Certificat de vie individuelle',
            'certificat_vie_collective' => 'Certificat de vie collective',
            'certificat_deces' => 'Certificat de décès'
        ];
        
        $sujet = "Confirmation de réception - Demande " . $demande['numero_demande'];
        
        $message = $this->genererTemplateHtml([
            'titre' => 'Demande reçue avec succès',
            'nom_complet' => $demande['prenoms'] . ' ' . $demande['nom'],
            'numero_demande' => $demande['numero_demande'],
            'type_acte' => $typesActes[$demande['type_acte']] ?? $demande['type_acte'],
            'date_soumission' => date('d/m/Y à H:i', strtotime($demande['date_soumission'])),
            'statut' => 'En attente de traitement',
            'message_principal' => 'Votre demande d\'acte d\'état civil a été reçue et enregistrée avec succès.',
            'message_secondaire' => 'Vous recevrez une notification dès que votre demande sera traitée.',
            'couleur_statut' => '#ffc107'
        ]);
        
        $resultat = $this->envoyerEmail($demande['email'], $sujet, $message, true);
        
        // Enregistrer le suivi
        $this->enregistrerSuiviEmail(
            $demande['id'], 
            $demande['email'], 
            $sujet, 
            'confirmation', 
            $resultat ? 'envoye' : 'echec'
        );
        
        return $resultat;
    }
    
    /**
     * Email de validation de demande
     */
    public function envoyerValidationDemande($demande, $commentaire = '') {
        $sujet = "Demande validée - " . $demande['numero_demande'];
        
        $message = $this->genererTemplateHtml([
            'titre' => 'Demande validée et en traitement',
            'nom_complet' => $demande['prenoms'] . ' ' . $demande['nom'],
            'numero_demande' => $demande['numero_demande'],
            'date_traitement' => date('d/m/Y à H:i'),
            'statut' => 'En cours de traitement',
            'message_principal' => 'Bonne nouvelle ! Votre demande a été validée et est maintenant en cours de traitement.',
            'message_secondaire' => 'Vous recevrez une nouvelle notification dès que votre acte sera prêt.',
            'commentaire' => $commentaire,
            'couleur_statut' => '#17a2b8'
        ]);
        
        $resultat = $this->envoyerEmail($demande['email'], $sujet, $message, true);
        
        // Enregistrer le suivi
        $this->enregistrerSuiviEmail(
            $demande['id'], 
            $demande['email'], 
            $sujet, 
            'validation', 
            $resultat ? 'envoye' : 'echec'
        );
        
        return $resultat;
    }
    
    /**
     * Email de demande prête
     */
    public function envoyerDemandePrete($demande, $commentaire = '') {
        $sujet = "Acte prêt pour retrait - " . $demande['numero_demande'];
        
        $message = $this->genererTemplateHtml([
            'titre' => 'Votre acte est prêt !',
            'nom_complet' => $demande['prenoms'] . ' ' . $demande['nom'],
            'numero_demande' => $demande['numero_demande'],
            'date_traitement' => date('d/m/Y à H:i'),
            'statut' => 'Prêt pour retrait',
            'message_principal' => 'Excellente nouvelle ! Votre acte d\'état civil est maintenant prêt.',
            'message_secondaire' => 'Vous pouvez venir le retirer aux heures d\'ouverture de la mairie avec une pièce d\'identité.',
            'commentaire' => $commentaire,
            'couleur_statut' => '#28a745',
            'info_supplementaire' => 'Horaires : Lundi à Vendredi de 8h à 17h'
        ]);
        
        $resultat = $this->envoyerEmail($demande['email'], $sujet, $message, true);
        
        // Enregistrer le suivi
        $this->enregistrerSuiviEmail(
            $demande['id'], 
            $demande['email'], 
            $sujet, 
            'pret', 
            $resultat ? 'envoye' : 'echec'
        );
        
        return $resultat;
    }
    
    /**
     * Email de rejet de demande
     */
    public function envoyerRejetDemande($demande, $motifRejet) {
        $sujet = "Demande rejetée - " . $demande['numero_demande'];
        
        $message = $this->genererTemplateHtml([
            'titre' => 'Demande rejetée',
            'nom_complet' => $demande['prenoms'] . ' ' . $demande['nom'],
            'numero_demande' => $demande['numero_demande'],
            'date_traitement' => date('d/m/Y à H:i'),
            'statut' => 'Rejetée',
            'message_principal' => 'Nous regrettons de vous informer que votre demande a été rejetée.',
            'message_secondaire' => 'Vous pouvez nous contacter pour plus d\'informations ou soumettre une nouvelle demande.',
            'commentaire' => $motifRejet,
            'couleur_statut' => '#dc3545',
            'info_supplementaire' => 'Contact : etat.civil@mairiedekhombole.sn'
        ]);
        
        $resultat = $this->envoyerEmail($demande['email'], $sujet, $message, true);
        
        // Enregistrer le suivi
        $this->enregistrerSuiviEmail(
            $demande['id'], 
            $demande['email'], 
            $sujet, 
            'rejet', 
            $resultat ? 'envoye' : 'echec'
        );
        
        return $resultat;
    }
    
    /**
     * Génère un template HTML pour les emails
     */
    private function genererTemplateHtml($data) {
        $html = '
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>' . htmlspecialchars($data['titre']) . '</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f4f4f4; }
                .container { max-width: 600px; margin: 0 auto; background-color: #ffffff; }
                .header { background: linear-gradient(135deg, #0b843e, #1e3a8a); color: white; padding: 30px; text-align: center; }
                .header h1 { margin: 0; font-size: 24px; }
                .content { padding: 30px; }
                .status-badge { display: inline-block; padding: 8px 16px; border-radius: 20px; color: white; font-weight: bold; margin: 10px 0; background-color: ' . ($data['couleur_statut'] ?? '#6c757d') . '; }
                .info-box { background-color: #f8f9fa; border-left: 4px solid #0b843e; padding: 15px; margin: 20px 0; }
                .footer { background-color: #f8f9fa; padding: 20px; text-align: center; font-size: 14px; color: #666; }
                .btn { display: inline-block; padding: 12px 24px; background-color: #0b843e; color: white; text-decoration: none; border-radius: 5px; margin: 10px 0; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h1>🏛️ Mairie de Khombole</h1>
                    <p>État Civil</p>
                </div>
                
                <div class="content">
                    <h2>' . htmlspecialchars($data['titre']) . '</h2>
                    
                    <p>Bonjour <strong>' . htmlspecialchars($data['nom_complet']) . '</strong>,</p>
                    
                    <p>' . htmlspecialchars($data['message_principal']) . '</p>
                    
                    <div class="info-box">
                        <p><strong>📄 Numéro de demande :</strong> ' . htmlspecialchars($data['numero_demande']) . '</p>
                        ' . (isset($data['type_acte']) ? '<p><strong>📋 Type d\'acte :</strong> ' . htmlspecialchars($data['type_acte']) . '</p>' : '') . '
                        ' . (isset($data['date_soumission']) ? '<p><strong>📅 Date de soumission :</strong> ' . htmlspecialchars($data['date_soumission']) . '</p>' : '') . '
                        ' . (isset($data['date_traitement']) ? '<p><strong>⏰ Date de traitement :</strong> ' . htmlspecialchars($data['date_traitement']) . '</p>' : '') . '
                        <p><strong>📊 Statut :</strong> <span class="status-badge">' . htmlspecialchars($data['statut']) . '</span></p>
                    </div>
                    
                    ' . (!empty($data['commentaire']) ? '<div class="info-box"><p><strong>💬 Commentaire :</strong><br>' . nl2br(htmlspecialchars($data['commentaire'])) . '</p></div>' : '') . '
                    
                    <p>' . htmlspecialchars($data['message_secondaire']) . '</p>
                    
                    ' . (!empty($data['info_supplementaire']) ? '<p><em>' . htmlspecialchars($data['info_supplementaire']) . '</em></p>' : '') . '
                    
                    <p>Cordialement,<br>
                    <strong>L\'équipe de la Mairie de Khombole</strong></p>
                </div>
                
                <div class="footer">
                    <p>📧 etat.civil@mairiedekhombole.sn | 📞 +221 33 XXX XX XX</p>
                    <p>Mairie de Khombole - Service État Civil</p>
                    <p><small>Cet email a été envoyé automatiquement, merci de ne pas y répondre directement.</small></p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
}
?>
