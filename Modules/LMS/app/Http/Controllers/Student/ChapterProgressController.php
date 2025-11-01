<?php

namespace Modules\LMS\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Modules\LMS\Models\ChapterProgress;
use Modules\LMS\Models\Courses\Chapter;
use Modules\LMS\Models\Courses\Course;
use Illuminate\Support\Facades\Auth;

class ChapterProgressController extends Controller
{
    /**
     * Marquer un chapitre comme commencé
     */
    public function markAsStarted(Request $request, int $chapterId): JsonResponse
    {
        $user = Auth::user();
        $chapter = Chapter::findOrFail($chapterId);
        
        // Vérifier si l'utilisateur est inscrit au cours
        if (!$this->isUserEnrolledInCourse($user->id, $chapter->course_id)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vous n\'êtes pas inscrit à ce cours.'
            ], 403);
        }

        // Créer ou mettre à jour la progression
        $progress = ChapterProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'chapter_id' => $chapterId,
                'course_id' => $chapter->course_id,
            ],
            [
                'status' => 'in_progress',
                'started_at' => now(),
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Chapitre marqué comme commencé.',
            'progress' => $progress
        ]);
    }

    /**
     * Marquer un chapitre comme terminé
     */
    public function markAsCompleted(Request $request, int $chapterId): JsonResponse
    {
        $user = Auth::user();
        $chapter = Chapter::findOrFail($chapterId);
        
        // Vérifier si l'utilisateur est inscrit au cours
        if (!$this->isUserEnrolledInCourse($user->id, $chapter->course_id)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vous n\'êtes pas inscrit à ce cours.'
            ], 403);
        }

        // Créer ou mettre à jour la progression
        $progress = ChapterProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'chapter_id' => $chapterId,
                'course_id' => $chapter->course_id,
            ],
            [
                'status' => 'completed',
                'completed_at' => now(),
            ]
        );

        // Calculer le pourcentage de completion du cours
        $courseCompletion = ChapterProgress::getCourseCompletionPercentage($user->id, $chapter->course_id);

        return response()->json([
            'status' => 'success',
            'message' => 'Chapitre marqué comme terminé.',
            'progress' => $progress,
            'course_completion' => $courseCompletion
        ]);
    }

    /**
     * Obtenir la progression d'un chapitre
     */
    public function getChapterProgress(int $chapterId): JsonResponse
    {
        $user = Auth::user();
        
        $progress = ChapterProgress::where('user_id', $user->id)
            ->where('chapter_id', $chapterId)
            ->with(['chapter', 'course'])
            ->first();

        if (!$progress) {
            return response()->json([
                'status' => 'success',
                'progress' => [
                    'status' => 'not_started',
                    'started_at' => null,
                    'completed_at' => null,
                ]
            ]);
        }

        return response()->json([
            'status' => 'success',
            'progress' => $progress
        ]);
    }

    /**
     * Obtenir la progression d'un cours
     */
    public function getCourseProgress(int $courseId): JsonResponse
    {
        $user = Auth::user();
        
        // Vérifier si l'utilisateur est inscrit au cours
        if (!$this->isUserEnrolledInCourse($user->id, $courseId)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Vous n\'êtes pas inscrit à ce cours.'
            ], 403);
        }

        $progress = ChapterProgress::getCourseProgress($user->id, $courseId);

        return response()->json([
            'status' => 'success',
            'progress' => $progress
        ]);
    }

    /**
     * Obtenir la progression de tous les cours de l'utilisateur
     */
    public function getAllProgress(): JsonResponse
    {
        $user = Auth::user();
        
        $enrolledCourses = $user->enrollments()
            ->with(['course.chapters.progress' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->get();

        $coursesProgress = [];
        
        foreach ($enrolledCourses as $enrollment) {
            $courseProgress = ChapterProgress::getCourseProgress($user->id, $enrollment->course_id);
            $coursesProgress[] = [
                'course_id' => $enrollment->course_id,
                'course_title' => $enrollment->course->title,
                'progress' => $courseProgress
            ];
        }

        return response()->json([
            'status' => 'success',
            'courses_progress' => $coursesProgress
        ]);
    }

    /**
     * Vérifier si l'utilisateur est inscrit au cours
     */
    private function isUserEnrolledInCourse(int $userId, int $courseId): bool
    {
        return \Modules\LMS\Models\Purchase\PurchaseDetails::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->where('type', \Modules\LMS\Enums\PurchaseType::ENROLLED)
            ->exists();
    }
}













