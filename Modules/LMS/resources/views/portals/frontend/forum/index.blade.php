<x-frontend-layout>
    <x-theme::breadcrumbs.breadcrumb-one pageTitle="Forums" pageRoute="Forums" pageName="Forums" />

    <div class="container py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="text-2xl font-bold mb-6">Forum List</h1>

                    <div class="space-y-6">
                        @forelse ($forums as $forum)
                            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-200 p-6">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h2 class="text-2xl font-extrabold text-gray-900 mb-1">
                                            <a href="{{ route('forum.detail', $forum->slug) }}" class="hover:text-blue-600 transition-colors duration-200">
                                                {{ $forum->title }}
                                            </a>
                                        </h2>
                                        <p class="text-gray-600 text-sm leading-relaxed">
                                            {!! clean($forum->description) !!}
                                        </p>
                                    </div>
                                    <a href="{{ route('forum.detail', $forum->slug) }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-black bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                        Voir le forum
                                        <svg class="ml-2 -mr-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    </a>
                                </div>
                                <div class="flex items-center space-x-6 text-sm text-gray-500 border-t border-gray-100 pt-4">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h10M7 16h10M9 18h6M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                        <strong class="text-gray-800">{{ $forum->topics }}&nbsp;</strong> Sujets
                                    </span>&nbsp;&nbsp;
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                        <strong class="text-gray-800">{{ $forum->forum_posts_count }}</strong> &nbsp;Messages
                                    </span>&nbsp;&nbsp;
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.146-1.28-.423-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.653.146-1.28.423-1.857m0 0a5.002 5.002 0 019.154 0m-4.5 0a7.5 7.5 0 00-7.5 7.5h15a7.5 7.5 0 00-7.5-7.5z"></path></svg>
                                        <strong class="text-gray-800">{{ $forum->forum_members_count }}</strong> Membres
                                    </span>
                                    &nbsp;
                                    @if ($forum->course && $forum->course->instructors->count() > 0)
                                        <span class="flex items-center">
                                            <strong class="text-gray-800">Par:
                                                @foreach ($forum->course->instructors as $instructor)
                                                    {{ $instructor->userable->first_name.' '.$instructor->userable->last_name }}
                                                    {{ !$loop->last ? ', ' : '' }}
                                                @endforeach
                                            </strong>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8 text-center text-gray-500">
                                Aucun forum trouvé pour le moment.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-frontend-layout>
