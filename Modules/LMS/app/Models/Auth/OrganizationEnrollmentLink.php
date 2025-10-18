<?php

namespace Modules\LMS\Models\Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\LMS\Traits\BelongsToOrganization;

class OrganizationEnrollmentLink extends Model
{
    // Ne pas utiliser BelongsToOrganization pour les liens d'inscription
    // car ils doivent être accessibles publiquement

    protected $fillable = [
        'organization_id',
        'name',
        'slug',
        'description',
        'valid_until',
        'max_enrollments',
        'course_id',
        'status',
        'current_enrollments',
    ];

    protected $casts = [
        'valid_until' => 'datetime',
        'max_enrollments' => 'integer',
        'current_enrollments' => 'integer',
    ];

    /**
     * Relation avec l'organisation
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Relation avec le cours
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(\Modules\LMS\Models\Courses\Course::class);
    }

    /**
     * Relation avec les participants inscrits via ce lien (système unifié)
     */
    public function participants(): HasMany
    {
        return $this->hasMany(\Modules\LMS\Models\User::class, 'organization_id')
            ->where('userable_type', 'Modules\LMS\Models\Auth\Student');
    }

    /**
     * Scope pour les liens actifs
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope pour les liens non pleins
     */
    public function scopeNotFull($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('max_enrollments')
              ->orWhereRaw('current_enrollments < max_enrollments');
        });
    }

    /**
     * Vérifier si le lien est valide
     */
    public function isValid(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        if ($this->valid_until && $this->valid_until < now()) {
            return false;
        }

        if ($this->max_enrollments && $this->current_enrollments >= $this->max_enrollments) {
            return false;
        }

        return true;
    }

    /**
     * Incrémenter le nombre d'inscriptions
     */
    public function incrementEnrollments(): void
    {
        $this->increment('current_enrollments');
    }

    /**
     * Décrémenter le nombre d'inscriptions
     */
    public function decrementEnrollments(): void
    {
        if ($this->current_enrollments > 0) {
            $this->decrement('current_enrollments');
        }
    }
}
