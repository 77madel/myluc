@php
    // Pour Admin et Instructeur, donner accès libre
    if (isAdmin() || isInstructor()) {
        // Admin/Instructeur ont accès libre sans vérification
        $auth = Auth::guard('admin')->check() ? Auth::guard('admin')->user() : authCheck();
        $purchaseCheck = true;  // Considéré comme ayant acheté
    } else {
        // Student/Organization : vérification normale
        $auth = authCheck();
        $purchaseCheck = false;
        if ($auth) {
            $purchaseCheck = purchaseCheck($course->id, 'course');
        }
    }
@endphp

@include('theme::layouts.partials.head')
<link rel="stylesheet" href="{{ asset('lms/frontend/assets/vendor/css/plyr.min.css') }}">

<body>
    @include('theme::layouts.partials.header', [
        'style' => 'one',
        'class' => "flex-center bg-primary-50 shadow-md py-4 fixed inset-0 h-[theme('spacing.header')] z-[101]",
        'data' => [
            'components' => [
                'inner-header-top' => '',
            ],
            'header_class' =>
                "flex-center bg-primary-50 shadow-md py-4 fixed inset-0 h-[theme('spacing.header')] z-[101]",
        ],
    ])
    <!-- END HEADER AREA -->
    <main>
        <div class="flex mb-16 sm:mb-24 lg:mb-[120px]">
            <!-- START COURSE VIDEO AREA -->
            <div class="relative p-3 mt-[theme('spacing.header')] w-[100%] lg:w-[calc(100%_-_19.5rem)] xl:w-[calc(100%_-_28.6rem)] overflow-hidden z-10">
                <div class="relative overflow-hidden">
                    <!-- COURSE CONTENT BUTTON FOR SMALL DEVICE -->
                    <div class="flex-center lg:hidden shrink-0 absolute top-0 right-0 translate-x-[128px] hover:translate-x-0 z-10 custom-transition">
                        <button type="button" aria-label="Course off-canvas drawer"
                            data-offcanvas-id="course-content-drawer"
                            class="btn b-solid bg-heading !text-white font-normal border border-gray-500 rounded-none">
                            <i class="ri-arrow-left-line"></i>
                            {{ translate('Course Content') }}
                        </button>
                    </div>
                    <div class="rounded-xl overflow-hidden curriculum-content">
                        <x-theme::course.details.video-play :course="$course" />
                    </div>
                </div>
                <!-- COURSE DETAILS TAB -->
                <div class="mt-6">
                    <div class="dashkit-tab bg-primary-50 flex items-center justify-center flex-wrap gap-3 p-3 rounded-lg"
                        id="courseDetailsTab">
                        <button type="button" aria-label="Course overview tab"
                            class="dashkit-tab-btn btn b-outline btn-primary-outline rounded-full [&.active]:bg-primary [&.active]:text-white [&.active]:border-transparent shrink-0 active"
                            id="courseOverview">{{ translate('Course Overview') }}</button>
                        <button type="button" aria-label="Course assignment tab"
                            class="dashkit-tab-btn btn b-outline btn-primary-outline rounded-full [&.active]:bg-primary [&.active]:text-white [&.active]:border-transparent shrink-0"
                            id="courseAssignment">{{ translate('Assignments') }}
                            <span>({{ count($assignments) ?? 0 }})</span></button>
                        <button type="button" aria-label="Course review tab"
                            class="dashkit-tab-btn btn b-outline btn-primary-outline rounded-full [&.active]:bg-primary [&.active]:text-white [&.active]:border-transparent shrink-0"
                            id="courseReview">{{ translate('Reviews') }}</button>
                       {{-- <button type="button" aria-label="Course progress tab"
                            class="dashkit-tab-btn btn b-outline btn-primary-outline rounded-full [&.active]:bg-primary [&.active]:text-white [&.active]:border-transparent shrink-0"
                            id="courseProgress">{{ translate('Progression') }}
                        </button>--}}
                    </div>
                    <div class="dashkit-tab-content mt-7 *:hidden container max-w-screen-lg"
                        id="courseDetailsTabContent">
                        <!-- COURSE OVERVIEW CONTENT -->
                        <div class="dashkit-tab-pane course-details-tab-content [&>:not(:first-child)]:mt-10 [&>:not(:first-child)]:pt-10 [&>:not(:first-child)]:border-t [&>:not(:first-child)]:border-border !block"
                            data-tab="courseOverview">
                            <x-theme::course.short-info :course="$course" />
                            <x-theme::course.details.course-overview :course="$course" />
                            <x-theme::course.details.course-instructor :course="$course" />
                        </div>
                        <!-- COURSE ASSIGNMENT CONTENT -->
                        <div class="dashkit-tab-pane course-details-tab-content [&>:not(:first-child)]:mt-10 [&>:not(:first-child)]:pt-10 [&>:not(:first-child)]:border-t [&>:not(:first-child)]:border-border"
                            data-tab="courseAssignment">
                            @if (count($assignments) > 0)
                                <x-theme::course.assignment :assignments="$assignments" />
                            @else
                                <x-theme::cards.empty
                                    title="No assignment"
                                    description="This course does not include any mandatory assignments, allowing you to focus on learning at your own pace without the pressure of submissions."
                                />
                            @endif
                        </div>
                        <!-- COURSE REVIEWS CONTENT -->
                        <div class="dashkit-tab-pane course-details-tab-content [&>:not(:first-child)]:mt-10 [&>:not(:first-child)]:pt-10 [&>:not(:first-child)]:border-t [&>:not(:first-child)]:border-border "
                            data-tab="courseReview">
                            <x-theme::course.details.course-review :course="$course" />

                            <x-theme::course.details.course-comment :course="$course" :auth="$auth ?? false"
                                :purchaseCheck="$purchaseCheck ?? false" />
                        </div>

                        <!-- COURSE PROGRESS CONTENT -->
                        <div class="dashkit-tab-pane course-details-tab-content [&>:not(:first-child)]:mt-10 [&>:not(:first-child)]:pt-10 [&>:not(:first-child)]:border-t [&>:not(:first-child)]:border-border"
                            data-tab="courseProgress">
                            @if(auth()->check() && auth()->user()->guard === 'student' && $course->chapters && $course->chapters->count() > 0)
                                <!-- PROGRESSION DES CHAPITRES -->
                                <div class="bg-white rounded-lg p-6">
                                    <h3 class="text-xl font-semibold text-heading mb-6">
                                        {{ translate('Progression des Chapitres') }}
                                    </h3>
                                    <div class="space-y-4">
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
                                            <div class="border border-gray-200 rounded-lg p-4 chapter-item" data-chapter-id="{{ $chapter->id }}">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center gap-3">
                                                        <div>
                                                            <h4 class="font-medium text-heading">{{ $chapter->title }}</h4>
                                                            <p class="text-sm text-gray-600">{{ $chapter->topics->count() }} {{ translate('leçons') }}</p>
                                                        </div>

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
                                                    </div>

                                                    <!-- Boutons de progression -->
                                                    <div class="flex gap-2">
                                                        @if(!$chapterProgress || $chapterProgress->status === 'not_started')
                                                            <button type="button"
                                                                    class="chapter-start-btn btn btn-primary bg-primary-600 text-white"
                                                                    style="border: none; padding: 8px 16px; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; transition: background-color 0.2s;"
                                                                    onmouseover="this.style.backgroundColor='#1d4ed8'"
                                                                    onmouseout="this.style.backgroundColor='#2563eb'"
                                                                    data-chapter-id="{{ $chapter->id }}">
                                                                <i class="ri-play-line mr-2"></i>
                                                                {{ translate('Commencer') }}
                                                            </button>
                                                        @elseif($chapterProgress->status === 'in_progress')
                                                            <button type="button"
                                                                    class="chapter-complete-btn btn btn-success bg-success text-white"
                                                                    style="border: none; padding: 8px 16px; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; transition: background-color 0.2s;"
                                                                    onmouseover="this.style.backgroundColor='#15803d'"
                                                                    onmouseout="this.style.backgroundColor='#16a34a'"
                                                                    data-chapter-id="{{ $chapter->id }}">
                                                                <i class="ri-check-line mr-2"></i>
                                                                {{ translate('Marquer comme terminé') }}
                                                            </button>
                                                        @else
                                                            <span class="badge badge-success"
                                                                  style="background-color: #dcfce7; color: #166534; padding: 8px 16px; border-radius: 6px; font-size: 14px; font-weight: 500; display: inline-flex; align-items: center;">
                                                                <i class="ri-check-circle-line mr-2"></i>
                                                                {{ translate('Terminé') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- Informations de progression -->
                                                @if($chapterProgress)
                                                    <div class="mt-3 text-sm text-gray-600">
                                                        @if($chapterProgress->status === 'completed')
                                                            <span class="text-green-600">
                                                                <i class="ri-check-circle-line mr-1"></i>
                                                                {{ translate('Chapitre terminé le') }} {{ $chapterProgress->completed_at->format('d/m/Y à H:i') }}
                                                            </span>
                                                        @elseif($chapterProgress->status === 'in_progress')
                                                            <span class="text-yellow-600">
                                                                <i class="ri-play-circle-line mr-1"></i>
                                                                {{ translate('En cours depuis le') }} {{ $chapterProgress->started_at->format('d/m/Y à H:i') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endif

                                                <!-- PROGRESSION DES LEÇONS DU CHAPITRE -->
                                                @if(isset($chapter->topics) && $chapter->topics->count() > 0)
                                                    <div class="mt-4 border-t border-gray-200 pt-4">
                                                        <h5 class="text-lg font-semibold text-gray-800 mb-4 flex items-center border-b border-gray-200 pb-2">
                                                            <i class="ri-book-open-line mr-3 text-blue-600"></i>
                                                            {{ translate('Leçons du chapitre') }}
                                                            <span class="ml-2 inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                {{ $chapter->topics->count() }} {{ translate('leçons') }}
                                                            </span>
                                                        </h5>

                                                        <div class="space-y-3">
                                                            @foreach($chapter->topics->sortBy('order') as $topic)
                                                                @php
                                                                    $topicProgress = \Modules\LMS\Models\TopicProgress::where('user_id', auth()->id())
                                                                        ->where('topic_id', $topic->id)
                                                                        ->first();
                                                                    $canAccess = \Modules\LMS\Models\TopicProgress::canAccessTopic(auth()->id(), $topic->id);
                                                                @endphp
                                                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200 topic-item" data-topic-id="{{ $topic->id }}">
                                                                    <div class="flex-1">
                                                                        <div class="flex items-start gap-4">
                                                                            <div class="flex-shrink-0 mt-1">
                                                                                @if($topic->topic_type?->slug === 'video')
                                                                                    <i class="ri-video-line text-blue-500 text-lg"></i>
                                                                                @elseif($topic->topic_type?->slug === 'reading')
                                                                                    <i class="ri-book-line text-green-500 text-lg"></i>
                                                                                @elseif($topic->topic_type?->slug === 'quiz')
                                                                                    <i class="ri-questionnaire-line text-purple-500 text-lg"></i>
                                                                                @elseif($topic->topic_type?->slug === 'assignment')
                                                                                    <i class="ri-file-text-line text-orange-500 text-lg"></i>
                                                                                @else
                                                                                    <i class="ri-file-line text-gray-500 text-lg"></i>
                                                                                @endif
                                                                            </div>
                                                                            <div class="flex-1">
                                                                                <h6 class="text-base font-semibold text-gray-900 mb-1">{{ $topic->topicable->title ?? 'Titre non disponible' }}</h6>
                                                                                <div class="flex items-center gap-3 text-sm text-gray-600">
                                                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 capitalize">
                                                                                        <i class="ri-file-line mr-1"></i>
                                                                                        {{ $topic->topicable_type ? class_basename($topic->topicable_type) : 'N/A' }}
                                                                                    </span>
                                                                                    @if($topic->topicable && $topic->topicable->duration)
                                                                                        <span class="inline-flex items-center text-xs text-gray-500">
                                                                                            <i class="ri-time-line mr-1"></i>
                                                                                            {{ $topic->topicable->duration }}{{ translate('min') }}
                                                                                        </span>
                                                                                    @endif
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="flex items-center gap-2 ml-4">
                                                                        <!-- Indicateur de statut -->
                                                                        <div class="flex-shrink-0">
                                                                            @if(!$canAccess)
                                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" style="background-color: #f3f4f6; color: #6b7280;">
                                                                                    <i class="ri-lock-line mr-1"></i>
                                                                                    {{ translate('Verrouillé') }}
                                                                                </span>
                                                                            @elseif(!$topicProgress || $topicProgress->status === 'not_started')
                                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" style="background-color: #e5e7eb; color: #374151;">
                                                                                    <i class="ri-time-line mr-1"></i>
                                                                                    {{ translate('Non commencé') }}
                                                                                </span>
                                                                            @elseif($topicProgress->status === 'in_progress')
                                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" style="background-color: #fef3c7; color: #92400e;">
                                                                                    <i class="ri-play-line mr-1"></i>
                                                                                    {{ translate('En cours') }}
                                                                                </span>
                                                                            @else
                                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" style="background-color: #dcfce7; color: #166534;">
                                                                                    <i class="ri-check-circle-line mr-1"></i>
                                                                                    {{ translate('Terminé') }}
                                                                                </span>
                                                                            @endif
                                                                        </div>

                                                                        <!-- Boutons d'action -->
                                                                        <div class="flex items-center gap-3">
                                                                            @if(!$canAccess)
                                                                                <div class="text-center">
                                                                                    <span class="text-xs text-gray-500 block">{{ translate('Terminez les leçons précédentes') }}</span>
                                                                                </div>
                                                                            @elseif(!$topicProgress || $topicProgress->status === 'not_started')
                                                                                <button type="button"
                                                                                        class="topic-start-btn btn btn-primary bg-primary-600 text-white"
                                                                                        style="border: none; padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);"
                                                                                        onmouseover="this.style.backgroundColor='#1d4ed8'; this.style.transform='translateY(-1px)'"
                                                                                        onmouseout="this.style.backgroundColor='#2563eb'; this.style.transform='translateY(0)'"
                                                                                        data-topic-id="{{ $topic->id }}">
                                                                                    <i class="ri-play-line mr-2"></i>
                                                                                    {{ translate('Commencer') }}
                                                                                </button>
                                                                            @elseif($topicProgress->status === 'in_progress')
                                                                                <button type="button"
                                                                                        class="topic-complete-btn btn btn-success bg-success text-white"
                                                                                        style="border: none; padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all 0.2s; box-shadow: 0 2px 4px rgba(22, 163, 74, 0.2);"
                                                                                        onmouseover="this.style.backgroundColor='#15803d'; this.style.transform='translateY(-1px)'"
                                                                                        onmouseout="this.style.backgroundColor='#16a34a'; this.style.transform='translateY(0)'"
                                                                                        data-topic-id="{{ $topic->id }}">
                                                                                    <i class="ri-check-line mr-2"></i>
                                                                                    {{ translate('Terminer') }}
                                                                                </button>
                                                                            @else
                                                                                <div class="text-center">
                                                                                    <span class="inline-flex items-center px-3 py-2 rounded-lg text-sm font-semibold text-green-700 bg-green-100">
                                                                                        <i class="ri-check-circle-line mr-2"></i>
                                                                                        {{ translate('Terminé') }}
                                                                                    </span>
                                                                                </div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="bg-white rounded-lg p-6 text-center">
                                    <p class="text-gray-600">{{ translate('Aucun chapitre disponible pour ce cours.') }}</p>
                                </div>
                            @endif
                        </div>

                    </div>
                </div>
            </div>
            <!-- COURSE CONTENT AREA -->
            <x-theme::course.topic-sidebar :course="$course" :data="$data" :auth="$auth ?? false" :purchaseCheck="$purchaseCheck ?? false" />
        </div>
    </main>

    <!-- END FOOTER AREA -->
    @include('theme::layouts.partials.footer-script')
    <script src="{{ asset('lms/frontend/assets/vendor/js/plyr.min.js') }}"></script>
    <script src="{{ edulab_asset('lms/frontend/assets/js/video-play.js') }}"></script>

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

            // Gérer le clic sur le bouton "Commencer" des leçons
            if (e.target.classList.contains('topic-start-btn') || e.target.closest('.topic-start-btn')) {
                const button = e.target.classList.contains('topic-start-btn') ? e.target : e.target.closest('.topic-start-btn');
                const topicId = button.getAttribute('data-topic-id');
                markTopicAsStarted(topicId, button);
            }

            // Gérer le clic sur le bouton "Terminer" des leçons
            if (e.target.classList.contains('topic-complete-btn') || e.target.closest('.topic-complete-btn')) {
                const button = e.target.classList.contains('topic-complete-btn') ? e.target : e.target.closest('.topic-complete-btn');
                const topicId = button.getAttribute('data-topic-id');
                markTopicAsCompleted(topicId, button);
            }
        });

        function markChapterAsStarted(chapterId, button) {
            button.disabled = true;
            button.innerHTML = '<i class="ri-loader-4-line mr-2 animate-spin"></i> {{ translate("En cours...") }}';

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
                    button.innerHTML = '<i class="ri-play-line mr-2"></i> {{ translate("Commencer") }}';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('{{ translate("Erreur de connexion") }}', 'error');
                button.disabled = false;
                button.innerHTML = '<i class="ri-play-line mr-2"></i> {{ translate("Commencer") }}';
            });
        }

        function markChapterAsCompleted(chapterId, button) {
            button.disabled = true;
            button.innerHTML = '<i class="ri-loader-4-line mr-2 animate-spin"></i> {{ translate("En cours...") }}';

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
                    button.innerHTML = '<i class="ri-check-line mr-2"></i> {{ translate("Marquer comme terminé") }}';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('{{ translate("Erreur de connexion") }}', 'error');
                button.disabled = false;
                button.innerHTML = '<i class="ri-check-line mr-2"></i> {{ translate("Marquer comme terminé") }}';
            });
        }

        function updateChapterProgress(chapterId, status, progress) {
            const chapterItem = document.querySelector(`[data-chapter-id="${chapterId}"]`);
            if (!chapterItem) return;

            const progressIndicator = chapterItem.querySelector('.chapter-progress-indicator');
            const buttonContainer = chapterItem.querySelector('.flex.gap-2');
            const progressInfo = chapterItem.querySelector('.mt-3.text-sm.text-gray-600');

            if (status === 'in_progress') {
                // Mettre à jour l'indicateur
                progressIndicator.innerHTML = `
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" style="background-color: #fef3c7; color: #92400e;">
                        <i class="ri-play-line mr-1"></i>
                        {{ translate('En cours') }}
                    </span>
                `;

                // Changer le bouton avec les styles inline
                buttonContainer.innerHTML = `
                    <button type="button"
                            class="chapter-complete-btn btn btn-success"
                            style="background-color: #16a34a; color: white; border: none; padding: 8px 16px; border-radius: 6px; font-size: 14px; font-weight: 500; cursor: pointer; transition: background-color 0.2s;"
                            onmouseover="this.style.backgroundColor='#15803d'"
                            onmouseout="this.style.backgroundColor='#16a34a'"
                            data-chapter-id="${chapterId}">
                        <i class="ri-check-line mr-2"></i>
                        {{ translate('Marquer comme terminé') }}
                    </button>
                `;

                // Ajouter les informations de progression
                const startDate = new Date().toLocaleDateString('fr-FR') + ' à ' + new Date().toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit'});
                if (progressInfo) {
                    progressInfo.innerHTML = `
                        <span style="color: #d97706;">
                            <i class="ri-play-circle-line mr-1"></i>
                            {{ translate('En cours depuis le') }} ${startDate}
                        </span>
                    `;
                }
            } else if (status === 'completed') {
                // Mettre à jour l'indicateur
                progressIndicator.innerHTML = `
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium" style="background-color: #dcfce7; color: #166534;">
                        <i class="ri-check-line mr-1"></i>
                        {{ translate('Terminé') }}
                    </span>
                `;

                // Changer le bouton avec les styles inline
                buttonContainer.innerHTML = `
                    <span class="badge badge-success" style="background-color: #dcfce7; color: #166534; padding: 8px 16px; border-radius: 6px; font-size: 14px; font-weight: 500; display: inline-flex; align-items: center;">
                        <i class="ri-check-circle-line mr-2"></i>
                        {{ translate('Terminé') }}
                    </span>
                `;

                // Ajouter les informations de progression
                const completedDate = new Date().toLocaleDateString('fr-FR') + ' à ' + new Date().toLocaleTimeString('fr-FR', {hour: '2-digit', minute: '2-digit'});
                if (progressInfo) {
                    progressInfo.innerHTML = `
                        <span style="color: #16a34a;">
                            <i class="ri-check-circle-line mr-1"></i>
                            {{ translate('Chapitre terminé le') }} ${completedDate}
                        </span>
                    `;
                }
            }
        }

        function markTopicAsStarted(topicId, button) {
            button.disabled = true;
            button.innerHTML = '<i class="ri-loader-4-line mr-1 animate-spin"></i> {{ translate("En cours...") }}';

            fetch(`{{ route('student.topic.start', '') }}/${topicId}`, {
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
                    updateTopicProgress(topicId, 'in_progress', data.progress);
                    showNotification('{{ translate("Leçon marquée comme commencée!") }}', 'success');
                } else {
                    showNotification(data.message || '{{ translate("Erreur lors de la mise à jour") }}', 'error');
                    button.disabled = false;
                    button.innerHTML = '<i class="ri-play-line text-xs"></i>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('{{ translate("Erreur de connexion") }}', 'error');
                button.disabled = false;
                    button.innerHTML = '<i class="ri-play-line text-xs"></i>';
            });
        }

        function markTopicAsCompleted(topicId, button) {
            button.disabled = true;
            button.innerHTML = '<i class="ri-loader-4-line mr-1 animate-spin"></i> {{ translate("En cours...") }}';

            fetch(`{{ route('student.topic.complete', '') }}/${topicId}`, {
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
                    updateTopicProgress(topicId, 'completed', data.progress);
                    showNotification('{{ translate("Leçon marquée comme terminée!") }}', 'success');

                    // Afficher si le chapitre est terminé
                    if (data.chapter_completed) {
                        showNotification('{{ translate("Chapitre terminé! Vous pouvez maintenant passer au suivant.") }}', 'info');
                    }
                } else {
                    showNotification(data.message || '{{ translate("Erreur lors de la mise à jour") }}', 'error');
                    button.disabled = false;
                    button.innerHTML = '<i class="ri-check-line text-xs"></i>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('{{ translate("Erreur de connexion") }}', 'error');
                button.disabled = false;
                    button.innerHTML = '<i class="ri-check-line text-xs"></i>';
            });
        }

        function updateTopicProgress(topicId, status, progress) {
            const topicItem = document.querySelector(`[data-topic-id="${topicId}"]`);
            if (!topicItem) return;

            const buttonContainer = topicItem.querySelector('.flex.items-center.gap-1.ml-2');

            if (status === 'in_progress') {
                // Changer le bouton
                buttonContainer.innerHTML = `
                    <button type="button"
                            class="topic-complete-btn inline-flex items-center justify-center w-6 h-6 rounded-full text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 hover:scale-110"
                            data-topic-id="${topicId}"
                            title="{{ translate('Marquer la leçon comme terminée') }}">
                        <i class="ri-check-line text-xs"></i>
                    </button>
                `;
            } else if (status === 'completed') {
                // Changer le bouton
                buttonContainer.innerHTML = `
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-green-800 bg-green-100"
                          title="{{ translate('Leçon terminée') }}">
                        <i class="ri-check-circle-line text-xs"></i>
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

    <!-- MODALS POUR LA PROGRESSION -->
    @if(auth()->check() && auth()->user()->guard === 'student')
    <!-- Modal de leçon terminée -->
    <div id="lesson-complete-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; justify-content: center; align-items: center;">
        <div style="background: white; border-radius: 12px; padding: 30px; max-width: 400px; text-align: center; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
            <div style="width: 60px; height: 60px; background: #10B981; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                <i class="ri-check-line" style="color: white; font-size: 32px;"></i>
            </div>
            <h3 id="lesson-modal-title" style="font-size: 22px; font-weight: 600; color: #1F2937; margin-bottom: 12px;">Leçon terminée !</h3>
            <p id="lesson-modal-message" style="font-size: 14px; color: #6B7280; margin-bottom: 25px;">Votre progression a été enregistrée.</p>
            <button id="lesson-modal-close" style="background: #3B82F6; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer;" onmouseover="this.style.background='#2563EB'" onmouseout="this.style.background='#3B82F6'">
                Continuer
            </button>
        </div>
    </div>

    <!-- Modal de cours terminé avec certificat -->
    <div id="course-complete-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 10000; justify-content: center; align-items: center;">
        <div style="background: white; border-radius: 12px; padding: 40px; max-width: 500px; text-align: center; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
            <div style="width: 80px; height: 80px; background: linear-gradient(135deg, #10B981 0%, #059669 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 25px;">
                <i class="ri-trophy-line" style="color: white; font-size: 40px;"></i>
            </div>
            <h3 style="font-size: 26px; font-weight: 700; color: #1F2937; margin-bottom: 15px;">🎉 Félicitations !</h3>
            <p id="course-complete-message" style="font-size: 16px; color: #374151; margin-bottom: 30px; line-height: 1.6;">Vous avez terminé ce cours avec succès !</p>
            <div id="course-complete-certificate" style="display: none; margin-bottom: 25px;">
                <a href="{{ route('student.certificate.index') }}" style="display: inline-block; background: linear-gradient(135deg, #10B981 0%, #059669 100%); color: white; padding: 14px 28px; border-radius: 8px; font-size: 15px; font-weight: 600; text-decoration: none; transition: transform 0.2s;" onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
                    <i class="ri-award-line"></i> Voir mon certificat
                </a>
            </div>
            <button id="course-complete-close" style="background: #E5E7EB; color: #374151; border: none; padding: 12px 24px; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer;" onmouseover="this.style.background='#D1D5DB'" onmouseout="this.style.background='#E5E7EB'">
                Fermer
            </button>
        </div>
    </div>

    <script>
    // Fonction pour afficher le modal de leçon terminée
    window.showLessonCompleteModal = function(data) {
        console.log('📖 [PARENT] showLessonCompleteModal appelée:', data);

        const modal = document.getElementById('lesson-complete-modal');
        const title = document.getElementById('lesson-modal-title');
        const message = document.getElementById('lesson-modal-message');
        const closeBtn = document.getElementById('lesson-modal-close');

        if (!modal) {
            console.error('❌ Modal lesson-complete-modal introuvable');
            return;
        }

        // Mettre à jour le contenu
        if (data.chapter_completed) {
            title.textContent = '📖 Chapitre terminé !';
            message.textContent = 'Félicitations ! Vous avez terminé ce chapitre.';
        } else {
            title.textContent = '✅ Leçon terminée !';
            message.textContent = 'Votre progression a été enregistrée.';
        }

        // Afficher le modal
        modal.style.display = 'flex';
        console.log('✅ [PARENT] Modal affiché');

        // Gestionnaire de fermeture
        closeBtn.onclick = function() {
            modal.style.display = 'none';
            // Recharger la page pour mettre à jour la sidebar
            window.location.reload();
        };
    };

    // Fonction pour afficher le modal de cours terminé
    window.showCourseCompleteModal = function(certificateGenerated) {
        console.log('🎓 [PARENT] showCourseCompleteModal appelée:', certificateGenerated);

        const modal = document.getElementById('course-complete-modal');
        const message = document.getElementById('course-complete-message');
        const certificateBtn = document.getElementById('course-complete-certificate');
        const closeBtn = document.getElementById('course-complete-close');

        if (!modal) {
            console.error('❌ Modal course-complete-modal introuvable');
            return;
        }

        // Afficher ou masquer le bouton certificat
        if (certificateGenerated) {
            message.textContent = 'Vous avez terminé ce cours avec succès et obtenu votre certificat !';
            certificateBtn.style.display = 'block';
        } else {
            message.textContent = 'Vous avez terminé ce cours avec succès !';
            certificateBtn.style.display = 'none';
        }

        // Afficher le modal
        modal.style.display = 'flex';
        console.log('✅ [PARENT] Modal cours terminé affiché');

        // Gestionnaire de fermeture
        closeBtn.onclick = function() {
            modal.style.display = 'none';
            window.location.reload();
        };
    };

    console.log('✅ [PARENT] Fonctions de modal définies:', {
        showLessonCompleteModal: typeof window.showLessonCompleteModal,
        showCourseCompleteModal: typeof window.showCourseCompleteModal
    });
    </script>
    @endif
</body>

</html>
