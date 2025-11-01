<?php

namespace Modules\LMS\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\LMS\Models\Courses\Course;
use Modules\LMS\Models\TopicProgress;
use Modules\LMS\Models\ChapterProgress;

class CourseProgressController extends Controller
{
    /**
     * Vue globale: étudiants qui suivent les cours de l'instructeur et progression agrégée.
     */
    public function myStudents(Request $request)
    {
        $instructorId = Auth::id();
        // Récupérer tous les cours de l'instructeur
        $courseIds = Course::whereHas('instructors', function ($q) use ($instructorId) {
            $q->where('users.id', $instructorId);
        })->pluck('id');

        if ($courseIds->isEmpty()) {
            $students = collect();
            return view('portal::instructor.students-progress', [
                'students' => $students,
                'rows' => collect(),
            ]);
        }

        // Étudiants ayant de l'activité ou progression sur ces cours
        $studentIds = TopicProgress::whereIn('course_id', $courseIds)->distinct()->pluck('user_id');

        $students = \Modules\LMS\Models\User::whereIn('id', $studentIds)
            ->where('guard', 'student')
            ->with('userable')
            ->paginate(15);

        $rows = $students->map(function ($student) use ($courseIds) {
            $totalTopics = \Modules\LMS\Models\Courses\Topic::whereHas('chapter', function($q) use ($courseIds){
                $q->whereIn('course_id', $courseIds);
            })->count();

            $completedTopics = TopicProgress::where('user_id', $student->id)
                ->whereIn('course_id', $courseIds)
                ->where('status', 'completed')
                ->count();

            $timeSpent = ChapterProgress::where('user_id', $student->id)
                ->whereIn('course_id', $courseIds)
                ->sum('time_spent');

            $progressPct = $totalTopics > 0 ? round(($completedTopics / $totalTopics) * 100, 2) : 0;

            // Top course by completed topics for this student
            $topCourseId = TopicProgress::where('user_id', $student->id)
                ->whereIn('course_id', $courseIds)
                ->where('status', 'completed')
                ->selectRaw('course_id, COUNT(*) as c')
                ->groupBy('course_id')
                ->orderByDesc('c')
                ->limit(1)
                ->value('course_id');
            $topCourseTitle = $topCourseId ? Course::where('id', $topCourseId)->value('title') : null;

            return [
                'student' => $student,
                'completed_topics' => $completedTopics,
                'total_topics' => $totalTopics,
                'avg_progress' => $progressPct,
                'time_spent' => $timeSpent,
                'course_title' => $topCourseTitle,
            ];
        });

        return view('portal::instructor.students-progress', [
            'students' => $students,
            'rows' => $rows,
        ]);
    }
    /**
     * Liste des étudiants inscrits à un cours avec progression.
     */
    public function students(Course $course, Request $request)
    {
        // Vérifier que l'instructeur est bien associé à ce cours
        $isOwner = $course->instructors()->where('users.id', Auth::id())->exists();
        if (!$isOwner) {
            abort(403);
        }

        // Récupérer les inscrits (via relation enrollments/purchase details)
        // On exploite les TopicProgress pour récupérer les user_ids actifs sur ce cours si besoin
        $studentIds = TopicProgress::where('course_id', $course->id)->distinct()->pluck('user_id');

        $students = \Modules\LMS\Models\User::whereIn('id', $studentIds)
            ->where('guard', 'student')
            ->with('userable')
            ->paginate(15);

        $rows = $students->map(function ($student) use ($course) {
            $totalTopics = \Modules\LMS\Models\Courses\Topic::whereHas('chapter', function($q) use ($course){
                $q->where('course_id', $course->id);
            })->count();

            $completedTopics = TopicProgress::where('user_id', $student->id)
                ->where('course_id', $course->id)
                ->where('status', 'completed')
                ->count();

            $timeSpent = ChapterProgress::where('user_id', $student->id)
                ->where('course_id', $course->id)
                ->sum('time_spent');

            $progressPct = $totalTopics > 0 ? round(($completedTopics / $totalTopics) * 100, 2) : 0;

            return [
                'student' => $student,
                'completed_topics' => $completedTopics,
                'total_topics' => $totalTopics,
                'avg_progress' => $progressPct,
                'time_spent' => $timeSpent,
            ];
        });

        return view('portal::instructor.course.students', [
            'course' => $course,
            'students' => $students,
            'rows' => $rows,
        ]);
    }

    /**
     * Détail de progression d'un étudiant sur un cours.
     */
    public function studentProgress(Course $course, int $studentId)
    {
        // Vérifier que l'instructeur est bien associé à ce cours
        $isOwner = $course->instructors()->where('users.id', Auth::id())->exists();
        if (!$isOwner) {
            abort(403);
        }

        $student = \Modules\LMS\Models\User::with('userable')->findOrFail($studentId);

        $topicProgress = TopicProgress::where('user_id', $studentId)
            ->where('course_id', $course->id)
            ->get();

        $completedTopics = $topicProgress->where('status', 'completed')->count();
        $totalTopics = \Modules\LMS\Models\Courses\Topic::whereHas('chapter', function($q) use ($course){
            $q->where('course_id', $course->id);
        })->count();

        $chapterProgressDetails = ChapterProgress::where('user_id', $studentId)
            ->where('course_id', $course->id)
            ->with('chapter')
            ->orderBy('chapter_id')
            ->get();

        $totalTimeSpent = $chapterProgressDetails->sum('time_spent');
        $progressPct = $totalTopics > 0 ? round(($completedTopics / $totalTopics) * 100, 2) : 0;

        return view('portal::instructor.course.student-progress', [
            'course' => $course,
            'student' => $student,
            'topic_progress' => $topicProgress,
            'completed_topics' => $completedTopics,
            'total_topics' => $totalTopics,
            'chapter_progress_details' => $chapterProgressDetails,
            'total_time_spent' => $totalTimeSpent,
            'progress_pct' => $progressPct,
        ]);
    }
}


