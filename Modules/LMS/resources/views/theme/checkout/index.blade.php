{{--
@php
    $backendSetting = get_theme_option(key: 'backend_general') ?? null;
    $currency = $backendSetting['currency'] ?? 'USD-$';
    $currencySymbol = get_currency_symbol($currency);
    $cartType = session()->has('type') ? session()->get('type') : '';
@endphp

@push('css')
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script async src="https://sandbox.doku.com/jokul-checkout-js/v1/jokul-checkout-1.0.0.js"></script>
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://www.paypalobjects.com/api/checkout.js"></script>
@endpush

<x-frontend-layout>
    <x-theme::breadcrumbs.breadcrumb-one pageTitle="Checkout" pageRoute="{{ route('checkout.page') }}"
        pageName="Checkout" />
    <div class="container">
        @csrf
        <div class="grid grid-cols-12 gap-5">
            <!-- START FILTER SIDEBAR -->
            <div class="col-span-full lg:col-span-8">
                <h2 class="area-title xl:text-3xl mb-5">{{ translate('Payment Method') }}</h2>
                <div class="dashkit-tab flex flex-wrap items-center gap-2.5" id="paymentMethodTab">
                    @foreach (get_payment_method() as $payment)
                        @php
                            $logo =
                                $payment->logo && fileExists('lms/payments', $payment->logo) == true
                                    ? asset('storage/lms/payments/' . $payment->logo)
                                    : asset('lms/frontend/assets/images/payment-method/master-card.webp');
                        @endphp
                        <button
                            class="dashkit-tab-btn btn border border-border btn-lg !px-8 h-14 !rounded-full [&.active]:border-primary payment-item"
                            data-method="{{ strtolower($payment->method_name) }}"
                            data-action ="{{ route('payment.form') }}">
                            <img data-src="{{ $logo }}" alt="master card" class="w-20">
                        </button>
                    @endforeach
                </div>
                <div class="dashkit-tab-content mt-[60px]" id="paymentMethodTabContent">
                    <!-- MASTER CARD FORM -->
                    <div class="dashkit-tab-pane">
                        <x-theme::cards.empty title="Select Payment" />
                    </div>
                </div>
            </div>
            <!-- END FILTER SIDEBAR -->

            <!-- START TOTAL -->
            <div class="col-span-full lg:col-span-4">
                <div class="bg-primary-50 p-6 rounded-xl">
                    <h6 class="text-3xl text-heading dark:text-white !leading-none font-bold">
                        {{ translate('Your Order') }}
                    </h6>
                    <table class="w-full my-7">
                        @if ($cartType !== 'subscription')
                            <caption
                                class="text-xl text-heading dark:text-white !leading-none font-bold text-left rtl:text-right mb-5">
                                {{ translate('Cart Total') . ' ' . total_qty() }}
                            </caption>
                        @endif
                        <tbody class="divide-y divide-border border-t border-border">
                            <tr>
                                <td class="px-1 py-4 text-left rtl:text-right">
                                    <div
                                        class="flex items-center gap-2 area-description text-heading/70 !leading-none shrink-0">
                                        <span
                                            class="text-heading dark:text-white mb-0.5">{{ translate('Subtotal') }}</span>
                                    </div>
                                </td>
                                <td class="px-1 py-4 text-right rtl:text-left">
                                    <div class="text-heading/70 font-semibold leading-none">
                                        {{ $currencySymbol }}{{ number_format($data['totalPrice'], 2) ?? null }}
                                    </div>
                                </td>
                            </tr>
                            @if ($data['discountAmount'])
                                <tr>
                                    <td class="px-1 py-4 text-left rtl:text-right">
                                        <div
                                            class="flex items-center gap-2 area-description text-heading/70 !leading-none shrink-0">
                                            <span
                                                class="text-heading dark:text-white mb-0.5">{{ translate('Discount') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-1 py-4 text-right rtl:text-left">
                                        <div class="text-heading/70 font-semibold leading-none">
                                            {{ $currencySymbol }}{{ $data['discountAmount'] }}</div>
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td class="px-1 py-4 text-left rtl:text-right">
                                    <div
                                        class="flex items-center gap-2 area-description text-heading/70 !leading-none shrink-0">
                                        <span
                                            class="text-heading dark:text-white text-lg font-bold mb-0.5">{{ translate('Total') }}</span>
                                    </div>
                                </td>
                                <td class="px-1 py-4 text-right rtl:text-left">
                                    <div class="text-primary text-lg font-bold leading-none">
                                        @php
                                            $totalPrice = $data['discountAmount']
                                                ? $data['totalPrice'] - $data['discountAmount']
                                                : $data['totalPrice'];
                                        @endphp
                                        {{ $currencySymbol }}{{ number_format($totalPrice, 2) }}
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div id="pay-button">

                    </div>
                </div>
            </div>
            <!-- END TOTAL -->
        </div>
    </div>
    <!-- END INNER CONTENT AREA -->
</x-frontend-layout>
--}}

{{--@php
    $backendSetting = get_theme_option(key: 'backend_general') ?? null;
    $currency = $backendSetting['currency'] ?? 'USD-$';
    $currencySymbol = get_currency_symbol($currency);
    $cartType = session()->has('type') ? session()->get('type') : '';

    // Récupérer la méthode de paiement Paydunya
    $paymentMethod = get_payment_method()->first();
@endphp--}}

@php
    $backendSetting = get_theme_option(key: 'backend_general') ?? null;
    $currency = $backendSetting['currency'] ?? 'USD-$';
    $currencySymbol = get_currency_symbol($currency);
    $cartType = session()->has('type') ? session()->get('type') : '';

    // Récupérer la méthode de paiement Paydunya
    $paymentMethod = get_payment_method()->first();
@endphp

<script>
    (function() {
        function initPayment() {
            var btn = document.getElementById('payWithPaydunya');
            if (!btn) {
                setTimeout(initPayment, 500);
                return;
            }

            var loadingModal = document.getElementById('loadingModal');
            var csrf = document.querySelector('meta[name="csrf-token"]').content;

            // Récupérer le texte original du bouton pour le reset
            var originalBtnContent = btn.innerHTML;

            btn.onclick = function(e) {
                e.preventDefault();

                if (loadingModal) loadingModal.style.display = 'flex';
                btn.disabled = true;
                btn.textContent = 'Traitement en cours...';

                var xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route("payment.form") }}', true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        try {
                            var data = JSON.parse(xhr.responseText);

                            if (data.status === 'success') {
                                var url = data.data?.checkout_url;

                                if (!url && data.data?.button) {
                                    var temp = document.createElement('div');
                                    temp.innerHTML = data.data.button;
                                    var link = temp.querySelector('a');
                                    url = link?.href;
                                }

                                if (url) {
                                    window.location.href = url;
                                } else {
                                    alert('Erreur: URL de paiement introuvable');
                                    resetButton();
                                }
                            } else {
                                alert('Erreur: ' + (data.message || 'Erreur inconnue'));
                                resetButton();
                            }
                        } catch (e) {
                            alert('Erreur: ' + e.message);
                            resetButton();
                        }
                    } else {
                        alert('Erreur HTTP: ' + xhr.status);
                        resetButton();
                    }
                };

                xhr.onerror = function() {
                    alert('Erreur de connexion');
                    resetButton();
                };

                xhr.send(JSON.stringify({ payment_method: 'paydunya' }));
            };

            function resetButton() {
                if (loadingModal) loadingModal.style.display = 'none';
                btn.disabled = false;
                btn.innerHTML = originalBtnContent;
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initPayment);
        } else {
            setTimeout(initPayment, 100);
        }
    })();
