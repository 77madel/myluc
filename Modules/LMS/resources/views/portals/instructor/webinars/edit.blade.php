<x-dashboard-layout>
    <x-slot name="title">
        {{ translate('Modifier le Webinaire') }}
    </x-slot>

    {{-- Fil d'Ariane --}}
    <x-portal::admin.breadcrumb>
        <x-slot name="title">{{ translate('Modifier le Webinaire') }}</x-slot>
        <li class="breadcrumb-item">
            <a href="{{ route('instructor.dashboard') }}">{{ translate('Dashboard') }}</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('instructor.webinars.index') }}">{{ translate('Webinaires') }}</a>
        </li>
        <li class="breadcrumb-item active">{{ translate('Modifier') }}</li>
    </x-portal::admin.breadcrumb>

    <div class="container-fluid create-webinar">
        <div class="row">
            <div class="col-12">
                {{-- En-tête de page moderne --}}
                <div class="mb-4">
                    <div class="relative overflow-hidden rounded-2xl shadow-xl" style="background:#4f46e5;">
                        <div class="position-absolute w-100 h-100" style="inset:0;background:rgba(0,0,0,.08);"></div>
                        <div class="position-relative p-4 p-md-5 text-white">
                            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between gap-3">
                                <div>
                                    <h2 class="mb-2 d-flex align-items-center fw-bold" style="font-size:1.8rem;">
                                        <span class="d-inline-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;background:rgba(255,255,255,.2);border-radius:12px;">
                                            <i class="fas fa-edit" style="font-size:1.2rem;"></i>
                                        </span>
                                        {{ translate('Modifier le Webinaire') }}
                                    </h2>
                                    <p class="mb-0" style="opacity:.95;">{{ $webinar->title }}</p>
                                </div>
                                <div>
                                    <a href="{{ route('instructor.webinars.index') }}" class="btn btn-light" style="background:rgba(255,255,255,.2);border:none;border-radius:12px;backdrop-filter: blur(2px);color:#fff;">
                                        <i class="fas fa-arrow-left me-2"></i>{{ translate('Retour') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        {{-- Affichage des erreurs de validation --}}
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Formulaire d'édition --}}
                        <form action="{{ route('instructor.webinars.update', $webinar->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row g-4">
                                {{-- Colonne principale : Détails, Planification, Média --}}
                                <div class="col-lg-8">
                                    <div class="card form-section mb-4 border-0 dark:bg-gray-800 dark:border-gray-700">
                                        <div class="card-body p-4">
                                            {{-- Détails du webinaire --}}
                                            <div class="section-box mb-4">
                                                <div class="section-heading mb-3">
                                                    <h5 class="mb-1 dark:text-white"><i class="fas fa-edit me-2 text-primary"></i>{{ translate('Détails du webinaire') }}</h5>
                                                    <small class="text-muted dark:text-gray-400">{{ translate('Modifiez les informations du webinaire') }}</small>
                                                </div>

                                                <div class="mb-4">
                                                    <label for="title" class="form-label fw-semibold dark:text-gray-300">{{ translate('Titre du Webinaire') }} <span class="text-danger">*</span></label>
                                                    <div class="input-group input-group-lg">
                                                        <span class="input-group-text bg-white dark:bg-gray-700 dark:border-gray-600"><i class="fas fa-heading text-muted dark:text-gray-400"></i></span>
                                                        <input type="text" class="form-control dark:bg-transparent dark:text-white dark:border-gray-600" id="title" name="title"
                                                               value="{{ old('title', $webinar->title) }}" required placeholder="Ex: Introduction à la programmation Python">
                                                    </div>
                                                    <div class="form-text mt-2 dark:text-gray-400">{{ translate('Choisissez un titre accrocheur et descriptif') }}</div>
                                                </div>

                                                <div class="mb-4">
                                                    <label for="short_description" class="form-label fw-bold dark:text-gray-300">{{ translate('Description courte') }} <span class="text-danger">*</span></label>
                                                    <textarea class="form-control sleek dark:bg-transparent dark:text-white dark:border-gray-600" id="short_description" name="short_description"
                                                               rows="3" maxlength="500" required placeholder="Résumé en quelques lignes de ce que vous allez couvrir dans ce webinaire...">{{ old('short_description', $webinar->short_description) }}</textarea>
                                                    <div class="d-flex justify-content-between align-items-center mt-1">
                                                        <div class="form-text dark:text-gray-400">{{ translate('Maximum 500 caractères - Cette description apparaîtra dans la liste des webinaires') }}</div>
                                                        <span class="badge rounded-pill bg-light text-secondary border dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600" id="short-desc-counter">0/500</span>
                                                    </div>
                                                </div>

                                                <div class="mb-4">
                                                    <label for="description" class="form-label fw-bold dark:text-gray-300">{{ translate('Description complète') }} <span class="text-danger">*</span></label>
                                                    <textarea class="form-control form-control-lg sleek dark:bg-transparent dark:text-white dark:border-gray-600" id="description" name="description"
                                                               rows="8" required placeholder="Décrivez en détail le contenu de votre webinaire, les objectifs d'apprentissage, le programme...">{{ old('description', $webinar->description) }}</textarea>
                                                    <div class="form-text dark:text-gray-400">{{ translate('Cette description détaillée sera visible par les participants') }}</div>
                                                </div>
                                            </div>

                                            <div class="section-divider"></div>

                                            {{-- Planification --}}
                                            <div class="section-box mb-4">
                                                <div class="section-heading mb-3">
                                                    <h5 class="mb-1 dark:text-white"><i class="fas fa-calendar-alt me-2 text-primary"></i>{{ translate('Planification') }}</h5>
                                                    <small class="text-muted dark:text-gray-400">{{ translate('Définissez les dates et la durée') }}</small>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="start_date" class="form-label fw-semibold dark:text-gray-300">{{ translate('Date de début') }} <span class="text-danger">*</span></label>
                                                            <div class="input-group">
                                                                <span class="input-group-text bg-white dark:bg-gray-700 dark:border-gray-600"><i class="far fa-clock text-muted dark:text-gray-400"></i></span>
                                                                <input type="datetime-local" class="form-control dark:bg-transparent dark:text-white dark:border-gray-600" id="start_date" name="start_date"
                                                                       value="{{ old('start_date', $webinar->start_date->format('Y-m-d\TH:i')) }}" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="end_date" class="form-label fw-semibold dark:text-gray-300">{{ translate('Date de fin') }} <span class="text-danger">*</span></label>
                                                            <div class="input-group">
                                                                <span class="input-group-text bg-white dark:bg-gray-700 dark:border-gray-600"><i class="far fa-calendar text-muted dark:text-gray-400"></i></span>
                                                                <input type="datetime-local" class="form-control dark:bg-transparent dark:text-white dark:border-gray-600" id="end_date" name="end_date"
                                                                       value="{{ old('end_date', $webinar->end_date->format('Y-m-d\TH:i')) }}" required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="duration" class="form-label fw-semibold dark:text-gray-300">{{ translate('Durée (minutes)') }} <span class="text-danger">*</span></label>
                                                            <input type="number" class="form-control dark:bg-transparent dark:text-white dark:border-gray-600" id="duration" name="duration"
                                                                   value="{{ old('duration', $webinar->duration) }}" min="15" required>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="mb-3">
                                                            <label for="max_participants" class="form-label fw-semibold dark:text-gray-300">{{ translate('Participants maximum') }}</label>
                                                            <input type="number" class="form-control dark:bg-transparent dark:text-white dark:border-gray-600" id="max_participants" name="max_participants"
                                                                   value="{{ old('max_participants', $webinar->max_participants) }}" min="1">
                                                            <div class="form-text dark:text-gray-400">{{ translate('Laisser vide pour illimité') }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="section-divider"></div>

                                            {{-- Catégorie et média --}}
                                            <div class="section-box mb-4">
                                                <div class="section-heading mb-3">
                                                    <h5 class="mb-1 dark:text-white"><i class="fas fa-tags me-2 text-primary"></i>{{ translate('Catégorie et média') }}</h5>
                                                    <small class="text-muted dark:text-gray-400">{{ translate('Aidez les étudiants à trouver votre webinaire') }}</small>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="category_id" class="form-label fw-semibold dark:text-gray-300">{{ translate('Catégorie') }} <span class="text-danger">*</span></label>
                                                    <select class="form-select dark:bg-transparent dark:text-white dark:border-gray-600" id="category_id" name="category_id" required>
                                                        <option value="">{{ translate('Sélectionner une catégorie') }}</option>
                                                        @foreach($categories as $category)
                                                            <option value="{{ $category->id }}"
                                                                    {{ old('category_id', $webinar->category_id) == $category->id ? 'selected' : '' }}>
                                                                {{ $category->title }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="meeting_url" class="form-label fw-semibold dark:text-gray-300">{{ translate('URL de la réunion (collez votre lien)') }}</label>
                                                    <input type="url" class="form-control dark:bg-transparent dark:text-white dark:border-gray-600" id="meeting_url" name="meeting_url"
                                                           value="{{ old('meeting_url', $webinar->meeting_url) }}" placeholder="https://...">
                                                    <div class="form-text dark:text-gray-400">{{ translate('Créez votre réunion sur Teams, Zoom ou Google Meet puis collez le lien ici.') }}</div>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="image" class="form-label fw-semibold dark:text-gray-300">{{ translate('Image du webinaire') }}</label>
                                                    @if($webinar->image)
                                                        <div class="mb-2">
                                                            <img src="{{ Storage::url($webinar->image) }}" alt="{{ $webinar->title }}"
                                                                 class="img-thumbnail dark:border-gray-600" style="max-width: 200px;">
                                                            <div class="form-text dark:text-gray-400">{{ translate('Image actuelle') }}</div>
                                                        </div>
                                                    @endif
                                                    <input type="file" class="form-control dark:bg-transparent dark:text-white dark:border-gray-600" id="image" name="image"
                                                           accept="image/jpeg,image/png,image/jpg,image/gif">
                                                    <div class="form-text dark:text-gray-400">{{ translate('Formats acceptés: JPEG, PNG, JPG, GIF (max 2MB)') }}</div>
                                                    <div class="mt-3">
                                                        <img id="image-preview" class="img-fluid rounded d-none" alt="preview" />
                                                    </div>
                                                </div>

                                                <div class="mb-0">
                                                    <div class="form-check">
                                                        <input class="form-check-input dark:bg-transparent dark:border-gray-600" type="checkbox" id="is_recorded" name="is_recorded"
                                                               {{ old('is_recorded', $webinar->is_recorded) ? 'checked' : '' }}>
                                                        <label class="form-check-label dark:text-gray-300" for="is_recorded">
                                                            {{ translate('Enregistrer le webinaire') }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Colonne latérale : Informations --}}
                                <div class="col-lg-4">
                                    <div class="card border-0 shadow-sm bg-gradient-light dark:bg-gray-800 sticky-side">
                                        <div class="card-header bg-transparent dark:bg-gray-900 border-0">
                                            <h6 class="mb-0 fw-bold text-dark dark:text-white">
                                                <i class="fas fa-info-circle text-primary me-2"></i>
                                                {{ translate('Informations') }}
                                            </h6>
                                        </div>
                                        <div class="card-body dark:bg-gray-800">
                                            <div class="mb-4">
                                                <label class="form-label fw-bold dark:text-gray-300">{{ translate('Type de webinaire') }}</label>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-success rounded-pill px-3 py-2 me-2">
                                                        <i class="fas fa-gift me-1"></i>
                                                        {{ translate('Gratuit') }}
                                                    </span>
                                                </div>
                                                <div class="form-text dark:text-gray-400">{{ translate('Les webinaires d\'instructeur sont toujours gratuits') }}</div>
                                            </div>

                                            <div class="mb-4">
                                                <label class="form-label fw-bold dark:text-gray-300">{{ translate('Statut') }}</label>
                                                <div class="d-flex align-items-center">
                                                    @if($webinar->is_published)
                                                        <span class="badge bg-success rounded-pill px-3 py-2 me-2">
                                                            <i class="fas fa-check me-1"></i>
                                                            {{ translate('Publié') }}
                                                        </span>
                                                    @else
                                                        <span class="badge bg-warning rounded-pill px-3 py-2 me-2">
                                                            <i class="fas fa-edit me-1"></i>
                                                            {{ translate('Brouillon') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="mb-4">
                                                <label class="form-label fw-bold dark:text-gray-300">{{ translate('Participants actuels') }}</label>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-primary rounded-pill px-3 py-2">
                                                        <i class="fas fa-users me-1"></i>
                                                        {{ $webinar->current_participants }}
                                                    </span>
                                                </div>
                                            </div>

                                            @if($webinar->meeting_url)
                                                <div class="mb-4">
                                                    <label class="form-label fw-bold dark:text-gray-300">{{ translate('Lien de réunion') }}</label>
                                                    <a href="{{ $webinar->meeting_url }}" target="_blank" class="btn btn-sm btn-outline-primary w-100 dark:bg-blue-900 dark:border-blue-700 dark:text-blue-200">
                                                        <i class="fas fa-external-link-alt me-1"></i> {{ translate('Ouvrir') }}
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Barre d'action fixe en bas --}}
                            <div class="action-bar dark:bg-gray-800 dark:border-gray-700">
                                <div class="container-fluid px-0 d-flex justify-content-between align-items-center">
                                    <a href="{{ route('instructor.webinars.index') }}" class="btn btn-secondary dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                                        <i class="fas fa-arrow-left"></i> {{ translate('Retour') }}
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> {{ translate('Mettre à jour') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* STYLES DE BASE - MODE CLAIR */
        .create-webinar .card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }

        .create-webinar .card-body {
            padding: 2rem;
        }

        .create-webinar .form-section {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }

        .create-webinar .section-box {
            padding: 0;
            margin-bottom: 0;
        }

        .create-webinar .section-heading {
            margin-bottom: 1.5rem;
        }

        .create-webinar .section-heading h5 {
            font-size: 1.2rem;
            font-weight: 600;
            color: #1f2937;
            display: flex;
            align-items: center;
        }

        .create-webinar .section-heading small {
            font-size: 0.875rem;
            color: #6b7280;
        }

        .create-webinar .section-divider {
            border-top: 2px solid #e5e7eb;
            margin: 2rem 0;
        }

        .create-webinar .form-control,
        .create-webinar .form-select,
        .create-webinar input,
        .create-webinar textarea,
        .create-webinar select {
            width: 100% !important;
            max-width: 100% !important;
            background-color: #ffffff;
            border: 2px solid #d1d5db;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            color: #1f2937;
            transition: all 0.2s ease;
            box-sizing: border-box;
        }

        .create-webinar .form-control:focus,
        .create-webinar input:focus,
        .create-webinar textarea:focus,
        .create-webinar select:focus {
            outline: none;
            border-color: #4f46e5;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .create-webinar .input-group {
            display: flex;
            width: 100% !important;
            max-width: 100% !important;
        }

        .create-webinar .input-group .form-control,
        .create-webinar .input-group input {
            flex: 1 !important;
            width: auto !important;
        }

        .create-webinar .input-group-text {
            background-color: #f3f4f6;
            border: 2px solid #d1d5db;
            border-right: none;
            padding: 0.75rem 1rem;
            border-radius: 8px 0 0 8px;
            display: flex;
            align-items: center;
        }

        .create-webinar .input-group .form-control {
            border-radius: 0 8px 8px 0;
            border-left: none;
        }

        .create-webinar .form-label,
        .create-webinar label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
            display: block;
        }

        .create-webinar .form-text {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 0.5rem;
        }

        .create-webinar .badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
        }

        .action-bar {
            position: sticky;
            bottom: 0;
            background: #ffffff;
            padding: 1rem;
            border-top: 2px solid #e5e7eb;
            margin-top: 2rem;
            z-index: 10;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
        }

        /* Styles des boutons */
        .create-webinar .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            border: 2px solid transparent;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .create-webinar .btn-primary {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%);
            color: #ffffff;
            border-color: #4f46e5;
            box-shadow: 0 2px 4px rgba(79, 70, 229, 0.3);
        }

        .create-webinar .btn-primary:hover {
            background: linear-gradient(135deg, #4338ca 0%, #4f46e5 100%);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(79, 70, 229, 0.4);
        }

        .create-webinar .btn-secondary {
            background: #f3f4f6;
            color: #374151;
            border-color: #d1d5db;
        }

        .create-webinar .btn-secondary:hover {
            background: #e5e7eb;
            border-color: #9ca3af;
        }

        .sticky-side {
            position: sticky;
            top: 88px;
        }

        .sticky-side .card {
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        }

        .sticky-side .card-header {
            background: #ffffff;
            border-bottom: 2px solid #e5e7eb;
            padding: 1rem 1.5rem;
            border-radius: 12px 12px 0 0;
        }

        .sticky-side .card-body {
            padding: 1.5rem;
        }

        /* Form check */
        .create-webinar .form-check-input {
            width: 1.2rem !important;
            height: 1.2rem !important;
            border: 2px solid #d1d5db;
        }

        /* Forcer la largeur complète pour tous les conteneurs de champs */
        .create-webinar .mb-3,
        .create-webinar .mb-4 {
            width: 100% !important;
        }

        .create-webinar .col-md-6 input,
        .create-webinar .col-md-6 select,
        .create-webinar .col-md-6 textarea {
            width: 100% !important;
        }

        /* MODE SOMBRE - FORMULAIRE INSTRUCTEUR EDIT */
        .dark .create-webinar .card {
            background: #1f2937 !important;
            border: 1px solid #374151 !important;
            box-shadow: none !important;
        }

        .dark .create-webinar .card-body {
            background: #1f2937 !important;
        }

        .dark .create-webinar .form-section {
            background: #1f2937 !important;
            border: 1px solid #374151 !important;
            box-shadow: none !important;
        }

        .dark .create-webinar .section-divider {
            border-color: #374151 !important;
        }

        .dark .create-webinar .section-heading h5 {
            color: #f3f4f6 !important;
        }

        .dark .create-webinar .section-heading small {
            color: #9ca3af !important;
        }

        .dark .create-webinar .form-control,
        .dark .create-webinar .form-select,
        .dark .create-webinar input,
        .dark .create-webinar textarea,
        .dark .create-webinar select {
            width: 100% !important;
            max-width: 100% !important;
            background-color: #374151 !important;
            border-color: #4b5563 !important;
            color: #ffffff !important;
            box-sizing: border-box !important;
        }

        .dark .create-webinar .form-control:focus,
        .dark .create-webinar input:focus,
        .dark .create-webinar textarea:focus,
        .dark .create-webinar select:focus {
            border-color: #6366f1 !important;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2) !important;
        }

        .dark .create-webinar .input-group-text {
            background-color: #4b5563 !important;
            border-color: #4b5563 !important;
            color: #e5e7eb !important;
        }

        .dark .create-webinar .form-label,
        .dark .create-webinar label {
            color: #e5e7eb !important;
        }

        .dark .create-webinar .form-text {
            color: #9ca3af !important;
        }

        .dark .form-control::placeholder,
        .dark input::placeholder,
        .dark textarea::placeholder {
            color: #9ca3af !important;
        }

        .dark .form-label,
        .dark label {
            color: #d1d5db !important;
        }

        .dark .form-text,
        .dark .text-muted {
            color: #9ca3af !important;
        }

        .dark .card {
            background-color: #1f2937 !important;
            border-color: #374151 !important;
            border: none !important;
            box-shadow: none !important;
        }

        .dark .card-header {
            background-color: #111827 !important;
            border-color: #374151 !important;
            border-bottom: 1px solid #374151 !important;
            color: #f3f4f6 !important;
        }

        .dark .card-body {
            background-color: #1f2937 !important;
        }

        .dark .alert {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
            color: #f3f4f6 !important;
        }

        /* Fond des cartes et sections en mode sombre */
        .dark .form-section {
            background-color: #1f2937 !important;
            border: none !important;
            box-shadow: none !important;
        }

        .dark .section-box {
            background-color: #1f2937 !important;
        }

        .dark .section-divider {
            border-color: #374151 !important;
        }

        .dark .section-heading h5 {
            color: #f3f4f6 !important;
        }

        .dark .section-heading small {
            color: #9ca3af !important;
        }

        .dark .input-group-text {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
            color: #d1d5db !important;
        }

        .dark .input-group-text i {
            color: #9ca3af !important;
        }

        /* Sidebar en mode sombre */
        .dark .sticky-side .card {
            background: #1f2937 !important;
            border: 1px solid #374151 !important;
            box-shadow: none !important;
        }

        .dark .sticky-side .card-header {
            background: #111827 !important;
            border-bottom: 1px solid #374151 !important;
        }

        .dark .sticky-side .card-body {
            background: #1f2937 !important;
        }

        /* Barre d'action en mode sombre */
        .dark .action-bar {
            background-color: #1f2937 !important;
            border-top-color: #374151 !important;
        }

        .dark .create-webinar .btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%) !important;
            color: #ffffff !important;
            border-color: #6366f1 !important;
            box-shadow: 0 2px 4px rgba(99, 102, 241, 0.3) !important;
        }

        .dark .create-webinar .btn-primary:hover {
            background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%) !important;
            box-shadow: 0 4px 8px rgba(99, 102, 241, 0.4) !important;
        }

        .dark .create-webinar .btn-secondary {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
            color: #e5e7eb !important;
        }

        .dark .create-webinar .btn-secondary:hover {
            background-color: #4b5563 !important;
            border-color: #6b7280 !important;
        }

        /* Badges en mode sombre */
        .dark .badge {
            background-color: #374151 !important;
            color: #e5e7eb !important;
            border: 1px solid #4b5563 !important;
        }

        .dark .badge.bg-success {
            background-color: #10b981 !important;
            color: #ffffff !important;
            border: none !important;
        }

        .dark .badge.bg-primary {
            background-color: #3b82f6 !important;
            color: #ffffff !important;
            border: none !important;
        }

        .dark .badge.bg-warning {
            background-color: #f59e0b !important;
            color: #ffffff !important;
            border: none !important;
        }

        /* Form check */
        .dark .form-check-input {
            background-color: transparent !important;
            border-color: #4b5563 !important;
        }

        .dark .form-check-label {
            color: #d1d5db !important;
        }

        /* Bouton outline en dark */
        .dark .btn-outline-primary {
            background-color: transparent !important;
            border-color: #3b82f6 !important;
            color: #60a5fa !important;
        }

        .dark .btn-outline-primary:hover {
            background-color: #3b82f6 !important;
            color: #ffffff !important;
        }

        /* Input group text en dark */
        .dark .input-group-text.bg-white {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
        }

        /* Suppression bordures */
        .dark .border-0,
        .dark .shadow-sm {
            border: none !important;
            box-shadow: none !important;
        }

        /* Protection du mode clair */
        html:not(.dark) .form-control,
        html:not(.dark) input,
        html:not(.dark) textarea,
        html:not(.dark) select {
            background-color: #ffffff !important;
            border-color: #d1d5db !important;
            color: #111827 !important;
        }

        html:not(.dark) .card {
            background-color: #ffffff !important;
        }

        html:not(.dark) .card-body {
            background-color: #ffffff !important;
        }
    </style>

    <script>
        // Auto-calculate end date based on start date and duration
        document.getElementById('start_date').addEventListener('change', function() {
            const startDate = new Date(this.value);
            const duration = parseInt(document.getElementById('duration').value);

            if (startDate && duration) {
                const endDate = new Date(startDate.getTime() + duration * 60000);
                document.getElementById('end_date').value = endDate.toISOString().slice(0, 16);
            }
        });

        document.getElementById('duration').addEventListener('change', function() {
            const startDateValue = document.getElementById('start_date').value;
            const duration = parseInt(this.value);

            if (startDateValue && duration) {
                const startDate = new Date(startDateValue);
                const endDate = new Date(startDate.getTime() + duration * 60000);

                document.getElementById('end_date').value = endDate.toISOString().slice(0, 16);
            }
        });

        // Image preview
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('image-preview');
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                }
                reader.readAsDataURL(file);
            }
        });

        // Character counter for short description
        const shortDescInput = document.getElementById('short_description');
        const counter = document.getElementById('short-desc-counter');
        if (shortDescInput && counter) {
            shortDescInput.addEventListener('input', function() {
                counter.textContent = this.value.length + '/500';
            });
            // Initialize counter with current value
            counter.textContent = shortDescInput.value.length + '/500';
        }
    </script>
</x-dashboard-layout>
