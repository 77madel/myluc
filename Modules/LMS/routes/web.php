<?php


use Illuminate\Support\Facades\Route;
use Modules\LMS\Http\Controllers\InstallerController;
use Modules\LMS\Http\Controllers\Auth\LoginController;
use Modules\LMS\Http\Controllers\Admin\ThemeController;
use Modules\LMS\Http\Controllers\LocalizationController;
use Modules\LMS\Http\Controllers\Auth\RegisterController;
use Modules\LMS\Http\Controllers\Frontend\BlogController;
use Modules\LMS\Http\Controllers\Frontend\CartController;
use Modules\LMS\Http\Controllers\Frontend\ExamController;
use Modules\LMS\Http\Controllers\Frontend\HomeController;
use Modules\LMS\Http\Controllers\Frontend\ForumController;
use Modules\LMS\Http\Controllers\Frontend\BundleController;
use Modules\LMS\Http\Controllers\Frontend\CourseController;
use Modules\LMS\Http\Controllers\Frontend\ContactController;
use Modules\LMS\Http\Controllers\Frontend\PaymentController;
use Modules\LMS\Http\Controllers\Frontend\CheckoutController;
use Modules\LMS\Http\Controllers\Auth\ForgotPasswordController;
use Modules\LMS\Http\Controllers\Frontend\InstructorController;
use Modules\LMS\Http\Controllers\Frontend\OrganizationController;
use Modules\LMS\Http\Controllers\Admin\Courses\Quizzes\QuizController;
use Modules\LMS\Http\Controllers\CertificateControllerSimple as CertificateController;

/*
 *--------------------------------------------------------------------------
 * API Routes
 *--------------------------------------------------------------------------
 *
 * Here is where you can register API routes for your application. These
 * routes are loaded by the RouteServiceProvider within a group which
 * is assigned the "api" middleware group. Enjoy building your API!
 *
*/

/* ==================== */

