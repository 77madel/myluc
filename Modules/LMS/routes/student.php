<?php

use Illuminate\Support\Facades\Route;
use Modules\LMS\Http\Controllers\Student\ReviewController;
use Modules\LMS\Http\Controllers\Student\StudentController;
use Modules\LMS\Http\Controllers\Student\SupportController;
use Modules\LMS\Http\Controllers\Student\NotificationController;
use Modules\LMS\Http\Controllers\Student\ChapterProgressController;
use Modules\LMS\Http\Controllers\CertificateController;

Route::group(
    ['prefix' => 'dashboard', 'as' => 'student.', 'middleware' => ['auth', 'role:Student', 'checkInstaller']],
    function () {

        Route::group(['controller' => StudentController::class], function () {
            Route::get('/', 'dashboard')->name('dashboard');
            Route::post('logout', 'logout')->name('logout');
            Route::get('my-enrolled-course', 'allCourse')->name('enroll.index');
            Route::get('purchase-course', 'purchaseCourse')->name('purchase.index');
            Route::get('bundle-course', 'bundleCourse')->name('bundle.index');
            Route::get('certificate', 'certificate')->name('certificate.index');
            Route::get('quizzes/my-result', 'quizResult')->name('quiz.result');
            Route::get('quiz-details/{userQuizId}', 'quizDetails')->name('quiz.details');
            Route::get('assignments', 'assignmentList')->name('assignment.list');
            Route::get("request/certificate/{id}", 'certificateGenerate')->name('generate.certificate');
            Route::get("certificate/view/{id}", [\Modules\LMS\Http\Controllers\CertificateControllerSimple::class, 'viewPdf'])->name('certificate.view');
            Route::get("certificate/download/{id}", [\Modules\LMS\Http\Controllers\CertificateControllerSimple::class, 'downloadPdf'])->name('certificate.download');
            Route::get("wishlists", 'wishlists')->name('wishlist');
            Route::get("offline/payment", 'offlinePayment')->name('offline.payment');
            Route::delete('wishlists/{id}', 'removeWishlist')->name('remove.wishlist');
        });

        Route::group(['controller' => NotificationController::class], function () {
            Route::get('notification', 'history')->name('notification.history');
            Route::get('notification/read/{id}', 'notificationHistoryStatus')->name('notification.history.status');
            Route::delete('notification/delete/{id}', 'notificationHistoryDelete')->name('notification.history.delete');
            Route::get('notification/read-all', 'notificationReadAll')->name('notification.read.all');
        });
        Route::resource('supports', SupportController::class);
        Route::get('course-support', [SupportController::class, 'courseSupport'])->name('course.support.index');
        Route::get('course-support-create', [SupportController::class, 'courseSupportCreate'])->name('course.support.create');
        Route::get('reply/{id}', [SupportController::class, 'reply'])->name('reply');
        Route::post('ticket-reply', [SupportController::class, 'ticketReply'])->name('ticket.reply');
        Route::post('ticket-close/{ticket_id}', [SupportController::class, 'ticketClose'])->name('ticket.close');

        Route::group(['prefix' => 'review'], function () {
            Route::resource('course-review', ReviewController::class)->except('destroy', 'edit', 'show');
        });

        // Routes pour la progression des chapitres
        Route::group(['prefix' => 'chapter-progress', 'controller' => ChapterProgressController::class], function () {
            Route::post('start/{chapterId}', 'markAsStarted')->name('chapter.start');
            Route::post('complete/{chapterId}', 'markAsCompleted')->name('chapter.complete');
            Route::get('progress/{chapterId}', 'getChapterProgress')->name('chapter.progress');
            Route::get('course/{courseId}', 'getCourseProgress')->name('course.progress');
            Route::get('all', 'getAllProgress')->name('all.progress');
        });

        // Routes pour la progression des leçons
        Route::group(['prefix' => 'topic-progress', 'controller' => \Modules\LMS\Http\Controllers\Student\TopicProgressController::class], function () {
            Route::post('start/{topicId}', 'markAsStarted')->name('topic.start');
            Route::post('complete/{topicId}', 'markAsCompleted')->name('topic.complete');
            Route::post('mark-completed', 'markReadingAsCompleted')->name('topic.mark-completed');
            Route::get('progress/{topicId}', 'getTopicProgress')->name('topic.progress');
            Route::get('chapter/{chapterId}', 'getChapterTopicsProgress')->name('chapter.topics.progress');
            Route::get('all', 'getAllProgress')->name('all.topics.progress');
        });
    }
);
