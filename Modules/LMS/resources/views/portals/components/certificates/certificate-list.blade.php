<div class="overflow-x-auto scrollbar-table">
    <table
        class="table-auto w-full whitespace-nowrap text-left text-gray-500 dark:text-dark-text font-medium leading-non">
        <thead class="text-primary-500">
            <tr>
                <th
                    class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">
                    {{ translate('Certificate') }}
                </th>
                <th
                    class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">
                    {{ translate('Certificate ID') }}
                </th>
                <th
                    class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">
                    {{ translate('Certificate Type') }}
                </th>

                <th
                    class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right">
                    {{ translate('Certificate Date') }}
                </th>

                <th
                    class="px-3.5 py-4 bg-[#F2F4F9] dark:bg-dark-card-two first:rounded-l-lg last:rounded-r-lg first:dk-theme-card-square-left last:dk-theme-card-square-right w-10">
                    {{ translate('Action') }}
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-dark-border-three">
            @foreach ($certificates as $certificate)
                <tr>
                    <td class="items-center gap-2 px-3.5 py-4">
                        <h6 class="leading-none text-heading dark:text-white font-bold mb-1.5 line-clamp-1">
                            {{ $certificate->subject ?? '' }}
                        </h6>
                    </td>
                    <td class="px-3.5 py-4">
                        {{ $certificate->certificate_id ?? 0 }}
                    </td>
                    <td class="px-3.5 py-4">
                        {{ $certificate?->type }}
                    </td>
                    <td class="px-3.5 py-4">
                        {{ customDateFormate($certificate->certificated_date, format: 'm D  Y') }}
                    </td>
                    <td class="px-3.5 py-4">
                        <div class="flex items-center gap-2">
                            <!-- Bouton View (toujours disponible) -->
                            <a href="{{ route('student.certificate.view', $certificate->id) }}" target="_blank"
                                class="btn b-solid btn-info-solid btn-sm" title="{{ translate('View Certificate') }}">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                {{ translate('View') }}
                            </a>
                            
                            @if($certificate->isDownloaded())
                                <!-- Certificat déjà téléchargé -->
                                <div class="flex items-center gap-1">
                                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        Téléchargé
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $certificate->downloaded_at->format('d/m/Y') }}
                                    </span>
                                </div>
                            @else
                                <!-- Bouton Télécharger (première fois seulement) -->
                                <a href="{{ route('student.certificate.download', $certificate->id) }}"
                                    class="btn b-solid btn-success-solid btn-sm" title="{{ translate('Download Certificate') }}">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    {{ translate('Télécharger') }}
                                </a>
                            @endif
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
