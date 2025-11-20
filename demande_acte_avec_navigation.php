<?php
/**
 * Page de demande d'acte d'état civil avec navigation
 * Mairie de Khombole
 */

// Configuration de la page
$pageTitle = 'Nouvelle Demande d\'Acte';
$breadcrumbs = [
    ['title' => 'Services', 'url' => 'menu.php#services'],
    ['title' => 'Nouvelle demande']
];

// Section hero
$showHero = true;
$heroTitle = 'Demande d\'Acte d\'État Civil';
$heroSubtitle = 'Effectuez votre demande d\'acte en ligne rapidement et en toute sécurité';
$heroButton = [
    'text' => 'Voir le guide',
    'url' => 'menu.php#guide',
    'icon' => 'fas fa-question-circle'
];

// CSS personnalisé
$additionalCSS = '
<style>
.form-container {
    background: white;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0,0,0,0.1);
    padding: 2.5rem;
    margin-bottom: 2rem;
    border: 1px solid #e9ecef;
}

.section-header {
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    color: white;
    padding: 1rem 1.5rem;
    border-radius: 10px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.form-label {
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border: 2px solid #e9ecef;
    border-radius: 10px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
    font-size: 0.95rem;
}

.form-control:focus, .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(11, 132, 62, 0.25);
}

.required-field::after {
    content: " *";
    color: #dc3545;
    font-weight: bold;
}

.btn-primary {
    background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
    border: none;
    border-radius: 25px;
    padding: 1rem 2rem;
    font-weight: 600;
    font-size: 1.1rem;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(11, 132, 62, 0.3);
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(11, 132, 62, 0.4);
}

