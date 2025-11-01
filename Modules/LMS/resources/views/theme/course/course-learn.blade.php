{{--
@php
    $data = $topic['data'];
@endphp

@if ($type == 'video')
    @if ($data->video_src_type == 'youtube' || $data->video_src_type == 'vimeo')
        <div class="plyr__video-embed" id="player">
            <iframe src="{{ $data->video_url }}" allowfullscreen allowtransparency allow="autoplay"></iframe>
        </div>
        <script>
            console.log('🔧 [THEME-COURSE-LEARN] Script YouTube/Vimeo chargé');

            var videoPlayer = new Plyr("#player", {
                settings: ["speed"],
                seekTime: 0,
                ratio: "16:7",
                speed: {
                    selected: 1,
                    options: [0.5, 0.75, 1, 1.25, 1.5]
                },
            });

            console.log('🔧 [THEME-COURSE-LEARN] Plyr player initialisé:', videoPlayer);

            @if(auth()->check() && auth()->user()->guard === 'student')
            var isVideoStarted = false;
            var isVideoCompleted = false;

            // Fonction pour obtenir le topic ID
            function getTopicId() {
                console.log('🔍 [THEME-LEARN] Recherche du topic ID...');

                // Méthode 1: Chercher dans les données passées par le backend
                @if(isset($topic['topicId']))
                    console.log('✅ [THEME-LEARN] Topic ID trouvé depuis backend:', {{ $topic['topicId'] }});
                    return {{ $topic['topicId'] }};
                @endif

                // Méthode 2: Chercher dans l'URL parent (window.parent pour iframe)
                try {
                    const parentUrl = new URLSearchParams(window.parent.location.search);
                    const topicId = parentUrl.get('topic_id');
                    if (topicId) {
                        console.log('✅ [THEME-LEARN] Topic ID trouvé dans parent URL:', topicId);
                        return topicId;
                    }
                } catch(e) {
                    console.log('⚠️ [THEME-LEARN] Impossible d\'accéder à parent URL');
                }

                // Méthode 3: Chercher dans les attributs data du parent
                try {
                    const topicElement = window.parent.document.querySelector('[data-topic-id].active') ||
                                       window.parent.document.querySelector('[data-topic-id]');
                    if (topicElement) {
                        const id = topicElement.getAttribute('data-topic-id');
                        console.log('✅ [THEME-LEARN] Topic ID trouvé dans parent DOM:', id);
                        return id;
                    }
                } catch(e) {
                    console.log('⚠️ [THEME-LEARN] Impossible d\'accéder au parent DOM');
                }

                console.error('❌ [THEME-LEARN] Aucun topic ID trouvé!');
                return null;
            }

            // Détecter le clic sur play
            videoPlayer.on('play', function() {
                console.log('▶️ [THEME-LEARN] Event PLAY déclenché!');
                console.log('▶️ [THEME-LEARN] isVideoStarted:', isVideoStarted);
                if (!isVideoStarted) {
                    isVideoStarted = true;
                    console.log('▶️ [THEME-LEARN] Marquer comme commencé');

                    const topicId = getTopicId();
                    if (topicId) {
                        console.log('🚀 [THEME-LEARN] Envoi de la progression start pour topic:', topicId);
                        fetch(`{{ route('student.topic.start', '') }}/${topicId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': window.parent.document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log('✅ [THEME-LEARN] Réponse start:', data);
                        })
                        .catch(error => {
                            console.error('❌ [THEME-LEARN] Erreur start:', error);
                        });
                    } else {
                        console.error('❌ [THEME-LEARN] Impossible de marquer comme commencé: pas de topic ID');
                    }
                }
            });

            // Détecter la fin
            videoPlayer.on('ended', function() {
                console.log('🎬 [THEME-LEARN] Event ENDED déclenché!');
                if (!isVideoCompleted) {
                    isVideoCompleted = true;
                    console.log('🎬 [THEME-LEARN] Vidéo terminée');

                    const topicId = getTopicId();
                    if (topicId) {
                        console.log('🏁 [THEME-LEARN] Envoi de la progression complete pour topic:', topicId);
                        fetch(`{{ route('student.topic.complete', '') }}/${topicId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': window.parent.document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log('✅ [THEME-LEARN] Réponse complete:', data);

                            // Afficher un modal si le chapitre ou le cours est terminé
                            if (data.certificate_generated) {
                                console.log('🎓 [THEME-LEARN] Certificat généré!');

                                // Afficher le modal dans le parent
                                try {
                                    if (window.parent && typeof window.parent.showCourseCompleteModal === 'function') {
                                        window.parent.showCourseCompleteModal(true);
                                    } else {
                                        alert('🎓 Félicitations ! Vous avez obtenu votre certificat !');
                                    }
                                } catch(e) {
                                    console.error('Erreur lors de l\'affichage du modal:', e);
                                    alert('🎓 Félicitations ! Vous avez obtenu votre certificat !');
                                }
                            } else if (data.chapter_completed) {
                                console.log('📖 [THEME-LEARN] Chapitre terminé!');

                                // Afficher un message pour le chapitre terminé
                                try {
                                    if (window.parent && typeof window.parent.showLessonCompleteModal === 'function') {
                                        window.parent.showLessonCompleteModal({
                                            chapter_completed: true,
                                            is_last_topic_in_chapter: true
                                        });
                                    } else {
                                        alert('📖 Félicitations ! Chapitre terminé !');
                                    }
                                } catch(e) {
                                    console.error('Erreur lors de l\'affichage du modal:', e);
                                    alert('📖 Félicitations ! Chapitre terminé !');
                                }
                            } else {
                                console.log('✅ [THEME-LEARN] Leçon terminée!');

                                // Afficher un simple message pour la leçon terminée
                                try {
                                    if (window.parent && typeof window.parent.showLessonCompleteModal === 'function') {
                                        window.parent.showLessonCompleteModal({
                                            chapter_completed: false,
                                            is_last_topic_in_chapter: false
                                        });
                                    }
                                } catch(e) {
                                    console.log('Leçon terminée (pas de modal)');
                                }
                            }
                        })
                        .catch(error => {
                            console.error('❌ [THEME-LEARN] Erreur complete:', error);
                        });
                    } else {
                        console.error('❌ [THEME-LEARN] Impossible de marquer comme terminé: pas de topic ID');
                    }
                }
            });

            console.log('✅ [THEME-LEARN] Listeners installés');
            @endif
        </script>
    @elseif($data->video_src_type == 'local')
        @if (
            $data->system_video &&
                fileExists('lms/courses/topics/videos/', $data->system_video) == true &&
                $data->system_video != '')
            <video id="main-course-video" playsinline controls data-poster="assets/images/course/course-2.png">
                <source src="{{ asset('storage/lms/courses/topics/videos/' . $data->system_video) }}" type="video/mp4" />
            </video>
        @endif
        <script>
            var videoPlayer = new Plyr("#player", {
                settings: ["speed"],
                seekTime: 0,
                ratio: "16:7",
                speed: {
                    selected: 1,
                    options: [0.5, 0.75, 1, 1.25, 1.5]
                },
            });
        </script>
    @endif
@elseif($type == 'reading')
    <div class="p-5 md:p-8 xl:p-10 relative overflow-hidden aspect-[16/7] bg-primary-50 rounded-xl">
        <div class="size-full overflow-x-hidden overflow-y-auto">
            <h5 class="area-title text-xl">{{ $data->title }}</h5>
            <div class="area-description mt-5">
                {!! clean($data->description) !!}
            </div>
        </div>

                    <!-- Bouton Marquer comme terminé pour les cours reading -->
                    @if(auth()->check() && auth()->user()->guard === 'student')
                        @php
                            // Vérifier si la leçon est déjà terminée
                            $topicProgress = null;
                            if (auth()->check()) {
                                $topic = \Modules\LMS\Models\Courses\Topic::where('topicable_id', $data->id)
                                    ->where('topicable_type', 'Modules\\LMS\\Models\\Courses\\Topics\\Reading')
                                    ->first();

                                if ($topic) {
                                    $topicProgress = \Modules\LMS\Models\TopicProgress::where('user_id', auth()->id())
                                        ->where('topic_id', $topic->id)
                                        ->first();
                                }
                            }
                            $isCompleted = $topicProgress && $topicProgress->isCompleted();
                        @endphp

                        <div class="absolute bottom-2 right-2 sm:bottom-4 sm:right-4">
                            @if($isCompleted)
                                <!-- Icône de validation si terminée -->
                                <div class="flex items-center gap-1 sm:gap-2 text-green-600 bg-green-100 px-2 py-1 sm:px-3 sm:py-2 rounded-md text-xs sm:text-sm">
                                    <i class="ri-check-double-line text-xs sm:text-sm"></i>
                                    <span class="hidden xs:inline">{{ translate('Terminée') }}</span>
                                    <span class="xs:hidden">{{ translate('✓') }}</span>
                                </div>
                            @else
                                <!-- Bouton pour marquer comme terminé -->
                                <button id="mark-reading-complete"
                                        class="btn b-solid btn-success-solid text-xs sm:text-sm px-2 py-1 sm:px-3 sm:py-2 flex items-center gap-1 sm:gap-2 whitespace-nowrap"
                                        data-topic-id="{{ $topic['id'] }}"
                                        data-topic-type="reading">
                                    <i class="ri-check-line text-xs sm:text-sm"></i>
                                    <span class="hidden xs:inline">{{ translate('Marquer comme terminé') }}</span>
                                    <span class="xs:hidden">{{ translate('Marquer') }}</span>
                                </button>
                            @endif
                        </div>
                    @endif
    </div>

        <!-- Modal de confirmation pour les cours reading -->
        @if(auth()->check() && auth()->user()->guard === 'student')
            <div id="reading-complete-modal" style="
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 9999;
                display: none;
                justify-content: center;
                align-items: center;
                padding: 20px;
                box-sizing: border-box;
            ">
                <div style="
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
                    max-width: 400px;
                    width: 100%;
                    padding: 30px;
                    text-align: center;
                    position: relative;
                ">
                    <div style="
                        width: 60px;
                        height: 60px;
                        background-color: #10B981;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin: 0 auto 20px;
                    ">
                        <i class="ri-check-line" style="color: white; font-size: 24px;"></i>
                    </div>

                    <h3 id="modal-title" style="
                        font-size: 20px;
                        font-weight: 600;
                        color: #1F2937;
                        margin: 0 0 10px 0;
                    ">
                        {{ translate('Leçon terminée !') }}
                    </h3>

                    <p id="modal-message" style="
                        font-size: 14px;
                        color: #6B7280;
                        margin: 0 0 25px 0;
                        line-height: 1.5;
                    ">
                        {{ translate('Vous avez terminé cette leçon. Votre progression a été enregistrée.') }}
                    </p>

                    <button id="modal-close" style="
                        background-color: #3B82F6;
                        color: white;
                        border: none;
                        padding: 10px 20px;
                        border-radius: 6px;
                        font-size: 14px;
                        font-weight: 500;
                        cursor: pointer;
                        transition: background-color 0.2s;
                    " onmouseover="this.style.backgroundColor='#2563EB'" onmouseout="this.style.backgroundColor='#3B82F6'">
                        {{ translate('Continuer') }}
                    </button>
                </div>
            </div>

            <!-- Modal de completion du cours -->
            <div id="course-complete-modal" style="
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 10000;
                display: none;
                justify-content: center;
                align-items: center;
                padding: 20px;
                box-sizing: border-box;
            ">
                <div style="
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
                    max-width: 500px;
                    width: 100%;
                    padding: 40px;
                    text-align: center;
                    position: relative;
                ">
                    <div style="
                        width: 80px;
                        height: 80px;
                        background-color: #10B981;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin: 0 auto 25px;
                    ">
                        <i class="ri-trophy-line" style="color: white; font-size: 32px;"></i>
                    </div>

                    <h3 id="course-complete-title" style="
                        font-size: 24px;
                        font-weight: 700;
                        color: #1F2937;
                        margin: 0 0 15px 0;
                    ">
                        {{ translate('Félicitations !') }}
                    </h3>

                    <p id="course-complete-message" style="
                        font-size: 16px;
                        color: #6B7280;
                        margin: 0 0 30px 0;
                        line-height: 1.6;
                    ">
                        {{ translate('Vous avez terminé ce cours avec succès ! Votre certificat a été généré automatiquement.') }}
                    </p>

                    <div style="
                        display: flex;
                        gap: 15px;
                        justify-content: center;
                        flex-wrap: wrap;
                    ">
                        <button id="course-complete-close" style="
                            background-color: #3B82F6;
                            color: white;
                            border: none;
                            padding: 12px 24px;
                            border-radius: 6px;
                            font-size: 14px;
                            font-weight: 500;
                            cursor: pointer;
                            transition: background-color 0.2s;
                        " onmouseover="this.style.backgroundColor='#2563EB'" onmouseout="this.style.backgroundColor='#3B82F6'">
                            {{ translate('Continuer') }}
                        </button>

                        <button id="course-complete-certificate" style="
                            background-color: #10B981;
                            color: white;
                            border: none;
                            padding: 12px 24px;
                            border-radius: 6px;
                            font-size: 14px;
                            font-weight: 500;
                            cursor: pointer;
                            transition: background-color 0.2s;
                        " onmouseover="this.style.backgroundColor='#059669'" onmouseout="this.style.backgroundColor='#10B981'">
                            {{ translate('Voir le certificat') }}
                        </button>
                    </div>
                </div>
            </div>

        <script>
        // Gestion d'erreur globale pour éviter les erreurs Flasher
        window.addEventListener('error', function(e) {
            if (e.message && e.message.includes('Flasher is not loaded')) {
                console.warn('Flasher error caught and ignored:', e.message);
                e.preventDefault();
                return false;
            }
        });

        // Fonction pour afficher le modal de completion du cours (scope global) - DÉFINIE EN PREMIER
        console.log('Defining showCourseCompleteModal function...');
        window.showCourseCompleteModal = function(certificateGenerated) {
            console.log('showCourseCompleteModal called with:', certificateGenerated);

            const courseCompleteModal = document.getElementById('course-complete-modal');
            const courseCompleteMessage = document.getElementById('course-complete-message');
            const courseCompleteCertificate = document.getElementById('course-complete-certificate');

            console.log('Modal elements found:', {
                modal: !!courseCompleteModal,
                message: !!courseCompleteMessage,
                certificate: !!courseCompleteCertificate
            });

            if (!courseCompleteModal) {
                console.error('Course complete modal not found!');
                return;
            }

            // Mettre à jour le message selon si le certificat a été généré
            if (certificateGenerated) {
                courseCompleteMessage.textContent = '{{ translate("Vous avez terminé ce cours avec succès ! Votre certificat a été généré automatiquement.") }}';
                courseCompleteCertificate.style.display = 'inline-block';
            } else {
                courseCompleteMessage.textContent = '{{ translate("Vous avez terminé ce cours avec succès !") }}';
                courseCompleteCertificate.style.display = 'none';
            }

            // Afficher le modal
            console.log('Showing course complete modal');
            courseCompleteModal.style.display = 'flex';

            // Gestionnaire pour le bouton "Continuer"
            const courseCompleteClose = document.getElementById('course-complete-close');
            if (courseCompleteClose) {
                courseCompleteClose.onclick = function() {
                    courseCompleteModal.style.display = 'none';
                };
            }

            // Gestionnaire pour le bouton "Voir le certificat"
            if (courseCompleteCertificate) {
                courseCompleteCertificate.onclick = function() {
                    // Rediriger vers la page des certificats
                    window.location.href = '{{ route("student.certificate.index") }}';
                };
            }

            // Fermer le modal en cliquant à l'extérieur
            courseCompleteModal.onclick = function(e) {
                if (e.target === courseCompleteModal) {
                    courseCompleteModal.style.display = 'none';
                }
            };
        }

        // Vérifier que la fonction est bien définie
        console.log('Function defined successfully:', typeof window.showCourseCompleteModal);

        // Attendre que le DOM soit complètement chargé
        function initReadingProgress() {
            const markButton = document.getElementById('mark-reading-complete');
            const modal = document.getElementById('reading-complete-modal');
            const modalTitle = document.getElementById('modal-title');
            const modalMessage = document.getElementById('modal-message');
            const modalClose = document.getElementById('modal-close');

            if (!markButton || !modal) {
                return;
            }


            // Gestionnaire de clic sur le bouton
            markButton.onclick = function(e) {
                e.preventDefault();
                e.stopPropagation();

                const topicId = this.getAttribute('data-topic-id');
                const topicType = this.getAttribute('data-topic-type');

                if (!topicId) {
                    toastr.error('Erreur: ID du topic non trouvé');
                    return;
                }

                // Token CSRF
                const csrfToken = '{{ csrf_token() }}';

                // Désactiver le bouton temporairement
                this.disabled = true;
                this.innerHTML = '<i class="ri-loader-4-line animate-spin text-xs sm:text-sm"></i><span class="hidden xs:inline">{{ translate("Enregistrement...") }}</span><span class="xs:hidden">{{ translate("...") }}</span>';

                // Envoyer la requête - Version corrigée 2025-10-23 - Cache bust FORCE
                const url = '{{ route("student.topic.mark-completed") }}';
                console.log('CSRF Token:', csrfToken);
                console.log('URL de la requête:', url);
                console.log('Timestamp:', new Date().getTime());
                console.log('Version: 2025-10-23-FORCE-CACHE-BUST-V2');
                console.log('Cache bust timestamp:', Date.now());
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        topic_id: topicId,
                        topic_type: topicType
                    })
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers);

                    if (!response.ok) {
                        console.error('HTTP Error:', response.status, response.statusText);
                        throw new Error('Erreur HTTP: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {

                    if (data.status === 'success') {
                        // Afficher un toast de succès
                       /* toastr.success('Leçon marquée comme terminée avec succès !');*/

                        // Mettre à jour le modal
                        if (data.is_last_topic_in_chapter) {
                            modalTitle.textContent = '{{ translate("Chapitre terminé !") }}';
                            modalMessage.textContent = '{{ translate("Félicitations ! Vous avez terminé ce chapitre. Votre progression a été enregistrée.") }}';
                        } else {
                            modalTitle.textContent = '{{ translate("Leçon terminée !") }}';
                            modalMessage.textContent = '{{ translate("Vous avez terminé cette leçon. Votre progression a été enregistrée.") }}';
                        }

                        // Afficher le modal
                        modal.style.display = 'flex';

                        // Vérifier si le cours est complètement terminé
                        if (data.course_completed || data.certificate_generated) {
                            // Fermer le modal de leçon d'abord
                            modal.style.display = 'none';
                            // Afficher le modal de completion du cours
                            console.log('About to call showCourseCompleteModal...');
                            console.log('Function exists?', typeof window.showCourseCompleteModal);
                            setTimeout(() => {
                                if (typeof window.showCourseCompleteModal === 'function') {
                                    window.showCourseCompleteModal(data.certificate_generated);
                                } else {
                                    console.error('showCourseCompleteModal is not a function!');
                                }
                            }, 500);
                        } else {
                            // Message simple de confirmation pour la leçon
                            modalMessage.textContent = '{{ translate("Vous avez terminé cette leçon. Votre progression a été enregistrée.") }}';
                            modalClose.textContent = '{{ translate("Continuer") }}';
                        }

                        // Remplacer le bouton par l'icône de validation
                        const buttonContainer = markButton.parentElement;
                        buttonContainer.innerHTML = `
                            <div class="flex items-center gap-1 sm:gap-2 text-green-600 bg-green-100 px-2 py-1 sm:px-3 sm:py-2 rounded-md text-xs sm:text-sm">
                                <i class="ri-check-double-line text-xs sm:text-sm"></i>
                                <span class="hidden xs:inline">{{ translate("Terminée") }}</span>
                                <span class="xs:hidden">{{ translate("✓") }}</span>
                            </div>
                        `;
                    } else {
                        throw new Error(data.message || 'Erreur inconnue');
                    }
                })
                .catch(error => {
                    console.error('Erreur complète:', error);
                    console.error('Stack trace:', error.stack);

                    if (error.message.includes('JSON')) {
                        toastr.error('Erreur de format de réponse du serveur. Vérifiez la console pour plus de détails.');
                    } else {
                        toastr.error('Erreur: ' + error.message);
                    }

                    // Réactiver le bouton en cas d'erreur
                    markButton.disabled = false;
                    markButton.innerHTML = '<i class="ri-check-line text-xs sm:text-sm"></i><span class="hidden xs:inline">{{ translate("Marquer comme terminé") }}</span><span class="xs:hidden">{{ translate("Marquer") }}</span>';
                });
            };

            // Gestionnaire de fermeture du modal
            if (modalClose) {
                modalClose.onclick = function() {
                    // Fermer le modal
                    modal.style.display = 'none';
                };
            }


            // Fermer le modal en cliquant à l'extérieur
            modal.onclick = function(e) {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            };
        }

        // Initialiser quand le DOM est prêt
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initReadingProgress);
        } else {
            initReadingProgress();
        }
        </script>
    @endif
