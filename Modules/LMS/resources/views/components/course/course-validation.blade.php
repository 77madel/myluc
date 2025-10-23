@php
    use Modules\LMS\Services\CourseValidationService;
    use Modules\LMS\Models\CourseCompletion;
    
    $validationService = new CourseValidationService();
    $validation = $validationService->validateCourse(auth()->id(), $course->id);
    $completion = $validationService->getCourseProgress(auth()->id(), $course->id);
@endphp

<div class="course-validation-card bg-white dark:bg-dark-card-two rounded-lg shadow-sm border border-gray-200 dark:border-dark-border-three p-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
            {{ translate('Progression du Cours') }}
        </h3>
        <div class="text-right">
            <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                {{ $validation['completion_percentage'] }}%
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                {{ $validation['completed_chapters'] }}/{{ $validation['total_chapters'] }} {{ translate('chapitres') }}
            </div>
        </div>
    </div>

    <!-- Barre de progression globale -->
    <div class="mb-6">
        <div class="flex justify-between text-sm text-gray-600 dark:text-gray-300 mb-2">
            <span>{{ translate('Progression globale') }}</span>
            <span>{{ $validation['completion_percentage'] }}%</span>
        </div>
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
            <div class="bg-gradient-to-r from-primary-500 to-primary-600 h-3 rounded-full transition-all duration-500"
                 style="width: {{ $validation['completion_percentage'] }}%"></div>
        </div>
    </div>

    <!-- Résumé des statistiques -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="text-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
            <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                {{ $validation['validation_summary']['total_lessons'] }}
            </div>
            <div class="text-sm text-blue-600 dark:text-blue-400">{{ translate('Leçons') }}</div>
        </div>
        <div class="text-center p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
            <div class="text-2xl font-bold text-green-600 dark:text-green-400">
                {{ $validation['validation_summary']['completed_lessons'] }}
            </div>
            <div class="text-sm text-green-600 dark:text-green-400">{{ translate('Terminées') }}</div>
        </div>
        <div class="text-center p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
            <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                {{ $validation['validation_summary']['total_quizzes'] }}
            </div>
            <div class="text-sm text-purple-600 dark:text-purple-400">{{ translate('Quiz') }}</div>
        </div>
        <div class="text-center p-3 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
            <div class="text-2xl font-bold text-orange-600 dark:text-orange-400">
                {{ $validation['validation_summary']['completed_quizzes'] }}
            </div>
            <div class="text-sm text-orange-600 dark:text-orange-400">{{ translate('Validés') }}</div>
        </div>
    </div>

    <!-- Détails par chapitre -->
    <div class="space-y-4">
        <h4 class="text-md font-medium text-gray-900 dark:text-white">
            {{ translate('Détails par Chapitre') }}
        </h4>
        
        @foreach($validation['chapters'] as $chapter)
            <div class="border border-gray-200 dark:border-dark-border-three rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <h5 class="font-medium text-gray-900 dark:text-white">
                        {{ $chapter['chapter_title'] }}
                    </h5>
                    <div class="flex items-center gap-2">
                        @if($chapter['is_completed'])
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                <i class="ri-check-line mr-1"></i>
                                {{ translate('Terminé') }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                <i class="ri-time-line mr-1"></i>
                                {{ translate('En cours') }}
                            </span>
                        @endif
                        <span class="text-sm text-gray-500 dark:text-gray-400">
                            {{ $chapter['completed_topics'] }}/{{ $chapter['total_topics'] }}
                        </span>
                    </div>
                </div>
                
                <!-- Barre de progression du chapitre -->
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mb-3">
                    @php
                        $chapterPercentage = $chapter['total_topics'] > 0 
                            ? round(($chapter['completed_topics'] / $chapter['total_topics']) * 100, 1)
                            : 0;
                    @endphp
                    <div class="bg-gradient-to-r from-primary-500 to-primary-600 h-2 rounded-full transition-all duration-500"
                         style="width: {{ $chapterPercentage }}%"></div>
                </div>
                
                <!-- Détails des topics -->
                @if(isset($chapter['topics']) && count($chapter['topics']) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                        @foreach($chapter['topics'] as $topic)
                            <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-800 rounded">
                                <span class="text-gray-700 dark:text-gray-300">{{ $topic['topic_title'] }}</span>
                                <div class="flex items-center gap-2">
                                    @if($topic['type'] === 'reading')
                                        <span class="text-xs px-2 py-1 rounded bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ translate('Lecture') }}
                                        </span>
                                    @elseif($topic['type'] === 'quiz')
                                        <span class="text-xs px-2 py-1 rounded bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                            {{ translate('Quiz') }}
                                        </span>
                                    @elseif($topic['type'] === 'video')
                                        <span class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                            {{ translate('Vidéo') }}
                                        </span>
                                    @endif
                                    
                                    @if($topic['is_completed'])
                                        <i class="ri-check-line text-green-600 dark:text-green-400"></i>
                                    @else
                                        <i class="ri-time-line text-yellow-600 dark:text-yellow-400"></i>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Message de completion -->
    @if($validation['is_completed'])
        <div class="mt-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="ri-check-circle-line text-2xl text-green-600 dark:text-green-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-green-800 dark:text-green-200">
                        {{ translate('🎉 Félicitations !') }}
                    </h3>
                    <div class="mt-2 text-sm text-green-700 dark:text-green-300">
                        <p>{{ translate('Vous avez terminé ce cours avec succès !') }}</p>
                        @if($course->is_certificate)
                            <p class="mt-1">{{ translate('Vous pouvez maintenant télécharger votre certificat.') }}</p>
                        @endif
                    </div>
                    @if($course->is_certificate)
                        <div class="mt-3">
                            <a href="{{ route('certificate.generate', $course->id) }}" 
                               class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                <i class="ri-download-line mr-2"></i>
                                {{ translate('Télécharger le Certificat') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <i class="ri-information-line text-2xl text-blue-600 dark:text-blue-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800 dark:text-blue-200">
                        {{ translate('Continuez votre apprentissage !') }}
                    </h3>
                    <div class="mt-2 text-sm text-blue-700 dark:text-blue-300">
                        <p>{{ translate('Vous avez terminé') }} {{ $validation['completed_chapters'] }} {{ translate('sur') }} {{ $validation['total_chapters'] }} {{ translate('chapitres.') }}</p>
                        <p class="mt-1">{{ translate('Terminez tous les chapitres pour obtenir votre certificat !') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
