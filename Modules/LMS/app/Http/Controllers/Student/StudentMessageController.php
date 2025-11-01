<?php

namespace Modules\LMS\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Modules\LMS\Models\LmsConversation;
use Modules\LMS\Models\LmsMessage;
use Illuminate\Support\Facades\Auth;
use Modules\LMS\Models\Auth\Instructor;

class StudentMessageController extends Controller
{
    public function index()
    {
        $student = Auth::user();

        // Récupérer tous les instructeurs
        // Assurez-vous que votre modèle User a une relation ou une portée pour les instructeurs
        // Par exemple, si vous utilisez Spatie/Laravel-Permission, vous pouvez faire :
        $instructors = Instructor::get();
        // dd($instructors);
        // Récupérer les conversations existantes de l'étudiant
        $conversations = LmsConversation::where(function ($query) use ($student) {
            $query->where('user1_id', $student->id)
                ->orWhere('user2_id', $student->id);
        })->with(['user1', 'user2', 'lastMessage'])->get();
        return view('lms::student.messages.index', compact('instructors', 'conversations'));
    }

    public function show($id)
    {
        $student = Auth::user();
        $instructor = Instructor::find($id); // L'utilisateur passé est l'instructeur
        // Trouver ou créer la conversation
        $conversation = LmsConversation::where(function ($query) use ($student, $instructor) {
            $query->where('user1_id', $student->id)
                ->where('user2_id', $instructor->id);
        })->orWhere(function ($query) use ($student, $instructor) {
            $query->where('user1_id', $instructor->id)
                ->where('user2_id', $student->id);
        })->first();

        if (!$conversation) {
            $conversation = LmsConversation::create([
                'user1_id' => $student->id,
                'user2_id' => $instructor->id,
            ]);
        }

        // Récupérer les messages de la conversation
        $messages = LmsMessage::where('conversation_id', $conversation->id)
            ->orderBy('created_at', 'asc')
            ->get();

        // Marquer les messages non lus de l'instructeur comme lus
        LmsMessage::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $student->id) // on marque les messages envoyés par l'autre utilisateur
            ->whereNull('read_at')
            ->update(['read_at' => now()]);


        return view('lms::student.messages.show', compact('conversation', 'messages', 'instructor'));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $student = Auth::user();
        $instructor = Instructor::find($id);

        $conversation = LmsConversation::where(function ($query) use ($student, $instructor) {
            $query->where('user1_id', $student->id)
                ->where('user2_id', $instructor->id);
        })->orWhere(function ($query) use ($student, $instructor) {
            $query->where('user1_id', $instructor->id)
                ->where('user2_id', $student->id);
        })->first();

        if (!$conversation) {
            $conversation = LmsConversation::create([
                'user1_id' => $student->id,
                'user2_id' => $instructor->id,
            ]);
        }

        $message = LmsMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $student->id,
            'receiver_id' => $instructor->id,
            'content' => $request->input('content'),
        ]);

        $conversation->update(['last_message_id' => $message->id]);

        return back()->with('success', 'Message envoyé !');
    }

    public function startConversation(Request $request)
    {
        $otherUserId = $request->input('user_id');
        $user = Auth::user();

        // Check if a conversation already exists
        $conversation = LmsConversation::where(function ($query) use ($user, $otherUserId) {
            $query->where('user1_id', $user->id)->where('user2_id', $otherUserId);
        })->orWhere(function ($query) use ($user, $otherUserId) {
            $query->where('user1_id', $otherUserId)->where('user2_id', $user->id);
        })->first();

        if (!$conversation) {
            $conversation = LmsConversation::create([
                'user1_id' => $user->id,
                'user2_id' => $otherUserId,
            ]);
        }

        return redirect()->route('student.messages.show', $conversation->id);
    }
}
