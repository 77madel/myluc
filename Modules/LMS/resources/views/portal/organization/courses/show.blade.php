<x-dashboard-layout>
    <x-slot:title>{{ $course->title }}</x-slot:title>
    
    <div class="grid grid-cols-12 gap-x-4">
        <div class="col-span-full lg:col-span-8">
            <div class="card">
                <div class="p-6">
                    <h1 class="text-2xl font-bold mb-4">{{ $course->title }}</h1>
                    
                    @if($course->description)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-2">{{ translate('Description') }}</h3>
                            <p class="text-gray-600 dark:text-gray-400">{{ $course->description }}</p>
                        </div>
                    @endif
                    
                    @if($course->chapters->count() > 0)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-2">{{ translate('Contenu du Cours') }}</h3>
                            <div class="space-y-2">
                                @foreach($course->chapters as $chapter)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded">
                                        <span>{{ $chapter->title }}</span>
                                        <span class="text-sm text-gray-500">{{ $chapter->topics->count() }} {{ translate('leçons') }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-span-full lg:col-span-4">
            <div class="card">
                <div class="p-6">
                    <h3 class="text-lg font-semibold mb-4">{{ translate('Informations d\'Achat') }}</h3>
                    
                    <div class="space-y-4">
                        <div class="flex justify-between">
                            <span>{{ translate('Prix') }}:</span>
                            <span class="font-bold text-2xl text-primary">
                                {{ $course->coursePrice ? number_format($course->coursePrice->price, 0, ',', ' ') . ' FCFA' : translate('Gratuit') }}
                            </span>
                        </div>
                        
                        <div class="flex justify-between">
                            <span>{{ translate('Statut') }}:</span>
                            <span class="badge badge-success">{{ $course->status }}</span>
                        </div>
                        
                        @if($course->instructors->count() > 0)
                            <div>
                                <span class="block text-sm text-gray-600 dark:text-gray-400 mb-1">{{ translate('Instructeur(s)') }}:</span>
                                @foreach($course->instructors as $instructor)
                                    <span class="text-sm">{{ $instructor->userable->first_name ?? 'N/A' }} {{ $instructor->userable->last_name ?? '' }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    
                    @if($course->coursePrice && $course->coursePrice->price > 0)
                        <form action="{{ route('organization.courses.purchase', $course) }}" method="POST" class="mt-6">
                            @csrf
                            <button type="submit" class="btn btn-success w-full">
                                <i class="ri-shopping-cart-line mr-2"></i>
                                {{ translate('Acheter ce Cours') }}
                            </button>
                        </form>
                    @else
                        <div class="mt-6">
                            <span class="text-green-600 font-semibold">{{ translate('Cours Gratuit') }}</span>
                        </div>
                    @endif
                    
                    <div class="mt-4">
                        <a href="{{ route('organization.courses.index') }}" class="btn btn-outline w-full">
                            {{ translate('Retour à la Liste') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>










