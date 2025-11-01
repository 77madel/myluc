<?php

namespace Modules\LMS\Models\Auth;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\LMS\Traits\BelongsToOrganization;

class OrganizationCoursePurchase extends Model
{
    use BelongsToOrganization;

    protected $fillable = [
        'organization_id',
        'course_id',
        'amount',
        'purchase_date',
        'enrollment_link_id',
        'status',
        'payment_reference',
        'payment_details',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'purchase_date' => 'datetime',
        'payment_details' => 'array',
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
     * Scope pour les achats complétés
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope pour les achats en attente
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
