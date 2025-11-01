<?php

namespace Modules\LMS\Models\Certificate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\LMS\Models\Courses\Course;
use Modules\LMS\Models\User;

class UserCertificate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'downloaded_at' => 'datetime',
        'certificated_date' => 'date',
    ];

    /**
     * Vérifier si le certificat a déjà été téléchargé
     */
    public function isDownloaded(): bool
    {
        return !is_null($this->downloaded_at);
    }

    /**
     * Marquer le certificat comme téléchargé
     */
    public function markAsDownloaded(): void
    {
        $this->update([
            'downloaded_at' => now(),
            'download_count' => $this->download_count + 1,
            'download_ip' => request()->ip(),
            'download_user_agent' => request()->userAgent(),
        ]);
    }

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
        return $this->belongsTo(Course::class);
    }
}
