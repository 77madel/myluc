@if (!empty($questionScore) && $questionScore['status'] == 1)
    <!-- STATUS CORRECT -->
    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mt-4">
        <div class="flex items-center gap-2.5">
            <i class="ri-checkbox-circle-line text-green-600 text-xl"></i>
            <span class="font-bold text-green-800">{{ translate('Correct') }}!</span>
            <span class="text-green-700 text-sm">(+{{ $questionScore['score'] ?? 0 }} points)</span>
        </div>
        <p class="text-green-700 text-sm mt-2">
            {{ translate('Félicitations ! Vous avez donné la bonne réponse.') }}
        </p>
    </div>
@else
    <!-- STATUS INCORRECT -->
    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mt-4">
        <div class="flex items-center gap-2.5">
            <i class="ri-close-circle-line text-red-600 text-xl"></i>
            <span class="font-bold text-red-800">{{ translate('Incorrect') }}</span>
        </div>
        <p class="text-red-700 text-sm mt-2 mb-3">
            {{ translate('Votre réponse n\'est pas correcte. Voici les bonnes réponses :') }}
        </p>
        <div class="bg-white border border-red-200 rounded p-3">
            <h4 class="font-semibold text-red-800 mb-2">{{ translate('Réponses correctes :') }}</h4>
            <ul class="space-y-1">
                @foreach ($answers as $key => $answer)
                    @if (!empty($answer))
                        <li class="flex items-center gap-2">
                            <i class="ri-check-line text-green-600"></i>
                            <span class="text-gray-800">{{ $answer }}</span>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
@endif
