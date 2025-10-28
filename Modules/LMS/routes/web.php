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
use Modules\LMS\Http\Controllers\Frontend\WebinarController;
use Modules\LMS\Http\Controllers\Admin\WebinarController as AdminWebinarController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['checkInstaller']], function () {

    // ==================== PUBLIC ROUTES ====================

    // Home Routes
    Route::group(['controller' => HomeController::class], function () {
        Route::get('/', 'index')->name('home.index');
        Route::get('/about-us', 'aboutUs')->name('about.us');
        Route::get('/category-course/{slug}', 'categoryCourse')->name('category.course');
        Route::get('success', 'success')->name('success');
        Route::get('verify-mail/{id}/{hash}', 'verificationMail')->name('mail.verify');
        Route::post('subscribe', 'newsletterSubscribe')->name('newsletter.subscribe');
        Route::get('privacy-policy', 'policyContent')->name('privacy.policy');
        Route::get('terms-conditions', 'termsCondition')->name('terms.condition');
        Route::get('categories', 'categoryList')->name('category.list');
        Route::get('users/{id}/profile', 'userDetail')->name('users.detail');
    });

    // Blog Routes
    Route::controller(BlogController::class)->group(function () {
        Route::get('blogs', 'blogs')->name('blog.list');
        Route::get('blogs/{slug}', 'blogDetail')->name('blog.detail');
    });

    // Instructor Routes
    Route::get('instructors', [InstructorController::class, 'index'])->name('instructor.list');

    // Course Routes
    Route::controller(CourseController::class)->group(function () {
        Route::get('courses', 'courseList')->name('course.list');
        Route::get('courses/{slug}', 'courseDetail')->name('course.detail');
    });

    // Bundle Routes
    Route::controller(BundleController::class)->group(function () {
        Route::get('bundles', 'bundleList')->name('bundle.list');
        Route::get('bundles/{slug}', 'bundleDetail')->name('bundle.detail');
    });

    // Forum Routes (Public)
    Route::controller(ForumController::class)->group(function () {
        Route::get('forums', 'forumsList')->name('forumsList');
        Route::get('forums/{slug}', 'forumDetail')->name('forum.detail');
        Route::get('forums/topic/{slug}', 'topicDetail')->name('forum.topic.detail');
    });

    // Authentication Routes
    Route::controller(LoginController::class)->group(function () {
        Route::get('login', 'showForm')->name('login');
        Route::post('login', 'login')->name('auth.login');
    });

    // Registration Routes
    Route::controller(RegisterController::class)->group(function () {
        Route::get('register', 'registerForm')->name('register.page');
        Route::post('register', 'register')->name('auth.register');

        // Organization Enrollment
        Route::get('enroll/{slug}', 'registerForm')->name('organization.enrollment.form');
        Route::post('enroll/{slug}', 'register')->name('organization.enrollment.process');
        Route::get('enroll/{slug}/success', 'enrollmentSuccess')->name('organization.enrollment.success');
    });

    // Password Reset Routes
    Route::controller(ForgotPasswordController::class)->group(function () {
        Route::get('forgot-password', 'showForm')->name('password.request');
        Route::post('forgot-password', 'forgotPassword')->name('forgot.password');
        Route::get('reset-password/{token}', 'passwordReset')->name('password.reset');
        Route::post('reset-password', 'passwordUpdate')->name('password.update');
    });

    // Cart Routes
    Route::controller(CartController::class)->group(function () {
        Route::get('add-to-cart', 'addToCart')->name('add.to.cart');
        Route::get('remove-cart', 'removeCart')->name('remove.cart');
        Route::get('cart', 'cartCourseList')->name('cart.page');
        Route::get('apply-coupon', 'applyCoupon')->name('apply.coupon');
    });

    // Contact Routes
    Route::controller(ContactController::class)->group(function () {
        Route::get('contact', 'index')->name('contact.page');
        Route::post('contact', 'store')->name('contact.store');
    });

    // Organization Routes
    Route::get('organizations', [OrganizationController::class, 'index'])->name('organization.list');

    // Webinar Routes (Public)
    Route::controller(WebinarController::class)->group(function () {
        Route::get('webinars', 'index')->name('webinar.list');
        Route::get('webinars/{slug}', 'show')->name('webinar.detail');
    });

    // Localization & Theme
    Route::get('language', [LocalizationController::class, 'setLanguage'])->name('language.set');
    Route::get('theme/activation/{slug}/{uuid}', [ThemeController::class, 'activationByUrl'])->name('theme.activation_by_uuid');

    // ==================== AUTHENTICATED ROUTES ====================

    Route::middleware('auth')->group(function () {

        // Forum Routes (Authenticated)
        Route::controller(ForumController::class)->group(function () {
            Route::post('forum-post', 'forumPost');
            Route::post('forums/reply', 'storeReply')->name('forum.reply.store');
            Route::get('forums/{forum_slug}/create-topic', 'createTopic')->name('forum.topic.create');
            Route::post('forums/{forum_slug}/store-topic', 'storeTopic')->name('forum.topic.store');
        });

        // Blog Comment
        Route::post('blog/store', [BlogController::class, 'store'])->name('blog.comment');

        // Checkout & Payment Routes
        Route::group(['middleware' => \Modules\LMS\Http\Middleware\CheckCartNotEmpty::class], function () {
            Route::get('/checkout', [CheckoutController::class, 'checkoutPage'])->name('checkout.page');
            Route::post('/checkout/process', [CheckoutController::class, 'checkout'])->name('checkout.process');
            Route::post('/payment/form', [CheckoutController::class, 'paymentFormRender'])->name('payment.form');
        });

        // Payment Controller Routes
        Route::controller(PaymentController::class)->group(function () {
            Route::get('/payment/success/{method}', 'success')->name('payment.success');
            Route::post('/payment/callback/{method}', 'callback')->name('payment.callback');
            Route::get('/payment/cancel', 'cancel')->name('payment.cancel');
        });

        // Checkout Controller Routes
        Route::controller(CheckoutController::class)->group(function () {
            Route::get('/transaction/success/{id?}', 'transactionSuccess')->name('transaction.success');
            Route::post('/course/enroll', 'courseEnrolled')->name('course.enroll');
            Route::post('/subscription/payment', 'subscriptionPayment')->name('subscription.payment');
        });

        // Learning Routes
        Route::controller(CourseController::class)->group(function () {
            Route::get('learn/course/{slug}', 'courseVideoPlayer')->name('play.course');
            Route::get('learn/course-topic', 'leanCourseTopic')->name('learn.course.topic');
            Route::post('course-review', 'review')->name('review');
        });

        // Quiz Routes
        Route::controller(QuizController::class)->group(function () {
            Route::post('quiz/{id}/store', 'quizStoreResult')->name('quiz.store.result');
            Route::post('user/submit-quiz-answer/{quiz_id}/{type}', 'submitQuizAnswer')->name('user.submit.quiz.answer');
            Route::get('quiz/score/{quiz_id}', 'getQuizScore')->name('quiz.score');
        });

        // Exam Routes
        Route::controller(ExamController::class)->group(function () {
            Route::get('exam/{type}/{exam_type_id}/{course_id}', 'examStart')->name('exam.start');
            Route::post('exam-store', 'store')->name('exam.store');
        });

        // Wishlist
        Route::get('add-wishlist', [HomeController::class, 'addWishlist'])->name('add.wishlist');

        // Certificate Routes
        Route::controller(CertificateController::class)->group(function () {
            Route::get('certificate/{id}/download', 'downloadPdf')->name('certificate.download');
            Route::get('certificate/{id}/view', 'viewPdf')->name('certificate.view');
        });
    });

    // ==================== INSTALLER ROUTES ====================

    Route::controller(InstallerController::class)->group(function () {
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
    });
});
