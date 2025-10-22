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

class EndCompletedWebinars implements ShouldQueue
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
        $now = Carbon::now();

        // Find webinars that have ended
        $endedWebinars = Webinar::where('end_date', '<=', $now)
            ->where('status', 'live')
            ->get();

        foreach ($endedWebinars as $webinar) {
            // Update webinar status
            $webinar->update([
                'status' => 'completed',
                'is_live' => false
            ]);

            // Send completion notifications to enrolled users
            $enrollments = WebinarEnrollment::where('webinar_id', $webinar->id)
                ->whereIn('status', ['enrolled', 'attended'])
                ->with('user')
                ->get();

            foreach ($enrollments as $enrollment) {
                $enrollment->user->notify(new WebinarNotification($webinar, 'webinar_completed'));
            }
        }
    }
}


