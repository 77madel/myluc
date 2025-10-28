<x-dashboard-layout>
    <x-slot:title>{{ translate('My Forums') }}</x-slot:title>
    <x-portal::admin.breadcrumb title="My Forums" page-to="Forum" />

    <div class="card">
        <div class="p-6">
            <h1 class="text-2xl font-bold mb-6">My Forums</h1>

            <div class="space-y-4">
                @forelse ($forums as $forum)
                    <div class="p-4 border rounded-lg shadow-sm">
                        <h2 class="text-xl font-semibold">
                            {{ $forum->title }}
                        </h2>
                        <p class="text-gray-600 mt-2">
                            {!! clean($forum->description) !!}
                        </p>
                        @if ($forum->course)
                            <p class="text-gray-500 text-sm mt-1">Associated Course: {{ $forum->course->title }}</p>
                        @endif
                        <div class="flex items-center space-x-4 mt-4 text-sm text-gray-500">
                            <span>
                                <strong>{{ $forum->sub_forums_count }}</strong> Topics
                            </span>
                            <span>
                                <strong>{{ $forum->forum_posts_count }}</strong> Posts
                            </span>
                            <a href="{{ route('instructor.forum.posts.index', $forum->id) }}" class="px-3 py-1 bg-blue-600 text-black rounded hover:bg-blue-700">View Topics</a>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">No forums created yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-dashboard-layout>
