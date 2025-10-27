<?php
namespace Modules\LMS\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Modules\LMS\Repositories\Auth\UserRepository;
use Modules\LMS\Repositories\SearchSuggestionRepository;
use Modules\LMS\Models\Auth\OrganizationEnrollmentLink;
// use Modules\LMS\Models\Auth\OrganizationParticipant; // Supprimé - utilise le système unifié
use Modules\LMS\Services\OrganizationEnrollmentService;
use Modules\LMS\Models\Courses\Course;
use Maatwebsite\Excel\Facades\Excel;

class DashboardController extends Controller implements HasMiddleware
{
    public function __construct(
        protected SearchSuggestionRepository $suggestion,
        protected UserRepository $user,
        protected OrganizationEnrollmentService $enrollmentService
    ) {}

    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['register']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->user->dashboardInfoOrganization();

        return view('portal::organization.index', compact('data'));
    }

    public function register(Request $request)
    {
        return view('portal::auth.organization-register');
    }

    public function searchingSuggestion(Request $request)
    {
        $results = $this->suggestion->searchSuggestion($request);

        return response()->json($results);
    }

    public function logout()
    {
        Auth::logout();
        Session::flush();

        return redirect('/');
    }

    public function students()
    {
        $students = $this->user->enrolledStudents();

        return view('portal::organization.student.student-list', compact('students'));
    }

    /**
     *  View Student Profile.
     */
    public function profile($id)
    {
        $user = $this->user->studentProfileView($id);

        return view('portal::admin.student.profile', compact('user'));
    }

    public function wishlists()
    {
        $response = UserRepository::wishlist();
        $wishlists = $response['data'] ?? [];

        return view('portal::organization.wishlist.index', compact('wishlists'));
    }

    public function removeWishlist($id)
    {
        $response = UserRepository::removeWishlist($id);
        $response['url'] = route('organization.wishlist');

        return response()->json($response);
    }

    /**
     * Afficher les liens d'inscription de l'organisation
     */
    public function enrollmentLinks()
    {
        $organization = Auth::user()->organization;

        if (!$organization) {
            abort(403, 'Aucune organisation associée à ce compte');
        }

        $enrollmentLinks = $organization->enrollmentLinks()
            ->with(['course', 'participants'])
            ->latest()
            ->paginate(10);

        return view('portal::organization.enrollment.links.index', compact('enrollmentLinks'));
    }


    /**
     * Afficher les étudiants de l'organisation
     */
    public function organizationStudents()
    {
        $organization = Auth::user()->organization;

        if (!$organization) {
            abort(403, 'Aucune organisation associée à ce compte');
        }

        // Utiliser le système unifié avec users + students
        $students = \Modules\LMS\Models\User::where('organization_id', $organization->id)
            ->where('userable_type', 'Modules\LMS\Models\Auth\Student')
            ->with(['userable', 'enrollments.course'])
            ->latest()
            ->paginate(15);

        // Précharger les données de progression pour chaque étudiant
        foreach ($students as $student) {
            $this->loadStudentProgressData($student);
        }

        return view('portal::organization.student.student-list', compact('students'));
    }

    /**
     * Charger les données de progression pour un étudiant
     */
    private function loadStudentProgressData($student)
    {
        $enrolledCourses = $student->enrollments()->with('course')->get();
        $totalProgress = 0;
        $courseCount = 0;
        $totalTimeSpent = 0;

        foreach ($enrolledCourses as $enrollment) {
            if ($enrollment->course) {
                $courseId = $enrollment->course->id;

                // Récupérer le nombre total de leçons
                $totalTopics = \Modules\LMS\Models\Courses\Topic::whereHas('chapter', function($query) use ($courseId) {
                    $query->where('course_id', $courseId);
                })->count();

                // Récupérer le nombre de leçons terminées
                $completedTopics = \Modules\LMS\Models\TopicProgress::where('user_id', $student->id)
                    ->where('course_id', $courseId)
                    ->where('status', 'completed')
                    ->count();

                // Récupérer le temps passé sur ce cours
                $courseTimeSpent = \Modules\LMS\Models\ChapterProgress::where('user_id', $student->id)
                    ->where('course_id', $courseId)
                    ->sum('time_spent');

                $totalTimeSpent += $courseTimeSpent;

                if ($totalTopics > 0) {
                    $courseProgress = round(($completedTopics / $totalTopics) * 100, 2);
                    $totalProgress += $courseProgress;
                    $courseCount++;
                }
            }
        }

        $student->average_progress = $courseCount > 0 ? round($totalProgress / $courseCount, 2) : 0;
        $student->enrolled_courses_count = $enrolledCourses->count();
        $student->total_time_spent = $totalTimeSpent;
        $student->total_time_spent_formatted = \App\Helpers\TimeHelper::formatTimeSpent($totalTimeSpent);
    }

    /**
     * Afficher la progression d'un étudiant
     */
    public function studentProgress($studentId)
    {
        $organization = Auth::user()->organization;

        if (!$organization) {
            abort(403, 'Aucune organisation associée à ce compte');
        }

        // Utiliser le système unifié
        $student = \Modules\LMS\Models\User::where('id', $studentId)
            ->where('organization_id', $organization->id)
            ->where('userable_type', 'Modules\LMS\Models\Auth\Student')
            ->with(['userable'])
            ->firstOrFail();

        // Récupérer les cours auxquels l'étudiant est inscrit
        $enrolledCourses = $student->enrollments()
            ->with('course')
            ->get();

        // Récupérer la progression détaillée pour chaque cours
        $progress = [];
        foreach ($enrolledCourses as $enrollment) {
            $course = $enrollment->course;
            if ($course) {
                // Récupérer la progression des chapitres
                $chapterProgress = \Modules\LMS\Models\ChapterProgress::getCourseProgress($studentId, $course->id);

                // Récupérer la progression des leçons (topics)
                $topicProgress = \Modules\LMS\Models\TopicProgress::where('user_id', $studentId)
                    ->where('course_id', $course->id)
                    ->get();

                $completedTopics = $topicProgress->where('status', 'completed')->count();
                $totalTopics = \Modules\LMS\Models\Courses\Topic::whereHas('chapter', function($query) use ($course) {
                    $query->where('course_id', $course->id);
                })->count();

                $topicCompletionPercentage = $totalTopics > 0 ? round(($completedTopics / $totalTopics) * 100, 2) : 0;

                // Récupérer les détails de progression des chapitres depuis la table chapter_progress
                $chapterProgressDetails = \Modules\LMS\Models\ChapterProgress::where('user_id', $studentId)
                    ->where('course_id', $course->id)
                    ->with('chapter')
                    ->get();

                // Calculer le temps total passé sur le cours
                $totalTimeSpent = $chapterProgressDetails->sum('time_spent');
                $totalTimeSpentFormatted = \App\Helpers\TimeHelper::formatTimeSpent($totalTimeSpent);

                // Récupérer les chapitres avec leur statut détaillé
                $chaptersWithProgress = \Modules\LMS\Models\Courses\Chapter::where('course_id', $course->id)
                    ->with(['progress' => function($query) use ($studentId) {
                        $query->where('user_id', $studentId);
                    }])
                    ->orderBy('order')
                    ->get();

                $progress[] = [
                    'course' => $course,
                    'enrollment' => $enrollment,
                    'chapter_progress' => $chapterProgress,
                    'chapter_progress_details' => $chapterProgressDetails,
                    'chapters_with_progress' => $chaptersWithProgress,
                    'topic_progress' => $topicProgress,
                    'topic_completion_percentage' => $topicCompletionPercentage,
                    'total_topics' => $totalTopics,
                    'completed_topics' => $completedTopics,
                    'total_time_spent' => $totalTimeSpent,
                    'total_time_spent_formatted' => $totalTimeSpentFormatted,
                ];
            }
        }

        return view('portal::organization.student.progress', compact('student', 'progress'));
    }

    /**
     * Exporter les étudiants
     */
    public function exportStudents()
    {
        $organization = Auth::user()->organization;

        if (!$organization) {
            abort(403, 'Aucune organisation associée à ce compte');
        }

        return Excel::download(
            new \Modules\LMS\Exports\OrganizationStudentsExport($organization),
            "etudiants-{$organization->name}-" . now()->format('Y-m-d') . '.xlsx'
        );
    }

}
