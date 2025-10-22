<?php
/*
namespace Modules\LMS\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Modules\LMS\Repositories\Auth\UserRepository;
use Modules\LMS\Repositories\SearchSuggestionRepository;

class DashboardController extends Controller implements HasMiddleware
{
    public function __construct(
        protected SearchSuggestionRepository $suggestion,
        protected UserRepository $user
    ) {}

    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['register']),
        ];
    }


     //Display a listing of the resource.

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


     // View Student Profile.

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
}*/


namespace Modules\LMS\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\OrganizationEnrollmentLink;
use App\Models\OrganizationParticipant;
use App\Models\OrganizationParticipantProgress;
use App\Models\OrganizationActivityLog;
use App\Services\OrganizationEnrollmentService;
use Modules\LMS\Repositories\SearchSuggestionRepository;

class DashboardController extends Controller implements HasMiddleware
{
    public function __construct(
        protected SearchSuggestionRepository    $suggestion,
        protected OrganizationEnrollmentService $enrollmentService
    )
    {
    }

    public static function middleware(): array
    {
        return [
            new Middleware('auth', except: ['register']),
        ];
    }

    /**
     * Tableau de bord principal de l'organisation
     * Remplace le système de création de cours
     */
    public function index()
    {
        $organization = auth()->user()->organization;

        // Liens d'inscription actifs
        $activeLinks = $organization->enrollmentLinks()
            ->where('status', 'active')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Modules assignés
        $modules = $this->enrollmentService->getOrganizationModules($organization);

        // Statistiques globales
        $stats = [
            'total_participants' => $organization->organizationParticipants()->count(),
            'active_participants' => $organization->organizationParticipants()
                ->where('status', 'active')
                ->count(),
            'this_month_enrollments' => $organization->organizationParticipants()
                ->whereMonth('enrolled_at', now()->month)
                ->count(),
            'total_modules' => $modules->count(),
            'average_completion' => $this->calculateAverageCompletion($organization),
            'recent_activity' => OrganizationActivityLog::where('organization_id', $organization->id)
                ->orderByDesc('performed_at')
                ->limit(8)
                ->get()
        ];

        return view('portal::organization.dashboard', compact('organization', 'activeLinks', 'modules', 'stats'));
    }

    /**
     * Gestion des liens d'inscription
     */
    public function enrollmentLinks()
    {
        $organization = auth()->user()->organization;
        $links = $organization->enrollmentLinks()
            ->with('participants')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('portal::organization.enrollment.links.index', compact('links'));
    }

    /**
     * Créer un nouveau lien d'inscription
     */
    public function createLink()
    {
        return view('portal::organization.enrollment.links.create');
    }