@elseif($type == 'assignment')
    <div class="p-5 md:p-8 xl:p-10 relative overflow-hidden aspect-[16/7] bg-primary-50 rounded-xl">
        <div class="size-full flex-center">
            <a href="{{ route('exam.start', ['type' => $type, 'exam_type_id' => $data->id, 'course_id' => $data?->topic?->course_id]) }}" aria-label="Go to Assignment"
                class="btn b-solid btn-primary-solid">
                {{ translate('Go to Assignment') }}
            </a>
        </div>
    </div>
@elseif($type == 'quiz')
    <div class="p-5 md:p-8 xl:p-10 relative overflow-hidden aspect-[16/7] bg-primary-50 rounded-xl">
        <div class="size-full flex-center flex-col gap-4">
            <div class="text-center">
                <h5 class="area-title text-xl mb-2">{{ $data->title }}</h5>
                <p class="text-gray-600 mb-4">{{ translate('Testez vos connaissances avec ce quiz') }}</p>
            </div>

            <a href="{{ route('exam.start', ['type' => $type, 'exam_type_id' => $data->id, 'course_id' => $topic['courseId'] ?? request('course_id'), 'topic_id' => $topic['topicId'] ?? request('topic_id'), 'chapterId' => $topic['chapterId'] ?? request('chapter_id')]) }}" aria-label="Go to Quiz"
                class="btn b-solid btn-primary-solid">
                {{ translate('Commencer le Quiz') }}
            </a>

            <!-- Bouton Marquer comme terminé pour les quiz -->
            @if(auth()->check() && auth()->user()->guard === 'student')
                @php
                    // Trouver le topic correspondant au quiz
                    $quizTopic = \Modules\LMS\Models\Courses\Topic::where('topicable_id', $data->id)
                        ->where('topicable_type', 'Modules\\LMS\\Models\\Courses\\Topics\\Quiz')
                        ->first();

                    // Vérifier si le quiz a été soumis (score >= pass_mark)
                    $quizSubmitted = false;
                    $quizPassed = false;
                    $userQuiz = null;
                    $topicAlreadyCompleted = false;

                    if (auth()->check()) {
                        $userQuiz = \Modules\LMS\Models\Auth\UserCourseExam::where('user_id', auth()->id())
                            ->where('quiz_id', $data->id)
                            ->whereNotNull('score')
                            ->first();

                        if ($userQuiz) {
                            $quizSubmitted = true;
                            $quizPassed = $userQuiz->score >= $data->pass_mark;
                        }

                        // Vérifier si le topic est déjà marqué comme terminé
                        if ($quizTopic) {
                            $topicProgress = \Modules\LMS\Models\TopicProgress::where('user_id', auth()->id())
                                ->where('topic_id', $quizTopic->id)
                                ->where('status', 'completed')
                                ->first();

                            $topicAlreadyCompleted = $topicProgress !== null;

                            // Debug logs pour vérifier le statut
                            \Log::info('Topic Completion Check', [
                                'user_id' => auth()->id(),
                                'topic_id' => $quizTopic->id,
                                'topic_progress_found' => $topicProgress ? true : false,
                                'topic_progress_status' => $topicProgress ? $topicProgress->status : null,
                                'topic_already_completed' => $topicAlreadyCompleted
                            ]);
                        }

                        // Debug logs
                        \Log::info('Quiz Debug Info', [
                            'user_id' => auth()->id(),
                            'quiz_id' => $data->id,
                            'quiz_topic_id' => $quizTopic ? $quizTopic->id : null,
                            'quiz_pass_mark' => $data->pass_mark,
                            'userQuiz_found' => $userQuiz ? true : false,
                            'userQuiz_score' => $userQuiz ? $userQuiz->score : null,
                            'quizSubmitted' => $quizSubmitted,
                            'quizPassed' => $quizPassed
                        ]);
                    }
                @endphp

                <div class="mt-4">
                    @if($topicAlreadyCompleted)
                        <!-- Topic déjà terminé - Afficher le statut de completion -->
                        <div class="flex items-center gap-2 text-green-600 bg-green-100 px-4 py-2 rounded-md">
                            <i class="ri-check-double-line"></i>
                            <span>{{ translate('Quiz terminé avec succès') }}</span>
                        </div>
                    @elseif($quizSubmitted && $quizPassed)
                        <!-- Quiz soumis et réussi - Afficher le bouton pour marquer comme terminé -->
                        <button id="mark-quiz-complete"
                                class="btn b-solid btn-success-solid flex items-center gap-2"
                                data-topic-id="{{ $quizTopic ? $quizTopic->id : request('topic_id') }}"
                                data-topic-type="quiz">
                            <i class="ri-check-line"></i>
                            {{ translate('Marquer comme terminé') }}
                        </button>
                    @elseif($quizSubmitted && !$quizPassed)
                        <!-- Quiz soumis mais échoué -->
                        <div class="flex items-center gap-2 text-red-600 bg-red-100 px-4 py-2 rounded-md">
                            <i class="ri-close-line"></i>
                            <span>{{ translate('Quiz échoué - Score: ') }}{{ $userQuiz->score ?? 0 }}/{{ $data->total_mark ?? 100 }}</span>
                        </div>
                    @else
                        <!-- Quiz pas encore soumis - Cacher le bouton -->
                        <div class="flex items-center gap-2 text-gray-500 bg-gray-100 px-4 py-2 rounded-md">
                            <i class="ri-lock-line"></i>
                            <span>{{ translate('Soumettez d\'abord le quiz pour le marquer comme terminé') }}</span>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Modal de confirmation pour les quiz -->
        @if(auth()->check() && auth()->user()->guard === 'student')
            <div id="quiz-complete-modal" style="
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 9999;
                display: none;
                justify-content: center;
                align-items: center;
                padding: 20px;
                box-sizing: border-box;
            ">
                <div style="
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
                    max-width: 400px;
                    width: 100%;
                    padding: 30px;
                    text-align: center;
                    position: relative;
                ">
                    <div style="
                        width: 60px;
                        height: 60px;
                        background-color: #10B981;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin: 0 auto 20px;
                    ">
                        <i class="ri-check-line" style="color: white; font-size: 24px;"></i>
                    </div>

                    <h3 id="quiz-modal-title" style="
                        font-size: 20px;
                        font-weight: 600;
                        color: #1F2937;
                        margin: 0 0 10px 0;
                    ">
                        {{ translate('Quiz terminé !') }}
                    </h3>

                    <p id="quiz-modal-message" style="
                        font-size: 14px;
                        color: #6B7280;
                        margin: 0 0 25px 0;
                        line-height: 1.5;
                    ">
                        {{ translate('Vous avez terminé ce quiz. Votre progression a été enregistrée.') }}
                    </p>

                    <button id="quiz-modal-close" style="
                        background-color: #3B82F6;
                        color: white;
                        border: none;
                        padding: 10px 20px;
                        border-radius: 6px;
                        font-size: 14px;
                        font-weight: 500;
                        cursor: pointer;
                        transition: background-color 0.2s;
                    " onmouseover="this.style.backgroundColor='#2563EB'" onmouseout="this.style.backgroundColor='#3B82F6'">
                        {{ translate('Continuer') }}
                    </button>
                </div>
            </div>

            <script>
            // Attendre que le DOM soit complètement chargé
            function initQuizProgress() {
                const markButton = document.getElementById('mark-quiz-complete');
                const modal = document.getElementById('quiz-complete-modal');
                const modalTitle = document.getElementById('quiz-modal-title');
                const modalMessage = document.getElementById('quiz-modal-message');
                const modalClose = document.getElementById('quiz-modal-close');

                if (!markButton || !modal) {
                    return;
                }

                // Gestionnaire de clic sur le bouton
                markButton.onclick = function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const topicId = this.getAttribute('data-topic-id');
                    const topicType = this.getAttribute('data-topic-type');

                    if (!topicId) {
                        toastr.error('Erreur: ID du topic non trouvé');
                        return;
                    }

                    // Token CSRF
                    const csrfToken = '{{ csrf_token() }}';

                    // Désactiver le bouton temporairement
                    this.disabled = true;
                    this.innerHTML = '<i class="ri-loader-4-line animate-spin"></i> {{ translate("Enregistrement...") }}';

                    // Envoyer la requête - Version corrigée 2025-10-23
                                     const url = '{{ route("student.topic.mark-completed") }}';
                                     fetch(url, {
                                         method: 'POST',
                                         headers: {
                                             'Content-Type': 'application/json',
                                             'X-CSRF-TOKEN': csrfToken,
                                             'Accept': 'application/json'
                                         },
                                         body: JSON.stringify({
                                             topic_id: topicId,
                                             topic_type: topicType
                                         })
                                     })                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Erreur HTTP: ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === 'success') {
                            // Vérifier si le cours est complètement terminé
                            if (data.course_completed || data.certificate_generated) {
                                // Afficher le modal de completion du cours
                                window.showCourseCompleteModal(data.certificate_generated);
                            } else {
                                // Mettre à jour le modal normal
                                if (data.is_last_topic_in_chapter) {
                                    modalTitle.textContent = '{{ translate("Chapitre terminé !") }}';
                                    modalMessage.textContent = '{{ translate("Félicitations ! Vous avez terminé ce chapitre. Votre progression a été enregistrée.") }}';
                                } else {
                                    modalTitle.textContent = '{{ translate("Quiz terminé !") }}';
                                    modalMessage.textContent = '{{ translate("Vous avez terminé ce quiz. Votre progression a été enregistrée.") }}';
                                }

                                // Afficher le modal
                                modal.style.display = 'flex';
                            }

                            // Remplacer le bouton par l'icône de validation
                            const buttonContainer = markButton.parentElement;
                            buttonContainer.innerHTML = `
                                <div class="flex items-center gap-2 text-green-600 bg-green-100 px-4 py-2 rounded-md">
                                    <i class="ri-check-double-line"></i>
                                    <span>{{ translate("Quiz terminé") }}</span>
                                </div>
                            `;

                            // Rafraîchir la page après 2 secondes
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                        } else {
                            throw new Error(data.message || 'Erreur inconnue');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur: ' + error.message);

                        // Réactiver le bouton en cas d'erreur
                        markButton.disabled = false;
                        markButton.innerHTML = '<i class="ri-check-line"></i> {{ translate("Marquer comme terminé") }}';
                    });
                };

                // Gestionnaire de fermeture du modal
                if (modalClose) {
                    modalClose.onclick = function() {
                        modal.style.display = 'none';
                        // Rafraîchir la page après fermeture du modal
                        window.location.reload();
                    };
                }

                // Fermer le modal en cliquant à l'extérieur
                modal.onclick = function(e) {
                    if (e.target === modal) {
                        modal.style.display = 'none';
                        // Rafraîchir la page après fermeture du modal
                        window.location.reload();
                    }
                };
            }

            // Initialiser quand le DOM est prêt
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initQuizProgress);
            } else {
                initQuizProgress();
            }
            </script>
        @endif
    </div>
