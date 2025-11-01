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
use Modules\LMS\Services\Payment\PaydunyaService;

class CourseController extends Controller
{
    protected $enrollmentService;

    public function __construct(OrganizationEnrollmentService $enrollmentService)
    {
        $this->enrollmentService = $enrollmentService;
    }

    /**
     * Afficher la liste des cours de l'organisation (achetés)
     */
    public function index()
    {
        $organization = Auth::user()->organization;
        if (! $organization) {
            abort(403);
        }

        $userId = Auth::id();
        $courseIds = \DB::table('purchase_details')
            ->whereNotNull('course_id')
            ->where(function($q) use ($organization, $userId) {
                $q->where('organization_id', $organization->id)
                  ->orWhere('user_id', $userId);
            })
            ->distinct()
            ->pluck('course_id');

        $courses = Course::whereIn('id', $courseIds)
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

    // myCourses supprimé: l'index affiche désormais les cours achetés

    /**
     * Traiter l'achat d'un cours avec Paydunya
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
            // Préparer les données pour Paydunya
            session([
                'type' => 'course_purchase',
                'course_id' => $course->id,
                'organization_id' => $organization->id,
                'amount' => $coursePrice->price,
                'course_title' => $course->title
            ]);

            // Utiliser le service Paydunya existant
            $paymentResult = PaydunyaService::makePayment();

            if ($paymentResult['status'] === 'success') {
                // Rediriger vers Paydunya
                return redirect($paymentResult['checkout_url']);
            } else {
                return back()->with('error', 'Erreur Paydunya: ' . ($paymentResult['message'] ?? 'Erreur inconnue'));
            }

        } catch (\Exception $e) {
            \Log::error('Paydunya Error: ' . $e->getMessage());
            return back()->with('error', 'Erreur lors du traitement du paiement: ' . $e->getMessage());
        }
    }

    /**
     * Succès du paiement
     */
    public function purchaseSuccess(Request $request, Course $course)
    {
        // Récupérer les données depuis la session
        $amount = session('amount');
        $organizationId = session('organization_id');
        $courseId = session('course_id');
        
        if (!$amount || !$organizationId || !$courseId) {
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

            // Enregistrer l'achat dans notre table dédiée aux organisations
            $purchase = \Modules\LMS\Models\Auth\OrganizationCoursePurchase::create([
                'organization_id' => $organization->id,
                'course_id' => $course->id,
                'amount' => $amount,
                'purchase_date' => now(),
                'enrollment_link_id' => $enrollmentLink->id,
                'status' => 'completed',
            ]);
            
            echo "✅ Achat enregistré avec l'ID: " . $purchase->id . "\n";

            DB::commit();

            // Nettoyer la session
            session()->forget(['amount', 'organization_id', 'course_id', 'course_title']);

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
        // Nettoyer la session
        session()->forget(['amount', 'organization_id', 'course_id', 'course_title']);
        
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
