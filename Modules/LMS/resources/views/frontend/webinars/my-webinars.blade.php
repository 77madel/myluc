<x-frontend-layout>
<!-- Breadcrumb Section Start -->
<section class="breadcrumb-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumb-content">
                    <h1 class="breadcrumb-title">Mes Webinaires</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home.index') }}">Accueil</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Mes Webinaires</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrumb Section End -->

<!-- My Webinars Section Start -->
<section class="my-webinars-section py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="my-webinars-header mb-4">
                    <h2>Mes Webinaires</h2>
                    <p>Gérez vos inscriptions aux webinaires</p>
                </div>

                @if($enrollments->count() > 0)
                    <div class="webinar-enrollments">
                        <div class="row">
                            @foreach($enrollments as $enrollment)
                                <div class="col-lg-6 col-xl-4 mb-4">
                                    <div class="enrollment-card">
                                        <div class="card h-100">
                                            <div class="webinar-image">
                                                @if($enrollment->webinar->image)
                                                    <img src="{{ Storage::url($enrollment->webinar->image) }}"
                                                         alt="{{ $enrollment->webinar->title }}" class="img-fluid">
                                                @else
                                                    <div class="placeholder-image">
                                                        <i class="fas fa-video"></i>
                                                    </div>
                                                @endif

                                                <!-- Status Badge -->
                                                <div class="status-badge">
                                                    @if($enrollment->webinar->isCurrentlyLive())
                                                        <span class="badge bg-danger">En direct</span>
                                                    @elseif($enrollment->webinar->hasEnded())
                                                        <span class="badge bg-secondary">Terminé</span>
                                                    @else
                                                        <span class="badge bg-success">À venir</span>
                                                    @endif
                                                </div>

                                                <!-- Enrollment Status -->
                                                <div class="enrollment-status-badge">
                                                    @if($enrollment->status === 'attended')
                                                        <span class="badge bg-success">Présent</span>
                                                    @elseif($enrollment->status === 'missed')
                                                        <span class="badge bg-warning">Absent</span>
                                                    @elseif($enrollment->status === 'cancelled')
                                                        <span class="badge bg-danger">Annulé</span>
                                                    @else
                                                        <span class="badge bg-info">Inscrit</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="card-body">
                                                <h5 class="card-title">
                                                    <a href="{{ route('webinar.detail', $enrollment->webinar->slug) }}">
                                                        {{ $enrollment->webinar->title }}
                                                    </a>
                                                </h5>

                                                <div class="webinar-meta mb-3">
                                                    <div class="meta-item">
                                                        <i class="fas fa-user"></i>
                                                        <span>{{ $enrollment->webinar->instructor->name }}</span>
                                                    </div>
                                                    <div class="meta-item">
                                                        <i class="fas fa-calendar"></i>
                                                        <span>{{ $enrollment->webinar->start_date->format('d/m/Y H:i') }}</span>
                                                    </div>
                                                    <div class="meta-item">
                                                        <i class="fas fa-clock"></i>
                                                        <span>{{ $enrollment->webinar->duration }} min</span>
                                                    </div>
                                                </div>

                                                <p class="card-text">
                                                    {{ Str::limit($enrollment->webinar->short_description ?? $enrollment->webinar->description, 100) }}
                                                </p>

                                                <div class="enrollment-info">
                                                    <small class="text-muted">
                                                        Inscrit le: {{ $enrollment->enrolled_at->format('d/m/Y H:i') }}
                                                    </small>
                                                    @if($enrollment->attended_at)
                                                        <br>
                                                        <small class="text-success">
                                                            Présent le: {{ $enrollment->attended_at->format('d/m/Y H:i') }}
                                                        </small>
                                                    @endif
                                                </div>

                                                <div class="enrollment-actions mt-3">
                                                    @if($enrollment->webinar->isCurrentlyLive() && $enrollment->status === 'enrolled')
                                                        <a href="{{ route('webinar.join', $enrollment->webinar->id) }}"
                                                           class="btn btn-danger btn-sm">
                                                            <i class="fas fa-play"></i> Rejoindre
                                                        </a>
                                                    @elseif($enrollment->webinar->hasEnded() && $enrollment->status === 'enrolled')
                                                        <button class="btn btn-warning btn-sm" disabled>
                                                            <i class="fas fa-times"></i> Terminé
                                                        </button>
                                                    @elseif($enrollment->status === 'enrolled' && !$enrollment->webinar->hasEnded())
                                                        <button class="btn btn-outline-danger btn-sm"
                                                                onclick="cancelEnrollment({{ $enrollment->webinar->id }})">
                                                            <i class="fas fa-times"></i> Annuler
                                                        </button>
                                                    @endif

                                                    <a href="{{ route('webinar.detail', $enrollment->webinar->slug) }}"
                                                       class="btn btn-outline-primary btn-sm">
                                                        <i class="fas fa-eye"></i> Voir
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        @if($enrollments->hasPages())
                            <div class="pagination-wrapper mt-4">
                                {{ $enrollments->links() }}
                            </div>
                        @endif
                    </div>
                @else
                    <div class="empty-state text-center py-5">
                        <i class="fas fa-video fa-3x text-muted mb-3"></i>
                        <h4>Aucun webinaire inscrit</h4>
                        <p class="text-muted">Vous n'êtes inscrit à aucun webinaire pour le moment.</p>
                        <a href="{{ route('webinar.list') }}" class="btn btn-primary">
                            Découvrir les webinaires
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
<!-- My Webinars Section End -->
@push('meta')
    <title>Mes Webinaires</title>
@endpush

</x-frontend-layout>

@push('scripts')
<script>
function cancelEnrollment(webinarId) {
    if (!confirm('Êtes-vous sûr de vouloir annuler votre inscription à ce webinaire ?')) {
        return;
    }

    fetch(`/webinars/${webinarId}/cancel`, {
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
.my-webinars-section {
    background: #f8f9fa;
}

.my-webinars-header {
    text-align: center;
}

.enrollment-card .card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.enrollment-card .card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.webinar-image {
    position: relative;
    height: 200px;
    overflow: hidden;
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
    font-size: 3rem;
    color: #6c757d;
}

.status-badge {
    position: absolute;
    top: 10px;
    left: 10px;
}

.enrollment-status-badge {
    position: absolute;
    top: 10px;
    right: 10px;
}

.webinar-meta {
    font-size: 0.9rem;
    color: #6c757d;
}

.meta-item {
    display: flex;
    align-items: center;
    margin-bottom: 0.25rem;
}

.meta-item i {
    margin-right: 0.5rem;
    color: #007bff;
    width: 16px;
}

.enrollment-info {
    border-top: 1px solid #e9ecef;
    padding-top: 0.75rem;
    margin-top: 0.75rem;
}

.enrollment-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.enrollment-actions .btn {
    flex: 1;
    min-width: 80px;
}

.empty-state {
    padding: 3rem 0;
}

@media (max-width: 768px) {
    .enrollment-actions {
        flex-direction: column;
    }

    .enrollment-actions .btn {
        flex: none;
    }
}
</style>
@endpush
