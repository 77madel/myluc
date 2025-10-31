<div class="overflow-x-auto">
    <table class="table-auto w-full text-left">
        <thead>
            <tr class="border-b border-gray-200 dark:border-dark-border-four">
                <th class="py-3 px-2 text-sm text-gray-500">{{ translate('Participant') }}</th>
                <th class="py-3 px-2 text-sm text-gray-500">{{ translate('Cours') }}</th>
                <th class="py-3 px-2 text-sm text-gray-500">{{ translate('Leçons terminées') }}</th>
                <th class="py-3 px-2 text-sm text-gray-500">{{ translate('Total leçons') }}</th>
                <th class="py-3 px-2 text-sm text-gray-500">{{ translate('Progression moyenne') }}</th>
                <th class="py-3 px-2 text-sm text-gray-500">{{ translate('Temps total') }}</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-dark-border-four">
        @foreach(($rows ?? []) as $r)
            @php $student = $r['student']; @endphp
            <tr>
                <td class="py-3 px-2">
                    <div class="flex items-center gap-3">
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
                            <div class="font-medium text-heading dark:text-white">{{ $fullName ?: ($student->name ?? $student->email) }}</div>
                            <div class="text-xs text-gray-500">{{ $student->email }}</div>
                        </div>
                    </div>
                </td>
                <td class="py-3 px-2">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-dark-card-two text-gray-700 dark:text-gray-300">{{ $r['courses_count'] }}</span>
                </td>
                <td class="py-3 px-2">{{ $r['completed_topics'] }}</td>
                <td class="py-3 px-2">{{ $r['total_topics'] }}</td>
                <td class="py-3 px-2">
                    <div class="flex items-center gap-2">
                        <div class="w-24 h-2 bg-gray-200 rounded">
                            <div class="h-2 bg-primary rounded" style="width: {{ $r['avg_progress'] }}%"></div>
                        </div>
                        <span class="text-sm">{{ $r['avg_progress'] }}%</span>
                    </div>
                </td>
                <td class="py-3 px-2">{{ \App\Helpers\TimeHelper::formatTimeSpent($r['time_spent'] ?? 0) }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>

@if(isset($students))
    <div class="mt-4">
        {{ $students->withQueryString()->links('portal::admin.pagination.paginate') }}
    </div>
@endif


