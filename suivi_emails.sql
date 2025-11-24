-- Table pour le suivi des emails envoyés
-- Mairie de Khombole - Système de notification par email

USE mairie_khombole;

-- Table pour suivre tous les emails envoyés
CREATE TABLE IF NOT EXISTS suivi_emails (
    id INT AUTO_INCREMENT PRIMARY KEY,
    demande_id INT NOT NULL,
    email_destinataire VARCHAR(255) NOT NULL,
    sujet VARCHAR(500) NOT NULL,
    type_email ENUM('confirmation', 'validation', 'pret', 'rejet', 'autre') NOT NULL,
    statut_envoi ENUM('envoye', 'echec', 'lu', 'clique') DEFAULT 'envoye',
    date_envoi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_lecture TIMESTAMP NULL,
    ip_lecture VARCHAR(45) NULL,
    user_agent_lecture TEXT NULL,
    erreur_envoi TEXT NULL,
    
    -- Métadonnées
    commentaire_admin TEXT NULL,
    
    FOREIGN KEY (demande_id) REFERENCES demandes_actes(id) ON DELETE CASCADE,
    INDEX idx_demande_id (demande_id),
    INDEX idx_email_destinataire (email_destinataire),
    INDEX idx_type_email (type_email),
    INDEX idx_statut_envoi (statut_envoi),
    INDEX idx_date_envoi (date_envoi)
) ENGINE=InnoDB;

-- Table pour les templates d'emails
CREATE TABLE IF NOT EXISTS templates_emails (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_template VARCHAR(100) UNIQUE NOT NULL,
    sujet_template VARCHAR(500) NOT NULL,
    contenu_html TEXT NOT NULL,
    contenu_texte TEXT NULL,
    variables_disponibles JSON NULL,
    actif BOOLEAN DEFAULT TRUE,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_modification TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_nom_template (nom_template),
    INDEX idx_actif (actif)
) ENGINE=InnoDB;

-- Insertion des templates par défaut
INSERT INTO templates_emails (nom_template, sujet_template, contenu_html, variables_disponibles) VALUES
('confirmation_demande', 'Confirmation de réception - Demande {numero_demande}', 
'<h2>Demande reçue avec succès</h2><p>Bonjour {nom_complet},</p><p>Votre demande d\'acte d\'état civil a été reçue et enregistrée avec succès.</p>', 
'["numero_demande", "nom_complet", "type_acte", "date_soumission"]'),

('validation_demande', 'Demande validée - {numero_demande}', 
'<h2>Demande validée et en traitement</h2><p>Bonjour {nom_complet},</p><p>Bonne nouvelle ! Votre demande a été validée et est maintenant en cours de traitement.</p>', 
'["numero_demande", "nom_complet", "commentaire"]'),

('demande_prete', 'Acte prêt pour retrait - {numero_demande}', 
'<h2>Votre acte est prêt !</h2><p>Bonjour {nom_complet},</p><p>Excellente nouvelle ! Votre acte d\'état civil est maintenant prêt.</p>', 
'["numero_demande", "nom_complet", "commentaire"]'),

('demande_rejetee', 'Demande rejetée - {numero_demande}', 
'<h2>Demande rejetée</h2><p>Bonjour {nom_complet},</p><p>Nous regrettons de vous informer que votre demande a été rejetée.</p>', 
'["numero_demande", "nom_complet", "motif_rejet"]');

-- Vue pour les statistiques d'emails
CREATE OR REPLACE VIEW vue_stats_emails AS
SELECT 
    DATE(date_envoi) as date_envoi,
    type_email,
    statut_envoi,
    COUNT(*) as nombre_emails,
    COUNT(CASE WHEN statut_envoi = 'envoye' THEN 1 END) as emails_envoyes,
    COUNT(CASE WHEN statut_envoi = 'echec' THEN 1 END) as emails_echec,
    COUNT(CASE WHEN statut_envoi = 'lu' THEN 1 END) as emails_lus,
    ROUND(COUNT(CASE WHEN statut_envoi = 'lu' THEN 1 END) * 100.0 / COUNT(*), 2) as taux_lecture
FROM suivi_emails 
GROUP BY DATE(date_envoi), type_email, statut_envoi
ORDER BY date_envoi DESC, type_email;
