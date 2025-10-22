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

        <div class="overflow-x-auto scrollbar-table">
            <table class="table-auto border-collapse w-full whitespace-nowrap text-left text-gray-500 dark:text-dark-text">
                <thead>
                    <tr class="text-primary-500">
                        <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">{{ translate('Nom') }}</th>
                        <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">{{ translate('Cours') }}</th>
                        <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">{{ translate('Lien') }}</th>
                        <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">{{ translate('Inscriptions') }}</th>
                        <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">{{ translate('Statut') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-dark-border-three">
                    @forelse($enrollmentLinks as $link)
                        <tr>
                            <td class="px-4 py-4">{{ $link->name }}</td>
                            <td class="px-4 py-4">{{ $link->course->title ?? 'N/A' }}</td>
                            <td class="px-4 py-4">
                                <input type="text" value="{{ url('/enroll/' . $link->slug) }}" readonly
                                    class="form-input text-sm w-48 dark:bg-gray-700 border-none">
                            </td>
                            <td class="px-4 py-4">{{ $link->current_enrollments }} / {{ $link->max_enrollments ?? 'Illimité' }}</td>
                            <td class="px-4 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $link->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
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
        <!-- Start Pagination -->
        {{ $enrollmentLinks->links('portal::admin.pagination.paginate') }}
        <!-- End Pagination -->
    </div>
</x-dashboard-layout>
