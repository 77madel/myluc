<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Paydunya Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration pour l'intégration de Paydunya comme gateway de paiement
    |
    */

    // Clés API Paydunya
    'master_key' => env('PAYDUNYA_MASTER_KEY', ''),
    'private_key' => env('PAYDUNYA_PRIVATE_KEY', ''),
    'token' => env('PAYDUNYA_TOKEN', ''),

    // Mode test ou production
    'test_mode' => env('PAYDUNYA_TEST_MODE', true),

    // URLs de l'API
    'api_url' => [
        'production' => 'https://app.paydunya.com/api/v1',
        'sandbox' => 'https://app.paydunya.com/sandbox-api/v1',
    ],

    // Devise par défaut
    'currency' => env('PAYDUNYA_CURRENCY', 'XOF'), // Franc CFA

    // Informations du marchand
    'store' => [
        'name' => env('APP_NAME', 'My Store'),
        'tagline' => env('PAYDUNYA_TAGLINE', 'Best online store'),
        'phone' => env('PAYDUNYA_PHONE', ''),
        'postal_address' => env('PAYDUNYA_ADDRESS', ''),
        'logo_url' => env('APP_URL') . '/logo.png',
        'website_url' => env('APP_URL'),
    ],

    // Méthodes de paiement acceptées
    'payment_methods' => [
        'mobile_money' => true,  // MTN, Moov, Orange Money
        'card' => true,          // Visa, Mastercard
    ],

    // Montants
    'min_amount' => 100,    // Montant minimum en XOF
    'max_amount' => 5000000, // Montant maximum en XOF

    // Timeout de la session de paiement (en minutes)
    'session_timeout' => 30,

    // URLs de callback
    'callback_url' => env('APP_URL') . '/payment/callback/paydunya',
    'return_url' => env('APP_URL') . '/payment/success/paydunya',
    'cancel_url' => env('APP_URL') . '/payment/cancel',
];
