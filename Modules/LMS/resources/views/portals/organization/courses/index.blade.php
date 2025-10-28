@php
    $user = authCheck();
    $isOrganization = isOrganization();
@endphp

<x-dashboard-layout>
    <x-slot:title>{{ translate('Cours Disponibles') }}</x-slot:title>
    
    <!-- BREADCRUMB -->
    <x-portal::admin.breadcrumb title="Cours Disponibles" page-to="Cours" />

    <!-- Search and Filter -->
    <div class="card mb-6">
        <div class="p-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" 
                           placeholder="{{ translate('Rechercher un cours...') }}" 
                           class="form-input w-full"
                           id="course-search">
                </div>
                <div class="flex gap-2">
                    <select class="form-select" id="price-filter">
                        <option value="">{{ translate('Tous les prix') }}</option>
                        <option value="free">{{ translate('Gratuits') }}</option>
                        <option value="paid">{{ translate('Payants') }}</option>
                    </select>
                    <select class="form-select" id="level-filter">
                        <option value="">{{ translate('Tous les niveaux') }}</option>
                        <option value="beginner">{{ translate('Débutant') }}</option>
                        <option value="intermediate">{{ translate('Intermédiaire') }}</option>
                        <option value="advanced">{{ translate('Avancé') }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    @if($courses->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($courses as $course)
                @php
                    $translations = parse_translation($course);
                    $title = $translations['title'] ?? ($course->title ?? '');
                    $description = $translations['description'] ?? ($course->description ?? '');
                    $instructors = $course->instructors ?? [];
                    $thumbnail = fileExists('lms/courses/thumbnails', $course->thumbnail) == true
                        ? asset("storage/lms/courses/thumbnails/{$course->thumbnail}")
                        : asset('lms/assets/images/placeholder/thumbnail612.jpg');
                    
                    $currency = $course?->coursePrice?->currency ?? 'USD-$';
                    $currencySymbol = get_currency_symbol($currency);
                    $price = $course?->coursePrice?->price ?? 0;
                    $isFree = $course?->courseSetting?->is_free ?? false;
                @endphp
                
                <div class="card overflow-hidden hover:shadow-lg transition-shadow duration-300">
                    <div class="relative">
                        <img src="{{ $thumbnail }}" 
                             alt="{{ $title }}" 
                             class="w-full h-48 object-cover">
                        @if($isFree)
                            <div class="absolute top-4 left-4">
                                <span class="badge b-solid badge-success-solid">{{ translate('Gratuit') }}</span>
                            </div>
                        @else
                            <div class="absolute top-4 right-4">
                                <span class="badge b-solid badge-primary-solid">
                                    {{ $currencySymbol }}{{ number_format($price, 0) }}
                                </span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-heading dark:text-white mb-2 line-clamp-2">
                            {{ $title }}
                        </h3>
                        
                        <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-3">
                            {{ Str::limit(strip_tags($description), 120) }}
                        </p>
                        
                        @if(count($instructors) > 0)
                            <div class="flex items-center gap-2 mb-4">
                                <div class="flex -space-x-2">
                                    @foreach($instructors->take(2) as $instructor)
                                        @php
                                            $userInfo = $instructor->userable ?? null;
                                            $userTranslations = parse_translation($userInfo);
                                            $avatar = $userInfo?->avatar 
                                                ? asset("storage/{$userInfo->avatar}")
                                                : asset('lms/assets/images/placeholder/avatar.png');
                                        @endphp
                                        <img src="{{ $avatar }}" 
                                             alt="{{ $userTranslations['first_name'] ?? $userInfo?->first_name }}"
                                             class="w-8 h-8 rounded-full border-2 border-white dark:border-gray-800">
                                    @endforeach
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    @if(count($instructors) > 1)
                                        {{ count($instructors) }} {{ translate('Instructeurs') }}
                                    @else
                                        {{ translate('Par') }} {{ $userTranslations['first_name'] ?? $instructors->first()->userable?->first_name }}
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4 text-sm text-gray-500">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    {{ $course->enrollments?->count() ?? 0 }} {{ translate('inscrits') }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                    </svg>
                                    {{ $course->chapters?->count() ?? 0 }} {{ translate('chapitres') }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('organization.courses.show', $course) }}" 
                               class="btn b-solid btn-primary-solid btn-sm w-full">
                                {{ translate('Voir les Détails') }}
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="mt-6">
            {{ $courses->links('portal::admin.pagination.paginate') }}
        </div>
    @else
        <x-portal::admin.empty-card title="{{ translate('Aucun cours trouvé') }}" />
    @endif
</x-dashboard-layout>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Search functionality
    const searchInput = document.getElementById('course-search');
    const priceFilter = document.getElementById('price-filter');
    const levelFilter = document.getElementById('level-filter');
    
    function filterCourses() {
        const searchTerm = searchInput.value.toLowerCase();
        const priceValue = priceFilter.value;
        const levelValue = levelFilter.value;
        
        const courseCards = document.querySelectorAll('.card');
        
        courseCards.forEach(card => {
            const title = card.querySelector('h3').textContent.toLowerCase();
            const price = card.querySelector('.badge-primary-solid, .badge-success-solid');
            const isFree = price && price.textContent.includes('Gratuit');
            
            let show = true;
            
            // Search filter
            if (searchTerm && !title.includes(searchTerm)) {
                show = false;
            }
            
            // Price filter
            if (priceValue === 'free' && !isFree) {
                show = false;
            }
            if (priceValue === 'paid' && isFree) {
                show = false;
            }
            
            card.style.display = show ? 'block' : 'none';
        });
    }
    
    searchInput.addEventListener('input', filterCourses);
    priceFilter.addEventListener('change', filterCourses);
    levelFilter.addEventListener('change', filterCourses);
});
</script>
@endpush