@endif

--}}
{{--

@php
    $data = $topic['data'];
@endphp

    <!-- DÉFINITION GLOBALE DE LA FONCTION (DÉBUT) -->
<script>
    // Gestion d'erreur globale pour éviter les erreurs Flasher
    window.addEventListener('error', function(e) {
        if (e.message && e.message.includes('Flasher is not loaded')) {
            console.warn('Flasher error caught and ignored:', e.message);
            e.preventDefault();
            return false;
        }
    });

    // ✅ FONCTION GLOBALE - Définie AVANT tout usage
    console.log('📍 Définition de window.showCourseCompleteModal');
    window.showCourseCompleteModal = function(certificateGenerated) {
        console.log('✅ showCourseCompleteModal appelée avec:', certificateGenerated);

        const courseCompleteModal = document.getElementById('course-complete-modal');
        const courseCompleteMessage = document.getElementById('course-complete-message');
        const courseCompleteCertificate = document.getElementById('course-complete-certificate');

        if (!courseCompleteModal) {
            console.error('❌ Modal #course-complete-modal introuvable!');
            return;
        }

        // Mettre à jour le message selon si le certificat a été généré
        if (certificateGenerated) {
            if (courseCompleteMessage) {
                courseCompleteMessage.textContent = '{{ translate("Vous avez terminé ce cours avec succès ! Votre certificat a été généré automatiquement.") }}';
            }
            if (courseCompleteCertificate) {
                courseCompleteCertificate.style.display = 'inline-block';
            }
        } else {
            if (courseCompleteMessage) {
                courseCompleteMessage.textContent = '{{ translate("Vous avez terminé ce cours avec succès !") }}';
            }
            if (courseCompleteCertificate) {
                courseCompleteCertificate.style.display = 'none';
            }
        }

        // Afficher le modal
        courseCompleteModal.style.display = 'flex';
        console.log('✅ Modal affiché');

        // Gestionnaire pour le bouton "Continuer"
        const courseCompleteClose = document.getElementById('course-complete-close');
        if (courseCompleteClose) {
            courseCompleteClose.onclick = function() {
                courseCompleteModal.style.display = 'none';
            };
        }

        // Gestionnaire pour le bouton "Voir le certificat"
        if (courseCompleteCertificate) {
            courseCompleteCertificate.onclick = function() {
                window.location.href = '{{ route("student.certificate.index") }}';
            };
        }

        // Fermer le modal en cliquant à l'extérieur
        courseCompleteModal.onclick = function(e) {
            if (e.target === courseCompleteModal) {
                courseCompleteModal.style.display = 'none';
            }
        };
    };

    console.log('✅ Fonction définie avec succès:', typeof window.showCourseCompleteModal);
</script>
<!-- DÉFINITION GLOBALE DE LA FONCTION (FIN) -->

@if ($type == 'video')
    @if ($data->video_src_type == 'youtube' || $data->video_src_type == 'vimeo')
        <div class="plyr__video-embed" id="player">
            <iframe src="{{ $data->video_url }}" allowfullscreen allowtransparency allow="autoplay"></iframe>
        </div>
        <script>
            console.log('🔧 [THEME-COURSE-LEARN] Script YouTube/Vimeo chargé');

            var videoPlayer = new Plyr("#player", {
                settings: ["speed"],
                seekTime: 0,
                ratio: "16:7",
                speed: {
                    selected: 1,
                    options: [0.5, 0.75, 1, 1.25, 1.5]
                },
            });

            console.log('🔧 [THEME-COURSE-LEARN] Plyr player initialisé:', videoPlayer);

            @if(auth()->check() && auth()->user()->guard === 'student')
            var isVideoStarted = false;
            var isVideoCompleted = false;

            // Fonction pour obtenir le topic ID
            function getTopicId() {
                console.log('🔍 [THEME-LEARN] Recherche du topic ID...');

                // Méthode 1: Chercher dans les données passées par le backend
                @if(isset($topic['topicId']))
                    console.log('✅ [THEME-LEARN] Topic ID trouvé depuis backend:', {{ $topic['topicId'] }});
                    return {{ $topic['topicId'] }};
                @endif

                // Méthode 2: Chercher dans l'URL parent (window.parent pour iframe)
                try {
                    const parentUrl = new URLSearchParams(window.parent.location.search);
                    const topicId = parentUrl.get('topic_id');
                    if (topicId) {
                        console.log('✅ [THEME-LEARN] Topic ID trouvé dans parent URL:', topicId);
                        return topicId;
                    }
                } catch(e) {
                    console.log('⚠️ [THEME-LEARN] Impossible d\'accéder à parent URL');
                }

                // Méthode 3: Chercher dans les attributs data du parent
                try {
                    const topicElement = window.parent.document.querySelector('[data-topic-id].active') ||
                                       window.parent.document.querySelector('[data-topic-id]');
                    if (topicElement) {
                        const id = topicElement.getAttribute('data-topic-id');
                        console.log('✅ [THEME-LEARN] Topic ID trouvé dans parent DOM:', id);
                        return id;
                    }
                } catch(e) {
                    console.log('⚠️ [THEME-LEARN] Impossible d\'accéder au parent DOM');
                }

                console.error('❌ [THEME-LEARN] Aucun topic ID trouvé!');
                return null;
            }

            // Détecter le clic sur play
            videoPlayer.on('play', function() {
                console.log('▶️ [THEME-LEARN] Event PLAY déclenché!');
                console.log('▶️ [THEME-LEARN] isVideoStarted:', isVideoStarted);
                if (!isVideoStarted) {
                    isVideoStarted = true;
                    console.log('▶️ [THEME-LEARN] Marquer comme commencé');

                    const topicId = getTopicId();
                    if (topicId) {
                        console.log('🚀 [THEME-LEARN] Envoi de la progression start pour topic:', topicId);
                        fetch(`{{ route('student.topic.start', '') }}/${topicId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': window.parent.document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log('✅ [THEME-LEARN] Réponse start:', data);
                        })
                        .catch(error => {
                            console.error('❌ [THEME-LEARN] Erreur start:', error);
                        });
                    } else {
                        console.error('❌ [THEME-LEARN] Impossible de marquer comme commencé: pas de topic ID');
                    }
                }
            });

            // Détecter la fin
            videoPlayer.on('ended', function() {
                console.log('🎬 [THEME-LEARN] Event ENDED déclenché!');
                if (!isVideoCompleted) {
                    isVideoCompleted = true;
                    console.log('🎬 [THEME-LEARN] Vidéo terminée');

                    const topicId = getTopicId();
                    if (topicId) {
                        console.log('🏁 [THEME-LEARN] Envoi de la progression complete pour topic:', topicId);
                        fetch(`{{ route('student.topic.complete', '') }}/${topicId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': window.parent.document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log('✅ [THEME-LEARN] Réponse complete:', data);

                            // Afficher un modal si le chapitre ou le cours est terminé
                            if (data.certificate_generated) {
                                console.log('🎓 [THEME-LEARN] Certificat généré!');

                                // Afficher le modal dans le parent
                                try {
                                    if (window.parent && typeof window.parent.showCourseCompleteModal === 'function') {
                                        window.parent.showCourseCompleteModal(true);
                                    } else {
                                        alert('🎓 Félicitations ! Vous avez obtenu votre certificat !');
                                    }
                                } catch(e) {
                                    console.error('Erreur lors de l\'affichage du modal:', e);
                                    alert('🎓 Félicitations ! Vous avez obtenu votre certificat !');
                                }
                            } else if (data.chapter_completed) {
                                console.log('📖 [THEME-LEARN] Chapitre terminé!');

                                // Afficher un message pour le chapitre terminé
                                try {
                                    if (window.parent && typeof window.parent.showLessonCompleteModal === 'function') {
                                        window.parent.showLessonCompleteModal({
                                            chapter_completed: true,
                                            is_last_topic_in_chapter: true
                                        });
                                    } else {
                                        alert('📖 Félicitations ! Chapitre terminé !');
                                    }
                                } catch(e) {
                                    console.error('Erreur lors de l\'affichage du modal:', e);
                                    alert('📖 Félicitations ! Chapitre terminé !');
                                }
                            } else {
                                console.log('✅ [THEME-LEARN] Leçon terminée!');

                                // Afficher un simple message pour la leçon terminée
                                try {
                                    if (window.parent && typeof window.parent.showLessonCompleteModal === 'function') {
                                        window.parent.showLessonCompleteModal({
                                            chapter_completed: false,
                                            is_last_topic_in_chapter: false
                                        });
                                    }
                                } catch(e) {
                                    console.log('Leçon terminée (pas de modal)');
                                }
                            }
                        })
                        .catch(error => {
                            console.error('❌ [THEME-LEARN] Erreur complete:', error);
                        });
                    } else {
                        console.error('❌ [THEME-LEARN] Impossible de marquer comme terminé: pas de topic ID');
                    }
                }
            });

            console.log('✅ [THEME-LEARN] Listeners installés');
            @endif
        </script>
    @elseif($data->video_src_type == 'local')
        @if (
            $data->system_video &&
                fileExists('lms/courses/topics/videos/', $data->system_video) == true &&
                $data->system_video != '')
            <video id="main-course-video" playsinline controls data-poster="assets/images/course/course-2.png">
                <source src="{{ asset('storage/lms/courses/topics/videos/' . $data->system_video) }}" type="video/mp4" />
            </video>
        @endif
        <script>
            var videoPlayer = new Plyr("#player", {
                settings: ["speed"],
                seekTime: 0,
                ratio: "16:7",
                speed: {
                    selected: 1,
                    options: [0.5, 0.75, 1, 1.25, 1.5]
                },
            });
        </script>
    @endif
@elseif($type == 'reading')
    <div class="p-5 md:p-8 xl:p-10 relative overflow-hidden aspect-[16/7] bg-primary-50 rounded-xl">
        <div class="size-full overflow-x-hidden overflow-y-auto">
            <h5 class="area-title text-xl">{{ $data->title }}</h5>
            <div class="area-description mt-5">
                {!! clean($data->description) !!}
            </div>
        </div>

        <!-- Bouton Marquer comme terminé pour les cours reading -->
        @if(auth()->check() && auth()->user()->guard === 'student')
            @php
                // Vérifier si la leçon est déjà terminée
                $topicProgress = null;
                if (auth()->check()) {
                    $topic = \Modules\LMS\Models\Courses\Topic::where('topicable_id', $data->id)
                        ->where('topicable_type', 'Modules\\LMS\\Models\\Courses\\Topics\\Reading')
                        ->first();

                    if ($topic) {
                        $topicProgress = \Modules\LMS\Models\TopicProgress::where('user_id', auth()->id())
                            ->where('topic_id', $topic->id)
                            ->first();
                    }
                }
                $isCompleted = $topicProgress && $topicProgress->isCompleted();
            @endphp

            <div class="absolute bottom-2 right-2 sm:bottom-4 sm:right-4">
                @if($isCompleted)
                    <!-- Icône de validation si terminée -->
                    <div class="flex items-center gap-1 sm:gap-2 text-green-600 bg-green-100 px-2 py-1 sm:px-3 sm:py-2 rounded-md text-xs sm:text-sm">
                        <i class="ri-check-double-line text-xs sm:text-sm"></i>
                        <span class="hidden xs:inline">{{ translate('Terminée') }}</span>
                        <span class="xs:hidden">{{ translate('✓') }}</span>
                    </div>
                @else
                    <!-- Bouton pour marquer comme terminé -->
                    <button id="mark-reading-complete"
                            class="btn b-solid btn-success-solid text-xs sm:text-sm px-2 py-1 sm:px-3 sm:py-2 flex items-center gap-1 sm:gap-2 whitespace-nowrap"
                            data-topic-id="{{ $topic['id'] }}"
                            data-topic-type="reading">
                        <i class="ri-check-line text-xs sm:text-sm"></i>
                        <span class="hidden xs:inline">{{ translate('Marquer comme terminé') }}</span>
                        <span class="xs:hidden">{{ translate('Marquer') }}</span>
                    </button>
                @endif
            </div>
        @endif
    </div>

    <!-- Modal de confirmation pour les cours reading -->
    @if(auth()->check() && auth()->user()->guard === 'student')
        <div id="reading-complete-modal" style="
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 9999;
                display: none;
                justify-content: center;
                align-items: center;
                padding: 20px;
                box-sizing: border-box;
            ">
            <div style="
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
                    max-width: 400px;
                    width: 100%;
                    padding: 30px;
                    text-align: center;
                    position: relative;
                ">
                <div style="
                        width: 60px;
                        height: 60px;
                        background-color: #10B981;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin: 0 auto 20px;
                    ">
                    <i class="ri-check-line" style="color: white; font-size: 24px;"></i>
                </div>

                <h3 id="modal-title" style="
                        font-size: 20px;
                        font-weight: 600;
                        color: #1F2937;
                        margin: 0 0 10px 0;
                    ">
                    {{ translate('Leçon terminée !') }}
                </h3>

                <p id="modal-message" style="
                        font-size: 14px;
                        color: #6B7280;
                        margin: 0 0 25px 0;
                        line-height: 1.5;
                    ">
                    {{ translate('Vous avez terminé cette leçon. Votre progression a été enregistrée.') }}
                </p>

                <button id="modal-close" style="
                        background-color: #3B82F6;
                        color: white;
                        border: none;
                        padding: 10px 20px;
                        border-radius: 6px;
                        font-size: 14px;
                        font-weight: 500;
                        cursor: pointer;
                        transition: background-color 0.2s;
                    " onmouseover="this.style.backgroundColor='#2563EB'" onmouseout="this.style.backgroundColor='#3B82F6'">
                    {{ translate('Continuer') }}
                </button>
            </div>
        </div>

        <!-- Modal de completion du cours -->
        <div id="course-complete-modal" style="
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 10000;
                display: none;
                justify-content: center;
                align-items: center;
                padding: 20px;
                box-sizing: border-box;
            ">
            <div style="
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
                    max-width: 500px;
                    width: 100%;
                    padding: 40px;
                    text-align: center;
                    position: relative;
                ">
                <div style="
                        width: 80px;
                        height: 80px;
                        background-color: #10B981;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin: 0 auto 25px;
                    ">
                    <i class="ri-trophy-line" style="color: white; font-size: 32px;"></i>
                </div>

                <h3 id="course-complete-title" style="
                        font-size: 24px;
                        font-weight: 700;
                        color: #1F2937;
                        margin: 0 0 15px 0;
                    ">
                    {{ translate('Félicitations !') }}
                </h3>

                <p id="course-complete-message" style="
                        font-size: 16px;
                        color: #6B7280;
                        margin: 0 0 30px 0;
                        line-height: 1.6;
                    ">
                    {{ translate('Vous avez terminé ce cours avec succès ! Votre certificat a été généré automatiquement.') }}
                </p>

                <div style="
                        display: flex;
                        gap: 15px;
                        justify-content: center;
                        flex-wrap: wrap;
                    ">
                    <button id="course-complete-close" style="
                            background-color: #3B82F6;
                            color: white;
                            border: none;
                            padding: 12px 24px;
                            border-radius: 6px;
                            font-size: 14px;
                            font-weight: 500;
                            cursor: pointer;
                            transition: background-color 0.2s;
                        " onmouseover="this.style.backgroundColor='#2563EB'" onmouseout="this.style.backgroundColor='#3B82F6'">
                        {{ translate('Continuer') }}
                    </button>

                    <button id="course-complete-certificate" style="
                            background-color: #10B981;
                            color: white;
                            border: none;
                            padding: 12px 24px;
                            border-radius: 6px;
                            font-size: 14px;
                            font-weight: 500;
                            cursor: pointer;
                            transition: background-color 0.2s;
                        " onmouseover="this.style.backgroundColor='#059669'" onmouseout="this.style.backgroundColor='#10B981'">
                        {{ translate('Voir le certificat') }}
                    </button>
                </div>
            </div>
        </div>

        <script>
            // Attendre que le DOM soit complètement chargé
            function initReadingProgress() {
                const markButton = document.getElementById('mark-reading-complete');
                const modal = document.getElementById('reading-complete-modal');
                const modalTitle = document.getElementById('modal-title');
                const modalMessage = document.getElementById('modal-message');
                const modalClose = document.getElementById('modal-close');

                if (!markButton || !modal) {
                    return;
                }

                // Gestionnaire de clic sur le bouton
                markButton.onclick = function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const topicId = this.getAttribute('data-topic-id');
                    const topicType = this.getAttribute('data-topic-type');

                    if (!topicId) {
                        toastr.error('Erreur: ID du topic non trouvé');
                        return;
                    }

                    // Token CSRF
                    const csrfToken = '{{ csrf_token() }}';

                    // Désactiver le bouton temporairement
                    this.disabled = true;
                    this.innerHTML = '<i class="ri-loader-4-line animate-spin text-xs sm:text-sm"></i><span class="hidden xs:inline">{{ translate("Enregistrement...") }}</span><span class="xs:hidden">{{ translate("...") }}</span>';

                    // Envoyer la requête
                    const url = '{{ route("student.topic.mark-completed") }}';
                    console.log('🔗 URL:', url);
                    console.log('📋 Data:', {topic_id: topicId, topic_type: topicType});

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            topic_id: topicId,
                            topic_type: topicType
                        })
                    })
                        .then(response => {
                            console.log('✅ Response status:', response.status);

                            if (!response.ok) {
                                console.error('❌ HTTP Error:', response.status, response.statusText);
                                throw new Error('Erreur HTTP: ' + response.status);
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('📦 Response data:', data);

                            if (data.status === 'success') {
                                // Afficher un toast de succès
                                // toastr.success('Leçon marquée comme terminée avec succès !');

                                // Mettre à jour le modal
                                if (data.is_last_topic_in_chapter) {
                                    modalTitle.textContent = '{{ translate("Chapitre terminé !") }}';
                                    modalMessage.textContent = '{{ translate("Félicitations ! Vous avez terminé ce chapitre. Votre progression a été enregistrée.") }}';
                                } else {
                                    modalTitle.textContent = '{{ translate("Leçon terminée !") }}';
                                    modalMessage.textContent = '{{ translate("Vous avez terminé cette leçon. Votre progression a été enregistrée.") }}';
                                }

                                // Afficher le modal
                                modal.style.display = 'flex';

                                // Vérifier si le cours est complètement terminé
                                if (data.course_completed || data.certificate_generated) {
                                    // Fermer le modal de leçon d'abord
                                    modal.style.display = 'none';
                                    // Afficher le modal de completion du cours
                                    console.log('🎯 Appel de showCourseCompleteModal...');
                                    console.log('✅ Fonction existe?', typeof window.showCourseCompleteModal);
                                    setTimeout(() => {
                                        if (typeof window.showCourseCompleteModal === 'function') {
                                            window.showCourseCompleteModal(data.certificate_generated);
                                        } else {
                                            console.error('❌ showCourseCompleteModal n\'est pas une fonction!');
                                        }
                                    }, 500);
                                } else {
                                    // Message simple de confirmation pour la leçon
                                    modalMessage.textContent = '{{ translate("Vous avez terminé cette leçon. Votre progression a été enregistrée.") }}';
                                    modalClose.textContent = '{{ translate("Continuer") }}';
                                }

                                // Remplacer le bouton par l'icône de validation
                                const buttonContainer = markButton.parentElement;
                                buttonContainer.innerHTML = `
                            <div class="flex items-center gap-1 sm:gap-2 text-green-600 bg-green-100 px-2 py-1 sm:px-3 sm:py-2 rounded-md text-xs sm:text-sm">
                                <i class="ri-check-double-line text-xs sm:text-sm"></i>
                                <span class="hidden xs:inline">{{ translate("Terminée") }}</span>
                                <span class="xs:hidden">{{ translate("✓") }}</span>
                            </div>
                        `;
                            } else {
                                throw new Error(data.message || 'Erreur inconnue');
                            }
                        })
                        .catch(error => {
                            console.error('❌ Erreur complète:', error);
                            console.error('📍 Stack trace:', error.stack);

                            if (error.message.includes('JSON')) {
                                toastr.error('Erreur de format de réponse du serveur. Vérifiez la console pour plus de détails.');
                            } else {
                                toastr.error('Erreur: ' + error.message);
                            }

                            // Réactiver le bouton en cas d'erreur
                            markButton.disabled = false;
                            markButton.innerHTML = '<i class="ri-check-line text-xs sm:text-sm"></i><span class="hidden xs:inline">{{ translate("Marquer comme terminé") }}</span><span class="xs:hidden">{{ translate("Marquer") }}</span>';
                        });
                };

                // Gestionnaire de fermeture du modal
                if (modalClose) {
                    modalClose.onclick = function() {
                        // Fermer le modal
                        modal.style.display = 'none';
                    };
                }

                // Fermer le modal en cliquant à l'extérieur
                modal.onclick = function(e) {
                    if (e.target === modal) {
                        modal.style.display = 'none';
                    }
                };
            }

            // Initialiser quand le DOM est prêt
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initReadingProgress);
            } else {
                initReadingProgress();
            }
        </script>
    @endif
