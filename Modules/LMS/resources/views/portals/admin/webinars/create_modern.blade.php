<x-dashboard-layout>
    <x-slot:title>{{ translate('Créer un Webinaire') }}</x-slot:title>

    <!-- BREADCRUMB -->
    <x-portal::admin.breadcrumb title="Webinaires" page-to="Créer un Webinaire" />

    <!-- Header Section avec design moderne -->
    <div class="grid grid-cols-12 gap-x-4 mb-6">
        <div class="col-span-full">
            <div class="relative overflow-hidden rounded-2xl shadow-xl" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">
                <div class="absolute inset-0 bg-black opacity-10"></div>
                <div class="relative p-6 md:p-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="text-white">
                            <h2 class="text-3xl font-bold mb-2 flex items-center">
                                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mr-4">
                                    <i class="fas fa-plus text-2xl"></i>
                                </div>
                                {{ translate('Créer un Webinaire') }}
                            </h2>
                            <p class="text-white text-opacity-90 text-lg">
                                {{ translate('Créez un webinaire professionnel en quelques étapes') }}
                            </p>
                        </div>
                        <div class="flex space-x-3">
                            <a href="{{ route('webinars.index') }}" class="inline-flex items-center gap-2 bg-white bg-opacity-20 text-white px-4 py-2 rounded-xl hover:bg-opacity-30 transition-all duration-300">
                                <i class="fas fa-arrow-left"></i>{{ translate('Retour') }}
                            </a>
                        </div>
                    </div>
                </div>
                <!-- Décoration -->
                <div class="absolute top-0 right-0 w-32 h-32 bg-white opacity-5 rounded-full -translate-y-16 translate-x-16"></div>
                <div class="absolute bottom-0 left-0 w-24 h-24 bg-white opacity-5 rounded-full translate-y-12 -translate-x-12"></div>
            </div>
        </div>
    </div>

    <!-- Formulaire de création -->
    <div class="grid grid-cols-12 gap-x-4">
        <div class="col-span-full">
            <form action="{{ route('webinars.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Informations principales -->
                <div class="bg-white dark:bg-dark-card rounded-xl shadow-sm border border-gray-200 dark:border-dark-border-three overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900 dark:to-indigo-900 px-6 py-4 border-b border-gray-200 dark:border-dark-border-three">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-info-circle text-white"></i>
                            </div>
                            {{ translate('Informations Principales') }}
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div class="lg:col-span-2">
                                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-heading mr-1"></i>{{ translate('Titre du Webinaire') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="title" name="title" value="{{ old('title') }}" required
                                       class="form-control w-full px-4 py-3 border border-gray-300 dark:border-dark-border-three rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('title') border-red-500 @enderror"
                                       placeholder="{{ translate('Ex: Introduction au Développement Web') }}">
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="short_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-align-left mr-1"></i>{{ translate('Description Courte') }} <span class="text-red-500">*</span>
                                </label>
                                <textarea id="short_description" name="short_description" rows="3" required
                                          class="form-control w-full px-4 py-3 border border-gray-300 dark:border-dark-border-three rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('short_description') border-red-500 @enderror"
                                          placeholder="{{ translate('Une brève description du webinaire...') }}">{{ old('short_description') }}</textarea>
                                @error('short_description')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="instructor_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-user mr-1"></i>{{ translate('Instructeur') }} <span class="text-red-500">*</span>
                                </label>
                                <select id="instructor_id" name="instructor_id" required
                                        class="form-select w-full px-4 py-3 border border-gray-300 dark:border-dark-border-three rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('instructor_id') border-red-500 @enderror">
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

                            <div>
                                <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-tags mr-1"></i>{{ translate('Catégorie') }} <span class="text-red-500">*</span>
                                </label>
                                <select id="category_id" name="category_id" required
                                        class="form-select w-full px-4 py-3 border border-gray-300 dark:border-dark-border-three rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('category_id') border-red-500 @enderror">
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
                        </div>

                        <div class="mt-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-align-justify mr-1"></i>{{ translate('Description Complète') }}
                            </label>
                            <textarea id="description" name="description" rows="6"
                                      class="form-control w-full px-4 py-3 border border-gray-300 dark:border-dark-border-three rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('description') border-red-500 @enderror"
                                      placeholder="{{ translate('Décrivez en détail le contenu du webinaire...') }}">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Planning et durée -->
                <div class="bg-white dark:bg-dark-card rounded-xl shadow-sm border border-gray-200 dark:border-dark-border-three overflow-hidden">
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900 dark:to-emerald-900 px-6 py-4 border-b border-gray-200 dark:border-dark-border-three">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <div class="w-8 h-8 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-calendar text-white"></i>
                            </div>
                            {{ translate('Planning et Durée') }}
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-calendar-alt mr-1"></i>{{ translate('Date de Début') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" id="start_date" name="start_date" value="{{ old('start_date') }}" required
                                       class="form-control w-full px-4 py-3 border border-gray-300 dark:border-dark-border-three rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('start_date') border-red-500 @enderror">
                                @error('start_date')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-calendar-check mr-1"></i>{{ translate('Date de Fin') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" id="end_date" name="end_date" value="{{ old('end_date') }}" required
                                       class="form-control w-full px-4 py-3 border border-gray-300 dark:border-dark-border-three rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('end_date') border-red-500 @enderror">
                                @error('end_date')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="duration" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-clock mr-1"></i>{{ translate('Durée (minutes)') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="number" id="duration" name="duration" value="{{ old('duration', 60) }}" required min="15" max="480"
                                       class="form-control w-full px-4 py-3 border border-gray-300 dark:border-dark-border-three rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent @error('duration') border-red-500 @enderror"
                                       placeholder="60">
                                @error('duration')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Participants et options -->
                <div class="bg-white dark:bg-dark-card rounded-xl shadow-sm border border-gray-200 dark:border-dark-border-three overflow-hidden">
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 dark:from-purple-900 dark:to-pink-900 px-6 py-4 border-b border-gray-200 dark:border-dark-border-three">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                            <div class="w-8 h-8 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-users text-white"></i>
                            </div>
                            {{ translate('Participants et Options') }}
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <div>
                                <label for="max_participants" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-user-friends mr-1"></i>{{ translate('Nombre Maximum de Participants') }}
                                </label>
                                <input type="number" id="max_participants" name="max_participants" value="{{ old('max_participants', 100) }}" min="1"
                                       class="form-control w-full px-4 py-3 border border-gray-300 dark:border-dark-border-three rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('max_participants') border-red-500 @enderror"
                                       placeholder="100">
                                @error('max_participants')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    <i class="fas fa-toggle-on mr-1"></i>{{ translate('Statut') }} <span class="text-red-500">*</span>
                                </label>
                                <select id="status" name="status" required
                                        class="form-select w-full px-4 py-3 border border-gray-300 dark:border-dark-border-three rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent @error('status') border-red-500 @enderror">
                                    <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>{{ translate('Brouillon') }}</option>
                                    <option value="scheduled" {{ old('status') == 'scheduled' ? 'selected' : '' }}>{{ translate('Programmé') }}</option>
                                    <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>{{ translate('Publié') }}</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6">
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <input type="checkbox" id="is_free" name="is_free" value="1" checked disabled
                                           class="form-checkbox h-4 w-4 text-green-600 transition duration-150 ease-in-out">
                                    <label for="is_free" class="ml-3 block text-sm text-gray-700 dark:text-gray-300">
                                        <i class="fas fa-gift mr-1"></i>{{ translate('Webinaire Gratuit') }}
                                        <span class="text-green-600 font-medium">(Toujours gratuit)</span>
                                    </label>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" id="is_recorded" name="is_recorded" value="1" {{ old('is_recorded') ? 'checked' : '' }}
                                           class="form-checkbox h-4 w-4 text-blue-600 transition duration-150 ease-in-out">
                                    <label for="is_recorded" class="ml-3 block text-sm text-gray-700 dark:text-gray-300">
                                        <i class="fas fa-video mr-1"></i>{{ translate('Enregistrer le Webinaire') }}
                                    </label>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}
                                           class="form-checkbox h-4 w-4 text-yellow-600 transition duration-150 ease-in-out">
                                    <label for="is_featured" class="ml-3 block text-sm text-gray-700 dark:text-gray-300">
                                        <i class="fas fa-star mr-1"></i>{{ translate('Mettre en Vedette') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white dark:bg-dark-card rounded-xl shadow-sm border border-gray-200 dark:border-dark-border-three overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                <i class="fas fa-info-circle mr-1"></i>
                                {{ translate('Tous les champs marqués d\'un astérisque (*) sont obligatoires.') }}
                            </div>
                            <div class="flex space-x-3">
                                <a href="{{ route('webinars.index') }}"
                                   class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-dark-border-three rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-dark-card hover:bg-gray-50 dark:hover:bg-dark-card-shade transition-colors">
                                    <i class="fas fa-times mr-2"></i>{{ translate('Annuler') }}
                                </a>
                                <button type="submit"
                                        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                    <i class="fas fa-save mr-2"></i>{{ translate('Créer le Webinaire') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('styles')
    <style>
        .form-control:focus, .form-select:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-checkbox:checked {
            background-color: currentColor;
            border-color: currentColor;
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
    @endpush
</x-dashboard-layout>





