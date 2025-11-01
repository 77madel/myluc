<?php

namespace Modules\LMS\Services;

use Modules\LMS\Models\Webinar;
use Modules\LMS\Models\WebinarEnrollment;
use Modules\LMS\Notifications\WebinarNotification;
use Modules\LMS\Jobs\SendWebinarReminders;
use Modules\LMS\Jobs\EndCompletedWebinars;
use Carbon\Carbon;

class WebinarNotificationService
{
    /**
     * Send enrollment confirmation notification
     */
    public function sendEnrollmentConfirmation(WebinarEnrollment $enrollment): void
    {
        $enrollment->user->notify(
            new WebinarNotification($enrollment->webinar, 'enrollment_confirmation')
        );
    }

    /**
     * Send webinar cancellation notification
     */
    public function sendCancellationNotification(Webinar $webinar): void
    {
        $enrollments = WebinarEnrollment::where('webinar_id', $webinar->id)
            ->whereIn('status', ['enrolled', 'attended'])
            ->with('user')
            ->get();

        foreach ($enrollments as $enrollment) {
            $enrollment->user->notify(
                new WebinarNotification($webinar, 'webinar_cancelled')
            );
        }
    }

    /**
     * Send webinar starting notification
     */
    public function sendStartingNotification(Webinar $webinar): void
    {
        $enrollments = WebinarEnrollment::where('webinar_id', $webinar->id)
            ->whereIn('status', ['enrolled', 'attended'])
            ->with('user')
            ->get();

        foreach ($enrollments as $enrollment) {
            $enrollment->user->notify(
                new WebinarNotification($webinar, 'webinar_starting')
            );
        }
    }

    /**
     * Send webinar completion notification
     */
    public function sendCompletionNotification(Webinar $webinar): void
    {
        $enrollments = WebinarEnrollment::where('webinar_id', $webinar->id)
            ->whereIn('status', ['enrolled', 'attended'])
            ->with('user')
            ->get();

        foreach ($enrollments as $enrollment) {
            $enrollment->user->notify(
                new WebinarNotification($webinar, 'webinar_completed')
            );
        }
    }

    /**
     * Schedule reminder notifications for a webinar
     */
    public function scheduleReminders(Webinar $webinar): void
    {
        // Schedule 24-hour reminder
        $reminderTime = $webinar->start_date->subDay();
        if ($reminderTime->isFuture()) {
            SendWebinarReminders::dispatch()->delay($reminderTime);
        }

        // Schedule 1-hour reminder
        $oneHourReminder = $webinar->start_date->subHour();
        if ($oneHourReminder->isFuture()) {
            SendWebinarReminders::dispatch()->delay($oneHourReminder);
        }

        // Schedule starting notification
        $startingTime = $webinar->start_date->subMinutes(5);
        if ($startingTime->isFuture()) {
            SendWebinarReminders::dispatch()->delay($startingTime);
        }

        // Schedule completion notification
        $completionTime = $webinar->end_date;
        if ($completionTime->isFuture()) {
            EndCompletedWebinars::dispatch()->delay($completionTime);
        }
    }

    /**
     * Send immediate reminder for webinar starting soon
     */
    public function sendImmediateReminder(Webinar $webinar): void
    {
        $enrollments = WebinarEnrollment::where('webinar_id', $webinar->id)
            ->whereIn('status', ['enrolled', 'attended'])
            ->with('user')
            ->get();

        foreach ($enrollments as $enrollment) {
            $enrollment->user->notify(
                new WebinarNotification($webinar, 'webinar_reminder')
            );
        }
    }

    /**
     * Send notification to instructor about webinar status
     */
    public function notifyInstructor(Webinar $webinar, string $type): void
    {
        $message = '';
        $subject = '';

        switch ($type) {
            case 'webinar_starting':
                $subject = 'Votre webinaire commence maintenant';
                $message = 'Votre webinaire "' . $webinar->title . '" commence maintenant.';
                break;
            case 'webinar_ended':
                $subject = 'Votre webinaire est terminé';
                $message = 'Votre webinaire "' . $webinar->title . '" est maintenant terminé.';
                break;
            case 'low_participation':
                $subject = 'Faible participation au webinaire';
                $message = 'Votre webinaire "' . $webinar->title . '" a une faible participation.';
                break;
        }

        if ($message) {
            $webinar->instructor->notify(
                new WebinarNotification($webinar, $type)
            );
        }
    }

    /**
     * Send bulk notifications to all enrolled users
     */
    public function sendBulkNotification(Webinar $webinar, string $message, string $type = 'info'): void
    {
        $enrollments = WebinarEnrollment::where('webinar_id', $webinar->id)
            ->whereIn('status', ['enrolled', 'attended'])
            ->with('user')
            ->get();

        foreach ($enrollments as $enrollment) {
            // Create a custom notification
            $enrollment->user->notify(
                new WebinarNotification($webinar, 'custom_message')
            );
        }
    }

    /**
     * Check and send overdue reminders
     */
    public function checkAndSendOverdueReminders(): void
    {
        $now = Carbon::now();

        // Find webinars starting in the next 2 hours that haven't sent reminders
        $upcomingWebinars = Webinar::whereBetween('start_date', [
            $now,
            $now->addHours(2)
        ])
        ->where('status', 'scheduled')
        ->where('is_published', true)
        ->get();

        foreach ($upcomingWebinars as $webinar) {
            $this->sendImmediateReminder($webinar);
        }
    }

    /**
     * Send notification when webinar is rescheduled
     */
    public function sendRescheduleNotification(Webinar $webinar, Carbon $oldDate): void
    {
        $enrollments = WebinarEnrollment::where('webinar_id', $webinar->id)
            ->whereIn('status', ['enrolled', 'attended'])
            ->with('user')
            ->get();

        foreach ($enrollments as $enrollment) {
            // Send custom notification about rescheduling
            $enrollment->user->notify(
                new WebinarNotification($webinar, 'webinar_rescheduled')
            );
        }
    }
}






