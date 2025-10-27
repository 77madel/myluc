@php
    $startTopicId = $start_topic_id ?? null;
    $auth = $auth ?? '';
    $purchaseCheck = $purchaseCheck ?? '';

    // Utiliser le contenu (topic) au lieu du vrai Topic pour la compatibilité
    $realTopic = $chapterTopic ?? null;
    $realTopicId = $topic->id; // Utiliser l'ID du topic (111) pour la progression

    if ($auth && $purchaseCheck !== false) {
        $route = route('play.course', [
            'slug' => $course->slug,
            'topic_id' => $realTopicId,
            'type' => $topic->topic_type?->slug,
            'chapter_id' => $chapterId ?? null,
        ]);
    } else {
        $route = '#';
    }

    // Récupérer la progression de la leçon pour l'utilisateur connecté
    $topicProgress = null;
    $canAccess = true;
    $chapterCompleted = false;

    if (auth()->check() && auth()->user()->guard === 'student') {
        $topicProgress = \Modules\LMS\Models\TopicProgress::where('user_id', auth()->id())
            ->where('topic_id', $realTopicId)
            ->first();

        // Vérifier si la leçon peut être accessible
        $canAccess = \Modules\LMS\Models\TopicProgress::canAccessTopic(auth()->id(), $realTopicId);

        // Vérifier si le chapitre parent est terminé
        if ($chapterId) {
            $chapterProgress = \Modules\LMS\Models\ChapterProgress::where('user_id', auth()->id())
                ->where('chapter_id', $chapterId)
                ->where('status', 'completed')
                ->first();
            $chapterCompleted = $chapterProgress !== null;
        }
    }
@endphp

<div class="border-t border-border hover:bg-slate-200 relative {{ $startTopicId == $realTopicId ? 'active' : '' }} topic-item" data-topic-id="{{ $realTopicId }}">
    <div class="flex items-center justify-between px-3 py-4">
        <div class="flex-1">
            <a href="{{ $sideBarShow == 'video-play' ? '#' : $route }}"
                class="flex flex-col gap-2 leading-none cursor-pointer {{ $sideBarShow == 'video-play' ? 'video-lesson-item' : '' }}"
                aria-label="{{ $topic->title }}"
                data-type="{{ $sideBarShow == 'video-play' ? $topic->topic_type?->slug : '' }}"
                data-id="{{ $sideBarShow == 'video-play' ? $realTopicId : '' }}"
                data-action="{{ $sideBarShow == 'video-play' ? route('learn.course.topic') . '?course_id=' . $course->id . '&chapter_id=' . $realTopic?->chapter_id . '&topic_id=' . $realTopicId : '' }}"
                data-topic-id="{{ $realTopicId }}">

                <div class="flex items-center gap-2">
                    <!-- Icône de progression -->
                    @if(auth()->check() && auth()->user()->guard === 'student')
                        @php
                            try {
                                $topicProgress = \Modules\LMS\Models\TopicProgress::where('user_id', auth()->id())
                                    ->where('topic_id', $realTopicId)
                                    ->first();

                                    // Vérifier le statut de progression

                            } catch (\Exception $e) {
                                $topicProgress = null;
                                \Log::error("Erreur lors de la récupération du topic progress: " . $e->getMessage());
                            }
                        @endphp

                        @if($chapterCompleted)
                            {{-- Icône de validation pour chapitre terminé --}}
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full"
                                  style="background-color: #10B981; color: white;"
                                  title="{{ translate('Chapitre terminé - Toutes les leçons validées') }}">
                                <i class="ri-check-double-line"></i>
                            </span>
                        @elseif($topicProgress && $topicProgress->status === 'completed')
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full"
                                  style="background-color: #66CC33; color: white;"
                                  title="{{ translate('Leçon terminée') }}">
                                <i class="ri-check-line"></i>
                            </span>
                        @elseif($topicProgress && $topicProgress->status === 'in_progress')
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full"
                                  style="background-color: #FFA305; color: white;"
                                  title="{{ translate('Leçon en cours') }}">
                                <i class="ri-play-line"></i>
                            </span>
                        @else
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full"
                                  style="background-color: #999; color: white;"
                                  title="{{ translate('Leçon non commencée') }}">
                                <i class="ri-play-line"></i>
                            </span>
                        @endif
                    @endif

                    <!-- Titre de la leçon -->
                    <h6 class="text-sm font-normal">
                        {{ $key + 1 }}.
                        {{ $topic->title }} ({{ $topic?->topic_type?->slug }})
                    </h6>
                </div>

                @if ($topic->duration)
                    <div class="flex items-center gap-1 ml-4 text-xs text-slate-900">
                        {!! $icon ?? '' !!}
                        {{ $topic->duration }}{{ translate('min') }}
                    </div>
                @endif
            </a>
        </div>
    </div>

    @if (!$auth || ($auth && $purchaseCheck == false))
        <span class="absolute top-1/2 -translate-y-1/2 right-2 rtl:right-auto rtl:left-2 text-heading">
            <i class="ri-lock-line"></i>
        </span>
    @endif
