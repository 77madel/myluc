<?php

namespace Modules\LMS\Repositories\Order;

use Modules\LMS\Classes\Cart;
use Illuminate\Support\Facades\DB;
use Modules\LMS\Enums\BundleAuthor;
use Modules\LMS\Enums\PurchaseType;
use Modules\LMS\Models\Courses\Course;
use Modules\LMS\Models\Auth\Instructor;
use Modules\LMS\Models\PaymentDocument;
use Modules\LMS\Models\Auth\Organization;
use Modules\LMS\Models\Purchase\Purchase;
use Modules\LMS\Models\Purchase\PurchaseDetails;
use Modules\LMS\Models\Courses\Bundle\CourseBundle;
use Modules\Subscribe\Repositories\Subscribe\SubscribeRepository;

class OrderRepository
{
    public static function placeOrder($method, $data = [])
    {

        try {
            DB::beginTransaction();
            $cart = Cart::get();

            $purchase =  self::order($method);
            if (! empty($cart['courses'])) {
                self::orderDetails($purchase->id, $method);
                if ($method == 'offline') {
                    self::paymentDocumentSave($purchase->id, $data['document']);
                }
            }
            if (session()->has('type') && session()->get('type') == "subscription" && module_enable_check('subscribe')) {
                self::subscription($purchase->id, $method);
                session()->forget('type');
                session()->forget('subscription_price');
                session()->forget('subscription_id');
                session()->forget('subscription_id');
            }

            // Gérer les achats d'organisation - détecter automatiquement
            $user = auth()->user();
            if ($user && $user->organization) {
                self::handleOrganizationPurchase($purchase->id, $method);
            }

            Cart::clear();
            DB::commit();
            return [
                'order_id' => $purchase->id,
                'payment_method' => $method,
                'order_status' => $purchase->status
            ];
        } catch (\Throwable $th) {


            dd($th->getMessage());
            DB::rollback();
        }
    }
    /**
     * Method subscription
     *
     * @param int $purchaseId
     * @param string $method
     *
     */
    public static function subscription($purchaseId, $method)
    {
        $subscriptionId = session()->get('subscription_id');
        $response = SubscribeRepository::first($subscriptionId);
        $subscribe = $response['data'] ?? null;
        $itemInfo = [
            'purchase_id' => $purchaseId,
            'subscribe_id' => $subscribe->id,
            'user_id' => authCheck()->id,
            'price' =>  $subscribe->price,
            'platform_fee' => 0,
            'discount_price' => 0,
            'details' => $subscribe,
            'type' => PurchaseType::PURCHASE,
            'status' => $method == 'offline'  ? 'pending' : 'processing',
            'purchase_type' =>  PurchaseType::SUBSCRIBE,
        ];
        static::purchaseDetails($itemInfo);
    }

    /**
     * Method order
     *
     * @param string $method  payment method
     */
    public static function order($method)
    {
        $purchase = Purchase::create([
            'total_amount' => session()->get('subscription_price') ?? Cart::totalPrice(),
            'payment_method' => $method,
            'user_id' => authCheck()->id,
            'type' => 'purchase',
            'status' => $method == 'offline'  ? 'pending' : 'success',
        ]);
        return $purchase;
    }

    /**
     * Method orderDetails
     *
     * @param int $purchaseId
     * @param string $method
     */
    public static function orderDetails($purchaseId, $method)
    {
        $cart = Cart::get();
        foreach ($cart['courses'] as $cart) {
            $item = '';
            if ($cart['type'] == 'bundle') {
                $item = CourseBundle::with('user.userable', 'creator.userable')->firstWhere('id', $cart['id']);
            } else {
                $item = Course::with('coursePrice', 'instructors.userable')->firstWhere('id', $cart['id']);
            }
            $discount_price =  isset($cart['discount_price']) && $cart['discount_price'] - ($item->coursePrice->platform_fee ?? $item->platform_fee) ?? 0;
            $itemInfo = [
                'purchase_id' => $purchaseId,
                'user_id' => authCheck()->id,
                'course_id' => $cart['type'] == 'course' ? $item->id : null,
                'bundle_id' => $cart['type'] == 'bundle' ? $item->id : null,
                'price' => $item?->coursePrice ? ($item->coursePrice->price - $item->coursePrice->platform_fee) : ($item->price - $item->platform_fee),
                'platform_fee' => $item->coursePrice->platform_fee ??  $item->platform_fee ?? 0,
                'discount_price' => $discount_price,
                'details' => $item,
                'type' => PurchaseType::PURCHASE,
                'status' => $method == 'offline'  ? 'pending' : 'processing',
                'purchase_type' => $cart['type'] == 'bundle' ? PurchaseType::BUNDLE : PurchaseType::COURSE,
            ];
            self::purchaseDetails($itemInfo);
            if ($cart['type'] == 'course') {
                self::profitShareCalculate($item, $cart['discount_price']);
            }
            if ($cart['type'] == "bundle") {
                $amount = $item->price - $item->platform_fee;
                switch ($item?->creator_type) {
                    case 'instructor':
                        self::updateUserBalance($amount, $item?->user?->userable?->id);
                        break;
                    case 'org':
                        self::orgProfit($amount, $item?->creator?->userable?->id);
                        break;
                }
            }
        }
    }
    /**
     * purchaseDetails
     *
     * @param array $purchaseDetail [item information]
     */
    public static function purchaseDetails(array $itemInfo)
    {
        $purchaseDetail =  PurchaseDetails::create([
            'purchase_number' => strtoupper(orderNumber()),
            'purchase_id' => $itemInfo['purchase_id'] ?? null,
            'user_id' => $itemInfo['user_id'],
            'course_id' => $itemInfo['course_id'] ?? null,
            'bundle_id' => $itemInfo['bundle_id'] ?? null,
            'subscribe_id' => $itemInfo['subscribe_id'] ?? null,
            'price' => $itemInfo['price'],
            'platform_fee' => $itemInfo['platform_fee'],
            'discount_price' => $itemInfo['discount_price'] ?? '',
            'details' => $itemInfo['details'],
            'type' => $itemInfo['type'],
            'status' => $itemInfo['status'],
            'purchase_type' => $itemInfo['purchase_type'],
        ]);
        return $purchaseDetail;
    }

