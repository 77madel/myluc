{{-- 
    Composant de surveillance de session (ADMIN/PORTALS)
    Vérifie toutes les 30 secondes si la session est toujours valide
--}}

@if(auth()->check() || auth()->guard('admin')->check())
<script>
(function() {
    'use strict';
    
    // Configuration
    const CHECK_INTERVAL = 30000; // 30 secondes
    const SESSION_CHECK_URL = '{{ route("session.check") }}';
    const CSRF_TOKEN = '{{ csrf_token() }}';
    
    let isChecking = false;
    
    // Fonction de vérification de session
    async function checkSessionStatus() {
        // Éviter les requêtes multiples simultanées
        if (isChecking) return;
        
        isChecking = true;
        
        try {
            const response = await fetch(SESSION_CHECK_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            
            const data = await response.json();
            
            console.log('🔍 [Session Monitor] Status:', data.status);
            
            // Si la session est invalide
            if (data.status === 'invalid') {
                console.warn('⚠️ [Session Monitor] Session invalide détectée !');
                
                // Afficher une notification toastr si disponible
                if (typeof toastr !== 'undefined') {
                    toastr.warning(data.message || '⚠️ Vous avez été déconnecté car une nouvelle connexion a été détectée.', '', {
                        timeOut: 5000,
                        closeButton: true,
                        progressBar: true
                    });
                }
                
                // Rediriger après 2 secondes
                setTimeout(() => {
                    window.location.href = data.redirect || '/login';
                }, 2000);
            }
        } catch (error) {
            console.error('❌ [Session Monitor] Erreur:', error);
        } finally {
            isChecking = false;
        }
    }
    
    // Démarrer la vérification périodique
    console.log('✅ [Session Monitor] Démarré - Vérification toutes les 30 secondes');
    
    // Première vérification après 5 secondes
    setTimeout(checkSessionStatus, 5000);
    
    // Vérifications régulières
    setInterval(checkSessionStatus, CHECK_INTERVAL);
    
    // Vérification lors du focus sur la fenêtre (l'utilisateur revient sur l'onglet)
    window.addEventListener('focus', () => {
        console.log('👁️ [Session Monitor] Focus détecté - Vérification immédiate');
        checkSessionStatus();
    });
    
    // Vérification lors de la visibilité de la page
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) {
            console.log('👁️ [Session Monitor] Page visible - Vérification immédiate');
            checkSessionStatus();
        }
    });
})();
</script>
@endif

