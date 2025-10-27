<div class="quiz-details">
    <!-- En-tête du quiz -->
    <div class="bg-gradient-to-r from-primary-50 to-blue-50 dark:from-dark-card-two dark:to-dark-card rounded-lg p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-heading dark:text-white mb-2">
                    {{ $userQuiz->quiz->title }}
                </h2>
                <p class="text-gray-600 dark:text-gray-300">
                    {{ translate('Cours') }}: {{ $userQuiz->course->title ?? 'N/A' }}
                </p>
            </div>
            <div class="text-right">
                <div class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                    {{ $userQuiz->score }}/{{ $userQuiz->quiz->total_mark }}
                </div>
                <div class="text-sm text-gray-500">
                    @php
                        $percentage = $userQuiz->quiz->total_mark > 0 ? round(($userQuiz->score / $userQuiz->quiz->total_mark) * 100, 1) : 0;
                    @endphp
                    {{ $percentage }}%
                </div>
            </div>
        </div>
        
        <!-- Barre de progression -->
        <div class="mt-4">
            <div class="flex justify-between text-sm text-gray-600 dark:text-gray-300 mb-2">
                <span>{{ translate('Score') }}</span>
                <span>{{ translate('Note de passage') }}: {{ $userQuiz->quiz->pass_mark }}</span>
            </div>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                <div class="bg-gradient-to-r from-primary-500 to-blue-500 h-3 rounded-full transition-all duration-500" 
                     style="width: {{ $percentage }}%"></div>
            </div>
        </div>
    </div>

    <!-- Résumé des résultats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
            <div class="flex items-center">
                <i class="ri-check-line text-green-600 text-2xl mr-3"></i>
                <div>
                    <div class="text-sm text-green-600 dark:text-green-400">{{ translate('Questions Correctes') }}</div>
                    <div class="text-xl font-bold text-green-800 dark:text-green-200">
                        {{ $questionScores->where('status', 1)->count() }}
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <div class="flex items-center">
                <i class="ri-close-line text-red-600 text-2xl mr-3"></i>
                <div>
                    <div class="text-sm text-red-600 dark:text-red-400">{{ translate('Questions Incorrectes') }}</div>
                    <div class="text-xl font-bold text-red-800 dark:text-red-200">
                        {{ $questionScores->where('status', 0)->count() }}
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
            <div class="flex items-center">
                <i class="ri-question-line text-blue-600 text-2xl mr-3"></i>
                <div>
                    <div class="text-sm text-blue-600 dark:text-blue-400">{{ translate('Total Questions') }}</div>
                    <div class="text-xl font-bold text-blue-800 dark:text-blue-200">
                        {{ $userQuiz->quiz->questions->count() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Détails des questions -->
    <div class="space-y-6">
        <h3 class="text-lg font-semibold text-heading dark:text-white mb-4">
            {{ translate('Détails des Questions') }}
        </h3>
        
        @foreach ($userQuiz->quiz->questions as $index => $quizQuestion)
            @php
                $question = $quizQuestion->question;
                $userAnswer = $takeAnswers->get($quizQuestion->id);
                $questionScore = $questionScores->get($question->id);
                $isCorrect = $questionScore && $questionScore->status == 1;
            @endphp
            
            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-6 
                @if($isCorrect) bg-green-50 dark:bg-green-900/10 border-green-200 dark:border-green-800 @else bg-red-50 dark:bg-red-900/10 border-red-200 dark:border-red-800 @endif">
                
                <!-- En-tête de la question -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="bg-primary-100 dark:bg-primary-900 text-primary-800 dark:text-primary-200 px-3 py-1 rounded-full text-sm font-medium">
                                {{ translate('Question') }} {{ $index + 1 }}
                            </span>
                            <span class="bg-gray-100 dark:bg-gray-800 text-gray-800 dark:text-gray-200 px-3 py-1 rounded-full text-sm">
                                {{ $quizQuestion->mark }} {{ translate('points') }}
                            </span>
                            @if($isCorrect)
                                <span class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-3 py-1 rounded-full text-sm">
                                    <i class="ri-check-line mr-1"></i>{{ translate('Correct') }}
                                </span>
                            @else
                                <span class="bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 px-3 py-1 rounded-full text-sm">
                                    <i class="ri-close-line mr-1"></i>{{ translate('Incorrect') }}
                                </span>
                            @endif
                        </div>
                        <h4 class="text-lg font-medium text-heading dark:text-white">
                            {{ $question->name }}
                        </h4>
                    </div>
                </div>

                <!-- Réponses -->
                <div class="space-y-3">
                    @foreach ($quizQuestion->questionAnswers as $answer)
                        @php
                            $isCorrectAnswer = $answer->correct == 1;
                            $isUserAnswer = $userAnswer && $userAnswer->question_answer == $answer->id;
                        @endphp
                        
                        <div class="flex items-center gap-3 p-3 rounded-lg border
                            @if($isCorrectAnswer) bg-green-100 dark:bg-green-900/20 border-green-300 dark:border-green-700 @elseif($isUserAnswer && !$isCorrectAnswer) bg-red-100 dark:bg-red-900/20 border-red-300 dark:border-red-700 @else bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 @endif">
                            
                            <div class="flex items-center gap-3 flex-1">
                                @if($isCorrectAnswer)
                                    <i class="ri-check-line text-green-600 text-lg"></i>
                                @elseif($isUserAnswer && !$isCorrectAnswer)
                                    <i class="ri-close-line text-red-600 text-lg"></i>
                                @else
                                    <div class="w-5 h-5 border-2 border-gray-300 dark:border-gray-600 rounded"></div>
                                @endif
                                
                                <span class="text-gray-800 dark:text-gray-200">
                                    {{ $answer->answer->name }}
                                </span>
                            </div>
                            
                            @if($isCorrectAnswer)
                                <span class="text-green-600 text-sm font-medium">
                                    {{ translate('Bonne réponse') }}
                                </span>
                            @elseif($isUserAnswer && !$isCorrectAnswer)
                                <span class="text-red-600 text-sm font-medium">
                                    {{ translate('Votre réponse') }}
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Score de la question -->
                @if($questionScore)
                    <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 dark:text-gray-300">
                                {{ translate('Score obtenu') }}:
                            </span>
                            <span class="font-bold {{ $isCorrect ? 'text-green-600' : 'text-red-600' }}">
                                {{ $questionScore->score }}/{{ $quizQuestion->mark }}
                            </span>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <!-- Actions -->
    <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
        <a href="{{ route('exam.start', ['type' => $userQuiz->exam_type, 'exam_type_id' => $userQuiz->quiz_id, 'course_id' => $userQuiz->course_id]) }}"
           class="btn b-solid btn-primary-solid">
            <i class="ri-eye-line mr-2"></i>
            {{ translate('Voir le Quiz Complet') }}
        </a>
        
        <a href="{{ route('course.detail', $userQuiz->course->slug) }}"
           class="btn b-solid btn-info-solid">
            <i class="ri-book-open-line mr-2"></i>
            {{ translate('Retour au Cours') }}
        </a>
    </div>
</div>
