<x-frontend-layout>
<!-- Breadcrumb Section Start -->
<section class="breadcrumb-section">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumb-content">
                    <h1 class="breadcrumb-title">Rejoindre le Webinaire</h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home.index') }}">Accueil</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('webinar.list') }}">Webinaires</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('webinar.detail', $webinar->slug) }}">{{ $webinar->title }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Rejoindre</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Breadcrumb Section End -->

<!-- Join Webinar Section Start -->
<section class="join-webinar-section py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="join-webinar-container">
                    <!-- Webinar Info -->
                    <div class="webinar-info-header mb-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h2>{{ $webinar->title }}</h2>
                                <div class="webinar-meta">
                                    <div class="meta-item">
                                        <i class="fas fa-user"></i>
                                        <span>{{ $webinar->instructor->name }}</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-calendar"></i>
                                        <span>{{ $webinar->start_date->format('d/m/Y H:i') }}</span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-clock"></i>
                                        <span>{{ $webinar->duration }} minutes</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="live-indicator">
                                    <div class="live-dot"></div>
                                    <span>En direct</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Meeting Container -->
                    <div class="meeting-container">
                        <div class="row">
                            <!-- Video/Audio Area -->
                            <div class="col-lg-8">
                                <div class="meeting-main">
                                    <div class="meeting-video">
                                        @if($webinar->meeting_url)
                                            <div class="meeting-iframe">
                                                <iframe src="{{ $webinar->meeting_url }}"
                                                        width="100%"
                                                        height="500"
                                                        frameborder="0"
                                                        allowfullscreen>
                                                </iframe>
                                            </div>
                                        @else
                                            <div class="meeting-placeholder">
                                                <div class="placeholder-content">
                                                    <i class="fas fa-video fa-3x"></i>
                                                    <h4>Webinaire en cours</h4>
                                                    <p>Le webinaire est en cours de diffusion</p>
                                                    @if($webinar->meeting_id)
                                                        <div class="meeting-info">
                                                            <p><strong>ID de réunion:</strong> {{ $webinar->meeting_id }}</p>
                                                            @if($webinar->meeting_password)
                                                                <p><strong>Mot de passe:</strong> {{ $webinar->meeting_password }}</p>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Meeting Controls -->
                                    <div class="meeting-controls">
                                        <div class="control-buttons">
                                            <button class="btn btn-outline-secondary" id="toggleMic">
                                                <i class="fas fa-microphone"></i>
                                            </button>
                                            <button class="btn btn-outline-secondary" id="toggleVideo">
                                                <i class="fas fa-video"></i>
                                            </button>
                                            <button class="btn btn-outline-secondary" id="toggleChat">
                                                <i class="fas fa-comments"></i>
                                            </button>
                                            <button class="btn btn-outline-secondary" id="toggleScreen">
                                                <i class="fas fa-desktop"></i>
                                            </button>
                                        </div>
                                        <div class="meeting-status">
                                            <span class="status-indicator">
                                                <div class="status-dot"></div>
                                                Connecté
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sidebar -->
                            <div class="col-lg-4">
                                <div class="meeting-sidebar">
                                    <!-- Participants -->
                                    <div class="participants-section">
                                        <h5>
                                            <i class="fas fa-users"></i>
                                            Participants ({{ $webinar->current_participants }})
                                        </h5>
                                        <div class="participants-list">
                                            <div class="participant-item">
                                                <div class="participant-avatar">
                                                    <img src="{{ $webinar->instructor->avatar ? Storage::url($webinar->instructor->avatar) : asset('images/default-avatar.png') }}"
                                                         alt="{{ $webinar->instructor->name }}">
                                                </div>
                                                <div class="participant-info">
                                                    <span class="participant-name">{{ $webinar->instructor->name }}</span>
                                                    <span class="participant-role">Instructeur</span>
                                                </div>
                                                <div class="participant-status">
                                                    <i class="fas fa-microphone text-success"></i>
                                                </div>
                                            </div>

                                            <!-- Current User -->
                                            <div class="participant-item">
                                                <div class="participant-avatar">
                                                    <img src="{{ auth()->user()->avatar ? Storage::url(auth()->user()->avatar) : asset('images/default-avatar.png') }}"
                                                         alt="{{ auth()->user()->name }}">
                                                </div>
                                                <div class="participant-info">
                                                    <span class="participant-name">{{ auth()->user()->name }}</span>
                                                    <span class="participant-role">Vous</span>
                                                </div>
                                                <div class="participant-status">
                                                    <i class="fas fa-microphone-slash text-muted"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Chat -->
                                    <div class="chat-section">
                                        <h5>
                                            <i class="fas fa-comments"></i>
                                            Chat
                                        </h5>
                                        <div class="chat-messages" id="chatMessages">
                                            <div class="chat-message">
                                                <div class="message-header">
                                                    <span class="sender">{{ $webinar->instructor->name }}</span>
                                                    <span class="time">{{ now()->format('H:i') }}</span>
                                                </div>
                                                <div class="message-content">
                                                    Bienvenue dans ce webinaire ! N'hésitez pas à poser vos questions.
                                                </div>
                                            </div>
                                        </div>
                                        <div class="chat-input">
                                            <div class="input-group">
                                                <input type="text" class="form-control" placeholder="Tapez votre message..." id="chatInput">
                                                <button class="btn btn-primary" type="button" id="sendMessage">
                                                    <i class="fas fa-paper-plane"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Webinar Info -->
                                    <div class="webinar-details">
                                        <h5>
                                            <i class="fas fa-info-circle"></i>
                                            Informations
                                        </h5>
                                        <div class="details-list">
                                            <div class="detail-item">
                                                <i class="fas fa-clock"></i>
                                                <span>Début: {{ $webinar->start_date->format('H:i') }}</span>
                                            </div>
                                            <div class="detail-item">
                                                <i class="fas fa-clock"></i>
                                                <span>Fin: {{ $webinar->end_date->format('H:i') }}</span>
                                            </div>
                                            <div class="detail-item">
                                                <i class="fas fa-users"></i>
                                                <span>{{ $webinar->current_participants }} participants</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="webinar-actions mt-4">
                        <div class="row">
                            <div class="col-md-6">
                                <button class="btn btn-success btn-lg w-100" onclick="markAttendance()">
                                    <i class="fas fa-check"></i>
                                    Marquer ma présence
                                </button>
                            </div>
                            <div class="col-md-6">
                                <a href="{{ route('webinar.detail', $webinar->slug) }}"
                                   class="btn btn-outline-secondary btn-lg w-100">
                                    <i class="fas fa-arrow-left"></i>
                                    Retour aux détails
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Join Webinar Section End -->
@push('meta')
    <title>Rejoindre le Webinaire</title>
