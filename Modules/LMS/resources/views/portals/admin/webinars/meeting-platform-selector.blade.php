<!-- Meeting Platform Selector Modal -->
<div class="modal fade" id="meetingPlatformModal" tabindex="-1" aria-labelledby="meetingPlatformModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="meetingPlatformModalLabel">
                    <i class="fas fa-video me-2"></i>{{ translate('Choisir la Plateforme de Réunion') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="meetingPlatformForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">{{ translate('Plateforme de Réunion') }}</label>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card platform-card" data-platform="teams">
                                    <div class="card-body text-center">
                                        <i class="fab fa-microsoft text-primary" style="font-size: 2rem;"></i>
                                        <h6 class="card-title mt-2">{{ translate('Microsoft Teams') }}</h6>
                                        <small class="text-muted">{{ translate('Intégration native') }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card platform-card" data-platform="zoom">
                                    <div class="card-body text-center">
                                        <i class="fas fa-video text-info" style="font-size: 2rem;"></i>
                                        <h6 class="card-title mt-2">{{ translate('Zoom') }}</h6>
                                        <small class="text-muted">{{ translate('Nécessite API Zoom') }}</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card platform-card" data-platform="google_meet">
                                    <div class="card-body text-center">
                                        <i class="fab fa-google text-success" style="font-size: 2rem;"></i>
                                        <h6 class="card-title mt-2">{{ translate('Google Meet') }}</h6>
                                        <small class="text-muted">{{ translate('Lien direct') }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="platform" id="selectedPlatform" value="teams">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ translate('Options Avancées') }}</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="waiting_room" id="waitingRoom" checked>
                            <label class="form-check-label" for="waitingRoom">
                                {{ translate('Salle d\'attente activée') }}
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="recording" id="recording">
                            <label class="form-check-label" for="recording">
                                {{ translate('Enregistrement automatique') }}
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="chat" id="chat" checked>
                            <label class="form-check-label" for="chat">
                                {{ translate('Chat activé') }}
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ translate('Annuler') }}
                </button>
                <button type="button" class="btn btn-primary" id="generateMeetingLink">
                    <i class="fas fa-link me-2"></i>{{ translate('Générer le Lien') }}
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.platform-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.platform-card:hover {
    border-color: #007bff;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.platform-card.selected {
    border-color: #007bff;
    background-color: #f8f9fa;
}

.platform-card i {
    transition: transform 0.3s ease;
}

.platform-card:hover i {
    transform: scale(1.1);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const platformCards = document.querySelectorAll('.platform-card');
    const selectedPlatformInput = document.getElementById('selectedPlatform');
    const generateBtn = document.getElementById('generateMeetingLink');

    // Platform selection
    platformCards.forEach(card => {
        card.addEventListener('click', function() {
            // Remove selected class from all cards
            platformCards.forEach(c => c.classList.remove('selected'));

            // Add selected class to clicked card
            this.classList.add('selected');

            // Update hidden input
            selectedPlatformInput.value = this.dataset.platform;
        });
    });

    // Generate meeting link
    generateBtn.addEventListener('click', function() {
        const platform = selectedPlatformInput.value;
        const webinarId = this.dataset.webinarId;

        if (!webinarId) {
            alert('ID du webinaire manquant');
            return;
        }

        // Show loading state
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Génération en cours...';
        this.disabled = true;

        // Make AJAX request to generate meeting link
        fetch(`/admin/webinars/${webinarId}/generate-meeting-link`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                platform: platform,
                options: {
                    waiting_room: document.getElementById('waitingRoom').checked,
                    recording: document.getElementById('recording').checked,
                    chat: document.getElementById('chat').checked
                }
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close modal and reload page
                bootstrap.Modal.getInstance(document.getElementById('meetingPlatformModal')).hide();
                location.reload();
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue');
        })
        .finally(() => {
            // Reset button
            this.innerHTML = '<i class="fas fa-link me-2"></i>Générer le Lien';
            this.disabled = false;
        });
    });
});
</script>




