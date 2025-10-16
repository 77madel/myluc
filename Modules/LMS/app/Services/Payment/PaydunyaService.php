<?php

/*
namespace Modules\LMS\Services\Payment;

use Illuminate\Support\Facades\Http;
use Modules\LMS\Classes\Cart;

class PaydunyaService
{
    protected static $masterKey;
    protected static $privateKey;
    protected static $token;
    protected static $baseUrl;
    protected static $testMode;


    protected static function init()
    {
        $paymentMethod = get_payment_method()->firstWhere('method_name', 'Paydunya');

        if (!$paymentMethod) {
            throw new \Exception('Paydunya payment method not configured');
        }

        $keys = json_decode($paymentMethod->keys, true);

        self::$masterKey = $keys['master_key'] ?? '';
        self::$privateKey = $keys['private_key'] ?? '';
        self::$token = $keys['token'] ?? '';
        self::$testMode = $paymentMethod->enabled_test_mode == 1;

        // URL de base selon le mode
        self::$baseUrl = self::$testMode
            ? 'https://app.paydunya.com/sandbox-api/v1'
            : 'https://app.paydunya.com/api/v1';
    }


    protected static function getHeaders()
    {
        return [
            'PAYDUNYA-MASTER-KEY' => self::$masterKey,
            'PAYDUNYA-PRIVATE-KEY' => self::$privateKey,
            'PAYDUNYA-TOKEN' => self::$token,
            'Content-Type' => 'application/json',
        ];
    }


    public static function makePayment()
    {
        try {
            self::init();

            $user = auth()->user();
            $cartType = session()->get('type', '');

            // Préparer les données de facturation
            if ($cartType === 'subscription') {
                $totalAmount = session()->get('subscription_price', 0);
                $description = 'Subscription Payment';
            } else {
                $totalAmount = Cart::totalPrice() - Cart::discountAmount();
                $description = 'Course Purchase';
            }

            // Préparer les items
            $items = [];
            if ($cartType !== 'subscription') {
                foreach (Cart::get() as $item) {
                    $items[] = [
                        'name' => $item['title'],
                        'quantity' => 1,
                        'unit_price' => $item['price'],
                        'total_price' => $item['price'],
                        'description' => $item['description'] ?? ''
                    ];
                }
            } else {
                $items[] = [
                    'name' => 'Subscription Plan',
                    'quantity' => 1,
                    'unit_price' => $totalAmount,
                    'total_price' => $totalAmount,
                    'description' => $description
                ];
            }

            // Données de la facture
            $invoiceData = [
                'invoice' => [
                    'total_amount' => $totalAmount,
                    'description' => $description,
                    'items' => $items
                ],
                'store' => [
                    'name' => config('app.name'),
                    'tagline' => 'Payment for ' . $description,
                    'postal_address' => config('app.address', ''),
                    'phone_number' => config('app.phone', ''),
                    'website_url' => url('/'),
                    'logo_url' => asset('logo.png')
                ],
                'actions' => [
                    'cancel_url' => route('payment.cancel'),
                    'return_url' => route('payment.success', ['method' => 'paydunya']),
                    'callback_url' => route('payment.callback', ['method' => 'paydunya'])
                ],
                'custom_data' => [
                    'user_id' => $user->id,
                    'cart_type' => $cartType,
                    'subscription_id' => session()->get('subscription_id', null)
                ]
            ];

            // Envoyer la requête à Paydunya
            $response = Http::withHeaders(self::getHeaders())
                ->post(self::$baseUrl . '/checkout-invoice/create', $invoiceData);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'status' => 'success',
                    'token' => $data['token'],
                    'response_code' => $data['response_code'],
                    'response_text' => $data['response_text'],
                    'checkout_url' => $data['response_text']
                ];
            }

            return [
                'status' => 'error',
                'message' => $response->json()['response_text'] ?? 'Payment initialization failed'
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }


    public static function verifyPayment($token)
    {
        try {
            self::init();

            $response = Http::withHeaders(self::getHeaders())
                ->get(self::$baseUrl . '/checkout-invoice/confirm/' . $token);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'status' => 'error',
                'message' => 'Payment verification failed'
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }


    public static function handleCallback($data)
    {
        try {
            self::init();

            $token = $data['token'] ?? null;

            if (!$token) {
                return [
                    'status' => 'error',
                    'message' => 'Invalid token'
                ];
            }

            // Vérifier le paiement
            $verification = self::verifyPayment($token);

            if ($verification['status'] === 'completed') {
                // Le paiement est confirmé
                $customData = $verification['custom_data'] ?? [];

                return [
                    'status' => 'success',
                    'data' => $verification,
                    'custom_data' => $customData
                ];
            }

            return [
                'status' => 'pending',
                'message' => 'Payment not completed yet'
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}*/