    /**
     * Sauvegarder le lien d'inscription
     */
    public function storeLink(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'valid_until' => 'nullable|date|after:today',
            'max_enrollments' => 'nullable|integer|min:1'
        ]);

        $organization = auth()->user()->organization;

        $link = $this->enrollmentService->createEnrollmentLink(
            $organization,
            $request->input('name'),
            [
                'description' => $request->input('description'),
                'valid_until' => $request->input('valid_until'),
                'max_enrollments' => $request->input('max_enrollments')
            ]
        );

        return redirect()
            ->route('organization.enrollment-links.show', $link->id)
            ->with('success', 'Lien d\'inscription créé avec succès');
    }

    /**
     * Afficher un lien d'inscription
     */
    public function showLink($linkId)
    {
        $link = OrganizationEnrollmentLink::findOrFail($linkId);
        $this->authorizeOrganization($link->organization);

        // Formater le lien public
        $publicLink = route('public.enrollment.show', $link->slug);

        $participants = $link->participants()
            ->with('user', 'progress')
            ->paginate(15);

        return view('portal::organization.enrollment.links.show', compact('link', 'publicLink', 'participants'));
    }

    /**
     * Gestion des modules assignés
     */
    public function modules()
    {
        $organization = auth()->user()->organization;
        $modules = $this->enrollmentService->getOrganizationModules($organization);

        // Pour la sélection, obtenir tous les cours disponibles
        $availableCourses = \App\Models\Course::where('status', 'published')
            ->orderBy('title')
            ->get();

        $assignedCourseIds = $modules->pluck('moduleable_id');

        return view('portal::organization.modules.index', compact(
            'modules',
            'availableCourses',
            'assignedCourseIds'
        ));
    }

    /**
     * Assigner un module
     */
    public function assignModule(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'order' => 'nullable|integer',
            'is_mandatory' => 'boolean'
        ]);

        $organization = auth()->user()->organization;
        $course = \App\Models\Course::findOrFail($request->input('course_id'));

        // Vérifier que le module n'est pas déjà assigné
        $exists = $organization->organizationModules()
            ->where('moduleable_id', $course->id)
            ->where('moduleable_type', get_class($course))
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Ce cours est déjà assigné à votre organisation'
            ], 422);
        }

        $this->enrollmentService->assignModule($organization, $course, [
            'order' => $request->input('order', 0),
            'is_mandatory' => $request->boolean('is_mandatory', true)
        ]);

        return response()->json(['message' => 'Module assigné avec succès']);
    }

    /**
     * Supprimer une assignation de module
     */
    public function removeModule($moduleId)
    {
        $module = \App\Models\OrganizationModule::findOrFail($moduleId);
        $this->authorizeOrganization($module->organization);

        $module->delete();

        return response()->json(['message' => 'Module supprimé']);
    }

    /**
     * Afficher les participants
     */
    public function participants()
    {
        $organization = auth()->user()->organization;

        $participants = $organization->organizationParticipants()
            ->with('user', 'progress', 'enrollmentLink')
            ->orderByDesc('enrolled_at')
            ->paginate(20);

        return view('portal::organization.participants.index', compact('participants'));
    }

    /**
     * Vue détaillée d'un participant
     */
    public function participantDetail($participantId)
    {
        $participant = OrganizationParticipant::findOrFail($participantId);
        $this->authorizeOrganization($participant->organization);

        $progress = $participant->progress()
            ->with('course')
            ->get()
            ->map(function ($p) {
                return [
                    'course' => $p->course,
                    'completion_percentage' => $p->completion_percentage,
                    'status' => $p->status,
                    'started_at' => $p->started_at,
                    'completed_at' => $p->completed_at,
                    'duration' => $p->completed_at && $p->started_at
                        ? $p->completed_at->diffInDays($p->started_at) . ' jours'
                        : null
                ];
            });

        $activityLogs = OrganizationActivityLog::where('organization_participant_id', $participant->id)
            ->orderByDesc('performed_at')
            ->limit(20)
            ->get();

        return view('portal::organization.participants.detail', compact('participant', 'progress', 'activityLogs'));
    }

    /**
     * Rapport de progression
     */
    public function progressionReport()
    {
        $organization = auth()->user()->organization;
        $report = $this->enrollmentService->getProgressionReport($organization);

        return view('portal::organization.reports.progression', compact('report'));
    }

    /**
     * Tableau de bord statistiques
     */
    public function reportsDashboard()
    {
        $organization = auth()->user()->organization;

        $stats = [
            'total_participants' => $organization->organizationParticipants()->count(),
            'active_participants' => $organization->organizationParticipants()
                ->where('status', 'active')
                ->count(),
            'total_courses' => $organization->organizationModules()
                ->where('moduleable_type', 'App\Models\Course')
                ->count(),
            'this_month_enrollments' => $organization->organizationParticipants()
                ->whereMonth('enrolled_at', now()->month)
                ->count(),
            'completion_rate' => $this->calculateCompletionRate($organization),
            'recent_activity' => OrganizationActivityLog::where('organization_id', $organization->id)
                ->orderByDesc('performed_at')
                ->limit(10)
                ->get(),
            'top_performers' => $this->getTopPerformers($organization, 5),
            'by_department' => $this->getStatsByDepartment($organization)
        ];

        return view('portal::organization.reports.dashboard', compact('stats'));
    }

    /**
     * Logs d'activité avec filtres
     */
    public function activityLogs(Request $request)
    {
        $organization = auth()->user()->organization;

        $query = OrganizationActivityLog::where('organization_id', $organization->id);

        if ($request->filled('participant_id')) {
            $query->where('organization_participant_id', $request->input('participant_id'));
        }

        if ($request->filled('action')) {
            $query->where('action', $request->input('action'));
        }

        if ($request->filled('start_date')) {
            $query->whereDate('performed_at', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('performed_at', '<=', $request->input('end_date'));
        }

        $logs = $query->orderByDesc('performed_at')->paginate(50);

        return view('portal::organization.reports.activity-logs', compact('logs'));
    }

    /**
     * Exporter les participants en Excel
     */
    public function exportParticipants()
    {
        $organization = auth()->user()->organization;

        return Excel::download(
            new \App\Exports\OrganizationParticipantsExport($organization),
            "participants-{$organization->slug}-" . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Exporter l'activité en Excel
     */
    public function exportActivity(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date'
        ]);

        $organization = auth()->user()->organization;

        return Excel::download(
            new \App\Exports\OrganizationActivityExport($organization, $request->all()),
            "activity-{$organization->slug}-" . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Générer un PDF de rapport
     */
    public function generatePdf()
    {
        $organization = auth()->user()->organization;
        $participants = $organization->organizationParticipants()
            ->with('user', 'progress')
            ->get();

        $stats = $this->enrollmentService->getProgressionReport($organization);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('portal::organization.reports.pdf', [
            'organization' => $organization,
            'participants' => $participants,
            'stats' => $stats,
            'generated_at' => now()
        ]);

        return $pdf->download("rapport-{$organization->slug}-" . now()->format('Y-m-d') . '.pdf');
    }

    /**
     * Recherche de suggestions
     */
    public function searchingSuggestion(Request $request)
    {
        $results = $this->suggestion->searchSuggestion($request);
        return response()->json($results);
    }

    /**
     * Déconnexion
     */
    public function logout()
    {
        Auth::logout();
        Session::flush();
        return redirect('/');
    }

    /**
     * Méthodes utilitaires privées
     */

    private function calculateAverageCompletion($organization): float
    {
        $participants = $organization->organizationParticipants()->count();

        if ($participants === 0) {
            return 0;
        }

        $totalCompletion = OrganizationParticipantProgress::whereHas(
            'participant',
            fn($q) => $q->where('organization_id', $organization->id)
        )->sum('completion_percentage');

        return round($totalCompletion / ($participants * 100) * 100, 2);
    }

    private function calculateCompletionRate($organization): float
    {
        $participants = $organization->organizationParticipants()->count();

        if ($participants === 0) {
            return 0;
        }

        $completed = $organization->organizationParticipants()
            ->whereHas('progress', function ($query) {
                $query->where('status', 'completed');
            })
            ->distinct('user_id')
            ->count();

        return round(($completed / $participants) * 100, 2);
    }

    private function getTopPerformers($organization, $limit = 5)
    {
        return $organization->organizationParticipants()
            ->with('user', 'progress')
            ->get()
            ->map(function ($p) {
                return [
                    'participant' => $p,
                    'average_score' => $p->progress()->avg('score') ?? 0,
                    'completion' => $p->progress()->avg('completion_percentage') ?? 0
                ];
            })
            ->sortByDesc('completion')
            ->take($limit)
            ->values();
    }

    private function getStatsByDepartment($organization)
    {
        return $organization->organizationParticipants()
            ->groupBy('department')
            ->selectRaw('department, count(*) as total')
            ->get();
    }

    private function authorizeOrganization($organization)
    {
        if (auth()->user()->organization_id !== $organization->id) {
            abort(403, 'Accès refusé');
        }
    }
}
