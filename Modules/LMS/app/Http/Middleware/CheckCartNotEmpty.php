<?php

namespace Modules\LMS\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\LMS\Classes\Cart;

class CheckCartNotEmpty
{
    /**
     * Handle an incoming request.
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Vérifier si c'est un paiement d'abonnement
        $isSubscription = session()->has('type') && session()->get('type') === 'subscription';

        // Si c'est un abonnement, vérifier la session
        if ($isSubscription) {
            if (! session()->has('subscription_id') || ! session()->has('subscription_price')) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Invalid subscription data',
                    ], 400);
                }

                toastr()->error('Invalid subscription. Please try again.');

                return redirect()->route('home.index');
            }

            return $next($request);
        }

        // Sinon, vérifier le panier
        if (Cart::cartQty() == 0) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Your cart is empty',
                ], 400);
            }

            toastr()->warning('Your cart is empty. Please add items to your cart.');

            return redirect()->route('home.index');
        }

        return $next($request);
    }
}