@endpush

</x-frontend-layout>

@push('scripts')
<script>
// Meeting controls
document.getElementById('toggleMic').addEventListener('click', function() {
    this.classList.toggle('active');
    const icon = this.querySelector('i');
    if (this.classList.contains('active')) {
        icon.className = 'fas fa-microphone';
    } else {
        icon.className = 'fas fa-microphone-slash';
    }
});

document.getElementById('toggleVideo').addEventListener('click', function() {
    this.classList.toggle('active');
    const icon = this.querySelector('i');
    if (this.classList.contains('active')) {
        icon.className = 'fas fa-video';
    } else {
        icon.className = 'fas fa-video-slash';
    }
});

// Chat functionality
document.getElementById('sendMessage').addEventListener('click', function() {
    const input = document.getElementById('chatInput');
    const message = input.value.trim();

    if (message) {
        addChatMessage('{{ auth()->user()->name }}', message);
        input.value = '';
    }
});

document.getElementById('chatInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        document.getElementById('sendMessage').click();
    }
});

function addChatMessage(sender, message) {
    const chatMessages = document.getElementById('chatMessages');
    const messageDiv = document.createElement('div');
    messageDiv.className = 'chat-message';

    const now = new Date();
    const time = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');

    messageDiv.innerHTML = `
        <div class="message-header">
            <span class="sender">${sender}</span>
            <span class="time">${time}</span>
        </div>
        <div class="message-content">${message}</div>
    `;

    chatMessages.appendChild(messageDiv);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

function markAttendance() {
    if (confirm('Marquer votre présence à ce webinaire ?')) {
        fetch(`/webinars/{{ $webinar->id }}/attendance`, {
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
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Une erreur est survenue');
        });
    }
}

// Auto-refresh participants count every 30 seconds
setInterval(function() {
    // This would typically fetch updated participant count from the server
    // For now, we'll just update the display
    console.log('Refreshing participant count...');
}, 30000);
</script>
@endpush

@push('styles')
<style>
.join-webinar-section {
    background: #f8f9fa;
}

.join-webinar-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    overflow: hidden;
}

