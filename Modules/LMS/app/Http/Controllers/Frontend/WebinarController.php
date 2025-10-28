<?php

namespace Modules\LMS\Http\Controllers\Frontend;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Modules\LMS\Models\User;
use Illuminate\Http\Response;
use Modules\LMS\Models\Webinar;
use Modules\LMS\Models\Category;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\WebinarRegistration;

class WebinarController extends Controller
{
    /**
     * Show the form for creating a new webinar.
     */
    public function create()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour créer un webinaire.');
        }

        $categories = Category::where('status', 1)->get();
        $instructors = User::where('guard', 'instructor')->get();

        return view('lms::frontend.webinars.create', compact('categories', 'instructors'));
    }

    /**
     * Store a newly created webinar.
     */
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour créer un webinaire.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'platform' => 'required|in:zoom,teams,google_meet,custom',
            'max_participants' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'is_free' => 'boolean',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        try {
            $webinar = Webinar::create([
                'title' => $request->title,
                'description' => $request->description,
                'short_description' => $request->short_description,
                'slug' => Str::slug($request->title),
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'platform' => $request->platform,
                'max_participants' => $request->max_participants ?? 100,
                'price' => $request->price ?? 0,
                'is_free' => $request->boolean('is_free'),
                'instructor_id' => Auth::id(),
                'category_id' => $request->category_id,
                'status' => 'draft',
            ]);

            return redirect()->route('webinar.detail', $webinar->slug)
                ->with('success', 'Webinaire créé avec succès !');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la création du webinaire : ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display a listing of webinars.
     */
    public function index(Request $request)
    {
        $query = Webinar::published()->with(['instructor', 'category']);

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            switch ($request->status) {
                case 'upcoming':
                    $query->upcoming();
                    break;
                case 'live':
                    $query->live();
                    break;
                case 'completed':
                    $query->completed();
                    break;
            }
        }

        // Filter by price
        if ($request->has('price') && $request->price) {
            if ($request->price === 'free') {
                $query->where('is_free', true);
            } elseif ($request->price === 'paid') {
                $query->where('is_free', false);
            }
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $webinars = $query->orderBy('start_date', 'asc')->paginate(12);
        $categories = Category::where('status', 1)->get();

        return view('lms::frontend.webinars.index_public', compact('webinars', 'categories'));
    }

    /**
     * Display the specified webinar.
     */
    public function show($slug)
    {
        $webinar = Webinar::published()
            ->with(['instructor', 'category', 'registrations'])
            ->where('slug', $slug)
            ->firstOrFail();

        $isEnrolled = false;
        $enrollment = null;

        if (Auth::check()) {
            $enrollment = WebinarRegistration::where('webinar_id', $webinar->id)
                ->where('user_id', Auth::id())
                ->first();
            $isEnrolled = $enrollment && $enrollment->status === 'confirmed';
        }

        // Related webinars
        $relatedWebinars = Webinar::published()
            ->where('id', '!=', $webinar->id)
            ->where('category_id', $webinar->category_id)
            ->limit(4)
            ->get();

        return view('lms::frontend.webinars.show_public', compact('webinar', 'isEnrolled', 'enrollment', 'relatedWebinars'))->with('isRegistered', $isEnrolled);
    }

    /**
     * Enroll user in webinar.
     */
    public function enroll(Request $request, $id)
    {
        $webinar = Webinar::published()->findOrFail($id);

        if (!$webinar->isAvailableForEnrollment()) {
            return redirect()->back()->with('error', 'Ce webinaire n\'est pas disponible pour l\'inscription.');
        }

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour vous inscrire.');
        }

        // Check if user is already enrolled
        $existingEnrollment = WebinarRegistration::where('webinar_id', $webinar->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($existingEnrollment && $existingEnrollment->status === 'confirmed') {
            return redirect()->back()->with('warning', 'Vous êtes déjà inscrit à ce webinaire.');
        }

        try {
            DB::beginTransaction();

            // Create enrollment
            WebinarRegistration::create([
                'webinar_id' => $webinar->id,
                'user_id' => Auth::id(),
                'registration_token' => \Illuminate\Support\Str::random(32),
                'status' => 'confirmed',
                'registered_at' => now(),
            ]);

            // Update participant count
            $webinar->increment('current_participants');

            DB::commit();

            return redirect()->back()->with('success', 'Inscription réussie! Vous recevrez un email de confirmation.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Webinar enrollment failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue lors de l\'inscription.');
        }
    }

    /**
     * Cancel enrollment.
     */
    public function cancelEnrollment($id)
    {
        $webinar = Webinar::findOrFail($id);

        $enrollment = WebinarRegistration::where('webinar_id', $webinar->id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$enrollment) {
            return redirect()->back()->with('error', 'Aucune inscription trouvée.');
        }

        try {
            DB::beginTransaction();

            // Update enrollment status to cancelled
            $enrollment->update(['status' => 'cancelled']);
            $webinar->decrement('current_participants');

            DB::commit();

            return redirect()->back()->with('success', 'Inscription annulée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Webinar enrollment cancellation failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue lors de l\'annulation.');
        }
    }

    /**
     * Get user's enrolled webinars.
     */
    public function myWebinars()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $enrollments = WebinarRegistration::with(['webinar.instructor', 'webinar.category'])
            ->where('user_id', Auth::id())
            ->whereIn('status', ['confirmed', 'attended'])
            ->orderBy('registered_at', 'desc')
            ->paginate(10);

        return view('lms::frontend.webinars.my-webinars', compact('enrollments'));
    }

    /**
     * Join live webinar.
     */
    public function join($id)
    {
        $webinar = Webinar::findOrFail($id);

        if (!$webinar->isCurrentlyLive()) {
            return redirect()->back()->with('error', 'Ce webinaire n\'est pas en cours.');
        }

        $enrollment = WebinarRegistration::where('webinar_id', $webinar->id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$enrollment || !$enrollment->isActive()) {
            return redirect()->back()->with('error', 'Vous n\'êtes pas inscrit à ce webinaire.');
        }

        return view('lms::frontend.webinars.join', compact('webinar'));
    }

    /**
     * Mark attendance.
     */
    public function markAttendance($id)
    {
        $webinar = Webinar::findOrFail($id);

        $enrollment = WebinarRegistration::where('webinar_id', $webinar->id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$enrollment) {
            return response()->json([
                'success' => false,
                'message' => 'Inscription non trouvée.'
            ], 400);
        }

        $enrollment->markAsAttended();

        return response()->json([
            'success' => true,
            'message' => 'Présence marquée avec succès.'
        ]);
    }
}

