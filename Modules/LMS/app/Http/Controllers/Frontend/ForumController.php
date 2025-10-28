<?php

namespace Modules\LMS\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\LMS\Repositories\Forum\ForumRepository;
use Modules\LMS\Repositories\Forum\SubForumRepository;
use Modules\LMS\Repositories\Forum\ForumPostRepository;

class ForumController extends Controller
{
    public function __construct(protected ForumRepository $forum, protected SubForumRepository $subForum, protected ForumPostRepository $forumPost) {}

    /**
     * Display a listing of the resource.
     */
    public function forumsList()
    {
        $data = $this->forum->forumsList();
        return view('portal::frontend.forum.index', $data);
    }

    public function forumDetail($slug)
    {
        $data = $this->forum->forumDetail($slug);
        return view('portal::frontend.forum.detail', $data);
    }

    public function topicDetail($slug)
    {
        $data = $this->subForum->findTopicBySlug($slug);

        $isInstructor = false;
        if (auth()->check()) {
            if (auth()->user()->userable_type === 'Modules\\LMS\\Models\\Auth\\Instructor') {
                $isInstructor = true;
            }
        }

        return view('portal::frontend.forum.topic-detail', array_merge($data, compact('isInstructor')));
    }

    public function createTopic($forum_slug)
    {
        $forum = $this->forum->findBySlug($forum_slug);
        return view('portal::frontend.forum.create-topic', compact('forum'));
    }

    public function storeTopic(Request $request, $forum_slug)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'forum_id' => 'required|exists:forums,id',
        ]);

        $response = $this->subForum->storeTopic($request);

        if ($response['status'] === 'success') {
            toastr()->success(translate('Topic created successfully!'));
            return redirect()->route('forum.detail', $forum_slug);
        } else {
            toastr()->error(translate('Failed to create topic.'));
            return back()->withInput()->withErrors($response['data']); // Pass validation errors back
        }
    }

    public function storeReply(Request $request)
    {
        $request->validate([
            'description' => 'required|string',
            'forum_id' => 'required|exists:forums,id',
            'sub_forum_id' => 'required|exists:sub_forums,id',
        ]);

        $this->forumPost->storeReply($request);

        return back()->with('success', 'Reply posted successfully!');
    }

    public function forumPost(Request $request)
    {
        $forumPost = $this->forum->forumPost($request);
        if ($forumPost['status'] !== 'success') {
            return response()->json($forumPost);
        }
        toastr()->success(translate('Post has been saved successfully!'));

        return response()->json(['status' => $forumPost['status']]);
    }
}
