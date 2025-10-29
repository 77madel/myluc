/**
 * Analytics Tracker - Système de tracking des utilisateurs
 * Collecte automatiquement les données de navigation
 */

(function() {
    'use strict';
    
    // Configuration
    const TRACKING_URL = '/analytics/track';
    const CONVERSION_URL = '/analytics/conversion';
    const SEND_INTERVAL = 15000; // Envoyer toutes les 15 secondes
    
    // Générer ou récupérer l'ID de session
    function getOrCreateSessionId() {
        let sessionId = sessionStorage.getItem('analytics_session_id');
        if (!sessionId) {
            sessionId = 'sess_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            sessionStorage.setItem('analytics_session_id', sessionId);
            console.log('📊 [Analytics] Nouvelle session créée:', sessionId);
        }
        return sessionId;
    }
    
    // Détecter le type d'appareil
    function getDeviceType() {
        const ua = navigator.userAgent;
        if (/(tablet|ipad|playbook|silk)|(android(?!.*mobi))/i.test(ua)) {
            return 'tablet';
        }
        if (/Mobile|Android|iP(hone|od)|IEMobile|BlackBerry|Kindle|Silk-Accelerated|(hpw|web)OS|Opera M(obi|ini)/.test(ua)) {
            return 'mobile';
        }
        return 'desktop';
    }
    
    // Données de tracking
    const analyticsData = {
        session_id: getOrCreateSessionId(),
        device: {
            type: getDeviceType(),
            screen_width: window.screen.width,
            screen_height: window.screen.height,
        },
        page: {
            url: window.location.href,
            title: document.title,
            referrer: document.referrer || null
        },
        time_on_page: 0,
        scroll_depth: 0
    };
    
    // Variables de tracking
    let startTime = Date.now();
    let maxScroll = 0;
    let dataSent = false;
    
    // Fonction pour envoyer les données
    function sendAnalytics(useBeacon = false) {
        if (dataSent) return; // Éviter les envois multiples
        
        // Calculer le temps passé
        analyticsData.time_on_page = Math.floor((Date.now() - startTime) / 1000);
        analyticsData.scroll_depth = maxScroll;
        
        const payload = JSON.stringify(analyticsData);
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        
        if (!csrfToken) {
            console.warn('⚠️ [Analytics] CSRF token not found');
            return;
        }
        
        if (useBeacon && navigator.sendBeacon) {
            // Utiliser sendBeacon pour garantir l'envoi (au départ de la page)
            const blob = new Blob([payload], { type: 'application/json' });
            const formData = new FormData();
            formData.append('data', blob);
            formData.append('_token', csrfToken);
            
            navigator.sendBeacon(TRACKING_URL, payload);
            dataSent = true;
            console.log('📤 [Analytics] Données envoyées (beacon)');
        } else {
            // Utiliser fetch normal
            fetch(TRACKING_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: payload,
                keepalive: true
            }).then(response => {
                if (response.ok) {
                    console.log('📤 [Analytics] Données envoyées (fetch)');
                }
            }).catch(err => {
                console.error('❌ [Analytics] Erreur:', err);
            });
        }
    }
    
    // Tracker le scroll
    let scrollTimeout;
    window.addEventListener('scroll', () => {
        clearTimeout(scrollTimeout);
        scrollTimeout = setTimeout(() => {
            const scrollPercent = Math.floor(
                (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100
            );
            maxScroll = Math.max(maxScroll, Math.min(scrollPercent, 100));
        }, 100);
    });
    
    // Envoyer périodiquement (toutes les 15 secondes)
    setInterval(() => {
        if (!dataSent) {
            sendAnalytics(false);
        }
    }, SEND_INTERVAL);
    
    // Envoyer au départ de la page (garantie avec sendBeacon)
    window.addEventListener('beforeunload', () => {
        sendAnalytics(true);
    });
    
    // Envoyer quand la page devient invisible (mobile)
    document.addEventListener('visibilitychange', () => {
        if (document.hidden && !dataSent) {
            sendAnalytics(true);
        }
    });
    
    // Log initial
    console.log('✅ [Analytics] Tracker démarré', {
        session_id: analyticsData.session_id,
        device: analyticsData.device.type,
        page: analyticsData.page.title
    });
    
    // Exposer la fonction de conversion globalement
    window.trackConversion = function(type) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        
        fetch(CONVERSION_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                session_id: analyticsData.session_id,
                type: type
            })
        }).then(response => {
            if (response.ok) {
                console.log('🎯 [Analytics] Conversion tracked:', type);
            }
        }).catch(err => {
            console.error('❌ [Analytics] Conversion error:', err);
        });
    };
    
})();

