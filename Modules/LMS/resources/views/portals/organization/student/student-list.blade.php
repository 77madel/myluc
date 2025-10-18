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
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">{{ translate('Département') }}</th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">{{ translate('Statut') }}</th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">{{ translate('Date d\'Inscription') }}</th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">{{ translate('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($students as $student)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $student->userable->first_name ?? '' }} {{ $student->userable->last_name ?? '' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $student->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">N/A</td>
                            <td class="px-6 py-4 whitespace-nowrap">Actif</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $student->created_at ? $student->created_at->format('Y-m-d') : 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('organization.students.progress', $student->id) }}" class="text-primary-600 hover:text-primary-900">{{ translate('Voir Progression') }}</a>
                            </td>
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