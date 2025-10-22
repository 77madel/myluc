<x-dashboard-layout>
    <x-slot name="title">
        {{ translate('Modifier le Webinaire') }}
    </x-slot>

    <x-portal::admin.breadcrumb>
        <x-slot name="title">{{ translate('Modifier le Webinaire') }}</x-slot>
        <li class="breadcrumb-item">
            <a href="{{ route('instructor.dashboard') }}">{{ translate('Dashboard') }}</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('instructor.webinars.index') }}">{{ translate('Webinaires') }}</a>
        </li>
        <li class="breadcrumb-item active">{{ translate('Modifier') }}</li>
    </x-portal::admin.breadcrumb>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title mb-0">{{ translate('Modifier le Webinaire') }}: {{ $webinar->title }}</h4>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('instructor.webinars.update', $webinar->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">{{ translate('Titre du Webinaire') }} <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="title" name="title"
                                               value="{{ old('title', $webinar->title) }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="short_description" class="form-label">{{ translate('Description courte') }} <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="short_description" name="short_description"
                                                  rows="3" maxlength="500" required>{{ old('short_description', $webinar->short_description) }}</textarea>
                                        <div class="form-text">{{ translate('Maximum 500 caractères') }}</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">{{ translate('Description complète') }} <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="description" name="description"
                                                  rows="6" required>{{ old('description', $webinar->description) }}</textarea>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="start_date" class="form-label">{{ translate('Date de début') }} <span class="text-danger">*</span></label>
                                                <input type="datetime-local" class="form-control" id="start_date" name="start_date"
                                                       value="{{ old('start_date', $webinar->start_date->format('Y-m-d\TH:i')) }}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="end_date" class="form-label">{{ translate('Date de fin') }} <span class="text-danger">*</span></label>
                                                <input type="datetime-local" class="form-control" id="end_date" name="end_date"
                                                       value="{{ old('end_date', $webinar->end_date->format('Y-m-d\TH:i')) }}" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="duration" class="form-label">{{ translate('Durée (minutes)') }} <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="duration" name="duration"
                                                       value="{{ old('duration', $webinar->duration) }}" min="15" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="max_participants" class="form-label">{{ translate('Participants maximum') }}</label>
                                                <input type="number" class="form-control" id="max_participants" name="max_participants"
                                                       value="{{ old('max_participants', $webinar->max_participants) }}" min="1">
                                                <div class="form-text">{{ translate('Laisser vide pour illimité') }}</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="category_id" class="form-label">{{ translate('Catégorie') }} <span class="text-danger">*</span></label>
                                        <select class="form-select" id="category_id" name="category_id" required>
                                            <option value="">{{ translate('Sélectionner une catégorie') }}</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}"
                                                        {{ old('category_id', $webinar->category_id) == $category->id ? 'selected' : '' }}>
                                                    {{ $category->title }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="image" class="form-label">{{ translate('Image du webinaire') }}</label>
                                        @if($webinar->image)
                                            <div class="mb-2">
                                                <img src="{{ Storage::url($webinar->image) }}" alt="{{ $webinar->title }}"
                                                     class="img-thumbnail" style="max-width: 200px;">
                                                <div class="form-text">{{ translate('Image actuelle') }}</div>
                                            </div>
                                        @endif
                                        <input type="file" class="form-control" id="image" name="image"
                                               accept="image/jpeg,image/png,image/jpg,image/gif">
                                        <div class="form-text">{{ translate('Formats acceptés: JPEG, PNG, JPG, GIF (max 2MB)') }}</div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_recorded" name="is_recorded"
                                                   {{ old('is_recorded', $webinar->is_recorded) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_recorded">
                                                {{ translate('Enregistrer le webinaire') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="card bg-light">
                                        <div class="card-header">
                                            <h6 class="mb-0">{{ translate('Informations') }}</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label class="form-label">{{ translate('Type de webinaire') }}</label>
                                                <div class="form-control-plaintext">
                                                    <span class="badge bg-success">{{ translate('Gratuit') }}</span>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">{{ translate('Statut') }}</label>
                                                <div class="form-control-plaintext">
                                                    @if($webinar->is_published)
                                                        <span class="badge bg-success">{{ translate('Publié') }}</span>
                                                    @else
                                                        <span class="badge bg-warning">{{ translate('Brouillon') }}</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">{{ translate('Participants actuels') }}</label>
                                                <div class="form-control-plaintext">
                                                    <span class="badge bg-primary">{{ $webinar->current_participants }}</span>
                                                </div>
                                            </div>

                                            @if($webinar->meeting_url)
                                                <div class="mb-3">
                                                    <label class="form-label">{{ translate('Lien de réunion') }}</label>
                                                    <div class="form-control-plaintext">
                                                        <a href="{{ $webinar->meeting_url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                            <i class="fas fa-external-link-alt"></i> {{ translate('Ouvrir') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('instructor.webinars.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> {{ translate('Retour') }}
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> {{ translate('Mettre à jour') }}
                                </button>
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
    </script>
</x-dashboard-layout>

