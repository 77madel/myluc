<div id="course-curriculumn" class="tabcontent hidden">
    <h3 class="lg:text-32 sm:text-28 text-xl font-semibold text-heading mb-4">
        {{ translate('Course Curriculum') }}
    </h3>
    <div class="space-y-5">
        @foreach ($course->chapters as $chapter)
            @php
                // Récupérer la progression du chapitre pour l'utilisateur connecté
                $chapterProgress = null;
                if (auth()->check() && auth()->user()->guard === 'student') {
                    $chapterProgress = \Modules\LMS\Models\ChapterProgress::where('user_id', auth()->id())
                        ->where('chapter_id', $chapter->id)
                        ->first();
                }
            @endphp
            <div class="border border-gray-200 rounded-md chapter-item" data-chapter-id="{{ $chapter->id }}">
                <div class="accordion course-over-accord group/courseAccord">
                    <div class="flex items-center justify-between w-full">
                        <h5 class="font-medium text-heading grow">
                            {{ $chapter->title }}
                        </h5>
                        <div class="flex items-center gap-3">
                            <h6 class="text-gray shrink-0"> 
                                {{ translate('Lesson') }}:
                                <strong>{{ $chapter?->topics?->count() }}</strong>
                            </h6>
                            
                            @if(auth()->check() && auth()->user()->guard === 'student')
                                <!-- Indicateur de progression -->
                                <div class="chapter-progress-indicator">
                                    @if($chapterProgress && $chapterProgress->status === 'completed')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="ri-check-line mr-1"></i>
                                            {{ translate('Terminé') }}
                                        </span>
                                    @elseif($chapterProgress && $chapterProgress->status === 'in_progress')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <i class="ri-play-line mr-1"></i>
                                            {{ translate('En cours') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            <i class="ri-time-line mr-1"></i>
                                            {{ translate('Non commencé') }}
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="accordionpanel panel">
                    @foreach ($chapter->topics as $topic)
                        @if ($topic?->topicable?->topic_type?->slug == 'video')
                            <a href="{{ authCheck() ? route('play.course', $course->slug . '?type=' . $topic?->topicable?->topic_type?->slug . '&item=' . $topic->id) : '#' }}" class="course-accord-item gap-5 hover:bg-slate-100 group/curriculum" aria-label="Course curriculum">
                                <div class="flex items-center gap-2 font-medium group-hover/curriculum:text-primary duration-200 grow">
                                    <i class="ri-file-video-line"></i>
                                    <span class="-mb-1 line-clamp-1">{{ $topic?->topicable?->title }}</span>
                                </div>
                                <div class="text-primary text-sm flex items-center justify-center gap-4 shrink-0">{{ translate('Video') }}: {{ $topic?->topicable?->duration }}</div>
                            </a>
                        @endif

                        @if ($topic?->topicable?->topic_type?->slug == 'reading')
                            <a href="{{ authCheck() ? route('play.course', $course->slug . '?type=' . $topic?->topicable?->topic_type?->slug . '&item=' . $topic->id) : '#' }}" class="course-accord-item gap-5 hover:bg-slate-100 group/curriculum" aria-label="Course curriculum">
                                <div class="flex items-center gap-2 font-medium group-hover/curriculum:text-primary duration-200 grow">
                                    <i class="ri-file-text-line"></i>
                                    <span class="-mb-1 line-clamp-1">{{ $topic?->topicable?->title }}</span>
                                </div>
                                <div class="text-primary text-sm flex items-center justify-center gap-4 shrink-0">{{ translate('Read') }}</div>
                            </a>
                        @endif

                        @if ($topic?->topicable?->topic_type?->slug == 'supplement')
                            <a href="{{ authCheck() ? route('play.course', $course->slug . '?type=' . $topic?->topicable?->topic_type?->slug . '&item=' . $topic->id) : '#' }}" class="course-accord-item gap-5 hover:bg-slate-100 group/curriculum" aria-label="Course curriculum">
                                <div class="flex items-center gap-2 font-medium group-hover/curriculum:text-primary duration-200 grow">
                                    <i class="ri-file-text-line"></i>
                                    <span class="-mb-1 line-clamp-1">{{ $topic?->topicable?->title }}</span>
                                </div>
                                <div class="text-primary text-sm flex items-center justify-center gap-4 shrink-0">{{ translate('Read') }}</div>
                            </a>
                        @endif

                        @if ($topic?->topicable?->topic_type?->slug == 'assignment')
                            <a href="{{ authCheck() ? route('play.course', $course->slug . '?type=' . $topic?->topicable?->topic_type?->slug . '&item=' . $topic->id) : '#' }}" class="course-accord-item gap-5 hover:bg-slate-100 group/curriculum" aria-label="Course curriculum">
                                <div class="flex items-center gap-2 font-medium group-hover/curriculum:text-primary duration-200 grow">
                                    <i class="ri-a-b"></i>
                                    <span class="-mb-1 line-clamp-1">{{ $topic?->topicable?->title }}</span>
                                </div>
                                <div class="text-primary text-sm flex items-center justify-center gap-4 shrink-0">{{ translate('Assignment') }}</div>
                            </a>
                        @endif

                        @if ($topic?->topicable?->topic_type?->slug == 'quiz')
                            <a href="{{ authCheck() ? route('play.course', $course->slug . '?type=' . $topic?->topicable?->topic_type?->slug . '&item=' . $topic->id) : '#' }}" class="course-accord-item gap-5 hover:bg-slate-100 group/curriculum" aria-label="Course curriculum">
                                <div class="flex items-center gap-2 font-medium group-hover/curriculum:text-primary duration-200 grow">
                                    <i class="ri-questionnaire-line"></i>
                                    <span class="-mb-1 line-clamp-1">{{ $topic?->topicable?->title }}</span>
                                </div>
                                <div class="text-primary text-sm flex items-center justify-center gap-4 shrink-0">{{ translate('Quiz') }}</div>
                            </a>
                        @endif
                    @endforeach
                    
                    @if(auth()->check() && auth()->user()->guard === 'student')
                        <!-- Boutons de progression du chapitre -->
                        <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-600">
                                    @if($chapterProgress && $chapterProgress->status === 'completed')
                                        <span class="text-green-600">
                                            <i class="ri-check-circle-line mr-1"></i>
                                            {{ translate('Chapitre terminé le') }} {{ $chapterProgress->completed_at->format('d/m/Y à H:i') }}
                                        </span>
                                    @elseif($chapterProgress && $chapterProgress->status === 'in_progress')
                                        <span class="text-yellow-600">
                                            <i class="ri-play-circle-line mr-1"></i>
                                            {{ translate('En cours depuis le') }} {{ $chapterProgress->started_at->format('d/m/Y à H:i') }}
                                        </span>
                                    @else
                                        <span class="text-gray-500">
                                            <i class="ri-time-line mr-1"></i>
                                            {{ translate('Chapitre non commencé') }}
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="flex gap-2">
                                    @if(!$chapterProgress || $chapterProgress->status === 'not_started')
                                        <button type="button" 
                                                class="chapter-start-btn inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-primary-600 hover:bg-primary-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                                                data-chapter-id="{{ $chapter->id }}">
                                            <i class="ri-play-line mr-1"></i>
                                            {{ translate('Commencer') }}
                                        </button>
                                    @elseif($chapterProgress->status === 'in_progress')
                                        <button type="button" 
                                                class="chapter-complete-btn inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-success hover:bg-success focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-success"
                                                data-chapter-id="{{ $chapter->id }}">
                                            <i class="ri-check-line mr-1"></i>
                                            {{ translate('Marquer comme terminé') }}
                                        </button>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-green-800 bg-green-100">
                                            <i class="ri-check-circle-line mr-1"></i>
                                            {{ translate('Terminé') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>

@if(auth()->check() && auth()->user()->guard === 'student')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gérer le clic sur le bouton "Commencer"
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('chapter-start-btn') || e.target.closest('.chapter-start-btn')) {
            const button = e.target.classList.contains('chapter-start-btn') ? e.target : e.target.closest('.chapter-start-btn');
            const chapterId = button.getAttribute('data-chapter-id');
            markChapterAsStarted(chapterId, button);
        }
        
        // Gérer le clic sur le bouton "Marquer comme terminé"
        if (e.target.classList.contains('chapter-complete-btn') || e.target.closest('.chapter-complete-btn')) {
            const button = e.target.classList.contains('chapter-complete-btn') ? e.target : e.target.closest('.chapter-complete-btn');
            const chapterId = button.getAttribute('data-chapter-id');
            markChapterAsCompleted(chapterId, button);
        }
    });
    
    function markChapterAsStarted(chapterId, button) {
        button.disabled = true;
        button.innerHTML = '<i class="ri-loader-4-line mr-1 animate-spin"></i> {{ translate("En cours...") }}';
        
        fetch(`{{ route('student.chapter.start', '') }}/${chapterId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Mettre à jour l'interface
                updateChapterProgress(chapterId, 'in_progress', data.progress);
                showNotification('{{ translate("Chapitre marqué comme commencé!") }}', 'success');
            } else {
                showNotification(data.message || '{{ translate("Erreur lors de la mise à jour") }}', 'error');
                button.disabled = false;
                button.innerHTML = '<i class="ri-play-line mr-1"></i> {{ translate("Commencer") }}';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('{{ translate("Erreur de connexion") }}', 'error');
            button.disabled = false;
            button.innerHTML = '<i class="ri-play-line mr-1"></i> {{ translate("Commencer") }}';
        });
    }
    
    function markChapterAsCompleted(chapterId, button) {
        button.disabled = true;
        button.innerHTML = '<i class="ri-loader-4-line mr-1 animate-spin"></i> {{ translate("En cours...") }}';
        
        fetch(`{{ route('student.chapter.complete', '') }}/${chapterId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                // Mettre à jour l'interface
                updateChapterProgress(chapterId, 'completed', data.progress);
                showNotification('{{ translate("Chapitre marqué comme terminé!") }}', 'success');
                
                // Afficher le pourcentage de completion du cours si disponible
                if (data.course_completion !== undefined) {
                    showNotification(`{{ translate("Progression du cours:") }} ${data.course_completion}%`, 'info');
                }
            } else {
                showNotification(data.message || '{{ translate("Erreur lors de la mise à jour") }}', 'error');
                button.disabled = false;
                button.innerHTML = '<i class="ri-check-line mr-1"></i> {{ translate("Marquer comme terminé") }}';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('{{ translate("Erreur de connexion") }}', 'error');
            button.disabled = false;
            button.innerHTML = '<i class="ri-check-line mr-1"></i> {{ translate("Marquer comme terminé") }}';
        });
    }
    
    function updateChapterProgress(chapterId, status, progress) {
        const chapterItem = document.querySelector(`[data-chapter-id="${chapterId}"]`);
        if (!chapterItem) return;
        
        const progressIndicator = chapterItem.querySelector('.chapter-progress-indicator');
        const progressSection = chapterItem.querySelector('.px-4.py-3.bg-gray-50');
        
        if (status === 'in_progress') {
            // Mettre à jour l'indicateur
            progressIndicator.innerHTML = `
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    <i class="ri-play-line mr-1"></i>
                    {{ translate('En cours') }}
                </span>
            `;
            
            // Mettre à jour la section de progression
            const startDate = new Date().toLocaleDateString('fr-FR') + ' à ' + new Date().toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit'});
            progressSection.querySelector('.text-sm.text-gray-600 span').innerHTML = `
                <span class="text-yellow-600">
                    <i class="ri-play-circle-line mr-1"></i>
                    {{ translate('En cours depuis le') }} ${startDate}
                </span>
            `;
            
            // Changer le bouton
            const buttonContainer = progressSection.querySelector('.flex.gap-2');
            buttonContainer.innerHTML = `
                <button type="button" 
                        class="chapter-complete-btn inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                        data-chapter-id="${chapterId}">
                    <i class="ri-check-line mr-1"></i>
                    {{ translate('Marquer comme terminé') }}
                </button>
            `;
        } else if (status === 'completed') {
            // Mettre à jour l'indicateur
            progressIndicator.innerHTML = `
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <i class="ri-check-line mr-1"></i>
                    {{ translate('Terminé') }}
                </span>
            `;
            
            // Mettre à jour la section de progression
            const completedDate = new Date().toLocaleDateString('fr-FR') + ' à ' + new Date().toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit'});
            progressSection.querySelector('.text-sm.text-gray-600 span').innerHTML = `
                <span class="text-green-600">
                    <i class="ri-check-circle-line mr-1"></i>
                    {{ translate('Chapitre terminé le') }} ${completedDate}
                </span>
            `;
            
            // Changer le bouton
            const buttonContainer = progressSection.querySelector('.flex.gap-2');
            buttonContainer.innerHTML = `
                <span class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-md text-green-800 bg-green-100">
                    <i class="ri-check-circle-line mr-1"></i>
                    {{ translate('Terminé') }}
                </span>
            `;
        }
    }
    
    function showNotification(message, type = 'info') {
        // Créer une notification simple
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-md shadow-lg ${
            type === 'success' ? 'bg-green-500 text-white' : 
            type === 'error' ? 'bg-red-500 text-white' : 
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Supprimer la notification après 3 secondes
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
});
</script>
@endif