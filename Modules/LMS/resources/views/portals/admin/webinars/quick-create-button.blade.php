<!-- Quick Create Webinar Button -->
<div class="dropdown">
    <button class="btn btn-primary dropdown-toggle" type="button" id="webinarDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-video"></i> Créer un Webinaire
    </button>
    <ul class="dropdown-menu" aria-labelledby="webinarDropdown">
        <li>
            <a class="dropdown-item" href="{{ route('webinars.create') }}">
                <i class="fas fa-plus"></i> Créer un Webinaire Complet
            </a>
        </li>
        <li>
            <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#quickCreateWebinarModal">
                <i class="fas fa-bolt"></i> Création Rapide
            </button>
        </li>
        <li><hr class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item" href="{{ route('webinars.index') }}">
                <i class="fas fa-list"></i> Voir tous les Webinaires
            </a>
        </li>
    </ul>
</div>
