@php
    $data = $topic['data'];
@endphp

@if ($type == 'video')
    @if ($data->video_src_type == 'youtube' || $data->video_src_type == 'vimeo')
        <div class="plyr__video-embed" id="player">
            <iframe src="{{ $data->video_url }}" allowfullscreen allowtransparency allow="autoplay"></iframe>
        </div>
        <script>
            new Plyr("#player", {
                settings: ["speed"],
                seekTime: 0,
                ratio: "16:7",
                speed: {
                    selected: 1,
                    options: [0.5, 0.75, 1, 1.25, 1.5]
                },
            });
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
            const player = new Plyr("#player", {
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
                        toastr.success('Leçon marquée comme terminée avec succès !');
                        
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
                            setTimeout(() => {
                                showCourseCompleteModal(data.certificate_generated);
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

        // Fonction pour afficher le modal de completion du cours
        function showCourseCompleteModal(certificateGenerated) {
            const courseCompleteModal = document.getElementById('course-complete-modal');
            const courseCompleteMessage = document.getElementById('course-complete-message');
            const courseCompleteCertificate = document.getElementById('course-complete-certificate');
            
            if (!courseCompleteModal) {
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
                    // Vérifier si le quiz est déjà terminé
                    $topicProgress = null;
                    if (auth()->check()) {
                        $topic = \Modules\LMS\Models\Courses\Topic::where('topicable_id', $data->id)
                            ->where('topicable_type', 'Modules\\LMS\\Models\\Courses\\Topics\\Quiz')
                            ->first();
                        
                        if ($topic) {
                            $topicProgress = \Modules\LMS\Models\TopicProgress::where('user_id', auth()->id())
                                ->where('topic_id', $topic->id)
                                ->first();
                        }
                    }
                    $isCompleted = $topicProgress && $topicProgress->isCompleted();
                @endphp
                
                <div class="mt-4">
                    @if($isCompleted)
                        <!-- Icône de validation si terminé -->
                        <div class="flex items-center gap-2 text-green-600 bg-green-100 px-4 py-2 rounded-md">
                            <i class="ri-check-double-line"></i>
                            <span>{{ translate('Quiz terminé') }}</span>
                        </div>
                    @else
                        <!-- Bouton pour marquer comme terminé -->
                        <button id="mark-quiz-complete" 
                                class="btn b-solid btn-success-solid flex items-center gap-2"
                                data-topic-id="{{ $topic['id'] }}"
                                data-topic-type="quiz">
                            <i class="ri-check-line"></i>
                            {{ translate('Marquer comme terminé') }}
                        </button>
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
                                showCourseCompleteModal(data.certificate_generated);
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
                        alert('Erreur: ' + error.message);
                        
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