@elseif($type == 'assignment')
    <div class="p-5 md:p-8 xl:p-10 relative overflow-hidden aspect-[16/7] bg-primary-50 rounded-xl">
        <div class="size-full flex-center">
            <a href="{{ route('exam.start', ['type' => $type, 'exam_type_id' => $data->id, 'course_id' => $data?->topic?->course_id]) }}" aria-label="Go to Assignment"
               class="btn b-solid btn-primary-solid">
                {{ translate('Go to Assignment') }}
            </a>
        </div>
    </div>
@elseif($type == 'quiz')
    <div class="p-5 md:p-8 xl:p-10 relative overflow-hidden aspect-[16/7] bg-primary-50 rounded-xl">
        <div class="size-full flex-center flex-col gap-4">
            <div class="text-center">
                <h5 class="area-title text-xl mb-2">{{ $data->title }}</h5>
                <p class="text-gray-600 mb-4">{{ translate('Testez vos connaissances avec ce quiz') }}</p>
            </div>

            <a href="{{ route('exam.start', ['type' => $type, 'exam_type_id' => $data->id, 'course_id' => $topic['courseId'] ?? request('course_id'), 'topic_id' => $topic['topicId'] ?? request('topic_id'), 'chapterId' => $topic['chapterId'] ?? request('chapter_id')]) }}" aria-label="Go to Quiz"
               class="btn b-solid btn-primary-solid">
                {{ translate('Commencer le Quiz') }}
            </a>

            <!-- Bouton Marquer comme terminé pour les quiz -->
            @if(auth()->check() && auth()->user()->guard === 'student')
                @php
                    // Trouver le topic correspondant au quiz
                    $quizTopic = \Modules\LMS\Models\Courses\Topic::where('topicable_id', $data->id)
                        ->where('topicable_type', 'Modules\\LMS\\Models\\Courses\\Topics\\Quiz')
                        ->first();

                    // Vérifier si le quiz a été soumis (score >= pass_mark)
                    $quizSubmitted = false;
                    $quizPassed = false;
                    $userQuiz = null;
                    $topicAlreadyCompleted = false;

                    if (auth()->check()) {
                        $userQuiz = \Modules\LMS\Models\Auth\UserCourseExam::where('user_id', auth()->id())
                            ->where('quiz_id', $data->id)
                            ->whereNotNull('score')
                            ->first();

                        if ($userQuiz) {
                            $quizSubmitted = true;
                            $quizPassed = $userQuiz->score >= $data->pass_mark;
                        }

                        // Vérifier si le topic est déjà marqué comme terminé
                        if ($quizTopic) {
                            $topicProgress = \Modules\LMS\Models\TopicProgress::where('user_id', auth()->id())
                                ->where('topic_id', $quizTopic->id)
                                ->where('status', 'completed')
                                ->first();

                            $topicAlreadyCompleted = $topicProgress !== null;

                            // Debug logs pour vérifier le statut
                            \Log::info('Topic Completion Check', [
                                'user_id' => auth()->id(),
                                'topic_id' => $quizTopic->id,
                                'topic_progress_found' => $topicProgress ? true : false,
                                'topic_progress_status' => $topicProgress ? $topicProgress->status : null,
                                'topic_already_completed' => $topicAlreadyCompleted
                            ]);
                        }

                        // Debug logs
                        \Log::info('Quiz Debug Info', [
                            'user_id' => auth()->id(),
                            'quiz_id' => $data->id,
                            'quiz_topic_id' => $quizTopic ? $quizTopic->id : null,
                            'quiz_pass_mark' => $data->pass_mark,
                            'userQuiz_found' => $userQuiz ? true : false,
                            'userQuiz_score' => $userQuiz ? $userQuiz->score : null,
                            'quizSubmitted' => $quizSubmitted,
                            'quizPassed' => $quizPassed
                        ]);
                    }
                @endphp

                <div class="mt-4">
                    @if($topicAlreadyCompleted)
                        <!-- Topic déjà terminé - Afficher le statut de completion -->
                        <div class="flex items-center gap-2 text-green-600 bg-green-100 px-4 py-2 rounded-md">
                            <i class="ri-check-double-line"></i>
                            <span>{{ translate('Quiz terminé avec succès') }}</span>
                        </div>
                    @elseif($quizSubmitted && $quizPassed)
                        <!-- Quiz soumis et réussi - Afficher le bouton pour marquer comme terminé -->
                        <button id="mark-quiz-complete"
                                class="btn b-solid btn-success-solid flex items-center gap-2"
                                data-topic-id="{{ $quizTopic ? $quizTopic->id : request('topic_id') }}"
                                data-topic-type="quiz">
                            <i class="ri-check-line"></i>
                            {{ translate('Marquer comme terminé') }}
                        </button>
                    @elseif($quizSubmitted && !$quizPassed)
                        <!-- Quiz soumis mais échoué -->
                        <div class="flex items-center gap-2 text-red-600 bg-red-100 px-4 py-2 rounded-md">
                            <i class="ri-close-line"></i>
                            <span>{{ translate('Quiz échoué - Score: ') }}{{ $userQuiz->score ?? 0 }}/{{ $data->total_mark ?? 100 }}</span>
                        </div>
                    @else
                        <!-- Quiz pas encore soumis - Cacher le bouton -->
                        <div class="flex items-center gap-2 text-gray-500 bg-gray-100 px-4 py-2 rounded-md">
                            <i class="ri-lock-line"></i>
                            <span>{{ translate('Soumettez d\'abord le quiz pour le marquer comme terminé') }}</span>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Modal de confirmation pour les quiz -->
        @if(auth()->check() && auth()->user()->guard === 'student')
            <div id="quiz-complete-modal" style="
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 9999;
                display: none;
                justify-content: center;
                align-items: center;
                padding: 20px;
                box-sizing: border-box;
            ">
                <div style="
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
                    max-width: 400px;
                    width: 100%;
                    padding: 30px;
                    text-align: center;
                    position: relative;
                ">
                    <div style="
                        width: 60px;
                        height: 60px;
                        background-color: #10B981;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin: 0 auto 20px;
                    ">
                        <i class="ri-check-line" style="color: white; font-size: 24px;"></i>
                    </div>

                    <h3 id="quiz-modal-title" style="
                        font-size: 20px;
                        font-weight: 600;
                        color: #1F2937;
                        margin: 0 0 10px 0;
                    ">
                        {{ translate('Quiz terminé !') }}
                    </h3>

                    <p id="quiz-modal-message" style="
                        font-size: 14px;
                        color: #6B7280;
                        margin: 0 0 25px 0;
                        line-height: 1.5;
                    ">
                        {{ translate('Vous avez terminé ce quiz. Votre progression a été enregistrée.') }}
                    </p>

                    <button id="quiz-modal-close" style="
                        background-color: #3B82F6;
                        color: white;
                        border: none;
                        padding: 10px 20px;
                        border-radius: 6px;
                        font-size: 14px;
                        font-weight: 500;
                        cursor: pointer;
                        transition: background-color 0.2s;
                    " onmouseover="this.style.backgroundColor='#2563EB'" onmouseout="this.style.backgroundColor='#3B82F6'">
                        {{ translate('Continuer') }}
                    </button>
                </div>
            </div>

            <script>
                // ✅ Attendre que le DOM soit complètement chargé
                function initQuizProgress() {
                    const markButton = document.getElementById('mark-quiz-complete');
                    const modal = document.getElementById('quiz-complete-modal');
                    const modalTitle = document.getElementById('quiz-modal-title');
                    const modalMessage = document.getElementById('quiz-modal-message');
                    const modalClose = document.getElementById('quiz-modal-close');

                    if (!markButton || !modal) {
                        console.log('⚠️ Quiz elements not found');
                        return;
                    }

                    // Gestionnaire de clic sur le bouton
                    markButton.onclick = function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        const topicId = this.getAttribute('data-topic-id');
                        const topicType = this.getAttribute('data-topic-type');

                        if (!topicId) {
                            toastr.error('Erreur: ID du topic non trouvé');
                            return;
                        }

                        // Token CSRF
                        const csrfToken = '{{ csrf_token() }}';

                        // Désactiver le bouton temporairement
                        this.disabled = true;
                        this.innerHTML = '<i class="ri-loader-4-line animate-spin"></i> {{ translate("Enregistrement...") }}';

                        // Envoyer la requête
                        const url = '{{ route("student.topic.mark-completed") }}';
                        console.log('🔗 Quiz URL:', url);
                        console.log('📋 Quiz Data:', {topic_id: topicId, topic_type: topicType});

                        fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                topic_id: topicId,
                                topic_type: topicType
                            })
                        })
                            .then(response => {
                                console.log('✅ Quiz Response status:', response.status);

                                if (!response.ok) {
                                    throw new Error('Erreur HTTP: ' + response.status);
                                }
                                return response.json();
                            })
                            .then(data => {
                                console.log('📦 Quiz Response data:', data);

                                if (data.status === 'success') {
                                    // Vérifier si le cours est complètement terminé
                                    if (data.course_completed || data.certificate_generated) {
                                        console.log('🎯 Quiz - Appel de showCourseCompleteModal...');
                                        console.log('✅ Fonction existe?', typeof window.showCourseCompleteModal);

                                        // Afficher le modal de completion du cours
                                        if (typeof window.showCourseCompleteModal === 'function') {
                                            window.showCourseCompleteModal(data.certificate_generated);
                                        } else {
                                            console.error('❌ showCourseCompleteModal n\'est pas une fonction!');
                                        }
                                    } else {
                                        // Mettre à jour le modal normal
                                        if (data.is_last_topic_in_chapter) {
                                            modalTitle.textContent = '{{ translate("Chapitre terminé !") }}';
                                            modalMessage.textContent = '{{ translate("Félicitations ! Vous avez terminé ce chapitre. Votre progression a été enregistrée.") }}';
                                        } else {
                                            modalTitle.textContent = '{{ translate("Quiz terminé !") }}';
                                            modalMessage.textContent = '{{ translate("Vous avez terminé ce quiz. Votre progression a été enregistrée.") }}';
                                        }

                                        // Afficher le modal
                                        modal.style.display = 'flex';
                                    }

                                    // Remplacer le bouton par l'icône de validation
                                    const buttonContainer = markButton.parentElement;
                                    buttonContainer.innerHTML = `
                                <div class="flex items-center gap-2 text-green-600 bg-green-100 px-4 py-2 rounded-md">
                                    <i class="ri-check-double-line"></i>
                                    <span>{{ translate("Quiz terminé") }}</span>
                                </div>
                            `;

                                    // Rafraîchir la page après 2 secondes
                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 2000);
                                } else {
                                    throw new Error(data.message || 'Erreur inconnue');
                                }
                            })
                            .catch(error => {
                                console.error('❌ Erreur: ' + error.message);

                                // Réactiver le bouton en cas d'erreur
                                markButton.disabled = false;
                                markButton.innerHTML = '<i class="ri-check-line"></i> {{ translate("Marquer comme terminé") }}';
                            });
                    };

                    // Gestionnaire de fermeture du modal
                    if (modalClose) {
                        modalClose.onclick = function() {
                            modal.style.display = 'none';
                            // Rafraîchir la page après fermeture du modal
                            window.location.reload();
                        };
                    }

                    // Fermer le modal en cliquant à l'extérieur
                    modal.onclick = function(e) {
                        if (e.target === modal) {
                            modal.style.display = 'none';
                            // Rafraîchir la page après fermeture du modal
                            window.location.reload();
                        }
                    };
                }

                // Initialiser quand le DOM est prêt
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initQuizProgress);
                } else {
                    initQuizProgress();
                }
            </script>
        @endif
    </div>
