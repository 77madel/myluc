<x-frontend-layout>
    <x-theme::breadcrumbs.breadcrumb-one pageTitle="Create Topic" :pageRoute="[
        ['name' => 'Forums', 'url' => route('forumsList')],
        ['name' => $forum->title, 'url' => route('forum.detail', $forum->slug)],
    ]" pageName="Create Topic" />

    <div class="container py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h1 class="text-3xl font-extrabold text-gray-900 mb-6">Créer un nouveau sujet dans <span
                            class="text-blue-600">{{ $forum->title }}</span></h1>

                    <form action="{{ route('forum.topic.store', $forum->slug) }}" method="POST" class="space-y-6">
                        @csrf
                        <input type="hidden" name="forum_id" value="{{ $forum->id }}">

                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Titre du sujet
                                <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}"
                                class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition placeholder-gray-400"
                                placeholder="Entrez le titre de votre sujet" required>
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description
                                <span class="text-red-500">*</span></label>
                            <textarea name="description" id="description" rows="7"
                                class="w-full p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 transition placeholder-gray-400"
                                placeholder="Décrivez votre sujet en détail" required>{{ old('description') }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <button type="submit"
                                class="px-6 py-3 bg-blue-600 text-black font-semibold rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                                Créer le sujet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-frontend-layout>
