<?php

namespace App\Http\Controllers;

use App\Models\Webinar;
use App\Models\WebinarRegistration;
use App\Models\WebinarPlatformIntegration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class WebinarController extends Controller
{
    public function index(Request $request)
    {
        $query = Webinar::with(['instructor', 'category', 'organization'])
            ->published();

        // Filter by platform
        if ($request->has('platform')) {
            $query->byPlatform($request->platform);
        }

        // Filter by status
        if ($request->has('status')) {
            switch ($request->status) {
                case 'upcoming':
                    $query->upcoming();
                    break;
                case 'live':
                    $query->live();
                    break;
                case 'featured':
                    $query->featured();
                    break;
                case 'free':
                    $query->free();
                    break;
                case 'paid':
                    $query->paid();
                    break;
            }
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('instructor', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Sort
        $sortBy = $request->get('sort', 'start_date');
        $sortOrder = $request->get('order', 'asc');

        switch ($sortBy) {
            case 'title':
                $query->orderBy('title', $sortOrder);
                break;
            case 'price':
                $query->orderBy('price', $sortOrder);
                break;
            case 'rating':
                $query->orderBy('rating', $sortOrder);
                break;
            case 'start_date':
            default:
                $query->orderBy('start_date', $sortOrder);
                break;
        }

        $webinars = $query->paginate(12);

        $platforms = WebinarPlatformIntegration::active()->get();

        return view('webinars.index', compact('webinars', 'platforms'));
    }

    public function show(Webinar $webinar)
    {
        // $webinar->increment('views'); // Colonne views n'existe pas

        $webinar->load(['instructor', 'category']);

        $isRegistered = false;
        $registration = null;

        if (Auth::check()) {
            $registration = $webinar->registrations()
                ->where('user_id', Auth::id())
                ->first();
            $isRegistered = $registration !== null;
        }

        $relatedWebinars = Webinar::published()
            ->where('id', '!=', $webinar->id)
            ->where('category_id', $webinar->category_id)
            ->limit(4)
            ->get();

        return view('webinars.show', compact('webinar', 'isRegistered', 'registration', 'relatedWebinars'));
    }

    public function register(Request $request, Webinar $webinar)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour vous inscrire.');
        }

        if (!$webinar->canRegister()) {
            return back()->with('error', 'L\'inscription n\'est pas disponible pour ce webinaire.');
        }

        if ($webinar->isUserRegistered(Auth::id())) {
            return back()->with('error', 'Vous êtes déjà inscrit à ce webinaire.');
        }

        try {
            $paymentData = [];

            if (!$webinar->is_free) {
                // Handle payment logic here
                $paymentData = [
                    'payment_method' => $request->payment_method,
                    'payment_reference' => $request->payment_reference,
                    'payment_status' => 'pending'
                ];
            }

            $registration = $webinar->registerUser(Auth::id(), $paymentData);

            // Create platform meeting if needed
            if ($webinar->platform !== 'custom') {
                $integration = $webinar->getPlatformIntegration();
                if ($integration) {
                    $meetingData = $integration->createMeeting($webinar);
                    $webinar->update([
                        'meeting_id' => $meetingData['meeting_id'],
                        'join_url' => $meetingData['join_url'],
                        'meeting_password' => $meetingData['password'] ?? null
                    ]);
                }
            }

            return back()->with('success', 'Inscription réussie ! Vous recevrez un email de confirmation.');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de l\'inscription : ' . $e->getMessage());
        }
    }

    public function unregister(Webinar $webinar)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        try {
            $webinar->unregisterUser(Auth::id());
            return back()->with('success', 'Désinscription réussie.');
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la désinscription : ' . $e->getMessage());
        }
    }

    public function join(Webinar $webinar, $token = null)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $registration = $webinar->registrations()
            ->where('user_id', Auth::id())
            ->first();

        if (!$registration) {
            return back()->with('error', 'Vous n\'êtes pas inscrit à ce webinaire.');
        }

        if ($token && $registration->registration_token !== $token) {
            return back()->with('error', 'Token d\'inscription invalide.');
        }

        // Check if webinar is live or about to start
        if (!$webinar->isCurrentlyLive && $webinar->start_date > now()->addMinutes(15)) {
            return back()->with('error', 'Le webinaire n\'a pas encore commencé.');
        }

        if ($webinar->end_date < now()) {
            return back()->with('error', 'Le webinaire est terminé.');
        }

        // Update attendance
        $webinar->updateAttendance(Auth::id(), now());

        // Redirect to platform
        if ($webinar->join_url) {
            return redirect($webinar->join_url);
        }

        return back()->with('error', 'Lien de participation non disponible.');
    }

    public function myWebinars()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $registrations = WebinarRegistration::with(['webinar.instructor', 'webinar.category'])
            ->where('user_id', Auth::id())
            ->orderBy('registered_at', 'desc')
            ->paginate(10);

        return view('webinars.my-webinars', compact('registrations'));
    }

    public function feedback(Request $request, Webinar $webinar)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $registration = $webinar->registrations()
            ->where('user_id', Auth::id())
            ->first();

        if (!$registration) {
            return back()->with('error', 'Vous n\'êtes pas inscrit à ce webinaire.');
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string|max:1000',
            'suggestions' => 'nullable|string|max:1000'
        ]);

        $registration->submitFeedback(
            $request->rating,
            $request->feedback,
            $request->suggestions
        );

        return back()->with('success', 'Merci pour votre retour !');
    }

    public function calendar()
    {
        $webinars = Webinar::published()
            ->upcoming()
            ->with(['instructor', 'category'])
            ->get();

        $events = $webinars->map(function($webinar) {
            return [
                'id' => $webinar->id,
                'title' => $webinar->title,
                'start' => $webinar->start_date->toISOString(),
                'end' => $webinar->end_date->toISOString(),
                'url' => route('webinar.detail', $webinar->slug),
                'color' => $webinar->is_free ? '#28a745' : '#007bff',
                'extendedProps' => [
                    'instructor' => $webinar->instructor->name ?? 'N/A',
                    'platform' => $webinar->platform,
                    'price' => $webinar->formatted_price
                ]
            ];
        });

        return response()->json($events);
    }

    public function platformWebhook(Request $request, $platform)
    {
        $integration = WebinarPlatformIntegration::where('platform', $platform)
            ->where('webhook_enabled', true)
            ->first();

        if (!$integration) {
            return response()->json(['error' => 'Integration not found'], 404);
        }

        try {
            $integration->handleWebhook($request->all());
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