/*namespace Modules\LMS\Services\Payment;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\LMS\Classes\Cart;
use Modules\LMS\Enums\PurchaseStatus;
use Modules\LMS\Models\Purchase\Purchase;
use Paydunya\Checkout\CheckoutInvoice;

class PaydunyaService
{
    atic function success($method, $data)
    {
        try {
            DB::beginTransaction();

            $user = auth()->user();
            $cartType = session()->get('type', '');

            // Créer l'enregistrement de l'achat
            $purchase = new Purchase;
            $purchase->user_id = $user->id;
            $purchase->type = $cartType === 'subscription' ? 'subscription' : 'purchase';
            $purchase->payment_method = $method;
            $purchase->transaction_id = $data['transaction_id'] ?? Str::uuid();
            $purchase->status = 'success';

            if ($cartType === 'subscription') {
                // Traiter l'abonnement
                $subscriptionId = session()->get('subscription_id');
                $amount = session()->get('subscription_price', 0);

                $purchase->total_amount = $amount;
                $purchase->save();

                // Créer l'enregistrement d'abonnement utilisateur
                $subscribeUser = new SubscribeUser;
                $subscribeUser->user_id = $user->id;
                $subscribeUser->subscribe_id = $subscriptionId;
                $subscribeUser->purchase_id = $purchase->id;
                $subscribeUser->status = 1;
                $subscribeUser->start_date = now();
                $subscribeUser->end_date = now()->addDays(30); // Ajustez selon le plan
                $subscribeUser->save();

                // Nettoyer les sessions
                session()->forget(['type', 'subscription_price', 'subscription_id']);

            } else {
                // Traiter l'achat de cours
                $cartItems = Cart::get();
                $totalAmount = Cart::totalPrice() - Cart::discountAmount();

                $purchase->total_amount = $totalAmount;
                $purchase->save();

                // Créer les détails d'achat pour chaque cours
                foreach ($cartItems as $item) {
                    $purchaseDetail = new PurchaseDetail;
                    $purchaseDetail->purchase_id = $purchase->id;
                    $purchaseDetail->course_id = $item['id'];
                    $purchaseDetail->amount = $item['price'];
                    $purchaseDetail->status = PurchaseStatus::PROCESSING;
                    $purchaseDetail->save();
                }

                // Vider le panier
                Cart::empty();
            }

            DB::commit();

            return [
                'status' => 'success',
                'purchase_id' => $purchase->id,
                'message' => 'Payment processed successfully',
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Payment processing error: '.$e->getMessage());

            return [
                'status' => 'error',
                'message' => 'Failed to process payment: '.$e->getMessage(),
            ];
        }
    }


    public static function failed($method, $data)
    {
        try {
            $user = auth()->user();

            // Enregistrer l'échec du paiement
            $purchase = new Purchase;
            $purchase->user_id = $user->id;
            $purchase->type = session()->get('type', 'purchase');
            $purchase->payment_method = $method;
            $purchase->transaction_id = $data['transaction_id'] ?? null;
            $purchase->status = 'failed';
            $purchase->total_amount = $data['amount'] ?? 0;
            $purchase->save();

            return [
                'status' => 'failed',
                'message' => 'Payment failed',
            ];

        } catch (\Exception $e) {
            \Log::error('Payment failure recording error: '.$e->getMessage());

            return [
                'status' => 'error',
                'message' => 'Failed to record payment failure',
            ];
        }
    }


    public static function getPaymentMethod($methodName)
    {
        return \Modules\LMS\Models\PaymentMethod::where('method_name', $methodName)
            ->where('status', 1)
            ->first();
    }


    public static function verifyTransaction($transactionId)
    {
        return Purchase::where('transaction_id', $transactionId)->first();
    }

    public function initiatePayment($data)
    {
        $invoice = new CheckoutInvoice();
        $invoice->addItem('Paiement Formation', 1, $data['amount'], $data['amount']);
        $invoice->setDescription('Paiement sur le LMS');
        $invoice->setTotalAmount($data['amount']);
        $invoice->setCallbackUrl(route('transaction.success'));

        if ($invoice->create()) {
            return [
                'status' => 'success',
                'redirect_url' => $invoice->getInvoiceUrl(),
            ];
        }

        return ['status' => 'error', 'message' => $invoice->response_text];
    }
}*/