Route::group(['middleware' => ['checkInstaller']], function () {
    // Home
    Route::group(['controller' => HomeController::class], function () {
        Route::get('/',  'index')->name('home.index');
        Route::get('/about-us',  'aboutUs')->name('about.us');
        Route::get('/category-course/{slug}',  'categoryCourse')->name('category.course');
        Route::get('success',  'success')->name('success');
        Route::get('verify-mail/{id}/{hash}',  'verificationMail')->name('mail.verify');
        Route::post('subscribe',  'newsletterSubscribe')->name('newsletter.subscribe');
        Route::get('privacy-policy',  'policyContent')->name('privacy.policy');
        Route::get('terms-conditions',  'termsCondition')->name('terms.condition');
        Route::get('categories',  'categoryList')->name('category.list');
    });

    Route::get('blogs', [BlogController::class, 'blogs'])->name('blog.list');
    Route::get('blogs/{slug}', [BlogController::class, 'blogDetail'])->name('blog.detail');

    Route::get('instructors', [InstructorController::class, 'index'])->name('instructor.list');
    Route::get('users/{id}/profile', [HomeController::class, 'userDetail'])->name('users.detail');
    Route::get('courses', [CourseController::class, 'courseList'])->name('course.list');
    Route::get('courses/{slug}', [CourseController::class, 'courseDetail'])->name('course.detail');
    Route::get('bundles/{slug}', [BundleController::class, 'bundleDetail'])->name('bundle.detail');
    Route::get('bundles', [BundleController::class, 'bundleList'])->name('bundle.list');

    Route::get('forums', [ForumController::class, 'forumsList']);

    Route::group(['controller' => LoginController::class], function () {
        Route::get('login', 'showForm')->name('login');
        Route::post('login',  'login')->name('auth.login');
    });

    // Register
    Route::group(['controller' => RegisterController::class], function () {

        Route::get('register',  'registerForm')->name('register.page');
        Route::post('register',  'register')->name('auth.register');
        
        // Inscription via lien d'organisation - utilise le même formulaire
        Route::get('enroll/{slug}', 'registerForm')->name('organization.enrollment.form');
        Route::post('enroll/{slug}', 'register')->name('organization.enrollment.process');
        Route::get('enroll/{slug}/success', 'enrollmentSuccess')->name('organization.enrollment.success');
    });

    //============== Forgot Password

    Route::group(['controller' => ForgotPasswordController::class], function () {
        Route::get('forgot-password', 'showForm')->name('password.request');
        Route::post('forgot-password', 'forgotPassword')->name('forgot.password');
        Route::get('reset-password/{token}', 'passwordReset')->name('password.reset');
        Route::post('reset-password', 'passwordUpdate')->name('password.update');
    });

    //========= Cart

    Route::group(['controller' => CartController::class], function () {
        Route::get('add-to-cart',  'addToCart')->name('add.to.cart');
        Route::get('remove-cart',  'removeCart')->name('remove.cart');
        Route::get('cart',  'cartCourseList')->name('cart.page');
        Route::get('apply-coupon', 'applyCoupon')->name('apply.coupon');
    });

    Route::group(['controller' => ContactController::class], function () {
        Route::get('contact', 'index')->name('contact.page');
        Route::post('contact', 'store')->name('contact.store');
    });

    Route::get('organizations', [OrganizationController::class, 'index'])->name('organization.list');
    //Route::get('checkout', [CheckoutController::class, 'checkoutPage'])->name('checkout.page');
    Route::get('/checkout', [CheckoutController::class, 'checkoutPage'])->name('checkout.page');
    
    // ✅ Route pour vérifier la validité de la session (Session Unique)
    Route::post('session/check', [\Modules\LMS\Http\Controllers\SessionCheckController::class, 'check'])->name('session.check');
    
    Route::group(['middleware' => 'auth'], function () {
        Route::post('forum-post', [ForumController::class, 'forumPost']);
        Route::post('blog/store', [BlogController::class, 'store'])->name('blog.comment');

        Route::group(['controller' => CheckoutController::class], function () {
            Route::post('checkout', 'checkout')->name('checkout');
            Route::get('success', 'transactionSuccess')->name('transaction.success');
            Route::get('payment-form', 'paymentFormRender')->name('payment.form');
            Route::post('enrolled',  'courseEnrolled')->name('course.enrolled');
            Route::post('subscription/payment', 'subscriptionPayment')->name('subscription.payment');
            // Page de checkout

            // Page de checkout (avec vérification du panier)
            Route::get('/checkout', [CheckoutController::class, 'checkoutPage'])
                ->name('checkout.page')
                ->middleware(['auth', \Modules\LMS\Http\Middleware\CheckCartNotEmpty::class]);

            // Traitement du checkout
            Route::post('/checkout/process', [CheckoutController::class, 'checkout'])
                ->name('checkout.process')
                ->middleware(['auth', \Modules\LMS\Http\Middleware\CheckCartNotEmpty::class]);

            // Formulaire de paiement
            Route::post('/payment/form', [CheckoutController::class, 'paymentFormRender'])
                ->name('payment.form')
                ->middleware(['auth', \Modules\LMS\Http\Middleware\CheckCartNotEmpty::class]);

            // Succès du paiement
            Route::get('/payment/success/{method}', [PaymentController::class, 'success'])
                ->name('payment.success');

            // Callback Paydunya (webhook)
            Route::post('/payment/callback/{method}', [PaymentController::class, 'callback'])
                ->name('payment.callback');

            // Annulation du paiement
            Route::get('/payment/cancel', [PaymentController::class, 'cancel'])
                ->name('payment.cancel');

            // Page de succès de transaction
            Route::get('/transaction/success/{id?}', [CheckoutController::class, 'transactionSuccess'])
                ->name('transaction.success')
                ->middleware('auth');

            // Inscription à un cours
            Route::post('/course/enroll', [CheckoutController::class, 'courseEnrolled'])
                ->name('course.enroll')
                ->middleware('auth');

            // Paiement d'abonnement
            Route::post('/subscription/payment', [CheckoutController::class, 'subscriptionPayment'])
                ->name('subscription.payment')
                ->middleware('auth');

            // Inscription à un cours
            Route::post('/course/enroll', [CheckoutController::class, 'courseEnrolled'])
                ->name('course.enroll')
                ->middleware('auth');

            // Paiement d'abonnement
            Route::post('/subscription/payment', [CheckoutController::class, 'subscriptionPayment'])
                ->name('subscription.payment')
                ->middleware('auth');
        });
        Route::group(['controller' => PaymentController::class], function () {
            Route::get('payment/success/{method}', 'success')->name('payment.success');
            Route::get('cancel', 'cancel')->name('payment.cancel.web');
        });
    });
    
    // ✅ Routes accessibles par TOUS (admin, instructeur, student) - HORS du groupe middleware auth
    Route::get('learn/course/{slug}', [CourseController::class, 'courseVideoPlayer'])->name('play.course');
    Route::get('learn/course-topic', [CourseController::class, 'leanCourseTopic'])->name('learn.course.topic');
    
    Route::group(['middleware' => 'auth'], function () {
        Route::post('course-review', [CourseController::class, 'review'])->name('review');
        Route::post('quiz/{id}/store', [QuizController::class, 'quizStoreResult'])->name('quiz.store.result');
        Route::post('user/submit-quiz-answer/{quiz_id}/{type}', [QuizController::class, 'submitQuizAnswer'])->name('user.submit.quiz.answer');
        Route::get('quiz/score/{quiz_id}', [QuizController::class, 'getQuizScore'])->name('quiz.score');
        Route::get('exam/{type}/{exam_type_id}/{course_id}', [ExamController::class, 'examStart'])->name('exam.start');
        Route::post('exam-store', [ExamController::class, 'store'])->name('exam.store');
        Route::get('add-wishlist', [HomeController::class, 'addWishlist'])->name('add.wishlist');
        
        // Routes pour les certificats PDF
        Route::get('certificate/{id}/download', [CertificateController::class, 'downloadPdf'])->name('certificate.download');
        Route::get('certificate/{id}/view', [CertificateController::class, 'viewPdf'])->name('certificate.view');
        
        // Routes publiques pour partage sur réseaux sociaux (HORS auth)
        Route::get('certificate/public/{uuid}', [\Modules\LMS\Http\Controllers\CertificateControllerSimple::class, 'showPublic'])->name('certificate.public');
        Route::get('certificate/public/{uuid}/image', [\Modules\LMS\Http\Controllers\CertificateControllerSimple::class, 'getPublicImage'])->name('certificate.public.image');
        
        // Routes LinkedIn OAuth (HORS auth pour callback public)
        Route::get('linkedin/callback', [\Modules\LMS\Http\Controllers\LinkedInShareController::class, 'callback'])->name('linkedin.callback');
    });
    Route::get('language', [LocalizationController::class, 'setLanguage'])->name('language.set');
    Route::get('theme/activation/{slug}/{uuid}', [ThemeController::class, 'activationByUrl'])->name('theme.activation_by_uuid');
});

// install.
Route::controller(InstallerController::class)->group(
    function () {
        Route::get('install', 'installContent')->name('install');
        Route::get('install/requirements', 'requirement')->name('install.requirement');
        Route::get('install/permission', 'permission')->name('install.permission');
        Route::get('install/environment', 'environmentForm')->name('install.environment.form');
        Route::post('install/environment', 'environment')->name('install.environment');
        Route::get('install/database', 'databaseForm')->name('install.database.form');

        Route::post('install/database', 'database')->name('install.database');
        Route::get('install/import-demo', 'importDemo')->name('install.import-demo');
        Route::get('install/license', 'licenseForm')->name('license.form');
        Route::post('install/purchase-code', 'purchaseCode')->name('purchase.code');
        Route::get('install/demo', 'imported')->name('install.demo');
        Route::get('install/final', 'finish')->name('install.final');
        Route::get('license', 'licenseVerifyForm')->name('license.verify.form');
        Route::post('license', 'licenseVerify')->name('license.verify');
    }
);
