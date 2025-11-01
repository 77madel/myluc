<?php

namespace Modules\LMS\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotifyEnrollmentDeadline extends Notification
{
    use Queueable;

    public function __construct(protected array $data)
    {
    }

    public function via($notifiable): array
    {
        return ['database']; // ajouter 'mail' si souhaité
    }

    public function toArray($notifiable): array
    {
        $kind = $this->data['kind'] ?? 'due';
        $courseTitle = $this->data['course_title'] ?? 'Cours';
        $days = $this->data['days_before'] ?? null;
        // Titre lisible attendu par les vues qui consomment notifications->data['title']
        $title = match ($kind) {
            'due' => $days ? "Rappel: fin de formation dans {$days} jour(s)" : 'Rappel: fin de formation imminente',
            'grace' => $days ? "Rappel: fin de grâce dans {$days} jour(s)" : 'Rappel: fin de grâce imminente',
            'expired' => 'Accès expiré au cours',
            default => 'Notification formation',
        };

        return [
            'type' => 'enrollment_deadline',
            'course_id' => $this->data['course_id'] ?? null,
            'course_title' => $courseTitle,
            'kind' => $kind, // due|grace|expired
            'days_before' => $days,
            'due_at' => optional($this->data['due_at'] ?? null)?->toISOString(),
            // Champs génériques généralement utilisés par les vues
            'title' => $title,
            'message' => match ($kind) {
                'due' => $days ? "Le cours '{$courseTitle}' arrive à sa fin dans {$days} jour(s)." : "Le cours '{$courseTitle}' arrive à sa fin très bientôt.",
                'grace' => $days ? "La période de grâce pour '{$courseTitle}' se termine dans {$days} jour(s)." : "La période de grâce pour '{$courseTitle}' se termine très bientôt.",
                'expired' => "Votre accès au cours '{$courseTitle}' a expiré.",
                default => null,
            },
        ];
    }
}


