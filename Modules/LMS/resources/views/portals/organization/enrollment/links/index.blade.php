<x-dashboard-layout>
    <x-slot:title>{{ translate('Liens d\'Inscription') }}</x-slot:title>
    <div class="card p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-semibold">{{ translate('Liens d\'Inscription Générés Automatiquement') }}</h3>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                {{ translate('Les liens sont créés automatiquement lors de l\'achat de cours') }}
            </div>
        </div>

        @if (session('success'))
            <div class="text-green-500 mb-4">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="text-red-500 mb-4">{{ session('error') }}</div>
        @endif

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">{{ translate('Nom') }}</th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">{{ translate('Cours') }}</th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">{{ translate('Lien') }}</th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">{{ translate('Inscriptions') }}</th>
                        <th class="px-6 py-3 bg-gray-50 dark:bg-gray-800 text-left text-xs font-medium text-gray-500 dark:text-gray-200 uppercase tracking-wider">{{ translate('Statut') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($enrollmentLinks as $link)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $link->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $link->course->title ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="text" value="{{ url('/enroll/' . $link->slug) }}" readonly
                                    class="form-input text-sm w-48 bg-gray-100 dark:bg-gray-700 border-none">
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $link->current_enrollments }} / {{ $link->max_enrollments ?? 'Illimité' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full {{ $link->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($link->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center">
                                <div class="text-gray-500 dark:text-gray-400">
                                    <i class="ri-information-line text-2xl mb-2 block"></i>
                                    {{ translate('Aucun lien d\'inscription généré.') }}<br>
                                    <small class="text-sm">{{ translate('Les liens apparaîtront automatiquement après l\'achat de cours.') }}</small>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $enrollmentLinks->links() }}
        </div>
    </div>
</x-dashboard-layout>
