<x-dashboard-layout>
    <x-slot:title>{{ translate('Étudiants - ') . ($course->title ?? 'Course') }}</x-slot:title>

    <div class="card mb-4">
        <div class="p-4 flex items-center justify-between gap-2">
            <div>
                <h3 class="text-lg font-semibold text-heading dark:text-white">{{ $course->title }}</h3>
                <p class="text-sm text-gray-500">{{ translate('Progression des étudiants inscrits au cours') }}</p>
            </div>
            <a href="{{ route('instructor.course.index') }}" class="btn b-light btn-primary-light btn-sm rounded-full">{{ translate('Retour aux cours') }}</a>
        </div>
    </div>

    <div class="card">
        <div class="overflow-x-auto scrollbar-table">
            <table class="table-auto border-collapse w-full whitespace-nowrap text-left text-gray-500 dark:text-dark-text">
                <thead>
                    <tr class="text-primary-500">
                        <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">{{ translate('Étudiant') }}</th>
                        <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">{{ translate('Cours') }}</th>
                        <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">{{ translate('Leçons terminées') }}</th>
                        <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">{{ translate('Total leçons') }}</th>
                        <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">{{ translate('Progression') }}</th>
                        <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">{{ translate('Temps total') }}</th>
                        <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right w-10">{{ translate('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-dark-border-three">
                    @foreach($rows as $r)
                        @php $student = $r['student']; $userInfo = $student->userable ?? null; @endphp
                        <tr>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3.5">
                                    <div>
                                        <h6 class="leading-none text-heading dark:text-white font-semibold capitalize">
                                            {{ trim(($userInfo->first_name ?? '') . ' ' . ($userInfo->last_name ?? '')) ?: $student->email }}
                                        </h6>
                                        <div class="text-xs text-gray-500">{{ $student->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4">{{ $course->title }}</td>
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
                            <td class="px-4 py-4">
                                <a href="{{ route('instructor.course.students.progress', [$course->id, $student->id]) }}" class="btn-icon btn-primary-icon-light size-8">
                                    <i class="ri-eye-line text-inherit text-base"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $students->withQueryString()->links('portal::admin.pagination.paginate') }}
        </div>
    </div>
</x-dashboard-layout>


