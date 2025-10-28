<x-dashboard-layout>
    <x-slot name="title">
        {{ translate('Détails du Webinaire') }}
    </x-slot>

    <x-portal::admin.breadcrumb>
        <x-slot name="title">{{ translate('Détails du Webinaire') }}</x-slot>
        <li class="breadcrumb-item">
            <a href="{{ route('instructor.dashboard') }}">{{ translate('Dashboard') }}</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('instructor.webinars.index') }}">{{ translate('Webinaires') }}</a>
        </li>
        <li class="breadcrumb-item active">{{ $webinar->title }}</li>
    </x-portal::admin.breadcrumb>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">{{ $webinar->title }}</h4>
                        <div>
                            @if($webinar->is_published)
                                <span class="badge bg-success">{{ translate('Publié') }}</span>
                            @else
                                <span class="badge bg-warning">{{ translate('Brouillon') }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        @if($webinar->image)
                            <div class="mb-4">
                                <img src="{{ Storage::url($webinar->image) }}" alt="{{ $webinar->title }}"
                                     class="img-fluid rounded" style="max-height: 300px; width: 100%; object-fit: cover;">
                            </div>
                        @endif

                        <div class="mb-4">
                            <h6>{{ translate('Description courte') }}</h6>
                            <p class="text-muted">{{ $webinar->short_description }}</p>
                        </div>

                        <div class="mb-4">
                            <h6>{{ translate('Description complète') }}</h6>
                            <div class="text-muted">{!! nl2br(e($webinar->description)) !!}</div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <h6>{{ translate('Informations du webinaire') }}</h6>
                                <ul class="list-unstyled">
                                    <li><strong>{{ translate('Date de début') }}:</strong> {{ $webinar->start_date->format('d/m/Y H:i') }}</li>
                                    <li><strong>{{ translate('Date de fin') }}:</strong> {{ $webinar->end_date->format('d/m/Y H:i') }}</li>
                                    <li><strong>{{ translate('Durée') }}:</strong> {{ $webinar->duration }} {{ translate('minutes') }}</li>
                                    <li><strong>{{ translate('Catégorie') }}:</strong> {{ $webinar->category->title ?? 'N/A' }}</li>
                                    <li><strong>{{ translate('Participants maximum') }}:</strong>
                                        {{ $webinar->max_participants ? $webinar->max_participants : translate('Illimité') }}
                                    </li>
                                    <li><strong>{{ translate('Enregistrement') }}:</strong>
                                        {{ $webinar->is_recorded ? translate('Oui') : translate('Non') }}
                                    </li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h6>{{ translate('Statistiques') }}</h6>
                                <ul class="list-unstyled">
                                    <li><strong>{{ translate('Participants actuels') }}:</strong>
                                        <span class="badge bg-primary">{{ $webinar->current_participants }}</span>
                                    </li>
                                    <li><strong>{{ translate('Inscriptions') }}:</strong>
                                        <span class="badge bg-info">{{ $webinar->registrations->count() }}</span>
                                    </li>
                                    <li><strong>{{ translate('Créé le') }}:</strong> {{ $webinar->created_at->format('d/m/Y H:i') }}</li>
                                    <li><strong>{{ translate('Modifié le') }}:</strong> {{ $webinar->updated_at->format('d/m/Y H:i') }}</li>
                                </ul>
                            </div>
                        </div>

                        @if($webinar->meeting_url)
                            <div class="alert alert-info">
                                <h6><i class="fas fa-video"></i> {{ translate('Lien de réunion') }}</h6>
                                <p class="mb-2">{{ translate('Votre webinaire dispose d\'un lien de réunion actif.') }}</p>
                                <a href="{{ $webinar->meeting_url }}" target="_blank" class="btn btn-primary">
                                    <i class="fas fa-external-link-alt"></i> {{ translate('Rejoindre la réunion') }}
                                </a>
                                @if($webinar->meeting_id)
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <strong>{{ translate('ID de réunion') }}:</strong> {{ $webinar->meeting_id }}
                                        </small>
                                    </div>
                                @endif
                                @if($webinar->meeting_password)
                                    <div class="mt-1">
                                        <small class="text-muted">
                                            <strong>{{ translate('Mot de passe') }}:</strong> {{ $webinar->meeting_password }}
                                        </small>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <h6><i class="fas fa-exclamation-triangle"></i> {{ translate('Aucun lien de réunion') }}</h6>
                                <p class="mb-2">{{ translate('Ce webinaire n\'a pas encore de lien de réunion. Publiez-le pour en générer un automatiquement.') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ translate('Actions') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('instructor.webinars.edit', $webinar->id) }}" class="btn btn-outline-primary">
                                <i class="fas fa-edit"></i> {{ translate('Modifier') }}
                            </a>

                            @if(!$webinar->is_published)
                                <form action="{{ route('instructor.webinars.publish', $webinar->id) }}" method="POST" class="d-grid">
                                    @csrf
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check"></i> {{ translate('Publier') }}
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('instructor.webinars.unpublish', $webinar->id) }}" method="POST" class="d-grid">
                                    @csrf
                                    <button type="submit" class="btn btn-warning">
                                        <i class="fas fa-times"></i> {{ translate('Dépublier') }}
                                    </button>
                                </form>
                            @endif

                            @if($webinar->meeting_url)
                                <a href="{{ $webinar->meeting_url }}" target="_blank" class="btn btn-primary">
                                    <i class="fas fa-external-link-alt"></i> {{ translate('Lien de réunion') }}
                                </a>
                            @endif

                            <hr>

                            <form action="{{ route('instructor.webinars.destroy', $webinar->id) }}" method="POST"
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce webinaire ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="fas fa-trash"></i> {{ translate('Supprimer') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ translate('Participants') }}</h5>
                    </div>
                    <div class="card-body">
                        @if($registrations->count() > 0)
                            <div class="list-group list-group-flush">
                                @foreach($registrations as $registration)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $registration->user->username ?? 'Utilisateur' }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ translate('Inscrit le') }} {{ $registration->created_at->format('d/m/Y H:i') }}
                                            </small>
                                        </div>
                                        <span class="badge bg-{{ $registration->status === 'confirmed' ? 'success' : 'warning' }}">
                                            {{ ucfirst($registration->status) }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-center mt-3">
                                {{ $registrations->links() }}
                            </div>
                        @else
                            <div class="text-center text-muted">
                                <i class="fas fa-users fa-2x mb-2"></i>
                                <p>{{ translate('Aucun participant inscrit') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>





