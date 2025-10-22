<!-- Quick Create Webinar Widget -->
<div class="col-lg-4 col-md-6 mb-4">
    <div class="card h-100 border-primary">
        <div class="card-body text-center">
            <div class="mb-3">
                <i class="fas fa-video fa-3x text-primary"></i>
            </div>
            <h5 class="card-title">Créer un Webinaire</h5>
            <p class="card-text text-muted">Organisez des webinaires avec vos participants via Zoom, Teams ou Google Meet</p>
            <div class="d-grid gap-2">
                <a href="{{ route('webinar.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Créer un Webinaire
                </a>
                <a href="{{ route('webinar.list') }}" class="btn btn-outline-primary">
                    <i class="fas fa-list"></i> Voir tous les Webinaires
                </a>
            </div>
        </div>
    </div>
</div>

