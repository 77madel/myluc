<?php

namespace Modules\LMS\Models\Auth;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\LMS\Models\DynamicContentTranslation;
use Modules\LMS\Models\Localization\City;
use Modules\LMS\Models\Localization\Country;
use Modules\LMS\Models\Localization\State;
use Modules\LMS\Models\User;

class Organization extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    public function instructors(): HasMany
    {
        return $this->hasMany(Instructor::class);
    }

    public function user(): MorphOne
    {
        return $this->morphOne(User::class, 'userable');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    /**
     * Get the user's image.
     */
    public function translations(): MorphMany
    {
        return $this->morphMany(DynamicContentTranslation::class, 'translationable');
    }

    /**
     * Liens d'inscription personnalisés
     */
    public function enrollmentLinks(): HasMany
    {
        return $this->hasMany(OrganizationEnrollmentLink::class);
    }

    /**
     * Modules assignés à cette organisation
     */
    public function organizationModules(): HasMany
    {
        return $this->hasMany(OrganizationModule::class);
    }

    /**
     * Participants de cette organisation
     */
    public function organizationParticipants(): HasMany
    {
        return $this->hasMany(OrganizationParticipant::class);
    }

    /**
     * Logs d'activité
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(OrganizationActivityLog::class);
    }

    /**
     * Récupérer le nombre de participants actifs
     */
    public function getActiveParticipantsCount(): int
    {
        return $this->organizationParticipants()
            ->where('status', 'active')
            ->count();
    }

    /**
     * Récupérer les statistiques globales
     */
    public function getStatistics(): array
    {
        return [
            'total_participants' => $this->organizationParticipants()->count(),
            'active_participants' => $this->getActiveParticipantsCount(),
            'total_modules' => $this->organizationModules()
                ->where('status', 'active')
                ->count(),
            'enrollment_links' => $this->enrollmentLinks()
                ->where('status', 'active')
                ->count(),
            'this_month_enrollments' => $this->organizationParticipants()
                ->whereMonth('enrolled_at', now()->month)
                ->count()
        ];
    }
}
