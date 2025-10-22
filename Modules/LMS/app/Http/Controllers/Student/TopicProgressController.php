<?php

namespace Modules\LMS\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\LMS\Models\TopicProgress;
use Modules\LMS\Models\Courses\Topic;
use Modules\LMS\Models\Courses\Chapter;
use Modules\LMS\Models\Courses\Course;

class TopicProgressController extends Controller
{
    /**
     * Mark a topic as started.
     */
    public function markAsStarted(int $topicId)
    {
        \Log::info('TopicProgressController::markAsStarted called with topicId: ' . $topicId);
        
        $user = Auth::user();
        \Log::info('Current user: ' . ($user ? $user->id . ' (guard: ' . $user->guard . ')' : 'null'));
        
        if (!$user || $user->guard !== 'student') {
            \Log::warning('Unauthorized access attempt');
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $topic = Topic::find($topicId);
        \Log::info('Looking for topic with ID: ' . $topicId);
        \Log::info('Topic found: ' . ($topic ? 'YES' : 'NO'));
        if (!$topic) {
            \Log::info('Topic not found by ID, trying topicable_id: ' . $topicId);
            $topic = Topic::where('topicable_id', $topicId)->first();
            \Log::info('Topic found by topicable_id: ' . ($topic ? 'YES' : 'NO'));
            if (!$topic) {
                \Log::warning('Topic not found with ID or topicable_id: ' . $topicId);
                return response()->json(['status' => 'error', 'message' => 'Topic not found'], 404);
            }
        }

        // Check if the student is enrolled in the course
        $isEnrolled = $user->enrollments()->where('course_id', $topic->course_id)->exists();
        \Log::info('Is enrolled check: ' . ($isEnrolled ? 'YES' : 'NO'));
        \Log::info('Course ID: ' . $topic->course_id);
        \Log::info('User enrollments count: ' . $user->enrollments()->count());
        
        // Skip enrollment check for testing - will be re-enabled later
        // if (!$isEnrolled) {
        //     return response()->json(['status' => 'error', 'message' => 'Student not enrolled in this course'], 403);
        // }

        // Check if previous topics are completed - DISABLED FOR TESTING
        // if (!TopicProgress::canAccessTopic($user->id, $topicId)) {
        //     return response()->json([
        //         'status' => 'error', 
        //         'message' => 'Vous devez terminer les leçons précédentes avant de commencer celle-ci'
        //     ], 403);
        // }

        $progress = TopicProgress::firstOrCreate(
            [
                'user_id' => $user->id,
                'topic_id' => $topic->id, // Use the actual Topic ID, not the content ID
                'chapter_id' => $topic->chapter_id,
                'course_id' => $topic->course_id,
            ],
            [
                'status' => 'not_started',
            ]
        );

        $progress->markAsStarted();

        return response()->json(['status' => 'success', 'message' => 'Topic marked as started', 'progress' => $progress]);
    }

    /**
     * Mark a topic as completed.
     */
    public function markAsCompleted(int $topicId)
    {
        \Log::info('TopicProgressController::markAsCompleted called with topicId: ' . $topicId);
        
        $user = Auth::user();
        \Log::info('Current user: ' . ($user ? $user->id . ' (guard: ' . $user->guard . ')' : 'null'));
        
        if (!$user || $user->guard !== 'student') {
            \Log::warning('Unauthorized access attempt');
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $topic = Topic::find($topicId);
        \Log::info('Looking for topic with ID: ' . $topicId);
        \Log::info('Topic found: ' . ($topic ? 'YES' : 'NO'));
        if (!$topic) {
            \Log::info('Topic not found by ID, trying topicable_id: ' . $topicId);
            $topic = Topic::where('topicable_id', $topicId)->first();
            \Log::info('Topic found by topicable_id: ' . ($topic ? 'YES' : 'NO'));
            if (!$topic) {
                \Log::warning('Topic not found with ID or topicable_id: ' . $topicId);
                return response()->json(['status' => 'error', 'message' => 'Topic not found'], 404);
            }
        }

        // Check if the student is enrolled in the course
        $isEnrolled = $user->enrollments()->where('course_id', $topic->course_id)->exists();
        \Log::info('Is enrolled check: ' . ($isEnrolled ? 'YES' : 'NO'));
        \Log::info('Course ID: ' . $topic->course_id);
        \Log::info('User enrollments count: ' . $user->enrollments()->count());
        
        // Skip enrollment check for testing - will be re-enabled later
        // if (!$isEnrolled) {
        //     return response()->json(['status' => 'error', 'message' => 'Student not enrolled in this course'], 403);
        // }

        $progress = TopicProgress::where('user_id', $user->id)
            ->where('topic_id', $topic->id) // Use the actual Topic ID, not the content ID
            ->first();

        if (!$progress) {
            // If no progress exists, create it and mark as completed directly
            $progress = TopicProgress::create([
                'user_id' => $user->id,
                'topic_id' => $topic->id, // Use the actual Topic ID, not the content ID
                'chapter_id' => $topic->chapter_id,
                'course_id' => $topic->course_id,
                'status' => 'not_started', // Will be updated by markAsCompleted
            ]);
        }

        $progress->markAsCompleted();

        // Check if all topics in the chapter are completed
        $chapterTopics = Topic::where('chapter_id', $topic->chapter_id)->get();
        $completedTopics = TopicProgress::where('user_id', $user->id)
            ->where('chapter_id', $topic->chapter_id)
            ->where('status', 'completed')
            ->count();

        $chapterCompleted = $completedTopics >= $chapterTopics->count();

        // If chapter is completed, mark it as completed
        if ($chapterCompleted) {
            $chapterProgress = \Modules\LMS\Models\ChapterProgress::where('user_id', $user->id)
                ->where('chapter_id', $topic->chapter_id)
                ->first();

            if (!$chapterProgress) {
                // Create chapter progress if it doesn't exist
                $chapterProgress = \Modules\LMS\Models\ChapterProgress::create([
                    'user_id' => $user->id,
                    'chapter_id' => $topic->chapter_id,
                    'course_id' => $topic->course_id,
                    'status' => 'not_started',
                ]);
            }
            
            $chapterProgress->markAsCompleted();
        }

        // Obtenir les informations du chapitre suivant si le chapitre actuel est terminé
        $nextChapter = null;
        if ($chapterCompleted && $topic->chapter && $topic->chapter->order !== null) {
            $nextChapter = \Modules\LMS\Models\Courses\Chapter::where('course_id', $topic->course_id)
                ->where('order', '>', $topic->chapter->order)
                ->orderBy('order')
                ->first();
        }

        // Vérifier si le cours est éligible pour un certificat
        $certificateGenerated = false;
        if ($chapterCompleted) {
            try {
                $certificateService = new \Modules\LMS\Services\CertificateService();
                $certificate = $certificateService::generateCertificate($user->id, $topic->course_id);
                $certificateGenerated = $certificate !== null;
                
                if ($certificateGenerated) {
                    \Log::info("🎓 Certificat généré automatiquement pour l'utilisateur {$user->id} et le cours {$topic->course_id}");
                }
            } catch (\Exception $e) {
                \Log::error("❌ Erreur lors de la génération du certificat: " . $e->getMessage());
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Topic marked as completed',
            'progress' => $progress,
            'chapter_completed' => $chapterCompleted,
            'certificate_generated' => $certificateGenerated,
            'next_chapter' => $nextChapter ? [
                'id' => $nextChapter->id,
                'title' => $nextChapter->title,
                'url' => route('play.course', [
                    'slug' => $topic->course->slug,
                    'chapter_id' => $nextChapter->id
                ])
            ] : null,
        ]);
    }

    /**
     * Get the progress for a specific topic.
     */
    public function getTopicProgress(int $topicId)
    {
        $user = Auth::user();
        if (!$user || $user->guard !== 'student') {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $progress = TopicProgress::where('user_id', $user->id)
            ->where('topic_id', $topicId)
            ->first();

        return response()->json(['status' => 'success', 'progress' => $progress]);
    }

    /**
     * Get the progress for all topics in a chapter.
     */
    public function getChapterTopicsProgress(int $chapterId)
    {
        $user = Auth::user();
        if (!$user || $user->guard !== 'student') {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $chapter = Chapter::find($chapterId);
        if (!$chapter) {
            return response()->json(['status' => 'error', 'message' => 'Chapter not found'], 404);
        }

        $topics = Topic::where('chapter_id', $chapterId)
            ->orderBy('order')
            ->get();

        $progress = [];
        foreach ($topics as $topic) {
            $topicProgress = TopicProgress::where('user_id', $user->id)
                ->where('topic_id', $topic->id)
                ->first();

            $progress[] = [
                'topic_id' => $topic->id,
                'title' => $topic->topicable->title ?? 'N/A',
                'status' => $topicProgress ? $topicProgress->status : 'not_started',
                'can_access' => TopicProgress::canAccessTopic($user->id, $topic->id),
                'started_at' => $topicProgress ? $topicProgress->started_at : null,
                'completed_at' => $topicProgress ? $topicProgress->completed_at : null,
            ];
        }

        return response()->json(['status' => 'success', 'progress' => $progress]);
    }

    /**
     * Get all topic progress for the authenticated student.
     */
    public function getAllProgress()
    {
        $user = Auth::user();
        if (!$user || $user->guard !== 'student') {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $allProgress = TopicProgress::where('user_id', $user->id)
            ->with('topic', 'chapter', 'course')
            ->get();

        return response()->json(['status' => 'success', 'progress' => $allProgress]);
    }
}

