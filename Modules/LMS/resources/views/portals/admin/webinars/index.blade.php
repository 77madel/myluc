<x-dashboard-layout>
    <x-slot:title>{{ translate('Gestion des Webinaires') }}</x-slot:title>

    @push('head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @endpush

    <!-- BREADCRUMB -->
    <x-portal::admin.breadcrumb title="Webinaires" page-to="Webinaires" />

    <!-- Header Section avec design moderne -->
    <div class="grid grid-cols-12 gap-x-4 mb-6">
        <div class="col-span-full">
            <div class="relative overflow-hidden rounded-2xl shadow-xl" style="background: #667eea;">
                <div class="absolute inset-0 "></div>
                <div class="relative p-6 md:p-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                        <div class="text-white">
                            <h2 class="text-3xl font-bold mb-2 flex items-center">
                                <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mr-4">
                                    <i class="fas fa-video text-2xl"></i>
                                </div>
                                {{ translate('Gestion des Webinaires') }}
                            </h2>
                            <p class="text-white text-opacity-90 text-lg">
                                {{ translate('Créez et gérez vos webinaires avec style') }}
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('webinars.create') }}"
                               class="create-webinar-btn inline-flex items-center gap-3 bg-white text-gray-800 px-6 py-3 rounded-xl font-semibold hover:bg-opacity-90 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                <i class="fas fa-plus text-lg"></i>
                                {{ translate('Créer un Webinaire') }}
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


    <!-- Messages de feedback -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-4 rounded-3" role="alert" style="border: none; background: #15d30057;">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-4 rounded-3" role="alert" style="border: none; background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filtres avec design moderne et élégant -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 mb-8 overflow-hidden" style="background-color: white;">
        <style>
            .dark .bg-white {
                background-color: #1f2937 !important;
            }
            .dark .border-gray-100 {
                border-color: #374151 !important;
            }
            .dark .text-gray-700 {
                color: #d1d5db !important;
            }
            /* Forcer le mode sombre pour les boutons */
            .dark .bg-gray-100 {
                background-color: #4b5563 !important;
            }
            .dark .hover\:bg-gray-200:hover {
                background-color: #6b7280 !important;
            }
            .dark .text-gray-700 {
                color: #e5e7eb !important;
            }
            /* Forcer le mode sombre pour le bouton Actions */
            .dark .btn-outline-secondary {
                background-color: #4b5563 !important;
                border-color: #6b7280 !important;
                color: #e5e7eb !important;
            }
            .dark .btn-outline-secondary:hover {
                background-color: #6b7280 !important;
                border-color: #9ca3af !important;
            }
            /* Forcer le mode sombre pour le bouton Supprimer */
            .dark .btn-danger {
                background-color: #dc2626 !important;
                border-color: #dc2626 !important;
                color: #ffffff !important;
            }
            .dark .btn-danger:hover {
                background-color: #b91c1c !important;
                border-color: #b91c1c !important;
            }
            /* Forcer le mode sombre pour le bouton "Créer un Webinaire" */
            .dark .create-webinar-btn {
                background-color: #ffffff !important;
                color: #1f2937 !important;
            }
            .dark .create-webinar-btn:hover {
                background-color: #f3f4f6 !important;
                color: #111827 !important;
            }
            /* Forcer la couleur du texte dans le header des filtres */
            .dark .text-blue-100 {
                color: #dbeafe !important;
            }
            /* Couleur adaptative pour le titre des filtres */
            .filter-title {
                color: #1f2937 !important; /* Gris foncé pour mode clair */
            }
            .dark .filter-title {
                color: #ffffff !important; /* Blanc pour mode sombre */
            }
            /* Couleur adaptative pour le sous-titre des filtres */
            .filter-subtitle {
                color: #1e40af !important; /* Bleu foncé pour mode clair */
            }
            .dark .filter-subtitle {
                color: #dbeafe !important; /* Bleu clair pour mode sombre */
            }
        </style>
        <!-- Header du filtre -->
        <div class="filter-header px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-gray-200 dark:bg-gray-600 rounded-xl flex items-center justify-center mr-3">
                        <i class="fas fa-filter text-gray-700 dark:text-white text-lg"></i>
                    </div>
                    <div>
                        <h3 class="filter-title text-xl font-bold text-white">{{ translate('Filtres Avancés') }}</h3>
                        <p class="filter-subtitle text-sm">{{ translate('Affinez votre recherche') }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-white px-3 py-1 rounded-full text-sm font-medium">
                        {{ $webinars->total() }} {{ translate('webinaires') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Contenu du formulaire -->
        <div class="p-6">
            <form method="GET" class="space-y-6">
                <!-- Champs en colonne (empilés) -->
                <div class="space-y-4">
                    <!-- Recherche -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                            <i class="fas fa-search text-blue-500 mr-2"></i>{{ translate('Recherche') }}
                        </label>
                        <div class="relative">
                            <input type="text"
                                   name="search"
                                   class="w-full pl-4 pr-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"
                                   placeholder="{{ translate('Rechercher...') }}"
                                   value="{{ request('search') }}">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <i class="fas fa-search text-gray-400 dark:text-gray-500"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Statut -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                            <i class="fas fa-flag text-green-500 mr-2"></i>{{ translate('Statut') }}
                        </label>
                        <select name="status" class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="">{{ translate('Tous les statuts') }}</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>{{ translate('Brouillon') }}</option>
                            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>{{ translate('Publié') }}</option>
                            <option value="live" {{ request('status') == 'live' ? 'selected' : '' }}>{{ translate('En direct') }}</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ translate('Terminé') }}</option>
                        </select>
                    </div>

                    <!-- Instructeur -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">
                            <i class="fas fa-user-tie text-purple-500 mr-2"></i>{{ translate('Instructeur') }}
                        </label>
                        <select name="instructor" class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            <option value="">{{ translate('Tous les instructeurs') }}</option>
                            @foreach($instructors as $instructor)
                                @if($instructor->user)
                                    <option value="{{ $instructor->user->id }}" {{ request('instructor') == $instructor->user->id ? 'selected' : '' }}>
                                        {{ $instructor->user->full_name ?? $instructor->user->username ?? 'Instructeur' }} ({{ $instructor->user->email }})
                                    </option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Boutons d'action en bas -->
                <div class="flex justify-center mt-4 gap-4">
                    <button type="submit"
                            class="bg-primary from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white font-semibold py-3 px-8 rounded-xl transition-all duration-200 transform hover:scale-105 hover:shadow-lg">
                        <i class="fas fa-search mr-2"></i>{{ translate('Filtrer') }}
                    </button>
                    <a href="{{ route('webinars.index') }}"
                       class="bg-gray-100 hover:bg-gray-200 dark:bg-gray-600 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-200 font-semibold py-3 px-8 rounded-xl transition-all duration-200">
                        <i class="fas fa-times mr-2"></i>{{ translate('Effacer') }}
                    </a>
                </div>

                <!-- Filtres actifs -->
                @if(request()->hasAny(['search', 'status', 'instructor']))
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-xl p-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                <span class="text-sm font-medium text-blue-700 dark:text-blue-300">{{ translate('Filtres actifs') }}</span>
                            </div>
                            <a href="{{ route('webinars.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 text-sm font-medium">
                                {{ translate('Tout effacer') }}
                            </a>
                        </div>
                        <div class="mt-2 flex flex-wrap gap-2">
                            @if(request('search'))
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    <i class="fas fa-search mr-1"></i>{{ translate('Recherche') }}: "{{ request('search') }}"
                                </span>
                            @endif
                            @if(request('status'))
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    <i class="fas fa-flag mr-1"></i>{{ translate('Statut') }}: {{ ucfirst(request('status')) }}
                                </span>
                            @endif
                            @if(request('instructor'))
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                    <i class="fas fa-user-tie mr-1"></i>{{ translate('Instructeur') }}: {{ $instructors->where('id', request('instructor'))->first()->username ?? 'Inconnu' }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Cartes des webinaires -->
    <div class="row g-3 p-3">
        <style>

            .webinar-card {
                border-radius: 20px !important;
                overflow: hidden;
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                border: 1px solid rgba(0,0,0,0.05);
                background: #fff;
            }
            .dark .webinar-card {
                background: #374151;
                border: 1px solid rgba(255,255,255,0.1);
            }
            .webinar-card:hover {
                transform: translateY(-8px) scale(1.02);
                box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
            }
            .webinar-card:hover .card-img-top {
                transform: scale(1.1);
            }
            .webinar-card .card-img-top {
                transition: transform 0.4s ease;
            }
            .info-item {
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                border-radius: 10px;
                padding: 12px;
                margin-bottom: 8px;
                border: 1px solid rgba(0,0,0,0.05);
                transition: all 0.3s ease;
            }
            .dark .info-item {
                background: linear-gradient(135deg, #4b5563 0%, #374151 100%);
                border: 1px solid rgba(255,255,255,0.1);
            }
            .info-item:hover {
                background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
                transform: translateX(4px);
            }
            .dark .info-item:hover {
                background: linear-gradient(135deg, #374151 0%, #1f2937 100%);
            }
            .dark .info-item small {
                color: #d1d5db !important;
            }
            .dark .info-item strong {
                color: #ffffff !important;
            }
            .icon-circle {
                width: 32px;
                height: 32px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                transition: all 0.3s ease;
            }
            .icon-circle:hover {
                transform: scale(1.1);
                box-shadow: 0 6px 16px rgba(0,0,0,0.2);
            }
            .btn-primary {
                background: #6A6AFF;
                border: none !important;
                transition: all 0.3s ease;
            }
            .btn-primary:hover {
                background: #6A6AFF;
                transform: translateY(-2px);
                box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
            }
            .dropdown-menu {
                border-radius: 12px !important;
                border: none !important;
                box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
                padding: 8px !important;
                display: none !important;
                background-color: white !important;
            }
            .dark .dropdown-menu {
                background-color: #374151 !important;
                border: 1px solid #4b5563 !important;
            }
            .dropdown:hover .dropdown-menu {
                display: block !important;
            }
            .dropdown-item {
                cursor: pointer;
            }
            .dropdown-item {
                border-radius: 8px !important;
                margin: 2px 0 !important;
                padding: 10px 16px !important;
                transition: all 0.2s ease !important;
                color: #374151 !important;
            }
            .dark .dropdown-item {
                color: #d1d5db !important;
            }
            .dropdown-item:hover {
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
                transform: translateX(4px) !important;
            }
            .dark .dropdown-item:hover {
                background: linear-gradient(135deg, #4b5563 0%, #374151 100%) !important;
                color: #f9fafb !important;
            }
        </style>
        @forelse($webinars as $webinar)
            <div class="col-lg-6 col-xl-4">
                <div class="card border-0 shadow-sm h-100 webinar-card">
                    <!-- Webinar Image -->
                    <div class="position-relative" style="height: 180px; overflow: hidden;">
                        @if($webinar->image)
                            <img src="{{ Storage::url($webinar->image) }}"
                                 alt="{{ $webinar->title }}"
                                 class="card-img-top"
                                 style="height: 100%; object-fit: cover;"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <!-- Fallback caché (visible seulement si image échoue) -->
                            <div class="card-img-top d-flex align-items-center justify-content-center h-100"
                                 style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: none;">
                                <div class="text-center">
                                    <i class="fas fa-video fa-4x text-white opacity-80 mb-2"></i>
                                    <div class="text-white text-sm font-medium">{{ translate('Webinaire') }}</div>
                                </div>
                            </div>
                        @else
                            <!-- Fallback visible (pas d'image) -->
                            <div class="card-img-top d-flex align-items-center justify-content-center h-100"
                                 style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <div class="text-center">
                                    <i class="fas fa-video fa-4x text-white opacity-80 mb-2"></i>
                                    <div class="text-white text-sm font-medium">{{ translate('Webinaire') }}</div>
                                </div>
                            </div>
                        @endif

                        <!-- Status & Category Badges -->
                        <div class="position-absolute top-0 start-0 m-3">
                            <!-- Category Badge - Top Left -->
                            <div class="mb-1">
                                <span class="badge rounded-pill px-3 py-2 shadow-lg" style="font-size: 0.8rem; font-weight: 600; background: #6A6AFF; border: none;">
                                    {{ $webinar->category->title ?? 'N/A' }}
                                </span>
                            </div>
                        </div>

                        <!-- Status Badge - Top Right -->
                        <div class="position-absolute top-0 end-0 m-3">
                            @if($webinar->is_published)
                                <span class="badge rounded-pill px-3 py-2 shadow-lg" style="font-size: 0.8rem; font-weight: 600; background: #28a745; border: none;">
                                    <i class="fas fa-check-circle me-1"></i>
                                    {{ translate('Publié') }}
                                </span>
                            @else
                                <span class="badge rounded-pill px-3 py-2 shadow-lg" style="font-size: 0.8rem; font-weight: 600; background: #ffc107 ; border: none;">
                                    <i class="fas fa-edit me-1"></i>
                                    {{ translate('Brouillon') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body d-flex flex-column p-3" style="padding-top: 12px !important;">
                        <h5 class="card-title fw-bold text-dark dark:text-white mb-1" style="font-size: 1.1rem; line-height: 1.3;">{{ $webinar->title }}</h5>
                        <p class="card-text text-muted dark:text-gray-300 mb-3" style="font-size: 0.9rem; line-height: 1.4;">{{ Str::limit($webinar->short_description, 100) }}</p>

                        <!-- Webinar Info -->
                        <div class="mb-3">
                            <div class="info-item">
                                <div class="d-flex align-items-center">
                                    <div class="icon-circle bg-primary me-3">
                                        <i class="fas fa-calendar-alt text-white" style="font-size: 0.9rem;"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted dark:text-gray-300 d-block" style="font-size: 0.8rem; font-weight: 500;">{{ translate('Date et heure') }}</small>
                                        <strong class="text-dark dark:text-white" style="font-size: 0.9rem;">
                                            {{ $webinar->start_date->format('d/m/Y') }} à {{ $webinar->start_date->format('H:i') }}
                                        </strong>
                                    </div>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="d-flex align-items-center">
                                    <div class="icon-circle bg-info me-3">
                                        <i class="fas fa-clock text-white" style="font-size: 0.9rem;"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted dark:text-gray-300 d-block" style="font-size: 0.8rem; font-weight: 500;">{{ translate('Durée') }}</small>
                                        <strong class="text-dark dark:text-white" style="font-size: 0.9rem;">{{ $webinar->duration ?? 60 }} {{ translate('minutes') }}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="d-flex align-items-center">
                                    <div class="icon-circle bg-success me-3">
                                        <i class="fas fa-users text-white" style="font-size: 0.9rem;"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted dark:text-gray-300 d-block" style="font-size: 0.8rem; font-weight: 500;">{{ translate('Participants') }}</small>
                                        <strong class="text-dark dark:text-white" style="font-size: 0.9rem;">
                                            {{ $webinar->current_participants ?? 0 }}/{{ $webinar->max_participants ?? '∞' }}
                                        </strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Meeting Link -->
                        @if($webinar->meeting_url)
                            <div class="mb-3">
                                <a href="{{ $webinar->meeting_url }}" target="_blank"
                                   class="btn btn-primary btn-sm w-100 rounded-pill shadow-sm" style="font-weight: 600; padding: 10px 20px; background:  #667eea; border: none; font-size: 0.85rem;">
                                    <i class="fas fa-external-link-alt me-2"></i>
                                    {{ translate('Rejoindre la réunion') }}
                                </a>
                            </div>
                        @endif

                        <!-- Actions -->
                        <div class="mt-auto">
                            <!-- Dropdown Actions -->
                            <div class="dropdown mb-2">
                                <button class="btn btn-outline-secondary btn-sm w-100 dropdown-toggle rounded-pill shadow-sm dark:bg-gray-600 dark:border-gray-500 dark:text-gray-200" style="font-weight: 600; padding: 10px 20px; border: 1px solid #e9ecef; background: #fff; font-size: 0.85rem;"
                                        type="button"
                                        data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v me-2"></i>
                                    {{ translate('Actions') }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('webinars.show', $webinar->id) }}" style="font-weight: 500;">
                                            <i class="fas fa-eye text-primary me-2"></i>
                                            {{ translate('Voir') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('webinars.edit', $webinar->id) }}" style="font-weight: 500;">
                                            <i class="fas fa-edit text-info me-2"></i>
                                            {{ translate('Modifier') }}
                                        </a>
                                    </li>
                                    @if(!$webinar->is_published)
                                        <li>
                                            <form action="{{ route('webinars.publish', $webinar->id) }}"
                                                  method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-success">
                                                    <i class="fas fa-check me-2"></i>
                                                    {{ translate('Publier') }}
                                                </button>
                                            </form>
                                        </li>
                                    @else
                                        <li>
                                            <form action="{{ route('webinars.unpublish', $webinar->id) }}"
                                                  method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-warning">
                                                    <i class="fas fa-times me-2"></i>
                                                    {{ translate('Dépublier') }}
                                                </button>
                                            </form>
                                        </li>
                                    @endif
                                </ul>
                            </div>

                            <!-- Bouton de suppression séparé -->
                            <form action="{{ route('webinars.destroy', $webinar->id) }}" method="POST" class="w-100">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm w-100 rounded-pill" style="font-size: 0.8rem; padding: 8px 12px;">
                                    <i class="fas fa-trash me-2"></i>
                                    {{ translate('Supprimer') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <!-- État vide -->
            <div class="col-span-full">
                <div class="text-center py-12">
                    <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-video text-gray-400 text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">{{ translate('Aucun webinaire') }}</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">{{ translate('Commencez par créer votre premier webinaire.') }}</p>
                    <a href="{{ route('webinars.create') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>{{ translate('Créer un Webinaire') }}
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($webinars->hasPages())
        <div class="mt-8">
            {{ $webinars->links() }}
        </div>
    @endif

     @push('styles')
     <style>
         .line-clamp-2 {
             display: -webkit-box;
             -webkit-line-clamp: 2;
             -webkit-box-orient: vertical;
             overflow: hidden;
         }

         /* Dropdown styles */
         .dropdown-menu {
             display: none;
             position: absolute;
             top: 100%;
             right: 0;
             z-index: 50;
             min-width: 12rem;
             background-color: white;
             border: 1px solid #e5e7eb;
             border-radius: 0.5rem;
             box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
         }

         .dark .dropdown-menu {
             background-color: #374151;
             border-color: #4b5563;
         }

         .dropdown-menu.show {
             display: block;
         }

         .dropdown-item {
             display: flex;
             align-items: center;
             padding: 0.5rem 1rem;
             font-size: 0.875rem;
             color: #374151;
             text-decoration: none;
             transition: background-color 0.15s ease-in-out;
         }

         .dropdown-item:hover {
             background-color: #f3f4f6;
             color: #111827;
         }

         .dark .dropdown-item {
             color: #d1d5db;
         }

         .dark .dropdown-item:hover {
             background-color: #4b5563;
             color: #f9fafb;
         }
     </style>
     @endpush

     @push('scripts')
     <!-- SweetAlert2 -->
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
     <script>
     // Fonction simple pour toggle les dropdowns
     function toggleDropdown(dropdownId) {
         // Fermer tous les autres dropdowns
         document.querySelectorAll('[id^="menu-"]').forEach(menu => {
             menu.classList.add('hidden');
         });

         // Toggle le dropdown actuel
         const menu = document.getElementById('menu-' + dropdownId.split('-')[1]);
         if (menu) {
             menu.classList.toggle('hidden');
         }
     }

     // Fermer les dropdowns quand on clique ailleurs
     document.addEventListener('click', function(e) {
         if (!e.target.closest('[id^="dropdown-"]')) {
             document.querySelectorAll('[id^="menu-"]').forEach(menu => {
                 menu.classList.add('hidden');
             });
         }
     });

     // Gestion des toasts et actions
     document.addEventListener('DOMContentLoaded', function() {
         // Messages de session
         const successMsg = '{{ session("success") }}';
         const errorMsg = '{{ session("error") }}';

         // Afficher les toasts pour les messages de session
         if (successMsg) {
             showToast('success', successMsg);
         }
         if (errorMsg) {
             showToast('error', errorMsg);
         }

         // Gestion des formulaires de suppression
         const deleteForms = document.querySelectorAll('form[action*="destroy"]');
         deleteForms.forEach((form) => {
             const submitBtn = form.querySelector('button[type="submit"]');
             if (submitBtn) {
                 submitBtn.addEventListener('click', function(e) {
                     e.preventDefault();

                     // Toast de confirmation avant suppression
                     Swal.fire({
                         title: 'Êtes-vous sûr ?',
                         text: 'Cette action ne peut pas être annulée !',
                         icon: 'warning',
                         showCancelButton: true,
                         confirmButtonColor: '#d33',
                         cancelButtonColor: '#3085d6',
                         confirmButtonText: 'Oui, supprimer !',
                         cancelButtonText: 'Annuler'
                     }).then((result) => {
                         if (result.isConfirmed) {
                             showToast('info', 'Suppression en cours...');
                             form.submit();
                         }
                     });
                 });
             }
         });

         // Gestion des formulaires de publication/dépublication
         const publishForms = document.querySelectorAll('form[action*="publish"], form[action*="unpublish"]');
         publishForms.forEach((form) => {
             form.addEventListener('submit', function(e) {
                 const action = form.action.includes('publish') ? 'publication' : 'dépublication';
                 showToast('info', `${action} en cours...`);
             });
         });

         // Gestion des liens de modification
         const editLinks = document.querySelectorAll('a[href*="edit"]');
         editLinks.forEach((link) => {
             link.addEventListener('click', function() {
                 showToast('info', 'Ouverture de l\'éditeur...');
             });
         });

         // Gestion des liens de visualisation
         const viewLinks = document.querySelectorAll('a[href*="show"]');
         viewLinks.forEach((link) => {
             link.addEventListener('click', function() {
                 showToast('info', 'Chargement des détails...');
             });
         });
     });

     // Fonction pour afficher les toasts
     function showToast(type, message) {
         const Toast = Swal.mixin({
             toast: true,
             position: 'top-end',
             showConfirmButton: false,
             timer: 3000,
             timerProgressBar: true,
             didOpen: (toast) => {
                 toast.addEventListener('mouseenter', Swal.stopTimer)
                 toast.addEventListener('mouseleave', Swal.resumeTimer)
             }
         });

         const config = {
             success: {
                 icon: 'success',
                 title: message,
                 background: '#d4edda',
                 color: '#155724',
                 iconColor: '#28a745'
             },
             error: {
                 icon: 'error',
                 title: message,
                 background: '#f8d7da',
                 color: '#721c24',
                 iconColor: '#dc3545'
             },
             info: {
                 icon: 'info',
                 title: message,
                 background: '#d1ecf1',
                 color: '#0c5460',
                 iconColor: '#17a2b8'
             },
             warning: {
                 icon: 'warning',
                 title: message,
                 background: '#fff3cd',
                 color: '#856404',
                 iconColor: '#ffc107'
             }
         };

         Toast.fire(config[type]);
     }
     </script>
     @endpush
</x-dashboard-layout>