@endif
--}}

@php
    $data = $topic['data'];
@endphp

    <!-- ✅ MODAL GLOBAL DE COMPLETION DU COURS (CRÉÉ UNE SEULE FOIS) -->
@if(auth()->check() && auth()->user()->guard === 'student')
    <div id="course-complete-modal" style="
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 10000;
        display: none;
        justify-content: center;
        align-items: center;
        padding: 20px;
        box-sizing: border-box;
    ">
        <div style="
            background: white;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 100%;
            padding: 40px;
            text-align: center;
            position: relative;
        ">
            <div style="
                width: 80px;
                height: 80px;
                background-color: #10B981;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 25px;
            ">
                <i class="ri-trophy-line" style="color: white; font-size: 32px;"></i>
            </div>

            <h3 id="course-complete-title" style="
                font-size: 24px;
                font-weight: 700;
                color: #1F2937;
                margin: 0 0 15px 0;
            ">
                {{ translate('Félicitations !') }}
            </h3>

            <p id="course-complete-message" style="
                font-size: 16px;
                color: #6B7280;
                margin: 0 0 30px 0;
                line-height: 1.6;
            ">
                {{ translate('Vous avez terminé ce cours avec succès ! Votre certificat a été généré automatiquement.') }}
            </p>

            <div style="
                display: flex;
                gap: 15px;
                justify-content: center;
                flex-wrap: wrap;
            ">
                <button id="course-complete-close" style="
                    background-color: #572571;
                    color: white;
                    border: none;
                    padding: 12px 24px;
                    border-radius: 6px;
                    font-size: 14px;
                    font-weight: 500;
                    cursor: pointer;
                    transition: background-color 0.2s;
                " onmouseover="this.style.backgroundColor='#572571'" onmouseout="this.style.backgroundColor='#572571'">
                    {{ translate('Continuer') }}
                </button>

                <button id="course-complete-certificate" style="
                    background-color: #10B981;
                    color: white;
                    border: none;
                    padding: 12px 24px;
                    border-radius: 6px;
                    font-size: 14px;
                    font-weight: 500;
                    cursor: pointer;
                    transition: background-color 0.2s;
                " onmouseover="this.style.backgroundColor='#059669'" onmouseout="this.style.backgroundColor='#10B981'">
                    {{ translate('Voir le certificat') }}
                </button>
            </div>
        </div>
    </div>
