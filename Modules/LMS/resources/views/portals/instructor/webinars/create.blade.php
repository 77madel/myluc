<x-dashboard-layout>
    <x-slot name="title">
        {{ translate('Créer un Webinaire') }}
    </x-slot>

    <x-portal::admin.breadcrumb>
        <x-slot name="title">{{ translate('Créer un Webinaire') }}</x-slot>
        <li class="breadcrumb-item">
            <a href="{{ route('instructor.dashboard') }}">{{ translate('Dashboard') }}</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('instructor.webinars.index') }}">{{ translate('Webinaires') }}</a>
        </li>
        <li class="breadcrumb-item active">{{ translate('Créer') }}</li>
    </x-portal::admin.breadcrumb>

    <div class="container-fluid create-webinar">
        <div class="row">
            <div class="col-12">
                <!-- Header style admin -->
                <div class="mb-4">
                    <div class="relative overflow-hidden rounded-2xl shadow-xl" style="background:#4f46e5 ;">
                        <div class="position-absolute w-100 h-100" style="inset:0;background:rgba(0,0,0,.08);"></div>
                        <div class="position-relative p-4 p-md-5 text-white">
                            <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-sm-between gap-3">
                                <div>
                                    <h2 class="mb-2 d-flex align-items-center fw-bold" style="font-size:1.8rem;">
                                        <span class="d-inline-flex align-items-center justify-content-center me-3" style="width:48px;height:48px;background:rgba(255,255,255,.2);border-radius:12px;">
                                            <i class="fas fa-plus" style="font-size:1.2rem;"></i>
                                        </span>
                                        {{ translate('Créer un Webinaire') }}
                                    </h2>
                                    <p class="mb-0" style="opacity:.95;">{{ translate('Créez un webinaire professionnel en quelques étapes') }}</p>
                                </div>
                                <div>
                                    <a href="{{ route('instructor.webinars.index') }}" class="btn btn-light" style="background:rgba(255,255,255,.2);border:none;border-radius:12px;backdrop-filter: blur(2px);color:#fff;">
                                        <i class="fas fa-arrow-left me-2"></i>{{ translate('Retour') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('instructor.webinars.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="row g-4">
                                <div class="col-lg-8">
                                    <div class="card form-section mb-4">
                                        <div class="card-body p-4">
                                            <div class="section-box mb-4">
                                            <div class="section-heading mb-3">
                                                <h5 class="mb-1 dark:text-white"><i class="fas fa-edit me-2 text-primary"></i>{{ translate('Détails du webinaire') }}</h5>
                                                <small class="text-muted dark:text-gray-400">{{ translate('Ajoutez un titre clair et une description engageante') }}</small>
                                            </div>

                                            <div class="mb-4">
                                                <label for="title" class="form-label fw-semibold dark:text-gray-300">{{ translate('Titre du Webinaire') }} <span class="text-danger">*</span></label>
                                                <div class="input-group input-group-lg">
                                                    <span class="input-group-text bg-white dark:bg-gray-700 dark:border-gray-600"><i class="fas fa-heading text-muted dark:text-gray-400"></i></span>
                                                    <input type="text" class="form-control dark:bg-transparent dark:text-white dark:border-gray-600" id="title" name="title"
                                                           value="{{ old('title') }}" required placeholder="Ex: Introduction à la programmation Python">
                                                </div>
                                                <div class="form-text mt-2 dark:text-gray-400">{{ translate('Choisissez un titre accrocheur et descriptif') }}</div>
                                            </div>

                                    <div class="mb-4">
                                        <label for="short_description" class="form-label fw-bold dark:text-gray-300">{{ translate('Description courte') }} <span class="text-danger">*</span></label>
                                            <textarea class="form-control sleek dark:bg-transparent dark:text-white dark:border-gray-600" id="short_description" name="short_description"
                                                      rows="3" maxlength="500" required
                                                      placeholder="Résumé en quelques lignes de ce que vous allez couvrir dans ce webinaire...">{{ old('short_description') }}</textarea>
                                            <div class="d-flex justify-content-between align-items-center mt-1">
                                                <div class="form-text dark:text-gray-400">{{ translate('Maximum 500 caractères - Cette description apparaîtra dans la liste des webinaires') }}</div>
                                                <span class="badge rounded-pill bg-light text-secondary border dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600" id="short-desc-counter">0/500</span>
                                            </div>
                                    </div>

                                    <div class="mb-4">
                                        <label for="description" class="form-label fw-bold dark:text-gray-300">{{ translate('Description complète') }} <span class="text-danger">*</span></label>
                                            <textarea class="form-control form-control-lg sleek dark:bg-transparent dark:text-white dark:border-gray-600" id="description" name="description"
                                                      rows="8" required
                                                      placeholder="Décrivez en détail le contenu de votre webinaire, les objectifs d'apprentissage, le programme...">{{ old('description') }}</textarea>
                                        <div class="form-text dark:text-gray-400">{{ translate('Cette description détaillée sera visible par les participants') }}</div>
                                    </div>
                                    </div>

                                        <div class="section-divider"></div>
                                        <div class="section-box mb-4">
                                        <div class="section-heading mb-3">
                                            <h5 class="mb-1 dark:text-white"><i class="fas fa-calendar-alt me-2 text-primary"></i>{{ translate('Planification') }}</h5>
                                            <small class="text-muted dark:text-gray-400">{{ translate('Définissez les dates et la durée') }}</small>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                            <div class="mb-3">
                                                    <label for="start_date" class="form-label fw-semibold dark:text-gray-300">{{ translate('Date de début') }} <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-white dark:bg-gray-700 dark:border-gray-600"><i class="far fa-clock text-muted dark:text-gray-400"></i></span>
                                                        <input type="datetime-local" class="form-control dark:bg-transparent dark:text-white dark:border-gray-600" id="start_date" name="start_date"
                                                               value="{{ old('start_date') }}" required>
                                                    </div>
                                            </div>
                                            </div>
                                            <div class="col-md-6">
                                            <div class="mb-3">
                                                    <label for="end_date" class="form-label fw-semibold dark:text-gray-300">{{ translate('Date de fin') }} <span class="text-danger">*</span></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-white dark:bg-gray-700 dark:border-gray-600"><i class="far fa-calendar text-muted dark:text-gray-400"></i></span>
                                                        <input type="datetime-local" class="form-control dark:bg-transparent dark:text-white dark:border-gray-600" id="end_date" name="end_date"
                                                               value="{{ old('end_date') }}" required>
                                                    </div>
                                            </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                            <div class="mb-3">
                                                    <label for="duration" class="form-label fw-semibold dark:text-gray-300">{{ translate('Durée (minutes)') }} <span class="text-danger">*</span></label>
                                                    <input type="number" class="form-control dark:bg-transparent dark:text-white dark:border-gray-600" id="duration" name="duration"
                                                           value="{{ old('duration') }}" min="15" required>
                                            </div>
                                            </div>
                                            <div class="col-md-6">
                                            <div class="mb-3">
                                                    <label for="max_participants" class="form-label fw-semibold dark:text-gray-300">{{ translate('Participants maximum') }}</label>
                                                    <input type="number" class="form-control dark:bg-transparent dark:text-white dark:border-gray-600" id="max_participants" name="max_participants"
                                                           value="{{ old('max_participants') }}" min="1">
                                                    <div class="form-text dark:text-gray-400">{{ translate('Laisser vide pour illimité') }}</div>
                                            </div>
                                            </div>
                                        </div>
                                        </div>

                                        <div class="section-divider"></div>
                                        <div class="section-box">
                                        <div class="section-heading mb-3">
                                            <h5 class="mb-1 dark:text-white"><i class="fas fa-tags me-2 text-primary"></i>{{ translate('Catégorie et média') }}</h5>
                                            <small class="text-muted dark:text-gray-400">{{ translate('Aidez les étudiants à trouver votre webinaire') }}</small>
                                        </div>

                                    <div class="mb-3">
                                            <label for="category_id" class="form-label fw-semibold dark:text-gray-300">{{ translate('Catégorie') }} <span class="text-danger">*</span></label>
                                        <select class="form-select dark:bg-transparent dark:text-white dark:border-gray-600" id="category_id" name="category_id" required>
                                            <option value="">{{ translate('Sélectionner une catégorie') }}</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                        <div class="mb-3">
                                            <label for="meeting_url" class="form-label fw-semibold dark:text-gray-300">{{ translate('URL de la réunion (collez votre lien)') }}</label>
                                            <input type="url" class="form-control dark:bg-transparent dark:text-white dark:border-gray-600" id="meeting_url" name="meeting_url" value="{{ old('meeting_url') }}" placeholder="https://...">
                                            <div class="form-text dark:text-gray-400">{{ translate('Créez votre réunion sur Teams, Zoom ou Google Meet puis collez le lien ici.') }}</div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="image" class="form-label fw-semibold dark:text-gray-300">{{ translate('Image du webinaire') }}</label>
                                            <input type="file" class="form-control dark:bg-transparent dark:text-white dark:border-gray-600" id="image" name="image"
                                                   accept="image/jpeg,image/png,image/jpg,image/gif">
                                            <div class="form-text dark:text-gray-400">{{ translate('Formats acceptés: JPEG, PNG, JPG, GIF (max 2MB)') }}</div>
                                            <div class="mt-3">
                                                <img id="image-preview" class="img-fluid rounded d-none dark:border-gray-600" alt="preview" />
                                            </div>
                                        </div>

                                        <div class="mb-0">
                                            <div class="form-check">
                                                <input class="form-check-input dark:bg-transparent dark:border-gray-600" type="checkbox" id="is_recorded" name="is_recorded"
                                                       {{ old('is_recorded') ? 'checked' : '' }}>
                                                <label class="form-check-label dark:text-gray-300" for="is_recorded">
                                                    {{ translate('Enregistrer le webinaire') }}
                                                </label>
                                            </div>
                                        </div>
                                        </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="card border-0 shadow-sm bg-gradient-light dark:bg-gray-800 sticky-side">
                                        <div class="card-header bg-transparent dark:bg-gray-900 border-0 dark:border-gray-700">
                                            <h6 class="mb-0 fw-bold text-dark dark:text-white">
                                                <i class="fas fa-info-circle text-primary me-2"></i>
                                                {{ translate('Informations') }}
                                            </h6>
                                        </div>
                                        <div class="card-body dark:bg-gray-800">
                                            <div class="mb-4">
                                                <label class="form-label fw-bold dark:text-gray-300">{{ translate('Type de webinaire') }}</label>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-success rounded-pill px-3 py-2 me-2">
                                                        <i class="fas fa-gift me-1"></i>
                                                        {{ translate('Gratuit') }}
                                                    </span>
                                                </div>
                                                <div class="form-text dark:text-gray-400">{{ translate('Les webinaires d\'instructeur sont toujours gratuits') }}</div>
                                            </div>

                                            <div class="mb-4">
                                                <label class="form-label fw-bold dark:text-gray-300">{{ translate('Statut') }}</label>
                                                <div class="d-flex align-items-center">
                                                    <span class="badge bg-warning rounded-pill px-3 py-2 me-2">
                                                        <i class="fas fa-edit me-1"></i>
                                                        {{ translate('Brouillon') }}
                                                    </span>
                                                </div>
                                                <div class="form-text dark:text-gray-400">{{ translate('Le webinaire sera en brouillon jusqu\'à publication') }}</div>
                                            </div>



                                            <div class="alert alert-info border-0 dark:bg-blue-900 dark:border-blue-800 dark:text-blue-200">
                                                <i class="fas fa-lightbulb text-info dark:text-blue-400 me-2"></i>
                                                <small>{{ translate('Conseil: Publiez votre webinaire au moins 24h avant la date prévue pour permettre aux étudiants de s\'inscrire.') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="action-bar dark:bg-gray-800 dark:border-gray-700">
                                <div class="container-fluid px-0 d-flex justify-content-between align-items-center">
                                    <a href="{{ route('instructor.webinars.index') }}" class="btn btn-secondary dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200">
                                        <i class="fas fa-arrow-left"></i> {{ translate('Retour') }}
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> {{ translate('Créer le Webinaire') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Auto-calculate end date based on start date and duration
        document.getElementById('start_date').addEventListener('change', function() {
            const startDate = new Date(this.value);
            const duration = parseInt(document.getElementById('duration').value) || 60;
            const endDate = new Date(startDate.getTime() + (duration * 60000));

            document.getElementById('end_date').value = endDate.toISOString().slice(0, 16);
        });

        document.getElementById('duration').addEventListener('change', function() {
            const startDate = document.getElementById('start_date').value;
            if (startDate) {
                const start = new Date(startDate);
                const duration = parseInt(this.value) || 60;
                const endDate = new Date(start.getTime() + (duration * 60000));

                document.getElementById('end_date').value = endDate.toISOString().slice(0, 16);
            }
        });

        // Live character counter for short description
        (function() {
            const textarea = document.getElementById('short_description');
            const counter = document.getElementById('short-desc-counter');
            if (!textarea || !counter) return;
            const updateCount = () => {
                const len = textarea.value.length;
                counter.textContent = `${len}/500`;
                counter.classList.toggle('text-danger', len > 500);
            };
            textarea.addEventListener('input', updateCount);
            updateCount();
        })();

        // Auto-resize textareas for better UX
        (function() {
            const autosize = (el) => {
                el.style.height = 'auto';
                el.style.height = (el.scrollHeight + 6) + 'px';
            };
            document.querySelectorAll('textarea').forEach((ta) => {
                autosize(ta);
                ta.addEventListener('input', () => autosize(ta));
            });
        })();

        // Image preview
        (function() {
            const input = document.getElementById('image');
            const preview = document.getElementById('image-preview');
            if (!input || !preview) return;
            input.addEventListener('change', function() {
                const file = this.files && this.files[0];
                if (!file) {
                    preview.classList.add('d-none');
                    preview.removeAttribute('src');
                    return;
                }
                const url = URL.createObjectURL(file);
                preview.src = url;
                preview.classList.remove('d-none');
            });
        })();
    </script>

    <style>
        /* Custom Styles for Create Form */
        :root{ --primary-600:#6b47ff; --accent-600:#5a38f6; --ring:rgba(59,130,246,.10); --slate-100:#f1f5f9; --slate-200:#e2e8f0; --slate-600:#475569; }
        .create-webinar{ max-width: 1000px; margin-inline: auto; }
        .bg-gradient-light {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .form-control, .form-select {
            border-radius: 12px;
            border: 1px solid var(--slate-200);
            padding: 12px 14px;
            transition: all 0.2s ease;
            background-color: #fbfcfe;
            width: 100%;
        }
        .input-group{ width: 100%; }
        textarea{ width: 100%; }

        .form-control:focus {
            border-color: var(--primary-600);
            box-shadow: 0 6px 20px rgba(76, 81, 191, 0.08), 0 0 0 3px var(--ring);
            background-color: #fff;
        }

        .form-label{ font-weight:600; color:#0f172a; }
        .form-text{ color:#64748b; }
        .form-control::placeholder{ color:#94a3b8; }
        textarea.form-control{ resize: vertical; min-height: 160px; line-height: 1.55; }
        #title.form-control{ min-height: 48px; }

        .form-control-lg {
            padding: 15px 20px;
            font-size: 1.1rem;
        }

        .section-heading h5 { font-weight: 700; color:#0f172a; }
        .section-divider { height: 1px; background: #eef0f2; margin: 16px 0 20px; }
        .sleek { border-radius: 12px; }

        /* Section wrapper for better visual grouping */
        .section-box{
            background: #ffffff;
            border: 1px solid #eef2f7;
            border-radius: 14px;
            padding: 18px;
            transition: box-shadow .2s ease, border-color .2s ease;
        }
        .section-box:focus-within{
            box-shadow: 0 8px 24px rgba(59,130,246,.10);
            border-color: var(--primary-600);
        }

        .input-group-text { border: 2px solid #e9ecef; border-right: 0; }
        .input-group .form-control { border-left: 0; }
        .input-group .form-control:focus { box-shadow: none; }

        .btn {
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .btn-primary { background: linear-gradient(135deg, var(--primary-600) 0%, var(--accent-600) 100%); border: none; }

        .btn-secondary { background: linear-gradient(135deg, #6c757d 0%, #495057 100%); border: none; }

        .card {
            border-radius: 15px;
            overflow: hidden;
            border: 1px solid #eaecef;
            box-shadow: 0 6px 18px rgba(16, 24, 40, 0.05);
        }

        /* Form section styling */
        .form-section .form-label { color: #343a40; }
        .form-section .form-text { color: #6c757d; }

        /* Sticky side card */
        .sticky-side { position: sticky; top: 88px; }

        /* Sticky action bar */
        .action-bar {
            position: sticky;
            bottom: -1px;
            background: #fff;
            padding: 12px 16px;
            border-top: 1px solid #e9ecef;
            margin-top: 12px;
            z-index: 5;
        }

        .badge {
            font-weight: 500;
            letter-spacing: 0.5px;
        }

        .alert {
            border-radius: 10px;
        }

        /* Animation for form elements */
        .form-control, .btn, .card {
            animation: fadeInUp 0.6s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Stagger animation */
        .form-control:nth-child(1) { animation-delay: 0.1s; }
        .form-control:nth-child(2) { animation-delay: 0.2s; }
        .form-control:nth-child(3) { animation-delay: 0.3s; }
        .form-control:nth-child(4) { animation-delay: 0.4s; }

        /* Counter badge styling */
        #short-desc-counter{ background:var(--slate-100); color:var(--slate-600); border:1px solid var(--slate-200); }

        /* MODE SOMBRE - FORMULAIRE INSTRUCTEUR */
        .dark .form-control,
        .dark .form-select,
        .dark input,
        .dark textarea,
        .dark select {
            background-color: transparent !important;
            border-color: #4b5563 !important;
            color: #ffffff !important;
        }

        .dark .form-control::placeholder,
        .dark input::placeholder,
        .dark textarea::placeholder {
            color: #9ca3af !important;
        }

        .dark .input-group-text {
            background-color: transparent !important;
            border-color: #4b5563 !important;
            color: #d1d5db !important;
        }

        .dark .form-label,
        .dark label {
            color: #d1d5db !important;
        }

        .dark .form-text {
            color: #9ca3af !important;
        }

        .dark .card {
            background-color: #1f2937 !important;
            border-color: #374151 !important;
        }

        .dark .section-heading h5 {
            color: #f3f4f6 !important;
        }

        .dark .section-heading small {
            color: #9ca3af !important;
        }

        .dark .badge {
            background-color: #374151 !important;
            color: #d1d5db !important;
            border-color: #4b5563 !important;
        }

        .dark .alert {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
            color: #f3f4f6 !important;
        }

        /* Renforcement des styles pour tous les éléments */
        .dark .section-heading h5 {
            color: #f3f4f6 !important;
        }

        .dark .section-heading small {
            color: #9ca3af !important;
        }

        .dark .input-group-text {
            background-color: #374151 !important;
            border-color: #4b5563 !important;
            color: #d1d5db !important;
        }

        .dark .input-group-text i {
            color: #9ca3af !important;
        }

        /* Fond des cartes et sections en mode sombre */
        .dark .card,
        .dark .form-section,
        .dark .card-body {
            background-color: #1f2937 !important;
            border: none !important;
            box-shadow: none !important;
        }

        .dark .section-box {
            background-color: #1f2937 !important;
        }

        .dark .section-divider {
            border-color: #374151 !important;
        }

        /* Barre d'action */
        .dark .action-bar {
            background-color: #1f2937 !important;
            border-color: #374151 !important;
        }

        .dark .btn-secondary {
            background-color: #4b5563 !important;
            border-color: #6b7280 !important;
            color: #e5e7eb !important;
        }

        .dark .btn-secondary:hover {
            background-color: #6b7280 !important;
            border-color: #9ca3af !important;
        }

        /* Card header */
        .dark .card-header {
            background-color: #111827 !important;
            border: none !important;
        }

        .dark .card-header h6 {
            color: #f3f4f6 !important;
        }

        /* Supprimer tous les bordures et ombres blanches */
        .dark .sticky-side {
            border: none !important;
            box-shadow: none !important;
        }

        .dark .bg-gradient-light {
            background: #1f2937 !important;
        }

        /* Action bar */
        .dark .action-bar {
            border-top-color: #374151 !important;
        }

        /* Protection du mode clair */
        html:not(.dark) .form-control,
        html:not(.dark) input,
        html:not(.dark) textarea,
        html:not(.dark) select {
            background-color: #ffffff !important;
            border-color: #d1d5db !important;
            color: #111827 !important;
        }
    </style>
</x-dashboard-layout>
