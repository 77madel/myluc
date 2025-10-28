<x-dashboard-layout>
    <x-slot:title>{{ $forum->title . ' - Topics' }}</x-slot:title>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ $forum->title }} - {{ translate('Topics') }}</h3>
                    </div>
                    <div class="card-body">
                        @forelse ($forum->subForums as $subForum)
                            <h4 class="mt-4 mb-3">{{ $subForum->title }}</h4>
                            @forelse ($subForum->forumPosts as $post)
                                <div class="p-3 mb-3 border rounded-lg shadow-sm">
                                    <h5 class="font-semibold">
                                        <a href="{{ route('instructor.forum.show', $post->id) }}" class="text-blue-600 hover:underline">
                                            {{ $post->title }}
                                        </a>
                                    </h5>
                                    <p class="text-gray-600 text-sm mt-1">
                                        {{ translate('Author') }}: {{ $post->user->name ?? 'Unknown User' }}
                                        @if ($post->user && $post->user->userable_type === 'Modules\\LMS\\Models\\Auth\\Instructor')
                                            <span class="badge badge-info ml-2">{{ translate('Instructor') }}</span>
                                        @endif
                                        | {{ $post->created_at->diffForHumans() }}
                                    </p>
                                    <p class="text-gray-500 text-sm mt-2">
                                        {!! $post->description !!}
                                    </p>
                                </div>
                            @empty
                                <p class="text-gray-500">{{ translate('No topics in this sub-forum yet.') }}</p>
                            @endforelse
                        @empty
                            <p class="text-gray-500">{{ translate('No sub-forums in this forum yet.') }}</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
