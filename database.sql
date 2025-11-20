-- Base de données pour les demandes d'actes d'état civil - Mairie de Khombole
-- KH-TRA-11-00

CREATE DATABASE IF NOT EXISTS mairie_khombole CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mairie_khombole;

-- Table pour les demandes d'actes
CREATE TABLE IF NOT EXISTS demandes_actes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_demande VARCHAR(20) UNIQUE NOT NULL,
    type_acte ENUM(
        'extrait_naissance',
        'copie_litterale_naissance', 
        'extrait_mariage',
        'certificat_residence',
        'certificat_vie_individuelle',
        'certificat_vie_collective',
        'certificat_deces'
    ) NOT NULL,
    nombre_exemplaires INT DEFAULT 1 NOT NULL,
    
    -- Informations du demandeur
    nom VARCHAR(100) NOT NULL,
    prenoms VARCHAR(200) NOT NULL,
    date_naissance DATE NOT NULL,
    lieu_naissance VARCHAR(100) NOT NULL,
    annee_registre YEAR NOT NULL,
    numero_registre VARCHAR(50) NOT NULL,
    qualite_demandeur ENUM('titulaire', 'parent', 'representant_legal') NOT NULL,
    adresse_actuelle TEXT NOT NULL,
    telephone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    
    -- Mode de délivrance et paiement
    mode_delivrance ENUM('retrait_physique', 'envoi_electronique') NOT NULL,
    mode_paiement ENUM('wave', 'orange_money') NOT NULL,
    
    -- Consentements
    consentement_donnees BOOLEAN NOT NULL DEFAULT FALSE,
    acceptation_clause BOOLEAN NOT NULL DEFAULT FALSE,
    
    -- Statut et suivi
    statut ENUM('en_attente', 'en_traitement', 'pret', 'delivre', 'rejete') DEFAULT 'en_attente',
    commentaire_admin TEXT,
    
    -- Dates
    date_soumission TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    date_traitement TIMESTAMP NULL,
    date_delivrance TIMESTAMP NULL,
    
    -- Métadonnées
    ip_soumission VARCHAR(45),
    user_agent TEXT,
    
    INDEX idx_numero_demande (numero_demande),
    INDEX idx_statut (statut),
    INDEX idx_type_acte (type_acte),
    INDEX idx_date_soumission (date_soumission)
) ENGINE=InnoDB;

-- Table pour l'historique des actions
CREATE TABLE IF NOT EXISTS historique_demandes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    demande_id INT NOT NULL,
    action VARCHAR(50) NOT NULL,
    ancien_statut VARCHAR(20),
    nouveau_statut VARCHAR(20),
    commentaire TEXT,
    utilisateur VARCHAR(100),
    date_action TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (demande_id) REFERENCES demandes_actes(id) ON DELETE CASCADE,
    INDEX idx_demande_id (demande_id),
    INDEX idx_date_action (date_action)
) ENGINE=InnoDB;

-- Insertion des données de test
INSERT INTO demandes_actes (
    numero_demande, type_acte, nom, prenoms, date_naissance, lieu_naissance,
    annee_registre, numero_registre, qualite_demandeur, adresse_actuelle,
    telephone, email, mode_delivrance, mode_paiement, consentement_donnees,
    acceptation_clause, statut
) VALUES 
(
    'KH-2024-001', 'extrait_naissance', 'DIOP', 'Amadou Samba', '1990-05-15',
    'Khombole', '1990', '125', 'titulaire', 'Quartier Centre, Khombole',
    '781234567', 'amadou.diop@email.com', 'retrait_physique', 'wave',
    TRUE, TRUE, 'en_attente'
);
