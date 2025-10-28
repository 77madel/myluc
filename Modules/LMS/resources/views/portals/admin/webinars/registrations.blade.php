<x-dashboard-layout>
    <x-slot name="title">{{ translate('Inscriptions au Webinaire') }}</x-slot>

    <div class="container-fluid">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('webinars.index') }}" class="text-decoration-none">
                        <i class="fas fa-video me-1"></i>{{ translate('Webinaires') }}
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('webinars.show', $webinar->id) }}" class="text-decoration-none">
                        {{ Str::limit($webinar->title, 30) }}
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="fas fa-users me-1"></i>{{ translate('Inscriptions') }}
                </li>
            </ol>
        </nav>

        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="card-title mb-2">{{ translate('Inscriptions au Webinaire') }}</h4>
                                <h5 class="text-muted mb-0">{{ $webinar->title }}</h5>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-calendar-alt me-2"></i>
                                    {{ $webinar->formatted_start_date }} - {{ $webinar->formatted_end_date }}
                                </p>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="text-center">
                                            <h3 class="text-primary mb-0">{{ $registrations->total() }}</h3>
                                            <small class="text-muted">{{ translate('Inscrits') }}</small>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="text-center">
                                            <h3 class="text-success mb-0">{{ $webinar->max_participants ?? '∞' }}</h3>
                                            <small class="text-muted">{{ translate('Capacité') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Registrations List -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h5 class="card-title mb-0">{{ translate('Liste des Inscriptions') }}</h5>
                            </div>
                            <div class="col-md-6 text-end">
                                <a href="{{ route('webinars.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>{{ translate('Retour') }}
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($registrations->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>{{ translate('Participant') }}</th>
                                            <th>{{ translate('Email') }}</th>
                                            <th>{{ translate('Téléphone') }}</th>
                                            <th>{{ translate('Date d\'inscription') }}</th>
                                            <th>{{ translate('Statut') }}</th>
                                            <th>{{ translate('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($registrations as $registration)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm me-3">
                                                            <div class="avatar-title bg-primary rounded-circle">
                                                                <i class="fas fa-user"></i>
                                                            </div>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ $registration->user->name ?? 'N/A' }}</h6>
                                                            <small class="text-muted">{{ translate('Participant') }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-muted">{{ $registration->user->email ?? 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    <span class="text-muted">{{ $registration->user->phone ?? 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    <span class="text-muted">
                                                        {{ $registration->created_at ? $registration->created_at->format('d/m/Y H:i') : 'N/A' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($registration->status == 'confirmed')
                                                        <span class="badge bg-success">{{ translate('Confirmé') }}</span>
                                                    @elseif($registration->status == 'pending')
                                                        <span class="badge bg-warning">{{ translate('En attente') }}</span>
                                                    @elseif($registration->status == 'cancelled')
                                                        <span class="badge bg-danger">{{ translate('Annulé') }}</span>
                                                    @else
                                                        <span class="badge bg-secondary">{{ translate('Inconnu') }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="dropdown">
                                                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                            <i class="fas fa-cog"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            <li>
                                                                <a class="dropdown-item" href="#">
                                                                    <i class="fas fa-eye me-2"></i>{{ translate('Voir le profil') }}
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item" href="#">
                                                                    <i class="fas fa-envelope me-2"></i>{{ translate('Envoyer un email') }}
                                                                </a>
                                                            </li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <a class="dropdown-item text-danger" href="#">
                                                                    <i class="fas fa-times me-2"></i>{{ translate('Annuler l\'inscription') }}
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $registrations->links() }}
                            </div>
                        @else
                            <!-- Empty State -->
                            <div class="text-center py-5">
                                <div class="mb-4">
                                    <i class="fas fa-users fa-3x text-muted"></i>
                                </div>
                                <h5 class="text-muted">{{ translate('Aucune inscription') }}</h5>
                                <p class="text-muted">{{ translate('Aucun participant ne s\'est encore inscrit à ce webinaire.') }}</p>
                                <a href="{{ route('webinars.index') }}" class="btn btn-primary">
                                    <i class="fas fa-arrow-left me-2"></i>{{ translate('Retour aux webinaires') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .avatar-sm {
            width: 40px;
            height: 40px;
        }

        .avatar-title {
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        .table th {
            border-top: none;
            font-weight: 600;
            color: #495057;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
        }

        .dropdown-toggle::after {
            display: none;
        }
    </style>
    @endpush
</x-dashboard-layout>
