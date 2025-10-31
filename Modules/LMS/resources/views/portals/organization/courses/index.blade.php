@php
    $user = authCheck();
    $isOrganization = isOrganization();
@endphp

<x-dashboard-layout>
    <x-slot:title>{{ translate('Cours Disponibles') }}</x-slot:title>
    
    <!-- BREADCRUMB -->
    <x-portal::admin.breadcrumb title="{{ translate('Course list') }}" page-to="{{ translate('Course') }}" />

    <!-- Search and Filter -->
    <div class="card mb-6 dk-theme-card-square">
        <div class="p-6">
            <div class="flex">
                <div class="flex-1">
                    <input type="text"
                           placeholder="{{ translate('Rechercher un cours...') }}"
                           class="form-input w-full"
                           id="course-search">
                </div>
            </div>
        </div>
    </div>

    @if($courses->count() > 0)
        <div class="card overflow-hidden dk-theme-card-square">
            <table class="table-auto w-full whitespace-nowrap text-left text-gray-500 dark:text-dark-text font-medium leading-none">
                <thead>
                    <tr>
                        <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">
                            {{ translate('Course title') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-dark-border-three" id="course-table-body">
                @foreach($courses as $course)
                    @php
                        $translations = parse_translation($course);
                        $title = $translations['title'] ?? ($course->title ?? '');
                        $thumbnail = fileExists('lms/courses/thumbnails', $course->thumbnail) == true
                            ? asset("storage/lms/courses/thumbnails/{$course->thumbnail}")
                            : asset('lms/assets/images/placeholder/thumbnail612.jpg');
                        $currency = $course?->coursePrice?->currency ?? 'USD-$';
                        $currencySymbol = get_currency_symbol($currency);
                        $price = $course?->coursePrice?->price ?? 0;
                        $isFree = $course?->courseSetting?->is_free ?? false;
                    @endphp
                    <tr>
                        <td class="px-3.5 py-4">
                            <div class="flex items-center gap-2">
                                <a href="#" class="size-[70px] rounded-50 overflow-hidden dk-theme-card-square">
                                    <img src="{{ $thumbnail }}" alt="thumb" class="size-full object-cover">
                                </a>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-dark-text mb-1.5">{{ customDateFormate($course->created_at, $format = 'd M Y') }}</p>
                                    <h6 class="text-lg leading-none text-heading dark:text-white font-bold mb-1.5 line-clamp-1" title="{{ $title }}">
                                        <a href="{{ route('organization.courses.show', $course) }}">{{ substr($title, 0, 30) . '...' }}</a>
                                    </h6>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
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
    const searchInput = document.getElementById('course-search');
    const tbody = document.getElementById('course-table-body');

    function normalize(str){ return (str || '').toString().toLowerCase(); }

    function filterCourses() {
        const searchTerm = normalize(searchInput.value);

        tbody.querySelectorAll('tr').forEach(row => {
            const title = normalize(row.querySelector('td:nth-child(1) .font-medium')?.textContent);

            let show = true;
            if (searchTerm && !title.includes(searchTerm)) show = false;

            row.style.display = show ? '' : 'none';
        });
    }

    searchInput.addEventListener('input', filterCourses);
});
</script>
@endpush












