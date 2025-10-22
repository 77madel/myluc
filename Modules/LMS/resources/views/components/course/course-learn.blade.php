@php
    $data = $topic['data'];
@endphp

@if ($type == 'video')
    @if ($data->video_src_type == 'youtube' || $data->video_src_type == 'vimeo')
        @php
            // Convertir l'URL YouTube en format embed
            $embedUrl = $data->video_url;
            if ($data->video_src_type == 'youtube') {
                // Extraire l'ID de la vidéo YouTube
                if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\n?#]+)/', $data->video_url, $matches)) {
                    $videoId = $matches[1];
                    $embedUrl = "https://www.youtube.com/embed/{$videoId}?rel=0&modestbranding=1&showinfo=0";
                }
            } elseif ($data->video_src_type == 'vimeo') {
                // Extraire l'ID de la vidéo Vimeo
                if (preg_match('/vimeo\.com\/(\d+)/', $data->video_url, $matches)) {
                    $videoId = $matches[1];
                    $embedUrl = "https://player.vimeo.com/video/{$videoId}";
                }
            }
        @endphp
        <div class="plyr__video-embed" id="player">
            <iframe src="{{ $embedUrl }}" 
                    allowfullscreen 
                    allowtransparency 
                    allow="autoplay"
                    frameborder="0"
                    webkitallowfullscreen
                    mozallowfullscreen>
            </iframe>
        </div>
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

            // Système de progression automatique
            @if(auth()->check() && auth()->user()->guard === 'student')
            
            // Détecter le clic sur play pour marquer comme in_progress
            player.on('play', function() {
                console.log('▶️ Video started playing - Marking as in_progress');
                markTopicAsStarted();
            });
            
            // Détecter la fin de vidéo pour marquer comme completed
            player.on('ended', function() {
                console.log('🎬 Video ended - Auto progress triggered');
                handleVideoCompletion();
            });

            // Fonction pour gérer la fin de vidéo
            function handleVideoCompletion() {
                const topicId = getCurrentTopicId();
                if (topicId) {
                    markTopicAsCompleted(topicId);
                }
            }

            // Fonction pour obtenir l'ID du topic actuel
            function getCurrentTopicId() {
                // Chercher dans l'URL
                const urlParams = new URLSearchParams(window.location.search);
                const topicId = urlParams.get('topic_id') || urlParams.get('item');
                if (topicId) return topicId;

                // Chercher dans les attributs data
                const topicElement = document.querySelector('[data-topic-id]');
                if (topicElement) {
                    return topicElement.getAttribute('data-topic-id');
                }

                return null;
            }

            // Fonction pour marquer une leçon comme terminée
            async function markTopicAsCompleted(topicId) {
                try {
                    const response = await fetch(`{{ route('student.topic.complete', '') }}/${topicId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    const data = await response.json();
                    
                    if (data.status === 'success') {
                        showLessonCompletionModal();
                        
                        // Vérifier si le chapitre est terminé
                        if (data.chapter_completed) {
                            setTimeout(() => {
                                showChapterCompletionModal(data.next_chapter);
                            }, 2000);
                        }
                    }
                } catch (error) {
                    console.error('Erreur lors de la progression:', error);
                }
            }

            // Modal de félicitations pour la leçon
            function showLessonCompletionModal() {
                const modal = document.createElement('div');
                modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
                modal.innerHTML = `
                    <div class="bg-white rounded-lg p-8 max-w-md mx-4 text-center">
                        <div class="text-6xl mb-4">🎉</div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Félicitations !</h3>
                        <p class="text-gray-600 mb-6">Vous avez terminé cette leçon.</p>
                        <button onclick="this.closest('.fixed').remove()" 
                                class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-colors">
                            Continuer
                        </button>
                    </div>
                `;
                document.body.appendChild(modal);
            }

            // Modal de félicitations pour le chapitre
            function showChapterCompletionModal(nextChapter = null) {
                const modal = document.createElement('div');
                modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
                
                const nextChapterButton = nextChapter ? 
                    `<button onclick="goToNextChapter('${nextChapter.url}')" 
                            class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg transition-colors">
                        Suivant: ${nextChapter.title}
                    </button>` : 
                    `<button onclick="this.closest('.fixed').remove()" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-colors">
                        Terminé
                    </button>`;
                
                modal.innerHTML = `
                    <div class="bg-white rounded-lg p-8 max-w-md mx-4 text-center">
                        <div class="text-6xl mb-4">🎉</div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Bravo !</h3>
                        <p class="text-gray-600 mb-6">Vous avez terminé ce chapitre !</p>
                        <div class="flex gap-3 justify-center">
                            <button onclick="this.closest('.fixed').remove()" 
                                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors">
                                Fermer
                            </button>
                            ${nextChapterButton}
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);
            }

            // Fonction pour aller au chapitre suivant
            function goToNextChapter(nextChapterUrl = null) {
                // Fermer le modal
                document.querySelector('.fixed').remove();
                
                if (nextChapterUrl) {
                    console.log('Navigating to next chapter:', nextChapterUrl);
                    window.location.href = nextChapterUrl;
                } else {
                    console.log('No next chapter available');
                }
            }
            @endif

            // Fonction pour obtenir l'ID du topic actuel
            function getCurrentTopicId() {
                // Chercher dans l'URL
                const urlParams = new URLSearchParams(window.location.search);
                const topicId = urlParams.get('topic_id') || urlParams.get('item');
                if (topicId) return topicId;

                // Chercher dans les attributs data
                const topicElement = document.querySelector('[data-topic-id]');
                if (topicElement) {
                    return topicElement.getAttribute('data-topic-id');
                }

                return null;
            }

            // Fonction pour marquer une leçon comme terminée
            async function markTopicAsCompleted(topicId) {
                try {
                    const response = await fetch(`{{ route('student.topic.complete', '') }}/${topicId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    const data = await response.json();
                    
                    if (data.status === 'success') {
                        showLessonCompletionModal();
                        
                        // Vérifier si le chapitre est terminé
                        if (data.chapter_completed) {
                            setTimeout(() => {
                                showChapterCompletionModal(data.next_chapter);
                            }, 2000);
                        }
                    }
                } catch (error) {
                    console.error('Erreur lors de la progression:', error);
                }
            }

            // Modal de félicitations pour la leçon
            function showLessonCompletionModal() {
                const modal = document.createElement('div');
                modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
                modal.innerHTML = `
                    <div class="bg-white rounded-lg p-8 max-w-md mx-4 text-center">
                        <div class="text-6xl mb-4">🎉</div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Félicitations !</h3>
                        <p class="text-gray-600 mb-6">Vous avez terminé cette leçon.</p>
                        <button onclick="this.closest('.fixed').remove()" 
                                class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-colors">
                            Continuer
                        </button>
                    </div>
                `;
                document.body.appendChild(modal);
            }

            // Modal de félicitations pour le chapitre
            function showChapterCompletionModal(nextChapter = null) {
                const modal = document.createElement('div');
                modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
                
                const nextChapterButton = nextChapter ? 
                    `<button onclick="goToNextChapter('${nextChapter.url}')" 
                            class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg transition-colors">
                        Suivant: ${nextChapter.title}
                    </button>` : 
                    `<button onclick="this.closest('.fixed').remove()" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-colors">
                        Terminé
                    </button>`;
                
                modal.innerHTML = `
                    <div class="bg-white rounded-lg p-8 max-w-md mx-4 text-center">
                        <div class="text-6xl mb-4">🎉</div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Bravo !</h3>
                        <p class="text-gray-600 mb-6">Vous avez terminé ce chapitre !</p>
                        <div class="flex gap-3 justify-center">
                            <button onclick="this.closest('.fixed').remove()" 
                                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors">
                                Fermer
                            </button>
                            ${nextChapterButton}
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);
            }

            // Fonction pour aller au chapitre suivant
            function goToNextChapter(nextChapterUrl = null) {
                // Fermer le modal
                document.querySelector('.fixed').remove();
                
                if (nextChapterUrl) {
                    console.log('Navigating to next chapter:', nextChapterUrl);
                    window.location.href = nextChapterUrl;
                } else {
                    console.log('No next chapter available');
                }
            }

            // Fonction pour marquer une leçon comme commencée
            async function markTopicAsStarted() {
                const topicId = getCurrentTopicId();
                if (topicId) {
                    try {
                        const response = await fetch(`{{ route('student.topic.start', '') }}/${topicId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        const data = await response.json();
                        
                        if (data.status === 'success') {
                            console.log('✅ Topic marked as started');
                        }
                    } catch (error) {
                        console.error('Erreur lors du démarrage de la leçon:', error);
                    }
                }
            }
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
            const player = new Plyr("#player", {
                settings: ["speed"],
                seekTime: 0,
                ratio: "16:7",
                speed: {
                    selected: 1,
                    options: [0.5, 0.75, 1, 1.25, 1.5]
                },
            });

            // Système de progression automatique pour vidéos locales
            @if(auth()->check() && auth()->user()->guard === 'student')
            
            // Détecter le clic sur play pour marquer comme in_progress
            player.on('play', function() {
                console.log('▶️ Local video started playing - Marking as in_progress');
                markTopicAsStarted();
            });
            
            // Détecter la fin de vidéo pour marquer comme completed
            player.on('ended', function() {
                console.log('🎬 Local video ended - Auto progress triggered');
                handleVideoCompletion();
            });

            // Fonction pour gérer la fin de vidéo
            function handleVideoCompletion() {
                const topicId = getCurrentTopicId();
                if (topicId) {
                    markTopicAsCompleted(topicId);
                }
            }

            // Fonction pour obtenir l'ID du topic actuel
            function getCurrentTopicId() {
                // Chercher dans l'URL
                const urlParams = new URLSearchParams(window.location.search);
                const topicId = urlParams.get('topic_id') || urlParams.get('item');
                if (topicId) return topicId;

                // Chercher dans les attributs data
                const topicElement = document.querySelector('[data-topic-id]');
                if (topicElement) {
                    return topicElement.getAttribute('data-topic-id');
                }

                return null;
            }

            // Fonction pour marquer une leçon comme terminée
            async function markTopicAsCompleted(topicId) {
                try {
                    const response = await fetch(`{{ route('student.topic.complete', '') }}/${topicId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    const data = await response.json();
                    
                    if (data.status === 'success') {
                        showLessonCompletionModal();
                        
                        // Vérifier si le chapitre est terminé
                        if (data.chapter_completed) {
                            setTimeout(() => {
                                showChapterCompletionModal(data.next_chapter);
                            }, 2000);
                        }
                    }
                } catch (error) {
                    console.error('Erreur lors de la progression:', error);
                }
            }

            // Modal de félicitations pour la leçon
            function showLessonCompletionModal() {
                const modal = document.createElement('div');
                modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
                modal.innerHTML = `
                    <div class="bg-white rounded-lg p-8 max-w-md mx-4 text-center">
                        <div class="text-6xl mb-4">🎉</div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Félicitations !</h3>
                        <p class="text-gray-600 mb-6">Vous avez terminé cette leçon.</p>
                        <button onclick="this.closest('.fixed').remove()" 
                                class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-colors">
                            Continuer
                        </button>
                    </div>
                `;
                document.body.appendChild(modal);
            }

            // Modal de félicitations pour le chapitre
            function showChapterCompletionModal(nextChapter = null) {
                const modal = document.createElement('div');
                modal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
                
                const nextChapterButton = nextChapter ? 
                    `<button onclick="goToNextChapter('${nextChapter.url}')" 
                            class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg transition-colors">
                        Suivant: ${nextChapter.title}
                    </button>` : 
                    `<button onclick="this.closest('.fixed').remove()" 
                            class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-lg transition-colors">
                        Terminé
                    </button>`;
                
                modal.innerHTML = `
                    <div class="bg-white rounded-lg p-8 max-w-md mx-4 text-center">
                        <div class="text-6xl mb-4">🎉</div>
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">Bravo !</h3>
                        <p class="text-gray-600 mb-6">Vous avez terminé ce chapitre !</p>
                        <div class="flex gap-3 justify-center">
                            <button onclick="this.closest('.fixed').remove()" 
                                    class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg transition-colors">
                                Fermer
                            </button>
                            ${nextChapterButton}
                        </div>
                    </div>
                `;
                document.body.appendChild(modal);
            }

            // Fonction pour aller au chapitre suivant
            function goToNextChapter(nextChapterUrl = null) {
                // Fermer le modal
                document.querySelector('.fixed').remove();
                
                if (nextChapterUrl) {
                    console.log('Navigating to next chapter:', nextChapterUrl);
                    window.location.href = nextChapterUrl;
                } else {
                    console.log('No next chapter available');
                }
            }

            // Fonction pour marquer une leçon comme commencée
            async function markTopicAsStarted() {
                const topicId = getCurrentTopicId();
                if (topicId) {
                    try {
                        const response = await fetch(`{{ route('student.topic.start', '') }}/${topicId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        });

                        const data = await response.json();
                        
                        if (data.status === 'success') {
                            console.log('✅ Topic marked as started');
                        }
                    } catch (error) {
                        console.error('Erreur lors du démarrage de la leçon:', error);
                    }
                }
            }
            @endif
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
    </div>
@elseif($type == 'assignment')
    <div class="p-5 md:p-8 xl:p-10 relative overflow-hidden aspect-[16/7] bg-primary-50 rounded-xl">
        <div class="size-full flex-center">
            <a href="{{ route('exam.start', ['type' => $type, 'exam_type_id' => $data->id, 'course_id' => $data?->topic?->course_id]) }}"
                class="btn b-solid btn-primary-solid"
                aria-label="Go to assignment"
            >
                {{ translate('Go to Assignment') }}
            </a>
        </div>
    </div>
@elseif($type == 'quiz')
    <div class="p-5 md:p-8 xl:p-10 relative overflow-hidden aspect-[16/7] bg-primary-50 rounded-xl">
        <div class="size-full flex-center">
            <a href="{{ route('exam.start', ['type' => $type, 'exam_type_id' => $data->id, 'course_id' => $topic['courseId'], 'topic_id' => $topic['topicId'], 'chapterId' => $topic['chapterId']]) }}"
                class="btn b-solid btn-primary-solid"
                aria-label="Go to quiz"
            >
                {{ translate('Go to Quiz') }}
            </a>
        </div>
    </div>
@endif
