<div class="overflow-x-auto scrollbar-table">
    <table class="table-auto border-collapse w-full whitespace-nowrap text-left text-gray-500 dark:text-dark-text">
        <thead>
            <tr class="text-primary-500">
                <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">{{ translate('Participant') }}</th>
                <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">{{ translate('Cours') }}</th>
                <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">{{ translate('Leçons terminées') }}</th>
                <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">{{ translate('Total leçons') }}</th>
                <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">{{ translate('Progression moyenne') }}</th>
                <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">{{ translate('Temps total') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-dark-border-three">
        @foreach(($rows ?? []) as $r)
            @php $student = $r['student']; @endphp
            <tr>
                <td class="px-4 py-4">
                    <div class="flex items-center gap-3.5">
                        @php
                            $userInfo = $student->userable ?? null;
                            $avatar = $userInfo?->avatar ? asset('storage/' . $userInfo->avatar) : null;
                            $fullName = trim(($userInfo->first_name ?? '') . ' ' . ($userInfo->last_name ?? ''));
                            $initials = collect(explode(' ', $fullName))->filter()->map(fn($p)=>mb_substr($p,0,1))->take(2)->implode('');
                        @endphp
                        @if($avatar)
                            <img src="{{ $avatar }}" alt="{{ $fullName ?: $student->email }}" class="w-8 h-8 rounded-full object-cover border border-gray-200 dark:border-gray-700">
                        @else
                            <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center text-xs font-semibold">{{ $initials ?: 'U' }}</div>
                        @endif
                        <div>
                            <div class="font-semibold leading-none text-heading dark:text-white">{{ $fullName ?: ($student->name ?? $student->email) }}</div>
                            <div class="text-xs text-gray-500">{{ $student->email }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-dark-card-two text-gray-700 dark:text-gray-300">{{ $r['courses_count'] }}</span>
                </td>
                <td class="px-4 py-4">{{ $r['completed_topics'] }}</td>
                <td class="px-4 py-4">{{ $r['total_topics'] }}</td>
                <td class="px-4 py-4">
                    <div class="flex items-center gap-2">
                        <div class="w-24 h-2 bg-gray-200 rounded">
                            <div class="h-2 bg-primary rounded" style="width: {{ $r['avg_progress'] }}%"></div>
                        </div>
                        <span class="text-sm">{{ $r['avg_progress'] }}%</span>
                    </div>
                </td>
                <td class="px-4 py-4">{{ \App\Helpers\TimeHelper::formatTimeSpent($r['time_spent'] ?? 0) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

@if(isset($students))
    <!-- Start Pagination -->
    {{ $students->withQueryString()->links('portal::admin.pagination.paginate') }}
    <!-- End Pagination -->
@endif


