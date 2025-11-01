<?php

namespace Modules\LMS\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NotifyCertificateGenerated extends Notification
{
    use Queueable;

    protected $certificate;
    protected $course;
    protected $user;

    public function __construct($certificate, $course, $user)
    {
        $this->certificate = $certificate;
        $this->course = $course;
        $this->user = $user;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'certificate_generated',
            'title' => '🎓 Félicitations ! Votre certificat est prêt',
            'message' => "Félicitations ! Vous avez terminé le cours \"{$this->course->title}\" et votre certificat est disponible.",
            'certificate_id' => $this->certificate->id,
            'course_title' => $this->course->title,
            'certificate_date' => is_string($this->certificate->certificated_date) ? $this->certificate->certificated_date : $this->certificate->certificated_date->format('d/m/Y'),
            'action_url' => route('student.certificate.download', $this->certificate->id),
            'icon' => '🎓'
        ];
    }
}

