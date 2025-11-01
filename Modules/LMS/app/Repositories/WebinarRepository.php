<?php

namespace Modules\LMS\Repositories;

use Modules\LMS\Models\Webinar;
use Modules\LMS\Models\WebinarEnrollment;
use Modules\LMS\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class WebinarRepository extends BaseRepository
{
    public function __construct(Webinar $model)
    {
        parent::__construct($model);
    }

    /**
     * Get published webinars with filters.
     */
    public function getPublishedWebinars(array $filters = []): LengthAwarePaginator
    {
        $query = $this->model->published()
            ->with(['instructor', 'category']);

        // Apply filters
        if (isset($filters['category_id']) && $filters['category_id']) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['status']) && $filters['status']) {
            switch ($filters['status']) {
                case 'upcoming':
                    $query->upcoming();
                    break;
                case 'live':
                    $query->live();
                    break;
                case 'completed':
                    $query->completed();
                    break;
            }
        }

        if (isset($filters['price']) && $filters['price']) {
            if ($filters['price'] === 'free') {
                $query->where('is_free', true);
            } elseif ($filters['price'] === 'paid') {
                $query->where('is_free', false);
            }
        }

        if (isset($filters['search']) && $filters['search']) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (isset($filters['instructor_id']) && $filters['instructor_id']) {
            $query->where('instructor_id', $filters['instructor_id']);
        }

        return $query->orderBy('start_date', 'asc')
            ->paginate($filters['per_page'] ?? 12);
    }

    /**
     * Get webinar by slug.
     */
    public function getBySlug(string $slug): ?Webinar
    {
        return $this->model->published()
            ->with(['instructor', 'category', 'enrollments'])
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Get user's enrolled webinars.
     */
    public function getUserEnrolledWebinars(int $userId): LengthAwarePaginator
    {
        return WebinarEnrollment::with(['webinar.instructor', 'webinar.category'])
            ->where('user_id', $userId)
            ->whereIn('status', ['enrolled', 'attended'])
            ->orderBy('enrolled_at', 'desc')
            ->paginate(10);
    }

    /**
     * Check if user is enrolled in webinar.
     */
    public function isUserEnrolled(int $webinarId, int $userId): bool
    {
        return WebinarEnrollment::where('webinar_id', $webinarId)
            ->where('user_id', $userId)
            ->whereIn('status', ['enrolled', 'attended'])
            ->exists();
    }

    /**
     * Enroll user in webinar.
     */
    public function enrollUser(int $webinarId, int $userId): WebinarEnrollment
    {
        return WebinarEnrollment::create([
            'webinar_id' => $webinarId,
            'user_id' => $userId,
            'status' => 'enrolled',
            'enrolled_at' => now(),
        ]);
    }

    /**
     * Cancel user enrollment.
     */
    public function cancelEnrollment(int $webinarId, int $userId): bool
    {
        $enrollment = WebinarEnrollment::where('webinar_id', $webinarId)
            ->where('user_id', $userId)
            ->whereIn('status', ['enrolled', 'attended'])
            ->first();

        if ($enrollment) {
            $enrollment->cancel();
            return true;
        }

        return false;
    }

    /**
     * Get webinar statistics.
     */
    public function getWebinarStatistics(int $webinarId): array
    {
        $webinar = $this->model->findOrFail($webinarId);

        return [
            'total_enrollments' => $webinar->enrollments()->count(),
            'attended' => $webinar->enrollments()->where('status', 'attended')->count(),
            'missed' => $webinar->enrollments()->where('status', 'missed')->count(),
            'cancelled' => $webinar->enrollments()->where('status', 'cancelled')->count(),
            'average_rating' => $webinar->enrollments()->whereNotNull('rating')->avg('rating'),
            'attendance_rate' => $this->calculateAttendanceRate($webinarId),
        ];
    }

    /**
     * Calculate attendance rate for webinar.
     */
    private function calculateAttendanceRate(int $webinarId): float
    {
        $totalEnrollments = WebinarEnrollment::where('webinar_id', $webinarId)->count();
        $attended = WebinarEnrollment::where('webinar_id', $webinarId)
            ->where('status', 'attended')
            ->count();

        if ($totalEnrollments === 0) {
            return 0;
        }

        return round(($attended / $totalEnrollments) * 100, 2);
    }

    /**
     * Get upcoming webinars for user.
     */
    public function getUpcomingWebinarsForUser(int $userId): Collection
    {
        return $this->model->published()
            ->upcoming()
            ->whereHas('enrollments', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->whereIn('status', ['enrolled', 'attended']);
            })
            ->with(['instructor', 'category'])
            ->orderBy('start_date', 'asc')
            ->get();
    }

    /**
     * Get live webinars for user.
     */
    public function getLiveWebinarsForUser(int $userId): Collection
    {
        return $this->model->published()
            ->live()
            ->whereHas('enrollments', function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->whereIn('status', ['enrolled', 'attended']);
            })
            ->with(['instructor', 'category'])
            ->get();
    }

    /**
     * Get related webinars.
     */
    public function getRelatedWebinars(int $webinarId, int $categoryId, int $limit = 4): Collection
    {
        return $this->model->published()
            ->where('id', '!=', $webinarId)
            ->where('category_id', $categoryId)
            ->with(['instructor', 'category'])
            ->limit($limit)
            ->get();
    }

    /**
     * Get webinars by instructor.
     */
    public function getWebinarsByInstructor(int $instructorId): LengthAwarePaginator
    {
        return $this->model->where('instructor_id', $instructorId)
            ->with(['category'])
            ->orderBy('start_date', 'desc')
            ->paginate(10);
    }

    /**
     * Get popular webinars.
     */
    public function getPopularWebinars(int $limit = 6): Collection
    {
        return $this->model->published()
            ->with(['instructor', 'category'])
            ->orderBy('current_participants', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get recent webinars.
     */
    public function getRecentWebinars(int $limit = 6): Collection
    {
        return $this->model->published()
            ->with(['instructor', 'category'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Search webinars.
     */
    public function searchWebinars(string $searchTerm): LengthAwarePaginator
    {
        return $this->model->published()
            ->where(function ($query) use ($searchTerm) {
                $query->where('title', 'like', '%' . $searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $searchTerm . '%')
                    ->orWhere('short_description', 'like', '%' . $searchTerm . '%');
            })
            ->with(['instructor', 'category'])
            ->orderBy('start_date', 'asc')
            ->paginate(12);
    }

    /**
     * Get webinar enrollment details.
     */
    public function getWebinarEnrollment(int $webinarId, int $userId): ?WebinarEnrollment
    {
        return WebinarEnrollment::where('webinar_id', $webinarId)
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Mark user attendance.
     */
    public function markAttendance(int $webinarId, int $userId): bool
    {
        $enrollment = WebinarEnrollment::where('webinar_id', $webinarId)
            ->where('user_id', $userId)
            ->first();

        if ($enrollment) {
            $enrollment->markAsAttended();
            return true;
        }

        return false;
    }

    /**
     * Get webinar participants.
     */
    public function getWebinarParticipants(int $webinarId): LengthAwarePaginator
    {
        return WebinarEnrollment::with('user')
            ->where('webinar_id', $webinarId)
            ->orderBy('enrolled_at', 'desc')
            ->paginate(20);
    }
}






