<x-frontend-layout>
<!-- Breadcrumb Section Start -->
<section class="breadcrumb-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumb-content">
                    <h1 class="breadcrumb-title">{{ $webinar->title }}</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home.index') }}">Accueil</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('webinar.list') }}">Webinaires</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $webinar->title }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrumb Section End -->

<!-- Webinar Detail Section Start -->
<section class="webinar-detail-section py-5">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <div class="webinar-detail">
                    <!-- Webinar Header -->
                    <div class="webinar-header mb-4">
                        <div class="webinar-image">
                            @if($webinar->image)
                                <img src="{{ Storage::url($webinar->image) }}"
                                     alt="{{ $webinar->title }}" class="img-fluid">
                            @else
                                <div class="placeholder-image">
                                    <i class="fas fa-video"></i>
                                </div>
                            @endif

                            <!-- Status Badge -->
                            <div class="status-badge">
                                @if($webinar->isCurrentlyLive())
                                    <span class="badge bg-danger">En direct</span>
                                @elseif($webinar->hasEnded())
                                    <span class="badge bg-secondary">Terminé</span>
                                @else
                                    <span class="badge bg-success">À venir</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Webinar Info -->
                    <div class="webinar-info mb-4">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <i class="fas fa-user"></i>
                                    <span><strong>Instructeur:</strong> {{ $webinar->instructor->name }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <i class="fas fa-calendar"></i>
                                    <span><strong>Date:</strong> {{ $webinar->start_date->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <i class="fas fa-clock"></i>
                                    <span><strong>Durée:</strong> {{ $webinar->duration }} minutes</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <i class="fas fa-users"></i>
                                    <span><strong>Participants:</strong> {{ $webinar->current_participants }}
                                    @if($webinar->max_participants)
                                        / {{ $webinar->max_participants }}
                                    @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="webinar-description mb-4">
                        <h3>Description</h3>
                        <p>{{ $webinar->description }}</p>
                    </div>

                    <!-- Requirements -->
                    @if($webinar->requirements && count($webinar->requirements) > 0)
                        <div class="webinar-requirements mb-4">
                            <h3>Prérequis</h3>
                            <ul>
                                @foreach($webinar->requirements as $requirement)
                                    <li>{{ $requirement }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Learning Outcomes -->
                    @if($webinar->learning_outcomes && count($webinar->learning_outcomes) > 0)
                        <div class="webinar-outcomes mb-4">
                            <h3>Objectifs d'apprentissage</h3>
                            <ul>
                                @foreach($webinar->learning_outcomes as $outcome)
                                    <li>{{ $outcome }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Tags -->
                    @if($webinar->tags && count($webinar->tags) > 0)
                        <div class="webinar-tags mb-4">
                            <h3>Tags</h3>
                            <div class="tag-list">
                                @foreach($webinar->tags as $tag)
                                    <span class="tag">{{ $tag }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="webinar-sidebar">
                    <!-- Enrollment Card -->
                    <div class="enrollment-card mb-4">
                        <div class="card">
                            <div class="card-body">
                                <div class="price-section mb-3">
                                    @if($webinar->is_free)
                                        <div class="price-free">
                                            <span class="price-amount">Gratuit</span>
                                        </div>
                                    @else
                                        <div class="price-paid">
                                            <span class="price-amount">{{ number_format($webinar->price, 0) }} FCFA</span>
                                        </div>
                                    @endif
                                </div>

                                @auth
                                    @if($isEnrolled)
                                        <div class="enrollment-status">
                                            <div class="alert alert-success">
                                                <i class="fas fa-check-circle"></i>
                                                Vous êtes inscrit à ce webinaire
                                            </div>

                                            @if($webinar->isCurrentlyLive())
                                                <a href="{{ route('webinar.join', $webinar->id) }}"
                                                   class="btn btn-danger btn-lg w-100">
                                                    <i class="fas fa-play"></i> Rejoindre maintenant
                                                </a>
                                            @elseif($webinar->hasEnded())
                                                <div class="alert alert-info">
                                                    Ce webinaire est terminé
                                                </div>
                                            @else
                                                <div class="alert alert-info">
                                                    Le webinaire commencera le {{ $webinar->start_date->format('d/m/Y à H:i') }}
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        @if($webinar->isAvailableForEnrollment())
                                            <button class="btn btn-primary btn-lg w-100"
                                                    onclick="enrollWebinar({{ $webinar->id }})">
                                                <i class="fas fa-user-plus"></i> S'inscrire
                                            </button>
                                        @else
                                            <div class="alert alert-warning">
                                                Ce webinaire n'est plus disponible pour l'inscription
                                            </div>
                                        @endif
                                    @endif
                                @else
                                    <div class="login-required">
                                        <p class="text-center mb-3">Vous devez être connecté pour vous inscrire</p>
                                        <a href="{{ route('login') }}" class="btn btn-primary w-100">
                                            Se connecter
                                        </a>
                                    </div>
                                @endauth
                            </div>
                        </div>
                    </div>

                    <!-- Instructor Info -->
                    <div class="instructor-card mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5>Instructeur</h5>
                            </div>
                            <div class="card-body">
                                <div class="instructor-info">
                                    <div class="instructor-avatar">
                                        @if($webinar->instructor->avatar)
                                            <img src="{{ Storage::url($webinar->instructor->avatar) }}"
                                                 alt="{{ $webinar->instructor->name }}" class="img-fluid">
                                        @else
                                            <div class="avatar-placeholder">
                                                {{ substr($webinar->instructor->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="instructor-details">
                                        <h6>{{ $webinar->instructor->name }}</h6>
                                        <p class="text-muted">{{ $webinar->instructor->designation ?? 'Instructeur' }}</p>
                                    </div>
                                </div>
                                <a href="{{ route('users.detail', $webinar->instructor->id) }}"
                                   class="btn btn-outline-primary btn-sm w-100 mt-2">
                                    Voir le profil
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Webinar Details -->
                    <div class="webinar-details-card">
                        <div class="card">
                            <div class="card-header">
                                <h5>Détails du webinaire</h5>
                            </div>
                            <div class="card-body">
                                <div class="detail-item">
                                    <i class="fas fa-calendar"></i>
                                    <span>Début: {{ $webinar->start_date->format('d/m/Y H:i') }}</span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-calendar"></i>
                                    <span>Fin: {{ $webinar->end_date->format('d/m/Y H:i') }}</span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-clock"></i>
                                    <span>Durée: {{ $webinar->duration }} minutes</span>
                                </div>
                                <div class="detail-item">
                                    <i class="fas fa-users"></i>
                                    <span>Participants: {{ $webinar->current_participants }}
                                    @if($webinar->max_participants)
                                        / {{ $webinar->max_participants }}
                                    @endif
                                    </span>
                                </div>
                                @if($webinar->category)
                                    <div class="detail-item">
                                        <i class="fas fa-tag"></i>
                                        <span>Catégorie: {{ $webinar->category->name }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Webinars -->
        @if($relatedWebinars->count() > 0)
            <div class="related-webinars mt-5">
                <h3>Webinaires similaires</h3>
                <div class="row">
                    @foreach($relatedWebinars as $relatedWebinar)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="related-webinar-card">
                                <div class="card">
                                    <div class="card-body">
                                        <h6 class="card-title">
                                            <a href="{{ route('webinar.detail', $relatedWebinar->slug) }}">
                                                {{ $relatedWebinar->title }}
                                            </a>
                                        </h6>
                                        <p class="card-text">
                                            {{ Str::limit($relatedWebinar->short_description ?? $relatedWebinar->description, 100) }}
                                        </p>
                                        <div class="webinar-meta">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar"></i>
                                                {{ $relatedWebinar->start_date->format('d/m/Y H:i') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
<!-- Webinar Detail Section End -->
@push('meta')
    <title>{{ $webinar->title }}</title>
@endpush

</x-frontend-layout>

@push('scripts')
<script>
function enrollWebinar(webinarId) {
    if (!confirm('Êtes-vous sûr de vouloir vous inscrire à ce webinaire ?')) {
        return;
    }

    fetch(`/webinars/${webinarId}/enroll`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Une erreur est survenue');
    });
}
</script>
@endpush

@push('styles')
<style>
.webinar-detail-section {
    background: #f8f9fa;
}

.webinar-image {
    position: relative;
    height: 300px;
    overflow: hidden;
    border-radius: 8px;
}

.webinar-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.placeholder-image {
    width: 100%;
    height: 100%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 4rem;
    color: #6c757d;
}

.status-badge {
    position: absolute;
    top: 15px;
    left: 15px;
}

.webinar-info {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.info-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.5rem;
}

.info-item i {
    margin-right: 0.5rem;
    color: #007bff;
    width: 20px;
}

.webinar-description,
.webinar-requirements,
.webinar-outcomes {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.webinar-tags {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.tag-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.tag {
    background: #e9ecef;
    color: #495057;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.875rem;
}

.enrollment-card .card {
    border: none;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.price-section {
    text-align: center;
}

.price-amount {
    font-size: 2rem;
    font-weight: bold;
    color: #28a745;
}

.price-paid .price-amount {
    color: #007bff;
}

.instructor-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.instructor-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    overflow: hidden;
}

.instructor-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    background: #007bff;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2rem;
}

.detail-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.75rem;
}

.detail-item i {
    margin-right: 0.75rem;
    color: #007bff;
    width: 20px;
}

.related-webinar-card .card {
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.related-webinar-card .card:hover {
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

@media (max-width: 768px) {
    .webinar-image {
        height: 200px;
    }

    .instructor-info {
        flex-direction: column;
        text-align: center;
    }
}
</style>
@endpush
