<x-dashboard-layout>
    <x-slot:title>{{ translate('Gestion des Webinaires') }}</x-slot:title>

    <!-- BREADCRUMB -->
    <x-portal::admin.breadcrumb title="Webinaires" page-to="Webinaires" />

    <!-- Header Section avec design moderne -->
    <div class="grid grid-cols-12 gap-x-4 mb-6">
        <div class="col-span-full">
            <div class="relative overflow-hidden rounded-2xl shadow-xl" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="absolute inset-0 bg-black opacity-10"></div>
                <div class="relative p-6 md:p-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
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

    <!-- Main Content -->
    <div class="grid grid-cols-12 gap-x-4">
        <div class="col-span-full">
            <div class="card overflow-hidden">
                <div class="card-header bg-[#F2F4F9] dark:bg-dark-card-two p-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            <i class="fas fa-list mr-2 text-primary-500"></i>
                            {{ translate('Liste des Webinaires') }}
                        </h3>
                        <span class="badge bg-primary-100 text-primary-600 px-3 py-1 rounded-full">
                            {{ $webinars->total() }} {{ translate('webinaire(s)') }}
                        </span>
                    </div>
                </div>
                <!-- Filtres avec design moderne -->
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-dark-card-shade dark:to-dark-card-two p-6 border-b border-gray-200 dark:border-dark-border-three">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-filter mr-1"></i>{{ translate('Statut') }}
                            </label>
                            <select class="form-select w-full" onchange="filterByStatus(this.value)">
                                <option value="">{{ translate('Tous les statuts') }}</option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>
                                    {{ translate('Brouillon') }}
                                </option>
                                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>
                                    {{ translate('Publié') }}
                                </option>
                                <option value="live" {{ request('status') == 'live' ? 'selected' : '' }}>
                                    {{ translate('En direct') }}
                                </option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                                    {{ translate('Terminé') }}
                                </option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>
                                    {{ translate('Annulé') }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-user-tie mr-1"></i>{{ translate('Instructeur') }}
                            </label>
                            <select class="form-select w-full" onchange="filterByInstructor(this.value)">
                                <option value="">{{ translate('Tous les instructeurs') }}</option>
                                @foreach($instructors as $instructor)
                                    <option value="{{ $instructor->id }}" {{ request('instructor') == $instructor->id ? 'selected' : '' }}>
                                        {{ $instructor->first_name ?? 'Instructeur' }} {{ $instructor->last_name ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                <i class="fas fa-search mr-1"></i>{{ translate('Rechercher') }}
                            </label>
                            <form method="GET" class="flex">
                                <input type="text" name="search" class="form-control flex-1"
                                       placeholder="{{ translate('Rechercher par titre...') }}"
                                       value="{{ request('search') }}">
                                <button type="submit" class="btn btn-primary ml-2">
                                    <i class="fas fa-search"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Cartes des webinaires avec design moderne -->
                <div class="p-6">
                    @if($webinars->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @forelse($webinars as $webinar)
                                <tr class="hover:bg-gray-50 dark:hover:bg-dark-card-shade transition-colors">
                                    <td class="px-3.5 py-4">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center">
                                                <i class="fas fa-video text-primary-500"></i>
                                            </div>
                                            <div>
                                                <h4 class="text-sm font-semibold text-gray-900 dark:text-white">
                                                    {{ $webinar->title }}
                                                </h4>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ Str::limit($webinar->short_description, 50) }}
                                                </p>
                                                @if($webinar->is_featured)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                        <i class="fas fa-star mr-1"></i>{{ translate('En vedette') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3.5 py-4">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                                <i class="fas fa-user text-gray-500"></i>
                                            </div>
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $webinar->instructor?->first_name ?? 'N/A' }} {{ $webinar->instructor?->last_name ?? '' }}
                                                </div>
                                                @if($webinar->instructor && $webinar->instructor->phone)
                                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $webinar->instructor->phone }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3.5 py-4">
                                        <div class="text-sm">
                                            <div class="font-medium text-gray-900 dark:text-white">
                                                {{ $webinar->start_date->format('d/m/Y') }}
                                            </div>
                                            <div class="text-gray-500 dark:text-gray-400">
                                                {{ $webinar->start_date->format('H:i') }}
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3.5 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            <i class="fas fa-tv mr-1"></i>
                                            {{ ucfirst($webinar->platform) }}
                                        </span>
                                    </td>
                                    <td class="px-3.5 py-4">
                                        <div class="text-sm">
                                            <div class="font-medium text-gray-900 dark:text-white">
                                                {{ $webinar->current_participants ?? 0 }} / {{ $webinar->max_participants ?? '∞' }}
                                            </div>
                                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 mt-1">
                                                <div class="bg-primary-500 h-1.5 rounded-full"
                                                     style="width: {{ $webinar->max_participants ? min(100, (($webinar->current_participants ?? 0) / $webinar->max_participants) * 100) : 0 }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-3.5 py-4">
                                        @switch($webinar->status)
                                            @case('draft')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                    <i class="fas fa-edit mr-1"></i>{{ translate('Brouillon') }}
                                                </span>
                                                @break
                                            @case('published')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                    <i class="fas fa-eye mr-1"></i>{{ translate('Publié') }}
                                                </span>
                                                @break
                                            @case('live')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                    <i class="fas fa-broadcast-tower mr-1"></i>{{ translate('En direct') }}
                                                </span>
                                                @break
                                            @case('completed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                    <i class="fas fa-check-circle mr-1"></i>{{ translate('Terminé') }}
                                                </span>
                                                @break
                                            @case('cancelled')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                    <i class="fas fa-times-circle mr-1"></i>{{ translate('Annulé') }}
                                                </span>
                                                @break
                                            @default
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                                    {{ $webinar->status }}
                                                </span>
                                        @endswitch
                                    </td>
                                    <td class="px-3.5 py-4">
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('webinars.show', $webinar->id) }}"
                                               class="btn btn-sm btn-outline-primary" title="{{ translate('Voir') }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <div class="relative">
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-primary modern-dropdown-btn"
                                                        title="{{ translate('Actions') }}"
                                                        style="border: 2px solid #0d6efd; color: #0d6efd; background: linear-gradient(135deg, rgba(13, 110, 253, 0.05), rgba(13, 110, 253, 0.1)); transition: all 0.3s ease; position: relative; overflow: hidden; min-width: 120px;">
                                                    <i class="fas fa-cog mr-2" style="transition: transform 0.3s ease;"></i>
                                                    {{ translate('Actions') }}
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-lg modern-dropdown-menu"
                                                    style="border: none; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15); border-radius: 12px; padding: 8px 0; backdrop-filter: blur(10px); background: rgba(255, 255, 255, 0.95); min-width: 200px;">

                                                    <!-- Action Modifier -->
                                                    <li>
                                                        <a href="{{ route('webinars.edit', $webinar) }}" class="dropdown-item modern-dropdown-item"
                                                           style="padding: 12px 16px; font-weight: 500; transition: all 0.3s ease; border-radius: 8px; margin: 4px 8px; position: relative; overflow: hidden; display: flex; align-items: center; border: 1px solid transparent;">
                                                            <i class="fas fa-edit mr-2 text-primary" style="transition: all 0.3s ease; margin-right: 8px; width: 16px; text-align: center;"></i>{{ translate('Modifier') }}
                                                        </a>
                                                    </li>

                                                    <!-- Actions de publication -->
                                                    @if($webinar->status == 'draft')
                                                        <li>
                                                            <form action="{{ route('webinars.publish', $webinar) }}" method="POST" class="d-inline" id="publish-form-{{ $webinar->id }}">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item modern-dropdown-item"
                                                                        style="padding: 12px 16px; font-weight: 500; transition: all 0.3s ease; border-radius: 8px; margin: 4px 8px; position: relative; overflow: hidden; display: flex; align-items: center; border: 1px solid transparent; width: 100%; text-align: left;"
                                                                        onclick="console.log('Publish button clicked for webinar: {{ $webinar->id }}'); console.log('Form action:', this.form.action); console.log('Form method:', this.form.method); return confirm('Êtes-vous sûr de vouloir publier ce webinaire ?');">
                                                                    <i class="fas fa-eye mr-2 text-success" style="transition: all 0.3s ease; margin-right: 8px; width: 16px; text-align: center;"></i>{{ translate('Publier') }}
                                                                </button>
                                                            </form>
                                                        </li>
                                                        <!-- BOUTON DE TEST SIMPLE -->
                                                        <li>
                                                            <a href="{{ route('webinars.publish', $webinar) }}"
                                                               class="dropdown-item modern-dropdown-item"
                                                               style="padding: 12px 16px; font-weight: 500; transition: all 0.3s ease; border-radius: 8px; margin: 4px 8px; position: relative; overflow: hidden; display: flex; align-items: center; border: 1px solid transparent; width: 100%; text-align: left; background-color: #ff6b6b; color: white;"
                                                               onclick="console.log('TEST PUBLISH LINK CLICKED for webinar: {{ $webinar->id }}'); return confirm('TEST: Publier ce webinaire ?');">
                                                                <i class="fas fa-test-tube mr-2" style="margin-right: 8px; width: 16px; text-align: center;"></i>TEST PUBLIER
                                                            </a>
                                                        </li>
                                                    @elseif($webinar->status == 'published')
                                                        <li>
                                                            <form action="{{ route('webinars.unpublish', $webinar) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item modern-dropdown-item"
                                                                        style="padding: 12px 16px; font-weight: 500; transition: all 0.3s ease; border-radius: 8px; margin: 4px 8px; position: relative; overflow: hidden; display: flex; align-items: center; border: 1px solid transparent; width: 100%; text-align: left;"
                                                                        onclick="console.log('Unpublish button clicked for webinar: {{ $webinar->id }}'); return confirm('Êtes-vous sûr de vouloir dépublier ce webinaire ?');">
                                                                    <i class="fas fa-eye-slash mr-2 text-warning" style="transition: all 0.3s ease; margin-right: 8px; width: 16px; text-align: center;"></i>{{ translate('Dépublier') }}
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif

                                                    <!-- Actions de vedette -->
                                                    @if(!$webinar->is_featured)
                                                        <li>
                                                            <form action="{{ route('webinars.feature', $webinar) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item modern-dropdown-item"
                                                                        style="padding: 12px 16px; font-weight: 500; transition: all 0.3s ease; border-radius: 8px; margin: 4px 8px; position: relative; overflow: hidden; display: flex; align-items: center; border: 1px solid transparent; width: 100%; text-align: left;">
                                                                    <i class="fas fa-star mr-2 text-warning" style="transition: all 0.3s ease; margin-right: 8px; width: 16px; text-align: center;"></i>{{ translate('Mettre en vedette') }}
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @else
                                                        <li>
                                                            <form action="{{ route('webinars.unfeature', $webinar) }}" method="POST" class="d-inline">
                                                                @csrf
                                                                <button type="submit" class="dropdown-item modern-dropdown-item"
                                                                        style="padding: 12px 16px; font-weight: 500; transition: all 0.3s ease; border-radius: 8px; margin: 4px 8px; position: relative; overflow: hidden; display: flex; align-items: center; border: 1px solid transparent; width: 100%; text-align: left;">
                                                                    <i class="fas fa-star-half-alt mr-2 text-warning" style="transition: all 0.3s ease; margin-right: 8px; width: 16px; text-align: center;"></i>{{ translate('Retirer de la vedette') }}
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif

                                                    <!-- Séparateur -->
                                                    <li><hr class="dropdown-divider" style="margin: 8px 0; border-color: rgba(0, 0, 0, 0.08);"></li>

                                                    <!-- Inscriptions -->
                                                    <li>
                                                        <a href="{{ route('webinars.registrations', $webinar->id) }}" class="dropdown-item modern-dropdown-item"
                                                           style="padding: 12px 16px; font-weight: 500; transition: all 0.3s ease; border-radius: 8px; margin: 4px 8px; position: relative; overflow: hidden; display: flex; align-items: center; border: 1px solid transparent;">
                                                            <i class="fas fa-users mr-2 text-info" style="transition: all 0.3s ease; margin-right: 8px; width: 16px; text-align: center;"></i>{{ translate('Inscriptions') }}
                                                        </a>
                                                    </li>

                                                    <!-- Séparateur -->
                                                    <li><hr class="dropdown-divider" style="margin: 8px 0; border-color: rgba(0, 0, 0, 0.08);"></li>

                                                    <!-- Supprimer -->
                                                    <li>
                                                        <form action="{{ route('webinars.destroy', $webinar) }}" method="POST" class="d-inline"
                                                              onsubmit="return confirm('{{ translate('Êtes-vous sûr de vouloir supprimer ce webinaire ?') }}')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item modern-dropdown-item text-danger"
                                                                    style="padding: 12px 16px; font-weight: 500; transition: all 0.3s ease; border-radius: 8px; margin: 4px 8px; position: relative; overflow: hidden; display: flex; align-items: center; border: 1px solid transparent; width: 100%; text-align: left;">
                                                                <i class="fas fa-trash mr-2" style="transition: all 0.3s ease; margin-right: 8px; width: 16px; text-align: center;"></i>{{ translate('Supprimer') }}
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-3.5 py-8 text-center">
                                        <div class="text-center">
                                            <div class="mb-4">
                                                <i class="fas fa-video text-gray-400 text-6xl"></i>
                                            </div>
                                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                                {{ translate('Aucun webinaire trouvé') }}
                                            </h3>
                                            <p class="text-gray-500 dark:text-gray-400 mb-4">
                                                {{ translate('Commencez par créer votre premier webinaire pour organiser des sessions en ligne.') }}
                                            </p>
                                            <a href="{{ route('webinars.create') }}" class="btn btn-primary btn-lg">
                                                <i class="fas fa-plus mr-2"></i>
                                                {{ translate('Créer un Webinaire') }}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($webinars->hasPages())
                    <div class="flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-dark-card-shade border-t border-gray-200 dark:border-dark-border-three">
                        <div class="text-sm text-gray-700 dark:text-gray-300">
                            {{ translate('Affichage de') }} {{ $webinars->firstItem() ?? 0 }} {{ translate('à') }} {{ $webinars->lastItem() ?? 0 }}
                            {{ translate('sur') }} {{ $webinars->total() }} {{ translate('webinaire(s)') }}
                        </div>
                        <div>
                            {{ $webinars->links() }}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-dashboard-layout>

