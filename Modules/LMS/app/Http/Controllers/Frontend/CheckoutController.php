<?php

/*
namespace Modules\LMS\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Modules\LMS\Classes\Cart;
use App\Http\Controllers\Controller;
use Modules\LMS\Services\Payment\PaypalService;
use Modules\Subscribe\Services\SubscribeService;
use Modules\LMS\Services\Payment\RazorpayService;
use Modules\LMS\Services\Checkout\CheckoutService;
use Modules\LMS\Repositories\Purchase\PurchaseRepository;
use Modules\Subscribe\Repositories\Subscribe\SubscribeRepository;

class CheckoutController extends Controller
{

    public function __construct(protected PurchaseRepository $enrolled) {}

    public function checkoutPage()
    {
        if (!authCheck()) {
            return redirect()->route('login');
        }

        if (Cart::cartQty() == 0) {
            return redirect()->route('home.index');
        }
        session()->forget('type');
        session()->forget('subscription_price');
        session()->forget('subscription_id');
        session()->forget('subscription_id');
        // Prepare cart data for the checkout view.
        $data = [
            'cartCourses' => Cart::get(),
            'totalPrice' => Cart::totalPrice(),
            'discountAmount' => Cart::discountAmount(),
        ];
        return view('theme::checkout.index', compact('data'));
    }

    public function checkout(Request $request)
    {
        $result = CheckoutService::checkout($request);
        return response()->json($result);
    }


    public function transactionSuccess($id = null)
    {
        return view('theme::success.index');
    }

    public function paymentFormRender(Request $request)
    {
        $paymentMethod = $request->payment_method;
        $result = '';
        if ($request->payment_method == "razorpay") {
            $result =  RazorpayService::makePayment();
        }
        if ($request->payment_method == "paypal") {
            $result = PaypalService::makePayment();
        }
        $data = [
            'button' => view('theme::payment.button', compact('paymentMethod', 'result'))->render(),
        ];
        return response()->json([
            'status' => 'success',
            'data' => $data,
            'payment' => true,
        ]);
    }


    public function courseEnrolled(Request $request)
    {
        if (!authCheck()) {
            toastr()->error(translate('Please Login'));
            return redirect()->back();
        }
        $response = $this->enrolled->courseEnrolled($request);
        if ($response['status'] !== "success") {
            return response()->json($response);
        }
        toastr()->success(translate('Thank you for Enrolling'));
        if ($request->ajax()) {
            return response()->json(['status' => $response['status'],  'type' => true]);
        }
        return redirect()->back();
    }


    public function subscriptionPayment(Request $request)
    {
        $activePlan =   SubscribeService::getActiveSubscribe();
        if ($activePlan) {
            toastr()->error('You have already active plan');
            return  redirect()->back();
        }
        $response = SubscribeRepository::first($request->id);
        $subscribe = $response['data'] ?? null;
        $subscribe->price;
        session()->put('type', 'subscription');
        session()->put('subscription_price', $subscribe->price);
        session()->put('subscription_id', $request->id);
        Cart::empty();
        $data = [
            'totalPrice' => $subscribe->price,
            'discountAmount' => 0,
        ];
        return view('theme::checkout.index', compact('data'));
    }
}*/

/*namespace Modules\LMS\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\LMS\Classes\Cart;
use Modules\LMS\Repositories\Purchase\PurchaseRepository;
use Modules\LMS\Services\Checkout\CheckoutService;
use Modules\LMS\Services\Payment\PaydunyaService;
use Modules\Subscribe\Repositories\Subscribe\SubscribeRepository;
use Modules\Subscribe\Services\SubscribeService;

class CheckoutController extends Controller
{
    public function __construct(protected PurchaseRepository $enrolled) {}

    public function checkoutPage()
    {
        if (! authCheck()) {
            return redirect()->route('login');
        }

        if (Cart::cartQty() == 0) {
            return redirect()->route('home.index');
        }

        session()->forget('type');
        session()->forget('subscription_price');
        session()->forget('subscription_id');

        // Prepare cart data for the checkout view.
        $data = [
            'cartCourses' => Cart::get(),
            'totalPrice' => Cart::totalPrice(),
            'discountAmount' => Cart::discountAmount(),
        ];

        return view('theme::checkout.index', compact('data'));
    }

    public function checkout(Request $request)
    {
        $result = CheckoutService::checkout($request);

        return response()->json($result);
    }

    public function transactionSuccess($id = null)
    {
        return view('theme::success.index');
    }

    public function paymentFormRender(Request $request)
    {
        $paymentMethod = $request->payment_method;
        $result = '';

        // Uniquement Paydunya maintenant
        if ($request->payment_method == 'paydunya') {
            $result = PaydunyaService::makePayment();
        }

        $data = [
            'button' => view('theme::payment.button', compact('paymentMethod', 'result'))->render(),
        ];

        return response()->json([
            'status' => 'success',
            'data' => $data,
            'payment' => true,
        ]);
    }

    public function courseEnrolled(Request $request)
    {
        if (! authCheck()) {
            toastr()->error(translate('Please Login'));

            return redirect()->back();
        }

        $response = $this->enrolled->courseEnrolled($request);

        if ($response['status'] !== 'success') {
            return response()->json($response);
        }

        toastr()->success(translate('Thank you for Enrolling'));

        if ($request->ajax()) {
            return response()->json(['status' => $response['status'], 'type' => true]);
        }

        return redirect()->back();
    }

    public function subscriptionPayment(Request $request)
    {
        $activePlan = SubscribeService::getActiveSubscribe();

        if ($activePlan) {
            toastr()->error('You have already active plan');

            return redirect()->back();
        }

        $response = SubscribeRepository::first($request->id);
        $subscribe = $response['data'] ?? null;

        session()->put('type', 'subscription');
        session()->put('subscription_price', $subscribe->price);
        session()->put('subscription_id', $request->id);

        Cart::empty();

        $data = [
            'totalPrice' => $subscribe->price,
            'discountAmount' => 0,
        ];

        return view('theme::checkout.index', compact('data'));
    }

    public function handlePaydunyaPayment(Request $request)
    {
        $request->validate([
            'fullname' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|min:8',
            'amount' => 'required|numeric|min:100',
        ]);

        // Appel du service PayDunya
        $payment = (new PaydunyaService)->initiatePayment([
            'fullname' => $request->fullname,
            'email' => $request->email,
            'phone' => $request->phone,
            'amount' => $request->amount,
        ]);

        if ($payment['status'] === 'success') {
            return redirect($payment['redirect_url']);
        }

        return back()->with('error', $payment['message'] ?? 'Une erreur est survenue.');
    }
}*/

