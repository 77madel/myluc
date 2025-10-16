<?php

/*
namespace Modules\LMS\Http\Controllers\Frontend;

use Illuminate\Http\Request;

use Illuminate\Contracts\View\View;
use App\Http\Controllers\Controller;
use Modules\LMS\Services\Payment\PaymentService;

class PaymentController extends Controller
{

    public function success($method, Request $request)
    {
        PaymentService::success($method, $request->all());
        return redirect()->route('transaction.success');
    }

    public function cancel()
    {
        return redirect()->route('checkout.page');
    }
}*/

namespace Modules\LMS\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\LMS\Services\Payment\PaydunyaService;
use Modules\LMS\Services\Payment\PaymentService;

class PaymentController extends Controller
{
    /**
     * Method success
     */
    /*public function success($method, Request $request)
    {
        if ($method === 'paydunya') {
            $token = $request->input('token');

            // Vérifier le paiement avec Paydunya
            $verification = PaydunyaService::verifyPayment($token);

            if ($verification['status'] === 'completed') {
                // Traiter le paiement réussi
                PaydunyaService::success($method, [
                    'transaction_id' => $verification['receipt_number'],
                    'amount' => $verification['invoice']['total_amount'],
                    'status' => 'completed',
                    'custom_data' => $verification['custom_data'] ?? [],
                ]);

                return redirect()->route('transaction.success');
            }

            toastr()->error('Payment verification failed');

            return redirect()->route('checkout.page');
        }

        PaydunyaService::success($method, $request->all());

        return redirect()->route('transaction.success');
    }*/

    public function success($method, Request $request)
    {
        if ($method === 'paydunya') {
            $token = $request->input('token');

            if (!$token) {
                toastr()->error('Token de paiement manquant');
                return redirect()->route('checkout.page');
            }

            // Vérifier le paiement avec Paydunya
            $verification = PaydunyaService::verifyPayment($token);

            \Log::info('Payment verification:', $verification);

            if (isset($verification['status']) && $verification['status'] === 'completed') {
                // Traiter le paiement réussi avec PaymentService
                PaymentService::success($method, [
                    'transaction_id' => $verification['receipt_number'] ?? $token,
                    'amount' => $verification['invoice']['total_amount'] ?? 0,
                    'status' => 'completed',
                    'custom_data' => $verification['custom_data'] ?? []
                ]);

                return redirect()->route('transaction.success');
            }

            toastr()->error('Le paiement n\'a pas été confirmé');
            return redirect()->route('checkout.page');
        }

        // Pour les autres méthodes de paiement
        PaymentService::success($method, $request->all());
        return redirect()->route('transaction.success');
    }

    /**
     * Handle Paydunya callback
     */
    public function callback($method, Request $request)
    {
        if ($method === 'paydunya') {
            $result = PaydunyaService::handleCallback($request->all());

            if ($result['status'] === 'success') {
                // Traiter le paiement confirmé
                PaydunyaService::success($method, [
                    'transaction_id' => $result['data']['receipt_number'],
                    'amount' => $result['data']['invoice']['total_amount'],
                    'status' => 'completed',
                    'custom_data' => $result['custom_data'],
                ]);

                return response()->json([
                    'status' => 'success',
                    'message' => 'Payment processed successfully',
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => $result['message'] ?? 'Payment processing failed',
            ], 400);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Invalid payment method',
        ], 400);
    }

    /**
     * cancel payment
     */
    public function cancel()
    {
        toastr()->warning('Payment cancelled');

        return redirect()->route('checkout.page');
    }
}
