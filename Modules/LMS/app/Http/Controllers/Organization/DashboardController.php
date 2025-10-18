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

        return view('portal::organization.student.student-list', compact('students'));
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
            ->with(['userable', 'enrollments.course'])
            ->firstOrFail();

        // Récupérer la progression via enrollments
        $progress = $student->enrollments()
            ->with('course')
            ->latest()
            ->get();

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
