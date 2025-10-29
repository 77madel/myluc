<?php

namespace Modules\LMS\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebinarEnrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'webinar_id',
        'user_id',
        'status',
        'enrolled_at',
        'attended_at',
        'feedback',
        'rating',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'attended_at' => 'datetime',
        'rating' => 'integer',
    ];

    /**
     * Get the webinar that owns the enrollment.
     */
    public function webinar(): BelongsTo
    {
        return $this->belongsTo(Webinar::class);
    }

    /**
     * Get the user that owns the enrollment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark the user as attended.
     */
    public function markAsAttended(): void
    {
        $this->update([
            'status' => 'attended',
            'attended_at' => now(),
        ]);
    }

    /**
     * Mark the user as missed.
     */
    public function markAsMissed(): void
    {
        $this->update([
            'status' => 'missed',
        ]);
    }

    /**
     * Cancel the enrollment.
     */
    public function cancel(): void
    {
        $this->update([
            'status' => 'cancelled',
        ]);
    }

    /**
     * Check if the user attended the webinar.
     */
    public function hasAttended(): bool
    {
        return $this->status === 'attended';
    }

    /**
     * Check if the enrollment is active.
     */
    public function isActive(): bool
    {
        return in_array($this->status, ['enrolled', 'attended']);
    }
}

