<?php

namespace Modules\LMS\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseCompletion extends Model
{
    use HasFactory;

    protected $table = 'course_completions';

    protected $fillable = [
        'user_id',
        'course_id',
        'is_completed',
        'completed_at',
        'completion_percentage',
        'validation_details',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'validation_details' => 'array',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec le cours
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(\Modules\LMS\Models\Courses\Course::class);
    }

    /**
     * Marquer le cours comme terminé
     */
    public function markAsCompleted(array $validationDetails = []): void
    {
        $this->update([
            'is_completed' => true,
            'completed_at' => now(),
            'completion_percentage' => 100,
            'validation_details' => $validationDetails,
        ]);
    }

    /**
     * Vérifier si le cours est terminé
     */
    public function isCompleted(): bool
    {
        return $this->is_completed;
    }

    /**
     * Obtenir le pourcentage de completion
     */
    public function getCompletionPercentage(): int
    {
        return $this->completion_percentage;
    }

    /**
     * Obtenir les détails de validation
     */
    public function getValidationDetails(): array
    {
        return $this->validation_details ?? [];
    }

    /**
     * Scope pour les cours terminés
     */
    public function scopeCompleted($query)
    {
        return $query->where('is_completed', true);
    }

    /**
     * Scope pour les cours en cours
     */
    public function scopeInProgress($query)
    {
        return $query->where('is_completed', false);
    }
}
