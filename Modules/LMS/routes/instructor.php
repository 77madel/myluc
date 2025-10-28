<?php

use Illuminate\Support\Facades\Route;
use Modules\LMS\Http\Controllers\Instructor\SaleController;
use Modules\LMS\Http\Controllers\Instructor\ForumController;
use Modules\LMS\Http\Controllers\Instructor\PayoutController;
use Modules\LMS\Http\Controllers\Instructor\ReviewController;
use Modules\LMS\Http\Controllers\Instructor\SettingController;
use Modules\LMS\Http\Controllers\Instructor\SupportController;
use Modules\LMS\Http\Controllers\Admin\Courses\ChapterController;
use Modules\LMS\Http\Controllers\Instructor\InstructorController;
use Modules\LMS\Http\Controllers\Instructor\Courses\TagController;
use Modules\LMS\Http\Controllers\Instructor\NoticesBoardController;
use Modules\LMS\Http\Controllers\Instructor\NotificationController;
use Modules\LMS\Http\Controllers\Admin\Localization\StateController;
use Modules\LMS\Http\Controllers\Instructor\Courses\CourseController;
use Modules\LMS\Http\Controllers\Admin\Courses\Quizzes\QuizController;
use Modules\LMS\Http\Controllers\Admin\Courses\Topics\TopicController;
use Modules\LMS\Http\Controllers\Admin\Localization\CountryController;
use Modules\LMS\Http\Controllers\Admin\Courses\Quizzes\QuestionController;
use Modules\LMS\Http\Controllers\Admin\Courses\Quizzes\QuizTypeController;
use Modules\LMS\Http\Controllers\Instructor\Courses\Bundle\BundleController;
use Modules\LMS\Http\Controllers\Admin\Courses\Quizzes\QuizQuestionController;
use Modules\LMS\Http\Controllers\Instructor\InstructorMessageController;

Route::group(
    ['prefix' => 'instructor', 'as' => 'instructor.', 'middleware' => ['auth:web',  'role:Instructor', 'checkInstaller']],
    function () {

        Route::get('/', [InstructorController::class, 'index'])->name('dashboard');
        Route::post('logout/', [InstructorController::class, 'logout'])->name('logout');
        Route::get('students/', [InstructorController::class, 'students'])->name('student.list');
        Route::get('students/profile/{id}', [InstructorController::class, 'profile'])->name('student.profile');
        Route::get('searching-suggestion', [InstructorController::class, 'searchingSuggestion'])->name('searching.suggestion');
        Route::get('assignments', [InstructorController::class, 'courseAssignments'])->name('assignments');
        Route::get('assignments/{assignment_id}/students', [InstructorController::class, 'studentAssignments'])->name('student.assignment');
        Route::post('assignment/mark/{id}', [InstructorController::class, 'assignmentMark'])->name('assignment.mark');

        // Instructor Course Routes
        Route::get('courses', [CourseController::class, 'index'])->name('course.index');
        Route::get('courses/create', [CourseController::class, 'create'])->name('course.create');

        // Instructor Bundle Routes
        Route::get('bundles', [BundleController::class, 'index'])->name('bundle.index');

        // Instructor Quiz Routes
        Route::get('quizzes', [QuizController::class, 'index'])->name('quiz.list');

        // Instructor Review Routes
        Route::get('course-reviews', [ReviewController::class, 'index'])->name('course-review.index');

        // Instructor Financial Routes
        Route::get('sales', [SaleController::class, 'index'])->name('sale.index');
        Route::get('payouts', [PayoutController::class, 'index'])->name('payout.index');

        // Instructor Noticeboard Routes
        Route::get('noticeboard', [NoticesBoardController::class, 'index'])->name('noticeboard.index');

        Route::group(['controller' => InstructorController::class], function () {
            Route::get("wishlists", 'wishlists')->name('wishlist');
            Route::get("offline/payment", 'offlinePayment')->name('offline.payment');
            Route::delete('wishlists/{id}', 'removeWishlist')->name('remove.wishlist');
        });

        // Instructor Forum Routes
        Route::get('forums', [ForumController::class, 'index'])->name('forum.index');
        Route::get('forums/create', [ForumController::class, 'create'])->name('forum.create');
        Route::post('forums', [ForumController::class, 'store'])->name('forum.store');
        Route::get('forums/{forum}/posts', [ForumController::class, 'postsIndex'])->name('forum.posts.index');
        Route::get('forums/{post}', [ForumController::class, 'show'])->name('forum.show');
        Route::post('forums/{post}/reply', [ForumController::class, 'reply'])->name('forum.reply');

        // Instructor Notification Routes
        Route::get('notification', [NotificationController::class, 'history'])->name('notification.history');
        Route::get('notification/read-all', [NotificationController::class, 'notificationReadAll'])->name('notification.read.all');

        // Instructor Setting Routes
        Route::get('setting', [SettingController::class, 'index'])->name('setting');

        // Instructor Support Routes
        Route::resource('supports', SupportController::class)->except(['show']);

        Route::group(['prefix' => 'messages', 'as' => 'messages.'], function () {
            Route::get('/', [\Modules\LMS\Http\Controllers\Instructor\InstructorMessageController::class, 'index'])->name('index');
            Route::get('/{id}', [\Modules\LMS\Http\Controllers\Instructor\InstructorMessageController::class, 'show'])->name('show');
            Route::post('/{id}', [\Modules\LMS\Http\Controllers\Instructor\InstructorMessageController::class, 'store'])->name('store');
            Route::post('/start', [\Modules\LMS\Http\Controllers\Instructor\InstructorMessageController::class, 'startConversation'])->name('start');
        });
        Route::get('student-support', [SupportController::class, 'studentSupport'])->name('student.support');

 
    });
