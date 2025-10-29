console.log('=== TEST PAYMENT SCRIPT LOADED ===');

(function() {
    function init() {
        console.log('Initializing...');

        var btn = document.getElementById('payWithPaydunya');
        if (!btn) {
            console.error('Button not found, retrying...');
            setTimeout(init, 500);
            return;
        }

        console.log('Button found:', btn);

        var csrf = document.querySelector('meta[name="csrf-token"]');
        if (!csrf) {
            console.error('CSRF token not found!');
            alert('CSRF token manquant!');
            return;
        }

        var csrfToken = csrf.content;
        console.log('CSRF token found');

        btn.onclick = function(e) {
            e.preventDefault();
            console.log('BUTTON CLICKED!');
            alert('Button clicked! Sending request...');

            btn.disabled = true;
            btn.textContent = 'Processing...';

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '/payment/form', true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.onload = function() {
                console.log('Response received:', xhr.status);
                console.log('Response text:', xhr.responseText);

                if (xhr.status >= 200 && xhr.status < 300) {
                    try {
                        var data = JSON.parse(xhr.responseText);
                        console.log('Parsed data:', data);

                        if (data.status === 'success' && data.data) {
                            var url = data.data.checkout_url;
                            console.log('Checkout URL:', url);

                            if (url) {
                                alert('Redirection vers Paydunya...');
                                window.location.href = url;
                            } else {
                                alert('URL de paiement introuvable');
                                resetButton();
                            }
                        } else {
                            alert('Erreur: ' + (data.message || 'Erreur inconnue'));
                            resetButton();
                        }
                    } catch (e) {
                        console.error('JSON parse error:', e);
                        alert('Erreur de parsing: ' + e.message);
                        resetButton();
                    }
                } else {
                    console.error('HTTP error:', xhr.status);
                    alert('Erreur HTTP: ' + xhr.status + '\n' + xhr.responseText);
                    resetButton();
                }
            };

            xhr.onerror = function() {
                console.error('Network error');
                alert('Erreur de connexion réseau');
                resetButton();
            };

            var requestData = JSON.stringify({ payment_method: 'paydunya' });
            console.log('Sending:', requestData);
            xhr.send(requestData);
        };

        function resetButton() {
            btn.disabled = false;
            btn.innerHTML = '<i class="ri-secure-payment-fill"></i> Pay Securely';
        }

        console.log('✅ Handler attached successfully!');
        alert('Script chargé! Cliquez sur le bouton pour tester.');
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        setTimeout(init, 100);
    }
})();
