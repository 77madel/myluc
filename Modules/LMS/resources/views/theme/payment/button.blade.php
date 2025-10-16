{{--
@if ($paymentMethod == 'paypal')
    @include('theme::payment.paypal.form')
@elseif($paymentMethod == 'razorpay')
    @include('theme::payment.razorpay.form')
@elseif($paymentMethod == 'xendit')
    @include('theme::payment.xendix.form')
@elseif($paymentMethod == 'paystack')
    @include('theme::payment.paystack.form')
@elseif($paymentMethod == 'stripe')
    @include('theme::payment.stripe.form')
@elseif($paymentMethod == 'offline')
    @include('theme::payment.offline.form')
@endif
--}}

@if($paymentMethod === 'paydunya')
    @if(isset($result['status']) && $result['status'] === 'success')
        <a href="{{ $result['checkout_url'] }}"
           class="btn btn-primary-solid w-full h-14 rounded-full text-white font-semibold hover:bg-primary-dark transition-colors">
            <i class="ri-secure-payment-line me-2"></i>
            {{ translate('Pay with Paydunya') }}
        </a>

        <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
            <p class="text-sm text-blue-600 dark:text-blue-400 flex items-center">
                <i class="ri-information-line me-2"></i>
                {{ translate('You will be redirected to Paydunya secure payment page') }}
            </p>
        </div>
    @else
        <div class="p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
            <p class="text-sm text-red-600 dark:text-red-400 flex items-center">
                <i class="ri-error-warning-line me-2"></i>
                {{ $result['message'] ?? translate('Unable to initialize payment. Please try again.') }}
            </p>
        </div>

        <button onclick="window.location.reload()"
                class="btn btn-outline-primary w-full h-14 rounded-full mt-4">
            <i class="ri-refresh-line me-2"></i>
            {{ translate('Retry') }}
        </button>
    @endif
@else
    <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
        <p class="text-sm text-yellow-600 dark:text-yellow-400">
            {{ translate('Please select a payment method') }}
        </p>
    </div>
@endif

@push('js')
    <script>
        // Gérer les redirections Paydunya
        document.addEventListener('DOMContentLoaded', function() {
            const paydunyaButton = document.querySelector('a[href*="paydunya"]');

            if (paydunyaButton) {
                paydunyaButton.addEventListener('click', function(e) {
                    // Afficher un indicateur de chargement
                    const loadingHtml = `
                    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
                        <div class="bg-white dark:bg-dark-card p-6 rounded-lg">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-primary mx-auto mb-4"></div>
                            <p class="text-heading dark:text-white">{{ translate('Redirecting to payment...') }}</p>
                        </div>
                    </div>
                `;

                    document.body.insertAdjacentHTML('beforeend', loadingHtml);
                });
            }
        });
    </script>
@endpush