@endif

<!-- DÉFINITION GLOBALE DE LA FONCTION (DÉBUT) -->
<script>
    // Gestion d'erreur globale pour éviter les erreurs Flasher
    window.addEventListener('error', function(e) {
        if (e.message && e.message.includes('Flasher is not loaded')) {
            console.warn('Flasher error caught and ignored:', e.message);
            e.preventDefault();
            return false;
        }
    });

    // ✅ FONCTION GLOBALE - Définie AVANT tout usage
    console.log('📍 Définition de window.showCourseCompleteModal');
    window.showCourseCompleteModal = function(certificateGenerated) {
        console.log('✅ showCourseCompleteModal appelée avec:', certificateGenerated);

        const courseCompleteModal = document.getElementById('course-complete-modal');
        const courseCompleteMessage = document.getElementById('course-complete-message');
        const courseCompleteCertificate = document.getElementById('course-complete-certificate');

        if (!courseCompleteModal) {
            console.error('❌ Modal #course-complete-modal introuvable!');
            return;
        }

        // Mettre à jour le message selon si le certificat a été généré
        if (certificateGenerated) {
            if (courseCompleteMessage) {
                courseCompleteMessage.textContent = '{{ translate("Vous avez terminé ce cours avec succès ! Votre certificat a été généré automatiquement.") }}';
            }
            if (courseCompleteCertificate) {
                courseCompleteCertificate.style.display = 'inline-block';
            }
        } else {
            if (courseCompleteMessage) {
                courseCompleteMessage.textContent = '{{ translate("Vous avez terminé ce cours avec succès !") }}';
            }
            if (courseCompleteCertificate) {
                courseCompleteCertificate.style.display = 'none';
            }
        }

        // Afficher le modal
        courseCompleteModal.style.display = 'flex';
        console.log('✅ Modal affiché');

        // Gestionnaire pour le bouton "Continuer"
        const courseCompleteClose = document.getElementById('course-complete-close');
        if (courseCompleteClose) {
            courseCompleteClose.onclick = function() {
                courseCompleteModal.style.display = 'none';
            };
        }

        // Gestionnaire pour le bouton "Voir le certificat"
        if (courseCompleteCertificate) {
            courseCompleteCertificate.onclick = function() {
                window.location.href = '{{ route("student.certificate.index") }}';
            };
        }

        // Fermer le modal en cliquant à l'extérieur
        courseCompleteModal.onclick = function(e) {
            if (e.target === courseCompleteModal) {
                courseCompleteModal.style.display = 'none';
            }
        };
    };

    console.log('✅ Fonction définie avec succès:', typeof window.showCourseCompleteModal);
</script>
<!-- DÉFINITION GLOBALE DE LA FONCTION (FIN) -->

@if ($type == 'video')
    @if ($data->video_src_type == 'youtube' || $data->video_src_type == 'vimeo')
        <div class="plyr__video-embed" id="player">
            <iframe src="{{ $data->video_url }}" allowfullscreen allowtransparency allow="autoplay"></iframe>
        </div>
        <script>
            console.log('🔧 [THEME-COURSE-LEARN] Script YouTube/Vimeo chargé');

            var videoPlayer = new Plyr("#player", {
                settings: ["speed"],
                seekTime: 0,
                ratio: "16:7",
                speed: {
                    selected: 1,
                    options: [0.5, 0.75, 1, 1.25, 1.5]
                },
            });

            console.log('🔧 [THEME-COURSE-LEARN] Plyr player initialisé:', videoPlayer);

            @if(auth()->check() && auth()->user()->guard === 'student')
            var isVideoStarted = false;
            var isVideoCompleted = false;

            // Fonction pour obtenir le topic ID
            function getTopicId() {
                console.log('🔍 [THEME-LEARN] Recherche du topic ID...');

                // Méthode 1: Chercher dans les données passées par le backend
                @if(isset($topic['topicId']))
                    console.log('✅ [THEME-LEARN] Topic ID trouvé depuis backend:', {{ $topic['topicId'] }});
                    return {{ $topic['topicId'] }};
                @endif

                // Méthode 2: Chercher dans l'URL parent (window.parent pour iframe)
                try {
                    const parentUrl = new URLSearchParams(window.parent.location.search);
                    const topicId = parentUrl.get('topic_id');
                    if (topicId) {
                        console.log('✅ [THEME-LEARN] Topic ID trouvé dans parent URL:', topicId);
                        return topicId;
                    }
                } catch(e) {
                    console.log('⚠️ [THEME-LEARN] Impossible d\'accéder à parent URL');
                }

                // Méthode 3: Chercher dans les attributs data du parent
                try {
                    const topicElement = window.parent.document.querySelector('[data-topic-id].active') ||
                                       window.parent.document.querySelector('[data-topic-id]');
                    if (topicElement) {
                        const id = topicElement.getAttribute('data-topic-id');
                        console.log('✅ [THEME-LEARN] Topic ID trouvé dans parent DOM:', id);
                        return id;
                    }
                } catch(e) {
                    console.log('⚠️ [THEME-LEARN] Impossible d\'accéder au parent DOM');
                }

                console.error('❌ [THEME-LEARN] Aucun topic ID trouvé!');
                return null;
            }

            // Détecter le clic sur play
            videoPlayer.on('play', function() {
                console.log('▶️ [THEME-LEARN] Event PLAY déclenché!');
                console.log('▶️ [THEME-LEARN] isVideoStarted:', isVideoStarted);
                if (!isVideoStarted) {
                    isVideoStarted = true;
                    console.log('▶️ [THEME-LEARN] Marquer comme commencé');

                    const topicId = getTopicId();
                    if (topicId) {
                        console.log('🚀 [THEME-LEARN] Envoi de la progression start pour topic:', topicId);
                        fetch(`{{ route('student.topic.start', '') }}/${topicId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': window.parent.document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log('✅ [THEME-LEARN] Réponse start:', data);
                        })
                        .catch(error => {
                            console.error('❌ [THEME-LEARN] Erreur start:', error);
                        });
                    } else {
                        console.error('❌ [THEME-LEARN] Impossible de marquer comme commencé: pas de topic ID');
                    }
                }
            });

            // Détecter la fin
            videoPlayer.on('ended', function() {
                console.log('🎬 [THEME-LEARN] Event ENDED déclenché!');
                if (!isVideoCompleted) {
                    isVideoCompleted = true;
                    console.log('🎬 [THEME-LEARN] Vidéo terminée');

                    const topicId = getTopicId();
                    if (topicId) {
                        console.log('🏁 [THEME-LEARN] Envoi de la progression complete pour topic:', topicId);
                        fetch(`{{ route('student.topic.complete', '') }}/${topicId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': window.parent.document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log('✅ [THEME-LEARN] Réponse complete:', data);

                            // Afficher un modal si le chapitre ou le cours est terminé
                            if (data.certificate_generated) {
                                console.log('🎓 [THEME-LEARN] Certificat généré!');

                                // Afficher le modal dans le parent
                                try {
                                    if (window.parent && typeof window.parent.showCourseCompleteModal === 'function') {
                                        window.parent.showCourseCompleteModal(true);
                                    } else {
                                        alert('🎓 Félicitations ! Vous avez obtenu votre certificat !');
                                    }
                                } catch(e) {
                                    console.error('Erreur lors de l\'affichage du modal:', e);
                                    alert('🎓 Félicitations ! Vous avez obtenu votre certificat !');
                                }
                            } else if (data.chapter_completed) {
                                console.log('📖 [THEME-LEARN] Chapitre terminé!');

                                // Afficher un message pour le chapitre terminé
                                try {
                                    if (window.parent && typeof window.parent.showLessonCompleteModal === 'function') {
                                        window.parent.showLessonCompleteModal({
                                            chapter_completed: true,
                                            is_last_topic_in_chapter: true
                                        });
                                    } else {
                                        alert('📖 Félicitations ! Chapitre terminé !');
                                    }
                                } catch(e) {
                                    console.error('Erreur lors de l\'affichage du modal:', e);
                                    alert('📖 Félicitations ! Chapitre terminé !');
                                }
                            } else {
                                console.log('✅ [THEME-LEARN] Leçon terminée!');

                                // Afficher un simple message pour la leçon terminée
                                try {
                                    if (window.parent && typeof window.parent.showLessonCompleteModal === 'function') {
                                        window.parent.showLessonCompleteModal({
                                            chapter_completed: false,
                                            is_last_topic_in_chapter: false
                                        });
                                    }
                                } catch(e) {
                                    console.log('Leçon terminée (pas de modal)');
                                }
                            }
                        })
                        .catch(error => {
                            console.error('❌ [THEME-LEARN] Erreur complete:', error);
                        });
                    } else {
                        console.error('❌ [THEME-LEARN] Impossible de marquer comme terminé: pas de topic ID');
                    }
                }
            });

            console.log('✅ [THEME-LEARN] Listeners installés');
            @endif
        </script>
    @elseif($data->video_src_type == 'local')
        @if (
            $data->system_video &&
                fileExists('lms/courses/topics/videos/', $data->system_video) == true &&
                $data->system_video != '')
            <video id="main-course-video" playsinline controls data-poster="assets/images/course/course-2.png">
                <source src="{{ asset('storage/lms/courses/topics/videos/' . $data->system_video) }}" type="video/mp4" />
            </video>
        @endif
        <script>
            var videoPlayer = new Plyr("#player", {
                settings: ["speed"],
                seekTime: 0,
                ratio: "16:7",
                speed: {
                    selected: 1,
                    options: [0.5, 0.75, 1, 1.25, 1.5]
                },
            });
        </script>
    @endif
