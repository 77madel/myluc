<?php

namespace Modules\LMS\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\LMS\Models\Webinar;
use Modules\LMS\Models\WebinarEnrollment;
use Modules\LMS\Models\Category;
use Modules\LMS\Models\User;
use Modules\LMS\Models\Auth\Instructor;
use Illuminate\Support\Facades\Storage;

class WebinarController extends Controller
{
    /**
     * Display a listing of webinars.
     */
    public function index(Request $request)
    {
        $query = Webinar::with(['instructor', 'category']);

        // Filter by status
        if ($request->has('status') && $request->status) {
            switch ($request->status) {
                case 'scheduled':
                    $query->where('status', 'scheduled');
                    break;
                case 'live':
                    $query->live();
                    break;
                case 'completed':
                    $query->completed();
                    break;
                case 'cancelled':
                    $query->where('status', 'cancelled');
                    break;
            }
        }

        // Filter by instructor
        if ($request->has('instructor') && $request->instructor) {
            $query->where('instructor_id', $request->instructor);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $webinars = $query->orderBy('start_date', 'desc')->paginate(15);
        // Récupérer les instructeurs depuis la table instructors avec leur relation user
        $instructors = Instructor::with('user')->get()->map(function ($instructor) {
            if ($instructor->user) {
                $instructor->user->full_name = $instructor->user->first_name . ' ' . $instructor->user->last_name;
            }
            return $instructor;
        });
        $categories = Category::where('status', 1)->get();

        return view('lms::portals.admin.webinars.index', compact('webinars', 'instructors', 'categories'));
    }

    /**
     * Show the form for creating a new webinar.
     */
    public function create()
    {
        // Récupérer les instructeurs depuis la table instructors avec leur relation user
        $instructors = Instructor::with('user')->get()->map(function ($instructor) {
            if ($instructor->user) {
                $instructor->user->full_name = $instructor->user->first_name . ' ' . $instructor->user->last_name;
            }
            return $instructor;
        });
        $categories = Category::where('status', 1)->get();

        return view('lms::portals.admin.webinars.create', compact('instructors', 'categories'));
    }

    /**
     * Store a newly created webinar.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'instructor_id' => 'required|exists:users,id',
            'category_id' => 'nullable|exists:categories,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'meeting_url' => 'nullable|url',
            'max_participants' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'is_free' => 'nullable|boolean',
            'is_recorded' => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
            'meeting_url' => 'nullable|url',
            'meeting_id' => 'nullable|string',
            'meeting_password' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->only([
                'title', 'description', 'short_description', 'instructor_id', 'category_id',
                'start_date', 'end_date', 'max_participants', 'price',
                'is_free', 'is_recorded', 'is_published', 'meeting_url', 'meeting_id', 'meeting_password'
            ]);

            // Handle image upload
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('webinars', 'public');
            }

            // Set default values
            $data['current_participants'] = 0;
            $data['status'] = 'draft';
            $data['slug'] = \Str::slug($data['title']);
            $data['is_free'] = $request->has('is_free') ? 1 : 1; // Default to free
            $data['price'] = $data['is_free'] ? 0 : ($data['price'] ?? 0);
            $data['is_recorded'] = $request->has('is_recorded') ? 1 : 0;
            $data['is_published'] = $request->has('is_published') ? 1 : 0;

            $webinar = Webinar::create($data);

            DB::commit();

            return redirect()->route('webinars.index')
                ->with('success', 'Webinaire créé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Webinar creation error: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création du webinaire: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified webinar.
     */
    public function show($id)
    {
        $webinar = Webinar::with(['instructor', 'category', 'registrations.user'])
            ->findOrFail($id);

        $registrations = $webinar->registrations()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('lms::portals.admin.webinars.show', compact('webinar', 'registrations'));
    }

    /**
     * Show the form for editing the webinar.
     */
    public function edit($id)
    {
        $webinar = Webinar::findOrFail($id);
        // Récupérer les instructeurs depuis la table instructors avec leur relation user
        $instructors = Instructor::with('user')->get()->map(function ($instructor) {
            if ($instructor->user) {
                $instructor->user->full_name = $instructor->user->first_name . ' ' . $instructor->user->last_name;
            }
            return $instructor;
        });
        $categories = Category::where('status', 1)->get();

        return view('lms::portals.admin.webinars.edit', compact('webinar', 'instructors', 'categories'));
    }

    /**
     * Update the specified webinar.
     */
    public function update(Request $request, $id)
    {
        $webinar = Webinar::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'instructor_id' => 'required|exists:users,id',
            'category_id' => 'nullable|exists:categories,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'max_participants' => 'nullable|integer|min:1',
            'is_free' => 'nullable|boolean',
            'is_recorded' => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
            'meeting_url' => 'nullable|url',
            'meeting_id' => 'nullable|string',
            'meeting_password' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->only([
                'title', 'description', 'short_description', 'instructor_id', 'category_id',
                'start_date', 'end_date', 'max_participants', 'is_free', 'is_recorded',
                'is_published', 'meeting_url', 'meeting_id', 'meeting_password'
            ]);

            // Force le prix à 0 et is_free à true
            $data['price'] = 0;
            $data['is_free'] = 1;

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($webinar->image) {
                    Storage::disk('public')->delete($webinar->image);
                }
                $data['image'] = $request->file('image')->store('webinars', 'public');
            }

            $webinar->update($data);

            DB::commit();

            return redirect()->route('webinars.index')
                ->with('success', 'Webinaire mis à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour.');
        }
    }

    /**
     * Remove the specified webinar.
     */
    public function destroy($id)
    {
        \Log::info('Destroy method called for webinar ID: ' . $id);

        $webinar = Webinar::findOrFail($id);
        \Log::info('Webinar found: ' . $webinar->title);

        try {
            DB::beginTransaction();
            \Log::info('Transaction started');

            // Delete image
            if ($webinar->image) {
                \Log::info('Deleting image: ' . $webinar->image);
                Storage::disk('public')->delete($webinar->image);
            }

            // Delete registrations (not enrollments)
            $registrationsCount = $webinar->registrations()->count();
            \Log::info('Found ' . $registrationsCount . ' registrations to delete');
            $webinar->registrations()->delete();

            // Delete webinar
            \Log::info('Deleting webinar: ' . $webinar->title);
            $webinar->delete();

            DB::commit();
            \Log::info('Transaction committed successfully');

            return redirect()->route('webinars.index')
                ->with('success', 'Webinaire supprimé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Webinar deletion error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la suppression: ' . $e->getMessage());
        }
    }

    /**
     * Toggle webinar status.
     */
    public function toggleStatus($id)
    {
        $webinar = Webinar::findOrFail($id);

        $newStatus = $webinar->status === 'scheduled' ? 'cancelled' : 'scheduled';
        $webinar->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => 'Statut mis à jour avec succès.',
            'status' => $newStatus
        ]);
    }

    /**
     * Start webinar.
     */
    public function start($id)
    {
        $webinar = Webinar::findOrFail($id);

        if ($webinar->status !== 'scheduled') {
            return response()->json([
                'success' => false,
                'message' => 'Ce webinaire ne peut pas être démarré.'
            ], 400);
        }

        $webinar->update([
            'status' => 'live',
            'is_live' => true
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Webinaire démarré avec succès.'
        ]);
    }

    /**
     * End webinar.
     */
    public function end($id)
    {
        $webinar = Webinar::findOrFail($id);

        if ($webinar->status !== 'live') {
            return response()->json([
                'success' => false,
                'message' => 'Ce webinaire n\'est pas en cours.'
            ], 400);
        }

        $webinar->update([
            'status' => 'completed',
            'is_live' => false
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Webinaire terminé avec succès.'
        ]);
    }

    /**
     * Get webinar statistics.
     */
    public function statistics($id)
    {
        $webinar = Webinar::findOrFail($id);

        $stats = [
            'total_enrollments' => $webinar->registrations()->count(),
            'attended' => $webinar->registrations()->where('status', 'attended')->count(),
            'missed' => $webinar->registrations()->where('status', 'missed')->count(),
            'cancelled' => $webinar->registrations()->where('status', 'cancelled')->count(),
            'average_rating' => $webinar->registrations()->whereNotNull('rating')->avg('rating'),
        ];

        return response()->json($stats);
    }

    /**
     * Publish a webinar.
     */
    public function publish($id)
    {
        $webinar = Webinar::findOrFail($id);
        \Log::info('Publish method called for webinar: ' . $webinar->id);

        $updateData = [
            'is_published' => true,
            'status' => 'published'
        ];

        // Generate real meeting link if not exists
        if (empty($webinar->meeting_url)) {
            $realMeetingService = new \App\Services\RealMeetingService();
            $meetingData = $realMeetingService->createMeeting($webinar, 'teams');

            $updateData['meeting_url'] = $meetingData['meeting_url'];
            $updateData['meeting_id'] = $meetingData['meeting_id'];
            $updateData['meeting_password'] = $meetingData['meeting_password'];

            \Log::info('Generated REAL meeting URL for webinar: ' . $webinar->id);
            \Log::info('Meeting URL: ' . $meetingData['meeting_url']);
            \Log::info('Platform: ' . $meetingData['platform']);
        }

        $webinar->update($updateData);
        \Log::info('Webinar updated successfully: ' . $webinar->id);

        return redirect()->back()
            ->with('success', 'Webinaire publié avec succès. Lien de réunion Teams généré automatiquement.');
    }

    /**
     * Unpublish a webinar.
     */
    public function unpublish($id)
    {
        $webinar = Webinar::findOrFail($id);
        $webinar->update([
            'is_published' => false,
            'status' => 'draft'
        ]);

        return redirect()->back()
            ->with('success', 'Webinaire dépublié avec succès.');
    }

    /**
     * Generate meeting link for webinar.
     */
    public function generateMeetingLink(Request $request, $id)
    {
        $webinar = Webinar::findOrFail($id);

        try {
            $platform = $request->input('platform', 'teams');
            $options = $request->input('options', []);

            $realMeetingService = new \App\Services\RealMeetingService();
            $meetingData = $realMeetingService->createMeeting($webinar, $platform);

            // Update webinar with meeting data
            $webinar->update([
                'meeting_url' => $meetingData['meeting_url'],
                'meeting_id' => $meetingData['meeting_id'],
                'meeting_password' => $meetingData['meeting_password'],
            ]);

            \Log::info("Generated {$platform} meeting link for webinar {$id}: {$meetingData['meeting_url']}");

            return response()->json([
                'success' => true,
                'message' => "Lien de réunion {$platform} généré avec succès.",
                'meeting_url' => $meetingData['meeting_url'],
                'meeting_id' => $meetingData['meeting_id'],
                'meeting_password' => $meetingData['meeting_password']
            ]);

        } catch (\Exception $e) {
            \Log::error("Failed to generate meeting link for webinar {$id}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du lien de réunion: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Feature a webinar.
     */
    public function feature(Webinar $webinar)
    {
        $webinar->update(['is_featured' => true]);

        return redirect()->back()
            ->with('success', 'Webinaire mis en vedette avec succès.');
    }

    /**
     * Unfeature a webinar.
     */
    public function unfeature(Webinar $webinar)
    {
        $webinar->update(['is_featured' => false]);

        return redirect()->back()
            ->with('success', 'Webinaire retiré de la vedette avec succès.');
    }

    /**
     * Show webinar registrations.
     */
    public function registrations($id)
    {
        $webinar = Webinar::findOrFail($id);
        $registrations = $webinar->registrations()->with('user')->paginate(15);

        return view('lms::portals.admin.webinars.registrations', compact('webinar', 'registrations'));
    }
}

