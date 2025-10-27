@php
    $inputType = $question['question_type'] == 'multiple-choice' ? 'checkbox' : 'radio';
    $answers = [];
    $questionScore = $question['question_score'] ?? null;
@endphp

@foreach ($question['question_answers'] as $questionAnswer)
    @php
        $answers[] = $questionAnswer['correct'] == 1 ? $questionAnswer['answer']['name'] : '';
        $isCorrect = $questionAnswer['correct'] == 1;
        $isSelected = $questionAnswer['take_answer'] ?? false;
        $showResults = $disabled == 'disabled';
    @endphp
    <li class="option">
        <label for="q-1-{{ $questionAnswer['id'] }}"
            class="flex items-start gap-3 dk-border-one rounded-lg p-3.5 cursor-pointer select-none
            @if($showResults)
                @if($isCorrect)
                    bg-green-50 border-green-200
                @elseif($isSelected && !$isCorrect)
                    bg-red-50 border-red-200
                @endif
            @endif">
            <input type="{{ $inputType }}" name="answers[]" {{ $questionAnswer['take_answer'] ? 'checked' : '' }}
                value="{{ $questionAnswer['id'] }}" id="q-1-{{ $questionAnswer['id'] }}" {{ $disabled }}
                data-correct="{{ $questionAnswer['correct'] }}"
                class=" {{ $inputType == 'checkbox' ? 'lms-checkbox-check checkbox checkbox-primary' : 'lms-radio-check radio radio-primary' }} quizSelectAnswer  ">
            <span class="text-heading dark:text-white font-medium leading-none flex items-center gap-2">
                {{ $questionAnswer['answer']['name'] }}
                @if($showResults)
                    @if($isCorrect)
                        <i class="ri-check-line text-green-600 text-lg" title="{{ translate('Bonne réponse') }}"></i>
                    @elseif($isSelected && !$isCorrect)
                        <i class="ri-close-line text-red-600 text-lg" title="{{ translate('Mauvaise réponse') }}"></i>
                    @endif
                @endif
            </span>
        </label>
    </li>
@endforeach
@if ($disabled == 'disabled')
    <x-theme::exam.quiz.result-show :questionScore="$questionScore" :answers="$answers" />
    @php
        reset($answers);
    @endphp
@endif
