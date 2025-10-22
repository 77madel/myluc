<?php

namespace Modules\LMS\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\LMS\Models\Courses\Chapter;
use Modules\LMS\Models\Courses\Course;

class TopicProgress extends Model
{
    use HasFactory;

    protected $table = 'topic_progress';

    protected $fillable = [
        'user_id',
        'topic_id',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(\Modules\LMS\Models\Courses\Topic::class);
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(Chapter::class);
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    // Helper methods
    public function markAsStarted(): void
    {
        if ($this->status === 'not_started') {
            $this->status = 'in_progress';
            $this->started_at = now();
            $this->save();
        }
    }

    public function markAsCompleted(): void
    {
        if ($this->status === 'in_progress' || $this->status === 'not_started') {
            $this->status = 'completed';
            $this->completed_at = now();
            if ($this->started_at) {
                $this->time_spent = $this->completed_at->diffInSeconds($this->started_at);
            }
            $this->save();
        }
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Check if a topic can be accessed (previous topics completed)
     */
    public static function canAccessTopic(int $userId, int $topicId): bool
    {
        $topic = \Modules\LMS\Models\Courses\Topic::find($topicId);
        if (!$topic) {
            return false;
        }

        // Get all topics in the same chapter, ordered by order
        $chapterTopics = \Modules\LMS\Models\Courses\Topic::where('chapter_id', $topic->chapter_id)
            ->orderBy('order')
            ->get();

        $currentTopicIndex = $chapterTopics->search(function ($t) use ($topicId) {
            return $t->id === $topicId;
        });

        if ($currentTopicIndex === false) {
            return false;
        }

        // If this is the first topic, allow access
        if ($currentTopicIndex === 0) {
            return true;
        }

        // Check if all previous topics are completed
        for ($i = 0; $i < $currentTopicIndex; $i++) {
            $previousTopic = $chapterTopics[$i];
            $progress = self::where('user_id', $userId)
                ->where('topic_id', $previousTopic->id)
                ->first();

            if (!$progress || !$progress->isCompleted()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if a chapter can be accessed (previous chapter completed)
     */
    public static function canAccessChapter(int $userId, int $chapterId): bool
    {
        $chapter = Chapter::find($chapterId);
        if (!$chapter) return false;

        // Get all chapters in the same course, ordered by order
        $courseChapters = Chapter::where('course_id', $chapter->course_id)
            ->orderBy('order')
            ->get();

        $currentChapterIndex = $courseChapters->search(function ($c) use ($chapterId) {
            return $c->id === $chapterId;
        });

        if ($currentChapterIndex === false) return false;

        // Check if all previous chapters are completed
        for ($i = 0; $i < $currentChapterIndex; $i++) {
            $previousChapter = $courseChapters[$i];
            $progress = ChapterProgress::where('user_id', $userId)
                ->where('chapter_id', $previousChapter->id)
                ->first();

            if (!$progress || !$progress->isCompleted()) {
                return false;
            }
        }

        return true;
    }
}
