<x-dashboard-layout>
    <x-slot:title>{{ translate('Créer un Webinaire') }}</x-slot:title>

    <x-portal::admin.breadcrumb title="Webinaires" page-to="Créer un Webinaire" />

    <div class="grid grid-cols-12 gap-x-4 mb-8">
        <div class="col-span-full">
            <div class="relative overflow-hidden rounded-xl shadow-2xl transition-all duration-300 transform hover:scale-[1.005]"
                 style="background: #667eea;">

                <div class="relative p-6 md:p-8 flex items-center justify-between">

                    <div class="absolute inset-y-0 left-0 w-48 h-48 rounded-full bg-white opacity-95 transform -translate-x-1/2 translate-y-1/2"></div>

                    <div class="text-white ml-24 relative z-10 flex-1">
                        <h2 class="text-3xl lg:text-4xl font-extrabold mb-1">
                            {{ translate('Créer un Webinaire') }}
                        </h2>
                        <p class="text-white text-opacity-90 text-lg">
                            {{ translate('Préparez votre prochaine session en direct en remplissant les détails ci-dessous.') }}
                        </p>
                    </div>

                    <div class="flex-shrink-0 relative z-10">
                        <a href="{{ route('webinars.index') }}"
                           class="inline-flex items-center justify-center w-24 h-12 bg-white text-black px-4 py-2.5 rounded-lg border border-white
                                  hover:bg-gray-100 transition-all duration-300 font-medium shadow-md">
                           {{ translate('Retour') }}
                        </a>
                    </div>
                </div>

                <div class="absolute top-1/2 right-0 transform -translate-y-1/2 w-8 h-12 bg-blue-700 rounded-l-lg flex items-center justify-center">
                    <i class="fas fa-cog text-white text-sm"></i>
                </div>

            </div>
        </div>
    </div>

    <div class="grid grid-cols-12 gap-x-6">
        <div class="col-span-full">
            <div >
                <form action="{{ route('webinars.store') }}" method="POST" enctype="multipart/form-data" class="space-y-12">
                    @csrf

                    <div class="bg-white dark:bg-dark-card rounded-xl shadow-lg border border-gray-200 dark:border-dark-border-three overflow-hidden mt-4 mb-4">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-dark-border-three">
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-white flex items-center">
                                {{ translate('Informations Principales') }}
                            </h3>
                        </div>
                        <div class="p-6 md:p-8">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8">

                                <div class="lg:col-span-2">
                                    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="fas fa-heading mr-1 text-blue-500"></i>{{ translate('Titre du Webinaire') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" id="title" name="title" value="{{ old('title') }}" required
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-dark-border-three rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-dark-input dark:text-white transition duration-200 @error('title') border-red-500 @enderror"
                                            placeholder="{{ translate('Ex: Introduction au Développement Web') }}">
                                    @error('title')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="short_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="fas fa-align-left mr-1 text-blue-500"></i>{{ translate('Description Courte') }} <span class="text-red-500">*</span>
                                    </label>
                                    <textarea id="short_description" name="short_description" rows="3" required
                                                class="w-full px-4 py-3 border border-gray-300 dark:border-dark-border-three rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-dark-input dark:text-white transition duration-200 @error('short_description') border-red-500 @enderror"
                                                placeholder="{{ translate('Une brève description du webinaire...') }}">{{ old('short_description') }}</textarea>
                                    @error('short_description')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="instructor_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="fas fa-user-tie mr-1 text-blue-500"></i>{{ translate('Instructeur') }} <span class="text-red-500">*</span>
                                    </label>
                                    <select id="instructor_id" name="instructor_id" required
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-dark-border-three rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-dark-input dark:text-white transition duration-200 @error('instructor_id') border-red-500 @enderror">
                                        <option value="">{{ translate('Sélectionner un instructeur') }}</option>
                                        @foreach($instructors as $instructor)
                                            <option value="{{ $instructor->id }}" {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>
                                                {{ $instructor->username ?? 'Instructeur' }} ({{ $instructor->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('instructor_id')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="lg:col-span-2 mt-2">
                                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="fas fa-align-justify mr-1 text-blue-500"></i>{{ translate('Description Complète') }}
                                    </label>
                                    <textarea id="description" name="description" rows="6"
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-dark-border-three rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-dark-input dark:text-white transition duration-200 @error('description') border-red-500 @enderror"
                                            placeholder="{{ translate('Décrivez en détail le contenu du webinaire...') }}">{{ old('description') }}</textarea>
                                    @error('description')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-dark-card rounded-xl shadow-lg border border-gray-200 dark:border-dark-border-three overflow-hidden  mb-4">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-dark-border-three">
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-white flex items-center">
                                <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center mr-3 shadow-md">
                                    <i class="fas fa-calendar-alt text-white text-sm"></i>
                                </div>
                                {{ translate('Planning et Durée') }}
                            </h3>
                        </div>
                        <div class="p-6 md:p-8">
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">

                                <div>
                                    <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="fas fa-calendar-day mr-1 text-green-500"></i>{{ translate('Date de Début') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="datetime-local" id="start_date" name="start_date" value="{{ old('start_date') }}" required
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-dark-border-three rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-dark-input dark:text-white transition duration-200 @error('start_date') border-red-500 @enderror">
                                    @error('start_date')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="fas fa-calendar-check mr-1 text-green-500"></i>{{ translate('Date de Fin') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="datetime-local" id="end_date" name="end_date" value="{{ old('end_date') }}" required
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-dark-border-three rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-dark-input dark:text-white transition duration-200 @error('end_date') border-red-500 @enderror">
                                    @error('end_date')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="duration" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="fas fa-clock mr-1 text-green-500"></i>{{ translate('Durée (minutes)') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" id="duration" name="duration" value="{{ old('duration', 60) }}" required min="15" max="480"
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-dark-border-three rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-dark-input dark:text-white transition duration-200 @error('duration') border-red-500 @enderror"
                                            placeholder="60">
                                    @error('duration')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-dark-card rounded-xl shadow-lg border border-gray-200 dark:border-dark-border-three overflow-hidden mb-4">
                        <div class="px-6 py-4 border-b border-gray-100 dark:border-dark-border-three">
                            <h3 class="text-xl font-semibold text-gray-800 dark:text-white flex items-center">
                                <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center mr-3 shadow-md">
                                    <i class="fas fa-users text-white text-sm"></i>
                                </div>
                                {{ translate('Participants et Options') }}
                            </h3>
                        </div>
                        <div class="p-6 md:p-8">
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">

                                <div>
                                    <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="fas fa-tags mr-1 text-purple-500"></i>{{ translate('Catégorie') }} <span class="text-red-500">*</span>
                                    </label>
                                    <select id="category_id" name="category_id" required
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-dark-border-three rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-dark-input dark:text-white transition duration-200 @error('category_id') border-red-500 @enderror">
                                        <option value="">{{ translate('Sélectionner une catégorie') }}</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="max_participants" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="fas fa-user-friends mr-1 text-purple-500"></i>{{ translate('Nombre Maximum de Participants') }}
                                    </label>
                                    <input type="number" id="max_participants" name="max_participants" value="{{ old('max_participants', 100) }}" min="1"
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-dark-border-three rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-dark-input dark:text-white transition duration-200 @error('max_participants') border-red-500 @enderror"
                                            placeholder="100">
                                    @error('max_participants')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        <i class="fas fa-toggle-on mr-1 text-purple-500"></i>{{ translate('Statut') }} <span class="text-red-500">*</span>
                                    </label>
                                    <select id="" name="status" required
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-dark-border-three rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-dark-input dark:text-white transition duration-200 @error('status') border-red-500 @enderror">
                                        <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>{{ translate('Brouillon') }}</option>
                                        <option value="scheduled" {{ old('status') == 'scheduled' ? 'selected' : '' }}>{{ translate('Programmé') }}</option>
                                        <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>{{ translate('Publié') }}</option>
                                    </select>

                                    @error('status')
                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="mt-6 pt-6 border-t border-gray-100 dark:border-dark-border-three">
                                <h4 class="text-md font-semibold text-gray-700 dark:text-gray-300 mb-4">{{ translate('Options du Webinaire') }}</h4>
                                <div class="space-y-4 grid grid-cols-1 md:grid-cols-3 gap-6">

                                    <div class="flex items-center p-3 bg-gray-50 dark:bg-dark-border-three rounded-lg border border-gray-200 dark:border-dark-border-three">
                                        <input type="checkbox" id="is_free" name="is_free" value="1" checked disabled
                                                class="form-checkbox h-5 w-5 text-green-600 rounded cursor-not-allowed">
                                        <label for="is_free" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300 cursor-not-allowed">
                                            <i class="fas fa-gift mr-1 text-green-500"></i>{{ translate('Webinaire Gratuit') }} 
                                            <span class="text-xs text-green-600 block leading-none">{{ translate('(Option non modifiable)') }}</span>
                                        </label>
                                    </div>

                                    <div class="flex items-center p-3 bg-gray-50 dark:bg-dark-border-three rounded-lg border border-gray-200 dark:border-dark-border-three">
                                        <input type="checkbox" id="is_recorded" name="is_recorded" value="1" {{ old('is_recorded') ? 'checked' : '' }}
                                                class="form-checkbox h-5 w-5 text-indigo-600 rounded">
                                        <label for="is_recorded" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            <i class="fas fa-video mr-1 text-indigo-500"></i>{{ translate('Enregistrer le Webinaire') }}
                                            <span class="text-xs text-gray-500 dark:text-gray-400 block leading-none">{{ translate('(Disponible en replay)') }}</span>
                                        </label>
                                    </div>

                                    <div class="flex items-center p-3 bg-gray-50 dark:bg-dark-border-three rounded-lg border border-gray-200 dark:border-dark-border-three">
                                        <input type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                                                class="form-checkbox h-5 w-5 text-yellow-500 rounded">
                                        <label for="is_featured" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                            <i class="fas fa-star mr-1 text-yellow-500"></i>{{ translate('Mettre en Vedette') }}
                                            <span class="text-xs text-gray-500 dark:text-gray-400 block leading-none">{{ translate('(Mise en avant sur le site)') }}</span>
                                        </label>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-dark-card rounded-xl shadow-lg border border-gray-200 dark:border-dark-border-three overflow-hidden" mb-4>
                        <div class="p-6">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                                <div class="text-sm text-gray-500 dark:text-gray-400 font-medium">
                                    <i class="fas fa-info-circle mr-1 text-blue-500"></i>
                                    {{ translate('Tous les champs marqués d\'un astérisque (*) sont obligatoires.') }}
                                </div>
                                <div class="flex space-x-3 gap-4" >
                                    <a href="{{ route('webinars.index') }}"
                                       class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-dark-border-three rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-dark-card
                                              hover:bg-gray-100 dark:hover:bg-dark-border-two transition-colors font-semibold shadow-sm">
                                        <i class="fas fa-times mr-2"></i>{{ translate('Annuler') }}
                                    </a>

                                    <button type="submit"
                                            class="inline-flex items-center px-8 py-3 bg-primary from-blue-600 to-purple-600 text-white rounded-lg font-bold shadow-lg
                                                    hover:from-blue-700 hover:to-purple-700 transition-all duration-300 hover:shadow-xl hover:scale-[1.02] transform
                                                    focus:outline-none focus:ring-4 focus:ring-blue-500/50">
                                        <i class="fas fa-save mr-2"></i>{{ translate('Créer le Webinaire') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div> {{-- Fin de la div position-relative --}}
        </div>
    </div>

    @push('styles')
    <style>
        /* Styles de base et accessibilité */
        .w-full:focus {
            outline: 2px solid transparent;
            outline-offset: 2px;
        }
        input[type="datetime-local"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
        }
        .form-checkbox {
            box-shadow: none;
            transition: all 0.2s;
        }
        .form-checkbox:checked {
            background-color: currentColor;
            border-color: currentColor;
        }



        /* Ajustements pour le mode sombre (Dark Mode) */
        .dark .dark\:bg-dark-input { background-color: #1f2937; border-color: #374151; }
        .dark .dark\:text-white { color: #ffffff; }
        .dark .dark\:border-dark-border-three { border-color: #374151; }
        .dark .dark\:bg-dark-card { background-color: #111827; }
    </style>
    @endpush
</x-dashboard-layout>
