@php
    $image =
        !empty($course->thumbnail) && fileExists('lms/courses/thumbnails', $course->thumbnail)
            ? asset('storage/lms/courses/thumbnails/' . $course->thumbnail)
            : asset('lms/frontend/assets/images/420x252.svg');

    $shortVideo =
        !empty($course->short_video) && fileExists('lms/courses/demo-videos', $course->short_video)
            ? asset('storage/lms/courses/demo-videos/' . $course->short_video)
            : null;

@endphp
@if ($course->video_src_type == 'local')
    <video id="course-demo" playsinline controls data-poster="{{ $image }}">
        <source src="{{ $shortVideo }}" type="video/mp4" />
    </video>
@else
    <!-- VIMEO/YOUTUBE -->
    @php
        // Convertir l'URL YouTube/Vimeo en format embed
        $embedUrl = $course->demo_url;
        if (strpos($course->demo_url, 'youtube.com') !== false || strpos($course->demo_url, 'youtu.be') !== false) {
            // Extraire l'ID de la vidéo YouTube
            if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/)([^&\n?#]+)/', $course->demo_url, $matches)) {
                $videoId = $matches[1];
                $embedUrl = "https://www.youtube.com/embed/{$videoId}?rel=0&modestbranding=1&showinfo=0";
            }
        } elseif (strpos($course->demo_url, 'vimeo.com') !== false) {
            // Extraire l'ID de la vidéo Vimeo
            if (preg_match('/vimeo\.com\/(\d+)/', $course->demo_url, $matches)) {
                $videoId = $matches[1];
                $embedUrl = "https://player.vimeo.com/video/{$videoId}";
            }
        }
    @endphp
    <div class="plyr__video-embed" id="course-demo">
        <iframe src="{{ $embedUrl }}"
                allowfullscreen
                allowtransparency
                allow="autoplay"
                frameborder="0"
                webkitallowfullscreen
                mozallowfullscreen>
        </iframe>
    </div>
@endif
