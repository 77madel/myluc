<?php

namespace Modules\LMS\Http\Controllers\Student;

use Modules\LMS\Enums\ExamType;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Modules\LMS\Models\Purchase\Purchase;
use Modules\LMS\Repositories\Auth\UserRepository;
use Modules\LMS\Models\Certificate\UserCertificate;
use Modules\LMS\Repositories\Student\StudentRepository;
use Modules\LMS\Repositories\Certificate\CertificateRepository;


class StudentController extends Controller
{
    public function __construct(
        protected StudentRepository $student,
        protected CertificateRepository $certificate
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function dashboard()
    {
        $data = $this->student->dashboardReport();
        $notifications = Auth::user()->unreadNotifications;
        return view('portal::student.index', compact('data', 'notifications'));
    }

    /**
     *  logout
     */
    public function logout()
    {
        Auth::logout();
        Session::flush();
        return redirect('/');
    }

    /**
     * certificate
     */
    public function certificate()
    {
        $certificates = UserCertificate::where('user_id', authCheck()->id)->get();
        return view('portal::student.certificate.index', compact('certificates'));
    }

    /**
     *  notification
     */
    public function notification()
    {
        return view('portal::student.notification.index');
    }

    /**
     *  allCourse
     */
    public function allCourse()
    {
        $enrollments = $this->student->courseEnrolled(10);
        return view('portal::student.course.index', compact('enrollments'));
    }

    /**
     * purchaseCourse
     */
    public function purchaseCourse()
    {
        $purchases = $this->student->purchaseCourses();
        return view('portal::student.course.purchase', compact('purchases'));
    }

    /**
     * purchaseCourse
     */
    public function bundleCourse()
    {
        $bundlesPurchases = $this->student->bundlePurchases();
        return view('portal::student.course.bundle', compact('bundlesPurchases'));
    }

    public function quizResult()
    {
        $userQuizzes = $this->student->getUserExamType(type: ExamType::QUIZ);
        return view('portal::student.quiz.quiz-result-list', compact('userQuizzes'));
    }

    /**
     * Afficher les détails d'un quiz spécifique
     */
    public function quizDetails($userQuizId)
    {
        try {
            $userQuiz = \Modules\LMS\Models\Auth\UserCourseExam::with([
                'quiz.questions.questionAnswers.answer',
                'quiz.questions.question',
                'course'
            ])->where('id', $userQuizId)
              ->where('user_id', authCheck()->id)
              ->first();

            if (!$userQuiz) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quiz non trouvé'
                ]);
            }

            // Récupérer les réponses de l'utilisateur
            $takeAnswers = \Modules\LMS\Models\TakeAnswer::where('user_course_exam_id', $userQuizId)
                ->with(['quizQuestion.question'])
                ->get()
                ->keyBy('quiz_question_id');

            // Récupérer les scores des questions
            $questionScores = \Modules\LMS\Models\QuestionScore::where('quiz_id', $userQuiz->quiz_id)
                ->get()
                ->keyBy('question_id');

            $html = view('portal::student.quiz.quiz-details', compact('userQuiz', 'takeAnswers', 'questionScores'))->render();

            return response()->json([
                'success' => true,
                'html' => $html
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in quizDetails: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des détails'
            ]);
        }
    }

    public function assignmentList()
    {

        $assignments = $this->student->getUserExamType(type: ExamType::ASSIGNMENT);
        return view('portal::student.assignment.index', compact('assignments'));
    }

    public function certificateGenerate($id)
    {
        $this->certificate->requestCertificate($id);
        return  back();
    }

    public function certificateView($id)
    {
        $certificate = UserCertificate::where(['user_id' => authCheck()->id, 'id' => $id])->first();

        if (!$certificate) {
            abort(404, 'Certificat non trouvé');
        }

        // Récupérer l'utilisateur et l'instructeur
        $user = authCheck();
        $instructor_name = 'Instructeur';

        // Essayer de récupérer l'instructeur du cours
        \Log::info('Debug certificat - quiz_id:', ['quiz_id' => $certificate->quiz_id]);

        if ($certificate->quiz_id) {
            try {
                $quiz = \Modules\LMS\Models\Courses\Topics\Quiz::find($certificate->quiz_id);
                \Log::info('Debug certificat - quiz trouvé:', ['quiz' => $quiz ? 'oui' : 'non']);

                if ($quiz && $quiz->chapter && $quiz->chapter->course) {
                    $course = $quiz->chapter->course;
                    \Log::info('Debug certificat - cours trouvé:', ['course_id' => $course->id, 'course_title' => $course->title]);

                    $instructor = $course->instructors()->first();
                    \Log::info('Debug certificat - instructeur trouvé:', ['instructor' => $instructor ? 'oui' : 'non']);

                    if ($instructor && $instructor->userable) {
                        $instructor_name = ($instructor->userable->first_name ?? '') . ' ' . ($instructor->userable->last_name ?? '');
                        \Log::info('Debug certificat - nom instructeur:', ['instructor_name' => $instructor_name]);
                    }
                }
            } catch (\Exception $e) {
                \Log::warning('Erreur lors de la récupération de l\'instructeur', ['error' => $e->getMessage()]);
            }
        }

        return view('portal::certificate.download', compact('certificate', 'user', 'instructor_name'));
    }

    public function certificateDownload($id)
    {
        $certificate = UserCertificate::where(['user_id' => authCheck()->id, 'id' => $id])->first();

        if (!$certificate) {
            abort(404, 'Certificat non trouvé');
        }

        // Vérifier si le certificat a déjà été téléchargé
        if ($certificate->isDownloaded()) {
            return view('portal::certificate.already-downloaded', compact('certificate'));
        }

        // Marquer le certificat comme téléchargé
        $certificate->markAsDownloaded();

        // Générer le nom du fichier
        $fileName = 'Certificat_' . $certificate->certificate_id . '_' . now()->format('Y-m-d') . '.html';

        // Retourner le fichier en téléchargement
        return response($certificate->certificate_content)
            ->header('Content-Type', 'text/html; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }


    public function wishlists()
    {
        $response =  UserRepository::wishlist();
        $wishlists = $response['data'] ?? [];
        return view('portal::student.wishlist.index', compact('wishlists'));
    }


    public function removeWishlist($id)
    {
        $response =  UserRepository::removeWishlist($id);
        $response['url'] = route('student.wishlist');
        return  response()->json($response);
    }

    public function offlinePayment()
    {
        $offlinePayments = Purchase::with('user', 'paymentDocument')->where('payment_method', 'offline')
            ->where(['user_id' => authCheck()->id, 'type' => 'purchase'])
            ->orderBy('id', 'DESC')
            ->paginate(15);
        return view('portal::student.payment.offline.index', compact('offlinePayments'));
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back();
    }
}
