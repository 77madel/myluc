<x-frontend-layout>
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home.index') }}">Accueil</a></li>
            <li class="breadcrumb-item"><a href="{{ route('webinar.list') }}">Webinaires</a></li>
            <li class="breadcrumb-item active">Créer</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="text-center">
                <i class="fas fa-video text-primary me-2"></i>
                Créer un Nouveau Webinaire
            </h2>
            <p class="text-center text-muted">Remplissez les informations ci-dessous pour créer votre webinaire</p>
        </div>
    </div>

    <!-- Form -->
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-body p-4">
                    <form action="{{ route('webinar.store') }}" method="POST">
                        @csrf

                        <!-- Titre -->
                        <div class="mb-3">
                            <label for="title" class="form-label">
                                <i class="fas fa-heading text-primary me-1"></i>
                                Titre du Webinaire *
                            </label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror"
                                   id="title" name="title" value="{{ old('title') }}"
                                   placeholder="Ex: Introduction à Laravel" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description courte et Catégorie -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="short_description" class="form-label">
                                        <i class="fas fa-align-left text-primary me-1"></i>
                                        Description Courte
                                    </label>
                                    <textarea class="form-control @error('short_description') is-invalid @enderror"
                                              id="short_description" name="short_description" rows="3"
                                              placeholder="Une brève description">{{ old('short_description') }}</textarea>
                                    @error('short_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category_id" class="form-label">
                                        <i class="fas fa-tags text-primary me-1"></i>
                                        Catégorie
                                    </label>
                                    <select class="form-select @error('category_id') is-invalid @enderror"
                                            id="category_id" name="category_id">
                                        <option value="">Sélectionner une catégorie</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                {{ $category->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Description complète -->
                        <div class="mb-3">
                            <label for="description" class="form-label">
                                <i class="fas fa-align-justify text-primary me-1"></i>
                                Description Complète *
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="4"
                                      placeholder="Décrivez en détail le contenu de votre webinaire" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Dates -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="start_date" class="form-label">
                                        <i class="fas fa-calendar-alt text-primary me-1"></i>
                                        Date et Heure de Début *
                                    </label>
                                    <input type="datetime-local" class="form-control @error('start_date') is-invalid @enderror"
                                           id="start_date" name="start_date" value="{{ old('start_date') }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="end_date" class="form-label">
                                        <i class="fas fa-calendar-check text-primary me-1"></i>
                                        Date et Heure de Fin *
                                    </label>
                                    <input type="datetime-local" class="form-control @error('end_date') is-invalid @enderror"
                                           id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Plateforme et Participants -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="platform" class="form-label">
                                        <i class="fas fa-video text-primary me-1"></i>
                                        Plateforme *
                                    </label>
                                    <select class="form-select @error('platform') is-invalid @enderror"
                                            id="platform" name="platform" required>
                                        <option value="">Sélectionner une plateforme</option>
                                        <option value="zoom" {{ old('platform') == 'zoom' ? 'selected' : '' }}>
                                            Zoom
                                        </option>
                                        <option value="teams" {{ old('platform') == 'teams' ? 'selected' : '' }}>
                                            Microsoft Teams
                                        </option>
                                        <option value="google_meet" {{ old('platform') == 'google_meet' ? 'selected' : '' }}>
                                            Google Meet
                                        </option>
                                        <option value="custom" {{ old('platform') == 'custom' ? 'selected' : '' }}>
                                            Personnalisé
                                        </option>
                                    </select>
                                    @error('platform')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="max_participants" class="form-label">
                                        <i class="fas fa-users text-primary me-1"></i>
                                        Participants Maximum
                                    </label>
                                    <input type="number" class="form-control @error('max_participants') is-invalid @enderror"
                                           id="max_participants" name="max_participants" value="{{ old('max_participants', 100) }}"
                                           min="1" placeholder="100">
                                    @error('max_participants')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Prix -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_free" name="is_free" value="1"
                                               {{ old('is_free') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_free">
                                            <i class="fas fa-gift text-primary me-1"></i>
                                            Webinaire Gratuit
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3" id="price_section" style="display: none;">
                                    <label for="price" class="form-label">
                                        <i class="fas fa-money-bill-wave text-primary me-1"></i>
                                        Prix (FCFA)
                                    </label>
                                    <input type="number" class="form-control @error('price') is-invalid @enderror"
                                           id="price" name="price" value="{{ old('price', 0) }}"
                                           min="0" step="0.01" placeholder="0.00">
                                    @error('price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="{{ route('webinar.list') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-video me-2"></i>
                                Créer le Webinaire
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</x-frontend-layout>

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

    // Auto-calculate end date based on start date
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');

    startDateInput.addEventListener('change', function() {
        if (this.value && !endDateInput.value) {
            const startDate = new Date(this.value);
            startDate.setHours(startDate.getHours() + 1); // Default 1 hour duration
            endDateInput.value = startDate.toISOString().slice(0, 16);
        }
    });

    // Form submission loading state
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Création en cours...';
                submitBtn.disabled = true;
            }
        });
    }
});
</script>
@endpush
