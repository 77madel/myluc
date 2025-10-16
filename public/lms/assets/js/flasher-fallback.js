// Créez ce fichier: public/js/flasher-fallback.js
// Ceci empêchera l'erreur Flasher de bloquer vos autres scripts

(function() {
    'use strict';

    console.log('Flasher fallback loaded');

    // Créer un objet Flasher basique si il n'existe pas
    if (typeof window.flasher === 'undefined') {
        window.flasher = {
            render: function() {
                console.log('Flasher render called (fallback)');
            },
            success: function(message) {
                console.log('Flasher success:', message);
                // Utiliser alert comme fallback
                if (message) alert('✓ ' + message);
            },
            error: function(message) {
                console.log('Flasher error:', message);
                if (message) alert('✗ ' + message);
            },
            info: function(message) {
                console.log('Flasher info:', message);
                if (message) alert('ℹ ' + message);
            },
            warning: function(message) {
                console.log('Flasher warning:', message);
                if (message) alert('⚠ ' + message);
            }
        };
    }

    // Si renderCallback est appelé avant que Flasher soit chargé
    if (typeof window.renderCallback === 'undefined') {
        window.renderCallback = function() {
            console.log('renderCallback called (fallback)');
        };
    }

    console.log('Flasher fallback initialized');
})();
