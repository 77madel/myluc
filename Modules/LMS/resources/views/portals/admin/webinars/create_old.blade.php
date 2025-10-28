<x-dashboard-layout>
    <x-slot:title>{{ translate('Créer un Webinaire') }}</x-slot:title>

    <!-- BREADCRUMB -->
    <x-portal::admin.breadcrumb title="Webinaires" page-to="Créer un Webinaire" />

    <!-- Header Section -->
    <div class="grid grid-cols-12 gap-x-4 mb-6">
        <div class="col-span-full">
            <div class="card p-4 md:p-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                            <i class="fas fa-plus-circle text-primary-500 mr-2"></i>
                            {{ translate('Créer un Webinaire') }}
                        </h2>
                        <p class="text-gray-600 dark:text-gray-400">
                            {{ translate('Remplissez les informations pour créer un nouveau webinaire') }}
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
                    <form action="{{ route('webinars.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

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
                                                   id="title" name="title" value="{{ old('title') }}" required>
                                            @error('title')
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                {{ translate('Description') }}
                                            </label>
                                            <textarea class="form-control w-full px-3 py-2 border border-gray-300 dark:border-dark-border-three rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('description') border-red-500 @enderror"
                                                      id="description" name="description" rows="4">{{ old('description') }}</textarea>
                                            @error('description')
                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="short_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                {{ translate('Description Courte') }}
                                            </label>
                                            <textarea class="form-control w-full px-3 py-2 border border-gray-300 dark:border-dark-border-three rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('short_description') border-red-500 @enderror"
                                                      id="short_description" name="short_description" rows="2">{{ old('short_description') }}</textarea>
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
                                                       id="start_date" name="start_date" value="{{ old('start_date') }}" required>
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
                                                       id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                                                @error('end_date')
                                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label for="platform" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    {{ translate('Plateforme') }} <span class="text-red-500">*</span>
                                                </label>
                                                <select class="form-select w-full px-3 py-2 border border-gray-300 dark:border-dark-border-three rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('platform') border-red-500 @enderror"
                                                        id="platform" name="platform" required>
                                                    <option value="">{{ translate('Sélectionner une plateforme') }}</option>
                                                    <option value="zoom" {{ old('platform') == 'zoom' ? 'selected' : '' }}>Zoom</option>
                                                    <option value="teams" {{ old('platform') == 'teams' ? 'selected' : '' }}>Microsoft Teams</option>
                                                    <option value="google_meet" {{ old('platform') == 'google_meet' ? 'selected' : '' }}>Google Meet</option>
                                                    <option value="custom" {{ old('platform') == 'custom' ? 'selected' : '' }}>Personnalisé</option>
                                                </select>
                                                @error('platform')
                                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                                @enderror
                                            </div>
                                            <div>
                                                <label for="max_participants" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    {{ translate('Participants Maximum') }}
                                                </label>
                                                <input type="number"
                                                       class="form-control w-full px-3 py-2 border border-gray-300 dark:border-dark-border-three rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('max_participants') border-red-500 @enderror"
                                                       id="max_participants" name="max_participants" value="{{ old('max_participants', 100) }}" min="1">
                                                @error('max_participants')
                                                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                        <div>
                                            <label for="instructor_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                {{ translate('Instructeur') }} <span class="text-red-500">*</span>
                                            </label>
                                            <select class="form-select w-full px-3 py-2 border border-gray-300 dark:border-dark-border-three rounded-md focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent @error('instructor_id') border-red-500 @enderror"
                                                    id="instructor_id" name="instructor_id" required>
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
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="status" class="form-label">Statut</label>
                                                    <select class="form-select @error('status') is-invalid @enderror"
                                                            id="status" name="status">
                                                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                                                        <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Publié</option>
                                                    </select>
                                                    @error('status')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Options et prix -->
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Options et Prix</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="is_free" name="is_free" value="1"
                                                       {{ old('is_free', true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_free">
                                                    Webinaire Gratuit
                                                </label>
                                            </div>
                                        </div>

                                        <div class="mb-3" id="price_section" style="display: none;">
                                            <label for="price" class="form-label">Prix (FCFA)</label>
                                            <input type="number" class="form-control @error('price') is-invalid @enderror"
                                                   id="price" name="price" value="{{ old('price', 0) }}" min="0" step="0.01">
                                            @error('price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1"
                                                       {{ old('is_featured') ? 'checked' : '' }}>
                                                <label class="form-check-label" for="is_featured">
                                                    Webinaire en Vedette
                                                </label>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="allow_recording" name="allow_recording" value="1"
                                                       {{ old('allow_recording', true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="allow_recording">
                                                    Autoriser l'Enregistrement
                                                </label>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="allow_chat" name="allow_chat" value="1"
                                                       {{ old('allow_chat', true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="allow_chat">
                                                    Autoriser le Chat
                                                </label>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="allow_questions" name="allow_questions" value="1"
                                                       {{ old('allow_questions', true) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="allow_questions">
                                                    Autoriser les Questions
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5>Image du Webinaire</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="thumbnail" class="form-label">Image</label>
                                            <input type="file" class="form-control @error('thumbnail') is-invalid @enderror"
                                                   id="thumbnail" name="thumbnail" accept="image/*">
                                            @error('thumbnail')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('webinars.index') }}" class="btn btn-secondary">
                                        Annuler
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        Créer le Webinaire
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isFreeCheckbox = document.getElementById('is_free');
    const priceSection = document.getElementById('price_section');
    const priceInput = document.getElementById('price');

    function togglePriceSection() {
        if (isFreeCheckbox.checked) {
            priceSection.style.display = 'none';
            priceInput.value = 0;
        } else {
            priceSection.style.display = 'block';
        }
    }

    isFreeCheckbox.addEventListener('change', togglePriceSection);
    togglePriceSection(); // Initial state
});
</script>
@endpush
