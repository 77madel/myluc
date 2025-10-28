<x-frontend-layout>
    <x-theme::breadcrumbs.breadcrumb-one
        :pageTitle="__('Conversation avec :name', ['name' => $instructor->first_name])"
        :pageRoute="[['name' => __('Messages'), 'url' => route('student.messages.index')]]"
        :pageName="__('Conversation')"
    />

    <div class="container py-12">
        <div class="max-w-4xl mx-auto bg-white border border-gray-200 rounded-2xl shadow-md overflow-hidden">
            <!-- Header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center space-x-3">
                    {{-- <img src="{{ $instructor->profile_photo_url ?? asset('images/default-avatar.png') }}" alt="{{ $instructor->first_name }}"
                         class="w-12 h-12 rounded-full object-cover border border-gray-300"> --}}
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">
                            {{ $instructor->first_name . ' ' . $instructor->last_name }}
                        </h2>
                        <p class="text-sm text-gray-500">Formateur</p>
                    </div>
                </div>
                <a href="{{ route('student.messages.index') }}"
                   class="text-sm text-blue-600 hover:underline">← Retour</a>
            </div>

            <!-- Messages -->
            <div id="messages-container" class="p-6 h-[480px] overflow-y-auto bg-gray-50 space-y-4 scroll-smooth">
                @forelse ($messages as $message)
                    <div class="flex {{ $message->sender_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
                        <div class="flex items-end {{ $message->sender_id === Auth::id() ? 'flex-row-reverse space-x-reverse' : '' }} space-x-2">
                            {{-- <img class="h-9 w-9 rounded-full object-cover border border-gray-300"
                                 src="{{ ($message->sender_id === Auth::id() ? Auth::user() : $instructor)->profile_photo_url ?? asset('images/default-avatar.png') }}"
                                 alt="{{ ($message->sender_id === Auth::id() ? Auth::user() : $instructor)->name }}"> --}}
                            <div class="max-w-xs lg:max-w-md px-4 py-3 rounded-2xl shadow-sm
                                {{ $message->sender_id === Auth::id() ? 'bg-blue-600 text-black' : 'bg-white border border-gray-200 text-gray-800' }}">
                                <p class="text-sm leading-relaxed">{{ $message->content }}</p>
                                <div class="text-right mt-1">
                                    <span class="text-xs opacity-75">
                                        {{ $message->created_at->format('H:i') }}
                                    </span>
                                    @if ($message->sender_id === Auth::id() && $message->read_at)
                                        <span class="ml-1 text-green-300">✓ Lu</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-gray-500 italic py-10">
                        {{ __('Aucun message dans cette conversation. Soyez le premier à envoyer un message !') }}
                    </div>
                @endforelse
            </div>

            <!-- Formulaire d’envoi -->
            <form action="{{ route('student.messages.store', $instructor->id) }}" method="POST"
                  class="flex items-center space-x-3 px-6 py-4 bg-white border-t border-gray-200">
                @csrf
                <input type="text"
                       name="content"
                       placeholder="{{ __('Écrivez votre message...') }}"
                       class="flex-1 p-3 border border-gray-300 rounded-full focus:ring-2 focus:ring-blue-500 focus:outline-none">
                <button type="submit"
                        class="p-3 rounded-full bg-blue-600 hover:bg-blue-700 transition text-black">
                    <i class="ti ti-send text-xl">Envoyer votre Message</i>
                </button>
            </form>
        </div>
    </div>

    <script>
        // Défilement automatique vers le bas à chaque chargement
        const messagesContainer = document.getElementById('messages-container');
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    </script>
</x-frontend-layout>
