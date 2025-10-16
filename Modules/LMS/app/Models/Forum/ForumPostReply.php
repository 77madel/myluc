<?php
// Modules/LMS/Models/Forum/ForumPostReply.php

namespace Modules\LMS\Models\Forum;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ForumPostReply extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'forum_post_id',
        'user_id',
        'parent_id',
        'content',
        'is_solution',
        'likes',
        'is_edited',
        'edited_at',
    ];

    protected $casts = [
        'is_solution' => 'boolean',
        'likes' => 'integer',
        'is_edited' => 'boolean',
        'edited_at' => 'datetime',
    ];

    // Relations
    public function post(): BelongsTo
    {
        return $this->belongsTo(ForumPost::class, 'forum_post_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ForumPostReply::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ForumPostReply::class, 'parent_id');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(ForumAttachment::class, 'attachable');
    }

    // Méthodes utilitaires
    public function markAsSolution(): void
    {
        // Retirer la solution actuelle
        $this->post->replies()
            ->where('is_solution', true)
            ->update(['is_solution' => false]);

        // Marquer cette réponse comme solution
        $this->update(['is_solution' => true]);
        $this->post->markAsResolved();
    }

    public function like(): void
    {
        $this->increment('likes');
    }

    public function unlike(): void
    {
        $this->decrement('likes');
    }

    public function isAuthor($user): bool
    {
        return $this->user_id === $user->id;
    }
}
