<?php

namespace Modules\LMS\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\LMS\Models\Webinar;
use Modules\LMS\Models\WebinarEnrollment;
use Modules\LMS\Notifications\WebinarNotification;
use Carbon\Carbon;

class SendWebinarReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Send 24-hour reminders
        $this->send24HourReminders();

        // Send 1-hour reminders
        $this->send1HourReminders();

        // Send starting notifications
        $this->sendStartingNotifications();
    }

    /**
     * Send 24-hour reminders for webinars starting tomorrow
     */
    private function send24HourReminders(): void
    {
        $tomorrow = Carbon::tomorrow();
        $webinars = Webinar::whereDate('start_date', $tomorrow)
            ->where('status', 'scheduled')
            ->where('is_published', true)
            ->get();

        foreach ($webinars as $webinar) {
            $enrollments = WebinarEnrollment::where('webinar_id', $webinar->id)
                ->whereIn('status', ['enrolled', 'attended'])
                ->with('user')
                ->get();

            foreach ($enrollments as $enrollment) {
                $enrollment->user->notify(new WebinarNotification($webinar, 'webinar_reminder'));
            }
        }
    }

    /**
     * Send 1-hour reminders for webinars starting in 1 hour
     */
    private function send1HourReminders(): void
    {
        $oneHourFromNow = Carbon::now()->addHour();
        $webinars = Webinar::whereBetween('start_date', [
            Carbon::now()->addMinutes(55),
            Carbon::now()->addMinutes(65)
        ])
        ->where('status', 'scheduled')
        ->where('is_published', true)
        ->get();

        foreach ($webinars as $webinar) {
            $enrollments = WebinarEnrollment::where('webinar_id', $webinar->id)
                ->whereIn('status', ['enrolled', 'attended'])
                ->with('user')
                ->get();

            foreach ($enrollments as $enrollment) {
                $enrollment->user->notify(new WebinarNotification($webinar, 'webinar_starting'));
            }
        }
    }

    /**
     * Send starting notifications for webinars that are about to start
     */
    private function sendStartingNotifications(): void
    {
        $now = Carbon::now();
        $webinars = Webinar::whereBetween('start_date', [
            $now->subMinutes(5),
            $now->addMinutes(5)
        ])
        ->where('status', 'scheduled')
        ->where('is_published', true)
        ->get();

        foreach ($webinars as $webinar) {
            // Update webinar status to live
            $webinar->update([
                'status' => 'live',
                'is_live' => true
            ]);

            $enrollments = WebinarEnrollment::where('webinar_id', $webinar->id)
                ->whereIn('status', ['enrolled', 'attended'])
                ->with('user')
                ->get();

            foreach ($enrollments as $enrollment) {
                $enrollment->user->notify(new WebinarNotification($webinar, 'webinar_starting'));
            }
        }
    }
}






