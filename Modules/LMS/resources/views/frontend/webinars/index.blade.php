<x-frontend-layout>
<!-- Breadcrumb Section Start -->
<section class="breadcrumb-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumb-content">
                    <h1 class="breadcrumb-title">Webinaires</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home.index') }}">Accueil</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Webinaires</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrumb Section End -->

<!-- Webinar Section Start -->
<section class="webinar-section py-5">
    <div class="container">
        <div class="row">
            <!-- Filter Sidebar -->
            <div class="col-lg-3">
                <div class="filter-sidebar">
                    <h4>Filtres</h4>

                    <!-- Search -->
                    <div class="filter-group mb-4">
                        <h5>Recherche</h5>
                        <form method="GET" action="{{ route('webinars.index') }}">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control"
                                       value="{{ request('search') }}" placeholder="Rechercher...">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Category Filter -->
                    <div class="filter-group mb-4">
                        <h5>Catégorie</h5>
                        <form method="GET" action="{{ route('webinars.index') }}">
                            @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif
                            <select name="category" class="form-select" onchange="this.form.submit()">
                                <option value="">Toutes les catégories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                            {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                    <!-- Status Filter -->
                    <div class="filter-group mb-4">
                        <h5>Statut</h5>
                        <form method="GET" action="{{ route('webinars.index') }}">
                            @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif
                            @if(request('category'))
                                <input type="hidden" name="category" value="{{ request('category') }}">
                            @endif
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">Tous les statuts</option>
                                <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>
                                    À venir
                                </option>
                                <option value="live" {{ request('status') == 'live' ? 'selected' : '' }}>
                                    En direct
                                </option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>
                                    Terminés
                                </option>
                            </select>
                        </form>
                    </div>

                    <!-- Price Filter -->
                    <div class="filter-group mb-4">
                        <h5>Prix</h5>
                        <form method="GET" action="{{ route('webinars.index') }}">
                            @if(request('search'))
                                <input type="hidden" name="search" value="{{ request('search') }}">
                            @endif
                            @if(request('category'))
                                <input type="hidden" name="category" value="{{ request('category') }}">
                            @endif
                            @if(request('status'))
                                <input type="hidden" name="status" value="{{ request('status') }}">
                            @endif
                            <select name="price" class="form-select" onchange="this.form.submit()">
                                <option value="">Tous les prix</option>
                                <option value="free" {{ request('price') == 'free' ? 'selected' : '' }}>
                                    Gratuits
                                </option>
                                <option value="paid" {{ request('price') == 'paid' ? 'selected' : '' }}>
                                    Payants
                                </option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Webinar List -->
            <div class="col-lg-9">
                <div class="webinar-list">
                    <!-- Results Header -->
                    <div class="results-header mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h3>{{ $webinars->total() }} webinaire(s) trouvé(s)</h3>
                            </div>
                            <div class="col-md-6 text-end">
                                <div class="d-flex gap-2 justify-content-end">
                                    <div class="sort-options">
                                        <select class="form-select" onchange="window.location.href = this.value">
                                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'start_date']) }}">
                                                Trier par date
                                            </option>
                                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'title']) }}">
                                                Trier par titre
                                            </option>
                                            <option value="{{ request()->fullUrlWithQuery(['sort' => 'price']) }}">
                                                Trier par prix
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Webinar Cards -->
                    <div class="row">
                        @forelse($webinars as $webinar)
                            <div class="col-lg-6 col-xl-4 mb-4">
                                <div class="webinar-card h-100">
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

                                        <!-- Price Badge -->
                                        <div class="price-badge">
                                            @if($webinar->is_free)
                                                <span class="badge bg-success">Gratuit</span>
                                            @else
                                                <span class="badge bg-primary">{{ number_format($webinar->price, 0) }} FCFA</span>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="webinar-content">
                                        <div class="webinar-meta">
                                            <div class="instructor">
                                                <i class="fas fa-user"></i>
                                                {{ $webinar->instructor->name }}
                                            </div>
                                            <div class="date">
                                                <i class="fas fa-calendar"></i>
                                                {{ $webinar->start_date->format('d/m/Y H:i') }}
                                            </div>
                                        </div>

                                        <h4 class="webinar-title">
                                            <a href="{{ route('webinar.detail', $webinar->slug) }}">
                                                {{ $webinar->title }}
                                            </a>
                                        </h4>

                                        <p class="webinar-description">
                                            {{ Str::limit($webinar->short_description ?? $webinar->description, 100) }}
                                        </p>

                                        <div class="webinar-footer">
                                            <div class="participants">
                                                <i class="fas fa-users"></i>
                                                {{ $webinar->current_participants }}
                                                @if($webinar->max_participants)
                                                    / {{ $webinar->max_participants }}
                                                @endif
                                            </div>
                                            <div class="duration">
                                                <i class="fas fa-clock"></i>
                                                {{ $webinar->duration }} min
                                            </div>
                                        </div>

                                        <div class="webinar-actions">
                                            <a href="{{ route('webinar.detail', $webinar->slug) }}"
                                               class="btn btn-primary">
                                                Voir détails
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <div class="empty-state text-center py-5">
                                    <i class="fas fa-video fa-3x text-muted mb-3"></i>
                                    <h4>Aucun webinaire trouvé</h4>
                                    <p class="text-muted">Essayez de modifier vos critères de recherche.</p>
                                </div>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if($webinars->hasPages())
                        <div class="pagination-wrapper mt-4">
                            {{ $webinars->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Webinar Section End -->
@push('meta')
    <title>Webinaires</title>
@endpush

</x-frontend-layout>

@push('styles')
<style>
.webinar-card {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
    transition: all 0.3s ease;
    background: white;
}

.webinar-card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
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
    background: #f8f9fa;
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

.price-badge {
    position: absolute;
    top: 10px;
    right: 10px;
}

.webinar-content {
    padding: 1.5rem;
}

.webinar-meta {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
    font-size: 0.9rem;
    color: #6c757d;
}

.webinar-title {
    font-size: 1.2rem;
    margin-bottom: 0.5rem;
}

.webinar-title a {
    color: #333;
    text-decoration: none;
}

.webinar-title a:hover {
    color: #007bff;
}

.webinar-description {
    color: #6c757d;
    margin-bottom: 1rem;
}

.webinar-footer {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
    font-size: 0.9rem;
    color: #6c757d;
}

.filter-sidebar {
    background: #f8f9fa;
    padding: 1.5rem;
    border-radius: 8px;
    margin-bottom: 2rem;
}

.filter-group h5 {
    font-size: 1rem;
    margin-bottom: 0.5rem;
    color: #333;
}

.results-header {
    border-bottom: 1px solid #e9ecef;
    padding-bottom: 1rem;
}

.empty-state {
    padding: 3rem 0;
}

@media (max-width: 768px) {
    .webinar-meta {
        flex-direction: column;
        gap: 0.5rem;
    }

    .webinar-footer {
        flex-direction: column;
        gap: 0.5rem;
    }
}
</style>
@endpush
