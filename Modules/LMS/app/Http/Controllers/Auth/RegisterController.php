<?php

namespace Modules\LMS\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\LMS\Repositories\Auth\RegisterRepository;
use Modules\LMS\Models\Auth\OrganizationEnrollmentLink;

class RegisterController extends Controller
{
    public function __construct(protected RegisterRepository $register) {}

    /**
     * Store a newly created resource in storage.
     */
    public function register(Request $request): JsonResponse
    {

        // Détecter automatiquement si c'est une inscription via lien d'organisation
        $this->detectAndHandleOrganizationEnrollment($request);

        // Vérifier si c'est une inscription via lien d'organisation et si l'étudiant existe déjà
        if ($request->has('enrollment_link_id') && $request->has('organization_id')) {
            $existingUser = $this->checkExistingStudent($request);
            if ($existingUser) {
                // L'étudiant existe déjà, l'enroll directement
                return $this->enrollExistingStudent($existingUser, $request);
            }
        }
        $user = $this->register->userRegister($request);
        if ($user['status'] !== 'success') {
            return response()->json($user);
        }
        return response()->json([
            'status' => $user['status'],
            'message' => translate('Thank Your For Register and Please Verify Your Email')
        ]);
    }

    public function registerForm()
    {
        return view('theme::register.register');
    }

    /**
     * Afficher la page de succès après enrollment
     */
    public function enrollmentSuccess($slug)
    {
        $enrollmentLink = OrganizationEnrollmentLink::where('slug', $slug)
            ->with(['organization', 'course'])
            ->first();

        if (!$enrollmentLink) {
            abort(404, 'Lien d\'inscription non trouvé');
        }

        return view('theme::register.enrollment-success', compact('enrollmentLink'));
    }

    /**
     * Détecter et gérer automatiquement l'inscription via lien d'organisation
     */
    private function detectAndHandleOrganizationEnrollment(Request $request)
    {
        // Vérifier si l'URL contient un slug d'organisation (détection automatique)
        $currentUrl = $request->url();
        $path = parse_url($currentUrl, PHP_URL_PATH);

        // Détecter le pattern /enroll/{slug}
        if (preg_match('/\/enroll\/([a-zA-Z0-9]+)/', $path, $matches)) {
            $slug = $matches[1];

            // Récupérer le lien d'inscription
            $enrollmentLink = OrganizationEnrollmentLink::where('slug', $slug)
                ->where('status', 'active')
                ->first();

            if ($enrollmentLink && $enrollmentLink->isValid()) {
                // Ajouter automatiquement les informations d'organisation
                $request->merge([
                    'organization_id' => $enrollmentLink->organization_id,
                    'enrollment_link_id' => $enrollmentLink->id,
                ]);
            }
        }
    }

    /**
     * Vérifier si l'étudiant existe déjà dans la base
     */
    private function checkExistingStudent(Request $request)
    {
        $email = $request->email;

        // Chercher un utilisateur avec cet email et type student
        $user = \Modules\LMS\Models\User::where('email', $email)
            ->where('userable_type', 'Modules\LMS\Models\Auth\Student')
            ->with('userable')
            ->first();

        return $user;
    }

    /**
     * Enroll un étudiant existant directement au cours
     */
    private function enrollExistingStudent($user, Request $request)
    {
        try {
            // Récupérer le lien d'inscription
            $enrollmentLink = OrganizationEnrollmentLink::find($request->enrollment_link_id);

            if (!$enrollmentLink || !$enrollmentLink->course_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Lien d\'inscription invalide'
                ]);
            }

            // Vérifier si l'étudiant est déjà enrollé dans ce cours
            $existingEnrollment = \Modules\LMS\Models\Purchase\PurchaseDetails::where('user_id', $user->id)
                ->where('course_id', $enrollmentLink->course_id)
                ->where('type', 'enrolled')
                ->first();

            if ($existingEnrollment) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Vous êtes déjà inscrit à ce cours',
                    'redirect' => route('organization.enrollment.success', $enrollmentLink->slug)
                ]);
            }

            // Enroll l'étudiant au cours via PurchaseDetails
            $course = \Modules\LMS\Models\Courses\Course::find($enrollmentLink->course_id);
            if ($course) {
                // Créer une entrée dans PurchaseDetails pour que l'étudiant puisse voir ses cours
                \Modules\LMS\Models\Purchase\PurchaseDetails::create([
                    'purchase_number' => 'ORG-' . time() . '-' . $user->id,
                    'purchase_id' => 0, // Pas de purchase_id pour les enrollments d'organisation
                    'course_id' => $course->id,
                    'user_id' => $user->id,
                    'platform_fee' => 0,
                    'price' => 0, // Gratuit pour les enrollments d'organisation
                    'discount_price' => 0,
                    'details' => json_encode([
                        'enrollment_type' => 'organization',
                        'enrollment_link_id' => $enrollmentLink->id,
                        'organization_id' => $enrollmentLink->organization_id,
                        'course_title' => $course->title,
                        'enrollment_date' => now()->toISOString()
                    ]),
                    'type' => \Modules\LMS\Enums\PurchaseType::ENROLLED, // Utiliser l'enum au lieu de string
                    'purchase_type' => 'course',
                    'status' => 'completed',
                    'organization_id' => $enrollmentLink->organization_id,
                    'enrollment_link_id' => $enrollmentLink->id,
                ]);
            }

            // Mettre à jour l'organization_id de l'utilisateur s'il n'en a pas
            if (!$user->organization_id) {
                $user->update(['organization_id' => $request->organization_id]);
            }

            // Incrémenter le nombre d'inscriptions du lien
            $enrollmentLink->incrementEnrollments();

            return response()->json([
                'status' => 'success',
                'message' => 'Vous avez été inscrit au cours avec succès',
                'redirect' => route('organization.enrollment.success', $enrollmentLink->slug)
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'enrollment de l\'étudiant existant: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de l\'inscription au cours'
            ]);
        }
    }


}
