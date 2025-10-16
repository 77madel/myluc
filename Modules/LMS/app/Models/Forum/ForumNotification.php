<?php
// Modules/LMS/Models/Forum/ForumNotification.php

namespace Modules\LMS\Models\Forum;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForumNotification extends Model
{
    protected $fillable = [
        'user_id',
        'forum_post_id',
        'forum_post_reply_id',
        'type',
        'message',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // Relations
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(ForumPost::class, 'forum_post_id');
    }

    public function reply(): BelongsTo
    {
        return $this->belongsTo(ForumPostReply::class, 'forum_post_reply_id');
    }

    // Méthodes utilitaires
    public function markAsRead(): void
    {
        if (!$this->is_read) {
            $this->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }
    }

    public static function sendToUser($userId, $type, $message, $postId = null, $replyId = null)
    {
        // Vérifier les préférences utilisateur
        $settings = ForumUserSettings::where('user_id', $userId)->first();

        if (!$settings || !$settings->platform_notification) {
            return null;
        }

        $notification = self::create([
            'user_id' => $userId,
            'forum_post_id' => $postId,
            'forum_post_reply_id' => $replyId,
            'type' => $type,
            'message' => $message,
        ]);

        // Envoyer email si configuré
        if ($settings && $settings->shouldSendEmail($type)) {
            // Queue email job
            dispatch(new SendForumNotificationEmail($notification));
        }

        return $notification;
    }
}
