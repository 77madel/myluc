@php
    $user = authCheck();
    $translations = parse_translation($course);
    $title = $translations['title'] ?? ($course->title ?? '');
    $description = $translations['description'] ?? ($course->description ?? '');
    $instructors = $course->instructors ?? [];
    $thumbnail = fileExists('lms/courses/thumbnails', $course->thumbnail) == true
        ? asset("storage/lms/courses/thumbnails/{$course->thumbnail}")
        : asset('lms/assets/images/placeholder/thumbnail612.jpg');
    
    $currency = $course?->coursePrice?->currency ?? 'USD-$';
    $currencySymbol = get_currency_symbol($currency);
    $price = $course?->coursePrice?->price ?? 0;
    $isFree = $course?->courseSetting?->is_free ?? false;
@endphp

<x-dashboard-layout>
    <x-slot:title>{{ $title }}</x-slot:title>
    
    <!-- BREADCRUMB -->
    <x-portal::admin.breadcrumb title="Détails du Cours" page-to="Cours" />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2">
            <div class="card">
                <div class="p-6">
                    <!-- Course Header -->
                    <div class="mb-6">
                        <img src="{{ $thumbnail }}" 
                             alt="{{ $title }}" 
                             class="w-full h-64 object-cover rounded-lg mb-4">
                        
                        <h1 class="text-2xl font-bold text-heading dark:text-white mb-2">
                            {{ $title }}
                        </h1>
                        
                        @if($isFree)
                            <span class="badge b-solid badge-success-solid text-lg px-4 py-2">
                                {{ translate('Gratuit') }}
                            </span>
                        @else
                            <span class="badge b-solid badge-primary-solid text-lg px-4 py-2">
                                {{ $currencySymbol }}{{ number_format($price, 0) }}
                            </span>
                        @endif
                    </div>
                    
                    <!-- Course Description -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-heading dark:text-white mb-3">
                            {{ translate('Description du Cours') }}
                        </h3>
                        <div class="prose dark:prose-invert max-w-none">
                            {!! $description !!}
                        </div>
                    </div>
                    
                    <!-- Course Instructors -->
                    @if(count($instructors) > 0)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-heading dark:text-white mb-3">
                                {{ translate('Instructeurs') }}
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($instructors as $instructor)
                                    @php
                                        $userInfo = $instructor->userable ?? null;
                                        $userTranslations = parse_translation($userInfo);
                                        $avatar = $userInfo?->avatar 
                                            ? asset("storage/{$userInfo->avatar}")
                                            : asset('lms/assets/images/placeholder/avatar.png');
                                    @endphp
                                    <div class="flex items-center gap-3 p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                                        <img src="{{ $avatar }}" 
                                             alt="{{ $userTranslations['first_name'] ?? $userInfo?->first_name }}"
                                             class="w-12 h-12 rounded-full">
                                        <div>
                                            <h4 class="font-semibold text-heading dark:text-white">
                                                {{ $userTranslations['first_name'] ?? $userInfo?->first_name }}
                                                {{ $userTranslations['last_name'] ?? $userInfo?->last_name }}
                                            </h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                {{ translate('Instructeur') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    
                    <!-- Course Chapters -->
                    @if($course->chapters && $course->chapters->count() > 0)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-heading dark:text-white mb-3">
                                {{ translate('Contenu du Cours') }}
                            </h3>
                            <div class="space-y-2">
                                @foreach($course->chapters as $chapter)
                                    @php
                                        $chapterTranslations = parse_translation($chapter);
                                        $chapterTitle = $chapterTranslations['title'] ?? ($chapter->title ?? '');
                                    @endphp
                                    <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                                        <div class="flex items-center gap-3">
                                            <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                            </svg>
                                            <span class="font-medium text-heading dark:text-white">
                                                {{ $chapterTitle }}
                                            </span>
                                        </div>
                                        <span class="text-sm text-gray-500">
                                            {{ $chapter->topics?->count() ?? 0 }} {{ translate('leçons') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="lg:col-span-1">
            <div class="card sticky top-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-heading dark:text-white mb-4">
                        {{ translate('Acheter ce Cours') }}
                    </h3>
                    
                    <!-- Course Stats -->
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-400">{{ translate('Étudiants inscrits') }}</span>
                            <span class="font-semibold">{{ $course->enrollments?->count() ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-400">{{ translate('Chapitres') }}</span>
                            <span class="font-semibold">{{ $course->chapters?->count() ?? 0 }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-400">{{ translate('Durée') }}</span>
                            <span class="font-semibold">{{ $course->duration ?? 'N/A' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-400">{{ translate('Niveau') }}</span>
                            <span class="font-semibold">{{ $course->levels?->first()?->name ?? 'N/A' }}</span>
                        </div>
                    </div>
                    
                    <!-- Purchase Button -->
                    <form action="{{ route('organization.courses.purchase', $course) }}" method="POST" class="mb-4">
                        @csrf
                        <button type="submit" 
                                class="btn b-solid btn-primary-solid w-full text-lg py-3">
                            @if($isFree)
                                {{ translate('Obtenir Gratuitement') }}
                            @else
                                {{ translate('Acheter pour') }} {{ $currencySymbol }}{{ number_format($price, 0) }}
                            @endif
                        </button>
                    </form>
                    
                    <!-- Course Features -->
                    <div class="space-y-2">
                        <div class="flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ translate('Accès à vie') }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ translate('Certificat de fin') }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ translate('Support mobile') }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span>{{ translate('Lien d\'inscription automatique') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>