@elseif($type == 'reading')
    <div class="p-5 md:p-8 xl:p-10 relative overflow-hidden aspect-[16/7] bg-primary-50 rounded-xl">
        <div class="size-full overflow-x-hidden overflow-y-auto">
            <h5 class="area-title text-xl">{{ $data->title }}</h5>
            <div class="area-description mt-5">
                {!! clean($data->description) !!}
            </div>
        </div>

        <!-- Bouton Marquer comme terminé pour les cours reading -->
        @if(auth()->check() && auth()->user()->guard === 'student')
            @php
                // Vérifier si la leçon est déjà terminée
                $topicProgress = null;
                if (auth()->check()) {
                    $topic = \Modules\LMS\Models\Courses\Topic::where('topicable_id', $data->id)
                        ->where('topicable_type', 'Modules\\LMS\\Models\\Courses\\Topics\\Reading')
                        ->first();

                    if ($topic) {
                        $topicProgress = \Modules\LMS\Models\TopicProgress::where('user_id', auth()->id())
                            ->where('topic_id', $topic->id)
                            ->first();
                    }
                }
                $isCompleted = $topicProgress && $topicProgress->isCompleted();
            @endphp

            <div class="absolute bottom-2 right-2 sm:bottom-4 sm:right-4">
                @if($isCompleted)
                    <!-- Icône de validation si terminée -->
                    <div class="flex items-center gap-1 sm:gap-2 text-green-600 bg-green-100 px-2 py-1 sm:px-3 sm:py-2 rounded-md text-xs sm:text-sm">
                        <i class="ri-check-double-line text-xs sm:text-sm"></i>
                        <span class="hidden xs:inline">{{ translate('Terminée') }}</span>
                        <span class="xs:hidden">{{ translate('✓') }}</span>
                    </div>
                @else
                    <!-- Bouton pour marquer comme terminé -->
                    <button id="mark-reading-complete"
                            class="btn b-solid btn-success-solid text-xs sm:text-sm px-2 py-1 sm:px-3 sm:py-2 flex items-center gap-1 sm:gap-2 whitespace-nowrap"
                            data-topic-id="{{ $topic['id'] }}"
                            data-topic-type="reading">
                        <i class="ri-check-line text-xs sm:text-sm"></i>
                        <span class="hidden xs:inline">{{ translate('Marquer comme terminé') }}</span>
                        <span class="xs:hidden">{{ translate('Marquer') }}</span>
                    </button>
                @endif
            </div>
        @endif
    </div>

    <!-- Modal de confirmation pour les cours reading -->
    @if(auth()->check() && auth()->user()->guard === 'student')
        <div id="reading-complete-modal" style="
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 9999;
                display: none;
                justify-content: center;
                align-items: center;
                padding: 20px;
                box-sizing: border-box;
            ">
            <div style="
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
                    max-width: 400px;
                    width: 100%;
                    padding: 30px;
                    text-align: center;
                    position: relative;
                ">
                <div style="
                        width: 60px;
                        height: 60px;
                        background-color: #10B981;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin: 0 auto 20px;
                    ">
                    <i class="ri-check-line" style="color: white; font-size: 24px;"></i>
                </div>

                <h3 id="modal-title" style="
                        font-size: 20px;
                        font-weight: 600;
                        color: #1F2937;
                        margin: 0 0 10px 0;
                    ">
                    {{ translate('Leçon terminée !') }}
                </h3>

                <p id="modal-message" style="
                        font-size: 14px;
                        color: #6B7280;
                        margin: 0 0 25px 0;
                        line-height: 1.5;
                    ">
                    {{ translate('Vous avez terminé cette leçon. Votre progression a été enregistrée.') }}
                </p>

                <button id="modal-close" style="
                        background-color: #572571;
                        color: white;
                        border: none;
                        padding: 10px 20px;
                        border-radius: 6px;
                        font-size: 14px;
                        font-weight: 500;
                        cursor: pointer;
                        transition: background-color 0.2s;
                    " onmouseover="this.style.backgroundColor='#572571'" onmouseout="this.style.backgroundColor='#572571'">
                    {{ translate('Continuer') }}
                </button>
            </div>
        </div>

        <!-- ✅ Modal course-complete-modal créé globalement au début -->

        <script>
            // Attendre que le DOM soit complètement chargé
            function initReadingProgress() {
                const markButton = document.getElementById('mark-reading-complete');
                const modal = document.getElementById('reading-complete-modal');
                const modalTitle = document.getElementById('modal-title');
                const modalMessage = document.getElementById('modal-message');
                const modalClose = document.getElementById('modal-close');

                if (!markButton || !modal) {
                    return;
                }

                // Gestionnaire de clic sur le bouton
                markButton.onclick = function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const topicId = this.getAttribute('data-topic-id');
                    const topicType = this.getAttribute('data-topic-type');

                    if (!topicId) {
                        toastr.error('Erreur: ID du topic non trouvé');
                        return;
                    }

                    // Token CSRF
                    const csrfToken = '{{ csrf_token() }}';

                    // Désactiver le bouton temporairement
                    this.disabled = true;
                    this.innerHTML = '<i class="ri-loader-4-line animate-spin text-xs sm:text-sm"></i><span class="hidden xs:inline">{{ translate("Enregistrement...") }}</span><span class="xs:hidden">{{ translate("...") }}</span>';

                    // Envoyer la requête
                    const url = '{{ route("student.topic.mark-completed") }}';
                    console.log('🔗 URL:', url);
                    console.log('📋 Data:', {topic_id: topicId, topic_type: topicType});

                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            topic_id: topicId,
                            topic_type: topicType
                        })
                    })
                        .then(response => {
                            console.log('✅ Response status:', response.status);

                            if (!response.ok) {
                                console.error('❌ HTTP Error:', response.status, response.statusText);
                                throw new Error('Erreur HTTP: ' + response.status);
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('📦 Response data:', data);

                            if (data.status === 'success') {
                                // Afficher un toast de succès
                                // toastr.success('Leçon marquée comme terminée avec succès !');

                                // Mettre à jour le modal
                                if (data.is_last_topic_in_chapter) {
                                    modalTitle.textContent = '{{ translate("Chapitre terminé !") }}';
                                    modalMessage.textContent = '{{ translate("Félicitations ! Vous avez terminé ce chapitre. Votre progression a été enregistrée.") }}';
                                } else {
                                    modalTitle.textContent = '{{ translate("Leçon terminée !") }}';
                                    modalMessage.textContent = '{{ translate("Vous avez terminé cette leçon. Votre progression a été enregistrée.") }}';
                                }

                                // Afficher le modal
                                modal.style.display = 'flex';

                                // Vérifier si le cours est complètement terminé
                                if (data.course_completed || data.certificate_generated) {
                                    // Fermer le modal de leçon d'abord
                                    modal.style.display = 'none';
                                    // Afficher le modal de completion du cours
                                    console.log('🎯 Appel de showCourseCompleteModal...');
                                    console.log('✅ Fonction existe?', typeof window.showCourseCompleteModal);
                                    setTimeout(() => {
                                        if (typeof window.showCourseCompleteModal === 'function') {
                                            window.showCourseCompleteModal(data.certificate_generated);
                                        } else {
                                            console.error('❌ showCourseCompleteModal n\'est pas une fonction!');
                                        }
                                    }, 500);
                                } else {
                                    // Message simple de confirmation pour la leçon
                                    modalMessage.textContent = '{{ translate("Vous avez terminé cette leçon. Votre progression a été enregistrée.") }}';
                                    modalClose.textContent = '{{ translate("Continuer") }}';
                                }

                                // Remplacer le bouton par l'icône de validation
                                const buttonContainer = markButton.parentElement;
                                buttonContainer.innerHTML = `
                            <div class="flex items-center gap-1 sm:gap-2 text-green-600 bg-green-100 px-2 py-1 sm:px-3 sm:py-2 rounded-md text-xs sm:text-sm">
                                <i class="ri-check-double-line text-xs sm:text-sm"></i>
                                <span class="hidden xs:inline">{{ translate("Terminée") }}</span>
                                <span class="xs:hidden">{{ translate("✓") }}</span>
                            </div>
                        `;
                            } else {
                                throw new Error(data.message || 'Erreur inconnue');
                            }
                        })
                        .catch(error => {
                            console.error('❌ Erreur complète:', error);
                            console.error('📍 Stack trace:', error.stack);

                            if (error.message.includes('JSON')) {
                                toastr.error('Erreur de format de réponse du serveur. Vérifiez la console pour plus de détails.');
                            } else {
                                toastr.error('Erreur: ' + error.message);
                            }

                            // Réactiver le bouton en cas d'erreur
                            markButton.disabled = false;
                            markButton.innerHTML = '<i class="ri-check-line text-xs sm:text-sm"></i><span class="hidden xs:inline">{{ translate("Marquer comme terminé") }}</span><span class="xs:hidden">{{ translate("Marquer") }}</span>';
                        });
                };

                // Gestionnaire de fermeture du modal
                if (modalClose) {
                    modalClose.onclick = function() {
                        // Fermer le modal
                        modal.style.display = 'none';
                    };
                }

                // Fermer le modal en cliquant à l'extérieur
                modal.onclick = function(e) {
                    if (e.target === modal) {
                        modal.style.display = 'none';
                    }
                };
            }

            // Initialiser quand le DOM est prêt
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initReadingProgress);
            } else {
                initReadingProgress();
            }
        </script>
    @endif
