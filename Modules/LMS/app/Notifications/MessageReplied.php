<?php

namespace Modules\LMS\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Modules\LMS\Models\LmsMessage;
use Modules\LMS\Models\LmsConversation;

class MessageReplied extends Notification
{
    use Queueable;

    protected $message;
    protected $conversation;

    /**
     * Create a new notification instance.
     */
    public function __construct(LmsMessage $message, LmsConversation $conversation)
    {
        $this->message = $message;
        $this->conversation = $conversation;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Nouveau message reçu 💬',
            'message' => $this->message->sender->name . ' vous a envoyé : "' . $this->message->content . '"',
            'message_id' => $this->message->id,
            'conversation_id' => $this->conversation->id,
            'sender_id' => $this->message->sender_id,
            'sender_name' => $this->message->sender->name,
            'content' => $this->message->content,
            'conversation_url' => route('student.messages.show', $this->conversation->id),
        ];
    }
}
