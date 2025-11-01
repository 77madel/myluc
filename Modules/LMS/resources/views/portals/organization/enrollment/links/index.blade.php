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
                        <th class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right w-10">{{ translate('Actions') }}</th>
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
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $link->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                    {{ ucfirst($link->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <button 
                                    onclick="copyEnrollmentLink('{{ url('/enroll/' . $link->slug) }}')"
                                    class="btn-icon btn-primary-icon-light size-8"
                                    title="{{ translate('Copier le lien') }}">
                                    <i class="ri-file-copy-line text-inherit text-base"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center">
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

    <script>
    function copyEnrollmentLink(url) {
        // Copier dans le presse-papier
        navigator.clipboard.writeText(url).then(function() {
            // Afficher un message de succès
            if (typeof toastr !== 'undefined') {
                toastr.success('{{ translate("Lien copié dans le presse-papier !") }}');
            } else {
                alert('{{ translate("Lien copié !") }}');
            }
        }).catch(function(err) {
            console.error('Erreur lors de la copie:', err);
            // Fallback: sélectionner le texte
            const tempInput = document.createElement('input');
            tempInput.value = url;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);
            
            if (typeof toastr !== 'undefined') {
                toastr.success('{{ translate("Lien copié dans le presse-papier !") }}');
            } else {
                alert('{{ translate("Lien copié !") }}');
            }
        });
    }
    </script>
</x-dashboard-layout>
