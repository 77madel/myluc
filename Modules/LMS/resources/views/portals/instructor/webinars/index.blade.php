<x-dashboard-layout>
    <x-slot name="title">
        {{ translate('Webinaires') }}
    </x-slot>

    <x-portal::admin.breadcrumb>
        <x-slot name="title">{{ translate('Webinaires') }}</x-slot>
        <li class="breadcrumb-item">
            <a href="{{ route('instructor.dashboard') }}">{{ translate('Dashboard') }}</a>
        </li>
        <li class="breadcrumb-item active">{{ translate('Webinaires') }}</li>
    </x-portal::admin.breadcrumb>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- Webinars Grid -->
                <div class="card border-0 shadow-sm glass-card" style="border-radius: 24px; overflow: hidden; backdrop-filter: blur(10px);">
                    <div class="card-header bg-primary border-0 py-4 px-4">
                        <div class="relative p-6 md:p-8">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-6">
                                <div class="text-white">
                                    <h2 class="text-3xl font-bold mb-2 flex items-center">
                                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center mr-4">
                                            <i class="fas fa-video text-2xl"></i>
                                        </div>
                                        {{ translate('Gestion des Webinaires') }}
                                    </h2>
                                    <p class="text-white text-opacity-90 text-lg">
                                        {{ translate('Créez et gérez vos webinaires avec style') }}
                                    </p>
                                </div>
                                <div>
                                    <a href="{{ route('instructor.webinars.create') }}"
                                       class="inline-flex items-center gap-3 bg-white text-gray-800 px-6 py-3 rounded-xl font-semibold hover:bg-opacity-90 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                        <i class="fas fa-plus text-lg"></i>
                                        {{ translate('Créer un Webinaire') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body p-0">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show m-3 m-md-4 rounded-3 glass-alert" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check-circle me-3" style="font-size: 1.2rem;"></i>
                                    <div class="flex-grow-1">{{ session('success') }}</div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show m-3 m-md-4 rounded-3 glass-alert" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-exclamation-triangle me-3" style="font-size: 1.2rem;"></i>
                                    <div class="flex-grow-1">{{ session('error') }}</div>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            </div>
                        @endif

                        @if($webinars->count() > 0)
                            <div class="row g-3 p-3">
                                <style>
                                    .webinar-card {
                                        border-radius: 20px !important;
                                        overflow: hidden;
                                        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                                        border: 1px solid rgba(255,255,255,0.2);
                                        background: rgba(255,255,255,0.8);
                                        backdrop-filter: blur(10px);
                                        position: relative;
                                    }
                                    .webinar-card::before {
                                        content: '';
                                        position: absolute;
                                        top: 0;
                                        left: 0;
                                        right: 0;
                                        bottom: 0;
                                        background: linear-gradient(135deg, rgba(106,106,255,0.05) 0%, rgba(139,106,255,0.05) 100%);
                                        border-radius: 20px;
                                        z-index: -1;
                                    }
                                    .webinar-card:hover {
                                        transform: translateY(-8px) scale(1.02);
                                        box-shadow: 0 25px 50px rgba(106,106,255,0.15) !important;
                                        border-color: rgba(106,106,255,0.3);
                                    }
                                    .webinar-card:hover .card-img-top {
                                        transform: scale(1.1);
                                    }
                                    .webinar-card .card-img-top {
                                        transition: transform 0.4s ease;
                                    }
                                    .info-item {
                                        background: rgba(255,255,255,0.6);
                                        border-radius: 12px;
                                        padding: 12px;
                                        margin-bottom: 8px;
                                        border: 1px solid rgba(0,0,0,0.05);
                                        transition: all 0.3s ease;
                                        backdrop-filter: blur(5px);
                                    }
                                    .info-item:hover {
                                        background: rgba(255,255,255,0.8);
                                        transform: translateX(4px);
                                        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                                    }
                                    .icon-circle {
                                        width: 40px;
                                        height: 40px;
                                        border-radius: 50%;
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                                        transition: all 0.3s ease;
                                        background: linear-gradient(135deg, #6A6AFF 0%, #8B6AFF 100%);
                                    }
                                    .icon-circle:hover {
                                        transform: scale(1.1) rotate(5deg);
                                        box-shadow: 0 6px 20px rgba(106,106,255,0.4);
                                    }
                                    .btn-primary {
                                        background: linear-gradient(135deg, #6A6AFF 0%, #8B6AFF 100%) !important;
                                        border: none !important;
                                        transition: all 0.3s ease;
                                    }
                                    .btn-primary:hover {
                                        background: linear-gradient(135deg, #5A5AFF 0%, #7B5AFF 100%) !important;
                                        transform: translateY(-2px);
                                        box-shadow: 0 8px 25px rgba(106,106,255,0.4);
                                    }
                                    .dropdown-menu {
                                        border-radius: 16px !important;
                                        border: 1px solid rgba(255,255,255,0.2) !important;
                                        box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
                                        padding: 8px !important;
                                        background: rgba(255,255,255,0.95) !important;
                                        backdrop-filter: blur(10px);
                                        display: none !important;
                                    }
                                    .dropdown:hover .dropdown-menu {
                                        display: block !important;
                                        animation: slideUp 0.3s ease;
                                    }
                                    .dropdown-item {
                                        border-radius: 12px !important;
                                        margin: 2px 0 !important;
                                        padding: 12px 16px !important;
                                        transition: all 0.2s ease !important;
                                        font-weight: 500;
                                    }
                                    .dropdown-item:hover {
                                        background: linear-gradient(135deg, #6A6AFF 0%, #8B6AFF 100%) !important;
                                        color: white !important;
                                        transform: translateX(8px) !important;
                                    }
                                    .glass-card {
                                        background: rgba(255,255,255,0.8);
                                        backdrop-filter: blur(10px);
                                        border: 1px solid rgba(255,255,255,0.2);
                                    }
                                    .glass-alert {
                                        background: rgba(255,255,255,0.9);
                                        backdrop-filter: blur(10px);
                                        border: 1px solid rgba(255,255,255,0.3);
                                    }
                                    .icon-wrapper {
                                        background: linear-gradient(135deg, #6A6AFF 0%, #8B6AFF 100%);
                                        border-radius: 16px;
                                        padding: 16px;
                                        box-shadow: 0 8px 25px rgba(106,106,255,0.3);
                                    }
                                    .gradient-text {
                                        background: linear-gradient(135deg, #6A6AFF 0%, #8B6AFF 100%);
                                        -webkit-background-clip: text;
                                        -webkit-text-fill-color: transparent;
                                        background-clip: text;
                                    }
                                    .floating-action {
                                        animation: float 3s ease-in-out infinite;
                                    }
                                    @keyframes float {
                                        0%, 100% { transform: translateY(0px); }
                                        50% { transform: translateY(-5px); }
                                    }
                                    @keyframes slideUp {
                                        from { opacity: 0; transform: translateY(10px); }
                                        to { opacity: 1; transform: translateY(0); }
                                    }

                                    /* Responsive improvements */
                                    @media (max-width: 768px) {
                                        .webinar-card {
                                            margin-bottom: 1rem;
                                        }
                                        .card-header .d-flex {
                                            gap: 1rem !important;
                                        }
                                        .icon-wrapper {
                                            padding: 12px;
                                        }
                                        .icon-wrapper i {
                                            font-size: 1.5rem !important;
                                        }
                                    }

                                    @media (max-width: 576px) {
                                        .webinar-card:hover {
                                            transform: translateY(-4px) scale(1.01);
                                        }
                                        .info-item {
                                            padding: 10px;
                                        }
                                        .icon-circle {
                                            width: 32px;
                                            height: 32px;
                                        }
                                    }
                                </style>
                                @foreach($webinars as $webinar)
                                    <div class="col-12 col-sm-6 col-lg-4">
                                        <div class="card border-0 shadow-sm h-100 webinar-card">
                                            <!-- Webinar Image -->
                                            <div class="position-relative" style="height: 200px; overflow: hidden;">
                                                @if($webinar->image && Storage::exists($webinar->image))
                                                    <img src="{{ Storage::url($webinar->image) }}"
                                                         alt="{{ $webinar->title }}"
                                                         class="card-img-top"
                                                         style="height: 100%; object-fit: cover;"
                                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                    <div class="card-img-top d-flex align-items-center justify-content-center h-100"
                                                         style="background: linear-gradient(135deg, #6A6AFF 0%, #8B6AFF 100%); display: none;">
                                                        <i class="fas fa-video fa-3x text-white opacity-80"></i>
                                                    </div>
                                                @else
                                                    <div class="card-img-top d-flex align-items-center justify-content-center h-100"
                                                         style="background: linear-gradient(135deg, #6A6AFF 0%, #8B6AFF 100%);">
                                                        <i class="fas fa-video fa-3x text-white opacity-80"></i>
                                                    </div>
                                                @endif

                                                <!-- Status & Category Badges -->
                                                <div class="position-absolute top-0 start-0 m-3 d-flex flex-column" style="gap: 8px;">
                                                    <!-- Category Badge -->
                                                    <span class="badge rounded-pill px-3 py-2 shadow mb-2" style="font-size: 0.75rem; font-weight: 600; background: rgba(255,255,255,0.9); color: #6A6AFF; backdrop-filter: blur(10px);">
                                                        {{ $webinar->category->title ?? 'N/A' }}
                                                    </span>

                                                    <!-- Status Badge -->
                                                    @if($webinar->is_published)
                                                        <span class="badge rounded-pill px-3 py-2 shadow" style="font-size: 0.75rem; font-weight: 600; background: rgba(40, 167, 69, 0.9); color: white; backdrop-filter: blur(10px);">
                                                            <i class="fas fa-check-circle me-1"></i>
                                                            {{ translate('Publié') }}
                                                        </span>
                                                    @else
                                                        <span class="badge rounded-pill px-3 py-2 shadow" style="font-size: 0.75rem; font-weight: 600; background: rgba(255, 193, 7, 0.9); color: #000; backdrop-filter: blur(10px);">
                                                            <i class="fas fa-edit me-1"></i>
                                                            {{ translate('Brouillon') }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Card Body -->
                                            <div class="card-body d-flex flex-column p-4">
                                                <h5 class="card-title fw-bold text-dark mb-2" style="font-size: 1.1rem; line-height: 1.3;">{{ $webinar->title }}</h5>
                                                <p class="card-text text-muted mb-3" style="font-size: 0.9rem; line-height: 1.4;">{{ Str::limit($webinar->short_description, 100) }}</p>

                                                <!-- Webinar Info -->
                                                <div class="mb-3">
                                                    <div class="info-item">
                                                        <div class="d-flex align-items-center">
                                                            <div class="icon-circle me-3">
                                                                <i class="fas fa-calendar-alt text-white" style="font-size: 0.9rem;"></i>
                                                            </div>
                                                            <div>
                                                                <small class="text-muted d-block" style="font-size: 0.8rem; font-weight: 500;">{{ translate('Date et heure') }}</small>
                                                                <strong class="text-dark" style="font-size: 0.9rem;">
                                                                    {{ $webinar->start_date->format('d/m/Y') }} à {{ $webinar->start_date->format('H:i') }}
                                                                </strong>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="info-item">
                                                        <div class="d-flex align-items-center">
                                                            <div class="icon-circle me-3">
                                                                <i class="fas fa-clock text-white" style="font-size: 0.9rem;"></i>
                                                            </div>
                                                            <div>
                                                                <small class="text-muted d-block" style="font-size: 0.8rem; font-weight: 500;">{{ translate('Durée') }}</small>
                                                                <strong class="text-dark" style="font-size: 0.9rem;">{{ $webinar->duration }} {{ translate('minutes') }}</strong>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="info-item">
                                                        <div class="d-flex align-items-center">
                                                            <div class="icon-circle me-3">
                                                                <i class="fas fa-users text-white" style="font-size: 0.9rem;"></i>
                                                            </div>
                                                            <div>
                                                                <small class="text-muted d-block" style="font-size: 0.8rem; font-weight: 500;">{{ translate('Participants') }}</small>
                                                                <strong class="text-dark" style="font-size: 0.9rem;">
                                                                    {{ $webinar->current_participants }}/{{ $webinar->max_participants ?? '∞' }}
                                                                </strong>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Meeting Link -->
                                                @if($webinar->meeting_url)
                                                    <div class="mb-3">
                                                        <a href="{{ $webinar->meeting_url }}" target="_blank"
                                                           class="btn btn-primary btn-sm w-100 rounded-pill shadow-sm" style="font-weight: 600; padding: 12px 20px; font-size: 0.85rem;">
                                                            <i class="fas fa-external-link-alt me-2"></i>
                                                            {{ translate('Rejoindre la réunion') }}
                                                        </a>
                                                    </div>
                                                @endif

                                                <!-- Actions -->
                                                <div class="mt-auto">
                                                    <div class="dropdown">
                                                        <button class="btn btn-outline-secondary btn-sm w-100 dropdown-toggle rounded-pill shadow-sm" style="font-weight: 600; padding: 12px 20px; border: 1px solid rgba(106,106,255,0.2); background: rgba(255,255,255,0.8); font-size: 0.85rem;"
                                                                type="button"
                                                                data-bs-toggle="dropdown">
                                                            <i class="fas fa-ellipsis-v me-2"></i>
                                                            {{ translate('Actions') }}
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('instructor.webinars.show', $webinar->id) }}">
                                                                    <i class="fas fa-eye text-primary me-2"></i>
                                                                    {{ translate('Voir') }}
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('instructor.webinars.edit', $webinar->id) }}">
                                                                    <i class="fas fa-edit text-info me-2"></i>
                                                                    {{ translate('Modifier') }}
                                                                </a>
                                                            </li>
                                                            @if(!$webinar->is_published)
                                                                <li>
                                                                    <form action="{{ route('instructor.webinars.publish', $webinar->id) }}"
                                                                          method="POST" class="d-inline">
                                                                        @csrf
                                                                        <button type="submit" class="dropdown-item text-success">
                                                                            <i class="fas fa-check me-2"></i>
                                                                            {{ translate('Publier') }}
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            @else
                                                                <li>
                                                                    <form action="{{ route('instructor.webinars.unpublish', $webinar->id) }}"
                                                                          method="POST" class="d-inline">
                                                                        @csrf
                                                                        <button type="submit" class="dropdown-item text-warning">
                                                                            <i class="fas fa-times me-2"></i>
                                                                            {{ translate('Dépublier') }}
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            @endif
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <form action="{{ route('instructor.webinars.destroy', $webinar->id) }}"
                                                                      method="POST" class="d-inline"
                                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce webinaire ?')">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="dropdown-item text-danger">
                                                                        <i class="fas fa-trash me-2"></i>
                                                                        {{ translate('Supprimer') }}
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="d-flex justify-content-center mt-4">
                                {{ $webinars->links() }}
                            </div>
                        @else
                            <div class="text-center py-5 px-4">
                                <div class="mb-4">
                                    <div class="rounded-circle d-inline-flex align-items-center justify-content-center shadow-lg floating-action"
                                         style="width: 120px; height: 120px; background: linear-gradient(135deg, #6A6AFF 0%, #8B6AFF 100%);">
                                        <i class="fas fa-video fa-3x text-white opacity-80"></i>
                                    </div>
                                </div>
                                <h4 class="fw-bold text-dark mb-3 gradient-text" style="font-size: 1.5rem;">{{ translate('Aucun webinaire trouvé') }}</h4>
                                <p class="text-muted mb-4" style="font-size: 1.1rem; max-width: 500px; margin: 0 auto;">{{ translate('Commencez par créer votre premier webinaire et partagez votre expertise avec vos participants.') }}</p>
                                <a href="{{ route('instructor.webinars.create') }}"
                                   class="btn btn-primary btn-lg px-5 py-3 rounded-pill shadow-lg floating-action" style="font-weight: 600;">
                                    <i class="fas fa-plus me-2"></i>
                                    {{ translate('Créer mon premier webinaire') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Enhanced Custom Styles */
        .glass-card {
            background: rgba(255,255,255,0.8);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .webinar-card {
            transition: all 0.3s ease;
            border-radius: 20px !important;
            overflow: hidden;
            background: rgba(255,255,255,0.9);
        }

        .webinar-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px rgba(106,106,255,0.15) !important;
        }

        .gradient-text {
            background: linear-gradient(135deg, #6A6AFF 0%, #8B6AFF 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .floating-action {
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-8px); }
        }

        .card-img-top {
            transition: transform 0.3s ease;
        }

        .webinar-card:hover .card-img-top {
            transform: scale(1.1);
        }

        .badge {
            font-weight: 500;
            letter-spacing: 0.5px;
            backdrop-filter: blur(10px);
        }

        .dropdown-menu {
            border: 1px solid rgba(255,255,255,0.2);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            border-radius: 16px;
            padding: 8px 0;
            background: rgba(255,255,255,0.95) !important;
            backdrop-filter: blur(10px);
        }

        .dropdown-item {
            padding: 12px 20px;
            transition: all 0.2s ease;
            border-radius: 12px;
            margin: 2px 8px;
        }

        .dropdown-item:hover {
            background: linear-gradient(135deg, #6A6AFF 0%, #8B6AFF 100%) !important;
            color: white !important;
            transform: translateX(8px);
        }

        .btn {
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .rounded-pill {
            border-radius: 50px !important;
        }

        /* Responsive animations */
        @media (max-width: 768px) {
            .webinar-card:hover {
                transform: translateY(-4px);
            }
            .floating-action {
                animation: none;
            }
        }

        /* Loading animation */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #6A6AFF;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>

    <script>
        // Enhanced JavaScript for better UX
        document.addEventListener('DOMContentLoaded', function() {
            // Add loading animation to buttons
            const buttons = document.querySelectorAll('.btn');
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (this.type === 'submit' || this.tagName === 'A') {
                        const originalContent = this.innerHTML;
                        this.innerHTML = '<span class="loading-spinner me-2"></span>Chargement...';
                        this.disabled = true;

                        // Revert after 3 seconds if still loading
                        setTimeout(() => {
                            this.innerHTML = originalContent;
                            this.disabled = false;
                        }, 3000);
                    }
                });
            });

            // Enhanced dropdown behavior with touch support
            const dropdowns = document.querySelectorAll('.dropdown');
            dropdowns.forEach(dropdown => {
                const button = dropdown.querySelector('.dropdown-toggle');
                const menu = dropdown.querySelector('.dropdown-menu');

                // Mouse events
                dropdown.addEventListener('mouseenter', function() {
                    menu.classList.add('show');
                });

                dropdown.addEventListener('mouseleave', function() {
                    setTimeout(() => {
                        if (!dropdown.matches(':hover')) {
                            menu.classList.remove('show');
                        }
                    }, 100);
                });

                // Touch events for mobile
                button.addEventListener('touchstart', function(e) {
                    e.preventDefault();
                    const isOpen = menu.classList.contains('show');

                    // Close all other dropdowns
                    dropdowns.forEach(otherDropdown => {
                        if (otherDropdown !== dropdown) {
                            otherDropdown.querySelector('.dropdown-menu').classList.remove('show');
                        }
                    });

                    // Toggle current dropdown
                    menu.classList.toggle('show');
                });
            });

            // Close dropdowns when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.dropdown')) {
                    dropdowns.forEach(dropdown => {
                        dropdown.querySelector('.dropdown-menu').classList.remove('show');
                    });
                }
            });

            // Add intersection observer for fade-in animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Observe webinar cards for scroll animations
            document.querySelectorAll('.webinar-card').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(card);
            });
        });
    </script>
</x-dashboard-layout>