.webinar-info-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    margin: -1rem -1rem 0 -1rem;
}

.webinar-meta {
    display: flex;
    gap: 2rem;
    margin-top: 1rem;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.live-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(255,255,255,0.2);
    padding: 0.5rem 1rem;
    border-radius: 20px;
}

.live-dot {
    width: 8px;
    height: 8px;
    background: #ff4444;
    border-radius: 50%;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.meeting-container {
    padding: 2rem;
}

.meeting-video {
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.meeting-iframe {
    width: 100%;
    height: 500px;
}

.meeting-placeholder {
    height: 500px;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
}

.placeholder-content i {
    color: #6c757d;
    margin-bottom: 1rem;
}

.meeting-info {
    background: white;
    padding: 1rem;
    border-radius: 8px;
    margin-top: 1rem;
    text-align: left;
}

.meeting-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
}

.control-buttons {
    display: flex;
    gap: 0.5rem;
}

.control-buttons .btn {
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.control-buttons .btn.active {
    background: #007bff;
    color: white;
}

.status-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #28a745;
}

.status-dot {
    width: 8px;
    height: 8px;
    background: #28a745;
    border-radius: 50%;
}

.meeting-sidebar {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1.5rem;
    height: 500px;
    overflow-y: auto;
}

.participants-section,
.chat-section,
.webinar-details {
    margin-bottom: 2rem;
}

.participants-section h5,
.chat-section h5,
.webinar-details h5 {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 1rem;
    color: #333;
}

.participant-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 0;
    border-bottom: 1px solid #e9ecef;
}

.participant-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    overflow: hidden;
}

.participant-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.participant-info {
    flex: 1;
}

.participant-name {
    font-weight: 500;
    display: block;
}

.participant-role {
    font-size: 0.8rem;
    color: #6c757d;
}

.participant-status i {
    font-size: 0.9rem;
}

.chat-messages {
    height: 200px;
    overflow-y: auto;
    background: white;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.chat-message {
    margin-bottom: 1rem;
}

.message-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.25rem;
}

.sender {
    font-weight: 500;
    color: #333;
}

.time {
    font-size: 0.8rem;
    color: #6c757d;
}

.message-content {
    background: #f8f9fa;
    padding: 0.5rem;
    border-radius: 8px;
    font-size: 0.9rem;
}

.chat-input .input-group {
    margin-bottom: 0;
}

.details-list {
    background: white;
    border-radius: 8px;
    padding: 1rem;
}

.detail-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.5rem;
}

.detail-item i {
    color: #007bff;
    width: 16px;
}

.webinar-actions {
    padding: 0 2rem 2rem;
}

@media (max-width: 768px) {
    .webinar-meta {
        flex-direction: column;
        gap: 0.5rem;
    }

    .meeting-sidebar {
        height: auto;
        margin-top: 1rem;
    }

    .meeting-iframe,
    .meeting-placeholder {
        height: 300px;
    }
}
</style>
@endpush
