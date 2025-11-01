<?php

namespace Modules\LMS\Repositories\Forum;

use Illuminate\Support\Str;
use Modules\LMS\Models\Forum\ForumPost;
use Modules\LMS\Repositories\BaseRepository;

class ForumPostRepository extends BaseRepository
{
    protected static $model = ForumPost::class;

    public function storeReply($request)
    {
        $postData = [
            'forum_id' => $request->forum_id,
            'sub_forum_id' => $request->sub_forum_id,
            'author_id' => auth()->id(),
            'description' => $request->description,
            'title' => 'Reply - ' . now()->toDateTimeString(), // Dummy title
            'slug' => 'reply-' . Str::slug(now()->toDateTimeString() . ' ' . uniqid()), // Unique slug
        ];

        return parent::save($postData);
    }
}
