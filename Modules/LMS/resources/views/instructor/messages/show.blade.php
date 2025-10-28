<x-frontend-layout>
    <x-theme::breadcrumbs.breadcrumb-one :pageTitle="__('Conversation avec :name', ['name' => $student->userable->first_name])" :pageRoute="[['name' => __('Messages'), 'url' => route('instructor.messages.index')]]" :pageName="__('Conversation')" />

    <div class="container py-12">
        <div class="max-w-3xl mx-auto bg-white border border-gray-200 rounded-lg shadow-sm p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">
                {{ __('Conversation avec :name', ['name' => $student->userable->first_name . ' ' . $student->userable->last_name]) }}
            </h2>

            <div class="flex flex-col space-y-4 h-96 overflow-y-auto p-4 border border-gray-300 rounded-lg mb-6"
                id="messages-container">
                @forelse ($messages as $message)
                    <div class="flex @if ($message->sender_id === Auth::id()) justify-end @else justify-start @endif">
                        <div
                            class="flex items-end @if ($message->sender_id === Auth::id()) flex-row-reverse @endif space-x-2 space-x-reverse">
                            <div class="flex-shrink-0">
                                <img class="h-8 w-8 rounded-full"
                                    src="{{ ($message->sender_id === Auth::id() ? Auth::user() : $student)->profile_photo_url }}"
                                    alt="{{ ($message->sender_id === Auth::id() ? Auth::user() : $student)->name }}">
                            </div>
                            <div
                                class="max-w-xs lg:max-w-md px-4 py-2 rounded-lg @if ($message->sender_id === Auth::id()) bg-blue-500 text-white @else bg-gray-200 text-gray-800 @endif">
                                <p class="text-sm">{{ $message->content }}</p>
                                <p
                                    class="text-xs text-right @if ($message->sender_id === Auth::id()) text-blue-200 @else text-gray-500 @endif mt-1">
                                    {{ $message->created_at->diffForHumans() }}
                                    @if ($message->sender_id === Auth::id() && $message->read_at)
                                        <span class="ml-1 text-green-200">✓ Lu</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    <br>
                @empty
                    <div class="text-center text-gray-500 italic">
                        {{ __('Aucun message dans cette conversation. Soyez le premier à envoyer un message !') }}</div>
                @endforelse
            </div>

            <form action="{{ route('instructor.messages.store', $student->id) }}" method="POST"
                class="flex items-center space-x-4">
                @csrf
                <input type="text" name="content" placeholder="Écrivez votre message..."
                    class="flex-1 p-3 border border-gray-300 rounded-lg">
                <button type="submit" class="px-6 py-3 bg-blue-600 text-black rounded-lg">Envoyer</button>
            </form>

        </div>
    </div>
</x-frontend-layout>