</div>

@if(auth()->check() && auth()->user()->guard === 'student')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Éviter les initialisations multiples
    if (window.autoProgressInitialized) {
        return;
    }
    window.autoProgressInitialized = true;

    console.log('🚀 Système de progression automatique initialisé');

    // Variables globales
    let isStarted = false;
    let isCompleted = false;
    let currentTopicId = null;
    let safetyTimer = null;

    // Détecter le clic sur une leçon vidéo
    document.addEventListener('click', function(e) {
        console.log('🖱️ Clic détecté sur:', e.target);

        // Chercher dans plusieurs sélecteurs possibles
        const videoLink = e.target.closest('.video-lesson-item') ||
                          e.target.closest('[data-type="video"]') ||
                          e.target.closest('[data-id]') ||
                          e.target.closest('.topic-item') ||
                          e.target.closest('.plyr') ||
                          e.target.closest('.plyr__poster');

        if (videoLink) {
            const topicId = videoLink.getAttribute('data-id') ||
                           videoLink.getAttribute('data-topic-id');
            const type = videoLink.getAttribute('data-type');

            console.log('🔍 Lien trouvé:', {
                element: videoLink,
                topicId: topicId,
                type: type,
                classes: videoLink.className
            });

            if (topicId) {
                currentTopicId = topicId;
                console.log('🎯 Leçon sélectionnée:', topicId, 'Type:', type);

                // Réinitialiser les flags
                isStarted = false;
                isCompleted = false;

                // Démarrer la surveillance de la vidéo après un délai
                setTimeout(() => {
                    startVideoMonitoring();
                }, 2000);
            } else {
                console.log('❌ Aucun topic ID trouvé dans le lien');
            }
        } else {
            console.log('❌ Aucun lien vidéo trouvé');
        }
    });

    // Détecter le début de lecture (play) - amélioré
    document.addEventListener('play', function(e) {
        console.log('▶️ Événement play détecté sur:', e.target);

        // Si pas de topic ID, essayer de le trouver automatiquement
        if (!currentTopicId) {
            console.log('🔍 Pas de topic ID, recherche automatique...');
            findAndSetTopicId();
        }

        if (!isStarted && currentTopicId) {
            isStarted = true;
            console.log('🚀 Marquer comme commencé:', currentTopicId);
            markAsStarted(currentTopicId);
        } else if (!currentTopicId) {
            console.log('❌ Impossible de marquer comme commencé - pas de topic ID');
        }
    });

    // Détecter le clic sur la vidéo (poster, play button, etc.)
    document.addEventListener('click', function(e) {
        // Si c'est un clic sur un élément vidéo (poster, play button, etc.)
        if (e.target.closest('.plyr') || e.target.closest('.plyr__poster') || e.target.closest('.plyr__control')) {
            console.log('🎬 Clic sur la vidéo détecté');

            // Si pas de topic ID, essayer de le trouver automatiquement
            if (!currentTopicId) {
                console.log('🔍 Pas de topic ID, recherche automatique...');
                findAndSetTopicId();
            }

            // Démarrer la surveillance de la vidéo
            setTimeout(() => {
                startVideoMonitoring();
            }, 1000);
        }
    });

    // Détecter la fin de vidéo - amélioré
    document.addEventListener('ended', function(e) {
        console.log('🏁 Événement ended détecté sur:', e.target);

        if (!isCompleted && currentTopicId) {
            isCompleted = true;
            console.log('🏁 Marquer comme terminé:', currentTopicId);
            markAsCompleted(currentTopicId);
        }
    });

    function startVideoMonitoring() {
        console.log('🔍 Début de la surveillance vidéo...');

        // Chercher les éléments vidéo
        const videoElements = document.querySelectorAll('video, iframe[src*="youtube"], iframe[src*="vimeo"]');
        console.log('📹 Éléments vidéo trouvés:', videoElements.length);

        if (videoElements.length > 0) {
            // Pour les vidéos HTML5
            videoElements.forEach(video => {
                if (video.tagName === 'VIDEO') {
                    video.addEventListener('play', handleVideoPlay);
                    video.addEventListener('ended', handleVideoEnd);
                    video.addEventListener('timeupdate', handleVideoProgress);
                }
            });

            // Pour YouTube/Vimeo - utiliser un timer de sécurité
            startSafetyTimer();
        }
    }

    function handleVideoPlay() {
        console.log('▶️ Vidéo en cours de lecture');
        if (!isStarted && currentTopicId) {
            isStarted = true;
            markAsStarted(currentTopicId);
        }
    }

    function handleVideoEnd() {
        console.log('🏁 Fin de vidéo détectée');
        if (!isCompleted && currentTopicId) {
            isCompleted = true;
            markAsCompleted(currentTopicId);
        }
    }

    function handleVideoProgress(e) {
        if (e.target.duration) {
            const progress = (e.target.currentTime / e.target.duration) * 100;
            if (progress >= 95 && !isCompleted && currentTopicId) {
                console.log('📊 Vidéo à 95% - Marquer comme terminée');
                isCompleted = true;
                markAsCompleted(currentTopicId);
            }
        }
    }

    function startSafetyTimer() {
        if (safetyTimer) clearTimeout(safetyTimer);

        safetyTimer = setTimeout(() => {
            if (isStarted && !isCompleted && currentTopicId) {
                console.log('⏰ Timer de sécurité - Marquer comme terminée');
                isCompleted = true;
                markAsCompleted(currentTopicId);
            }
        }, 30000); // 30 secondes
    }

    function markAsStarted(topicId) {
        console.log('🚀 Marquer comme commencé:', topicId);

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
                console.log('✅ Leçon marquée comme commencée');
            }
        })
        .catch(error => {
            console.error('❌ Erreur lors du démarrage:', error);
        });
    }

    function markAsCompleted(topicId) {
        console.log('🏁 Marquer comme terminé:', topicId);

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
                console.log('✅ Leçon marquée comme terminée');

                // Vérifier si un certificat a été généré
                if (data.certificate_generated) {
                    console.log('🎓 Certificat généré automatiquement !');
                    showCertificateModal();
                } else {
                    showModal(data);
                }

                // Mettre à jour les icônes de progression après un délai
                setTimeout(() => {
                    updateProgressIcons();
                }, 2000);
            }
        })
        .catch(error => {
            console.error('❌ Erreur lors de la finalisation:', error);
        });
    }

    function showModal(data) {
        // Supprimer l'ancien modal
        const existingModal = document.getElementById('progress-modal');
        if (existingModal) {
            existingModal.remove();
        }

        // Vérifier si c'est la dernière leçon du chapitre
        const isLastLessonInChapter = checkIfLastLessonInChapter();

        let modalHtml;

        if (isLastLessonInChapter) {
            // Modal pour chapitre terminé
            modalHtml = `
                <div id="progress-modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999;">
                    <div style="background: white; padding: 2rem; border-radius: 12px; max-width: 500px; width: 90%; text-align: center; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
                        <div style="font-size: 4rem; margin-bottom: 1rem;">🎉</div>
                        <h2 style="margin: 0 0 1rem 0; color: #1f2937; font-size: 1.5rem; font-weight: 600;">
                            Bravo ! Chapitre terminé !
                        </h2>
                        <p style="margin: 0 0 2rem 0; color: #6b7280; font-size: 1rem;">
                            Vous avez terminé toutes les leçons de ce chapitre avec succès !
                        </p>
                        <div style="display: flex; gap: 1rem; justify-content: center;">
                            <button onclick="closeModalAndMarkChapterCompleted()" style="padding: 0.75rem 1.5rem; background: #10b981; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 500;">
                                Fermer
                            </button>
                        </div>
                    </div>
                </div>
            `;
        } else {
            // Modal pour leçon terminée
            modalHtml = `
                <div id="progress-modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999;">
                    <div style="background: white; padding: 2rem; border-radius: 12px; max-width: 500px; width: 90%; text-align: center; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
                        <div style="font-size: 4rem; margin-bottom: 1rem;">🎉</div>
                        <h2 style="margin: 0 0 1rem 0; color: #1f2937; font-size: 1.5rem; font-weight: 600;">
                            Félicitations !
                        </h2>
                        <p style="margin: 0 0 2rem 0; color: #6b7280; font-size: 1rem;">
                            Vous avez terminé cette leçon avec succès !
                        </p>
                        <div style="display: flex; gap: 1rem; justify-content: center;">
                            <button onclick="closeModal()" style="padding: 0.75rem 1.5rem; border: 1px solid #d1d5db; background: white; color: #374151; border-radius: 8px; cursor: pointer; font-weight: 500;">
                                Fermer
                            </button>
                            <button onclick="navigateToNextLesson()" style="padding: 0.75rem 1.5rem; background: #3b82f6; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 500;">
                                Continuer
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }

        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    // Fonction pour vérifier si c'est la dernière leçon du chapitre
    function checkIfLastLessonInChapter() {
        console.log('🔍 Vérification si c\'est la dernière leçon du chapitre...');

        // Trouver l'élément actuel
        const currentElement = document.querySelector('.topic-item.active') ||
                              document.querySelector(`[data-topic-id="${currentTopicId}"]`)?.closest('.topic-item');

        if (!currentElement) {
            console.log('❌ Élément actuel non trouvé');
            return false;
        }

        // Vérifier s'il y a un élément suivant dans le même chapitre
        const nextElement = currentElement.nextElementSibling;
        const hasNextLesson = nextElement && nextElement.classList.contains('topic-item');

        console.log('📋 Élément suivant trouvé:', hasNextLesson);

        return !hasNextLesson; // True si c'est la dernière leçon
    }

    // Fonction pour fermer le modal et marquer le chapitre comme terminé
    window.closeModalAndMarkChapterCompleted = function() {
        console.log('🎯 Fermer le modal et marquer le chapitre comme terminé...');

        // Fermer le modal
        const modal = document.getElementById('progress-modal');
        if (modal) {
            modal.remove();
        }

        // Marquer le chapitre comme terminé
        markChapterAsCompleted();
    };

    // Fonction pour marquer le chapitre comme terminé
    function markChapterAsCompleted() {
        console.log('📖 Marquer le chapitre comme terminé...');

        // Récupérer l'ID du chapitre depuis l'élément actuel
        const currentElement = document.querySelector('.topic-item.active') ||
                              document.querySelector(`[data-topic-id="${currentTopicId}"]`)?.closest('.topic-item');

        if (!currentElement) {
            console.log('❌ Impossible de trouver l\'élément actuel');
            return;
        }

        // Extraire l'ID du chapitre (vous devrez adapter selon votre structure)
        const chapterId = getChapterIdFromCurrentElement();

        if (!chapterId) {
            console.log('❌ Impossible de trouver l\'ID du chapitre');
            return;
        }

        // Envoyer la requête pour marquer le chapitre comme terminé
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
                console.log('✅ Chapitre marqué comme terminé');
                // Recharger la page pour mettre à jour les icônes
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }
        })
        .catch(error => {
            console.error('❌ Erreur lors de la finalisation du chapitre:', error);
        });
    }

    // Fonction pour extraire l'ID du chapitre
    function getChapterIdFromCurrentElement() {
        // Cette fonction doit être adaptée selon votre structure HTML
        // Pour l'instant, on utilise une valeur par défaut
        return 22; // Remplacez par la logique appropriée
    }

    // Fonction pour afficher le modal de certificat
    function showCertificateModal() {
        // Supprimer l'ancien modal
        const existingModal = document.getElementById('progress-modal');
        if (existingModal) {
            existingModal.remove();
        }

        // Créer le modal de certificat
        const modalHtml = `
            <div id="progress-modal" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999;">
                <div style="background: white; padding: 2rem; border-radius: 12px; max-width: 600px; width: 90%; text-align: center; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
                    <div style="font-size: 4rem; margin-bottom: 1rem;">🏆</div>
                    <h2 style="margin: 0 0 1rem 0; color: #1f2937; font-size: 1.5rem; font-weight: 600;">
                        Félicitations ! Certificat obtenu !
                    </h2>
                    <p style="margin: 0 0 1rem 0; color: #6b7280; font-size: 1rem;">
                        Vous avez terminé tous les chapitres et leçons de ce cours avec succès !
                    </p>
                    <p style="margin: 0 0 2rem 0; color: #10b981; font-size: 1rem; font-weight: 600;">
                        🎓 Votre certificat a été généré automatiquement et est disponible dans votre dashboard.
                    </p>
                    <div style="display: flex; gap: 1rem; justify-content: center;">
                        <button onclick="closeModal()" style="padding: 0.75rem 1.5rem; border: 1px solid #d1d5db; background: white; color: #374151; border-radius: 8px; cursor: pointer; font-weight: 500;">
                            Fermer
                        </button>
                        <button onclick="goToDashboard()" style="padding: 0.75rem 1.5rem; background: #10b981; color: white; border: none; border-radius: 8px; cursor: pointer; font-weight: 500;">
                            Voir mon certificat
                        </button>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
    }

    // Fonction pour aller au dashboard
    window.goToDashboard = function() {
        console.log('🎓 Redirection vers le dashboard pour voir le certificat...');
        closeModal();

        // Rediriger vers le dashboard des certificats
        setTimeout(() => {
            window.location.href = '{{ route("student.certificate.index") }}';
        }, 300);
    };

    // Fonctions globales
    window.closeModal = function() {
        const modal = document.getElementById('progress-modal');
        if (modal) {
            modal.remove();
        }
    };

    window.navigateToNextLesson = function() {
        console.log('🔄 Navigation vers la leçon suivante...');
        closeModal();

        // Attendre un peu pour que le modal se ferme
        setTimeout(() => {
            // Chercher l'élément actuel par topic ID
            let currentTopicElement = null;
            if (currentTopicId) {
                currentTopicElement = document.querySelector(`[data-topic-id="${currentTopicId}"]`);
                if (currentTopicElement) {
                    currentTopicElement = currentTopicElement.closest('.topic-item');
                    console.log('🎯 Élément actuel trouvé par topic ID:', currentTopicElement);
                }
            }

            // Si pas trouvé par topic ID, chercher par classe active
            if (!currentTopicElement) {
                currentTopicElement = document.querySelector('.topic-item.active');
                console.log('🎯 Élément actuel trouvé par classe active:', currentTopicElement);
            }

            if (currentTopicElement) {
                console.log('📍 Élément actuel:', currentTopicElement);
                const nextTopicElement = currentTopicElement.nextElementSibling;
                console.log('➡️ Élément suivant:', nextTopicElement);

                if (nextTopicElement) {
                    console.log('🎯 Leçon suivante trouvée, navigation...');
                    const nextLink = nextTopicElement.querySelector('a');
                    if (nextLink) {
                        console.log('🔗 Lien suivant trouvé:', nextLink.href);
                        // Utiliser window.location au lieu de click() pour éviter les problèmes
                        window.location.href = nextLink.href;
                        return;
                    } else {
                        console.log('❌ Pas de lien dans l\'élément suivant');
                    }
                } else {
                    console.log('❌ Pas d\'élément suivant trouvé');
                }
            } else {
                console.log('❌ Aucun élément actuel trouvé');
            }

            // Si pas de leçon suivante, chercher le chapitre suivant
            const currentChapter = document.querySelector('.chapter-item.active');
            if (currentChapter) {
                const nextChapter = currentChapter.nextElementSibling;
                if (nextChapter) {
                    console.log('🎯 Chapitre suivant trouvé, navigation...');
                    const nextChapterLink = nextChapter.querySelector('a');
                    if (nextChapterLink) {
                        window.location.href = nextChapterLink.href;
                        return;
                    }
                }
            }

            // Fallback: recharger la page
            console.log('⚠️ Aucune leçon suivante trouvée, rechargement...');
            window.location.reload();
        }, 300);
    };

    // Test manuel pour debug
    window.testProgress = function() {
        if (currentTopicId) {
            console.log('🧪 Test manuel - Marquer comme terminé');
            markAsCompleted(currentTopicId);
        } else {
            console.log('❌ Aucun topic ID trouvé');
            console.log('💡 Utilisez setTopicId(123) pour définir manuellement un ID');
        }
    };

    // Fonction pour définir manuellement le topic ID
    window.setTopicId = function(id) {
        currentTopicId = id;
        console.log('🎯 Topic ID défini manuellement:', id);
    };

    // Fonction pour trouver et définir automatiquement le topic ID
    window.findAndSetTopicId = function() {
        // Chercher tous les éléments avec data-topic-id
        const topicElements = document.querySelectorAll('[data-topic-id]');
        console.log('🔍 Éléments avec data-topic-id trouvés:', topicElements.length);

        if (topicElements.length > 0) {
            // Chercher l'élément actif ou le premier
            let activeTopic = document.querySelector('.topic-item.active [data-topic-id]') ||
                             document.querySelector('.topic-item[data-topic-id]') ||
                             topicElements[0];

            const topicId = activeTopic.getAttribute('data-topic-id');
            console.log('🎯 Topic actif trouvé:', topicId);
            setTopicId(topicId);
        } else {
            console.log('❌ Aucun élément avec data-topic-id trouvé');
        }
    };

    // Fonction pour trouver le topic ID depuis l'URL
    window.findTopicIdFromUrl = function() {
        const urlParams = new URLSearchParams(window.location.search);
        const topicId = urlParams.get('topic_id') || urlParams.get('item');
        if (topicId) {
            console.log('🎯 Topic ID trouvé dans l\'URL:', topicId);
            setTopicId(topicId);
        } else {
            console.log('❌ Aucun topic ID dans l\'URL');
        }
    };

    // Fonction pour voir l'état actuel
    window.getStatus = function() {
        console.log('📊 État actuel:', {
            currentTopicId: currentTopicId,
            isStarted: isStarted,
            isCompleted: isCompleted
        });
    };

    // Fonction pour marquer l'élément actuel comme actif
    window.markCurrentAsActive = function() {
        if (currentTopicId) {
            // Retirer la classe active de tous les éléments
            document.querySelectorAll('.topic-item.active').forEach(el => {
                el.classList.remove('active');
            });

            // Ajouter la classe active à l'élément actuel
            const currentElement = document.querySelector(`[data-topic-id="${currentTopicId}"]`);
            if (currentElement) {
                const topicItem = currentElement.closest('.topic-item');
                if (topicItem) {
                    topicItem.classList.add('active');
                    console.log('✅ Élément marqué comme actif:', currentTopicId);
                }
            }
        }
    };

    // Fonction pour mettre à jour les icônes de progression
    window.updateProgressIcons = function() {
        console.log('🔄 Mise à jour des icônes de progression...');

        // Recharger la page pour mettre à jour les icônes
        setTimeout(() => {
            window.location.reload();
        }, 1000);
    };

    // Fonction pour tester la navigation
    window.testNavigation = function() {
        console.log('🧪 Test de navigation...');
        navigateToNextLesson();
    };

    // Fonction pour vérifier le statut d'un topic
    window.checkTopicStatus = function(topicId) {
        console.log('🔍 Vérification du statut du topic:', topicId);

        fetch(`{{ route('student.topic.progress', '') }}/${topicId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            console.log('📡 Réponse HTTP:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('📊 Statut du topic:', data);
        })
        .catch(error => {
            console.error('❌ Erreur lors de la vérification:', error);
        });
    };

    // Fonction pour forcer le statut completed
    window.forceCompleted = function(topicId) {
        console.log('🔧 Forçage du statut completed pour le topic:', topicId);

        fetch(`{{ route('student.topic.complete', '') }}/${topicId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            console.log('📡 Réponse HTTP:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('✅ Statut forcé:', data);
            // Recharger la page pour voir les changements
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        })
        .catch(error => {
            console.error('❌ Erreur lors du forçage:', error);
        });
    };

    // Fonction pour vérifier directement dans la base de données
    window.checkDatabase = function() {
        console.log('🔍 Vérification directe de la base de données...');

        fetch(`{{ route('student.topic.progress', '') }}/all`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            console.log('📡 Réponse HTTP:', response.status);
            if (response.status === 200) {
                return response.json();
            } else {
                throw new Error(`HTTP ${response.status}`);
            }
        })
        .then(data => {
            console.log('📊 Tous les progress:', data);
        })
        .catch(error => {
            console.error('❌ Erreur lors de la vérification:', error);
        });
    };

    // Fonction pour lister tous les topic IDs dans la sidebar
    window.listTopicIds = function() {
        console.log('📋 Liste des topic IDs dans la sidebar:');

        const topicItems = document.querySelectorAll('.topic-item');
        topicItems.forEach((item, index) => {
            const topicId = item.getAttribute('data-topic-id');
            const title = item.querySelector('h6')?.textContent || 'N/A';
            console.log(`📌 Item ${index}: ID=${topicId}, Title=${title.trim()}`);
        });
    };

    // Fonction pour tester un nouveau topic
    window.testNewTopic = function(topicId) {
        console.log(`🧪 Test du nouveau topic: ${topicId}`);

        // Marquer comme commencé
        fetch(`{{ route('student.topic.start', '') }}/${topicId}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            console.log('📡 Réponse HTTP pour start:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('✅ Topic marqué comme commencé:', data);

            // Attendre 2 secondes puis marquer comme terminé
            setTimeout(() => {
                fetch(`{{ route('student.topic.complete', '') }}/${topicId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => {
                    console.log('📡 Réponse HTTP pour complete:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('✅ Topic marqué comme terminé:', data);
                    console.log('🔄 Rechargez la page pour voir les icônes');
                })
                .catch(error => {
                    console.error('❌ Erreur lors de la finalisation:', error);
                });
            }, 2000);
        })
        .catch(error => {
            console.error('❌ Erreur lors du démarrage:', error);
        });
    };

    // Fonction pour diagnostiquer les erreurs de navigation
    window.diagnoseNavigation = function() {
        console.log('🔍 Diagnostic de la navigation...');

        // Vérifier les routes
        console.log('📍 Route start:', `{{ route('student.topic.start', '') }}/112`);
        console.log('📍 Route complete:', `{{ route('student.topic.complete', '') }}/112`);
        console.log('📍 Route progress:', `{{ route('student.topic.progress', '') }}/112`);

        // Vérifier le CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        console.log('🔐 CSRF Token:', csrfToken ? csrfToken.getAttribute('content') : 'NON TROUVÉ');

        // Vérifier l'utilisateur
        console.log('👤 Utilisateur:', window.location.href);

        // Tester une requête simple
        fetch(`{{ route('student.topic.progress', '') }}/112`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            console.log('📡 Test de la route progress:', response.status);
            if (response.ok) {
                return response.json();
            } else {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
        })
        .then(data => {
            console.log('✅ Route progress fonctionne:', data);
        })
        .catch(error => {
            console.error('❌ Erreur avec la route progress:', error);
        });
    };

    // Fonction pour diagnostiquer les clics sur les leçons
    window.diagnoseLessonClicks = function() {
        console.log('🔍 Diagnostic des clics sur les leçons...');

        // Ajouter des listeners pour capturer les clics
        document.addEventListener('click', function(e) {
            // Vérifier si c'est un clic sur une leçon
            if (e.target.closest('.video-lesson-item') || e.target.closest('.topic-item')) {
                console.log('🖱️ Clic détecté sur une leçon');
                console.log('🎯 Élément cliqué:', e.target);
                console.log('🎯 Élément parent:', e.target.closest('.topic-item'));

                const topicItem = e.target.closest('.topic-item');
                if (topicItem) {
                    const topicId = topicItem.getAttribute('data-topic-id');
                    const dataAction = topicItem.querySelector('[data-action]')?.getAttribute('data-action');
                    const dataId = topicItem.querySelector('[data-id]')?.getAttribute('data-id');

                    console.log('📋 Topic ID:', topicId);
                    console.log('📋 Data Action:', dataAction);
                    console.log('📋 Data ID:', dataId);

                    if (dataAction) {
                        console.log('🔄 Test de la requête AJAX...');

                        fetch(dataAction, {
                            method: 'GET',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(response => {
                            console.log('📡 Réponse AJAX:', response.status);
                            if (response.ok) {
                                return response.text();
                            } else {
                                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                            }
                        })
                        .then(data => {
                            console.log('✅ Contenu reçu:', data.substring(0, 200) + '...');
                        })
                        .catch(error => {
                            console.error('❌ Erreur AJAX:', error);
                        });
                    }
                }
            }
        });

    };

    // Fonction pour naviguer vers un élément spécifique
    window.navigateToSpecific = function(topicId) {

        const targetElement = document.querySelector(`[data-topic-id="${topicId}"]`);
        if (targetElement) {
            const topicItem = targetElement.closest('.topic-item');
            if (topicItem) {
                const link = topicItem.querySelector('a');
                if (link) {
                    link.click();
                    return true;
                }
            }
        }


        return false;
    };

    // Fonction pour naviguer vers le premier élément
    window.navigateToFirst = function() {

        const firstTopicItem = document.querySelector('.topic-item');
        if (firstTopicItem) {
            if (link) {
                link.click();
                return true;
            }
        }

        return false;
    };

    // Fonction pour inspecter la sidebar
    window.inspectSidebar = function() {

        const allTopicItems = document.querySelectorAll('.topic-item');

        allTopicItems.forEach((item, index) => {
            const topicId = item.getAttribute('data-topic-id');
            const isActive = item.classList.contains('active');
            const link = item.querySelector('a');
        });

        const activeItems = document.querySelectorAll('.topic-item.active');

        // Chercher l'élément actuel par topic ID
        if (currentTopicId) {
            const currentElement = document.querySelector(`[data-topic-id="${currentTopicId}"]`);
            if (currentElement) {
                const topicItem = currentElement.closest('.topic-item');
                if (topicItem) {
                    const nextElement = topicItem.nextElementSibling;
                    if (nextElement) {
                        const nextLink = nextElement.querySelector('a');
                    }
                }
            }
        }

    };

});
</script>
@endif
