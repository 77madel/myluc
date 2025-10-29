<x-frontend-layout>
    <x-theme::breadcrumbs.breadcrumb-one 
        :pageTitle="$topic->title" 
        :pageRoute="[
            ['name' => 'Forums', 'url' => route('forumsList')],
            ['name' => $topic->forum->title, 'url' => route('forum.detail', $topic->forum->slug)],
        ]" 
        :pageName="$topic->title" 
    />

    <div class="container py-12">
        <div class="max-w-6xl mx-auto space-y-10">
            
            <!-- Sujet principal -->
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm">
                <div class="p-8">
                    <h1 class="text-3xl font-extrabold text-gray-900 mb-2 leading-snug">
                        {{ $topic->name }}
                    </h1>
                    <div class="flex items-center text-gray-600 text-sm mb-4">
                            Publié par <span class="font-semibold text-gray-900 ml-1">
                            @if ($topic->user)
                                {{ $topic->user->name ?? ($topic->user->userable->name ?? ($topic->user->userable->first_name ?? 'Utilisateur inconnu')) }}
                            @else
                                Utilisateur inconnu
                            @endif
                        </span>
                        @if ($topic->user && $topic->user->userable_type === 'Modules\\LMS\\Models\\Auth\\Instructor')
                            <span class="badge badge-info ml-2">{{ translate('Instructeur') }}</span>
                        @endif
                        <span class="mx-2">•</span>
                        {{ $topic->created_at->diffForHumans() }}
                    </div>
                    <div class="prose prose-gray max-w-none text-gray-800 leading-relaxed">
                        {!! clean($topic->description) !!}
                    </div>
                </div>
            </div>

            <!-- Liste des posts -->
            <div class="space-y-6">
                @forelse ($topic->forumPosts as $post)
                    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm hover:shadow-md transition-shadow duration-200
                        @if ($post->user && $post->user->userable_type === 'Modules\\LMS\\Models\\Auth\\Instructor')
                            !bg-blue-50 !border-blue-300
                        @endif
                    ">
                        {{-- @if ($post->user && $post->user->userable_type === 'Modules\\LMS\\Models\\Auth\\Instructor')
                            <div class="bg-blue-600 text-black px-6 py-2 rounded-t-2xl flex items-center justify-between">
                                <span class="font-bold text-sm">Réponse de l'instructeur</span>
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                                    <path fill-rule="evenodd" d="M16.403 12.652a3 3 0 000-5.304 3 3 0 00-3.75-3.75 3 3 0 00-5.304 0 3 3 0 00-3.75 3.75 3 3 0 000 5.304 3 3 0 003.75 3.75 3 3 0 005.304 0 3 3 0 003.75-3.75zm-2.546-4.464a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.06l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        @endif --}}
                        <div class="p-6">
                            <div class="flex items-center mb-4">
                                <div class="flex items-center space-x-3">
                                    <div>
                                            <div class="font-semibold text-gray-900 text-lg">
                                            @if ($post->user)
                                                {{ $post->user->name ?? ($post->user->userable->name ?? ($post->user->userable->first_name.' '.$post->user->userable->last_name ?? 'Utilisateur inconnu')) }}
                                            @else
                                                Utilisateur inconnu
                                            @endif
                                            @if ($post->user && $post->user->userable_type === 'Modules\\LMS\\Models\\Auth\\Instructor')
                                                <span class="inline-flex items-center px-3 py-0.5 rounded-full text-sm font-medium bg-blue-600 text-black ml-2">
                                                    <svg class="-ml-1 mr-1.5 h-2 w-2 text-white" fill="currentColor" viewBox="0 0 8 8">
                                                        <circle cx="4" cy="4" r="3" />
                                                    </svg>
                                                    {{ translate('Instructeur') }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-gray-500 text-sm">{{ $post->created_at->diffForHumans() }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="text-gray-800 leading-relaxed">
                                {!! clean($post->description) !!}
                            </div>

                            @auth
                                @if($isInstructor)
                                    <div class="mt-4">
                                        <a href="{{ route('instructor.forum.show', $post->id) }}" 
                                           class="inline-block px-4 py-2 bg-green-600 text-black text-sm font-medium rounded-lg hover:bg-green-700 transition">
                                            💬 Répondre en tant que formateur
                                        </a>
                                    </div>
                                @endif
                            @endauth
                        </div>
                    </div>
                @empty
                    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8 text-center text-gray-500">
                        Aucun message dans ce sujet pour le moment.<br>
                        <span class="text-gray-700 font-medium">Soyez le premier à répondre ! 💬</span>
                    </div>
                @endforelse
            </div>

            <!-- Formulaire de réponse -->
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">💭 Laisser une réponse</h2>
                <form action="{{ route('forum.reply.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <input type="hidden" name="forum_id" value="{{ $topic->forum_id }}">
                    <input type="hidden" name="sub_forum_id" value="{{ $topic->id }}">
                    
                    <div>
                        <textarea 
                            name="description" 
                            id="description" 
                            rows="5" 
                            class="w-full p-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition placeholder-gray-400"
                            placeholder="Écrivez votre réponse ici..."></textarea>
                    </div>
                    
                    <div>
                        <button type="submit" 
                            class="px-6 py-3 bg-blue-600 text-black font-semibold rounded-lg hover:bg-blue-700 transition">
                            Publier la réponse
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-frontend-layout>
