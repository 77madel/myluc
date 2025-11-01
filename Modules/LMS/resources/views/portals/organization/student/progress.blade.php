<x-dashboard-layout>
    <x-slot:title>{{ translate('Progression de l\'Étudiant') }}: {{ $student->userable->first_name ?? '' }} {{ $student->userable->last_name ?? '' }}</x-slot:title>

    <div class="mb-4">
        <a href="{{ route('organization.students.index') }}" class="btn btn-secondary">
            {{ translate('Retour à la liste') }}
        </a>
    </div>
    
    <div class="grid grid-cols-12 gap-4">
        <!-- Informations de l'étudiant -->
        <div class="col-span-full lg:col-span-4">
            <div class="card p-6">
                <h3 class="text-xl font-semibold mb-4 text-heading dark:text-white">{{ translate('Informations de l\'Étudiant') }}</h3>
                
                <div class="space-y-3">
                    <div>
                        <label class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ translate('Nom complet') }}</label>
                        <p class="text-lg font-semibold text-heading dark:text-white">{{ $student->userable->first_name ?? '' }} {{ $student->userable->last_name ?? '' }}</p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ translate('Email') }}</label>
                        <p class="text-sm font-medium text-heading dark:text-white">{{ $student->email }}</p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ translate('Date d\'inscription') }}</label>
                        <p class="text-sm font-medium text-heading dark:text-white">{{ $student->created_at ? $student->created_at->format('d/m/Y') : 'N/A' }}</p>
                    </div>
                    
                    <div>
                        <label class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ translate('Statut') }}</label>
                        <div class="mt-1">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                {{ translate('Actif') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progression des cours -->
        <div class="col-span-full lg:col-span-8">
            <div class="card p-6">
                <h3 class="text-xl font-semibold mb-4 text-heading dark:text-white">{{ translate('Progression par Cours') }}</h3>
                
        @forelse($progress as $item)
                    <div class="mb-6 p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ $item['course']->title ?? 'N/A' }}
                            </h4>
                            <span class="px-3 py-1 text-sm font-medium rounded-full 
                                {{ $item['topic_completion_percentage'] == 100 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                   ($item['topic_completion_percentage'] > 0 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200') }}">
                                {{ $item['topic_completion_percentage'] }}% {{ translate('terminé') }}
                            </span>
                        </div>

                        <!-- Barre de progression -->
                        <div class="mb-4">
                            <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
                                <span>{{ translate('Progression') }}</span>
                                <span>{{ $item['completed_topics'] }}/{{ $item['total_topics'] }} {{ translate('leçons') }}</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="bg-primary dark:bg-primary h-2 rounded-full transition-all duration-300" 
                                     style="width: {{ $item['topic_completion_percentage'] }}%"></div>
                            </div>
                        </div>

                        <!-- Détails de progression -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <label class="font-medium text-gray-500 dark:text-gray-400">{{ translate('Chapitres terminés') }}</label>
                                <p class="text-lg font-semibold text-heading dark:text-white">{{ $item['chapter_progress']['completed_chapters'] }}/{{ $item['chapter_progress']['total_chapters'] }}</p>
                            </div>
                            
                            <div>
                                <label class="font-medium text-gray-500 dark:text-gray-400">{{ translate('Leçons terminées') }}</label>
                                <p class="text-lg font-semibold text-heading dark:text-white">{{ $item['completed_topics'] }}/{{ $item['total_topics'] }}</p>
                            </div>
                            
                            <div>
                                <label class="font-medium text-gray-500 dark:text-gray-400">{{ translate('Pourcentage global') }}</label>
                                <p class="text-lg font-semibold text-primary dark:text-primary">{{ $item['topic_completion_percentage'] }}%</p>
                            </div>
                        </div>

                        <!-- Détails des chapitres -->
                        @if($item['chapter_progress']['chapters']->count() > 0)
                            <div class="mt-4">
                                <h5 class="font-medium text-heading dark:text-white mb-2">{{ translate('Détails par chapitre') }}</h5>
                                <div class="space-y-2">
                                    @foreach($item['chapter_progress']['chapters'] as $chapter)
                                        @php
                                            $chapterProgress = $chapter->progress->first();
                                            $chapterStatus = $chapterProgress ? $chapterProgress->status : 'not_started';
                                        @endphp
                                        <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-dark-card-two rounded">
                                            <span class="text-sm text-heading dark:text-white">{{ $chapter->title }}</span>
                                            <span class="text-xs px-2 py-1 rounded-full 
                                                {{ $chapterStatus === 'completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 
                                                   ($chapterStatus === 'in_progress' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200') }}">
                                                {{ ucfirst($chapterStatus) }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
            </div>
        @empty
                    <div class="text-center py-8">
                        <div class="text-gray-400 dark:text-gray-500 mb-4">
                            <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-heading dark:text-white mb-2">{{ translate('Aucun cours inscrit') }}</h3>
                        <p class="text-gray-500 dark:text-gray-400">{{ translate('Cet étudiant n\'est inscrit à aucun cours pour le moment.') }}</p>
                    </div>
        @endforelse
            </div>
        </div>
    </div>
</x-dashboard-layout>
