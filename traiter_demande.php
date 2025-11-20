<?php
/**
 * Script de traitement des demandes d'actes d'état civil
 * Mairie de Khombole - KH-TRA-11-00
 */

session_start();

// Connexion à la base de données
require_once 'db_connection.php';

// Classe pour gérer les demandes d'actes
class DemandeActe {
    private $pdo;
    
    public function __construct() {
        try {
            $this->pdo = createPDOConnection();
        } catch (Exception $e) {
            throw new Exception("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }
    
    /**
     * Génère un numéro de demande unique
     */
    private function genererNumeroDemande() {
        $annee = date('Y');
        $mois = date('m');
        
        // Compter les demandes du mois
        $sql = "SELECT COUNT(*) as total FROM demandes_actes 
                WHERE YEAR(date_soumission) = ? AND MONTH(date_soumission) = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$annee, $mois]);
        $count = $stmt->fetch()['total'] + 1;
        
        return sprintf("KH-%s-%s-%03d", $annee, $mois, $count);
    }
    
    /**
     * Valide les données du formulaire
     */
    private function validerDonnees($donnees) {
        $erreurs = [];
        
        // Champs obligatoires
        $champsObligatoires = [
            'type_acte', 'nombre_exemplaires', 'nom', 'prenoms', 'date_naissance', 'lieu_naissance',
            'annee_registre', 'numero_registre', 'qualite_demandeur', 
            'adresse_actuelle', 'telephone', 'email', 'mode_delivrance', 'mode_paiement'
        ];
        
        foreach ($champsObligatoires as $champ) {
            if (empty($donnees[$champ])) {
                $erreurs[] = "Le champ '$champ' est obligatoire.";
            }
        }
        
        // Validation spécifique
        if (!empty($donnees['email']) && !filter_var($donnees['email'], FILTER_VALIDATE_EMAIL)) {
            $erreurs[] = "L'adresse e-mail n'est pas valide.";
        }
        
        if (!empty($donnees['telephone']) && !preg_match('/^[0-9]{9}$/', $donnees['telephone'])) {
            $erreurs[] = "Le numéro de téléphone doit contenir 9 chiffres.";
        }
        
        if (!empty($donnees['annee_registre'])) {
            $annee = intval($donnees['annee_registre']);
            if ($annee < 1900 || $annee > date('Y')) {
                $erreurs[] = "L'année du registre n'est pas valide.";
            }
        }
        
        // Validation des consentements
        if (empty($donnees['consentement_donnees'])) {
            $erreurs[] = "Vous devez donner votre consentement au traitement des données.";
        }
        
        if (empty($donnees['acceptation_clause'])) {
            $erreurs[] = "Vous devez accepter la clause de non-responsabilité.";
        }
        
        return $erreurs;
    }
    
    /**
     * Nettoie et sécurise les données
     */
    private function nettoyerDonnees($donnees) {
        $donneesNettoyees = [];
        
        foreach ($donnees as $cle => $valeur) {
            if (is_string($valeur)) {
                $donneesNettoyees[$cle] = trim(htmlspecialchars($valeur, ENT_QUOTES, 'UTF-8'));
            } else {
                $donneesNettoyees[$cle] = $valeur;
            }
        }
        
        return $donneesNettoyees;
    }
    
    /**
     * Enregistre une nouvelle demande
     */
    public function enregistrerDemande($donnees) {
        // Nettoyer les données
        $donnees = $this->nettoyerDonnees($donnees);
        
        // Valider les données
        $erreurs = $this->validerDonnees($donnees);
        if (!empty($erreurs)) {
            throw new Exception("Erreurs de validation : " . implode(', ', $erreurs));
        }
        
        try {
            $this->pdo->beginTransaction();
            
            // Générer le numéro de demande
            $numeroDemande = $this->genererNumeroDemande();
            
            // Préparer les données pour l'insertion
            $sql = "INSERT INTO demandes_actes (
                numero_demande, type_acte, nombre_exemplaires, nom, prenoms, date_naissance, lieu_naissance,
                annee_registre, numero_registre, qualite_demandeur, adresse_actuelle,
                telephone, email, mode_delivrance, mode_paiement, consentement_donnees,
                acceptation_clause, ip_soumission, user_agent
            ) VALUES (
                :numero_demande, :type_acte, :nombre_exemplaires, :nom, :prenoms, :date_naissance, :lieu_naissance,
                :annee_registre, :numero_registre, :qualite_demandeur, :adresse_actuelle,
                :telephone, :email, :mode_delivrance, :mode_paiement, :consentement_donnees,
                :acceptation_clause, :ip_soumission, :user_agent
            )";
            
            $stmt = $this->pdo->prepare($sql);
            
            $parametres = [
                ':numero_demande' => $numeroDemande,
                ':type_acte' => $donnees['type_acte'],
                ':nombre_exemplaires' => intval($donnees['nombre_exemplaires']),
                ':nom' => strtoupper($donnees['nom']),
                ':prenoms' => ucwords(strtolower($donnees['prenoms'])),
                ':date_naissance' => $donnees['date_naissance'],
                ':lieu_naissance' => ucwords(strtolower($donnees['lieu_naissance'])),
                ':annee_registre' => $donnees['annee_registre'],
                ':numero_registre' => $donnees['numero_registre'],
                ':qualite_demandeur' => $donnees['qualite_demandeur'],
                ':adresse_actuelle' => $donnees['adresse_actuelle'],
                ':telephone' => $donnees['telephone'],
                ':email' => strtolower($donnees['email']),
                ':mode_delivrance' => $donnees['mode_delivrance'],
                ':mode_paiement' => $donnees['mode_paiement'],
                ':consentement_donnees' => 1,
                ':acceptation_clause' => 1,
                ':ip_soumission' => $_SERVER['REMOTE_ADDR'] ?? 'Inconnue',
                ':user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Inconnu'
            ];
            
            $stmt->execute($parametres);
            $demandeId = $this->pdo->lastInsertId();
            
            // Enregistrer dans l'historique
            $this->ajouterHistorique($demandeId, 'creation', null, 'en_attente', 'Demande créée par le demandeur');
            
            $this->pdo->commit();
            
            return [
                'success' => true,
                'numero_demande' => $numeroDemande,
                'demande_id' => $demandeId
            ];
            
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw new Exception("Erreur lors de l'enregistrement : " . $e->getMessage());
        }
    }
    
