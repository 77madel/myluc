<?php

namespace Modules\LMS\Models\Analytics;

use Illuminate\Database\Eloquent\Model;
use Modules\LMS\Models\User;

class UserAnalytics extends Model
{
    protected $table = 'user_analytics';
    
    public $timestamps = false; // Pas de created_at/updated_at
    
    protected $fillable = [
        'user_id',
        'session_id',
        'device_type',
        'os',
        'browser',
        'browser_version',
        'screen_width',
        'screen_height',
        'ip_address',
        'country',
        'country_code',
        'city',
        'timezone',
        'referrer',
        'traffic_source',
        'search_engine',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'age',
        'gender',
        'profession',
        'first_visit',
        'last_visit',
    ];
    
    protected $casts = [
        'first_visit' => 'datetime',
        'last_visit' => 'datetime',
    ];
    
    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Relation avec les pages vues
     */
    public function pageViews()
    {
        return $this->hasMany(PageView::class, 'session_id', 'session_id');
    }
    
    /**
     * Relation avec la session
     */
    public function session()
    {
        return $this->hasOne(UserSession::class, 'session_id', 'session_id');
    }
}

