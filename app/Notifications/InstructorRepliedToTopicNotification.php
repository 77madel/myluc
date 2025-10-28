<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\LMS\Models\Forum\ForumPost; // Import ForumPost model

class InstructorRepliedToTopicNotification extends Notification
{
    use Queueable;

    protected $reply;
    protected $topic;

    /**
     * Create a new notification instance.
     */
    public function __construct(ForumPost $reply, ForumPost $topic)
    {
        $this->reply = $reply;
        $this->topic = $topic;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database']; // Notify via mail and database
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = route('forum.topic.detail', $this->topic->slug); // Assuming topic has a slug for frontend view

        return (new MailMessage)
                    ->subject('Instructor Replied to Your Topic: ' . $this->topic->title)
                    ->line('An instructor has replied to your topic: "' . $this->topic->title . '" ')
                    ->action('View Reply', $url)
                    ->line('Reply content: ' . \Illuminate\Support\Str::limit($this->reply->description, 100))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'topic_id' => $this->topic->id,
            'topic_title' => $this->topic->title,
            'reply_id' => $this->reply->id,
            'reply_description' => $this->reply->description,
            'instructor_name' => $this->reply->user->name ?? 'An Instructor',
            'url' => route('forum.topic.detail', $this->topic->slug),
        ];
    }
}