/*namespace Modules\LMS\Services\Payment;

use Illuminate\Support\Facades\Http;
use Modules\LMS\Classes\Cart;

class PaydunyaService
{
    protected static $masterKey;

    protected static $privateKey;

    protected static $token;

    protected static $baseUrl;

    protected static $testMode;


    protected static function init()
    {
        $paymentMethod = get_payment_method()->firstWhere('method_name', 'Paydunya');

        if (! $paymentMethod) {
            throw new \Exception('Paydunya payment method not configured');
        }

        $keys = json_decode($paymentMethod->keys, true);

        self::$masterKey = $keys['master_key'] ?? '';
        self::$privateKey = $keys['private_key'] ?? '';
        self::$token = $keys['token'] ?? '';
        self::$testMode = $paymentMethod->enabled_test_mode == 1;

        // URL de base selon le mode
        self::$baseUrl = self::$testMode
            ? 'https://app.paydunya.com/sandbox-api/v1'
            : 'https://app.paydunya.com/api/v1';
    }


    protected static function getHeaders()
    {
        return [
            'PAYDUNYA-MASTER-KEY' => self::$masterKey,
            'PAYDUNYA-PRIVATE-KEY' => self::$privateKey,
            'PAYDUNYA-TOKEN' => self::$token,
            'Content-Type' => 'application/json',
        ];
    }


    public static function makePayment()
    {
        try {
            self::init();

            $user = auth()->user();
            $cartType = session()->get('type', '');

            // Préparer les données de facturation
            if ($cartType === 'subscription') {
                $totalAmount = session()->get('subscription_price', 0);
                $description = 'Subscription Payment';
            } else {
                $totalAmount = Cart::totalPrice() - Cart::discountAmount();
                $description = 'Course Purchase';
            }

            // Préparer les items
            $items = [];
            if ($cartType !== 'subscription') {
                foreach (Cart::get() as $item) {
                    $items[] = [
                        'name' => $item['title'] ?? $item['name'] ?? 'Course',
                        'quantity' => 1,
                        'unit_price' => $item['price'] ?? $item['amount'] ?? 0,
                        'total_price' => $item['price'] ?? $item['amount'] ?? 0,
                        'description' => $item['description'] ?? $item['title'] ?? 'Course',
                    ];
                }
            } else {
                $items[] = [
                    'name' => 'Subscription Plan',
                    'quantity' => 1,
                    'unit_price' => $totalAmount,
                    'total_price' => $totalAmount,
                    'description' => $description,
                ];
            }

            // Données de la facture
            $invoiceData = [
                'invoice' => [
                    'total_amount' => $totalAmount,
                    'description' => $description,
                    'items' => $items,
                ],
                'store' => [
                    'name' => config('app.name'),
                    'tagline' => 'Payment for '.$description,
                    'postal_address' => config('app.address', ''),
                    'phone_number' => config('app.phone', ''),
                    'website_url' => url('/'),
                    'logo_url' => asset('logo.png'),
                ],
                'actions' => [
                    'cancel_url' => route('payment.cancel'),
                    'return_url' => route('payment.success', ['method' => 'paydunya']),
                    'callback_url' => route('payment.callback', ['method' => 'paydunya']),
                ],
                'custom_data' => [
                    'user_id' => $user->id,
                    'cart_type' => $cartType,
                    'subscription_id' => session()->get('subscription_id', null),
                ],
            ];

            // Envoyer la requête à Paydunya
            $response = Http::withHeaders(self::getHeaders())
                ->post(self::$baseUrl.'/checkout-invoice/create', $invoiceData);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'status' => 'success',
                    'token' => $data['token'],
                    'response_code' => $data['response_code'],
                    'response_text' => $data['response_text'],
                    'checkout_url' => $data['response_text'],
                ];
            }

            return [
                'status' => 'error',
                'message' => $response->json()['response_text'] ?? 'Payment initialization failed',
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }


    public static function verifyPayment($token)
    {
        try {
            self::init();

            $response = Http::withHeaders(self::getHeaders())
                ->get(self::$baseUrl.'/checkout-invoice/confirm/'.$token);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'status' => 'error',
                'message' => 'Payment verification failed',
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }


    public static function handleCallback($data): array
    {
        try {
            self::init();

            $token = $data['token'] ?? null;

            if (! $token) {
                return [
                    'status' => 'error',
                    'message' => 'Invalid token',
                ];
            }

            // Vérifier le paiement
            $verification = self::verifyPayment($token);

            if ($verification['status'] === 'completed') {
                // Le paiement est confirmé
                $customData = $verification['custom_data'] ?? [];

                return [
                    'status' => 'success',
                    'data' => $verification,
                    'custom_data' => $customData,
                ];
            }

            return [
                'status' => 'pending',
                'message' => 'Payment not completed yet',
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }
}*/

