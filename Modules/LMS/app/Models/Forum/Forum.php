<?php

namespace Modules\LMS\Models\Forum;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\LMS\Models\User;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Modules\LMS\Models\Courses\Course;
use Modules\LMS\Models\Forum\ForumPost;
use Modules\LMS\Models\Forum\SubForum;

class Forum extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $guarded = ['id'];
    protected $fillable = ['title', 'slug', 'description', 'image', 'status', 'course_id'];

    public function subForums(): HasMany
    {
        return $this->hasMany(SubForum::class);
    }

    public function forumPosts(): HasManyThrough
    {
        return $this->hasManyThrough(ForumPost::class, SubForum::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