    /**
     * Method profitShareCalculate
     *
     * @param object $item
     * @param float $discountPrice
     */
    public static function profitShareCalculate($item, $discountPrice, $type = null)
    {
        if ($type == "subscribe") {
            $price = $discountPrice;
        } else {
            $coursePrice = $item->coursePrice ?? null;
            $price =   ($discountPrice ? $discountPrice : $coursePrice->price) - $coursePrice->platform_fee;
        }
        if ($item->organization_id) {
            $totalAmount = 0;
            if ($item->is_multiple_instructor == 1) {
                $totalAmount = self::instructorProfitShare($item->instructors, $price);
            }
            $orgProfit = $price - $totalAmount;
            if ($totalAmount !=  $price) {
                self::orgProfit($orgProfit, $item->organization->userable->id);
            }
        } else {
            if ($item->is_multiple_instructor !== 1) {
                foreach ($item->instructors as $key => $instructor) {
                    if ($key == 0) {
                        $instructorBalance = $price;
                        self::updateUserBalance($instructorBalance, $instructor->userable->id);
                        break;
                    }
                }
            }
            self::instructorProfitShare($item->instructors, $price);
        }
    }

    public static function bundleProfitShare($item, $price, $type = null)
    {
        if (!empty($item->creator_type) && $item->creator_type == BundleAuthor::ORG) {
            self::orgProfit($price, $item?->creator?->userable->id);
        } else {
            self::updateUserBalance(amount: $price, userId: $item?->user?->userable->id);
        }
    }
    /**
     * Method orgProfit
     *
     * @param float $profitBalance
     * @param integer $orgId
     *
     * @return void
     */
    public static function orgProfit($profitBalance, $orgId)
    {
        $organization =  Organization::where('id', $orgId)->first();
        $organization->user_balance += $profitBalance;
        $organization->update();
    }

    /**
     * Method instructorProfitShare
     *
     * @param array $instructors
     * @param float $price
     */
    public static function instructorProfitShare($instructors, $price)
    {
        $totalProfitAmount = 0;
        foreach ($instructors as  $instructor) {
            $percentage =  $instructor->pivot->percentage ?? 0;
            $profitBalance = $percentage != 0  ? $percentage / 100 * $price : 0;
            $totalProfitAmount +=  $profitBalance;
            self::updateUserBalance($profitBalance, $instructor->userable->id);
        }
        return $totalProfitAmount;
    }

    /**
     * Method updateUserBalance
     *
     * @param float $amount
     * @param int $userId ;
     *
     */
    public static function updateUserBalance($amount, $userId)
    {
        $instructor =  Instructor::where('id', $userId)->first();
        $instructor->user_balance += $amount;
        $instructor->update();
    }
    /**
     * Method paymentDocumentSave
     *
     * @param int $purchaseId
     * @param string $document
     */
    public static function paymentDocumentSave($purchaseId, $document)
    {
        PaymentDocument::create([
            'purchase_id' => $purchaseId,
            'document' => $document
        ]);
    }

    /**
     * Gérer les achats d'organisation
     */
    public static function handleOrganizationPurchase($purchaseId, $method)
    {
        try {
            $user = auth()->user();
            if (!$user || !$user->organization) {
                return;
            }

            $organization = $user->organization;

            // Récupérer les informations du cours depuis purchase_details
            $purchaseDetails = DB::table('purchase_details')
                ->where('purchase_id', $purchaseId)
                ->first();

            if (!$purchaseDetails || !$purchaseDetails->course_id) {
                return;
            }

            $courseId = $purchaseDetails->course_id;

            // Créer un lien d'inscription
            $enrollmentLink = \Modules\LMS\Models\Auth\OrganizationEnrollmentLink::create([
                'organization_id' => $organization->id,
                'course_id' => $courseId,
                'name' => "Cours acheté - " . now()->format('Y-m-d H:i:s'),
                'slug' => \Illuminate\Support\Str::random(10),
                'description' => "Lien d'inscription généré automatiquement",
                'valid_until' => now()->addYear(),
                'max_enrollments' => null,
                'current_enrollments' => 0,
                'status' => 'active',
            ]);

            // Mettre à jour la table purchases avec les informations d'organisation
            DB::table('purchases')
                ->where('id', $purchaseId)
                ->update([
                    'organization_id' => $organization->id,
                    'enrollment_link_id' => $enrollmentLink->id,
                    'purchase_type' => 'organization_course',
                    'updated_at' => now()
                ]);

            // Mettre à jour purchase_details avec les informations d'organisation
            DB::table('purchase_details')
                ->where('purchase_id', $purchaseId)
                ->update([
                    'organization_id' => $organization->id,
                    'enrollment_link_id' => $enrollmentLink->id,
                    'updated_at' => now()
                ]);

        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'enregistrement de l\'achat d\'organisation: ' . $e->getMessage());
        }
    }
}
