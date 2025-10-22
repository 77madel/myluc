<x-dashboard-layout>
    <x-slot:title>{{ translate('Étudiants de l\'Organisation') }}</x-slot:title>
    <div class="card p-6">
        <h3 class="text-xl font-semibold mb-4">{{ translate('Liste des Étudiants') }}</h3>

        <div class="mb-4">
            <a href="{{ route('organization.students.export') }}" class="btn b-solid btn-primary-solid">
                {{ translate('Exporter les Étudiants') }}
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">{{ translate('Nom') }}</th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">{{ translate('Email') }}</th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">{{ translate('Cours Inscrits') }}</th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">{{ translate('Progression Moyenne') }}</th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">{{ translate('Statut') }}</th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">{{ translate('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($students as $student)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $student->userable->first_name ?? '' }} {{ $student->userable->last_name ?? '' }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $student->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ $student->enrolled_courses_count ?? 0 }} {{ translate('cours') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-1 mr-2">
                                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                                 style="width: {{ $student->average_progress ?? 0 }}%"></div>
                                        </div>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $student->average_progress ?? 0 }}%
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ translate('Actif') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('organization.students.progress', $student->id) }}"
                                       class="btn b-solid btn-success-solid mr-2 text-white hover:text-green-700 dark:text-blue-400">
                                        {{ translate('Progression') }}
                                    </a>

                                    <a href="{{ route('organization.student.profile', $student->id) }}"
                                       class="btn b-solid btn-primary-solid text-gray-600 hover:text-gray-900 dark:text-gray-400">
                                        {{ translate('Profil') }}
                                    </a>
                                </div>
                            </td>
                            {{--<td class="px-4 py-3 lg:px-6 lg:py-4 whitespace-nowrap">
                                <div class="flex flex-col sm:flex-row gap-2 sm:gap-3 justify-end">
                                    <a href="{{ route('organization.students.progress', $student->id) }}"
                                       class="btn b-solid btn-primary-solid inline-flex items-center justify-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 bg-blue-50 text-blue-700 hover:bg-blue-100 hover:text-blue-900 border border-blue-200 dark:bg-blue-900/30 dark:text-blue-300 dark:border-blue-800 dark:hover:bg-blue-800/40 dark:hover:text-blue-200 shadow-sm hover:shadow-md">
                                        <svg class="w-4 h-4 mr-2 hidden xs:inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                        {{ translate('Progression') }}
                                    </a>

                                    <a href="{{ route('organization.student.profile', $student->id) }}"
                                       class="btn b-solid btn-primary-solid inline-flex items-center justify-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 bg-gray-50 text-gray-700 hover:bg-gray-100 hover:text-gray-900 border border-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-700 dark:hover:text-gray-200 shadow-sm hover:shadow-md">
                                        <svg class="w-4 h-4 mr-2 hidden xs:inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        {{ translate('Profil') }}
                                    </a>
                                </div>
                            </td>--}}
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center">{{ translate('Aucun étudiant inscrit.') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $students->links() }}
        </div>
    </div>
</x-dashboard-layout>
