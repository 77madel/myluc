@php
    $answers = [];
    $questionScore = $question['question_score'] ?? null;
@endphp
@foreach ($question['question_answers'] as $questionAnswer)
    @php
        $answers[] = $questionAnswer['answer']['name'];
        $userAnswer = $questionAnswer['take_answer']['value'] ?? '';
        $correctAnswer = $questionAnswer['answer']['name'];
        $isCorrect = strtolower(trim($userAnswer)) === strtolower(trim($correctAnswer));
        $showResults = $disabled == 'disabled';
    @endphp
    <div class="flex items-start gap-3 dk-border-one rounded-lg p-3.5
        @if($showResults)
            @if($isCorrect)
                bg-green-50 border-green-200
            @else
                bg-red-50 border-red-200
            @endif
        @endif">
        <div class="flex-1">
            <input type="text" name="answers[{{ $question['id'] }}][{{ $questionAnswer['id'] }}][]"
                class="form-input focus-visible:outline-primary fill-in-blank w-full
                @if($showResults)
                    @if($isCorrect)
                        border-green-300 bg-green-50
                    @else
                        border-red-300 bg-red-50
                    @endif
                @endif"
                value="{{ $userAnswer }}" {{ $disabled }}>
        </div>
        @if($showResults)
            <div class="flex items-center gap-2">
                @if($isCorrect)
                    <i class="ri-check-line text-green-600 text-lg" title="{{ translate('Bonne réponse') }}"></i>
                @else
                    <i class="ri-close-line text-red-600 text-lg" title="{{ translate('Mauvaise réponse') }}"></i>
                    <span class="text-sm text-gray-600">
                        {{ translate('Correct:') }} <strong>{{ $correctAnswer }}</strong>
                    </span>
                @endif
            </div>
        @endif
    </div>
@endforeach

@if ($disabled == 'disabled')
    <x-theme::exam.quiz.result-show :questionScore="$questionScore" :answers="$answers" />
    @php
        reset($answers);
    @endphp
@endif
