<?php

namespace Modules\LMS\Http\Controllers\Instructor;

use Illuminate\Http\Request;
use Modules\LMS\Models\User;
use Modules\LMS\Models\LmsMessage;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\LMS\Models\Auth\Student;
use Modules\LMS\Models\LmsConversation;


class InstructorMessageController extends Controller
{
    /**
     * Liste des conversations de l’instructeur
     */
    public function index()
    {
        $instructor = Auth::user();
        $students = Student::orderBy('id','desc')->get();

        $conversations = LmsConversation::select('lms_conversations.*')
            ->leftJoin('lms_messages', 'lms_conversations.last_message_id', '=', 'lms_messages.id')
            ->where('user2_id', $instructor->userable_id)
            ->with(['user1', 'user2', 'lastMessage'])
            ->orderBy('lms_messages.created_at', 'desc')
            ->get();
        return view('lms::instructor.messages.index', compact('students', 'conversations','instructor'));
    }

    /**
     * Affiche la conversation entre l’instructeur et un étudiant
     */
    public function show($id)
    {
        $instructor = Auth::user(); // Auth côté prof
        $student = User::findOrFail($id); // l'étudiant est dans users
        // ✅ Vérifie si la conversation existe déjà
        $conversation = LmsConversation::where(function ($query) use ($student, $instructor) {
            $query->where('user1_id', $student->id)
                ->where('user2_id', $instructor->userable_id);
        })->orWhere(function ($query) use ($student, $instructor) {
            $query->where('user1_id', $instructor->userable_id)
                ->where('user2_id', $student->id);
        })->first();

        // ✅ Si pas de conversation, on la crée dans le bon sens
        if (!$conversation) {
            $conversation = LmsConversation::create([
                'user1_id' => $student->id,     // user (table users)
                'user2_id' => $instructor->userable_id,  // instructor
            ]);
        }

        // ✅ Récupère les messages
        $messages = LmsMessage::where('conversation_id', $conversation->id)
            ->orderBy('created_at', 'asc')
            ->get();

        // ✅ Marque les messages reçus par le prof comme lus
        LmsMessage::where('conversation_id', $conversation->id)
            ->where('receiver_id', $instructor->userable_id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return view('lms::instructor.messages.show', compact('conversation', 'messages', 'student'));
    }

    /**
     * Envoie un nouveau message à un étudiant
     */
    public function store(Request $request, $id)
    {
        // ✅ Validation
        $request->validate([
            'content' => 'required|string|max:2000',
        ]);
        
        $instructor = Auth::user();           // L'instructeur connecté
        $student = User::find($id);  // L'étudiant ciblé
        
        // ✅ Vérifie si la conversation existe déjà
        $conversation = LmsConversation::where(function ($query) use ($instructor, $student) {
            $query->where('user1_id', $instructor->userable_id) // ID dans la table instructors
                ->where('user2_id', $student->id);           // ID dans la table students
        })
            ->orWhere(function ($query) use ($instructor, $student) {
                $query->where('user1_id', $student->id)
                    ->where('user2_id', $instructor->userable_id);
            })
            ->first();

        // ✅ Si pas de conversation, on la crée
        if (!$conversation) {
            $conversation = LmsConversation::create([
                'user1_id' => $student->id,
                'user2_id' => $instructor->userable_id,
            ]);
        }

        // ✅ Création du message
        $message = LmsMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $student->id,
            'receiver_id'     => $instructor->userable_id,
            'content'         => $request->input('content'),
        ]);

        // ✅ Met à jour le dernier message dans la conversation
        $conversation->update([
            'last_message_id' => $message->id,
            'updated_at'      => now(),
        ]);

        // Envoyer la notification à l'étudiant
        $student->notify(new \Modules\LMS\Notifications\MessageReplied($message, $conversation));

        return back()->with('success', 'Message envoyé avec succès ✅');
    }
}
