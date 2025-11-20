        </div> <!-- Fin container-fluid -->
    </div> <!-- Fin main-content -->

    <!-- Footer Admin -->
    <footer class="bg-dark text-light py-3 mt-auto">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <small>
                        <i class="fas fa-shield-alt me-1"></i>
                        Administration Mairie de Khombole - Version 1.0
                    </small>
                </div>
                <div class="col-md-6 text-end">
                    <small>
                        <i class="fas fa-clock me-1"></i>
                        Connecté depuis <?= date('H:i', $adminInfo['login_time'] ?? time()) ?>
                    </small>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Scripts communs -->
    <script>
        // Fonction pour afficher les statistiques
        function showStats() {
            // Placeholder pour les statistiques
            alert('Fonctionnalité statistiques à implémenter');
        }

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

        // Confirmation pour les actions dangereuses
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-danger') || e.target.closest('.btn-danger')) {
                if (!confirm('Êtes-vous sûr de vouloir effectuer cette action ?')) {
                    e.preventDefault();
                    return false;
                }
            }
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

        // Raccourcis clavier
        document.addEventListener('keydown', function(e) {
            // Ctrl+H : Retour à l'accueil admin
            if (e.ctrlKey && e.key === 'h') {
                e.preventDefault();
                window.location.href = 'liste_demandes.php';
            }
            
            // Ctrl+L : Déconnexion
            if (e.ctrlKey && e.key === 'l') {
                e.preventDefault();
                if (confirm('Voulez-vous vous déconnecter ?')) {
                    window.location.href = 'admin_logout.php';
                }
            }
        });

        // Actualisation automatique des données (optionnel)
        <?php if (isset($autoRefresh) && $autoRefresh): ?>
        setInterval(function() {
            // Recharger la page toutes les 5 minutes
            if (document.visibilityState === 'visible') {
                location.reload();
            }
        }, 300000);
        <?php endif; ?>
    </script>

    <?php if (isset($additionalJS)): ?>
        <?= $additionalJS ?>
    <?php endif; ?>

</body>
</html>
