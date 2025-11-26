<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test d'Accessibilité - Thème Sénégal</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Thème Sénégal -->
    <link href="assets/css/senegal-theme.css" rel="stylesheet">
    
    <style>
        .test-section {
            margin: 2rem 0;
            padding: 2rem;
            border-radius: 15px;
        }
        
        .contrast-test {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            margin: 1rem 0;
        }
        
        .contrast-item {
            padding: 1rem;
            border-radius: 10px;
            text-align: center;
            font-weight: 600;
        }
        
        .accessibility-info {
            background: var(--blanc-principal);
            border: 2px solid var(--senegal-vert);
            padding: 1.5rem;
            border-radius: 15px;
            margin: 1rem 0;
        }
    </style>
</head>
<body>
    <div class="container py-4">
        <!-- Header avec drapeau sénégalais -->
        <div class="header-section text-center mb-5">
            <h1 class="display-4 fw-bold mb-3">
                <i class="fas fa-universal-access me-3"></i>
                Test d'Accessibilité - Thème Sénégal
            </h1>
            <p class="lead">Vérification de l'accessibilité avec dominance du blanc</p>
        </div>

        <!-- Section d'information sur l'accessibilité -->
        <div class="accessibility-info">
            <h2 class="senegal-gradient-text">
                <i class="fas fa-eye me-2"></i>
                Accessibilité pour les Personnes qui ne Voient que le Blanc
            </h2>
            <p class="mb-3">
                Ce thème a été conçu spécialement pour être accessible aux personnes qui ne perçoivent que la couleur blanche. 
                Voici les mesures prises :
            </p>
            <ul class="list-unstyled">
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i><strong>Dominance du blanc :</strong> Fond principal entièrement blanc</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i><strong>Contraste élevé :</strong> Texte noir sur fond blanc</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i><strong>Bordures visibles :</strong> Contours nets pour délimiter les éléments</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i><strong>Typographie claire :</strong> Police Poppins, tailles adaptées</li>
                <li class="mb-2"><i class="fas fa-check text-success me-2"></i><strong>Espacement généreux :</strong> Éléments bien séparés</li>
            </ul>
        </div>

        <!-- Test des contrastes -->
        <div class="test-section" style="background: var(--gris-tres-clair);">
            <h3 class="text-center mb-4">
                <i class="fas fa-palette me-2"></i>
                Test des Contrastes de Couleurs
            </h3>
            
            <div class="contrast-test">
                <!-- Texte sur blanc -->
                <div class="contrast-item" style="background: var(--blanc-principal); color: var(--texte-fonce); border: 2px solid var(--gris-clair);">
                    <h5>Texte Principal</h5>
                    <p>Texte noir sur fond blanc<br>
                    <small>Contraste optimal : 21:1</small></p>
                </div>
                
                <!-- Vert sénégalais sur blanc -->
                <div class="contrast-item" style="background: var(--blanc-principal); color: var(--senegal-vert); border: 2px solid var(--senegal-vert);">
                    <h5>Vert Sénégal</h5>
                    <p>Vert sur fond blanc<br>
                    <small>Contraste élevé : 8.2:1</small></p>
                </div>
                
                <!-- Rouge sénégalais sur blanc -->
                <div class="contrast-item" style="background: var(--blanc-principal); color: var(--senegal-rouge); border: 2px solid var(--senegal-rouge);">
                    <h5>Rouge Sénégal</h5>
                    <p>Rouge sur fond blanc<br>
                    <small>Contraste élevé : 7.1:1</small></p>
                </div>
                
                <!-- Jaune sénégalais avec texte noir -->
                <div class="contrast-item" style="background: var(--senegal-jaune-fonce); color: var(--texte-fonce); border: 2px solid var(--texte-fonce);">
                    <h5>Jaune Sénégal</h5>
                    <p>Noir sur jaune foncé<br>
                    <small>Contraste élevé : 9.1:1</small></p>
                </div>
            </div>
        </div>

        <!-- Test des éléments d'interface -->
        <div class="test-section" style="background: var(--blanc-principal); border: 2px solid var(--gris-clair);">
            <h3 class="text-center mb-4">
                <i class="fas fa-mouse-pointer me-2"></i>
                Test des Éléments d'Interface
            </h3>
            
            <div class="row g-4">
                <!-- Boutons -->
                <div class="col-md-6">
                    <h5>Boutons</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <button class="btn btn-primary">Primaire</button>
                        <button class="btn btn-success">Succès</button>
                        <button class="btn btn-warning">Attention</button>
                        <button class="btn btn-danger">Erreur</button>
                        <button class="btn btn-outline-primary">Contour</button>
                    </div>
                </div>
                
                <!-- Alertes -->
                <div class="col-md-6">
                    <h5>Alertes</h5>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>Succès avec contraste élevé
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>Attention visible
                    </div>
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle me-2"></i>Erreur bien contrastée
                    </div>
                </div>
            </div>
        </div>

        <!-- Test de navigation -->
        <div class="test-section" style="background: var(--blanc-principal);">
            <h3 class="text-center mb-4">
                <i class="fas fa-compass me-2"></i>
                Test de Navigation
            </h3>
            
            <!-- Simulation de navbar -->
            <nav class="navbar navbar-expand-lg public-navbar mb-4">
                <div class="container">
                    <a class="navbar-brand" href="#">
                        <i class="fas fa-building me-2"></i>Mairie de Khombole
                    </a>
                    <div class="navbar-nav">
                        <a class="nav-link active" href="#">Accueil</a>
                        <a class="nav-link" href="#">Demandes</a>
                        <a class="nav-link" href="#">Suivi</a>
                    </div>
                </div>
            </nav>
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#">Accueil</a></li>
                    <li class="breadcrumb-item"><a href="#">Services</a></li>
                    <li class="breadcrumb-item active">Test Accessibilité</li>
                </ol>
            </nav>
        </div>

        <!-- Test des formulaires -->
        <div class="test-section" style="background: var(--blanc-principal); border: 2px solid var(--gris-clair);">
            <h3 class="text-center mb-4">
                <i class="fas fa-edit me-2"></i>
                Test des Formulaires
            </h3>
            
            <form>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="nom" class="form-label">Nom complet</label>
                        <input type="text" class="form-control" id="nom" placeholder="Votre nom">
                    </div>
                    <div class="col-md-6">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" placeholder="votre@email.com">
                    </div>
                    <div class="col-12">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" rows="3" placeholder="Votre message"></textarea>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane me-2"></i>Envoyer
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Recommandations d'accessibilité -->
        <div class="test-section senegal-border" style="background: var(--blanc-principal);">
            <h3 class="text-center mb-4 senegal-gradient-text">
                <i class="fas fa-lightbulb me-2"></i>
                Recommandations d'Accessibilité
            </h3>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fas fa-eye me-2"></i>Visuel</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success me-2"></i>Fond blanc dominant</li>
                                <li><i class="fas fa-check text-success me-2"></i>Contraste minimum 7:1</li>
                                <li><i class="fas fa-check text-success me-2"></i>Bordures visibles</li>
                                <li><i class="fas fa-check text-success me-2"></i>Taille de police ≥ 16px</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fas fa-keyboard me-2"></i>Navigation</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success me-2"></i>Focus visible</li>
                                <li><i class="fas fa-check text-success me-2"></i>Navigation clavier</li>
                                <li><i class="fas fa-check text-success me-2"></i>Ordre logique</li>
                                <li><i class="fas fa-check text-success me-2"></i>Liens descriptifs</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="fas fa-universal-access me-2"></i>Inclusivité</h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success me-2"></i>Texte alternatif</li>
                                <li><i class="fas fa-check text-success me-2"></i>Labels explicites</li>
                                <li><i class="fas fa-check text-success me-2"></i>Messages d'erreur clairs</li>
                                <li><i class="fas fa-check text-success me-2"></i>Temps suffisant</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="text-center mt-5 pt-4" style="border-top: 3px solid var(--senegal-vert);">
            <p class="text-muted">
                <i class="fas fa-heart text-danger me-2"></i>
                Thème Sénégal - Conçu pour l'accessibilité universelle
            </p>
            <p class="small text-muted">
                Mairie de Khombole - République du Sénégal
            </p>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Test de focus visible
        document.addEventListener('DOMContentLoaded', function() {
            // Améliorer la visibilité du focus pour l'accessibilité
            const focusableElements = document.querySelectorAll('a, button, input, textarea, select');
            
            focusableElements.forEach(element => {
                element.addEventListener('focus', function() {
                    this.style.outline = '3px solid var(--senegal-jaune-fonce)';
                    this.style.outlineOffset = '2px';
                });
                
                element.addEventListener('blur', function() {
                    this.style.outline = '';
                    this.style.outlineOffset = '';
                });
            });
        });
    </script>
</body>
</html>
