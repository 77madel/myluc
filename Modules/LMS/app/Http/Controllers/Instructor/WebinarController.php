<?php

namespace Modules\LMS\Http\Controllers\Instructor;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Modules\LMS\Models\Webinar;
use Modules\LMS\Models\Category;
use App\Models\WebinarRegistration;
use App\Services\RealMeetingService;
use App\Http\Controllers\Controller;

class WebinarController extends Controller
{
    /**
     * Display a listing of webinars for instructor.
     */
    public function index()
    {
        $webinars = Webinar::where('instructor_id', Auth::id())
            ->with(['category', 'registrations'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('lms::portals.instructor.webinars.index', compact('webinars'));
    }

    /**
     * Show the form for creating a new webinar.
     */
    public function create()
    {
        $categories = Category::where('status', 1)->get();
        return view('lms::portals.instructor.webinars.create', compact('categories'));
    }

    /**
     * Store a newly created webinar.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'required|string|max:500',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'duration' => 'required|integer|min:15',
            'max_participants' => 'nullable|integer|min:1',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'meeting_url' => 'nullable|url',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->only([
                'title', 'description', 'short_description', 'start_date', 'end_date',
                'duration', 'max_participants', 'category_id', 'meeting_url'
            ]);

            // Set instructor and defaults
            $data['instructor_id'] = Auth::id();
            $data['slug'] = Str::slug($request->title);
            $data['current_participants'] = 0;
            $data['is_free'] = 1; // Always free for instructor webinars
            $data['price'] = 0;
            $data['is_live'] = false;
            $data['is_recorded'] = $request->has('is_recorded');
            $data['is_published'] = false;
            $data['status'] = 'draft';

            // Meeting URL is provided manually by the instructor (no auto-generation)

            // Handle image upload
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('webinars', 'public');
            }

            $webinar = Webinar::create($data);

            DB::commit();

            return redirect()->route('instructor.webinars.index')
                ->with('success', 'Webinaire créé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Instructor webinar creation failed: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la création.');
        }
    }

    /**
     * Display the specified webinar.
     */
    public function show($id)
    {
        $webinar = Webinar::where('instructor_id', Auth::id())
            ->with(['category', 'registrations.user'])
            ->findOrFail($id);

        $registrations = $webinar->registrations()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('lms::portals.instructor.webinars.show', compact('webinar', 'registrations'));
    }

    /**
     * Show the form for editing the specified webinar.
     */
    public function edit($id)
    {
        $webinar = Webinar::where('instructor_id', Auth::id())->findOrFail($id);
        $categories = Category::where('status', 1)->get();

        return view('lms::portals.instructor.webinars.edit', compact('webinar', 'categories'));
    }

    /**
     * Update the specified webinar.
     */
    public function update(Request $request, $id)
    {
        $webinar = Webinar::where('instructor_id', Auth::id())->findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'short_description' => 'required|string|max:500',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'duration' => 'required|integer|min:15',
            'max_participants' => 'nullable|integer|min:1',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->only([
                'title', 'description', 'short_description', 'start_date', 'end_date',
                'duration', 'max_participants', 'category_id'
            ]);

            // Update slug if title changed
            if ($webinar->title !== $request->title) {
                $data['slug'] = Str::slug($request->title);
            }

            // Keep instructor and defaults
            $data['is_free'] = 1;
            $data['price'] = 0;
            $data['is_recorded'] = $request->has('is_recorded');

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

            return redirect()->route('instructor.webinars.index')
                ->with('success', 'Webinaire mis à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Instructor webinar update failed: ' . $e->getMessage());
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
        $webinar = Webinar::where('instructor_id', Auth::id())->findOrFail($id);

        try {
            DB::beginTransaction();

            // Delete image
            if ($webinar->image) {
                Storage::disk('public')->delete($webinar->image);
            }

            // Delete registrations
            $webinar->registrations()->delete();

            // Delete webinar
            $webinar->delete();

            DB::commit();

            return redirect()->route('instructor.webinars.index')
                ->with('success', 'Webinaire supprimé avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Instructor webinar deletion failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la suppression.');
        }
    }

    /**
     * Publish a webinar.
     */
    public function publish($id)
    {
        $webinar = Webinar::where('instructor_id', Auth::id())->findOrFail($id);

        try {
            DB::beginTransaction();

            $updateData = [
                'is_published' => true,
                'status' => 'published'
            ];

            $webinar->update($updateData);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Webinaire publié avec succès. Lien de réunion généré automatiquement.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Instructor webinar publish failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la publication.');
        }
    }

    /**
     * Unpublish a webinar.
     */
    public function unpublish($id)
    {
        $webinar = Webinar::where('instructor_id', Auth::id())->findOrFail($id);

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
        $webinar = Webinar::where('instructor_id', Auth::id())->findOrFail($id);

        try {
            $platform = $request->input('platform', 'teams');

            $realMeetingService = new RealMeetingService();
            $meetingData = $realMeetingService->createMeeting($webinar, $platform);

            // Update webinar with meeting data
            $webinar->update([
                'meeting_url' => $meetingData['meeting_url'],
                'meeting_id' => $meetingData['meeting_id'],
                'meeting_password' => $meetingData['meeting_password'],
            ]);

            Log::info("Generated {$platform} meeting link for instructor webinar {$id}: {$meetingData['meeting_url']}");

            return response()->json([
                'success' => true,
                'message' => "Lien de réunion {$platform} généré avec succès.",
                'meeting_url' => $meetingData['meeting_url'],
                'meeting_id' => $meetingData['meeting_id'],
                'meeting_password' => $meetingData['meeting_password']
            ]);

        } catch (\Exception $e) {
            Log::error("Failed to generate meeting link for instructor webinar {$id}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du lien de réunion: ' . $e->getMessage()
            ], 500);
        }
    }
}
