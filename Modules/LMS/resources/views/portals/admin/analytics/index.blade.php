<x-dashboard-layout>
    <x-slot:title>{{ translate('Analytics Dashboard') }}</x-slot:title>
    
    <!-- BREADCRUMB -->
    <x-portal::admin.breadcrumb title="Analytics" page-to="Dashboard" />
    
    <!-- Filtre de période -->
    <div class="card mb-4">
        <div class="flex-center-between">
            <h6 class="card-title">{{ translate('Analytics Dashboard') }}</h6>
            <div class="flex gap-2">
                <a href="?period=7" class="btn b-solid {{ $period == 7 ? 'btn-primary-solid' : 'btn-secondary-solid' }} btn-sm">7 {{ translate('jours') }}</a>
                <a href="?period=30" class="btn b-solid {{ $period == 30 ? 'btn-primary-solid' : 'btn-secondary-solid' }} btn-sm">30 {{ translate('jours') }}</a>
                <a href="?period=90" class="btn b-solid {{ $period == 90 ? 'btn-primary-solid' : 'btn-secondary-solid' }} btn-sm">90 {{ translate('jours') }}</a>
            </div>
        </div>
    </div>
    
    <!-- STATISTIQUES GÉNÉRALES -->
    <div class="card">
        <div class="grid grid-cols-12 gap-4">
            <!-- Visiteurs -->
            <div class="col-span-full md:col-span-6 lg:col-span-3 p-4 dk-border-one rounded-xl dk-theme-card-square">
                <div class="flex-center-between">
                    <h6 class="leading-none text-gray-500 dark:text-dark-text font-semibold">
                        {{ translate('Total Visiteurs') }}
                    </h6>
                </div>
                <div class="pt-3 bg-[url('../../assets/images/card/pattern.png')] dark:bg-[url('../../assets/images/card/pattern-dark.png')] bg-no-repeat bg-100% flex gap-4 mt-3">
                    <div class="pb-4 shrink-0">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="counter-value card-title text-2xl text-primary" data-value="{{ $stats['total_visitors'] }}">
                                0
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Utilisateurs -->
            <div class="col-span-full md:col-span-6 lg:col-span-3 p-4 dk-border-one rounded-xl dk-theme-card-square">
                <div class="flex-center-between">
                    <h6 class="leading-none text-gray-500 dark:text-dark-text font-semibold">
                        {{ translate('Utilisateurs Connectés') }}
                    </h6>
                </div>
                <div class="pt-3 bg-[url('../../assets/images/card/pattern.png')] dark:bg-[url('../../assets/images/card/pattern-dark.png')] bg-no-repeat bg-100% flex gap-4 mt-3">
                    <div class="pb-4 shrink-0">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="counter-value card-title text-2xl text-success" data-value="{{ $stats['total_users'] }}">
                                0
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Pages Vues -->
            <div class="col-span-full md:col-span-6 lg:col-span-3 p-4 dk-border-one rounded-xl dk-theme-card-square">
                <div class="flex-center-between">
                    <h6 class="leading-none text-gray-500 dark:text-dark-text font-semibold">
                        {{ translate('Pages Vues') }}
                    </h6>
                </div>
                <div class="pt-3 bg-[url('../../assets/images/card/pattern.png')] dark:bg-[url('../../assets/images/card/pattern-dark.png')] bg-no-repeat bg-100% flex gap-4 mt-3">
                    <div class="pb-4 shrink-0">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="counter-value card-title text-2xl text-warning" data-value="{{ $stats['total_page_views'] }}">
                                0
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Temps Moyen -->
            <div class="col-span-full md:col-span-6 lg:col-span-3 p-4 dk-border-one rounded-xl dk-theme-card-square">
                <div class="flex-center-between">
                    <h6 class="leading-none text-gray-500 dark:text-dark-text font-semibold">
                        {{ translate('Temps Moyen') }}
                    </h6>
                </div>
                <div class="pt-3 bg-[url('../../assets/images/card/pattern.png')] dark:bg-[url('../../assets/images/card/pattern-dark.png')] bg-no-repeat bg-100% flex gap-4 mt-3">
                    <div class="pb-4 shrink-0">
                        <div class="flex items-center gap-2 mb-3">
                            <div class="card-title text-2xl text-danger">
                                {{ gmdate('i:s', $stats['avg_session_duration'] ?? 0) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
        <!-- APPAREILS -->
        <div class="card">
            <h6 class="card-title mb-4">{{ translate('Appareils') }}</h6>
            <div class="space-y-3">
                @foreach($devices as $device)
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <i class="ri-{{ $device->device_type == 'mobile' ? 'smartphone' : ($device->device_type == 'tablet' ? 'tablet' : 'computer') }}-line text-xl text-gray-600 dark:text-gray-400"></i>
                        <span class="capitalize text-heading dark:text-white">{{ $device->device_type }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="font-semibold text-heading dark:text-white">{{ $device->count }}</span>
                        <div class="w-32 bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                            <div class="bg-blue-600 dark:bg-blue-500 h-2 rounded-full" style="width: {{ ($device->count / $stats['total_visitors']) * 100 }}%"></div>
                        </div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ number_format(($device->count / $stats['total_visitors']) * 100, 1) }}%</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        
        <!-- SOURCES DE TRAFIC -->
        <div class="card">
            <h6 class="card-title mb-4">{{ translate('Sources de Trafic') }}</h6>
            <div class="space-y-3">
                @foreach($trafficSources as $source)
                @php
                    $icons = [
                        'direct' => 'arrow-right-line',
                        'organic' => 'search-line',
                        'social' => 'share-line',
                        'referral' => 'links-line',
                    ];
                    $colors = [
                        'direct' => 'blue',
                        'organic' => 'green',
                        'social' => 'purple',
                        'referral' => 'orange',
                    ];
                @endphp
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-2">
                        <i class="ri-{{ $icons[$source->traffic_source] ?? 'question-line' }} text-xl text-{{ $colors[$source->traffic_source] ?? 'gray' }}-600 dark:text-{{ $colors[$source->traffic_source] ?? 'gray' }}-400"></i>
                        <span class="capitalize text-heading dark:text-white">{{ $source->traffic_source }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="font-semibold text-heading dark:text-white">{{ $source->count }}</span>
                        <div class="w-32 bg-gray-200 rounded-full h-2 dark:bg-gray-700">
                            <div class="bg-{{ $colors[$source->traffic_source] ?? 'gray' }}-600 dark:bg-{{ $colors[$source->traffic_source] ?? 'gray' }}-500 h-2 rounded-full" style="width: {{ ($source->count / $stats['total_visitors']) * 100 }}%"></div>
                        </div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ number_format(($source->count / $stats['total_visitors']) * 100, 1) }}%</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
        <!-- PAYS -->
        <div class="card">
            <h6 class="card-title mb-4">{{ translate('Top 10 Pays') }}</h6>
            <div class="overflow-x-auto">
                <table class="table-auto w-full">
                    <thead>
                        <tr class="border-b dark:border-gray-700">
                            <th class="text-left py-2 text-heading dark:text-white">{{ translate('Pays') }}</th>
                            <th class="text-right py-2 text-heading dark:text-white">{{ translate('Visiteurs') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($countries as $country)
                        <tr class="border-b dark:border-gray-700">
                            <td class="py-2 text-heading dark:text-white">
                                <span class="mr-2">{{ $country->country_code ? '🏳️' : '' }}</span>
                                {{ $country->country ?? 'Unknown' }}
                            </td>
                            <td class="text-right font-semibold text-heading dark:text-white">{{ number_format($country->count) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- VILLES -->
        <div class="card">
            <h6 class="card-title mb-4">{{ translate('Top 10 Villes') }}</h6>
            <div class="overflow-x-auto">
                <table class="table-auto w-full">
                    <thead>
                        <tr class="border-b dark:border-gray-700">
                            <th class="text-left py-2 text-heading dark:text-white">{{ translate('Ville') }}</th>
                            <th class="text-right py-2 text-heading dark:text-white">{{ translate('Visiteurs') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cities as $city)
                        <tr class="border-b dark:border-gray-700">
                            <td class="py-2 text-heading dark:text-white">
                                {{ $city->city ?? 'Unknown' }}
                                <span class="text-xs text-gray-500 dark:text-gray-400">({{ $city->country }})</span>
                            </td>
                            <td class="text-right font-semibold text-heading dark:text-white">{{ number_format($city->count) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- PAGES LES PLUS VISITÉES -->
    <div class="card mb-4">
        <h6 class="card-title mb-4">{{ translate('Pages les Plus Visitées') }}</h6>
        <div class="overflow-x-auto">
            <table class="table-auto w-full">
                <thead>
                    <tr class="bg-gray-50 dark:bg-dark-card-two">
                        <th class="text-left px-4 py-3 text-heading dark:text-white">{{ translate('Page') }}</th>
                        <th class="text-right px-4 py-3 text-heading dark:text-white">{{ translate('Vues') }}</th>
                        <th class="text-right px-4 py-3 text-heading dark:text-white">{{ translate('Temps Moyen') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topPages as $page)
                    <tr class="border-b dark:border-gray-700">
                        <td class="px-4 py-3">
                            <div class="text-heading dark:text-white">{{ $page->page_title ?? 'Sans titre' }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-md">{{ $page->page_url }}</div>
                        </td>
                        <td class="text-right px-4 py-3 font-semibold text-heading dark:text-white">{{ number_format($page->views) }}</td>
                        <td class="text-right px-4 py-3 text-heading dark:text-white">{{ gmdate('i:s', $page->avg_time ?? 0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- DONNÉES DÉMOGRAPHIQUES -->
    @if($demographics['age_groups']->count() > 0 || $demographics['genders']->count() > 0)
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
        <!-- ÂGE -->
        <div class="card">
            <h6 class="card-title mb-4">{{ translate('Répartition par Âge') }}</h6>
            <div class="space-y-2">
                @foreach($demographics['age_groups'] as $group)
                <div class="flex justify-between items-center">
                    <span class="text-heading dark:text-white">{{ $group->age_group }}</span>
                    <span class="font-semibold text-heading dark:text-white">{{ $group->count }}</span>
                </div>
                @endforeach
            </div>
        </div>
        
        <!-- GENRE -->
        <div class="card">
            <h6 class="card-title mb-4">{{ translate('Répartition par Genre') }}</h6>
            <div class="space-y-2">
                @foreach($demographics['genders'] as $gender)
                <div class="flex justify-between items-center">
                    <span class="capitalize text-heading dark:text-white">{{ translate($gender->gender) }}</span>
                    <span class="font-semibold text-heading dark:text-white">{{ $gender->count }}</span>
                </div>
                @endforeach
            </div>
        </div>
        
        <!-- PROFESSIONS -->
        <div class="card">
            <h6 class="card-title mb-4">{{ translate('Top Professions') }}</h6>
            <div class="space-y-2">
                @forelse($demographics['professions'] as $prof)
                <div class="flex justify-between items-center">
                    <span class="text-heading dark:text-white">{{ $prof->profession }}</span>
                    <span class="font-semibold text-heading dark:text-white">{{ $prof->count }}</span>
                </div>
                @empty
                <p class="text-sm text-gray-500 dark:text-gray-400">{{ translate('Aucune donnée') }}</p>
                @endforelse
            </div>
        </div>
    </div>
    @endif
    
    <!-- NAVIGATEURS ET OS -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
        <div class="card">
            <h6 class="card-title mb-4">{{ translate('Navigateurs') }}</h6>
            <div class="overflow-x-auto">
                <table class="table-auto w-full">
                    <tbody>
                        @foreach($browsers as $browser)
                        <tr class="border-b dark:border-gray-700">
                            <td class="py-2 text-heading dark:text-white">{{ $browser->browser }}</td>
                            <td class="text-right font-semibold text-heading dark:text-white">{{ number_format($browser->count) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card">
            <h6 class="card-title mb-4">{{ translate('Systèmes d\'Exploitation') }}</h6>
            <div class="overflow-x-auto">
                <table class="table-auto w-full">
                    <tbody>
                        @foreach($os as $system)
                        <tr class="border-b dark:border-gray-700">
                            <td class="py-2 text-heading dark:text-white">{{ $system->os }}</td>
                            <td class="text-right font-semibold text-heading dark:text-white">{{ number_format($system->count) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- MOTEURS DE RECHERCHE -->
    @if($searchEngines->count() > 0)
    <div class="card mb-4">
        <h6 class="card-title mb-4">{{ translate('Moteurs de Recherche') }}</h6>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($searchEngines as $engine)
            <div class="text-center p-4 bg-gray-50 dark:bg-dark-card-two rounded-lg border border-gray-200 dark:border-gray-700">
                <p class="text-sm text-gray-600 dark:text-gray-400 capitalize">{{ $engine->search_engine }}</p>
                <p class="text-2xl font-bold text-heading dark:text-white">{{ number_format($engine->count) }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    
    <!-- CONVERSIONS -->
    @if($conversions->count() > 0)
    <div class="card">
        <h6 class="card-title mb-4">{{ translate('Conversions') }}</h6>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach($conversions as $conversion)
            <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-700">
                <p class="text-sm capitalize text-heading dark:text-white mb-2">{{ translate($conversion->conversion_type) }}</p>
                <p class="text-3xl font-bold text-heading dark:text-white">{{ number_format($conversion->count) }}</p>
            </div>
            @endforeach
        </div>
    </div>
    @endif
    
</x-dashboard-layout>