namespace Modules\LMS\Services\Payment;

use Illuminate\Support\Facades\Http;
use Modules\LMS\Classes\Cart;

class PaydunyaService
{
    protected static $masterKey;

    protected static $privateKey;

    protected static $token;

    protected static $baseUrl;

    protected static $testMode;

    /**
     * Initialize Paydunya configuration
     */
    protected static function init()
    {
        $paymentMethod = get_payment_method()->firstWhere('method_name', 'Paydunya');

        if (! $paymentMethod) {
            throw new \Exception('Paydunya payment method not configured');
        }

        // CORRECTION: Vérifier si keys est déjà un tableau
        $keys = is_array($paymentMethod->keys)
            ? $paymentMethod->keys
            : json_decode($paymentMethod->keys, true);

        self::$masterKey = $keys['master_key'] ?? '';
        self::$privateKey = $keys['private_key'] ?? '';
        self::$token = $keys['token'] ?? '';
        self::$testMode = $paymentMethod->enabled_test_mode == 1;

        // URL de base selon le mode
        self::$baseUrl = self::$testMode
            ? 'https://app.paydunya.com/sandbox-api/v1'
            : 'https://app.paydunya.com/api/v1';
    }

    /**
     * Get headers for API requests
     */
    protected static function getHeaders()
    {
        return [
            'PAYDUNYA-MASTER-KEY' => self::$masterKey,
            'PAYDUNYA-PRIVATE-KEY' => self::$privateKey,
            'PAYDUNYA-TOKEN' => self::$token,
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Create payment invoice
     */
    /*public static function makePayment()
    {
        try {
            self::init();

            $user = auth()->user();

            if (! $user) {
                return [
                    'status' => 'error',
                    'message' => 'User not authenticated',
                ];
            }

            // Vérifier si la session est disponible
            if (! request()->hasSession()) {
                return [
                    'status' => 'error',
                    'message' => 'Session not available',
                ];
            }

            $cartType = session()->get('type', '');

            // Préparer les données de facturation
            if ($cartType === 'subscription') {
                $totalAmount = session()->get('subscription_price', 0);
                $description = 'Subscription Payment';
            } else {
                $totalAmount = Cart::totalPrice() - Cart::discountAmount();
                $description = 'Course Purchase';

                if ($totalAmount <= 0) {
                    return [
                        'status' => 'error',
                        'message' => 'Invalid cart amount',
                    ];
                }
            }

            // Préparer les items
            $items = [];
            if ($cartType !== 'subscription') {
                foreach (Cart::get() as $item) {
                    $items[] = [
                        'name' => $item['title'] ?? $item['name'] ?? 'Course',
                        'quantity' => 1,
                        'unit_price' => $item['price'] ?? $item['amount'] ?? 0,
                        'total_price' => $item['price'] ?? $item['amount'] ?? 0,
                        'description' => $item['description'] ?? $item['title'] ?? 'Course',
                    ];
                }
            } else {
                $items[] = [
                    'name' => 'Subscription Plan',
                    'quantity' => 1,
                    'unit_price' => $totalAmount,
                    'total_price' => $totalAmount,
                    'description' => $description,
                ];
            }

            // Données de la facture
            $invoiceData = [
                'invoice' => [
                    'total_amount' => $totalAmount,
                    'description' => $description,
                    'items' => $items,
                ],
                'store' => [
                    'name' => config('app.name'),
                    'tagline' => 'Payment for '.$description,
                    'postal_address' => config('app.address', ''),
                    'phone_number' => config('app.phone', ''),
                    'website_url' => url('/'),
                    'logo_url' => asset('logo.png'),
                ],
                'actions' => [
                    'cancel_url' => route('payment.cancel'),
                    'return_url' => route('payment.success', ['method' => 'paydunya']),
                    'callback_url' => route('payment.callback', ['method' => 'paydunya']),
                ],
                'custom_data' => [
                    'user_id' => $user->id,
                    'cart_type' => $cartType,
                    'subscription_id' => session()->get('subscription_id', null),
                ],
            ];

            // Envoyer la requête à Paydunya
            $response = Http::withHeaders(self::getHeaders())
                ->post(self::$baseUrl.'/checkout-invoice/create', $invoiceData);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'status' => 'success',
                    'token' => $data['token'],
                    'response_code' => $data['response_code'],
                    'response_text' => $data['response_text'],
                    'checkout_url' => $data['response_text'],
                ];
            }

            return [
                'status' => 'error',
                'message' => $response->json()['response_text'] ?? 'Payment initialization failed',
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }*/

    public static function makePayment()
    {
        \Log::info('=== PAYDUNYA START ===');

        try {
            self::init();

            \Log::info('Init done. Keys:', [
                'master' => substr(self::$masterKey, 0, 10) . '...',
                'private' => substr(self::$privateKey, 0, 10) . '...',
                'token' => substr(self::$token, 0, 10) . '...',
                'baseUrl' => self::$baseUrl
            ]);

            $user = auth()->user();

            if (!$user) {
                \Log::error('User not authenticated');
                return ['status' => 'error', 'message' => 'User not authenticated'];
            }

            if (!request()->hasSession()) {
                \Log::error('Session not available');
                return ['status' => 'error', 'message' => 'Session not available'];
            }

            $cartType = session()->get('type', '');
            \Log::info('Cart type: ' . $cartType);

            if ($cartType === 'subscription') {
                $totalAmount = session()->get('subscription_price', 0);
                $description = 'Subscription Payment';
            } else {
                $totalAmount = Cart::totalPrice() - Cart::discountAmount();
                $description = 'Course Purchase';

                if ($totalAmount <= 0) {
                    \Log::error('Invalid amount: ' . $totalAmount);
                    return ['status' => 'error', 'message' => 'Invalid cart amount'];
                }
            }

            \Log::info('Amount: ' . $totalAmount);

            $items = [];
            if ($cartType !== 'subscription') {
                foreach (Cart::get() as $item) {
                    $items[] = [
                        'name' => $item['title'] ?? $item['name'] ?? 'Course',
                        'quantity' => 1,
                        'unit_price' => $item['price'] ?? $item['amount'] ?? 0,
                        'total_price' => $item['price'] ?? $item['amount'] ?? 0,
                        'description' => $item['description'] ?? $item['title'] ?? 'Course',
                    ];
                }
            } else {
                $items[] = [
                    'name' => 'Subscription Plan',
                    'quantity' => 1,
                    'unit_price' => $totalAmount,
                    'total_price' => $totalAmount,
                    'description' => $description,
                ];
            }

            $invoiceData = [
                'invoice' => [
                    'total_amount' => $totalAmount,
                    'description' => $description,
                ],
                'store' => [
                    'name' => config('app.name', 'My Store'),
                ],
                'actions' => [
                    'cancel_url' => route('payment.cancel'),
                    'return_url' => route('payment.success', ['method' => 'paydunya']),
                ],
            ];

            \Log::info('Invoice data:', $invoiceData);
            \Log::info('Sending to: ' . self::$baseUrl . '/checkout-invoice/create');

            $response = Http::withHeaders(self::getHeaders())
                ->post(self::$baseUrl . '/checkout-invoice/create', $invoiceData);

            \Log::info('Response status: ' . $response->status());
            \Log::info('Response body: ' . $response->body());

            if ($response->successful()) {
                $data = $response->json();

                \Log::info('Response data:', $data);

                return [
                    'status' => 'success',
                    'token' => $data['token'] ?? '',
                    'response_code' => $data['response_code'] ?? '',
                    'checkout_url' => $data['response_text'] ?? '',
                ];
            }

            $errorMsg = $response->json()['response_text'] ?? 'Payment initialization failed';
            \Log::error('Paydunya API error: ' . $errorMsg);

            return [
                'status' => 'error',
                'message' => $errorMsg,
            ];

        } catch (\Exception $e) {
            \Log::error('Exception: ' . $e->getMessage());
            \Log::error('Line: ' . $e->getLine());
            \Log::error('File: ' . $e->getFile());

            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Verify payment status
     */
    public static function verifyPayment($token)
    {
        try {
            self::init();

            $response = Http::withHeaders(self::getHeaders())
                ->get(self::$baseUrl.'/checkout-invoice/confirm/'.$token);

            if ($response->successful()) {
                return $response->json();
            }

            return [
                'status' => 'error',
                'message' => 'Payment verification failed',
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Handle payment callback
     */
    public static function handleCallback($data)
    {
        try {
            self::init();

            $token = $data['token'] ?? null;

            if (! $token) {
                return [
                    'status' => 'error',
                    'message' => 'Invalid token',
                ];
            }

            // Vérifier le paiement
            $verification = self::verifyPayment($token);

            if ($verification['status'] === 'completed') {
                // Le paiement est confirmé
                $customData = $verification['custom_data'] ?? [];

                return [
                    'status' => 'success',
                    'data' => $verification,
                    'custom_data' => $customData,
                ];
            }

            return [
                'status' => 'pending',
                'message' => 'Payment not completed yet',
            ];

        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }
}