<style>
    /* Amélioration des boutons d'action */
    .btn-sm {
        transition: all 0.2s ease;
        border-radius: 6px;
    }

    .btn-sm:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Style pour les menus dropdown */
    .dropdown-menu {
        border: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        border-radius: 8px;
        padding: 4px 0;
    }

    .dropdown-item {
        padding: 12px 16px;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 8px;
        margin: 4px 8px;
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        border: 1px solid transparent;
    }

    .dropdown-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    .dropdown-item:hover::before {
        left: 100%;
    }

    .dropdown-item:hover {
        background: linear-gradient(135deg, rgba(13, 110, 253, 0.08), rgba(13, 110, 253, 0.12));
        transform: translateX(4px) translateY(-1px);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.15);
        border-color: rgba(13, 110, 253, 0.2);
    }

    .dropdown-item.text-danger:hover {
        background: linear-gradient(135deg, rgba(220, 53, 69, 0.08), rgba(220, 53, 69, 0.12));
        box-shadow: 0 4px 12px rgba(220, 53, 69, 0.15);
        border-color: rgba(220, 53, 69, 0.2);
    }

    /* Styles spécifiques pour chaque type d'action */
    .dropdown-item:has(.fa-eye):hover {
        background: linear-gradient(135deg, rgba(40, 167, 69, 0.08), rgba(40, 167, 69, 0.12));
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.15);
        border-color: rgba(40, 167, 69, 0.2);
    }

    .dropdown-item:has(.fa-eye-slash):hover {
        background: linear-gradient(135deg, rgba(255, 193, 7, 0.08), rgba(255, 193, 7, 0.12));
        box-shadow: 0 4px 12px rgba(255, 193, 7, 0.15);
        border-color: rgba(255, 193, 7, 0.2);
    }

    .dropdown-item:has(.fa-star):hover {
        background: linear-gradient(135deg, rgba(255, 193, 7, 0.08), rgba(255, 193, 7, 0.12));
        box-shadow: 0 4px 12px rgba(255, 193, 7, 0.15);
        border-color: rgba(255, 193, 7, 0.2);
    }

    .dropdown-item:has(.fa-users):hover {
        background: linear-gradient(135deg, rgba(13, 202, 240, 0.08), rgba(13, 202, 240, 0.12));
        box-shadow: 0 4px 12px rgba(13, 202, 240, 0.15);
        border-color: rgba(13, 202, 240, 0.2);
    }

    /* Animation pour les icônes */
    .dropdown-item i {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        margin-right: 8px;
        width: 16px;
        text-align: center;
    }

    .dropdown-item:hover i {
        transform: scale(1.15) rotate(5deg);
    }

    .dropdown-item:hover .fa-eye {
        color: #28a745 !important;
    }

    .dropdown-item:hover .fa-eye-slash {
        color: #ffc107 !important;
    }

    .dropdown-item:hover .fa-star {
        color: #ffc107 !important;
    }

    .dropdown-item:hover .fa-users {
        color: #0dcaf0 !important;
    }

    .dropdown-item:hover .fa-trash {
        color: #dc3545 !important;
    }

    /* Effet de pulsation pour les actions importantes */
    .dropdown-item:has(.fa-star) {
        animation: pulse-gold 2s infinite;
    }

    @keyframes pulse-gold {
        0%, 100% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.4); }
        50% { box-shadow: 0 0 0 4px rgba(255, 193, 7, 0.1); }
    }

    /* Amélioration du dropdown menu */
    .dropdown-menu {
        border: none;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        border-radius: 12px;
        padding: 8px 0;
        backdrop-filter: blur(10px);
        background: rgba(255, 255, 255, 0.95);
        display: none;
        opacity: 0;
        transform: translateY(-10px);
        transition: all 0.3s ease;
    }

    /* Animation pour le dropdown au survol */
    .relative:hover .dropdown-menu {
        display: block !important;
        opacity: 1 !important;
        transform: translateY(0) !important;
    }

    .dropdown-divider {
        margin: 8px 0;
        border-color: rgba(0, 0, 0, 0.08);
    }

    /* Amélioration du bouton principal du dropdown */
    .btn-outline-info.dropdown-toggle {
        border: 2px solid #0dcaf0;
        color: #0dcaf0;
        background: linear-gradient(135deg, rgba(13, 202, 240, 0.05), rgba(13, 202, 240, 0.1));
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .btn-outline-info.dropdown-toggle::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s;
    }

    .btn-outline-info.dropdown-toggle:hover::before {
        left: 100%;
    }

    .btn-outline-info.dropdown-toggle:hover {
        background: linear-gradient(135deg, rgba(13, 202, 240, 0.15), rgba(13, 202, 240, 0.25));
        border-color: #0dcaf0;
        color: #0dcaf0;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(13, 202, 240, 0.3);
    }

    .btn-outline-info.dropdown-toggle:focus {
        box-shadow: 0 0 0 0.2rem rgba(13, 202, 240, 0.25);
    }

    .btn-outline-info.dropdown-toggle i {
        transition: transform 0.3s ease;
    }

    .btn-outline-info.dropdown-toggle:hover i {
        transform: scale(1.1) rotate(90deg);
    }

    /* Animation pour le bouton d'action */
    .btn-outline-info.dropdown-toggle {
        animation: subtle-pulse 3s infinite;
    }

    @keyframes subtle-pulse {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(13, 202, 240, 0.4);
        }
        50% {
            box-shadow: 0 0 0 3px rgba(13, 202, 240, 0.1);
        }
    }
