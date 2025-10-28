<?php

namespace Modules\LMS\Repositories\Forum;

use Illuminate\Support\Str;
use Modules\LMS\Models\Forum\Forum;
use Modules\LMS\Repositories\BaseRepository;

class ForumRepository extends BaseRepository
{
    protected static $model = Forum::class;

    protected static $exactSearchFields = [];

    protected static $excludedFields = [
        'save' => ['_token',  'forum_img'],
        'update' => ['_token', '_method', 'forum_img'],
    ];

    protected static $rules = [
        'save' => [
            'title' => 'required|unique:forums,title',
            'slug' => 'required|unique:forums,slug',
            'description' => 'required',
        ],
        'update' => [],
    ];

    /**
     * @param  mixed  $request
     */
    public static function save($data): array
    {
        return parent::save($data);
    }

    /**
     * Create a new forum for a specific course.
     *
     * @param \Modules\LMS\Models\Course $course
     * @return array
     */
    public function createForCourse($course): array
    {
        $forumData = [
            'title' => 'Forum for course: ' . $course->title,
            'slug' => Str::slug('Forum for course ' . $course->title . ' ' . uniqid()), // Ensure unique slug
            'description' => 'This is the discussion forum for the course "' . $course->title . '".',
            'course_id' => $course->id,
        ];

        // We call the parent save method directly to bypass the validation rules in the local save method.
        return parent::save($forumData);
    }

    /**
     * @param  int  $id
     * @param  array  $data
     */
    public static function update($id, $request): array
    {
        static::$rules['update'] = [
            'title' => 'required|unique:forums,title,' . $id,
            'description' => 'required',
        ];
        if ($request->hasFile('forum_img')) {
            $forum = parent::first($request->id);
            $forum = $forum['data'];
            $image = parent::upload($request, fieldname: 'forum_img', file: $forum->image ?? '', folder: 'lms/forums');
            $request->request->add(['image' => $image ? $image : $forum->image]);
        }

        $request->request->add(['slug' => Str::slug($request->title)]);

        return parent::update($id, $request->all());
    }

    /**
     *  delete
     *
     * @param  $id  $id
     */
    public static function delete($id, $data = [], $options = [], $relations = []): array
    {
        $forum = parent::first($id);
        if ($forum['status'] == 'success') {
            parent::fileDelete(folder: 'lms/forum', file: $forum['data']->image);
            $forum['data']->delete();
        }

        return $forum;
    }

    /**
     * statusChange
     *
     * @param int id
     */
    public function statusChange($id): array
    {
        $forum = parent::first($id);
        $forum = $forum['data'];
        $forum->status = ! $forum->status;
        $forum->update();

        return [
            'status' => 'success',
            'message' => translate('Status Change Successfully')
        ];
    }

    /**
     * forumsList
     */
    public function forumsList()
    {
        $data['forums'] = static::$model::withCount('forumPosts')->withCount('subForums as topics')->with('course.instructors')->get();
        $data['posts'] = static::$model::with('forumPosts');

        return $data;
    }

    public function forumDetail($slug)
    {
        $forum = static::$model::where('slug', $slug)
            ->with(['subForums' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->firstOrFail();

        return ['forum' => $forum];
    }

    public function findBySlug($slug)
    {
        return static::$model::where('slug', $slug)->firstOrFail();
    }

    /**
     * forumPost
     *
     * @param  mixed  $request
     * @return array
     */
    public function forumPost($request)
    {
        static::$rules['save'] = [
            'title' => 'required',
            'sub_forum_id' => 'required',
            'description' => 'required',
        ];
        $post = static::$model::where('slug', str::slug($request->title))->exists();
        $request->request->add([
            'author_id' => $request->user()->id,
            'slug' => $post ? $post->slug . '-' . random_string() : str::slug($request->title),
        ]);

        return parent::save($request->all());
    }

    public function getInstructorForums($instructorId)
    {
        return static::$model::whereHas('course', function ($query) use ($instructorId) {
            $query->whereHas('instructors', function ($query) use ($instructorId) {
                $query->where('instructor_id', $instructorId);
            });
        })->get();
    }
}