    /**
     * Ajoute une entrée dans l'historique
     */
    private function ajouterHistorique($demandeId, $action, $ancienStatut, $nouveauStatut, $commentaire = '') {
        $sql = "INSERT INTO historique_demandes (
            demande_id, action, ancien_statut, nouveau_statut, commentaire, utilisateur
        ) VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            $demandeId,
            $action,
            $ancienStatut,
            $nouveauStatut,
            $commentaire,
            'Demandeur'
        ]);
    }
    
    /**
     * Récupère une demande par son numéro
     */
    public function getDemande($numeroDemande) {
        $sql = "SELECT * FROM demandes_actes WHERE numero_demande = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$numeroDemande]);
        return $stmt->fetch();
    }
    
    /**
     * Envoie un email de confirmation (simulation)
     */
    public function envoyerEmailConfirmation($demande) {
        // Simulation d'envoi d'email
        // Dans un environnement réel, utiliser PHPMailer ou une API d'email
        
        $typesActes = [
            'extrait_naissance' => 'Extrait d\'acte de naissance',
            'copie_litterale_naissance' => 'Copie littérale d\'acte de naissance',
            'extrait_mariage' => 'Extrait d\'acte de mariage',
            'certificat_residence' => 'Certificat de résidence',
            'certificat_vie_individuelle' => 'Certificat de vie individuelle',
            'certificat_vie_collective' => 'Certificat de vie collective',
            'certificat_deces' => 'Certificat de décès'
        ];
        
        $sujet = "Confirmation de votre demande d'acte - " . $demande['numero_demande'];
        
        $message = "
        Bonjour {$demande['prenoms']} {$demande['nom']},
        
        Votre demande d'acte d'état civil a été enregistrée avec succès.
        
        Détails de votre demande :
        - Numéro de demande : {$demande['numero_demande']}
        - Type d'acte : " . ($typesActes[$demande['type_acte']] ?? $demande['type_acte']) . "
        - Date de soumission : " . date('d/m/Y à H:i', strtotime($demande['date_soumission'])) . "
        - Statut : En attente de traitement
        
        Votre demande sera traitée dans les plus brefs délais.
        Vous recevrez une notification dès que votre acte sera prêt.
        
        Cordialement,
        La Mairie de Khombole
        ";
        
        // Log de l'email (en production, remplacer par un vrai envoi)
        error_log("EMAIL ENVOYÉ À : {$demande['email']} - SUJET : $sujet");
        
        return true;
    }
}

// Traitement de la demande
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $demandeActe = new DemandeActe($config);
        
        // Enregistrer la demande
        $resultat = $demandeActe->enregistrerDemande($_POST);
        
        if ($resultat['success']) {
            // Récupérer les détails de la demande
            $demande = $demandeActe->getDemande($resultat['numero_demande']);
            
            // Envoyer l'email de confirmation
            $demandeActe->envoyerEmailConfirmation($demande);
            
            // Rediriger vers la page de confirmation
            $_SESSION['demande_success'] = [
                'numero_demande' => $resultat['numero_demande'],
                'nom_complet' => $demande['prenoms'] . ' ' . $demande['nom'],
                'type_acte' => $demande['type_acte'],
                'email' => $demande['email']
            ];
            
            header('Location: confirmation_demande.php');
            exit;
        }
        
    } catch (Exception $e) {
        $_SESSION['demande_error'] = $e->getMessage();
        header('Location: demande_acte.php');
        exit;
    }
} else {
    // Redirection si accès direct
    header('Location: demande_acte.php');
    exit;
}
?>
