<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    use HasFactory;

    protected $table = 'instructors';

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'bio',
        'specialization',
        'experience_years',
        'education',
        'certifications',
        'social_links',
        'is_active'
    ];

    protected $casts = [
        'social_links' => 'array',
        'is_active' => 'boolean'
    ];

    // Accessor pour le nom complet
    public function getFullNameAttribute()
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    // Relations
    public function webinars()
    {
        return $this->hasMany(Webinar::class);
    }
}
