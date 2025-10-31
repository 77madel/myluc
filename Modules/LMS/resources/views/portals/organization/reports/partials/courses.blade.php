<div class="overflow-x-auto scrollbar-table">
    @if(($rows ?? collect())->count() === 0)
        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
            {{ translate('Aucun cours trouvé pour cette organisation.') }}
        </div>
    @else
    <table class="table-auto border-collapse w-full whitespace-nowrap text-left text-gray-500 dark:text-dark-text">
        <thead>
            <tr class="text-primary-500">
                <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">{{ translate('Cours') }}</th>
                <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">{{ translate('Participants actifs') }}</th>
                <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">{{ translate('Progression avg') }}</th>
                <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">{{ translate('Temps total (s)') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-dark-border-three">
        @foreach(($rows ?? []) as $r)
            <tr>
                <td class="px-4 py-4 font-semibold text-heading dark:text-white">{{ data_get($r, 'course.title', 'N/A') }}</td>
                <td class="px-4 py-4">{{ data_get($r, 'participants', 0) }}</td>
                <td class="px-4 py-4">
                    <div class="flex items-center gap-2">
                        <div class="w-24 h-2 bg-gray-200 rounded">
                            <div class="h-2 bg-primary rounded" style="width: {{ (float) data_get($r, 'progress_avg', 0) }}%"></div>
                        </div>
                        <span class="text-sm">{{ (float) data_get($r, 'progress_avg', 0) }}%</span>
                    </div>
                </td>
                <td class="px-4 py-4">{{ data_get($r, 'time_spent', 0) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @endif
</div>

@if(isset($courses))
    <!-- Start Pagination -->
    {{ $courses->withQueryString()->links('portal::admin.pagination.paginate') }}
    <!-- End Pagination -->
@endif


