<x-dashboard-layout>
    <x-slot:title>{{ translate('Modifier le Webinaire') }}</x-slot:title>

    <!-- BREADCRUMB -->
    <x-portal::admin.breadcrumb title="Webinaires" page-to="Modifier" />

    <!-- Header Section -->
    <div class="grid grid-cols-12 gap-x-4 mb-6">
        <div class="col-span-full">
            <div class="card p-4 md:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                            <i class="fas fa-edit text-primary-500 mr-2"></i>
                            {{ translate('Modifier le Webinaire') }}
                        </h2>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ translate('Modifiez les informations de votre webinaire') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-12 gap-x-4">
        <div class="col-span-full">
            <div class="card overflow-hidden">
                <div class="card-header bg-[#F2F4F9] dark:bg-dark-card-two p-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        <i class="fas fa-info-circle mr-2 text-primary-500"></i>
                        {{ translate('Informations du Webinaire') }}
                    </h3>
                </div>
                <div class="card-body p-6">
                    <form action="{{ route('webinars.update', $webinar) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Informations de base -->
                            <div class="lg:col-span-2">
                                <div class="bg-white dark:bg-dark-card rounded-lg border border-gray-200 dark:border-dark-border-three overflow-hidden">
                                    <div class="bg-gray-50 dark:bg-dark-card-shade px-4 py-3 border-b border-gray-200 dark:border-dark-border-three">
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            <i class="fas fa-info-circle mr-2 text-primary-500"></i>
                                            {{ translate('Informations du Webinaire') }}
                                        </h4>
                                    </div>
                                    <div class="p-6 space-y-6">
                                        <div>
                                            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                {{ translate('Titre du Webinaire') }} <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text"
                                                   class="form-control w-full px-3 py-2 border border-gray-300 dark:border-dark-border-three rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('title') border-red-500 @enderror"
                                                   id="title" name="title" value="{{ old('title', $webinar->title) }}" required>
                                            @error('title')
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                {{ translate('Description') }}
                                            </label>
                                            <textarea class="form-control w-full px-3 py-2 border border-gray-300 dark:border-dark-border-three rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('description') border-red-500 @enderror"
                                                      id="description" name="description" rows="4">{{ old('description', $webinar->description) }}</textarea>
                                            @error('description')
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="short_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                {{ translate('Description Courte') }}
                                            </label>
                                            <textarea class="form-control w-full px-3 py-2 border border-gray-300 dark:border-dark-border-three rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('short_description') border-red-500 @enderror"
                                                      id="short_description" name="short_description" rows="2"
>{{ old('short_description', $webinar->short_description) }}</textarea>
                                            @error('short_description')
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label for="start_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    {{ translate('Date de Début') }} <span class="text-red-500">*</span>
                                                </label>
                                                <input type="datetime-local"
                                                       class="form-control w-full px-3 py-2 border border-gray-300 dark:border-dark-border-three rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('start_date') border-red-500 @enderror"
                                                       id="start_date" name="start_date" value="{{ old('start_date', $webinar->start_date ? \Carbon\Carbon::parse($webinar->start_date)->format('Y-m-d\TH:i') : '') }}" required
>
                                                @error('start_date')
                                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <div>
                                                <label for="end_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    {{ translate('Date de Fin') }} <span class="text-red-500">*</span>
                                                </label>
                                                <input type="datetime-local"
                                                       class="form-control w-full px-3 py-2 border border-gray-300 dark:border-dark-border-three rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('end_date') border-red-500 @enderror"
                                                       id="end_date" name="end_date" value="{{ old('end_date', $webinar->end_date ? \Carbon\Carbon::parse($webinar->end_date)->format('Y-m-d\TH:i') : '') }}" required
>
                                                @error('end_date')
                                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label for="meeting_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    {{ translate('URL de la Réunion') }}
                                                </label>
                                                <input type="url"
                                                       class="form-control w-full px-3 py-2 border border-gray-300 dark:border-dark-border-three rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('meeting_url') border-red-500 @enderror"
                                                       id="meeting_url" name="meeting_url" value="{{ old('meeting_url', $webinar->meeting_url) }}"