.info-box {
    background: linear-gradient(135deg, #e3f2fd, #bbdefb);
    border-left: 5px solid var(--accent-color);
    padding: 1.5rem;
    border-radius: 10px;
    margin-bottom: 2rem;
}

.payment-info {
    background: linear-gradient(135deg, #f8f9ff, #e8f4fd);
    border: 2px solid var(--primary-color);
    border-radius: 15px;
    padding: 2rem;
    margin-top: 1rem;
    box-shadow: 0 5px 15px rgba(11, 132, 62, 0.1);
}

.payment-method {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 1rem;
    background: white;
    border-radius: 10px;
    margin: 0.5rem 0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
}

.payment-method:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.payment-icon {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: white;
    font-size: 0.9rem;
}

.wave-icon {
    background: linear-gradient(135deg, #ff6b35, #f7931e);
}

.orange-icon {
    background: linear-gradient(135deg, #ff8c00, #ffa500);
}

.checkbox-container {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 1.5rem;
    margin: 1rem 0;
    border: 2px solid #e9ecef;
}

.form-check-input:checked {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.progress-indicator {
    background: white;
    border-radius: 15px;
    padding: 1rem;
    margin-bottom: 2rem;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.step {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 0.5rem;
}

.step-number {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: var(--primary-color);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .form-container {
        padding: 1.5rem;
        margin: 0 10px;
    }
}
</style>';

// Inclure le header public
require_once 'public_header.php';
?>

<!-- Information Box -->
<div class="info-box">
    <div class="d-flex align-items-start gap-3">
        <i class="fas fa-info-circle fs-4 text-primary mt-1"></i>
        <div>
            <h5 class="fw-bold text-primary mb-2">Informations importantes</h5>
            <p class="mb-2">Ce formulaire permet de faire une demande d'acte d'état civil auprès de la Mairie de Khombole.</p>
            <p class="mb-2">Les informations recueillies sont nécessaires au traitement de votre demande et sont protégées conformément à la <strong>Loi n°2008-12 du 25 janvier 2008</strong> relative à la protection des données personnelles.</p>
            <p class="mb-0"><strong>Veuillez remplir tous les champs obligatoires avant de soumettre votre demande.</strong></p>
        </div>
    </div>
</div>

<!-- Progress Indicator -->
<div class="progress-indicator">
    <div class="row">
        <div class="col-md-4">
            <div class="step">
                <div class="step-number">1</div>
                <span class="fw-semibold">Type d'acte</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="step">
                <div class="step-number">2</div>
                <span class="fw-semibold">Informations personnelles</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="step">
                <div class="step-number">3</div>
                <span class="fw-semibold">Validation</span>
            </div>
        </div>
    </div>
</div>

<!-- Main Form -->
<form method="POST" action="traiter_demande.php" class="needs-validation" novalidate>
    <div class="form-container">
        <!-- Section 1: Type d'acte -->
        <div class="section-header">
            <i class="fas fa-file-alt"></i>
            <h4 class="mb-0">1. Type d'acte demandé</h4>
        </div>

        <div class="row g-3">
            <div class="col-md-8">
                <label for="type_acte" class="form-label required-field">Type d'acte</label>
                <select class="form-select" id="type_acte" name="type_acte" required>
                    <option value="">Sélectionnez le type d'acte</option>
                    <option value="extrait_naissance">Extrait d'acte de naissance</option>
                    <option value="copie_litterale_naissance">Copie littérale d'acte de naissance</option>
                    <option value="extrait_mariage">Extrait d'acte de mariage</option>
                    <option value="certificat_residence">Certificat de résidence</option>
                    <option value="certificat_vie_individuelle">Certificat de vie individuelle</option>
                    <option value="certificat_vie_collective">Certificat de vie collective</option>
                    <option value="certificat_deces">Certificat de décès</option>
                </select>
                <div class="invalid-feedback">
                    Veuillez sélectionner un type d'acte.
                </div>
            </div>
            <div class="col-md-4">
                <label for="nombre_exemplaires" class="form-label required-field">Nombre d'exemplaires</label>
                <select class="form-select" id="nombre_exemplaires" name="nombre_exemplaires" required>
                    <option value="">Choisir</option>
                    <option value="1" selected>1 exemplaire</option>
                    <option value="2">2 exemplaires</option>
                    <option value="3">3 exemplaires</option>
                    <option value="4">4 exemplaires</option>
                    <option value="5">5 exemplaires</option>
                </select>
                <div class="invalid-feedback">
                    Veuillez sélectionner le nombre d'exemplaires.
                </div>
            </div>
        </div>
    </div>

    <div class="form-container">
        <!-- Section 2: Informations du demandeur -->
        <div class="section-header">
            <i class="fas fa-user"></i>
            <h4 class="mb-0">2. Informations du demandeur</h4>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label for="nom" class="form-label required-field">Nom</label>
                <input type="text" class="form-control" id="nom" name="nom" required>
                <div class="invalid-feedback">
                    Veuillez saisir votre nom.
                </div>
            </div>
            <div class="col-md-6">
                <label for="prenoms" class="form-label required-field">Prénom(s)</label>
                <input type="text" class="form-control" id="prenoms" name="prenoms" required>
                <div class="invalid-feedback">
                    Veuillez saisir vos prénoms.
                </div>
            </div>
            <div class="col-md-6">
                <label for="date_naissance" class="form-label required-field">Date de naissance</label>
                <input type="date" class="form-control" id="date_naissance" name="date_naissance" required>
                <div class="invalid-feedback">
                    Veuillez saisir votre date de naissance.
                </div>
            </div>
            <div class="col-md-6">
                <label for="lieu_naissance" class="form-label required-field">Lieu de naissance</label>
                <input type="text" class="form-control" id="lieu_naissance" name="lieu_naissance" required>
                <div class="invalid-feedback">
                    Veuillez saisir votre lieu de naissance.
                </div>
            </div>
            <div class="col-md-6">
                <label for="annee_registre" class="form-label required-field">Année du registre</label>
                <input type="number" class="form-control" id="annee_registre" name="annee_registre" min="1900" max="2024" required>
                <div class="invalid-feedback">
                    Veuillez saisir l'année du registre.
                </div>
            </div>
            <div class="col-md-6">
                <label for="numero_registre" class="form-label required-field">Numéro dans le registre</label>
                <input type="text" class="form-control" id="numero_registre" name="numero_registre" required>
                <div class="invalid-feedback">
                    Veuillez saisir le numéro dans le registre.
                </div>
            </div>
            <div class="col-12">
                <label for="qualite_demandeur" class="form-label required-field">Demandeur</label>
                <select class="form-select" id="qualite_demandeur" name="qualite_demandeur" required>
                    <option value="">Sélectionnez votre qualité</option>
                    <option value="titulaire">Titulaire de l'acte</option>
                    <option value="parent">Parent</option>
                    <option value="representant_legal">Représentant légal</option>
                </select>
                <div class="invalid-feedback">
                    Veuillez sélectionner votre qualité.
                </div>
            </div>
            <div class="col-12">
                <label for="adresse_actuelle" class="form-label required-field">Adresse actuelle</label>
                <textarea class="form-control" id="adresse_actuelle" name="adresse_actuelle" rows="3" required></textarea>
                <div class="invalid-feedback">
                    Veuillez saisir votre adresse actuelle.
                </div>
            </div>
            <div class="col-md-6">
                <label for="telephone" class="form-label required-field">Téléphone</label>
                <input type="tel" class="form-control" id="telephone" name="telephone" pattern="[0-9]{9}" required>
                <div class="invalid-feedback">
                    Veuillez saisir un numéro de téléphone valide (9 chiffres).
                </div>
            </div>
            <div class="col-md-6">
                <label for="email" class="form-label required-field">Adresse e-mail</label>
                <input type="email" class="form-control" id="email" name="email" required>
                <div class="invalid-feedback">
                    Veuillez saisir une adresse e-mail valide.
                </div>
            </div>
        </div>
    </div>

    <div class="form-container">
        <!-- Section 3: Mode de délivrance et paiement -->
        <div class="section-header">
            <i class="fas fa-cogs"></i>
            <h4 class="mb-0">3. Mode de délivrance et paiement</h4>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label for="mode_delivrance" class="form-label required-field">Mode de délivrance souhaité</label>
                <select class="form-select" id="mode_delivrance" name="mode_delivrance" required>
                    <option value="">Sélectionnez le mode de délivrance</option>
                    <option value="retrait_physique">Retrait physique à la mairie</option>
                    <option value="envoi_electronique">Envoi électronique</option>
                </select>
                <div class="invalid-feedback">
                    Veuillez sélectionner un mode de délivrance.
                </div>
            </div>
            <div class="col-md-6">
                <label for="mode_paiement" class="form-label required-field">Mode de paiement</label>
                <select class="form-select" id="mode_paiement" name="mode_paiement" required>
                    <option value="">Sélectionnez le mode de paiement</option>
                    <option value="wave">Par WAVE (781210618)</option>
                    <option value="orange_money">Par Orange Money (781210618)</option>
                </select>
                <div class="invalid-feedback">
                    Veuillez sélectionner un mode de paiement.
                </div>
            </div>
        </div>

        <div class="payment-info mt-3">
            <div class="d-flex align-items-center gap-2 mb-3">
                <i class="fas fa-credit-card text-primary"></i>
                <h6 class="mb-0 fw-bold text-primary">Informations de paiement</h6>
            </div>
            
            <div class="payment-method">
                <div class="payment-icon wave-icon">
                    WAVE
                </div>
                <div>
                    <strong>WAVE</strong><br>
                    <span class="text-muted">Numéro : </span><strong>781210618</strong>
                </div>
            </div>
            
            <div class="payment-method">
                <div class="payment-icon orange-icon">
                    OM
                </div>
                <div>
                    <strong>Orange Money</strong><br>
                    <span class="text-muted">Numéro : </span><strong>781210618</strong>
                </div>
            </div>
            
            <div class="alert alert-info mt-3 mb-0">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Important :</strong> Effectuez le paiement et conservez la référence de transaction.
            </div>
        </div>
    </div>

    <div class="form-container">
        <!-- Section 4: Consentements -->
        <div class="section-header">
            <i class="fas fa-shield-alt"></i>
            <h4 class="mb-0">4. Consentements et validation</h4>
        </div>

        <div class="checkbox-container">
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="consentement_donnees" name="consentement_donnees" required>
                <label class="form-check-label" for="consentement_donnees">
                    <strong>Je donne mon consentement libre et éclairé au traitement de mes données personnelles.</strong>
                </label>
                <div class="invalid-feedback">
                    Vous devez donner votre consentement pour continuer.
                </div>
            </div>
            <div class="small text-muted mb-3">
                En soumettant ce formulaire, je consens à la collecte et au traitement de mes données personnelles destinées exclusivement au traitement de cette présente demande. Ces données ne seront ni vendues ni partagées à des tiers non autorisés et seront conservées dans des conditions sécurisées. Je reconnais avoir été informé(e) de mon droit d'accès, de rectification ou de suppression de mes données.
            </div>
        </div>

        <div class="checkbox-container">
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="acceptation_clause" name="acceptation_clause" required>
                <label class="form-check-label" for="acceptation_clause">
                    <strong>J'ai lu, compris et accepte la clause de non-responsabilité de la Mairie de Khombole.</strong>
                </label>
                <div class="invalid-feedback">
                    Vous devez accepter la clause de non-responsabilité.
                </div>
            </div>
            <div class="small text-muted">
                La Mairie de Khombole s'engage à protéger la confidentialité et la sécurité des données collectées. Cependant, elle ne saurait être tenue responsable des utilisations non autorisées, des erreurs externes ou des incidents techniques indépendants de sa volonté. En soumettant ce formulaire, je reconnais avoir pris connaissance de cette clause et dégage la Mairie de toute responsabilité en cas de litige lié à des causes externes au traitement légal de mes données.
            </div>
        </div>

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-paper-plane me-2"></i>
                Soumettre ma demande
            </button>
        </div>
    </div>
</form>

<!-- Actions rapides -->
<div class="row mt-4">
    <div class="col-md-4">
        <div class="card border-0 bg-light">
            <div class="card-body text-center">
                <i class="fas fa-search fa-2x text-primary mb-2"></i>
                <h6 class="fw-bold">Suivi de demande</h6>
                <p class="text-muted small">Suivez l'état de votre demande</p>
                <a href="suivi_demande.php" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-arrow-right me-1"></i>Accéder
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 bg-light">
            <div class="card-body text-center">
                <i class="fas fa-info-circle fa-2x text-info mb-2"></i>
                <h6 class="fw-bold">Informations</h6>
                <p class="text-muted small">Tarifs, délais et documents</p>
                <a href="menu.php#informations" class="btn btn-outline-info btn-sm">
                    <i class="fas fa-arrow-right me-1"></i>Voir
                </a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 bg-light">
            <div class="card-body text-center">
                <i class="fas fa-phone fa-2x text-success mb-2"></i>
                <h6 class="fw-bold">Contact</h6>
                <p class="text-muted small">Besoin d'aide ?</p>
                <a href="menu.php#contact" class="btn btn-outline-success btn-sm">
                    <i class="fas fa-arrow-right me-1"></i>Contacter
                </a>
            </div>
        </div>
    </div>
</div>

<?php
// JavaScript personnalisé
$additionalJS = '
<script>
// Validation du formulaire
(function() {
    "use strict";
    window.addEventListener("load", function() {
        var forms = document.getElementsByClassName("needs-validation");
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener("submit", function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add("was-validated");
            }, false);
        });
    }, false);
})();

// Animation des sections au scroll
const observerOptions = {
    threshold: 0.1,
    rootMargin: "0px 0px -50px 0px"
};

const observer = new IntersectionObserver(function(entries) {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = "1";
            entry.target.style.transform = "translateY(0)";
        }
    });
}, observerOptions);

document.querySelectorAll(".form-container").forEach(container => {
    container.style.opacity = "0";
    container.style.transform = "translateY(20px)";
    container.style.transition = "all 0.6s ease";
    observer.observe(container);
});

// Formatage automatique du téléphone
document.getElementById("telephone").addEventListener("input", function(e) {
    let value = e.target.value.replace(/\D/g, "");
    if (value.length > 9) {
        value = value.substring(0, 9);
    }
    e.target.value = value;
});

// Validation de l\'année
document.getElementById("annee_registre").addEventListener("input", function(e) {
    const currentYear = new Date().getFullYear();
    const year = parseInt(e.target.value);
    if (year > currentYear) {
        e.target.value = currentYear;
    }
});
</script>';

// Inclure le footer public
require_once 'public_footer.php';
?>
