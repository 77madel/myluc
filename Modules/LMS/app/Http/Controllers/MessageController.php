<?php

namespace Modules\LMS\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\LMS\Models\LmsConversation;
use Modules\LMS\Models\LmsMessage;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function index()
    {
        // Récupérer les conversations de l'utilisateur authentifié
        $conversations = LmsConversation::where('user1_id', Auth::id())
            ->orWhere('user2_id', Auth::id())
            ->get();

        return view('lms::messages.index', compact('conversations'));
    }

    public function show($conversation_id)
    {
        // Récupérer la conversation et ses messages
        $conversation = LmsConversation::with('messages.sender')->findOrFail($conversation_id);

        // Marquer les messages comme lus
        $this->markMessagesAsRead($conversation);

        return view('lms::messages.show', compact('conversation'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:lms_conversations,id',
            'content' => 'required|string',
        ]);

        // Créer le message
        $message = LmsMessage::create([
            'conversation_id' => $request->conversation_id,
            'sender_id' => Auth::id(),
            'content' => $request->content,
        ]);

        // Mettre à jour le dernier message de la conversation
        $conversation = LmsConversation::find($request->conversation_id);
        $conversation->last_message_id = $message->id;
        $conversation->save();

        return back();
    }

    private function markMessagesAsRead(LmsConversation $conversation)
    {
        $conversation->messages()
            ->where('sender_id', '!=', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}