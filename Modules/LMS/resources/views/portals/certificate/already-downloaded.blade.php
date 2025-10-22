<x-dashboard-layout>
    <div class="card">
        <div class="card-body text-center py-8">
            <!-- Icône d'avertissement -->
            <div class="mb-6">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-yellow-100 rounded-full mb-4">
                    <svg class="w-10 h-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
            </div>

            <!-- Titre -->
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
                🔒 Certificat Déjà Téléchargé
            </h2>

            <!-- Message principal -->
            <div class="max-w-2xl mx-auto mb-6">
                <p class="text-lg text-gray-600 dark:text-gray-300 mb-4">
                    Ce certificat a déjà été téléchargé le 
                    <strong class="text-gray-900 dark:text-white">
                        {{ $certificate->downloaded_at->format('d/m/Y à H:i') }}
                    </strong>
                </p>
                
                <p class="text-gray-600 dark:text-gray-300 mb-6">
                    Pour des raisons de sécurité et d'intégrité, chaque certificat ne peut être téléchargé qu'une seule fois.
                </p>

                <!-- Informations de téléchargement -->
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 mb-6">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Informations de téléchargement :</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Date de téléchargement :</span>
                            <span class="text-gray-900 dark:text-white font-medium">
                                {{ $certificate->downloaded_at->format('d/m/Y H:i:s') }}
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Nombre de téléchargements :</span>
                            <span class="text-gray-900 dark:text-white font-medium">
                                {{ $certificate->download_count }}
                            </span>
                        </div>
                        @if($certificate->download_ip)
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Adresse IP :</span>
                            <span class="text-gray-900 dark:text-white font-medium">
                                {{ $certificate->download_ip }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('student.certificate.index') }}" 
                   class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Retour aux Certificats
                </a>
                
                <a href="{{ route('student.dashboard') }}" 
                   class="inline-flex items-center px-6 py-3 bg-gray-600 text-white font-medium rounded-lg hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path>
                    </svg>
                    Tableau de Bord
                </a>
            </div>

            <!-- Note de sécurité -->
            <div class="mt-8 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-100">Pourquoi cette limitation ?</h4>
                        <p class="text-sm text-blue-800 dark:text-blue-200 mt-1">
                            Cette restriction protège l'intégrité de vos certificats et empêche leur utilisation frauduleuse. 
                            Si vous avez perdu votre certificat, contactez le support technique.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>

