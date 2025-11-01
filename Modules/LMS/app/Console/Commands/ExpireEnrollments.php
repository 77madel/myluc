<?php

namespace Modules\LMS\Console\Commands;

use Illuminate\Console\Command;
use Modules\LMS\Models\Purchase\PurchaseDetails;
use Modules\LMS\Notifications\NotifyEnrollmentDeadline;

class ExpireEnrollments extends Command
{
    protected $signature = 'lms:expire-enrollments';
    protected $description = 'Marquer les inscriptions expirées et mettre à jour le statut selon les échéances';

    public function handle(): int
    {
        $now = now();

        // Notifications J-7 et J-2 avant course_due_at (limite formation)
        foreach ([7, 2] as $days) {
            $target = $now->copy()->addDays($days)->toDateString();
            $dueSoon = PurchaseDetails::with(['user', 'course'])
                ->whereNull('deleted_at')
                ->where('type', 'enrolled')
                ->whereNotNull('course_due_at')
                ->whereDate('course_due_at', $target)
                ->get();
            foreach ($dueSoon as $pd) {
                if ($pd->user) {
                    $pd->user->notify(new NotifyEnrollmentDeadline([
                        'course_id' => $pd->course_id,
                        'course_title' => $pd->course?->title,
                        'kind' => 'due',
                        'days_before' => $days,
                        'due_at' => $pd->course_due_at,
                    ]));
                }
            }
        }

        // Notifications J-7 et J-2 avant grace_due_at (fin définitive)
        foreach ([7, 2] as $days) {
            $target = $now->copy()->addDays($days)->toDateString();
            $graceSoon = PurchaseDetails::with(['user', 'course'])
                ->whereNull('deleted_at')
                ->where('type', 'enrolled')
                ->whereNotNull('grace_due_at')
                ->whereDate('grace_due_at', $target)
                ->get();
            foreach ($graceSoon as $pd) {
                if ($pd->user) {
                    $pd->user->notify(new NotifyEnrollmentDeadline([
                        'course_id' => $pd->course_id,
                        'course_title' => $pd->course?->title,
                        'kind' => 'grace',
                        'days_before' => $days,
                        'due_at' => $pd->grace_due_at,
                    ]));
                }
            }
        }

        // Passer en grace si limite formation dépassée mais encore avant grace_due_at
        PurchaseDetails::whereNull('deleted_at')
            ->where('type', 'enrolled')
            ->where('enrollment_status', 'in_progress')
            ->whereNotNull('course_due_at')
            ->where('course_due_at', '<=', $now)
            ->where(function($q){ $q->whereNull('grace_due_at')->orWhere('grace_due_at', '>', now()); })
            ->update(['enrollment_status' => 'grace', 'updated_at' => $now]);

        // Passer en expired si grace_due_at dépassé (et notifier)
        $toExpire = PurchaseDetails::with(['user','course'])
            ->whereNull('deleted_at')
            ->where('type', 'enrolled')
            ->whereIn('enrollment_status', ['in_progress','grace'])
            ->whereNotNull('grace_due_at')
            ->where('grace_due_at', '<=', $now)
            ->get();

        foreach ($toExpire as $pd) {
            $pd->update(['enrollment_status' => 'expired', 'updated_at' => $now]);
            if ($pd->user) {
                $pd->user->notify(new NotifyEnrollmentDeadline([
                    'course_id' => $pd->course_id,
                    'course_title' => $pd->course?->title,
                    'kind' => 'expired',
                    'days_before' => 0,
                    'due_at' => $pd->grace_due_at,
                ]));
            }
        }

        $this->info('Enrollments status updated.');
        return self::SUCCESS;
    }
}


