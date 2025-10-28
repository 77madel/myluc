<?php

namespace Modules\LMS\Http\Controllers\Organization;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\LMS\Models\Courses\Course;
use Modules\LMS\Models\Auth\Organization;
use Modules\LMS\Models\Auth\OrganizationEnrollmentLink;
use Modules\LMS\Services\OrganizationEnrollmentService;

class CourseControllerTest extends Controller
{
    protected $enrollmentService;

    public function __construct(OrganizationEnrollmentService $enrollmentService)
    {
        $this->enrollmentService = $enrollmentService;
    }

    /**
     * Afficher la liste des cours disponibles pour achat
     */
    public function index()
    {
        $courses = Course::where('status', 'active')
            ->with(['coursePrice', 'instructors.userable'])
            ->paginate(12);

        return view('portal::organization.courses.index', compact('courses'));
    }

    /**
     * Afficher les détails d'un cours
     */
    public function show(Course $course)
    {
        $course->load(['coursePrice', 'instructors.userable', 'chapters', 'category']);
        
        return view('portal::organization.courses.show', compact('course'));
    }

    /**
     * Simuler l'achat d'un cours (VERSION TEST - SANS PAYDUNYA)
     */
    public function purchase(Request $request, Course $course)
    {
        $organization = Auth::user()->organization;
        
        if (!$organization) {
            return back()->with('error', 'Organisation non trouvée');
        }

        $coursePrice = $course->coursePrice;
        if (!$coursePrice || $coursePrice->price <= 0) {
            return back()->with('error', 'Ce cours n\'est pas disponible à l\'achat');
        }

        try {
            // SIMULATION D'ACHAT (pour test uniquement)
            // Sauvegarder les données de la transaction
            session([
                'purchase_data' => [
                    'course_id' => $course->id,
                    'organization_id' => $organization->id,
                    'amount' => $coursePrice->price,
                    'course_title' => $course->title
                ]
            ]);

            // Rediriger directement vers le succès (simulation)
            return redirect()->route('organization.courses.purchase.success', $course)
                ->with('info', 'Mode test activé - Achat simulé avec succès');

        } catch (\Exception $e) {
            \Log::error('Purchase Error: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors du traitement de l\'achat: ' . $e->getMessage());
        }
    }

    /**
     * Succès du paiement
     */
    public function purchaseSuccess(Request $request, Course $course)
    {
        $purchaseData = session('purchase_data');
        
        if (!$purchaseData) {
            return redirect()->route('organization.courses.index')
                ->with('error', 'Données de transaction non trouvées');
        }

        try {
            DB::beginTransaction();

            $organization = Auth::user()->organization;
            
            // Créer automatiquement un lien d'inscription pour ce cours
            $enrollmentLink = $this->enrollmentService->createEnrollmentLink(
                $organization,
                "Cours: {$course->title}",
                [
                    'description' => "Lien d'inscription pour le cours: {$course->title}",
                    'valid_until' => now()->addYear(), // Valide 1 an
                    'max_enrollments' => null, // Pas de limite
                ]
            );

            // Associer le cours au lien d'inscription
            $enrollmentLink->update([
                'course_id' => $course->id,
                'status' => 'active'
            ]);

            // Enregistrer l'achat dans la base de données
            DB::table('organization_course_purchases')->insert([
                'organization_id' => $organization->id,
                'course_id' => $course->id,
                'amount' => $purchaseData['amount'],
                'purchase_date' => now(),
                'enrollment_link_id' => $enrollmentLink->id,
                'status' => 'completed',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            DB::commit();

            session()->forget('purchase_data');

            return redirect()->route('organization.enrollment-links.index')
                ->with('success', 'Cours acheté avec succès ! Un lien d\'inscription a été généré automatiquement.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('organization.courses.index')
                ->with('error', 'Erreur lors de l\'enregistrement de l\'achat: ' . $e->getMessage());
        }
    }

    /**
     * Annulation du paiement
     */
    public function purchaseCancel(Request $request, Course $course)
    {
        session()->forget('purchase_data');
        
        return redirect()->route('organization.courses.show', $course)
            ->with('error', 'Paiement annulé');
    }

    /**
     * Callback Paydunya (webhook)
     */
    public function purchaseCallback(Request $request, Course $course)
    {
        // Traitement du callback Paydunya
        // Vérifier le statut du paiement et mettre à jour la base de données
        return response()->json(['status' => 'success']);
    }
}











