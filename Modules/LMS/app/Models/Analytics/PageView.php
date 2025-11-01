<?php

namespace Modules\LMS\Models\Analytics;

use Illuminate\Database\Eloquent\Model;
use Modules\LMS\Models\User;

class PageView extends Model
{
    protected $table = 'page_views';
    
    public $timestamps = false;
    
    protected $fillable = [
        'session_id',
        'user_id',
        'page_url',
        'page_title',
        'referrer_url',
        'time_on_page',
        'scroll_depth',
        'visited_at',
    ];
    
    protected $casts = [
        'visited_at' => 'datetime',
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
        return $this->belongsTo(UserAnalytics::class, 'session_id', 'session_id');
    }
}

