        </div> <!-- Fin container -->
    </div> <!-- Fin main-content -->

    <!-- Footer Public -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5 class="fw-bold mb-3">
                        <img src="logo.jpg" alt="Logo" width="30" height="30" class="rounded-circle me-2">
                        Mairie de Khombole
                    </h5>
                    <p class="text-muted">
                        Service de demandes d'actes d'état civil en ligne. 
                        Simplifiez vos démarches administratives.
                    </p>
                </div>
                
                <div class="col-md-4 mb-3">
                    <h6 class="fw-bold mb-3">Services</h6>
                    <ul class="list-unstyled">
                        <li><a href="demande_acte.php" class="text-muted text-decoration-none">
                            <i class="fas fa-file-alt me-2"></i>Nouvelle demande
                        </a></li>
                        <li><a href="suivi_demande.php" class="text-muted text-decoration-none">
                            <i class="fas fa-search me-2"></i>Suivi de demande
                        </a></li>
                        <li><a href="menu.php#tarifs" class="text-muted text-decoration-none">
                            <i class="fas fa-euro-sign me-2"></i>Tarifs
                        </a></li>
                        <li><a href="menu.php#contact" class="text-muted text-decoration-none">
                            <i class="fas fa-phone me-2"></i>Contact
                        </a></li>
                    </ul>
                </div>
                
                <div class="col-md-4 mb-3">
                    <h6 class="fw-bold mb-3">Contact</h6>
                    <div class="text-muted">
                        <p><i class="fas fa-map-marker-alt me-2"></i>Khombole, Sénégal</p>
                        <p><i class="fas fa-phone me-2"></i>+221 78 121 06 18</p>
                        <p><i class="fas fa-envelope me-2"></i>mairiedekhombole@gmail.com</p>
                    </div>
                    
                    <div class="mt-3">
                        <h6 class="fw-bold">Paiement Mobile</h6>
                        <div class="d-flex gap-3">
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-mobile-alt me-1"></i>Wave: 78 121 06 18
                            </span>
                            <span class="badge bg-primary">
                                <i class="fas fa-mobile-alt me-1"></i>OM: 78 121 06 18
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr class="my-4">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <small class="text-muted">
                        © <?= date('Y') ?> Mairie de Khombole. Tous droits réservés.
                    </small>
                </div>
                <div class="col-md-6 text-end">
                    <small class="text-muted">
                        <i class="fas fa-shield-alt me-1"></i>
                        Plateforme sécurisée - Version 1.0
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Scripts communs -->
    <script>
        // Auto-hide des alertes après 5 secondes
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });

        // Smooth scroll pour les liens d'ancrage
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Indicateur de chargement pour les formulaires
        document.addEventListener('submit', function(e) {
            const form = e.target;
            const submitBtn = form.querySelector('button[type="submit"]');
            
            if (submitBtn && !submitBtn.disabled) {
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Traitement...';
                
                // Restaurer le bouton après 10 secondes (sécurité)
                setTimeout(function() {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }, 10000);
            }
        });

        // Validation en temps réel des formulaires
        document.addEventListener('input', function(e) {
            if (e.target.type === 'email') {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (e.target.value && !emailRegex.test(e.target.value)) {
                    e.target.setCustomValidity('Veuillez saisir une adresse email valide');
                } else {
                    e.target.setCustomValidity('');
                }
            }
            
            if (e.target.type === 'tel') {
                const phoneRegex = /^7[0-9]{8}$/;
                if (e.target.value && !phoneRegex.test(e.target.value)) {
                    e.target.setCustomValidity('Format: 7XXXXXXXX (9 chiffres commençant par 7)');
                } else {
                    e.target.setCustomValidity('');
                }
            }
        });

        // Raccourcis clavier
        document.addEventListener('keydown', function(e) {
            // Ctrl+H : Retour à l'accueil
            if (e.ctrlKey && e.key === 'h') {
                e.preventDefault();
                window.location.href = 'menu.php';
            }
            
            // Ctrl+N : Nouvelle demande
            if (e.ctrlKey && e.key === 'n') {
                e.preventDefault();
                window.location.href = 'demande_acte.php';
            }
            
            // Ctrl+S : Suivi demande
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                window.location.href = 'suivi_demande.php';
            }
        });

        // Copier les numéros de téléphone au clic
        document.querySelectorAll('[data-copy]').forEach(element => {
            element.addEventListener('click', function() {
                const textToCopy = this.getAttribute('data-copy');
                navigator.clipboard.writeText(textToCopy).then(() => {
                    // Afficher une notification
                    const toast = document.createElement('div');
                    toast.className = 'toast-notification';
                    toast.textContent = 'Numéro copié !';
                    document.body.appendChild(toast);
                    
                    setTimeout(() => {
                        document.body.removeChild(toast);
                    }, 2000);
                });
            });
        });
    </script>

    <style>
        .toast-notification {
            position: fixed;
            top: 100px;
            right: 20px;
            background: var(--primary-color);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 5px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            z-index: 9999;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>

    <?php if (isset($additionalJS)): ?>
        <?= $additionalJS ?>
    <?php endif; ?>

</body>
</html>
