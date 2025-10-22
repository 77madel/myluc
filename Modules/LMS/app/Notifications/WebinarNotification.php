<?php

namespace Modules\LMS\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\LMS\Models\Webinar;

class WebinarNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $webinar;
    protected $type;

    /**
     * Create a new notification instance.
     */
    public function __construct(Webinar $webinar, string $type)
    {
        $this->webinar = $webinar;
        $this->type = $type;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $message = new MailMessage;

        switch ($this->type) {
            case 'enrollment_confirmation':
                $message->subject('Confirmation d\'inscription au webinaire')
                        ->greeting('Bonjour ' . $notifiable->name . ',')
                        ->line('Votre inscription au webinaire "' . $this->webinar->title . '" a été confirmée.')
                        ->line('Détails du webinaire :')
                        ->line('• Date : ' . $this->webinar->start_date->format('d/m/Y à H:i'))
                        ->line('• Durée : ' . $this->webinar->duration . ' minutes')
                        ->line('• Instructeur : ' . $this->webinar->instructor->name)
                        ->action('Voir le webinaire', route('webinar.detail', $this->webinar->slug))
                        ->line('Vous recevrez un rappel 24h avant le début du webinaire.');

                break;

            case 'webinar_reminder':
                $message->subject('Rappel : Votre webinaire commence bientôt')
                        ->greeting('Bonjour ' . $notifiable->name . ',')
                        ->line('Ceci est un rappel que votre webinaire "' . $this->webinar->title . '" commence dans 24 heures.')
                        ->line('Détails du webinaire :')
                        ->line('• Date : ' . $this->webinar->start_date->format('d/m/Y à H:i'))
                        ->line('• Durée : ' . $this->webinar->duration . ' minutes')
                        ->line('• Instructeur : ' . $this->webinar->instructor->name)
                        ->action('Rejoindre le webinaire', route('webinar.join', $this->webinar->id))
                        ->line('Préparez-vous et assurez-vous d\'avoir une connexion internet stable.');

                break;

            case 'webinar_starting':
                $message->subject('Votre webinaire commence maintenant !')
                        ->greeting('Bonjour ' . $notifiable->name . ',')
                        ->line('Votre webinaire "' . $this->webinar->title . '" commence maintenant !')
                        ->line('Cliquez sur le lien ci-dessous pour rejoindre la session.')
                        ->action('Rejoindre maintenant', route('webinar.join', $this->webinar->id))
                        ->line('Si vous avez des difficultés à vous connecter, contactez le support.');

                break;

            case 'webinar_cancelled':
                $message->subject('Webinaire annulé')
                        ->greeting('Bonjour ' . $notifiable->name . ',')
                        ->line('Nous vous informons que le webinaire "' . $this->webinar->title . '" a été annulé.')
                        ->line('Date prévue : ' . $this->webinar->start_date->format('d/m/Y à H:i'))
                        ->line('Nous nous excusons pour ce désagrément.')
                        ->action('Voir d\'autres webinaires', route('webinar.list'))
                        ->line('Nous vous informerons dès qu\'un nouveau webinaire sera programmé.');

                break;

            case 'webinar_completed':
                $message->subject('Webinaire terminé - Merci de votre participation')
                        ->greeting('Bonjour ' . $notifiable->name . ',')
                        ->line('Merci d\'avoir participé au webinaire "' . $this->webinar->title . '".')
                        ->line('Nous espérons que cette session vous a été utile.')
                        ->action('Donner votre avis', route('webinar.detail', $this->webinar->slug))
                        ->line('N\'hésitez pas à consulter nos autres webinaires disponibles.');

                break;
        }

        return $message;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable): array
    {
        $data = [
            'webinar_id' => $this->webinar->id,
            'webinar_title' => $this->webinar->title,
            'webinar_slug' => $this->webinar->slug,
            'instructor_name' => $this->webinar->instructor->name,
            'start_date' => $this->webinar->start_date->toISOString(),
            'type' => $this->type,
        ];

        switch ($this->type) {
            case 'enrollment_confirmation':
                $data['title'] = 'Inscription confirmée';
                $data['message'] = 'Votre inscription au webinaire "' . $this->webinar->title . '" a été confirmée.';
                $data['action_url'] = route('webinar.detail', $this->webinar->slug);
                break;

            case 'webinar_reminder':
                $data['title'] = 'Rappel webinaire';
                $data['message'] = 'Votre webinaire "' . $this->webinar->title . '" commence dans 24 heures.';
                $data['action_url'] = route('webinar.join', $this->webinar->id);
                break;

            case 'webinar_starting':
                $data['title'] = 'Webinaire en cours';
                $data['message'] = 'Votre webinaire "' . $this->webinar->title . '" commence maintenant !';
                $data['action_url'] = route('webinar.join', $this->webinar->id);
                break;

            case 'webinar_cancelled':
                $data['title'] = 'Webinaire annulé';
                $data['message'] = 'Le webinaire "' . $this->webinar->title . '" a été annulé.';
                $data['action_url'] = route('webinar.list');
                break;

            case 'webinar_completed':
                $data['title'] = 'Webinaire terminé';
                $data['message'] = 'Merci d\'avoir participé au webinaire "' . $this->webinar->title . '".';
                $data['action_url'] = route('webinar.detail', $this->webinar->slug);
                break;
        }

        return $data;
    }
}


