<?php

namespace Modules\LMS\Repositories\Organization;

use Carbon\Carbon;
use Modules\LMS\Repositories\BaseRepository;
use Modules\LMS\Models\User;
use Modules\LMS\Models\ChapterProgress;
use Modules\LMS\Models\TopicProgress;
use Modules\LMS\Models\Courses\Course;

class ReportsRepository extends BaseRepository
{
    public function participants(int $organizationId, array $filters = []): array
    {
        $dateFrom = $filters['date_from'] ?? null;
        $dateTo   = $filters['date_to'] ?? null;
        $courseIds = $filters['course_ids'] ?? [];

        $students = User::query()
            ->where('organization_id', $organizationId)
            ->where('userable_type', 'Modules\\LMS\\Models\\Auth\\Student')
            ->with(['enrollments.course'])
            ->paginate(15);

        $rows = $students->map(function ($student) use ($courseIds, $dateFrom, $dateTo) {
            $enrollments = $student->enrollments()->when($courseIds, fn($q) => $q->whereIn('course_id', $courseIds))->get();
            $courseIdsForUser = $enrollments->pluck('course_id');

            $totalTopics = TopicProgress::where('user_id', $student->id)
                ->when($courseIdsForUser, fn($q) => $q->whereIn('course_id', $courseIdsForUser))
                ->count();

            $completed = TopicProgress::where('user_id', $student->id)
                ->when($courseIdsForUser, fn($q) => $q->whereIn('course_id', $courseIdsForUser))
                ->where('status', 'completed')
                ->count();

            $timeSpent = ChapterProgress::where('user_id', $student->id)
                ->when($courseIdsForUser, fn($q) => $q->whereIn('course_id', $courseIdsForUser))
                ->when($dateFrom, fn($q) => $q->where('updated_at', '>=', Carbon::parse($dateFrom)))
                ->when($dateTo, fn($q) => $q->where('updated_at', '<=', Carbon::parse($dateTo)))
                ->sum('time_spent');

            $progressPct = $totalTopics > 0 ? round(($completed / $totalTopics) * 100, 2) : 0;

            return [
                'student' => $student,
                'courses_count' => $enrollments->count(),
                'avg_progress' => $progressPct,
                'completed_topics' => $completed,
                'total_topics' => $totalTopics,
                'time_spent' => $timeSpent,
            ];
        });

        return ['students' => $students, 'rows' => $rows];
    }

    public function courses(int $organizationId, array $filters = []): array
    {
        $dateFrom = $filters['date_from'] ?? null;
        $dateTo   = $filters['date_to'] ?? null;

        $baseQuery = Course::query()->where('organization_id', $organizationId);
        $courses = (clone $baseQuery)
            ->with(['instructors.userable', 'enrollments'])
            ->paginate(15);

        // Fallback: si aucune correspondance par organization_id, lister les cours suivis par les étudiants de l'organisation
        if ($courses->isEmpty()) {
            $orgUserIds = User::where('organization_id', $organizationId)->pluck('id');
            $courseIdsFromUsage = TopicProgress::whereIn('user_id', $orgUserIds)
                ->distinct()->pluck('course_id');
            $courses = Course::whereIn('id', $courseIdsFromUsage)
                ->with(['instructors.userable', 'enrollments'])
                ->paginate(15);
        }

        $rows = $courses->map(function ($course) use ($dateFrom, $dateTo) {
            $completedTopics = TopicProgress::where('course_id', $course->id)->where('status', 'completed')->count();
            $totalTopics = TopicProgress::where('course_id', $course->id)->count();
            $progressPct = $totalTopics > 0 ? round(($completedTopics / $totalTopics) * 100, 2) : 0;

            $timeSpent = ChapterProgress::where('course_id', $course->id)
                ->when($dateFrom, fn($q) => $q->where('updated_at', '>=', Carbon::parse($dateFrom)))
                ->when($dateTo, fn($q) => $q->where('updated_at', '<=', Carbon::parse($dateTo)))
                ->sum('time_spent');

            return [
                'course' => $course,
                'participants' => $course->enrollments->unique('user_id')->count(),
                'progress_avg' => $progressPct,
                'time_spent' => $timeSpent,
            ];
        });

        return ['courses' => $courses, 'rows' => $rows];
    }

    public function usage(int $organizationId, array $filters = []): array
    {
        $dateFrom = $filters['date_from'] ?? Carbon::now()->subDays(30);
        $dateTo   = $filters['date_to'] ?? Carbon::now();

        // Filtrer par cours appartenant à l'organisation
        $courseIds = Course::where('organization_id', $organizationId)->pluck('id');

        $series = ChapterProgress::query()
            ->whereIn('course_id', $courseIds)
            ->whereBetween('updated_at', [Carbon::parse($dateFrom), Carbon::parse($dateTo)])
            ->selectRaw('DATE(updated_at) as d, SUM(time_spent) as seconds')
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        return ['series' => $series, 'date_from' => $dateFrom, 'date_to' => $dateTo];
    }
}


