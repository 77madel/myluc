<?php

use Illuminate\Support\Facades\Route;
use Modules\LMS\Http\Controllers\InstallerController;
use Modules\LMS\Http\Controllers\Auth\{
    LoginController,
    RegisterController,
    ForgotPasswordController
};
use Modules\LMS\Http\Controllers\Admin\{
    ThemeController,
    Courses\Quizzes\QuizController,
    WebinarController as AdminWebinarController
};
use Modules\LMS\Http\Controllers\Frontend\{
    BlogController,
    CartController,
    CourseController,
    BundleController,
    ForumController,
    ContactController,
    HomeController,
    ExamController,
    InstructorController,
    OrganizationController,
    PaymentController,
    CheckoutController,
    WebinarController
};
use Modules\LMS\Http\Controllers\LocalizationController;
use Modules\LMS\Http\Controllers\CertificateControllerSimple as CertificateController;
use Modules\LMS\Http\Controllers\SessionCheckController;
use Modules\LMS\Http\Controllers\AnalyticsController;
use Modules\LMS\Http\Controllers\LinkedInShareController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => ['checkInstaller']], function () {

    /*
    |--------------------------------------------------------------------------
    | PUBLIC ROUTES
    |--------------------------------------------------------------------------
    */
    Route::controller(HomeController::class)->group(function () {
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

    Route::controller(BlogController::class)->group(function () {
        Route::get('blogs', 'blogs')->name('blog.list');
        Route::get('blogs/{slug}', 'blogDetail')->name('blog.detail');
    });

    Route::get('instructors', [InstructorController::class, 'index'])->name('instructor.list');

    Route::controller(CourseController::class)->group(function () {
        Route::get('courses', 'courseList')->name('course.list');
        Route::get('courses/{slug}', 'courseDetail')->name('course.detail');
    });

    Route::controller(BundleController::class)->group(function () {
        Route::get('bundles', 'bundleList')->name('bundle.list');
        Route::get('bundles/{slug}', 'bundleDetail')->name('bundle.detail');
    });

    Route::controller(ForumController::class)->group(function () {
        Route::get('forums', 'forumsList')->name('forums.list');
        Route::get('forums/{slug}', 'forumDetail')->name('forum.detail');
        Route::get('forums/topic/{slug}', 'topicDetail')->name('forum.topic.detail');
    });

    // Authentication
    Route::controller(LoginController::class)->group(function () {
        Route::get('login', 'showForm')->name('login');
        Route::post('login', 'login')->name('auth.login');
    });

    Route::controller(RegisterController::class)->group(function () {
        Route::get('register', 'registerForm')->name('register.page');
        Route::post('register', 'register')->name('auth.register');

        Route::get('enroll/{slug}', 'registerForm')->name('organization.enrollment.form');
        Route::post('enroll/{slug}', 'register')->name('organization.enrollment.process');
        Route::get('enroll/{slug}/success', 'enrollmentSuccess')->name('organization.enrollment.success');
    });

    Route::controller(ForgotPasswordController::class)->group(function () {
        Route::get('forgot-password', 'showForm')->name('password.request');
        Route::post('forgot-password', 'forgotPassword')->name('forgot.password');
        Route::get('reset-password/{token}', 'passwordReset')->name('password.reset');
        Route::post('reset-password', 'passwordUpdate')->name('password.update');
    });

    Route::controller(CartController::class)->group(function () {
        Route::get('cart', 'cartCourseList')->name('cart.page');
        Route::get('add-to-cart', 'addToCart')->name('add.to.cart');
        Route::get('remove-cart', 'removeCart')->name('remove.cart');
        Route::get('apply-coupon', 'applyCoupon')->name('apply.coupon');
    });

    Route::controller(ContactController::class)->group(function () {
        Route::get('contact', 'index')->name('contact.page');
        Route::post('contact', 'store')->name('contact.store');
    });

    Route::get('organizations', [OrganizationController::class, 'index'])->name('organization.list');

    // ✅ Session & Analytics
    Route::post('session/check', [SessionCheckController::class, 'check'])->name('session.check');
    Route::post('analytics/track', [AnalyticsController::class, 'track'])->name('analytics.track');
    Route::post('analytics/conversion', [AnalyticsController::class, 'trackConversion'])->name('analytics.conversion');

    // ✅ Public course access
    Route::get('learn/course/{slug}', [CourseController::class, 'courseVideoPlayer'])->name('play.course');
    Route::get('learn/course-topic', [CourseController::class, 'leanCourseTopic'])->name('learn.course.topic');

    // ✅ Webinars
    Route::controller(WebinarController::class)->group(function () {
        Route::get('webinars', 'index')->name('webinar.list');
        Route::get('webinars/{slug}', 'show')->name('webinar.detail');
    });

    // ✅ Localization & Themes
    Route::get('language', [LocalizationController::class, 'setLanguage'])->name('language.set');
    Route::get('theme/activation/{slug}/{uuid}', [ThemeController::class, 'activationByUrl'])->name('theme.activation_by_uuid');

    /*
    |--------------------------------------------------------------------------
    | AUTHENTICATED ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware('auth')->group(function () {

        // Forum (auth)
        Route::controller(ForumController::class)->group(function () {
            Route::post('forum-post', 'forumPost');
            Route::post('forums/reply', 'storeReply')->name('forum.reply.store');
            Route::get('forums/{forum_slug}/create-topic', 'createTopic')->name('forum.topic.create');
            Route::post('forums/{forum_slug}/store-topic', 'storeTopic')->name('forum.topic.store');
        });

        // Blog
        Route::post('blog/store', [BlogController::class, 'store'])->name('blog.comment');

        // Checkout & Payment
        Route::middleware(\Modules\LMS\Http\Middleware\CheckCartNotEmpty::class)->group(function () {
            Route::get('/checkout', [CheckoutController::class, 'checkoutPage'])->name('checkout.page');
            Route::post('/checkout/process', [CheckoutController::class, 'checkout'])->name('checkout.process');
            Route::post('/payment/form', [CheckoutController::class, 'paymentFormRender'])->name('payment.form');
        });

        Route::controller(PaymentController::class)->group(function () {
            Route::get('/payment/success/{method}', 'success')->name('payment.success');
            Route::post('/payment/callback/{method}', 'callback')->name('payment.callback');
            Route::get('/payment/cancel', 'cancel')->name('payment.cancel');
            // Alias requis par certains services de paiement tiers
            Route::get('/payment/cancel', 'cancel')->name('payment.cancel.web');
        });

        Route::controller(CheckoutController::class)->group(function () {
            Route::get('/transaction/success/{id?}', 'transactionSuccess')->name('transaction.success');
            Route::post('/course/enroll', 'courseEnrolled')->name('course.enroll');
            Route::post('/subscription/payment', 'subscriptionPayment')->name('subscription.payment');
        });

        // Learning
        Route::controller(CourseController::class)->group(function () {
            Route::post('course-review', 'review')->name('review');
        });

        // Quiz
        Route::controller(QuizController::class)->group(function () {
            Route::post('quiz/{id}/store', 'quizStoreResult')->name('quiz.store.result');
            Route::post('user/submit-quiz-answer/{quiz_id}/{type}', 'submitQuizAnswer')->name('user.submit.quiz.answer');
            Route::get('quiz/score/{quiz_id}', 'getQuizScore')->name('quiz.score');
        });

        // Exam
        Route::controller(ExamController::class)->group(function () {
            Route::get('exam/{type}/{exam_type_id}/{course_id}', 'examStart')->name('exam.start');
            Route::post('exam-store', 'store')->name('exam.store');
        });

        // Wishlist
        Route::get('add-wishlist', [HomeController::class, 'addWishlist'])->name('add.wishlist');

        // Certificates
        Route::controller(CertificateController::class)->group(function () {
            Route::get('certificate/{id}/download', 'downloadPdf')->name('certificate.download');
            Route::get('certificate/{id}/view', 'viewPdf')->name('certificate.view');
        });
    });

    /*
    |--------------------------------------------------------------------------
    | PUBLIC CERTIFICATE & LINKEDIN
    |--------------------------------------------------------------------------
    */
    Route::get('certificate/public/{uuid}', [CertificateController::class, 'showPublic'])->name('certificate.public');
    Route::get('certificate/public/{uuid}/image', [CertificateController::class, 'getPublicImage'])->name('certificate.public.image');
    Route::get('linkedin/callback', [LinkedInShareController::class, 'callback'])->name('linkedin.callback');

    /*
    |--------------------------------------------------------------------------
    | INSTALLER ROUTES
    |--------------------------------------------------------------------------
    */
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