</script>

<x-frontend-layout>
    <x-theme::breadcrumbs.breadcrumb-one pageTitle="Checkout" pageRoute="{{ route('checkout.page') }}"
                                         pageName="Checkout" />
    <div class="container py-10">
        @csrf
        <div class="grid grid-cols-12 gap-8">
            <!-- START PAYMENT SECTION -->
            <div class="col-span-full lg:col-span-8">
                <div class="bg-white dark:bg-dark-card rounded-2xl shadow-lg p-8">
                    <h2 class="text-3xl font-bold text-heading dark:text-white mb-6 flex items-center gap-3">
                        <i class="ri-shopping-cart-line text-primary"></i>
                        {{ translate('Finalize Your Order') }}
                    </h2>

                    <!-- Informations de commande -->
                    <div class="mb-8 p-6 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
                        <h3 class="text-lg font-semibold text-heading dark:text-white mb-4">
                            {{ translate('Order Summary') }}
                        </h3>
                        <div class="space-y-3">
                            @if ($cartType !== 'subscription')
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-heading/70 dark:text-white/70">{{ translate('Number of courses') }}</span>
                                    <span class="font-semibold text-heading dark:text-white">{{ total_qty() }}</span>
                                </div>
                            @else
                                <div class="flex justify-between items-center text-sm">
                                    <span class="text-heading/70 dark:text-white/70">{{ translate('Subscription Plan') }}</span>
                                    <span class="font-semibold text-heading dark:text-white">{{ translate('Premium') }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between items-center text-sm border-t border-blue-200 dark:border-blue-700 pt-3">
                                <span class="text-heading/70 dark:text-white/70">{{ translate('Subtotal') }}</span>
                                <span class="font-semibold text-heading dark:text-white">{{ $currencySymbol }}{{ number_format($data['totalPrice'], 2) }}</span>
                            </div>
                            @if ($data['discountAmount'])
                                <div class="flex justify-between items-center text-sm text-green-600">
                                    <span>{{ translate('Discount') }}</span>
                                    <span class="font-semibold">-{{ $currencySymbol }}{{ number_format($data['discountAmount'], 2) }}</span>
                                </div>
                            @endif
                            <div class="flex justify-between items-center text-lg border-t-2 border-blue-300 dark:border-blue-600 pt-3">
                                <span class="font-bold text-heading dark:text-white">{{ translate('Total to Pay') }}</span>
                                @php
                                    $totalPrice = $data['discountAmount']
                                        ? $data['totalPrice'] - $data['discountAmount']
                                        : $data['totalPrice'];
                                @endphp
                                <span class="font-bold text-2xl text-primary">{{ $currencySymbol }}{{ number_format($totalPrice, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Formulaire de paiement -->
                    <div class="space-y-6">
                        <div class="border-t border-border pt-6">
                            <h3 class="text-xl font-semibold text-heading dark:text-white mb-4 flex items-center gap-2">
                                <i class="ri-secure-payment-line text-primary"></i>
                                {{ translate('Payment Method') }}
                            </h3>

                            <!-- Logo Paydunya -->
                            @if($paymentMethod)
                                @php
                                    $logo = $paymentMethod->logo && fileExists('lms/payments', $paymentMethod->logo) == true
                                        ? asset('storage/lms/payments/' . $paymentMethod->logo)
                                        : asset('lms/frontend/assets/images/payment-method/paydunya.png');
                                @endphp
                                <div class="mb-6 p-4 bg-gray-50 dark:bg-dark-card-two rounded-lg flex items-center justify-center">
                                    <img src="{{ $logo }}" alt="Paydunya" class="h-12">
                                </div>
                            @endif

                            <!-- Bouton de paiement -->
                            <div id="payment-button-container">
                                <button type="button"
                                        id="payWithPaydunya"
                                        class="w-full btn btn-primary-solid h-16 rounded-xl text-lg font-semibold hover:bg-primary-dark transition-all duration-300 flex items-center justify-center gap-3 group"
                                >
                                    <i class="ri-secure-payment-fill text-2xl group-hover:scale-110 transition-transform"></i>
                                    <span>{{ translate('Pay Securely') }} - {{ number_format($totalPrice, 2) }} {{ $currencySymbol }}</span>
                                    <i class="ri-arrow-right-line text-xl group-hover:translate-x-2 transition-transform"></i>
                                </button>
                            </div>

                            <!-- Messages d'erreur/chargement -->
                            <div id="payment-message" class="mt-4 hidden"></div>
                        </div>

                        <!-- Méthodes de paiement acceptées -->
                        <div class="mt-8 p-6 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl border border-green-200 dark:border-green-800">
                            <h4 class="text-lg font-semibold text-heading dark:text-white mb-4 flex items-center gap-2">
                                <i class="ri-checkbox-circle-line text-green-600"></i>
                                {{ translate('Accepted Payment Methods') }}
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Mobile Money -->
                                <div class="flex items-start gap-3 p-3 bg-white dark:bg-dark-card rounded-lg">
                                    <i class="ri-smartphone-line text-2xl text-primary mt-1"></i>
                                    <div>
                                        <h5 class="font-semibold text-heading dark:text-white mb-1">Mobile Money</h5>
                                        <p class="text-sm text-heading/70 dark:text-white/70">MTN, Moov, Orange Money</p>
                                    </div>
                                </div>
                                <!-- Cartes bancaires -->
                                <div class="flex items-start gap-3 p-3 bg-white dark:bg-dark-card rounded-lg">
                                    <i class="ri-bank-card-line text-2xl text-primary mt-1"></i>
                                    <div>
                                        <h5 class="font-semibold text-heading dark:text-white mb-1">{{ translate('Bank Cards') }}</h5>
                                        <p class="text-sm text-heading/70 dark:text-white/70">Visa, Mastercard</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Informations de sécurité -->
                        <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                            <div class="flex items-start gap-3">
                                <i class="ri-shield-check-fill text-2xl text-blue-600 dark:text-blue-400 mt-1"></i>
                                <div>
                                    <h5 class="font-semibold text-heading dark:text-white mb-2">
                                        {{ translate('Secure Payment') }}
                                    </h5>
                                    <ul class="space-y-1 text-sm text-heading/70 dark:text-white/70">
                                        <li class="flex items-center gap-2">
                                            <i class="ri-check-line text-green-600"></i>
                                            {{ translate('256-bit SSL Encryption') }}
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <i class="ri-check-line text-green-600"></i>
                                            {{ translate('PCI DSS Certified') }}
                                        </li>
                                        <li class="flex items-center gap-2">
                                            <i class="ri-check-line text-green-600"></i>
                                            {{ translate('Your data is protected') }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- END PAYMENT SECTION -->

            <!-- START ORDER DETAILS SIDEBAR -->
            <div class="col-span-full lg:col-span-4">
                <div class="bg-white dark:bg-dark-card rounded-2xl shadow-lg p-6 sticky top-24">
                    <h3 class="text-2xl font-bold text-heading dark:text-white mb-6 flex items-center gap-2">
                        <i class="ri-file-list-3-line text-primary"></i>
                        {{ translate('Order Details') }}
                    </h3>

                    @if ($cartType !== 'subscription' && isset($data['cartCourses']))
                        <!-- Liste des cours -->
                        <div class="space-y-4 mb-6 max-h-96 overflow-y-auto scrollbar-thin">
                            @foreach($data['cartCourses'] as $course)
                                <div class="flex gap-3 p-3 bg-gray-50 dark:bg-dark-card-two rounded-lg">
                                    @if(isset($course['thumbnail']))
                                        <img src="{{ $course['thumbnail'] }}" alt="{{ $course['title'] ?? 'Course' }}" class="w-16 h-16 object-cover rounded">
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-sm text-heading dark:text-white truncate">
                                            {{ $course['title'] ?? $course['name'] ?? 'Course' }}
                                        </h4>
                                        <p class="text-xs text-heading/70 dark:text-white/70 mt-1">
                                            {{ $currencySymbol }}{{ number_format($course['price'] ?? $course['amount'] ?? 0, 2) }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Récapitulatif des prix -->
                    <div class="border-t border-border pt-4 space-y-3">
                        <div class="flex justify-between text-sm">
                            <span class="text-heading/70 dark:text-white/70">{{ translate('Subtotal') }}</span>
                            <span class="font-semibold text-heading dark:text-white">{{ $currencySymbol }} {{ number_format($data['totalPrice'], 2) }}</span>
                        </div>
                        @if ($data['discountAmount'])
                            <div class="flex justify-between text-sm text-green-600">
                                <span>{{ translate('Discount') }}</span>
                                <span class="font-semibold">{{ number_format($data['discountAmount'], 2) }}-{{ $currencySymbol }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between text-lg font-bold border-t border-border pt-3">
                            <span class="text-heading dark:text-white">{{ translate('Total') }}</span>
                            <span class="text-primary text-2xl">{{ number_format($totalPrice, 2) }} {{ $currencySymbol }}</span>
                        </div>
                    </div>

                    <!-- Garanties -->
                    <div class="mt-6 pt-6 border-t border-border">
                        <h4 class="font-semibold text-heading dark:text-white mb-3 text-sm">{{ translate('Our Guarantees') }}</h4>
                        <ul class="space-y-2 text-xs text-heading/70 dark:text-white/70">
                            <li class="flex items-center gap-2">
                                <i class="ri-time-line text-primary"></i>
                                {{ translate('Instant Access') }}
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="ri-refresh-line text-primary"></i>
                                {{ translate('30-Day Money Back Guarantee') }}
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="ri-customer-service-2-line text-primary"></i>
                                {{ translate('24/7 Support') }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- END ORDER DETAILS SIDEBAR -->
        </div>
    </div>

    <!-- Modal de chargement -->
    <div id="loadingModal" class="fixed inset-0 z-[9999] hidden bg-black/50 backdrop-blur-sm">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-dark-card rounded-2xl p-8 max-w-sm w-full text-center">
                <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-primary mx-auto mb-4"></div>
                <h3 class="text-xl font-semibold text-heading dark:text-white mb-2">
                    {{ translate('Processing Payment...') }}
                </h3>
                <p class="text-sm text-heading/70 dark:text-white/70">
                    {{ translate('Please wait, you will be redirected to Paydunya') }}
                </p>
            </div>
        </div>
    </div>
</x-frontend-layout>

@push('js')
    <script>
        // Attendre que TOUT soit chargé
        window.addEventListener('load', function() {
            console.log('=== PAYMENT DEBUG START ===');

            // Trouver le bouton
            const payButton = document.getElementById('payWithPaydunya');
            console.log('1. Button element:', payButton);

            if (!payButton) {
                console.error('ERREUR: Bouton non trouvé!');
                alert('ERREUR: Bouton de paiement non trouvé. Vérifiez la console (F12)');
                return;
            }

            // Trouver le CSRF token (plusieurs méthodes)
            let csrfToken = null;

            // Méthode 1: meta tag
            const metaTag = document.querySelector('meta[name="csrf-token"]');
            if (metaTag) {
                csrfToken = metaTag.getAttribute('content');
                console.log('2. CSRF from meta tag:', csrfToken);
            }

            // Méthode 2: input hidden
            if (!csrfToken) {
                const inputToken = document.getElementById('csrf-token-input');
                if (inputToken) {
                    csrfToken = inputToken.value;
                    console.log('2. CSRF from input:', csrfToken);
                }
            }

            // Méthode 3: variable Laravel
            if (!csrfToken) {
                csrfToken = '{{ csrf_token() }}';
                console.log('2. CSRF from blade:', csrfToken);
            }

            if (!csrfToken) {
                console.error('ERREUR: CSRF token non trouvé!');
                alert('ERREUR: Token de sécurité manquant. Rechargez la page.');
                return;
            }

            console.log('3. CSRF Token final:', csrfToken);
            console.log('4. Payment URL:', '{{ route("payment.form") }}');

            // Éléments UI
            const loadingModal = document.getElementById('loadingModal');
            const paymentMessage = document.getElementById('payment-message');

            console.log('5. Loading modal:', loadingModal ? 'trouvé' : 'non trouvé');
            console.log('6. Message div:', paymentMessage ? 'trouvé' : 'non trouvé');

            // Fonction pour afficher les messages
            function showMessage(message, type) {
                console.log('Affichage message:', message, type);
                if (!paymentMessage) {
                    alert(message);
                    return;
                }
                paymentMessage.style.display = 'block';
                paymentMessage.className = 'mt-4 p-4 rounded-lg ' +
                    (type === 'error' ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600');
                paymentMessage.innerHTML = '<strong>' + message + '</strong>';
            }

            // Fonction pour afficher le chargement
            function showLoading() {
                console.log('Affichage loading...');
                if (loadingModal) {
                    loadingModal.style.display = 'flex';
                }
                payButton.disabled = true;
                payButton.innerHTML = 'Traitement en cours...';
            }

            // Fonction pour masquer le chargement
            function hideLoading() {
                console.log('Masquage loading...');
                if (loadingModal) {
                    loadingModal.style.display = 'none';
                }
                payButton.disabled = false;
                payButton.innerHTML = '<i class="ri-secure-payment-fill"></i> Payer {{ $currencySymbol }}{{ number_format($totalPrice, 2) }}';
            }

            // Gestionnaire de clic
            payButton.onclick = function(e) {
                e.preventDefault();
                e.stopPropagation();

                console.log('=== BOUTON CLIQUÉ ===');

                if (paymentMessage) {
                    paymentMessage.style.display = 'none';
                }

                showLoading();

                // Données de la requête
                const requestData = {
                    payment_method: 'paydunya'
                };

                console.log('7. Request data:', requestData);
                console.log('8. Envoi de la requête...');

                // Utiliser XMLHttpRequest (plus compatible)
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route("payment.form") }}', true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                xhr.onload = function() {
                    console.log('9. Response status:', xhr.status);
                    console.log('10. Response text:', xhr.responseText);

                    if (xhr.status >= 200 && xhr.status < 300) {
                        try {
                            const data = JSON.parse(xhr.responseText);
                            console.log('11. Response data:', data);

                            if (data.status === 'success') {
                                // Chercher l'URL de paiement
                                let paymentUrl = null;

                                if (data.data && data.data.checkout_url) {
                                    paymentUrl = data.data.checkout_url;
                                    console.log('12. URL trouvée (directe):', paymentUrl);
                                } else if (data.data && data.data.button) {
                                    // Extraire de l'HTML
                                    const temp = document.createElement('div');
                                    temp.innerHTML = data.data.button;
                                    const link = temp.querySelector('a');
                                    if (link) {
                                        paymentUrl = link.href;
                                        console.log('12. URL trouvée (HTML):', paymentUrl);
                                    }
                                }

                                if (paymentUrl) {
                                    console.log('13. Redirection vers:', paymentUrl);
                                    showMessage('Redirection vers le paiement...', 'success');
                                    setTimeout(function() {
                                        window.location.href = paymentUrl;
                                    }, 500);
                                } else {
                                    console.error('ERREUR: URL de paiement non trouvée');
                                    hideLoading();
                                    showMessage('Erreur: URL de paiement non trouvée', 'error');
                                }
                            } else {
                                console.error('ERREUR: Status non success:', data);
                                hideLoading();
                                showMessage(data.message || 'Erreur lors de l\'initialisation du paiement', 'error');
                            }
                        } catch (e) {
                            console.error('ERREUR: Parse JSON:', e);
                            hideLoading();
                            showMessage('Erreur: Réponse invalide du serveur', 'error');
                        }
                    } else {
                        console.error('ERREUR: HTTP', xhr.status);
                        hideLoading();
                        showMessage('Erreur serveur (HTTP ' + xhr.status + ')', 'error');
                    }
                };

                xhr.onerror = function() {
                    console.error('ERREUR: Connexion');
                    hideLoading();
                    showMessage('Erreur de connexion. Vérifiez votre connexion internet.', 'error');
                };

                xhr.send(JSON.stringify(requestData));
            };

            console.log('=== PAYMENT READY ===');
            console.log('Cliquez sur le bouton pour tester');
        });
    </script>
@endpush

@push('css')
    <style>
        .scrollbar-thin::-webkit-scrollbar {
            width: 6px;
        }
        .scrollbar-thin::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 10px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.5);
        }
    </style>
@endpush

{{--
@php
    $backendSetting = get_theme_option(key: 'backend_general') ?? null;
    $currency = $backendSetting['currency'] ?? 'USD-$';
    $currencySymbol = get_currency_symbol($currency);
    $cartType = session()->has('type') ? session()->get('type') : '';

    // Récupérer la méthode de paiement Paydunya
    $paymentMethod = get_payment_method()->first();
@endphp

<script>
    (function() {
        function initPayment() {
            var btn = document.getElementById('payWithPaydunya');
            if (!btn) {
                setTimeout(initPayment, 500);
                return;
            }

            var loadingModal = document.getElementById('loadingModal');
            var csrf = document.querySelector('meta[name="csrf-token"]').content;

            // Récupérer le texte original du bouton pour le reset
            var originalBtnContent = btn.innerHTML;

            btn.onclick = function(e) {
                e.preventDefault();

                if (loadingModal) loadingModal.style.display = 'flex';
                btn.disabled = true;
                btn.textContent = 'Traitement en cours...';

                var xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route("payment.form") }}', true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.setRequestHeader('X-CSRF-TOKEN', csrf);
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                xhr.onload = function() {
                    if (xhr.status >= 200 && xhr.status < 300) {
                        try {
                            var data = JSON.parse(xhr.responseText);

                            if (data.status === 'success') {
                                var url = data.data?.checkout_url;

                                if (!url && data.data?.button) {
                                    var temp = document.createElement('div');
                                    temp.innerHTML = data.data.button;
                                    var link = temp.querySelector('a');
                                    url = link?.href;
                                }

                                if (url) {
                                    window.location.href = url;
                                } else {
                                    alert('Erreur: URL de paiement introuvable');
                                    resetButton();
                                }
                            } else {
                                alert('Erreur: ' + (data.message || 'Erreur inconnue'));
                                resetButton();
                            }
                        } catch (e) {
                            alert('Erreur: ' + e.message);
                            resetButton();
                        }
                    } else {
                        alert('Erreur HTTP: ' + xhr.status);
                        resetButton();
                    }
                };

                xhr.onerror = function() {
                    alert('Erreur de connexion');
                    resetButton();
                };

                xhr.send(JSON.stringify({ payment_method: 'paydunya' }));
            };

            function resetButton() {
                if (loadingModal) loadingModal.style.display = 'none';
                btn.disabled = false;
                btn.innerHTML = originalBtnContent;
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initPayment);
        } else {
            setTimeout(initPayment, 100);
        }
    })();
</script>
<x-frontend-layout>
    <x-theme::breadcrumbs.breadcrumb-one pageTitle="Checkout" pageRoute="{{ route('checkout.page') }}"
                                         pageName="Checkout" />

    <!-- CSRF Token - Multiple façons pour s'assurer qu'il existe -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <input type="hidden" name="_token" value="{{ csrf_token() }}" id="csrf-token-input">

    <div class="container py-10">
        <!-- START PAYMENT SECTION -->
        <div class="col-span-full lg:col-span-8">
            <div class="bg-white dark:bg-dark-card rounded-2xl shadow-lg p-8">
                <h2 class="text-3xl font-bold text-heading dark:text-white mb-6 flex items-center gap-3">
                    <i class="ri-shopping-cart-line text-primary"></i>
                    {{ translate('Finalize Your Order') }}
                </h2>

                <!-- Informations de commande -->
                <div class="mb-8 p-6 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl border border-blue-200 dark:border-blue-800">
                    <h3 class="text-3xl font-bold text-heading dark:text-white mb-6 flex items-center gap-3">
                        {{ translate('Order Summary') }}
                    </h3>
                    <div class="space-y-3">
                        @if ($cartType !== 'subscription')
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-heading/70 dark:text-white/70">{{ translate('Number of courses') }}</span>
                                <span class="font-semibold text-heading dark:text-white">{{ total_qty() }}</span>
                            </div>
                        @else
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-heading/70 dark:text-white/70">{{ translate('Subscription Plan') }}</span>
                                <span class="font-semibold text-heading dark:text-white">{{ translate('Premium') }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between items-center text-sm border-t border-blue-200 dark:border-blue-700 pt-3">
                            <span class="text-heading/70 dark:text-white/70">{{ translate('Subtotal') }}</span>
                            <span class="font-semibold text-heading dark:text-white">{{ $currencySymbol }}{{ number_format($data['totalPrice'], 2) }}</span>
                        </div>
                        @if ($data['discountAmount'])
                            <div class="flex justify-between items-center text-sm text-green-600">
                                <span>{{ translate('Discount') }}</span>
                                <span class="font-semibold">-{{ $currencySymbol }}{{ number_format($data['discountAmount'], 2) }}</span>
                            </div>
                        @endif
                        <div class="flex justify-between items-center text-lg border-t-2 border-blue-300 dark:border-blue-600 pt-3">
                            <span class="font-bold text-heading dark:text-white">{{ translate('Total to Pay') }}</span>
                            @php
                                $totalPrice = $data['discountAmount']
                                    ? $data['totalPrice'] - $data['discountAmount']
                                    : $data['totalPrice'];
                            @endphp
                            <span class="font-bold text-2xl text-primary">{{ $currencySymbol }}{{ number_format($totalPrice, 2) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Formulaire de paiement -->
                <div class="space-y-6">
                    <div class="border-t border-border pt-6">
                        <h3 class="text-xl font-semibold text-heading dark:text-white mb-4 flex items-center gap-2">
                            <i class="ri-secure-payment-line text-primary"></i>
                            {{ translate('Payment Method') }}
                        </h3>

                        <!-- Logo Paydunya -->
                         @if($paymentMethod)
                            @php
                                $logo = $paymentMethod->logo && fileExists('lms/payments', $paymentMethod->logo) == true
                                    ? asset('storage/lms/payments/' . $paymentMethod->logo)
                                    : asset('lms/assets/images/logo/paydunya_logo.png');
                            @endphp
                            <div class="mb-6 p-4 bg-gray-50 dark:bg-dark-card-two rounded-lg flex items-center justify-center">
                                <img src="{{ $logo }}" alt="Paydunya" class="h-12">
                            </div>
                        @endif


                        <!-- Bouton de paiement -->
                        <div id="payment-button-container">
                            <button type="button"
                                    id="payWithPaydunya"
                                    class="w-full btn btn-primary-solid h-16 rounded-xl text-lg font-semibold hover:bg-primary-dark transition-all duration-300 flex items-center justify-center gap-3 group">
                                <i class="ri-secure-payment-fill text-2xl group-hover:scale-110 transition-transform"></i>
                                <span class="text-white">{{ translate('Pay Securely') }} - {{ $currencySymbol }}{{ number_format($totalPrice, 2) }}</span>
                                <i class="ri-arrow-right-line text-xl group-hover:translate-x-2 transition-transform"></i>
                            </button>
                        </div>

                        <!-- Messages d'erreur/chargement -->
                        <div id="payment-message" class="mt-4 hidden"></div>
                    </div>

                    <!-- Méthodes de paiement acceptées -->
                    <div class="mt-8 p-6 bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl border border-green-200 dark:border-green-800">
                        <h4 class="text-lg font-semibold text-heading dark:text-white mb-4 flex items-center gap-2">
                            <i class="ri-checkbox-circle-line text-green-600"></i>
                            {{ translate('Accepted Payment Methods') }}
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Mobile Money -->
                            <div class="flex items-start gap-3 p-3 bg-white dark:bg-dark-card rounded-lg">
                                <i class="ri-smartphone-line text-2xl text-primary mt-1"></i>
                                <div>
                                    <h5 class="font-semibold text-heading dark:text-white mb-1">Mobile Money</h5>
                                    <p class="text-sm text-heading/70 dark:text-white/70">MTN, Moov, Orange Money</p>
                                </div>
                            </div>
                            <!-- Cartes bancaires -->
                            <div class="flex items-start gap-3 p-3 bg-white dark:bg-dark-card rounded-lg">
                                <i class="ri-bank-card-line text-2xl text-primary mt-1"></i>
                                <div>
                                    <h5 class="font-semibold text-heading dark:text-white mb-1">{{ translate('Bank Cards') }}</h5>
                                    <p class="text-sm text-heading/70 dark:text-white/70">Visa, Mastercard</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Informations de sécurité -->
                    <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                        <div class="flex items-start gap-3">
                            <i class="ri-shield-check-fill text-2xl text-blue-600 dark:text-blue-400 mt-1"></i>
                            <div>
                                <h5 class="font-semibold text-heading dark:text-white mb-2">
                                    {{ translate('Secure Payment') }}
                                </h5>
                                <ul class="space-y-1 text-sm text-heading/70 dark:text-white/70">
                                    <li class="flex items-center gap-2">
                                        <i class="ri-check-line text-green-600"></i>
                                        {{ translate('256-bit SSL Encryption') }}
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <i class="ri-check-line text-green-600"></i>
                                        {{ translate('PCI DSS Certified') }}
                                    </li>
                                    <li class="flex items-center gap-2">
                                        <i class="ri-check-line text-green-600"></i>
                                        {{ translate('Your data is protected') }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PAYMENT SECTION -->

        <!-- START ORDER DETAILS SIDEBAR -->
        <div class="col-span-full lg:col-span-4">
            <div class="bg-white dark:bg-dark-card rounded-2xl shadow-lg p-8">
                <h2 class="text-3xl font-bold text-heading dark:text-white mb-6 flex items-center gap-3">
                    <i class="ri-file-list-3-line text-primary"></i>
                    {{ translate('Order Details') }}
                </h2>

                @if ($cartType !== 'subscription' && isset($data['cartCourses']))
                    <!-- Liste des cours -->
                    <div class="space-y-4 mb-6 max-h-96 overflow-y-auto scrollbar-thin">
                        @foreach($data['cartCourses'] as $course)
                            <div class="flex gap-3 p-3 bg-gray-50 dark:bg-dark-card-two rounded-lg">
                                @if(isset($course['thumbnail']))
                                    <img src="{{ $course['thumbnail'] }}" alt="{{ $course['title'] ?? 'Course' }}" class="w-16 h-16 object-cover rounded">
                                @endif
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-sm text-heading dark:text-white truncate">
                                        {{ $course['title'] ?? $course['name'] ?? 'Course' }}
                                    </h4>
                                    <p class="text-xs text-heading/70 dark:text-white/70 mt-1">
                                        {{ $currencySymbol }}{{ number_format($course['price'] ?? $course['amount'] ?? 0, 2) }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Récapitulatif des prix -->
                <div class="border-t border-border pt-4 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-heading/70 dark:text-white/70">{{ translate('Subtotal') }}</span>
                        <span class="font-semibold text-heading dark:text-white">{{ $currencySymbol }}{{ number_format($data['totalPrice'], 2) }}</span>
                    </div>
                    @if ($data['discountAmount'])
                        <div class="flex justify-between text-sm text-green-600">
                            <span>{{ translate('Discount') }}</span>
                            <span class="font-semibold">-{{ $currencySymbol }}{{ number_format($data['discountAmount'], 2) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between text-lg font-bold border-t border-border pt-3">
                        <span class="text-heading dark:text-white">{{ translate('Total') }}</span>
                        <span class="text-primary text-2xl">{{ $currencySymbol }}{{ number_format($totalPrice, 2) }}</span>
                    </div>
                </div>

                <!-- Garanties -->
                <div class="mt-6 pt-6 border-t border-border">
                    <h4 class="font-semibold text-heading dark:text-white mb-3 text-sm">{{ translate('Our Guarantees') }}</h4>
                    <ul class="space-y-2 text-xs text-heading/70 dark:text-white/70">
                        <li class="flex items-center gap-2">
                            <i class="ri-time-line text-primary"></i>
                            {{ translate('Instant Access') }}
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="ri-refresh-line text-primary"></i>
                            {{ translate('30-Day Money Back Guarantee') }}
                        </li>
                        <li class="flex items-center gap-2">
                            <i class="ri-customer-service-2-line text-primary"></i>
                            {{ translate('24/7 Support') }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- END ORDER DETAILS SIDEBAR -->
    </div>


    <!-- Modal de chargement -->
    <div id="loadingModal" class="fixed inset-0 z-[9999] hidden bg-black/50 backdrop-blur-sm">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white dark:bg-dark-card rounded-2xl p-8 max-w-sm w-full text-center">
                <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-primary mx-auto mb-4"></div>
                <h3 class="text-xl font-semibold text-heading dark:text-white mb-2">
                    {{ translate('Processing Payment...') }}
                </h3>
                <p class="text-sm text-heading/70 dark:text-white/70">
                    {{ translate('Please wait, you will be redirected to Paydunya') }}
                </p>
            </div>
        </div>
    </div>
</x-frontend-layout>

@push('js')
    <script>
        // Attendre que TOUT soit chargé
        window.addEventListener('load', function() {
            console.log('=== PAYMENT DEBUG START ===');

            // Trouver le bouton
            const payButton = document.getElementById('payWithPaydunya');
            console.log('1. Button element:', payButton);

            if (!payButton) {
                console.error('ERREUR: Bouton non trouvé!');
                alert('ERREUR: Bouton de paiement non trouvé. Vérifiez la console (F12)');
                return;
            }

            // Trouver le CSRF token (plusieurs méthodes)
            let csrfToken = null;

            // Méthode 1: meta tag
            const metaTag = document.querySelector('meta[name="csrf-token"]');
            if (metaTag) {
                csrfToken = metaTag.getAttribute('content');
                console.log('2. CSRF from meta tag:', csrfToken);
            }

            // Méthode 2: input hidden
            if (!csrfToken) {
                const inputToken = document.getElementById('csrf-token-input');
                if (inputToken) {
                    csrfToken = inputToken.value;
                    console.log('2. CSRF from input:', csrfToken);
                }
            }

            // Méthode 3: variable Laravel
            if (!csrfToken) {
                csrfToken = '{{ csrf_token() }}';
                console.log('2. CSRF from blade:', csrfToken);
            }

            if (!csrfToken) {
                console.error('ERREUR: CSRF token non trouvé!');
                alert('ERREUR: Token de sécurité manquant. Rechargez la page.');
                return;
            }

            console.log('3. CSRF Token final:', csrfToken);
            console.log('4. Payment URL:', '{{ route("payment.form") }}');

            // Éléments UI
            const loadingModal = document.getElementById('loadingModal');
            const paymentMessage = document.getElementById('payment-message');

            console.log('5. Loading modal:', loadingModal ? 'trouvé' : 'non trouvé');
            console.log('6. Message div:', paymentMessage ? 'trouvé' : 'non trouvé');

            // Fonction pour afficher les messages
            function showMessage(message, type) {
                console.log('Affichage message:', message, type);
                if (!paymentMessage) {
                    alert(message);
                    return;
                }
                paymentMessage.style.display = 'block';
                paymentMessage.className = 'mt-4 p-4 rounded-lg ' +
                    (type === 'error' ? 'bg-red-50 text-red-600' : 'bg-green-50 text-green-600');
                paymentMessage.innerHTML = '<strong>' + message + '</strong>';
            }

            // Fonction pour afficher le chargement
            function showLoading() {
                console.log('Affichage loading...');
                if (loadingModal) {
                    loadingModal.style.display = 'flex';
                }
                payButton.disabled = true;
                payButton.innerHTML = 'Traitement en cours...';
            }

            // Fonction pour masquer le chargement
            function hideLoading() {
                console.log('Masquage loading...');
                if (loadingModal) {
                    loadingModal.style.display = 'none';
                }
                payButton.disabled = false;
                payButton.innerHTML = '<i class="ri-secure-payment-fill"></i> Payer {{ $currencySymbol }}{{ number_format($totalPrice, 2) }}';
            }

            // Gestionnaire de clic
            payButton.onclick = function(e) {
                e.preventDefault();
                e.stopPropagation();

                console.log('=== BOUTON CLIQUÉ ===');

                if (paymentMessage) {
                    paymentMessage.style.display = 'none';
                }

                showLoading();

                // Données de la requête
                const requestData = {
                    payment_method: 'paydunya'
                };

                console.log('7. Request data:', requestData);
                console.log('8. Envoi de la requête...');

                // Utiliser XMLHttpRequest (plus compatible)
                const xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route("payment.form") }}', true);
                xhr.setRequestHeader('Content-Type', 'application/json');
                xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);
                xhr.setRequestHeader('Accept', 'application/json');
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

                xhr.onload = function() {
                    console.log('9. Response status:', xhr.status);
                    console.log('10. Response text:', xhr.responseText);

                    if (xhr.status >= 200 && xhr.status < 300) {
                        try {
                            const data = JSON.parse(xhr.responseText);
                            console.log('11. Response data:', data);

                            if (data.status === 'success') {
                                // Chercher l'URL de paiement
                                let paymentUrl = null;

                                if (data.data && data.data.checkout_url) {
                                    paymentUrl = data.data.checkout_url;
                                    console.log('12. URL trouvée (directe):', paymentUrl);
                                } else if (data.data && data.data.button) {
                                    // Extraire de l'HTML
                                    const temp = document.createElement('div');
                                    temp.innerHTML = data.data.button;
                                    const link = temp.querySelector('a');
                                    if (link) {
                                        paymentUrl = link.href;
                                        console.log('12. URL trouvée (HTML):', paymentUrl);
                                    }
                                }

                                if (paymentUrl) {
                                    console.log('13. Redirection vers:', paymentUrl);
                                    showMessage('Redirection vers le paiement...', 'success');
                                    setTimeout(function() {
                                        window.location.href = paymentUrl;
                                    }, 500);
                                } else {
                                    console.error('ERREUR: URL de paiement non trouvée');
                                    hideLoading();
                                    showMessage('Erreur: URL de paiement non trouvée', 'error');
                                }
                            } else {
                                console.error('ERREUR: Status non success:', data);
                                hideLoading();
                                showMessage(data.message || 'Erreur lors de l\'initialisation du paiement', 'error');
                            }
                        } catch (e) {
                            console.error('ERREUR: Parse JSON:', e);
                            hideLoading();
                            showMessage('Erreur: Réponse invalide du serveur', 'error');
                        }
                    } else {
                        console.error('ERREUR: HTTP', xhr.status);
                        hideLoading();
                        showMessage('Erreur serveur (HTTP ' + xhr.status + ')', 'error');
                    }
                };

                xhr.onerror = function() {
                    console.error('ERREUR: Connexion');
                    hideLoading();
                    showMessage('Erreur de connexion. Vérifiez votre connexion internet.', 'error');
                };

                xhr.send(JSON.stringify(requestData));
            };

            console.log('=== PAYMENT READY ===');
            console.log('Cliquez sur le bouton pour tester');
        });
    </script>
@endpush

@push('css')
    <style>
        .scrollbar-thin::-webkit-scrollbar {
            width: 6px;
        }
        .scrollbar-thin::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 10px;
        }
        .scrollbar-thin::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 0, 0, 0.5);
        }
    </style>
@endpush
--}}

