<x-frontend-layout>
    <x-theme::breadcrumbs.breadcrumb-one :pageTitle="__('Messages')" :pageRoute="[['name' => __('Dashboard'), 'url' => route('instructor.dashboard')]]" :pageName="__('Messages')" />

    <div class="container py-10">
        <div class="max-w-6xl mx-auto space-y-8">

            <!-- 🔹 Vos conversations existantes -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('Vos Conversations') }}</h2>

                @forelse ($conversations as $conversation)
                    @php
                        // Ici, l'instructeur est TOUJOURS user2
                        $otherUser = $conversation->user1; // donc user1 = étudiant
                        $lastMessage = $conversation->lastMessage;
                    @endphp
                    <a href="{{ route('instructor.messages.show', $otherUser->id) }}"
                        class="block bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 p-4">
                        <div class="flex items-center space-x-4">
                            <div class="flex-1 min-w-0">
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ $otherUser->userable->first_name }} {{ $otherUser->userable->last_name }}
                                </p>
                                @if ($lastMessage)
                                    <p class="text-sm text-gray-500 truncate">{{ $lastMessage->content }}</p>
                                    <p class="text-xs text-gray-400">{{ $lastMessage->created_at->diffForHumans() }}</p>
                                @else
                                    <p class="text-sm text-gray-500">{{ __('Commencez une conversation') }}</p>
                                @endif
                            </div>
                        </div>
                    </a>
                    <br>
                @empty
                    <div class="text-center py-6 text-gray-500 italic">
                        {{ __('Aucune conversation trouvée.') }}
                    </div>
                @endforelse
            </div>

            <!-- 🔹 Démarrer une nouvelle conversation -->
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">{{ __('Démarrer une nouvelle conversation') }}</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($students as $student)
                        @php
                            // Vérifie si une conversation existe déjà entre l'instructeur connecté et cet étudiant
                            $hasConversation = $conversations->contains(function ($conv) use ($student, $instructor) {
                                return ($conv->user1_id === $student->id &&
                                    $conv->user2_id === $instructor->userable_id) ||
                                    ($conv->user1_id === $instructor->userable_id && $conv->user2_id === $student->id);
                            });
                        @endphp

                        @if (!$hasConversation)
                            <a href="{{ route('instructor.messages.show', $student->user->id) }}"
                                class="block text-center bg-gray-50 border border-gray-200 hover:bg-blue-50 rounded-lg shadow-sm p-5 transition">
                                <p class="font-semibold text-gray-900">{{ $student->first_name }}
                                    {{ $student->last_name }}</p>
                                <p class="text-sm text-gray-500">{{ __('Étudiant') }}</p>
                            </a>
                        @endif
                    @endforeach

                </div>
            </div>
        </div>
    </div>
</x-frontend-layout>
