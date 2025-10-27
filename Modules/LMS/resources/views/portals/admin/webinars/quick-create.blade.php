<!-- Quick Create Webinar Modal -->
<div class="modal fade" id="quickCreateWebinarModal" tabindex="-1" aria-labelledby="quickCreateWebinarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="quickCreateWebinarModalLabel">Créer un Webinaire Rapide</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('webinars.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="quick_title" class="form-label">Titre du Webinaire *</label>
                                <input type="text" class="form-control" id="quick_title" name="title" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="quick_platform" class="form-label">Plateforme *</label>
                                <select class="form-select" id="quick_platform" name="platform" required>
                                    <option value="">Sélectionner</option>
                                    <option value="zoom">Zoom</option>
                                    <option value="teams">Microsoft Teams</option>
                                    <option value="google_meet">Google Meet</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="quick_start_date" class="form-label">Date de Début *</label>
                                <input type="datetime-local" class="form-control" id="quick_start_date" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="quick_duration" class="form-label">Durée (minutes)</label>
                                <input type="number" class="form-control" id="quick_duration" name="duration" value="60" min="15" max="480">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="quick_description" class="form-label">Description</label>
                        <textarea class="form-control" id="quick_description" name="description" rows="3"></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="quick_instructor" class="form-label">Instructeur *</label>
                                <select class="form-select" id="quick_instructor" name="instructor_id" required>
                                    <option value="">Sélectionner</option>
                                    @foreach($instructors as $instructor)
                                        <option value="{{ $instructor->id }}">{{ $instructor->first_name ?? 'Instructeur' }} {{ $instructor->last_name ?? '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="quick_participants" class="form-label">Participants Max</label>
                                <input type="number" class="form-control" id="quick_participants" name="max_participants" value="50" min="1">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="quick_is_free" name="is_free" value="1" checked>
                            <label class="form-check-label" for="quick_is_free">
                                Webinaire Gratuit
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Créer le Webinaire</button>
                </div>
            </form>
        </div>
    </div>
</div>