@elseif($type == 'assignment')
    <div class="p-5 md:p-8 xl:p-10 relative overflow-hidden aspect-[16/7] bg-primary-50 rounded-xl">
        <div class="size-full flex-center">
            <a href="{{ route('exam.start', ['type' => $type, 'exam_type_id' => $data->id, 'course_id' => $data?->topic?->course_id]) }}" aria-label="Go to Assignment"
               class="btn b-solid btn-primary-solid">
                {{ translate('Go to Assignment') }}
            </a>
        </div>
    </div>
@elseif($type == 'quiz')
    <div class="p-5 md:p-8 xl:p-10 relative overflow-hidden aspect-[16/7] bg-primary-50 rounded-xl">
        <div class="size-full flex-center flex-col gap-4">
            <div class="text-center">
                <h5 class="area-title text-xl mb-2">{{ $data->title }}</h5>
                <p class="text-gray-600 mb-4">{{ translate('Testez vos connaissances avec ce quiz') }}</p>
            </div>

            <a href="{{ route('exam.start', ['type' => $type, 'exam_type_id' => $data->id, 'course_id' => $topic['courseId'] ?? request('course_id'), 'topic_id' => $topic['topicId'] ?? request('topic_id'), 'chapterId' => $topic['chapterId'] ?? request('chapter_id')]) }}" aria-label="Go to Quiz"
               class="btn b-solid btn-primary-solid">
                {{ translate('Commencer le Quiz') }}
            </a>

            <!-- Bouton Marquer comme terminé pour les quiz -->
            @if(auth()->check() && auth()->user()->guard === 'student')
                @php
                    // Trouver le topic correspondant au quiz
                    $quizTopic = \Modules\LMS\Models\Courses\Topic::where('topicable_id', $data->id)
                        ->where('topicable_type', 'Modules\\LMS\\Models\\Courses\\Topics\\Quiz')
                        ->first();

                    // Vérifier si le quiz a été soumis (score >= pass_mark)
                    $quizSubmitted = false;
                    $quizPassed = false;
                    $userQuiz = null;
                    $topicAlreadyCompleted = false;

                    if (auth()->check()) {
                        $userQuiz = \Modules\LMS\Models\Auth\UserCourseExam::where('user_id', auth()->id())
                            ->where('quiz_id', $data->id)
                            ->whereNotNull('score')
                            ->first();

                        if ($userQuiz) {
                            $quizSubmitted = true;
                            $quizPassed = $userQuiz->score >= $data->pass_mark;
                        }

                        // Vérifier si le topic est déjà marqué comme terminé
                        if ($quizTopic) {
                            $topicProgress = \Modules\LMS\Models\TopicProgress::where('user_id', auth()->id())
                                ->where('topic_id', $quizTopic->id)
                                ->where('status', 'completed')
                                ->first();

                            $topicAlreadyCompleted = $topicProgress !== null;

                            // Debug logs pour vérifier le statut
                            \Log::info('Topic Completion Check', [
                                'user_id' => auth()->id(),
                                'topic_id' => $quizTopic->id,
                                'topic_progress_found' => $topicProgress ? true : false,
                                'topic_progress_status' => $topicProgress ? $topicProgress->status : null,
                                'topic_already_completed' => $topicAlreadyCompleted
                            ]);
                        }

                        // Debug logs
                        \Log::info('Quiz Debug Info', [
                            'user_id' => auth()->id(),
                            'quiz_id' => $data->id,
                            'quiz_topic_id' => $quizTopic ? $quizTopic->id : null,
                            'quiz_pass_mark' => $data->pass_mark,
                            'userQuiz_found' => $userQuiz ? true : false,
                            'userQuiz_score' => $userQuiz ? $userQuiz->score : null,
                            'quizSubmitted' => $quizSubmitted,
                            'quizPassed' => $quizPassed
                        ]);
                    }
                @endphp

                <div class="mt-4">
                    @if($topicAlreadyCompleted)
                        <!-- Topic déjà terminé - Afficher le statut de completion -->
                        <div class="flex items-center gap-2 text-green-600 bg-green-100 px-4 py-2 rounded-md">
                            <i class="ri-check-double-line"></i>
                            <span>{{ translate('Quiz terminé avec succès') }}</span>
                        </div>
                    @elseif($quizSubmitted && $quizPassed)
                        <!-- Quiz soumis et réussi - Afficher le bouton pour marquer comme terminé -->
                        <button id="mark-quiz-complete"
                                class="btn b-solid btn-success-solid flex items-center gap-2"
                                data-topic-id="{{ $quizTopic ? $quizTopic->id : request('topic_id') }}"
                                data-topic-type="quiz">
                            <i class="ri-check-line"></i>
                            {{ translate('Marquer comme terminé') }}
                        </button>
                    @elseif($quizSubmitted && !$quizPassed)
                        <!-- Quiz soumis mais échoué -->
                        <div class="flex items-center gap-2 text-red-600 bg-red-100 px-4 py-2 rounded-md">
                            <i class="ri-close-line"></i>
                            <span>{{ translate('Quiz échoué - Score: ') }}{{ $userQuiz->score ?? 0 }}/{{ $data->total_mark ?? 100 }}</span>
                        </div>
                    @else
                        <!-- Quiz pas encore soumis - Cacher le bouton -->
                        <div class="flex items-center gap-2 text-gray-500 bg-gray-100 px-4 py-2 rounded-md">
                            <i class="ri-lock-line"></i>
                            <span>{{ translate('Soumettez d\'abord le quiz pour le marquer comme terminé') }}</span>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Modal de confirmation pour les quiz -->
        @if(auth()->check() && auth()->user()->guard === 'student')
            <div id="quiz-complete-modal" style="
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 9999;
                display: none;
                justify-content: center;
                align-items: center;
                padding: 20px;
                box-sizing: border-box;
            ">
                <div style="
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
                    max-width: 400px;
                    width: 100%;
                    padding: 30px;
                    text-align: center;
                    position: relative;
                ">
                    <div style="
                        width: 60px;
                        height: 60px;
                        background-color: #10B981;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        margin: 0 auto 20px;
                    ">
                        <i class="ri-check-line" style="color: white; font-size: 24px;"></i>
                    </div>

                    <h3 id="quiz-modal-title" style="
                        font-size: 20px;
                        font-weight: 600;
                        color: #1F2937;
                        margin: 0 0 10px 0;
                    ">
                        {{ translate('Quiz terminé !') }}
                    </h3>

                    <p id="quiz-modal-message" style="
                        font-size: 14px;
                        color: #6B7280;
                        margin: 0 0 25px 0;
                        line-height: 1.5;
                    ">
                        {{ translate('Vous avez terminé ce quiz. Votre progression a été enregistrée.') }}
                    </p>

                    <button id="quiz-modal-close" style="
                        background-color: #572571;
                        color: white;
                        border: none;
                        padding: 10px 20px;
                        border-radius: 6px;
                        font-size: 14px;
                        font-weight: 500;
                        cursor: pointer;
                        transition: background-color 0.2s;
                    " onmouseover="this.style.backgroundColor='#572571'" onmouseout="this.style.backgroundColor='#572571'">
                        {{ translate('Continuer') }}
                    </button>
                </div>
            </div>

            <script>
                // ✅ Attendre que le DOM soit complètement chargé
                function initQuizProgress() {
                    const markButton = document.getElementById('mark-quiz-complete');
                    const modal = document.getElementById('quiz-complete-modal');
                    const modalTitle = document.getElementById('quiz-modal-title');
                    const modalMessage = document.getElementById('quiz-modal-message');
                    const modalClose = document.getElementById('quiz-modal-close');

                    if (!markButton || !modal) {
                        console.log('⚠️ Quiz elements not found');
                        return;
                    }

                    // Gestionnaire de clic sur le bouton
                    markButton.onclick = function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        const topicId = this.getAttribute('data-topic-id');
                        const topicType = this.getAttribute('data-topic-type');

                        if (!topicId) {
                            toastr.error('Erreur: ID du topic non trouvé');
                            return;
                        }

                        // Token CSRF
                        const csrfToken = '{{ csrf_token() }}';

                        // Désactiver le bouton temporairement
                        this.disabled = true;
                        this.innerHTML = '<i class="ri-loader-4-line animate-spin"></i> {{ translate("Enregistrement...") }}';

                        // Envoyer la requête
                        const url = '{{ route("student.topic.mark-completed") }}';
                        console.log('🔗 Quiz URL:', url);
                        console.log('📋 Quiz Data:', {topic_id: topicId, topic_type: topicType});

                        fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                topic_id: topicId,
                                topic_type: topicType
                            })
                        })
                            .then(response => {
                                console.log('✅ Quiz Response status:', response.status);

                                if (!response.ok) {
                                    throw new Error('Erreur HTTP: ' + response.status);
                                }
                                return response.json();
                            })
                            .then(data => {
                                console.log('📦 Quiz Response data:', data);

                                if (data.status === 'success') {
                                    // Vérifier si le cours est complètement terminé
                                    if (data.course_completed || data.certificate_generated) {
                                        console.log('🎯 Quiz - Appel de showCourseCompleteModal...');
                                        console.log('✅ Fonction existe?', typeof window.showCourseCompleteModal);

                                        // Afficher le modal de completion du cours
                                        if (typeof window.showCourseCompleteModal === 'function') {
                                            window.showCourseCompleteModal(data.certificate_generated);
                                        } else {
                                            console.error('❌ showCourseCompleteModal n\'est pas une fonction!');
                                        }
                                    } else {
                                        // Mettre à jour le modal normal
                                        if (data.is_last_topic_in_chapter) {
                                            modalTitle.textContent = '{{ translate("Chapitre terminé !") }}';
                                            modalMessage.textContent = '{{ translate("Félicitations ! Vous avez terminé ce chapitre. Votre progression a été enregistrée.") }}';
                                        } else {
                                            modalTitle.textContent = '{{ translate("Quiz terminé !") }}';
                                            modalMessage.textContent = '{{ translate("Vous avez terminé ce quiz. Votre progression a été enregistrée.") }}';
                                        }

                                        // Afficher le modal
                                        modal.style.display = 'flex';
                                    }

                                    // Remplacer le bouton par l'icône de validation
                                    const buttonContainer = markButton.parentElement;
                                    buttonContainer.innerHTML = `
                                <div class="flex items-center gap-2 text-green-600 bg-green-100 px-4 py-2 rounded-md">
                                    <i class="ri-check-double-line"></i>
                                    <span>{{ translate("Quiz terminé") }}</span>
                                </div>`;

                                    // Rafraîchir la page après 2 secondes
                                    setTimeout(() => {
                                        window.location.reload();
                                    }, 2000);
                                } else {
                                    throw new Error(data.message || 'Erreur inconnue');
                                }
                            })
                            .catch(error => {
                                console.error('❌ Erreur: ' + error.message);

                                // Réactiver le bouton en cas d'erreur
                                markButton.disabled = false;
                                markButton.innerHTML = '<i class="ri-check-line"></i> {{ translate("Marquer comme terminé") }}';
                            });
                    };

                    // Gestionnaire de fermeture du modal
                    if (modalClose) {
                        modalClose.onclick = function() {
                            modal.style.display = 'none';
                            // Rafraîchir la page après fermeture du modal
                            window.location.reload();
                        };
                    }

                    // Fermer le modal en cliquant à l'extérieur
                    modal.onclick = function(e) {
                        if (e.target === modal) {
                            modal.style.display = 'none';
                            // Rafraîchir la page après fermeture du modal
                            window.location.reload();
                        }
                    };
                }

                // Initialiser quand le DOM est prêt
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', initQuizProgress);
                } else {
                    initQuizProgress();
                }
            </script>
        @endif
    </div>
@endif