namespace Modules\LMS\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\LMS\Classes\Cart;
use Modules\LMS\Repositories\Purchase\PurchaseRepository;
use Modules\LMS\Services\Checkout\CheckoutService;
use Modules\LMS\Services\Payment\PaydunyaService;
use Modules\Subscribe\Repositories\Subscribe\SubscribeRepository;
use Modules\Subscribe\Services\SubscribeService;

class CheckoutController extends Controller
{
    public function __construct(protected PurchaseRepository $enrolled) {}

    /**
     * checkoutPage
     */
    public function checkoutPage()
    {
        if (! authCheck()) {
            return redirect()->route('login');
        }

        if (Cart::cartQty() == 0) {
            return redirect()->route('home.index');
        }

        session()->forget('type');
        session()->forget('subscription_price');
        session()->forget('subscription_id');

        // Prepare cart data for the checkout view.
        $data = [
            'cartCourses' => Cart::get(),
            'totalPrice' => Cart::totalPrice(),
            'discountAmount' => Cart::discountAmount(),
        ];

        return view('theme::checkout.index', compact('data'));
    }

    /**
     * checkout
     */
    public function checkout(Request $request)
    {
        $result = CheckoutService::checkout($request);

        return response()->json($result);
    }

    /**
     * Method transactionSuccess
     */
    public function transactionSuccess($id = null)
    {
        return view('theme::success.index');
    }

    /**
     * paymentFormRender - Initialise directement le paiement Paydunya
     */
    public function paymentFormRender(Request $request)
    {
        if (! authCheck()) {
            return response()->json([
                'status' => 'error',
                'message' => translate('Please login to continue'),
            ], 401);
        }

        $paymentMethod = $request->payment_method;

        // Uniquement Paydunya
        if ($paymentMethod !== 'paydunya') {
            return response()->json([
                'status' => 'error',
                'message' => translate('Invalid payment method'),
            ], 400);
        }

        try {
            // Initialiser le paiement Paydunya
            $result = PaydunyaService::makePayment();

            if ($result['status'] === 'success') {
                // Retourner directement l'URL de paiement
                return response()->json([
                    'status' => 'success',
                    'data' => [
                        'button' => view('theme::payment.button', compact('paymentMethod', 'result'))->render(),
                        'checkout_url' => $result['checkout_url'],
                        'token' => $result['token'],
                    ],
                    'payment' => true,
                ]);
            }

            return response()->json([
                'status' => 'error',
                'message' => $result['message'] ?? translate('Unable to initialize payment'),
            ], 500);

        } catch (\Exception $e) {
            \Log::error('Payment initialization error: '.$e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => translate('An error occurred. Please try again.'),
            ], 500);
        }
    }

    /**
     * courseEnrolled
     */
    public function courseEnrolled(Request $request)
    {
        if (! authCheck()) {
            toastr()->error(translate('Please Login'));

            return redirect()->back();
        }

        $response = $this->enrolled->courseEnrolled($request);

        if ($response['status'] !== 'success') {
            return response()->json($response);
        }

        toastr()->success(translate('Thank you for Enrolling'));

        if ($request->ajax()) {
            return response()->json(['status' => $response['status'], 'type' => true]);
        }

        return redirect()->back();
    }

    /**
     * subscriptionPayment
     */
    public function subscriptionPayment(Request $request)
    {
        $activePlan = SubscribeService::getActiveSubscribe();

        if ($activePlan) {
            toastr()->error('You have already active plan');

            return redirect()->back();
        }

        $response = SubscribeRepository::first($request->id);
        $subscribe = $response['data'] ?? null;

        session()->put('type', 'subscription');
        session()->put('subscription_price', $subscribe->price);
        session()->put('subscription_id', $request->id);

        Cart::empty();

        $data = [
            'totalPrice' => $subscribe->price,
            'discountAmount' => 0,
        ];

        return view('theme::checkout.index', compact('data'));
    }
}
