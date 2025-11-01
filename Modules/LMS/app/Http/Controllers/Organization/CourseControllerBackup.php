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
use Paydunya\Checkout\Store;
use Paydunya\Checkout\Invoice;
use Paydunya\Checkout\Payline;
use Paydunya\Checkout\Product;

class CourseControllerBackup extends Controller
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
            // Vérifier la configuration Paydunya
            $masterKey = config('paydunya.master_key');
            $privateKey = config('paydunya.private_key');
            $token = config('paydunya.token');
            
            if (empty($masterKey) || empty($privateKey) || empty($token)) {
                return back()->with('error', 'Configuration Paydunya incomplète. Veuillez vérifier vos clés API dans le fichier .env');
            }

            // Configuration Paydunya
            Store::setName($organization->name);
            Store::setTagline($organization->name . ' - Formation');
            Store::setPhoneNumber($organization->phone ?? '+22300000000');
            Store::setPostalAddress($organization->address ?? 'Adresse non définie');
            Store::setWebsiteUrl(url('/'));
            Store::setLogoUrl(asset('lms/assets/images/logo.png'));

            // Créer la facture
            $invoice = new Invoice();
            $invoice->addItem($course->title, 1, $coursePrice->price, $coursePrice->price);
            $invoice->setTotalAmount($coursePrice->price);
            $invoice->setDescription("Achat du cours: {$course->title}");
            $invoice->setReturnUrl(route('organization.courses.purchase.success', $course));
            $invoice->setCancelUrl(route('organization.courses.purchase.cancel', $course));
            $invoice->setCallbackUrl(route('organization.courses.purchase.callback', $course));

            // Sauvegarder les données de la transaction
            session([
                'purchase_data' => [
                    'course_id' => $course->id,
                    'organization_id' => $organization->id,
                    'amount' => $coursePrice->price,
                    'course_title' => $course->title
                ]
            ]);

            if ($invoice->create()) {
                return redirect($invoice->getInvoiceUrl());
            } else {
                return back()->with('error', 'Erreur lors de la création de la facture Paydunya. Vérifiez vos clés API.');
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
