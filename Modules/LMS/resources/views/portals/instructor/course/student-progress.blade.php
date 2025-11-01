<x-dashboard-layout>
    <x-slot:title>{{ translate('Progression - ') . ($course->title ?? 'Course') }}</x-slot:title>

    <div class="card mb-4">
        <div class="p-4 flex items-center justify-between gap-2">
            <div>
                <h3 class="text-lg font-semibold text-heading dark:text-white">{{ $course->title }}</h3>
                <p class="text-sm text-gray-500">
                    {{ translate('Progression de') }}
                    {{ trim(($student->userable->first_name ?? '') . ' ' . ($student->userable->last_name ?? '')) ?: $student->email }}
                </p>
            </div>
            <a href="{{ route('instructor.course.students', $course->id) }}" class="btn b-light btn-primary-light btn-sm rounded-full">{{ translate('Retour à la liste') }}</a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="card">
            <div class="p-4">
                <div class="text-sm text-gray-500">{{ translate('Progression') }}</div>
                <div class="mt-2 flex items-center gap-2">
                    <div class="w-40 h-2 bg-gray-200 rounded">
                        <div class="h-2 bg-primary rounded" style="width: {{ $progress_pct }}%"></div>
                    </div>
                    <span class="text-base font-semibold">{{ $progress_pct }}%</span>
                </div>
                <div class="mt-3 grid grid-cols-3 gap-3 text-sm">
                    <div>
                        <div class="text-gray-500">{{ translate('Terminées') }}</div>
                        <div class="font-semibold">{{ $completed_topics }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">{{ translate('Total leçons') }}</div>
                        <div class="font-semibold">{{ $total_topics }}</div>
                    </div>
                    <div>
                        <div class="text-gray-500">{{ translate('Temps total') }}</div>
                        <div class="font-semibold">{{ \App\Helpers\TimeHelper::formatTimeSpent($total_time_spent ?? 0) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card lg:col-span-2">
            <div class="p-4">
                <div class="text-sm font-semibold mb-3">{{ translate('Détails par chapitre') }}</div>
                <div class="overflow-x-auto scrollbar-table">
                    <table class="table-auto border-collapse w-full whitespace-nowrap text-left text-gray-500 dark:text-dark-text">
                        <thead>
                            <tr class="text-primary-500">
                                <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">{{ translate('Chapitre') }}</th>
                                <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">{{ translate('Statut') }}</th>
                                <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">{{ translate('Temps (s)') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-dark-border-three">
                            @foreach($chapter_progress_details as $cp)
                                <tr>
                                    <td class="px-4 py-3">{{ $cp->chapter?->title ?? ('#'.$cp->chapter_id) }}</td>
                                    <td class="px-4 py-3">
                                        @php $status = $cp->status; @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $status === 'completed' ? 'bg-green-100 text-green-800' : ($status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ $status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">{{ $cp->time_spent ?? 0 }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>


