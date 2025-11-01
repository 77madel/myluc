<?php

namespace Modules\LMS\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\LMS\Models\Auth\Instructor;

class LmsConversation extends Model
{
    use HasFactory;

    protected $table = 'lms_conversations';

    protected $fillable = [
        'user1_id',
        'user2_id',
        'last_message_id',
    ];

    public function user1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    public function user2(): BelongsTo
    {
        return $this->belongsTo(Instructor::class, 'user2_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(LmsMessage::class, 'conversation_id');
    }

    public function lastMessage(): BelongsTo
    {
        return $this->belongsTo(LmsMessage::class, 'last_message_id');
    }
}