>
                                                @error('meeting_url')
                                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <div>
                                                <label for="max_participants" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    {{ translate('Participants Maximum') }}
                                                </label>
                                                <input type="number"
                                                       class="form-control w-full px-3 py-2 border border-gray-300 dark:border-dark-border-three rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('max_participants') border-red-500 @enderror"
                                                       id="max_participants" name="max_participants" value="{{ old('max_participants', $webinar->max_participants) }}" min="1"
>
                                                @error('max_participants')
                                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Options et paramètres -->
                            <div class="lg:col-span-1">
                                <div class="bg-white dark:bg-dark-card rounded-lg border border-gray-200 dark:border-dark-border-three overflow-hidden">
                                    <div class="bg-gray-50 dark:bg-dark-card-shade px-4 py-3 border-b border-gray-200 dark:border-dark-border-three">
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            <i class="fas fa-cog mr-2 text-primary-500"></i>
                                            {{ translate('Options et Paramètres') }}
                                        </h4>
                                    </div>
                                    <div class="p-6 space-y-6">
                                        <div>
                                            <label for="instructor_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                {{ translate('Instructeur') }} <span class="text-red-500">*</span>
                                            </label>
                                            <select class="form-select w-full px-3 py-2 border border-gray-300 dark:border-dark-border-three rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('instructor_id') border-red-500 @enderror"
                                                    id="instructor_id" name="instructor_id" required
>
                                                <option value="">{{ translate('Sélectionner un instructeur') }}</option>
                                                @foreach($instructors as $instructor)
                                                    @if($instructor->user)
                                                        <option value="{{ $instructor->user->id }}" {{ old('instructor_id', $webinar->instructor_id) == $instructor->user->id ? 'selected' : '' }}>
                                                            {{ $instructor->user->full_name ?? $instructor->user->username ?? 'Instructeur' }} ({{ $instructor->user->email }})
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            @error('instructor_id')
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="category_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                {{ translate('Catégorie') }}
                                            </label>
                                            <select class="form-select w-full px-3 py-2 border border-gray-300 dark:border-dark-border-three rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('category_id') border-red-500 @enderror"
                                                    id="category_id" name="category_id"
