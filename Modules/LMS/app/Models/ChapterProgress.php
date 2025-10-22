<?php

namespace Modules\LMS\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\LMS\Models\Courses\Chapter;
use Modules\LMS\Models\Courses\Course;
use Modules\LMS\Models\User;

class ChapterProgress extends Model
{
    protected $fillable = [
        'user_id',
        'chapter_id',
        'course_id',
        'status',
        'started_at',
        'completed_at',
        'time_spent',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec le chapitre
     */
    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    /**
     * Relation avec le cours
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Marquer le chapitre comme commencé
     */
    public function markAsStarted(): void
    {
        $this->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);
    }

    /**
     * Marquer le chapitre comme terminé
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Vérifier si le chapitre est terminé
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Vérifier si le chapitre est en cours
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Vérifier si le chapitre n'a pas été commencé
     */
    public function isNotStarted(): bool
    {
        return $this->status === 'not_started';
    }

    /**
     * Obtenir le pourcentage de completion d'un cours pour un utilisateur
     */
    public static function getCourseCompletionPercentage(int $userId, int $courseId): float
    {
        $totalChapters = Chapter::where('course_id', $courseId)->count();
        
        if ($totalChapters === 0) {
            return 0;
        }

        $completedChapters = self::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->where('status', 'completed')
            ->count();

        return round(($completedChapters / $totalChapters) * 100, 2);
    }

    /**
     * Obtenir la progression d'un cours pour un utilisateur
     */
    public static function getCourseProgress(int $userId, int $courseId): array
    {
        $chapters = Chapter::where('course_id', $courseId)
            ->with(['progress' => function ($query) use ($userId) {
                $query->where('user_id', $userId);
            }])
            ->orderBy('order')
            ->get();

        $totalChapters = $chapters->count();
        $completedChapters = $chapters->where('progress.status', 'completed')->count();
        $inProgressChapters = $chapters->where('progress.status', 'in_progress')->count();
        $notStartedChapters = $chapters->where('progress.status', 'not_started')->count();

        return [
            'total_chapters' => $totalChapters,
            'completed_chapters' => $completedChapters,
            'in_progress_chapters' => $inProgressChapters,
            'not_started_chapters' => $notStartedChapters,
            'completion_percentage' => $totalChapters > 0 ? round(($completedChapters / $totalChapters) * 100, 2) : 0,
            'chapters' => $chapters,
        ];
    }
}





