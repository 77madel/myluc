<x-frontend-layout>
    <x-theme::breadcrumbs.breadcrumb-one
        :pageTitle="__('Messages')"
        :pageRoute="[['name' => __('Dashboard'), 'url' => route('student.dashboard')]]"
        :pageName="__('Messages')"
    />
    <div class="container py-12">
        <div class="max-w-6xl mx-auto space-y-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ __('Vos Conversations') }}</h2>

            @forelse ($conversations as $conversation)
                @php
                    $otherUser = ($conversation->user1_id === Auth::id()) ? $conversation->user2 : $conversation->user1;
                @endphp
                <a href="{{ route('student.messages.show', $otherUser->id) }}" class="block bg-white border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 p-4">
                    <div class="flex items-center space-x-4">
                        {{-- <div class="flex-shrink-0">
                            <img class="h-10 w-10 rounded-full" src="{{ $otherUser->profile_photo_url }}" alt="{{ $otherUser->first_name }}">
                        </div> --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-lg font-semibold text-gray-900">{{ $otherUser->first_name }} {{ $otherUser->last_name }}</p>
                            @if ($conversation->lastMessage)
                                <p class="text-sm text-gray-500 truncate">{{ $conversation->lastMessage->content }}</p>
                                <p class="text-xs text-gray-400">{{ $conversation->lastMessage->created_at->diffForHumans() }}</p>
                            @else
                                <p class="text-sm text-gray-500">{{ __('Commencez une conversation') }}</p>
                            @endif
                        </div>
                    </div>
                </a>
            @empty
                <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6 text-center text-gray-500">
                    <p>Vous n'avez pas encore de conversations.</p>
                </div>
            @endforelse

            <h2 class="text-2xl font-bold text-gray-900 mt-8 mb-4">{{ __('Démarrer une nouvelle conversation') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($instructors as $instructor)
                    @php
                        $hasConversation = $conversations->contains(function ($conv) use ($instructor) {
                            return ($conv->user1_id === $instructor->id || $conv->user2_id === $instructor->id);
                        });
                    @endphp
                    @if (!$hasConversation)
                        <a href="{{ route('student.messages.show', $instructor->id) }}" class="block bg-black border border-gray-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 p-4 text-center">
                            {{-- <img class="h-16 w-16 rounded-full mx-auto mb-2" src="{{ $instructor->profile_photo_url }}" alt="{{ $instructor->first_name }}"> --}}
                            <p class="text-lg font-semibold text-gray-900">{{ $instructor->first_name }} {{ $instructor->last_name }}</p>
                            <p class="text-sm text-gray-500">{{ __('Instructeur') }}</p>
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</x-frontend-layout>