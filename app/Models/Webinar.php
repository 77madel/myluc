<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Webinar extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'short_description',
        'slug',
        'thumbnail',
        'banner',
        'start_date',
        'end_date',
        'timezone',
        'duration_minutes',
        'platform',
        'meeting_id',
        'meeting_password',
        'meeting_url',
        'join_url',
        'recording_url',
        'max_participants',
        'current_participants',
        'registration_required',
        'registration_open',
        'registration_deadline',
        'price',
        'is_free',
        'currency',
        'status',
        'is_featured',
        'is_published',
        'is_live',
        'allow_recording',
        'allow_chat',
        'allow_questions',
        'allow_screen_sharing',
        'instructor_id',
        'organization_id',
        'category_id',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'views',
        'registrations',
        'attendees',
        'rating',
        'total_ratings'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'registration_deadline' => 'datetime',
        'is_free' => 'boolean',
        'is_published' => 'boolean',
        'registration_required' => 'boolean',
        'registration_open' => 'boolean',
        'is_featured' => 'boolean',
        'is_live' => 'boolean',
        'allow_recording' => 'boolean',
        'allow_chat' => 'boolean',
        'allow_questions' => 'boolean',
        'allow_screen_sharing' => 'boolean',
        'meta_keywords' => 'array',
        'price' => 'decimal:2',
        'rating' => 'decimal:2'
    ];

    // Relationships
    public function instructor()
    {
        return $this->belongsTo(\App\Models\Instructor::class, 'instructor_id');
    }

    // public function organization()
    // {
    //     return $this->belongsTo(\Modules\LMS\Models\Organization::class);
    // }

    public function category()
    {
        return $this->belongsTo(\Modules\LMS\Models\Category::class);
    }

    public function registrations()
    {
        return $this->hasMany(WebinarRegistration::class);
    }

    public function attendees()
    {
        return $this->hasMany(WebinarRegistration::class)->where('status', 'attended');
    }

    public function platformIntegration()
    {
        return $this->belongsTo(WebinarPlatformIntegration::class, 'platform', 'platform');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeLive($query)
    {
        return $query->where('is_live', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeByPlatform($query, $platform)
    {
        return $query->where('platform', $platform);
    }

    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    public function scopePaid($query)
    {
        return $query->where('is_free', false);
    }

    // Accessors & Mutators
    public function getIsUpcomingAttribute()
    {
        return $this->start_date > now();
    }

    public function getIsPastAttribute()
    {
        return $this->end_date < now();
    }

    public function getIsCurrentlyLiveAttribute()
    {
        return $this->is_live && $this->start_date <= now() && $this->end_date >= now();
    }

    public function getDurationFormattedAttribute()
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'min';
        }

        return $minutes . 'min';
    }

    public function getRegistrationStatusAttribute()
    {
        if (!$this->registration_required) {
            return 'not_required';
        }

        if (!$this->registration_open) {
            return 'closed';
        }

        if ($this->registration_deadline && $this->registration_deadline < now()) {
            return 'deadline_passed';
        }

        if ($this->current_participants >= $this->max_participants) {
            return 'full';
        }

        return 'open';
    }

    public function getAvailableSpotsAttribute()
    {
        return max(0, $this->max_participants - $this->current_participants);
    }

    public function getFormattedPriceAttribute()
    {
        if ($this->is_free) {
            return 'Gratuit';
        }

        return number_format($this->price, 2) . ' ' . $this->currency;
    }

    // Methods
    public function canRegister()
    {
        return $this->registration_status === 'open';
    }

    public function isUserRegistered($userId)
    {
        return $this->registrations()->where('user_id', $userId)->exists();
    }

    public function registerUser($userId, $paymentData = [])
    {
        if (!$this->canRegister()) {
            throw new \Exception('Registration is not available for this webinar');
        }

        if ($this->isUserRegistered($userId)) {
            throw new \Exception('User is already registered for this webinar');
        }

        $registration = $this->registrations()->create([
            'user_id' => $userId,
            'registration_token' => \Str::random(32),
            'registered_at' => now(),
            'amount_paid' => $this->price,
            'payment_status' => $this->is_free ? 'paid' : 'pending',
            ...$paymentData
        ]);

        $this->increment('current_participants');
        $this->increment('registrations');

        return $registration;
    }

    public function unregisterUser($userId)
    {
        $registration = $this->registrations()->where('user_id', $userId)->first();

        if ($registration) {
            $registration->update(['status' => 'cancelled']);
            $this->decrement('current_participants');
        }
    }

    public function startWebinar()
    {
        $this->update([
            'is_live' => true,
            'status' => 'live'
        ]);
    }

    public function endWebinar()
    {
        $this->update([
            'is_live' => false,
            'status' => 'completed'
        ]);
    }

    public function generateMeetingLink()
    {
        // This would integrate with the specific platform API
        // For now, return a placeholder
        return route('webinar.join', $this->slug);
    }

    public function getPlatformIntegration()
    {
        return WebinarPlatformIntegration::where('platform', $this->platform)
            ->where('is_active', true)
            ->first();
    }

    public function createPlatformMeeting()
    {
        $integration = $this->getPlatformIntegration();

        if (!$integration) {
            throw new \Exception('No active integration found for platform: ' . $this->platform);
        }

        // This would make API calls to create the meeting
        // Implementation depends on the specific platform
        return $this->generateMeetingLink();
    }

    public function sendReminderEmails()
    {
        $registrations = $this->registrations()
            ->where('status', 'registered')
            ->where('email_reminders', true)
            ->get();

        foreach ($registrations as $registration) {
            // Send reminder email
            // This would integrate with your email system
        }
    }

    public function updateAttendance($userId, $joinTime = null, $leaveTime = null)
    {
        $registration = $this->registrations()->where('user_id', $userId)->first();

        if ($registration) {
            $registration->update([
                'join_time' => $joinTime ?? now(),
                'leave_time' => $leaveTime,
                'was_present' => true,
                'status' => 'attended'
            ]);

            if ($leaveTime && $joinTime) {
                $duration = $leaveTime->diffInMinutes($joinTime);
                $registration->update(['attendance_duration_minutes' => $duration]);
            }
        }
    }

    public function calculateRating()
    {
        $ratings = $this->registrations()
            ->whereNotNull('rating')
            ->pluck('rating');

        if ($ratings->count() > 0) {
            $this->update([
                'rating' => $ratings->avg(),
                'total_ratings' => $ratings->count()
            ]);
        }
    }
}
