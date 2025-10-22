<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $webinar->title }} - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 60px 0;
        }
        .instructor-card {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 15px;
            padding: 30px;
            text-align: center;
        }
        .instructor-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .info-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border: none;
        }
        .btn-enroll {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            color: white;
            padding: 15px 30px;
            border-radius: 30px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .btn-enroll:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
        }
        .btn-join {
            background: linear-gradient(135deg, #dc3545, #c82333);
            border: none;
            color: white;
            padding: 15px 30px;
            border-radius: 30px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .btn-join:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4);
        }
        .status-badge {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 10;
        }
        .meeting-info {
            background: linear-gradient(135deg, #e3f2fd, #bbdefb);
            border-radius: 15px;
            padding: 20px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand text-primary" href="{{ url('/') }}">
                <i class="fas fa-graduation-cap me-2"></i>{{ config('app.name') }}
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ url('/') }}">{{ translate('Accueil') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('webinar.list') }}">{{ translate('Webinaires') }}</a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('student.dashboard') }}">{{ translate('Dashboard') }}</a>
                        </li>
                        <li class="nav-item">
                            <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="nav-link btn btn-link">{{ translate('Déconnexion') }}</button>
                            </form>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">{{ translate('Connexion') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register.page') }}">{{ translate('Inscription') }}</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center position-relative">
                    <!-- Badge de statut -->
                    <div class="status-badge">
                        @switch($webinar->status)
                            @case('published')
                                <span class="badge bg-success fs-6">
                                    <i class="fas fa-eye me-1"></i>{{ translate('Publié') }}
                                </span>
                                @break
                            @case('live')
                                <span class="badge bg-danger fs-6">
                                    <i class="fas fa-broadcast-tower me-1"></i>{{ translate('En direct') }}
                                </span>
                                @break
                            @case('completed')
                                <span class="badge bg-info fs-6">
                                    <i class="fas fa-check-circle me-1"></i>{{ translate('Terminé') }}
                                </span>
                                @break
                            @default
                                <span class="badge bg-secondary fs-6">{{ translate('Programmé') }}</span>
                        @endswitch

                        @if($webinar->is_featured)
                            <span class="badge bg-warning ms-2 fs-6">
                                <i class="fas fa-star me-1"></i>{{ translate('Vedette') }}
                            </span>
                        @endif
                    </div>

                    <h1 class="display-4 fw-bold mb-4">{{ $webinar->title }}</h1>
                    <p class="lead mb-4">{{ $webinar->short_description }}</p>

                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <i class="fas fa-calendar fa-2x me-3"></i>
                                <div>
                                    <h5 class="mb-0">{{ $webinar->start_date->format('d/m/Y') }}</h5>
                                    <small>{{ translate('Date') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <i class="fas fa-clock fa-2x me-3"></i>
                                <div>
                                    <h5 class="mb-0">{{ $webinar->start_date->format('H:i') }}</h5>
                                    <small>{{ translate('Heure') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <i class="fas fa-hourglass-half fa-2x me-3"></i>
                                <div>
                                    <h5 class="mb-0">{{ $webinar->duration }} min</h5>
                                    <small>{{ translate('Durée') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <i class="fas fa-users fa-2x me-3"></i>
                                <div>
                                    <h5 class="mb-0">{{ $webinar->current_participants ?? 0 }}/{{ $webinar->max_participants ?? '∞' }}</h5>
                                    <small>{{ translate('Participants') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Messages -->
    @if(session('success'))
        <div class="container mt-4">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="container mt-4">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <!-- Contenu Principal -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <!-- Contenu du webinaire -->
                <div class="col-lg-8">
                    <div class="info-card mb-4">
                        <h3 class="mb-3">
                            <i class="fas fa-info-circle me-2 text-primary"></i>{{ translate('À propos de ce webinaire') }}
                        </h3>
                        <div class="content">
                            {!! $webinar->description !!}
                        </div>
                    </div>

                    <!-- Informations de la réunion -->
                    @if($webinar->meeting_url && $isRegistered)
                        <div class="meeting-info">
                            <h4 class="mb-3">
                                <i class="fas fa-video me-2"></i>{{ translate('Informations de la Réunion') }}
                            </h4>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>{{ translate('Lien de la réunion') }} :</strong></p>
                                    <a href="{{ $webinar->meeting_url }}" target="_blank" class="btn btn-outline-primary">
                                        <i class="fas fa-external-link-alt me-1"></i>{{ translate('Rejoindre la réunion') }}
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>{{ translate('ID de la réunion') }} :</strong> {{ $webinar->meeting_id }}</p>
                                    <p><strong>{{ translate('Mot de passe') }} :</strong> {{ $webinar->meeting_password }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Webinaires similaires -->
                    @if($relatedWebinars->isNotEmpty())
                        <div class="info-card">
                            <h4 class="mb-3">
                                <i class="fas fa-video me-2 text-primary"></i>{{ translate('Webinaires Similaires') }}
                            </h4>
                            <div class="row">
                                @foreach($relatedWebinars as $related)
                                    <div class="col-md-6 mb-3">
                                        <div class="card">
                                            <div class="card-body">
                                                <h6 class="card-title">{{ $related->title }}</h6>
                                                <p class="card-text text-muted small">{{ Str::limit($related->short_description, 80) }}</p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">{{ $related->start_date->format('d/m/Y H:i') }}</small>
                                                    <a href="{{ route('webinar.detail', $related->slug) }}" class="btn btn-sm btn-outline-primary">
                                                        {{ translate('Voir') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Actions -->
                    <div class="info-card mb-4">
                        <h4 class="mb-3">
                            <i class="fas fa-ticket-alt me-2 text-primary"></i>{{ translate('Participation') }}
                        </h4>

                        @if($isRegistered)
                            <div class="text-center">
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i>{{ translate('Vous êtes inscrit à ce webinaire') }}
                                </div>
                                @if($webinar->status == 'live' && $webinar->meeting_url)
                                    <a href="{{ $webinar->meeting_url }}" target="_blank" class="btn btn-join w-100 mb-2">
                                        <i class="fas fa-play me-2"></i>{{ translate('Rejoindre maintenant') }}
                                    </a>
                                @endif
                                <form action="{{ route('webinar.cancel', $webinar->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger w-100"
                                            onclick="return confirm('{{ translate('Êtes-vous sûr de vouloir annuler votre inscription ?') }}')">
                                        <i class="fas fa-times me-2"></i>{{ translate('Annuler l\'inscription') }}
                                    </button>
                                </form>
                            </div>
                        @else
                            @if($webinar->status == 'published' && $webinar->start_date > now())
                                @auth
                                    <form action="{{ route('webinar.enroll', $webinar->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-enroll w-100">
                                            <i class="fas fa-user-plus me-2"></i>{{ translate('S\'inscrire gratuitement') }}
                                        </button>
                                    </form>
                                @else
                                    <div class="text-center">
                                        <p class="text-muted mb-3">{{ translate('Connectez-vous pour vous inscrire') }}</p>
                                        <a href="{{ route('login') }}" class="btn btn-primary w-100 mb-2">
                                            <i class="fas fa-sign-in-alt me-2"></i>{{ translate('Se connecter') }}
                                        </a>
                                        <a href="{{ route('register.page') }}" class="btn btn-outline-primary w-100">
                                            <i class="fas fa-user-plus me-2"></i>{{ translate('Créer un compte') }}
                                        </a>
                                    </div>
                                @endauth
                            @else
                                <div class="alert alert-info text-center">
                                    <i class="fas fa-info-circle me-2"></i>{{ translate('Ce webinaire n\'est pas disponible pour l\'inscription') }}
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- Instructeur -->
                    <div class="instructor-card">
                        <img src="{{ asset('placeholder-avatar.png') }}" alt="Instructeur" class="instructor-avatar mb-3">
                        <h5>{{ $webinar->instructor?->first_name ?? 'N/A' }} {{ $webinar->instructor?->last_name ?? '' }}</h5>
                        <p class="text-muted mb-3">{{ translate('Instructeur') }}</p>
                        <p class="small">{{ $webinar->instructor?->bio ?? 'Instructeur expérimenté dans son domaine.' }}</p>
                    </div>

                    <!-- Informations -->
                    <div class="info-card">
                        <h5 class="mb-3">
                            <i class="fas fa-info-circle me-2 text-primary"></i>{{ translate('Informations') }}
                        </h5>
                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <div class="border-end">
                                    <h6 class="text-primary">{{ $webinar->category?->title ?? 'N/A' }}</h6>
                                    <small class="text-muted">{{ translate('Catégorie') }}</small>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <h6 class="text-success">{{ translate('Gratuit') }}</h6>
                                <small class="text-muted">{{ translate('Prix') }}</small>
                            </div>
                            <div class="col-6">
                                <h6 class="text-info">{{ $webinar->is_recorded ? translate('Oui') : translate('Non') }}</h6>
                                <small class="text-muted">{{ translate('Enregistré') }}</small>
                            </div>
                            <div class="col-6">
                                <h6 class="text-warning">{{ $webinar->current_participants ?? 0 }}</h6>
                                <small class="text-muted">{{ translate('Inscrits') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>{{ config('app.name') }}</h5>
                    <p class="text-muted">{{ translate('Plateforme d\'apprentissage en ligne') }}</p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="text-muted">&copy; {{ date('Y') }} {{ config('app.name') }}. {{ translate('Tous droits réservés.') }}</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