</style>

<script>
function filterByStatus(status) {
    const url = new URL(window.location);
    if (status) {
        url.searchParams.set('status', status);
    } else {
        url.searchParams.delete('status');
    }
    window.location = url;
}

function filterByInstructor(instructorId) {
    const url = new URL(window.location);
    if (instructorId) {
        url.searchParams.set('instructor', instructorId);
    } else {
        url.searchParams.delete('instructor');
    }
    window.location = url;
}

// Amélioration des menus dropdown
document.addEventListener('DOMContentLoaded', function() {
    // Fermer les menus quand on clique ailleurs
    document.addEventListener('click', function(e) {
        const dropdowns = document.querySelectorAll('.group');
        dropdowns.forEach(dropdown => {
            if (!dropdown.contains(e.target)) {
                dropdown.classList.remove('group-hover:opacity-100');
            }
        });
    });

    // Animation des boutons
    const buttons = document.querySelectorAll('.btn-sm');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px)';
        });
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});

    // Améliorer les boutons d'action au chargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        // Gérer l'affichage du dropdown au survol
        const dropdownContainers = document.querySelectorAll('.relative');
        dropdownContainers.forEach(container => {
            const btn = container.querySelector('.dropdown-toggle');
            const menu = container.querySelector('.dropdown-menu');

            if (btn && menu) {
                // Afficher au survol du bouton
                btn.addEventListener('mouseenter', function() {
                    menu.style.display = 'block';
                    menu.style.opacity = '0';
                    menu.style.transform = 'translateY(-10px)';

                    // Animation d'apparition
                    setTimeout(() => {
                        menu.style.transition = 'all 0.3s ease';
                        menu.style.opacity = '1';
                        menu.style.transform = 'translateY(0)';
                    }, 10);

                    // Styles du bouton au survol
                    this.style.background = 'linear-gradient(135deg, rgba(13, 110, 253, 0.15), rgba(13, 110, 253, 0.25))';
                    this.style.transform = 'translateY(-2px)';
                    this.style.boxShadow = '0 6px 20px rgba(13, 110, 253, 0.3)';
                    this.querySelector('i').style.transform = 'scale(1.1) rotate(90deg)';
                });

                // Masquer quand on quitte le bouton ET le menu
                container.addEventListener('mouseleave', function() {
                    menu.style.transition = 'all 0.3s ease';
                    menu.style.opacity = '0';
                    menu.style.transform = 'translateY(-10px)';

                    setTimeout(() => {
                        menu.style.display = 'none';
                    }, 300);

                    // Reset styles du bouton
                    btn.style.background = 'linear-gradient(135deg, rgba(13, 110, 253, 0.05), rgba(13, 110, 253, 0.1))';
                    btn.style.transform = 'translateY(0)';
                    btn.style.boxShadow = 'none';
                    btn.querySelector('i').style.transform = 'scale(1) rotate(0deg)';
                });

                // Garder le menu ouvert quand on survole le menu
                menu.addEventListener('mouseenter', function() {
                    this.style.display = 'block';
                    this.style.opacity = '1';
                    this.style.transform = 'translateY(0)';
                });
            }
        });

        // Améliorer les éléments du dropdown
        const dropdownItems = document.querySelectorAll('.dropdown-item');
        dropdownItems.forEach(item => {
            item.addEventListener('mouseenter', function() {
                const icon = this.querySelector('i');
                if (icon) {
                    icon.style.transform = 'scale(1.15) rotate(5deg)';
                }

                // Couleurs spécifiques selon l'icône
                if (icon && icon.classList.contains('fa-edit')) {
                    this.style.background = 'linear-gradient(135deg, rgba(13, 110, 253, 0.08), rgba(13, 110, 253, 0.12))';
                    this.style.boxShadow = '0 4px 12px rgba(13, 110, 253, 0.15)';
                    this.style.borderColor = 'rgba(13, 110, 253, 0.2)';
                    icon.style.color = '#0d6efd';
                } else if (icon && icon.classList.contains('fa-eye')) {
                    this.style.background = 'linear-gradient(135deg, rgba(40, 167, 69, 0.08), rgba(40, 167, 69, 0.12))';
                    this.style.boxShadow = '0 4px 12px rgba(40, 167, 69, 0.15)';
                    this.style.borderColor = 'rgba(40, 167, 69, 0.2)';
                    icon.style.color = '#28a745';
                } else if (icon && icon.classList.contains('fa-star')) {
                    this.style.background = 'linear-gradient(135deg, rgba(255, 193, 7, 0.08), rgba(255, 193, 7, 0.12))';
                    this.style.boxShadow = '0 4px 12px rgba(255, 193, 7, 0.15)';
                    this.style.borderColor = 'rgba(255, 193, 7, 0.2)';
                    icon.style.color = '#ffc107';
                } else if (icon && icon.classList.contains('fa-users')) {
                    this.style.background = 'linear-gradient(135deg, rgba(13, 202, 240, 0.08), rgba(13, 202, 240, 0.12))';
                    this.style.boxShadow = '0 4px 12px rgba(13, 202, 240, 0.15)';
                    this.style.borderColor = 'rgba(13, 202, 240, 0.2)';
                    icon.style.color = '#0dcaf0';
                } else if (icon && icon.classList.contains('fa-trash')) {
                    this.style.background = 'linear-gradient(135deg, rgba(220, 53, 69, 0.08), rgba(220, 53, 69, 0.12))';
                    this.style.boxShadow = '0 4px 12px rgba(220, 53, 69, 0.15)';
                    this.style.borderColor = 'rgba(220, 53, 69, 0.2)';
                    icon.style.color = '#dc3545';
                }

                this.style.transform = 'translateX(4px) translateY(-1px)';
            });

            item.addEventListener('mouseleave', function() {
                this.style.background = '';
                this.style.boxShadow = '';
                this.style.borderColor = '';
                this.style.transform = '';

                const icon = this.querySelector('i');
                if (icon) {
                    icon.style.transform = '';
                    icon.style.color = '';
                }
            });
        });
    });
</script>
