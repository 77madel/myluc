<?php

namespace Modules\LMS\Models\Analytics;

use Illuminate\Database\Eloquent\Model;
use Modules\LMS\Models\User;

class UserSession extends Model
{
    protected $table = 'user_sessions';
    
    public $timestamps = false;
    
    protected $fillable = [
        'session_id',
        'user_id',
        'started_at',
        'ended_at',
        'duration',
        'pages_visited',
        'actions_performed',
        'converted',
        'conversion_type',
    ];
    
    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'converted' => 'boolean',
    ];
    
    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Relation avec les analytics
     */
    public function analytics()
    {
        return $this->hasOne(UserAnalytics::class, 'session_id', 'session_id');
    }
    
    /**
     * Relation avec les pages vues
     */
    public function pageViews()
    {
        return $this->hasMany(PageView::class, 'session_id', 'session_id');
    }
}

