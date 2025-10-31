<div class="overflow-x-auto">
    @if(($rows ?? collect())->count() === 0)
        <div class="text-center py-8 text-gray-500 dark:text-gray-400">
            {{ translate('Aucun cours trouvé pour cette organisation.') }}
        </div>
    @else
    <table class="table-auto w-full text-left">
        <thead>
            <tr class="border-b border-gray-200 dark:border-dark-border-four">
                <th class="py-3 px-2 text-sm text-gray-500">{{ translate('Cours') }}</th>
                <th class="py-3 px-2 text-sm text-gray-500">{{ translate('Participants actifs') }}</th>
                <th class="py-3 px-2 text-sm text-gray-500">{{ translate('Progression avg') }}</th>
                <th class="py-3 px-2 text-sm text-gray-500">{{ translate('Temps total (s)') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-dark-border-four">
        @foreach(($rows ?? []) as $r)
            <tr>
                <td class="py-3 px-2 font-medium text-heading dark:text-white">{{ data_get($r, 'course.title', 'N/A') }}</td>
                <td class="py-3 px-2">{{ data_get($r, 'participants', 0) }}</td>
                <td class="py-3 px-2">
                    <div class="flex items-center gap-2">
                        <div class="w-24 h-2 bg-gray-200 rounded">
                            <div class="h-2 bg-primary rounded" style="width: {{ (float) data_get($r, 'progress_avg', 0) }}%"></div>
                        </div>
                        <span class="text-sm">{{ (float) data_get($r, 'progress_avg', 0) }}%</span>
                    </div>
                </td>
                <td class="py-3 px-2">{{ data_get($r, 'time_spent', 0) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @endif
</div>

@if(isset($courses))
    <div class="mt-4">
        {{ $courses->withQueryString()->links('portal::admin.pagination.paginate') }}
    </div>
@endif


