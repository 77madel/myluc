<x-dashboard-layout>
    <x-slot name="title">{{ $webinar->title }}</x-slot>

    <div class="container-fluid">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('webinars.index') }}" class="text-decoration-none">
                        <i class="fas fa-video me-1"></i>{{ translate('Webinaires') }}
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    {{ Str::limit($webinar->title, 50) }}
                </li>
            </ol>
        </nav>

        <!-- Webinar Details -->
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="card-title mb-0">{{ $webinar->title }}</h4>
                                <p class="text-muted mb-0">{{ $webinar->short_description }}</p>
                            </div>
                            <div class="col-md-4 text-end">
                                <span class="badge bg-{{ $webinar->status == 'published' ? 'success' : ($webinar->status == 'draft' ? 'secondary' : 'warning') }}">
                                    {{ translate(ucfirst($webinar->status)) }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted">{{ translate('Description') }}</h6>
                                <p>{{ $webinar->description ?? translate('Aucune description disponible') }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">{{ translate('Détails') }}</h6>
                                <ul class="list-unstyled">
                                    <li><strong>{{ translate('Instructeur') }}:</strong> {{ $webinar->instructor?->first_name ?? 'N/A' }} {{ $webinar->instructor?->last_name ?? '' }}</li>
                                    <li><strong>{{ translate('Date de début') }}:</strong> {{ $webinar->formatted_start_date }}</li>
                                    <li><strong>{{ translate('Date de fin') }}:</strong> {{ $webinar->formatted_end_date }}</li>
                                    <li><strong>{{ translate('Durée') }}:</strong> {{ $webinar->duration ?? 'N/A' }} {{ translate('minutes') }}</li>
                                    <li><strong>{{ translate('Participants max') }}:</strong> {{ $webinar->max_participants ?? '∞' }}</li>
                                    <li><strong>{{ translate('Prix') }}:</strong> {{ $webinar->is_free ? translate('Gratuit') : $webinar->price . ' €' }}</li>
                                </ul>
                            </div>
                        </div>

                        @if($webinar->meeting_url)
                            <div class="mt-4">
                                <h6 class="text-muted">{{ translate('Informations de connexion') }}</h6>
                                <div class="alert alert-info">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>{{ translate('Lien de meeting') }}:</strong><br>
                                            <a href="{{ $webinar->meeting_url }}" target="_blank" class="btn btn-sm btn-primary">
                                                <i class="fas fa-external-link-alt me-1"></i>{{ translate('Rejoindre') }}
                                            </a>
                                        </div>
                                        @if($webinar->meeting_id)
                                            <div class="col-md-3">
                                                <strong>{{ translate('ID de meeting') }}:</strong><br>
                                                <code>{{ $webinar->meeting_id }}</code>
                                            </div>
                                        @endif
                                        @if($webinar->meeting_password)
                                            <div class="col-md-3">
                                                <strong>{{ translate('Mot de passe') }}:</strong><br>
                                                <code>{{ $webinar->meeting_password }}</code>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Quick Stats -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ translate('Statistiques') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-6">
                                <h3 class="text-primary mb-0">{{ $registrations->total() }}</h3>
                                <small class="text-muted">{{ translate('Inscrits') }}</small>
                            </div>
                            <div class="col-6">
                                <h3 class="text-success mb-0">{{ $webinar->max_participants ?? '∞' }}</h3>
                                <small class="text-muted">{{ translate('Capacité') }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">{{ translate('Actions') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('webinars.edit', $webinar->id) }}" class="btn btn-outline-primary">
                                <i class="fas fa-edit me-2"></i>{{ translate('Modifier') }}
                            </a>
                            <a href="{{ route('webinars.registrations', $webinar->id) }}" class="btn btn-outline-info">
                                <i class="fas fa-users me-2"></i>{{ translate('Voir les inscriptions') }}
                            </a>
                            @if($webinar->status == 'draft')
                                <form action="{{ route('webinars.publish', $webinar->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-eye me-2"></i>{{ translate('Publier') }}
                                    </button>
                                </form>
                            @elseif($webinar->status == 'published')
                                <form action="{{ route('webinars.unpublish', $webinar->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-warning w-100">
                                        <i class="fas fa-eye-slash me-2"></i>{{ translate('Dépublier') }}
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('webinars.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>{{ translate('Retour à la liste') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Registrations -->
        @if($registrations->count() > 0)
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <h5 class="card-title mb-0">{{ translate('Inscriptions récentes') }}</h5>
                                </div>
                                <div class="col-md-6 text-end">
                                    <a href="{{ route('webinars.registrations', $webinar->id) }}" class="btn btn-sm btn-outline-primary">
                                        {{ translate('Voir toutes') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>{{ translate('Participant') }}</th>
                                            <th>{{ translate('Email') }}</th>
                                            <th>{{ translate('Date') }}</th>
                                            <th>{{ translate('Statut') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($registrations->take(5) as $registration)
                                            <tr>
                                                <td>{{ $registration->user->name ?? 'N/A' }}</td>
                                                <td>{{ $registration->user->email ?? 'N/A' }}</td>
                                                <td>{{ $registration->created_at ? $registration->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $registration->status == 'confirmed' ? 'success' : 'warning' }}">
                                                        {{ translate(ucfirst($registration->status)) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-dashboard-layout>

