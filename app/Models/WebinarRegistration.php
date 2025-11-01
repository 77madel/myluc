<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebinarRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'webinar_id',
        'user_id',
        'registration_token',
        'status',
        'registered_at',
        'confirmed_at',
        'attended_at',
        'amount_paid',
        'payment_method',
        'payment_reference',
        'payment_status',
        'email_reminders',
        'sms_reminders',
        'custom_fields',
        'platform_user_id',
        'platform_join_url',
        'platform_access_granted',
        'join_time',
        'leave_time',
        'attendance_duration_minutes',
        'was_present',
        'rating',
        'feedback',
        'suggestions'
    ];

    protected $casts = [
        'registered_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'attended_at' => 'datetime',
        'join_time' => 'datetime',
        'leave_time' => 'datetime',
        'amount_paid' => 'decimal:2',
        'email_reminders' => 'boolean',
        'sms_reminders' => 'boolean',
        'custom_fields' => 'array',
        'platform_access_granted' => 'boolean',
        'was_present' => 'boolean'
    ];

    // Relationships
    public function webinar()
    {
        return $this->belongsTo(Webinar::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeRegistered($query)
    {
        return $query->where('status', 'registered');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    public function scopeAttended($query)
    {
        return $query->where('status', 'attended');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    public function scopePending($query)
    {
        return $query->where('payment_status', 'pending');
    }

    // Accessors
    public function getAttendanceDurationFormattedAttribute()
    {
        $hours = floor($this->attendance_duration_minutes / 60);
        $minutes = $this->attendance_duration_minutes % 60;

        if ($hours > 0) {
            return $hours . 'h ' . $minutes . 'min';
        }

        return $minutes . 'min';
    }

    public function getIsConfirmedAttribute()
    {
        return $this->status === 'confirmed';
    }

    public function getIsAttendedAttribute()
    {
        return $this->status === 'attended';
    }

    public function getIsPaidAttribute()
    {
        return $this->payment_status === 'paid';
    }

    // Methods
    public function confirm()
    {
        $this->update([
            'status' => 'confirmed',
            'confirmed_at' => now()
        ]);
    }

    public function markAsAttended()
    {
        $this->update([
            'status' => 'attended',
            'attended_at' => now(),
            'was_present' => true
        ]);
    }

    public function markAsNoShow()
    {
        $this->update([
            'status' => 'no_show'
        ]);
    }

    public function cancel()
    {
        $this->update([
            'status' => 'cancelled'
        ]);
    }

    public function updateAttendance($joinTime = null, $leaveTime = null)
    {
        $this->update([
            'join_time' => $joinTime ?? now(),
            'leave_time' => $leaveTime,
            'was_present' => true
        ]);

        if ($leaveTime && $this->join_time) {
            $duration = $leaveTime->diffInMinutes($this->join_time);
            $this->update(['attendance_duration_minutes' => $duration]);
        }
    }

    public function submitFeedback($rating, $feedback = null, $suggestions = null)
    {
        $this->update([
            'rating' => $rating,
            'feedback' => $feedback,
            'suggestions' => $suggestions
        ]);

        // Update webinar rating
        $this->webinar->calculateRating();
    }

    public function generateJoinUrl()
    {
        if ($this->platform_join_url) {
            return $this->platform_join_url;
        }

        // Generate join URL based on platform
        return route('webinar.join', [
            'webinar' => $this->webinar->slug,
            'token' => $this->registration_token
        ]);
    }

    public function sendReminder($type = 'email')
    {
        if ($type === 'email' && $this->email_reminders) {
            // Send email reminder
            // This would integrate with your email system
        } elseif ($type === 'sms' && $this->sms_reminders) {
            // Send SMS reminder
            // This would integrate with your SMS system
        }
    }
}





