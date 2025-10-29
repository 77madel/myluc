<?php

namespace Modules\LMS\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Webinar extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'short_description',
        'slug',
        'image',
        'video_url',
        'meeting_url',
        'meeting_id',
        'meeting_password',
        'start_date',
        'end_date',
        'duration',
        'max_participants',
        'current_participants',
        'price',
        'is_free',
        'is_live',
        'is_recorded',
        'is_published',
        'status',
        'instructor_id',
        'category_id',
        'tags',
        'requirements',
        'learning_outcomes',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_free' => 'boolean',
        'is_live' => 'boolean',
        'is_recorded' => 'boolean',
        'is_published' => 'boolean',
        'tags' => 'array',
        'requirements' => 'array',
        'learning_outcomes' => 'array',
        'price' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($webinar) {
            if (empty($webinar->slug)) {
                $webinar->slug = Str::slug($webinar->title);
            }
        });
    }

    /**
     * Get the instructor that owns the webinar.
     */
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    /**
     * Get the category that owns the webinar.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the enrollments for the webinar.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(WebinarEnrollment::class);
    }

    /**
     * Get the registrations for the webinar.
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(\App\Models\WebinarRegistration::class);
    }

    /**
     * Get the users enrolled in the webinar.
     */
    public function enrolledUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'webinar_enrollments')
                    ->withPivot(['status', 'enrolled_at', 'attended_at', 'feedback', 'rating'])
                    ->withTimestamps();
    }

    /**
     * Check if the webinar is available for enrollment.
     */
    public function isAvailableForEnrollment(): bool
    {
        return $this->is_published
            && in_array($this->status, ['scheduled', 'published'])
            && $this->start_date > now()
            && ($this->max_participants === null || $this->current_participants < $this->max_participants);
    }

    /**
     * Check if the webinar is currently live.
     */
    public function isCurrentlyLive(): bool
    {
        return $this->is_live && $this->start_date <= now() && $this->end_date > now();
    }

    /**
     * Check if the webinar has ended.
     */
    public function hasEnded(): bool
    {
        return $this->end_date < now();
    }

    /**
     * Get the webinar status based on dates.
     */
    public function getStatusAttribute($value)
    {
        if ($this->isCurrentlyLive()) {
            return 'live';
        }

        if ($this->hasEnded()) {
            return 'completed';
        }

        return $value;
    }

    /**
     * Scope for published webinars.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope for upcoming webinars.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now())
                    ->where('status', 'scheduled');
    }

    /**
     * Scope for live webinars.
     */
    public function scopeLive($query)
    {
        return $query->where('is_live', true)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>', now());
    }

    /**
     * Scope for completed webinars.
     */
    public function scopeCompleted($query)
    {
        return $query->where('end_date', '<', now());
    }
}

