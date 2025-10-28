<x-frontend-layout>
    <x-theme::breadcrumbs.breadcrumb-one :pageTitle="$forum->title" pageRoute="Forums" :pageName="$forum->title" />

    <div class="container py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Main Forum Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="text-3xl font-bold mb-4">{{ $forum->title }}</h1>
                    <div class="text-gray-700 text-lg">
                        {!! clean($forum->description) !!}
                    </div>
                </div>
            </div>

            <!-- Topic List -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-extrabold text-gray-900">Sujets du forum</h2>
                @auth
                    <a href="{{ route('forum.topic.create', $forum->slug) }}" class="inline-flex items-center justify-center px-5 py-2 border border-transparent text-base font-medium rounded-lg shadow-sm text-black bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"> 
                        Créer un nouveau sujet
                    </a>
                @endauth
            </div>
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 space-y-6">
                    @forelse ($forum->subForums as $subForum)
                        <div class="p-6 border border-gray-200 rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h3 class="text-xl font-extrabold text-gray-900 mb-1">
                                        <a href="{{ route('forum.topic.detail', $subForum->slug) }}" class="hover:text-blue-600 transition-colors duration-200">
                                            {{ $subForum->name }}
                                        </a>
                                    </h3>
                                    <p class="text-gray-600 text-sm leading-relaxed">
                                        {!! $subForum->description !!}
                                    </p>
                                </div>
                                <a href="{{ route('forum.topic.detail', $subForum->slug) }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-black bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-700 transition-colors duration-200">
                                    Voir le sujet
                                </a>
                            </div>
                            <div class="flex items-center space-x-6 text-sm text-gray-500 border-t border-gray-100 pt-4">
                                <span class="flex items-center">
                                    <strong class="text-gray-800">{{ $subForum->user->userable->first_name.' '.$subForum->user->userable->last_name }}</strong>
                                </span>
                                &nbsp;
                                <span class="flex items-center">
                                    <strong class="text-gray-800">Il y a {{ $subForum->created_at->diffForHumans() }}</strong>
                                </span>
                                <!-- You can add replies count here if $subForum has a relationship for posts -->
                                {{-- <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                    <strong class="text-gray-800">{{ $subForum->forumPosts->count() }}</strong> Réponses
                                </span> --}}
                            </div>
                        </div>
                    @empty
                        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8 text-center text-gray-500">
                            Aucun sujet n'a été créé dans ce forum pour le moment.<br>
                            @auth
                                <span class="text-gray-700 font-medium">Soyez le premier à <a href="{{ route('forum.topic.create', $forum->slug) }}" class="text-blue-600 hover:underline">créer un sujet</a> ! 💬</span>
                            @else
                                <span class="text-gray-700 font-medium">Connectez-vous pour créer un sujet ! 💬</span>
                            @endauth
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-frontend-layout>
