<x-dashboard-layout>
    <x-slot:title>{{ translate('Gestion des Webinaires') }}</x-slot:title>

    <!-- BREADCRUMB -->
    <x-portal::admin.breadcrumb title="Webinaires" page-to="Webinaires" />

    <!-- Header Section avec design moderne -->
    <div class="grid grid-cols-12 gap-x-4 mb-6">
        <div class="col-span-full">
            <div class="relative overflow-hidden rounded-2xl shadow-xl" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="absolute inset-0 ></div>
                <div class="relative p-6 md:p-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div class="text-white">
                            <h2 class="text-3xl font-bold mb-2 flex items-center">
                                <div class="w-12 h-12  rounded-xl flex items-center justify-center mr-4">
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
                               class="inline-flex items-center gap-3 bg-white text-gray-800 px-6 py-3 rounded-xl font-semibold hover:bg-opacity-90 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
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

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white dark:bg-dark-card rounded-xl p-6 shadow-sm border border-gray-200 dark:border-dark-border-three">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-xl flex items-center justify-center">
                    <i class="fas fa-video text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ translate('Total Webinaires') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $webinars->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-card rounded-xl p-6 shadow-sm border border-gray-200 dark:border-dark-border-three">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-xl flex items-center justify-center">
                    <i class="fas fa-eye text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ translate('Publiés') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $webinars->where('status', 'published')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-card rounded-xl p-6 shadow-sm border border-gray-200 dark:border-dark-border-three">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-xl flex items-center justify-center">
                    <i class="fas fa-edit text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ translate('Brouillons') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $webinars->where('status', 'draft')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-dark-card rounded-xl p-6 shadow-sm border border-gray-200 dark:border-dark-border-three">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ translate('Participants') }}</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $webinars->sum('current_participants') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres avec design moderne -->
    <div class="bg-white dark:bg-dark-card rounded-xl shadow-sm border border-gray-200 dark:border-dark-border-three mb-6">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                <i class="fas fa-filter text-primary-500 mr-2"></i>{{ translate('Filtres') }}
            </h3>
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ translate('Recherche') }}
                    </label>
                    <input type="text" name="search" class="form-control" placeholder="{{ translate('Rechercher...') }}" value="{{ request('search') }}">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ translate('Statut') }}
                    </label>
                    <select name="status" class="form-select">
                        <option value="">{{ translate('Tous les statuts') }}</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>{{ translate('Brouillon') }}</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>{{ translate('Publié') }}</option>
                        <option value="live" {{ request('status') == 'live' ? 'selected' : '' }}>{{ translate('En direct') }}</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>{{ translate('Terminé') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        {{ translate('Instructeur') }}
                    </label>
                    <select name="instructor" class="form-select">
                        <option value="">{{ translate('Tous les instructeurs') }}</option>
                        @foreach($instructors as $instructor)
                            <option value="{{ $instructor->id }}" {{ request('instructor') == $instructor->id ? 'selected' : '' }}>
                                {{ $instructor->username ?? 'Instructeur' }} ({{ $instructor->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="btn btn-primary w-full">
                        <i class="fas fa-search mr-2"></i>{{ translate('Filtrer') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Cartes des webinaires -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($webinars as $webinar)
            <div class="bg-white dark:bg-dark-card rounded-xl shadow-sm border border-gray-200 dark:border-dark-border-three overflow-hidden hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                <!-- Header de la carte -->
                <div class="relative p-6 pb-4">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center">
                                <i class="fas fa-video text-white text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white line-clamp-2">
                                    {{ $webinar->title }}
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ Str::limit($webinar->short_description, 60) }}
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-col items-end space-y-2">
                            @switch($webinar->status)
                                @case('draft')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                        <i class="fas fa-edit mr-1"></i>{{ translate('Brouillon') }}
                                    </span>
                                    @break
                                @case('published')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        <i class="fas fa-eye mr-1"></i>{{ translate('Publié') }}
                                    </span>
                                    @break
                                @case('live')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        <i class="fas fa-broadcast-tower mr-1"></i>{{ translate('En direct') }}
                                    </span>
                                    @break
                                @case('completed')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                        <i class="fas fa-check-circle mr-1"></i>{{ translate('Terminé') }}
                                    </span>
                                    @break
                                @default
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                        <i class="fas fa-question mr-1"></i>{{ translate('Inconnu') }}
                            @endswitch

                            @if($webinar->is_featured)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                    <i class="fas fa-star mr-1"></i>{{ translate('En vedette') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Contenu de la carte -->
                <div class="px-6 pb-4">
                    <div class="space-y-3">
                        <!-- Instructeur -->
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-gray-500 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $webinar->instructor?->first_name ?? 'N/A' }} {{ $webinar->instructor?->last_name ?? '' }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ translate('Instructeur') }}</p>
                            </div>
                        </div>

                        <!-- Date et heure -->
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                <i class="fas fa-calendar text-blue-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $webinar->start_date->format('d/m/Y') }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $webinar->start_date->format('H:i') }} - {{ $webinar->end_date->format('H:i') }}
                                </p>
                            </div>
                        </div>

                        <!-- Participants -->
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                <i class="fas fa-users text-green-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex justify-between text-sm">
                                    <span class="font-medium text-gray-900 dark:text-white">
                                        {{ $webinar->current_participants ?? 0 }} / {{ $webinar->max_participants ?? '∞' }}
                                    </span>
                                    <span class="text-gray-500 dark:text-gray-400">{{ translate('Participants') }}</span>
                                </div>
                                @if($webinar->max_participants)
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-1">
                                        <div class="bg-gradient-to-r from-green-500 to-blue-500 h-2 rounded-full"
                                             style="width: {{ min(100, (($webinar->current_participants ?? 0) / $webinar->max_participants) * 100) }}%"></div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="px-6 py-4 bg-gray-50 dark:bg-dark-card-shade border-t border-gray-200 dark:border-dark-border-three">
                    <div class="flex items-center justify-between">
                        <div class="flex space-x-2">
                            <a href="{{ route('webinars.show', $webinar->id) }}"
                               class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-dark-card border border-gray-300 dark:border-dark-border-three rounded-lg hover:bg-gray-50 dark:hover:bg-dark-card-shade transition-colors">
                                <i class="fas fa-eye mr-2"></i>{{ translate('Voir') }}
                            </a>

                            <div class="relative">
                                <button type="button" class="inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-dark-card border border-gray-300 dark:border-dark-border-three rounded-lg hover:bg-gray-50 dark:hover:bg-dark-card-shade transition-colors dropdown-toggle">
                                    <i class="fas fa-cog mr-2"></i>{{ translate('Actions') }}
                                </button>

                                <div class="absolute right-0 mt-2 w-48 bg-white dark:bg-dark-card rounded-lg shadow-lg border border-gray-200 dark:border-dark-border-three z-10 hidden dropdown-menu">
                                    <div class="py-1">
                                        <a href="{{ route('webinars.edit', $webinar->id) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-dark-card-shade">
                                            <i class="fas fa-edit mr-3"></i>{{ translate('Modifier') }}
                                        </a>

                                        @if($webinar->status == 'draft')
                                            <form action="{{ route('webinars.publish', $webinar->id) }}" method="POST" class="block">
                                                @csrf
                                                <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-dark-card-shade">
                                                    <i class="fas fa-eye mr-3"></i>{{ translate('Publier') }}
                                                </button>
                                            </form>
                                        @elseif($webinar->status == 'published')
                                            <form action="{{ route('webinars.unpublish', $webinar->id) }}" method="POST" class="block">
                                                @csrf
                                                <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-dark-card-shade">
                                                    <i class="fas fa-eye-slash mr-3"></i>{{ translate('Dépublier') }}
                                                </button>
                                            </form>
                                        @endif

                                        <a href="{{ route('webinars.registrations', $webinar->id) }}" class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-dark-card-shade">
                                            <i class="fas fa-users mr-3"></i>{{ translate('Inscriptions') }}
                                        </a>

                                        <div class="border-t border-gray-200 dark:border-dark-border-three my-1"></div>

                                        <form action="{{ route('webinars.destroy', $webinar->id) }}" method="POST" class="block" onsubmit="return confirm('{{ translate('Êtes-vous sûr de vouloir supprimer ce webinaire ?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900">
                                                <i class="fas fa-trash mr-3"></i>{{ translate('Supprimer') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
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

        .dropdown-toggle:hover + .dropdown-menu,
        .dropdown-menu:hover {
            display: block !important;
        }

        .dropdown-menu {
            display: none;
        }
    </style>
    @endpush
</x-dashboard-layout>