>
                                                <option value="">{{ translate('Sélectionner une catégorie') }}</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ old('category_id', $webinar->category_id) == $category->id ? 'selected' : '' }}>
                                                        {{ $category->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('category_id')
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div style="margin-bottom: 2rem; position: relative; z-index: 1;">
                                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                {{ translate('Statut') }}
                                            </label>
                                            <select class="form-select w-full px-3 py-2 border border-gray-300 dark:border-dark-border-three rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('status') border-red-500 @enderror"
                                                    id="" name="status" style="position: relative; z-index: 1; background-color: transparent !important; border-color: #4b5563 !important; color: #ffffff !important;">
                                                <option value="draft" {{ old('status', $webinar->status) == 'draft' ? 'selected' : '' }}>{{ translate('Brouillon') }}</option>
                                                <option value="scheduled" {{ old('status', $webinar->status) == 'scheduled' ? 'selected' : '' }}>{{ translate('Programmé') }}</option>
                                                <option value="published" {{ old('status', $webinar->status) == 'published' ? 'selected' : '' }}>{{ translate('Publié') }}</option>
                                                <option value="completed" {{ old('status', $webinar->status) == 'completed' ? 'selected' : '' }}>{{ translate('Terminé') }}</option>
                                                <option value="cancelled" {{ old('status', $webinar->status) == 'cancelled' ? 'selected' : '' }}>{{ translate('Annulé') }}</option>
                                            </select>
                                            @error('status')
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="space-y-4">
                                            <div class="flex items-center">
                                                <input type="checkbox" id="is_free" name="is_free" value="1"
                                                       class="form-checkbox h-4 w-4 text-primary-600 transition duration-150 ease-in-out"
                                                       checked disabled
                                                       style="background-color: transparent !important; border-color: #4b5563 !important;">
                                                <label for="is_free" class="ml-2 block text-sm text-gray-700 dark:text-gray-300"
>
                                                    {{ translate('Webinaire Gratuit') }} <span class="text-green-600 font-medium">(Toujours gratuit)</span>
                                                </label>
                                            </div>

                                            <div class="flex items-center">
                                                <input type="checkbox" id="is_recorded" name="is_recorded" value="1"
                                                       class="form-checkbox h-4 w-4 text-primary-600 transition duration-150 ease-in-out"
                                                       {{ old('is_recorded', $webinar->is_recorded) ? 'checked' : '' }}
                                                       style="background-color: transparent !important; border-color: #4b5563 !important;">
                                                <label for="is_recorded" class="ml-2 block text-sm text-gray-700 dark:text-gray-300"
>
                                                    {{ translate('Enregistrement Autorisé') }}
                                                </label>
                                            </div>

                                            <div class="flex items-center">
                                                <input type="checkbox" id="is_published" name="is_published" value="1"
                                                       class="form-checkbox h-4 w-4 text-primary-600 transition duration-150 ease-in-out"
                                                       {{ old('is_published', $webinar->is_published) ? 'checked' : '' }}
                                                       style="background-color: transparent !important; border-color: #4b5563 !important;">
                                                <label for="is_published" class="ml-2 block text-sm text-gray-700 dark:text-gray-300"
>
                                                    {{ translate('Publié') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="flex justify-end space-x-4 mt-8">
                            <a href="{{ route('webinars.index') }}" class="btn btn-secondary">
                                {{ translate('Annuler') }}
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-2"></i>
                                {{ translate('Mettre à jour') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>

<style>
/* CORRECTION MODE SOMBRE - FORMULAIRE DE MODIFICATION */
/* Forcer la transparence en mode sombre */
.dark .form-control,
.dark .form-select,
.dark input,
.dark textarea,
.dark select {
    background-color: transparent !important;
    border-color: #4b5563 !important;
    color: #ffffff !important;
}

.dark .form-checkbox {
    background-color: transparent !important;
    border-color: #4b5563 !important;
}

.dark label {
    color: #d1d5db !important;
}

/* Protection du mode clair */
html:not(.dark) .form-control,
html:not(.dark) .form-select,
html:not(.dark) input,
html:not(.dark) textarea,
html:not(.dark) select {
    background-color: #ffffff !important;
    border-color: #d1d5db !important;
    color: #111827 !important;
}

/* Correction du positionnement des selects */
.form-select {
    position: relative;
    z-index: 1;
    background-color: white;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
    line-height: 1.25rem;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
    background-position: right 0.5rem center;
    background-repeat: no-repeat;
    background-size: 1.5em 1.5em;
    padding-right: 2.5rem;
}

.form-select:focus {
    z-index: 10;
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Correction spécifique pour le select du statut */
#status {
    position: relative;
    z-index: 1;
    background-color: white;
    margin-bottom: 1rem;
}

#status:focus {
    z-index: 1000;
    position: relative;
}

/* Amélioration de l'espacement des sections */
.space-y-4 > * + * {
    margin-top: 1rem;
}

.space-y-6 > * + * {
    margin-top: 1.5rem;
}

/* Correction du positionnement des colonnes */
.lg\:col-span-1 {
    position: relative;
    z-index: 1;
}

.lg\:col-span-2 {
    position: relative;
    z-index: 1;
}

/* Amélioration de l'espacement entre les éléments de formulaire */
.form-control, .form-select {
    margin-bottom: 1rem;
}

/* Correction du z-index pour éviter les chevauchements */
.grid {
    position: relative;
    z-index: 1;
}

.grid .form-select {
    position: relative;
    z-index: 1;
}

.grid .form-select:focus {
    z-index: 1000;
    position: relative;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Les webinaires sont toujours gratuits
    console.log('Webinaires toujours gratuits - Aucune gestion de prix nécessaire');
});
</script>
