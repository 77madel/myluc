<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ translate('Webinaires') }} - {{ config('app.name') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 80px 0;
        }
        .webinar-card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .webinar-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        .status-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 10;
        }
        .instructor-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }
        .filter-section {
            background: #f8f9fa;
            padding: 30px 0;
            margin-bottom: 40px;
        }
        .btn-enroll {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        .btn-enroll:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
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
                        <a class="nav-link active" href="{{ route('webinar.list') }}">{{ translate('Webinaires') }}</a>
                    </li>
                    @auth
                        <li class="nav-item">
                            @if(isStudent())
                                <a class="nav-link" href="{{ route('student.dashboard') }}">{{ translate('Dashboard') }}</a>
                            @elseif(isInstructor())
                                <a class="nav-link" href="{{ route('instructor.dashboard') }}">{{ translate('Dashboard') }}</a>
                            @elseif(isOrganization())
                                <a class="nav-link" href="{{ route('organization.dashboard') }}">{{ translate('Dashboard') }}</a>
                            @endif
                        </li>
                        <li class="nav-item">
                            <form action="@if(isStudent()){{ route('student.logout') }}@elseif(isInstructor()){{ route('instructor.logout') }}@elseif(isOrganization()){{ route('organization.logout') }}@endif" method="POST" class="d-inline">
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
    <section class="hero-section text-center">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <h1 class="display-4 fw-bold mb-4">
                        <i class="fas fa-video me-3"></i>{{ translate('Webinaires en Ligne') }}
                    </h1>
                    <p class="lead mb-4">{{ translate('Découvrez nos webinaires gratuits et apprenez avec des experts') }}</p>
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <i class="fas fa-users fa-2x me-3"></i>
                                <div>
                                    <h4 class="mb-0">{{ $webinars->where('status', 'published')->sum('current_participants') }}</h4>
                                    <small>{{ translate('Participants') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <i class="fas fa-calendar fa-2x me-3"></i>
                                <div>
                                    <h4 class="mb-0">{{ $webinars->where('status', 'published')->count() }}</h4>
                                    <small>{{ translate('Webinaires') }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center justify-content-center mb-2">
                                <i class="fas fa-star fa-2x me-3"></i>
                                <div>
                                    <h4 class="mb-0">{{ $webinars->where('is_featured', true)->count() }}</h4>
                                    <small>{{ translate('En Vedette') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Filtres -->
    <section class="filter-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <form method="GET" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">{{ translate('Rechercher') }}</label>
                            <input type="text" name="search" class="form-control"
                                   placeholder="{{ translate('Titre du webinaire...') }}"
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ translate('Catégorie') }}</label>
                            <select name="category" class="form-select">
                                <option value="">{{ translate('Toutes les catégories') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{ translate('Statut') }}</label>
                            <select name="status" class="form-select">
                                <option value="">{{ translate('Tous') }}</option>
                                <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>{{ translate('À venir') }}</option>
                                <option value="live" {{ request('status') == 'live' ? 'selected' : '' }}>{{ translate('En direct') }}</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i>{{ translate('Filtrer') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Liste des Webinaires -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h2 class="text-center mb-5">
                        <i class="fas fa-calendar-alt me-2"></i>{{ translate('Nos Webinaires') }}
                    </h2>
                </div>
            </div>

            @if($webinars->count() > 0)
                <div class="row">
                    @foreach($webinars as $webinar)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card webinar-card h-100 position-relative">
                                <!-- Badge de statut -->
                                <div class="status-badge">
                                    @switch($webinar->status)
                                        @case('published')
                                            <span class="badge bg-success">
                                                <i class="fas fa-eye me-1"></i>{{ translate('Publié') }}
                                            </span>
                                            @break
                                        @case('live')
                                            <span class="badge bg-danger">
                                                <i class="fas fa-broadcast-tower me-1"></i>{{ translate('En direct') }}
                                            </span>
                                            @break
                                        @case('completed')
                                            <span class="badge bg-info">
                                                <i class="fas fa-check-circle me-1"></i>{{ translate('Terminé') }}
                                            </span>
                                            @break
                                        @default
                                            <span class="badge bg-secondary">{{ translate('Programmé') }}</span>
                                    @endswitch

                                    @if($webinar->is_featured)
                                        <span class="badge bg-warning ms-1">
                                            <i class="fas fa-star me-1"></i>{{ translate('Vedette') }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Image du webinaire -->
                                <div class="card-img-top" style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-video fa-3x text-white"></i>
                                </div>

                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">{{ $webinar->title }}</h5>
                                    <p class="card-text text-muted">{{ Str::limit($webinar->short_description, 100) }}</p>

                                    <!-- Instructeur -->
                                    <div class="d-flex align-items-center mb-3">
                                        <img src="{{ asset('placeholder-avatar.png') }}" alt="Instructeur" class="instructor-avatar me-2">
                                        <div>
                                            <small class="text-muted">{{ translate('Instructeur') }}</small>
                                            <div class="fw-semibold">
                                                {{ $webinar->instructor?->first_name ?? 'N/A' }} {{ $webinar->instructor?->last_name ?? '' }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Informations -->
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between mb-1">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>{{ $webinar->start_date->format('d/m/Y') }}
                                            </small>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>{{ $webinar->start_date->format('H:i') }}
                                            </small>
                                        </div>
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">
                                                <i class="fas fa-users me-1"></i>{{ $webinar->current_participants ?? 0 }}/{{ $webinar->max_participants ?? '∞' }}
                                            </small>
                                            <small class="text-success">
                                                <i class="fas fa-gift me-1"></i>{{ translate('Gratuit') }}
                                            </small>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="mt-auto">
                                        <div class="d-grid gap-2">
                                            <a href="{{ route('webinar.detail', $webinar->slug) }}" class="btn btn-outline-primary">
                                                <i class="fas fa-info-circle me-1"></i>{{ translate('Voir Détails') }}
                                            </a>
                                            @if($webinar->status == 'published' && $webinar->start_date > now())
                                                <a href="{{ route('webinar.enroll', $webinar->id) }}" class="btn btn-enroll">
                                                    <i class="fas fa-user-plus me-1"></i>{{ translate('S\'inscrire') }}
                                                </a>
                                            @elseif($webinar->status == 'live')
                                                <a href="{{ $webinar->meeting_url }}" target="_blank" class="btn btn-danger">
                                                    <i class="fas fa-play me-1"></i>{{ translate('Rejoindre') }}
                                                </a>
                                            @else
                                                <button class="btn btn-secondary" disabled>
                                                    <i class="fas fa-lock me-1"></i>{{ translate('Non disponible') }}
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($webinars->hasPages())
                    <div class="d-flex justify-content-center mt-5">
                        {{ $webinars->links() }}
                    </div>
                @endif
            @else
                <!-- État vide -->
                <div class="text-center py-5">
                    <i class="fas fa-video fa-4x text-muted mb-4"></i>
                    <h3 class="text-muted">{{ translate('Aucun webinaire disponible') }}</h3>
                    <p class="text-muted">{{ translate('Revenez bientôt pour découvrir nos prochains webinaires !') }}</p>
                </div>
            @endif
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
