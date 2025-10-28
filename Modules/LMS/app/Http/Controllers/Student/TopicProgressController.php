<?php

namespace Modules\LMS\Http\Controllers\Student;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\LMS\Models\Courses\Topic;
use Modules\LMS\Models\Courses\Chapter;
use Modules\LMS\Models\TopicProgress;
use Modules\LMS\Models\ChapterProgress;
use Modules\LMS\Services\CourseValidationService;
use Modules\LMS\Services\CertificateService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TopicProgressController
{
    /**
     * Mark a reading topic as completed.
     */
    public function markReadingAsCompleted(Request $request)
    {
        Log::info('markReadingAsCompleted: Request received', $request->all());
        try {
            $user = Auth::user();
            if (!$user) {
                Log::warning('markReadingAsCompleted: User not authenticated');
                return response()->json(['status' => 'error', 'message' => 'User not authenticated'], 401);
            }

            $topicId = $request->input('topic_id');
            $topicType = $request->input('topic_type');
            Log::info('markReadingAsCompleted: Processing topic', ['topic_id' => $topicId, 'topic_type' => $topicType]);

            // Trouver le topic directement par son ID
            $topic = Topic::find($topicId);
            
            if (!$topic) {
                Log::warning('markReadingAsCompleted: Topic not found', ['topic_id' => $topicId]);
                return response()->json(['status' => 'error', 'message' => 'Topic not found'], 404);
            }
            Log::info('markReadingAsCompleted: Topic found', ['topic' => $topic]);

            // Vérifier si c'est un quiz et s'il a été soumis avec succès
            if ($topicType === 'quiz') {
                $quiz = $topic->topicable;
                if (!$quiz) {
                    Log::warning('markReadingAsCompleted: Quiz not found for topic', ['topic_id' => $topic->id]);
                    return response()->json(['status' => 'error', 'message' => 'Quiz not found'], 404);
                }
                
                // Vérifier si l'utilisateur a soumis le quiz avec un score suffisant
                $userQuiz = \Modules\LMS\Models\Auth\UserCourseExam::where('user_id', $user->id)
                    ->where('quiz_id', $quiz->id)
                    ->whereNotNull('score')
                    ->first();
                
                if (!$userQuiz) {
                    Log::warning('markReadingAsCompleted: Quiz not submitted by user', [
                        'user_id' => $user->id,
                        'quiz_id' => $quiz->id
                    ]);
                    return response()->json([
                        'status' => 'error', 
                        'message' => 'Vous devez d\'abord soumettre le quiz avant de le marquer comme terminé'
                    ], 400);
                }
                
                if ($userQuiz->score < $quiz->pass_mark) {
                    Log::warning('markReadingAsCompleted: Quiz failed by user', [
                        'user_id' => $user->id,
                        'quiz_id' => $quiz->id,
                        'score' => $userQuiz->score,
                        'pass_mark' => $quiz->pass_mark
                    ]);
                    return response()->json([
                        'status' => 'error', 
                        'message' => 'Vous devez réussir le quiz (score >= ' . $quiz->pass_mark . '%) avant de le marquer comme terminé'
                    ], 400);
                }
                
                Log::info('markReadingAsCompleted: Quiz validation passed', [
                    'user_id' => $user->id,
                    'quiz_id' => $quiz->id,
                    'score' => $userQuiz->score,
                    'pass_mark' => $quiz->pass_mark
                ]);
            }

            // Marquer le topic comme commencé et terminé
            $progress = TopicProgress::where('user_id', $user->id)
                ->where('topic_id', $topic->id)
                ->first();

            if (!$progress) {
                Log::info('markReadingAsCompleted: No progress found, creating new progress');
                $progress = TopicProgress::create([
                    'user_id' => $user->id,
                    'topic_id' => $topic->id,
                    'chapter_id' => $topic->chapter_id,
                    'course_id' => $topic->course_id,
                    'status' => 'completed',
                    'started_at' => now(),
                    'completed_at' => now()
                ]);
                Log::info('markReadingAsCompleted: New progress created', ['progress' => $progress]);
            } else {
                Log::info('markReadingAsCompleted: Progress found, updating progress');
                $progress->markAsStarted();
                $progress->markAsCompleted();
                Log::info('markReadingAsCompleted: Progress updated', ['progress' => $progress]);
            }

            // Pour les quiz, marquer directement le chapitre comme terminé
            if ($topicType === 'quiz' && $topic->chapter) {
                Log::info('markReadingAsCompleted: Marking chapter as completed for quiz');
                $chapterProgress = ChapterProgress::where('user_id', $user->id)
                    ->where('chapter_id', $topic->chapter->id)
                    ->first();

                if (!$chapterProgress) {
                    $chapterProgress = ChapterProgress::create([
                        'user_id' => $user->id,
                        'chapter_id' => $topic->chapter->id,
                        'course_id' => $topic->course_id,
                        'status' => 'completed',
                        'started_at' => now(),
                        'completed_at' => now()
                    ]);
                    Log::info('markReadingAsCompleted: New chapter progress created', ['chapter_progress' => $chapterProgress]);
                } else {
                    $chapterProgress->markAsCompleted();
                    Log::info('markReadingAsCompleted: Chapter progress updated', ['chapter_progress' => $chapterProgress]);
                }
            }

            // Vérifier si le chapitre est terminé
            $chapterCompleted = false;
            $nextChapter = null;
            
            if ($topic->chapter) {
                $courseValidationService = new CourseValidationService();
                $chapterValidation = $courseValidationService->validateChapter($user->id, $topic->chapter);

                if ($chapterValidation['is_completed']) {
                    $chapterProgress = ChapterProgress::where('user_id', $user->id)
                        ->where('chapter_id', $topic->chapter->id)
                        ->first();

                    if (!$chapterProgress) {
                        $chapterProgress = ChapterProgress::create([
                            'user_id' => $user->id,
                            'chapter_id' => $topic->chapter->id,
                            'course_id' => $topic->course_id,
                            'started_at' => now(),
                            'completed_at' => now()
                        ]);
                    } else {
                        $chapterProgress->markAsCompleted();
                    }
                    $chapterCompleted = true;
                }
                
                // Trouver le chapitre suivant
                if ($topic->chapter && $topic->chapter->order !== null) {
                    $nextChapter = Chapter::where('course_id', $topic->course_id)
                        ->where('order', '>', $topic->chapter->order)
                        ->orderBy('order')
                        ->first();
                }
            }

            // Vérifier si le cours est éligible pour un certificat
            $certificateGenerated = false;
            $courseCompleted = false;
            
            // Vérifier si le cours est complètement terminé
            try {
                $courseValidationService = new CourseValidationService();
                $courseValidation = $courseValidationService->validateCourse($user->id, $topic->course_id);
                
                Log::info("🔍 Validation du cours", [
                    'course_id' => $topic->course_id,
                    'is_completed' => $courseValidation['is_completed'] ?? false,
                    'completion_percentage' => $courseValidation['completion_percentage'] ?? 0
                ]);
                
                $courseCompleted = isset($courseValidation['is_completed']) && $courseValidation['is_completed'];
                
                // Si le cours est complètement terminé, générer le certificat
                if ($courseCompleted) {
                    $certificate = CertificateService::generateCertificate($user->id, $topic->course_id);
                    $certificateGenerated = $certificate !== null;
                    
                    if ($certificateGenerated) {
                        Log::info("🎓 Certificat généré automatiquement pour l'utilisateur {$user->id} et le cours {$topic->course_id}");
                    } else {
                        Log::info("⚠️ Le cours est terminé mais le certificat n'a pas pu être généré");
                    }
                } else {
                    Log::info("📚 Le cours n'est pas encore complètement terminé");
                }
            } catch (\Exception $e) {
                Log::error("❌ Erreur lors de la génération du certificat: " . $e->getMessage());
                $courseCompleted = false;
                $certificateGenerated = false;
            }

            // Récupérer la leçon suivante
            $nextTopic = $this->getNextTopic($topic);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Topic marked as completed',
                'progress' => $progress,
                'chapter_completed' => $chapterCompleted,
                'course_completed' => $courseCompleted,
                'certificate_generated' => $certificateGenerated,
                'next_chapter' => $nextChapter ? [
                    'id' => $nextChapter->id,
                    'title' => $nextChapter->title,
                    'url' => route('play.course', [
                        'slug' => $topic->course->slug,
                        'chapter_id' => $nextChapter->id
                    ])
                ] : null,
                'next_topic' => $nextTopic ? [
                    'id' => $nextTopic->id,
                    'title' => $nextTopic->title,
                    'url' => route('play.course', [
                        'slug' => $topic->course->slug,
                        'topic_id' => $nextTopic->id,
                        'type' => $nextTopic->topicable?->topic_type?->slug,
                        'chapter_id' => $nextTopic->chapter_id
                    ])
                ] : null,
            ]);

        } catch (\Exception $e) {
            Log::error("❌ Erreur dans markReadingAsCompleted: " . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Une erreur est survenue lors du traitement',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Get the next topic in the course sequence.
     */
    private function getNextTopic($currentTopic)
    {
        try {
            // Récupérer tous les topics du cours, ordonnés par chapitre puis par ordre
            $allTopics = Topic::where('course_id', $currentTopic->course_id)
                ->with(['chapter', 'topicable.topic_type'])
                ->orderBy('chapter_id')
                ->orderBy('order')
                ->get();
            
            // Trouver l'index du topic actuel
            $currentIndex = $allTopics->search(function ($topic) use ($currentTopic) {
                return $topic->id === $currentTopic->id;
            });
            
            // Si on trouve l'index et qu'il n'est pas le dernier
            if ($currentIndex !== false && $currentIndex < $allTopics->count() - 1) {
                $nextTopic = $allTopics[$currentIndex + 1];
                
                Log::info("Next topic found", [
                    'current_topic_id' => $currentTopic->id,
                    'next_topic_id' => $nextTopic->id,
                    'next_topic_title' => $nextTopic->title,
                    'next_chapter_id' => $nextTopic->chapter_id
                ]);
                
                return $nextTopic;
            }
            
            Log::info("No next topic found", [
                'current_topic_id' => $currentTopic->id,
                'total_topics' => $allTopics->count(),
                'current_index' => $currentIndex
            ]);
            
            return null;
            
        } catch (\Exception $e) {
            Log::error("Error getting next topic: " . $e->getMessage(), [
                'current_topic_id' => $currentTopic->id,
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine()
            ]);
            
            return null;
        }
    }

    /**
     * Mark a topic as started.
     */
    public function markAsStarted(int $topicId)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'User not authenticated'], 401);
            }

            $progress = TopicProgress::where('user_id', $user->id)
                ->where('topic_id', $topicId)
                ->first();

            if (!$progress) {
                $topic = Topic::find($topicId);
                $progress = TopicProgress::create([
                    'user_id' => $user->id,
                    'topic_id' => $topicId,
                    'chapter_id' => $topic->chapter_id,
                    'course_id' => $topic->course_id,
                    'started_at' => now()
                ]);
            } else {
                $progress->markAsStarted();
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Topic marked as started',
                'progress' => $progress
            ]);

        } catch (\Exception $e) {
            Log::error("❌ Erreur dans markAsStarted: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Une erreur est survenue lors du traitement'
            ], 500);
        }
    }

    /**
     * Mark a topic as completed.
     */
    public function markAsCompleted(int $topicId)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['status' => 'error', 'message' => 'User not authenticated'], 401);
            }

            $topic = Topic::find($topicId);
            if (!$topic) {
                return response()->json(['status' => 'error', 'message' => 'Topic not found'], 404);
            }

            $progress = TopicProgress::where('user_id', $user->id)
                ->where('topic_id', $topicId)
                ->first();

            if (!$progress) {
                $progress = TopicProgress::create([
                    'user_id' => $user->id,
                    'topic_id' => $topicId,
                    'chapter_id' => $topic->chapter_id,
                    'course_id' => $topic->course_id,
                    'started_at' => now(),
                    'completed_at' => now()
                ]);
            } else {
                $progress->markAsStarted();
                $progress->markAsCompleted();
            }

            // Vérifier si le chapitre est terminé
            $chapterCompleted = false;
            $nextChapter = null;
            
            if ($topic->chapter) {
                $courseValidationService = new \Modules\LMS\Services\CourseValidationService();
                $chapterValidation = $courseValidationService->validateChapter($user->id, $topic->chapter);

                if ($chapterValidation['is_completed']) {
                    $chapterProgress = ChapterProgress::where('user_id', $user->id)
                        ->where('chapter_id', $topic->chapter->id)
                        ->first();

                    if (!$chapterProgress) {
                        $chapterProgress = ChapterProgress::create([
                            'user_id' => $user->id,
                            'chapter_id' => $topic->chapter->id,
                            'course_id' => $topic->course_id,
                            'status' => 'completed',
                            'started_at' => now(),
                            'completed_at' => now()
                        ]);
                        Log::info('✅ [markAsCompleted] Chapitre marqué comme terminé', [
                            'user_id' => $user->id,
                            'chapter_id' => $topic->chapter->id
                        ]);
                    } else {
                        $chapterProgress->markAsCompleted();
                    }
                    $chapterCompleted = true;
                }
                
                // Trouver le chapitre suivant
                if ($topic->chapter && $topic->chapter->order !== null) {
                    $nextChapter = Chapter::where('course_id', $topic->course_id)
                        ->where('order', '>', $topic->chapter->order)
                        ->orderBy('order')
                        ->first();
                }
            }

            // Vérifier si le cours est éligible pour un certificat
            $certificateGenerated = false;
            $courseCompleted = false;
            
            try {
                Log::info("🔍 [markAsCompleted] Vérification du cours terminé", [
                    'user_id' => $user->id,
                    'course_id' => $topic->course_id
                ]);
                
                $courseValidationService = new \Modules\LMS\Services\CourseValidationService();
                $courseValidation = $courseValidationService->validateCourse($user->id, $topic->course_id);
                $courseCompleted = $courseValidation['is_completed'];
                
                Log::info("📊 [markAsCompleted] Résultat validation cours", [
                    'course_completed' => $courseCompleted,
                    'completion_percentage' => $courseValidation['completion_percentage'] ?? 'N/A'
                ]);
                
                if ($courseCompleted) {
                    Log::info("✅ [markAsCompleted] Cours terminé ! Génération du certificat...");
                    
                    $certificate = \Modules\LMS\Services\CertificateService::generateCertificate($user->id, $topic->course_id);
                    $certificateGenerated = $certificate !== null;
                    
                    if ($certificateGenerated) {
                        Log::info("🎓 [markAsCompleted] Certificat généré automatiquement", [
                            'user_id' => $user->id,
                            'course_id' => $topic->course_id,
                            'certificate_id' => $certificate->certificate_id
                        ]);
                    } else {
                        Log::warning("⚠️ [markAsCompleted] Le certificat n'a pas pu être généré");
                    }
                } else {
                    Log::info("📚 [markAsCompleted] Cours pas encore terminé", [
                        'completed_chapters' => $courseValidation['completed_chapters'] ?? 'N/A',
                        'total_chapters' => $courseValidation['total_chapters'] ?? 'N/A'
                    ]);
                }
            } catch (\Exception $e) {
                Log::error("❌ [markAsCompleted] Erreur lors de la génération du certificat", [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Topic marked as completed',
                'progress' => $progress,
                'chapter_completed' => $chapterCompleted,
                'course_completed' => $courseCompleted,
                'certificate_generated' => $certificateGenerated,
                'next_chapter' => $nextChapter ? [
                    'id' => $nextChapter->id,
                    'title' => $nextChapter->title
                ] : null
            ]);

        } catch (\Exception $e) {
            Log::error("❌ Erreur dans markAsCompleted: " . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Une erreur est survenue lors du traitement'
            ], 500);
        }
    }

    /**
     * Get the progress for a specific topic.
     */
    public function getTopicProgress(int $topicId)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not authenticated'], 401);
        }

        $progress = TopicProgress::where('user_id', $user->id)
            ->where('topic_id', $topicId)
            ->first();

        if (!$progress) {
            return response()->json(['status' => 'error', 'message' => 'Progress not found'], 404);
        }

        return response()->json([
            'status' => 'success',
            'progress' => $progress
        ]);
    }

    /**
     * Get chapter topics progress.
     */
    public function getChapterTopicsProgress(int $chapterId)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not authenticated'], 401);
        }

        $progress = TopicProgress::where('user_id', $user->id)
            ->whereHas('topic', function($query) use ($chapterId) {
                $query->where('chapter_id', $chapterId);
            })
            ->get();

        return response()->json([
            'status' => 'success',
            'progress' => $progress
        ]);
    }

    /**
     * Get all progress.
     */
    public function getAllProgress()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not authenticated'], 401);
        }

        $progress = TopicProgress::where('user_id', $user->id)->get();

        return response()->json([
            'status' => 'success',
            'progress' => $progress
        ]);
    }
}