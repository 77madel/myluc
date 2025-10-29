<?php

namespace Modules\LMS\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\LMS\Repositories\Forum\ForumRepository;
use Modules\LMS\Repositories\Courses\CourseRepository;
use Modules\LMS\Repositories\Forum\ForumPostRepository;
use Modules\LMS\Models\Forum\ForumPost;
use Modules\LMS\Models\Forum\Forum;
use Illuminate\Support\Str;
use App\Notifications\InstructorRepliedToTopicNotification;

class ForumController extends Controller
{
    public function __construct(protected ForumRepository $forum, protected CourseRepository $course, protected ForumPostRepository $forumPost) {}

    public function create()
    {
        $courses = $this->course->getInstructorCourses(auth()->id());
        return view('portal::instructor.forum.create', compact('courses'));
    }

    public function index()
    {
        $forums = $this->forum->getInstructorForums(auth()->id());
        return view('portal::instructor.forum.index', compact('forums'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:forums,slug',
            'description' => 'required|string',
            'course_id' => 'nullable|exists:courses,id',
        ]);

        $forumData = $request->only(['title', 'slug', 'description', 'course_id']);
        $forumData['slug'] = $forumData['slug'] ? Str::slug($forumData['slug']) : Str::slug($forumData['title']);

        // Handle image upload if any
        if ($request->hasFile('forum_img')) {
            $image = $this->forum->upload($request, fieldname: 'forum_img', file: '', folder: 'lms/forums');
            $forumData['image'] = $image;
        }

        $response = $this->forum->save($forumData);

        if ($response['status'] === 'success') {
            toastr()->success(translate('Forum created successfully!'));
            return redirect()->route('instructor.dashboard'); // Redirect to instructor dashboard or forum list
        } else {
            toastr()->error(translate('Failed to create forum.'));
            return back()->withInput();
        }
    }

    public function postsIndex(Forum $forum)
    {
        // Explicitly fetch the forum with its relationships
        $forum = Forum::with(['subForums.forumPosts.user'])->find($forum->id);

        if (!$forum) {
            abort(404); // Or handle the case where forum is not found
        }

        return view('portal::instructor.forum.posts_index', compact('forum'));
    }

    public function show(ForumPost $post)
    {
        $post->load('replies.user'); // Load replies and their authors
        return view('portal::instructor.forum.show', compact('post'));
    }

    public function reply(Request $request, ForumPost $post)
    {
        $request->validate([
            'description' => 'required|string',
        ]);

        $replyData = [
            'forum_id' => $post->forum_id,
            'sub_forum_id' => $post->sub_forum_id,
            'author_id' => auth()->id(),
            'title' => 'Re: ' . $post->title,
            'description' => $request->input('description'),
            'parent_id' => $post->id,
        ];

        $response = $this->forumPost->save($replyData);

        if ($response['status'] === 'success') {
            // Send notification to the original topic author
            $originalTopicAuthor = $post->user; // $post is the original topic
            if ($originalTopicAuthor) {
                $originalTopicAuthor->notify(new InstructorRepliedToTopicNotification($response['data'], $post));
            }

            toastr()->success(translate('Reply posted successfully!'));
            return back();
        } else {
            toastr()->error(translate('Failed to post reply.'));
            return back()->withInput();
        }
    }
}
