<?php

namespace Modules\LMS\Services;

use Modules\LMS\Models\CourseCompletion;
use Modules\LMS\Models\Courses\Course;
use Modules\LMS\Models\TopicProgress;
use Modules\LMS\Models\Auth\UserCourseExam;

class CourseValidationService
{
    /**
     * Valider un cours complet
     */
    public function validateCourse(int $userId, int $courseId): array
    {
        try {
            \Log::info("CourseValidationService::validateCourse called", [
                'user_id' => $userId,
                'course_id' => $courseId
            ]);
            
            $course = Course::find($courseId);
            if (!$course) {
                \Log::warning("Course not found", ['course_id' => $courseId]);
                return [
                    'status' => 'error',
                    'message' => 'Cours non trouvé'
                ];
            }
            
            \Log::info("Course found", ['course_title' => $course->title]);

        $chapters = $course->chapters()->orderBy('order')->get();
        
        $validation = [
            'course_id' => $courseId,
            'user_id' => $userId,
            'chapters' => [],
            'total_chapters' => $chapters->count(),
            'completed_chapters' => 0,
            'completion_percentage' => 0,
            'is_completed' => false,
            'validation_summary' => [
                'total_lessons' => 0,
                'completed_lessons' => 0,
                'total_quizzes' => 0,
                'completed_quizzes' => 0,
            ]
        ];
        
        foreach ($chapters as $chapter) {
            $chapterValidation = $this->validateChapter($userId, $chapter);
            $validation['chapters'][] = $chapterValidation;
            
            if ($chapterValidation['is_completed']) {
                $validation['completed_chapters']++;
            }
            
            // Mettre à jour le résumé
            $validation['validation_summary']['total_lessons'] += $chapterValidation['total_lessons'];
            $validation['validation_summary']['completed_lessons'] += $chapterValidation['completed_lessons'];
            $validation['validation_summary']['total_quizzes'] += $chapterValidation['total_quizzes'];
            $validation['validation_summary']['completed_quizzes'] += $chapterValidation['completed_quizzes'];
        }
        
        $validation['completion_percentage'] = $validation['total_chapters'] > 0 
            ? round(($validation['completed_chapters'] / $validation['total_chapters']) * 100, 2)
            : 0;
            
        $validation['is_completed'] = $validation['completion_percentage'] == 100;
        
        \Log::info("Course validation completed", [
            'is_completed' => $validation['is_completed'],
            'completion_percentage' => $validation['completion_percentage'],
            'total_chapters' => $validation['total_chapters'],
            'completed_chapters' => $validation['completed_chapters']
        ]);
        
        return $validation;
        
        } catch (\Exception $e) {
            \Log::error("Error in CourseValidationService::validateCourse", [
                'user_id' => $userId,
                'course_id' => $courseId,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return [
                'status' => 'error',
                'message' => 'Erreur lors de la validation du cours: ' . $e->getMessage()
            ];
        }
    }
    
    /**
     * Valider un chapitre
     */
    public function validateChapter(int $userId, $chapter): array
    {
        $topics = $chapter->topics()->orderBy('order')->get();
        $completedTopics = 0;
        $totalLessons = 0;
        $completedLessons = 0;
        $totalQuizzes = 0;
        $completedQuizzes = 0;
        
        $topicDetails = [];
        
        foreach ($topics as $topic) {
            $topicValidation = $this->validateTopic($userId, $topic);
            $topicDetails[] = $topicValidation;
            
            if ($topicValidation['is_completed']) {
                $completedTopics++;
            }
            
            // Compter les leçons et quiz
            if ($topicValidation['type'] === 'reading') {
                $totalLessons++;
                if ($topicValidation['is_completed']) {
                    $completedLessons++;
                }
            } elseif ($topicValidation['type'] === 'quiz') {
                $totalQuizzes++;
                if ($topicValidation['is_completed']) {
                    $completedQuizzes++;
                }
            }
        }
        
        return [
            'chapter_id' => $chapter->id,
            'chapter_title' => $chapter->title,
            'total_topics' => $topics->count(),
            'completed_topics' => $completedTopics,
            'total_lessons' => $totalLessons,
            'completed_lessons' => $completedLessons,
            'total_quizzes' => $totalQuizzes,
            'completed_quizzes' => $completedQuizzes,
            'is_completed' => $completedTopics == $topics->count(),
            'topics' => $topicDetails
        ];
    }
    
    /**
     * Valider un topic (leçon ou quiz)
     */
    public function validateTopic(int $userId, $topic): array
    {
        $result = [
            'topic_id' => $topic->id,
            'topic_title' => $topic->title,
            'type' => $this->getTopicType($topic),
            'is_completed' => false,
            'details' => []
        ];
        
        if ($topic->topicable_type === 'Modules\\LMS\\Models\\Courses\\Topics\\Reading') {
            // Vérifier si la leçon reading est terminée
            $progress = TopicProgress::where('user_id', $userId)
                ->where('topic_id', $topic->id)
                ->where('status', 'completed')
                ->first();
                
            $result['is_completed'] = $progress !== null;
            $result['details'] = [
                'progress_id' => $progress?->id,
                'completed_at' => $progress?->completed_at,
                'time_spent' => $progress?->time_spent
            ];
        }
        
        elseif ($topic->topicable_type === 'Modules\\LMS\\Models\\Courses\\Topics\\Quiz') {
            // Vérifier si le quiz est validé
            $quiz = $topic->topicable;
            $attempt = UserCourseExam::where('user_id', $userId)
                ->where('quiz_id', $quiz->id)
                ->where('score', '>=', $quiz->pass_mark)
                ->orderBy('created_at', 'desc')
                ->first();
                
            $result['is_completed'] = $attempt !== null;
            $result['details'] = [
                'attempt_id' => $attempt?->id,
                'score' => $attempt?->score,
                'pass_mark' => $quiz->pass_mark,
                'attempted_at' => $attempt?->created_at
            ];
        }
        
        // Pour les vidéos, vérifier la progression
        elseif ($topic->topicable_type === 'Modules\\LMS\\Models\\Courses\\Topics\\Video') {
            $progress = TopicProgress::where('user_id', $userId)
                ->where('topic_id', $topic->id)
                ->where('status', 'completed')
                ->first();
                
            $result['type'] = 'video';
            $result['is_completed'] = $progress !== null;
            $result['details'] = [
                'progress_id' => $progress?->id,
                'completed_at' => $progress?->completed_at,
                'time_spent' => $progress?->time_spent
            ];
        }
        
        return $result;
    }
    
    /**
     * Déterminer le type de topic
     */
    private function getTopicType($topic): string
    {
        if ($topic->topicable_type === 'Modules\\LMS\\Models\\Courses\\Topics\\Reading') {
            return 'reading';
        }
        if ($topic->topicable_type === 'Modules\\LMS\\Models\\Courses\\Topics\\Quiz') {
            return 'quiz';
        }
        if ($topic->topicable_type === 'Modules\\LMS\\Models\\Courses\\Topics\\Video') {
            return 'video';
        }
        return 'other';
    }
    
    /**
     * Enregistrer ou mettre à jour la completion du cours
     */
    public function saveCourseCompletion(int $userId, int $courseId, array $validationDetails = []): CourseCompletion
    {
        return CourseCompletion::updateOrCreate(
            [
                'user_id' => $userId,
                'course_id' => $courseId,
            ],
            [
                'is_completed' => $validationDetails['is_completed'] ?? false,
                'completed_at' => $validationDetails['is_completed'] ? now() : null,
                'completion_percentage' => $validationDetails['completion_percentage'] ?? 0,
                'validation_details' => $validationDetails,
            ]
        );
    }
    
    /**
     * Vérifier si un cours est terminé
     */
    public function isCourseCompleted(int $userId, int $courseId): bool
    {
        $completion = CourseCompletion::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();
            
        return $completion && $completion->is_completed;
    }
    
    /**
     * Obtenir la progression d'un cours
     */
    public function getCourseProgress(int $userId, int $courseId): ?CourseCompletion
    {
        return CourseCompletion::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();
    }
}
