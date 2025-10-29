<?php

namespace Modules\LMS\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Modules\LMS\Enums\TopicTypes;
use App\Http\Controllers\Controller;
use Modules\LMS\Repositories\Courses\CourseRepository;
use Modules\LMS\Repositories\Purchase\PurchaseRepository;
use Modules\LMS\Repositories\Courses\Topics\TopicRepository;

class  CourseController extends Controller
{
    public function __construct(protected CourseRepository $course) {}

    /**
     * Display a listing of the resource.
     */
    public function courseList(Request $request)
    {
        $courses = $this->course->courseList($request);

        if ($request->ajax()) {
            $result = view('theme::course.ajax-course-list', compact('courses'))->render();
            return response()->json(
                [
                    'status' => true,
                    'data' => $result,
                    'total' => $courses->total(),
                    'first_item' => $courses->firstItem(),
                    'last_item' => $courses->lastItem(),
                ]
            );
        }

        return view('theme::course.course-list', compact('courses'));
    }

    /**
     * courseDetail
     *
     * @param  string  $slug
     */
    public function courseDetail($slug)
    {
        $course = $this->course->courseDetail($slug);
        $hasPurchase =  $course->hasUserPurchased(user: null);
        $request = Request()->merge([
            'course_id' => $course->id,
            'categories' => $course->category_id
        ]);
        $relatedCourses =  $this->course->courseList($request);
        return view('theme::course.course-detail', compact('course', 'relatedCourses', 'hasPurchase'));
    }

    /**
     * courseBundleDetail
     *
     * @param  string  $slug
     */
    public function courseBundleDetail($slug)
    {
        $bundle = $this->course->courseBundleDetail($slug);
        return view('theme::course.bundle', compact('bundles'));
    }
    /**
     * courseVideoPlayer
     *
     * @param  string  $slug
     */
    public function courseVideoPlayer($slug, Request $request)
    {
        \Log::info('🎬 [courseVideoPlayer] Accès demandé', [
            'slug' => $slug,
            'user_guard' => auth()->check() ? auth()->user()->guard : 'guest',
            'isAdmin' => isAdmin(),
            'isInstructor' => isInstructor(),
            'isStudent' => isStudent()
        ]);
        
        $course = $this->course->courseDetail($slug);
        
        // ✅ ACCÈS LIBRE POUR ADMIN ET INSTRUCTEUR
        if (isAdmin() || isInstructor()) {
            \Log::info('✅ [courseVideoPlayer] Admin/Instructeur détecté - Accès libre');
            
            $data = [
                'type' => $request->type ?? null,
                'topic_id' => $request->topic_id ?? null,
                'chapter_id' => $request->chapter_id ?? null,
            ];
            
            $assignments = TopicRepository::getTopicByCourse($course->id,  TopicTypes::ASSIGNMENT);
            
            return view('theme::course.course-video', compact('course', 'assignments', 'data'));
        }
        
        // ✅ VÉRIFICATION D'ACCÈS POUR LES STUDENTS
        $purchaseDetails = \Modules\LMS\Models\Purchase\PurchaseDetails::where('user_id', authCheck()->id)
            ->where('course_id', $course->id)
            ->where('type', 'enrolled')
            ->first();

        $data = [
            'type' => $request->type ?? null,
            'topic_id' => $request->topic_id ?? null,
            'chapter_id' => $request->chapter_id ?? null,
        ];

        $assignments = TopicRepository::getTopicByCourse($course->id,  TopicTypes::ASSIGNMENT);

        if (isStudent()) {
            if (!$purchaseDetails) {
                // Vérifier si l'étudiant a obtenu un certificat pour ce cours
                $hasCertificate = \Modules\LMS\Models\Certificate\UserCertificate::where('user_id', authCheck()->id)
                    ->where('course_id', $course->id)
                    ->where('type', 'course')
                    ->exists();
                
                if ($hasCertificate) {
                    return redirect()->route('student.dashboard')
                        ->with('warning', 'Vous avez déjà obtenu le certificat pour ce cours. Contactez un administrateur pour une réinscription si nécessaire.');
                }
                
                return redirect()->back()->with('error', 'Vous n\'êtes pas inscrit à ce cours. Veuillez l\'acheter ou demander un accès.');
            }
        }
        
        return view('theme::course.course-video', compact('course', 'assignments', 'data'));
    }
    /**
     *  leanCourseTopic
     */
    public function leanCourseTopic(Request $request)
    {
        return $this->course->getCourseTopicByType($request);
    }
    /**
     * Review
     */
    public function review(Request $request)
    {
        return $this->course->review($request);
    }
}
