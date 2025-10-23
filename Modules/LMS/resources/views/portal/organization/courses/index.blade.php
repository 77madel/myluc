<x-dashboard-layout>
    <x-slot:title>{{ translate('Cours Disponibles') }}</x-slot:title>
    
    <div class="grid grid-cols-12 gap-x-4">
        <div class="col-span-full">
            <div class="flex-center-between mb-6">
                <h6 class="card-title">{{ translate('Cours Disponibles pour Achat') }}</h6>
            </div>
            
            @if($courses->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($courses as $course)
                        <div class="card">
                            <div class="p-6">
                                <h5 class="text-lg font-semibold mb-2">{{ $course->title }}</h5>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">{{ Str::limit($course->description, 100) }}</p>
                                
                                <div class="flex items-center justify-between mb-4">
                                    <span class="text-2xl font-bold text-primary">
                                        {{ $course->coursePrice ? number_format($course->coursePrice->price, 0, ',', ' ') . ' FCFA' : 'Gratuit' }}
                                    </span>
                                    <span class="badge badge-success">{{ $course->status }}</span>
                                </div>
                                
                                <div class="flex gap-2">
                                    <a href="{{ route('organization.courses.show', $course) }}" 
                                       class="btn btn-primary btn-sm flex-1">
                                        {{ translate('Voir Détails') }}
                                    </a>
                                    @if($course->coursePrice && $course->coursePrice->price > 0)
                                        <form action="{{ route('organization.courses.purchase', $course) }}" method="POST" class="flex-1">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm w-full">
                                                {{ translate('Acheter') }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-6">
                    {{ $courses->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <p class="text-gray-500">{{ translate('Aucun cours disponible pour le moment.') }}</p>
                </div>
            @endif
        </div>
    </div>
</x-dashboard-layout>








