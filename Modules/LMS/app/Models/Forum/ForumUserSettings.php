<?php

// Modules/LMS/Models/Forum/ForumUserSettings.php

namespace Modules\LMS\Models\Forum;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForumUserSettings extends Model
{
    protected $fillable = [
        'user_id',
        'email_on_reply',
        'email_on_mention',
        'email_on_new_post',
        'platform_notification',
        'email_frequency',
    ];

    protected $casts = [
        'email_on_reply' => 'boolean',
        'email_on_mention' => 'boolean',
        'email_on_new_post' => 'boolean',
        'platform_notification' => 'boolean',
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Méthodes utilitaires
    public function shouldSendEmail($notificationType): bool
    {
        if ($this->email_frequency === 'realtime') {
            return match($notificationType) {
                'new_reply' => $this->email_on_reply,
                'mention' => $this->email_on_mention,
                'new_post' => $this->email_on_new_post,
                default => false,
            };
        }

        // Pour daily/weekly, gérer via un job planifié
        return false;
    }

    public static function getOrCreateForUser($userId): self
    {
        return self::firstOrCreate(
            ['user_id' => $userId],
            [
                'email_on_reply' => true,
                'email_on_mention' => true,
                'email_on_new_post' => false,
                'platform_notification' => true,
                'email_frequency' => 'realtime',
            ]
        );
    }
}
