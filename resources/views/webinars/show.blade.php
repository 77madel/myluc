@extends('layouts.app')

@section('title', $webinar->title)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h1>{{ $webinar->title }}</h1>
                    <p class="text-muted">{{ $webinar->short_description }}</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>{{ translate('Description') }}</h5>
                            <p>{{ $webinar->description ?? translate('Aucune description disponible') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h5>{{ translate('Détails') }}</h5>
                            <ul class="list-unstyled">
                                <li><strong>{{ translate('Instructeur') }}:</strong> {{ $webinar->instructor?->first_name ?? 'N/A' }} {{ $webinar->instructor?->last_name ?? '' }}</li>
                                <li><strong>{{ translate('Date de début') }}:</strong> {{ $webinar->start_date ? $webinar->start_date->format('d/m/Y H:i') : 'N/A' }}</li>
                                <li><strong>{{ translate('Date de fin') }}:</strong> {{ $webinar->end_date ? $webinar->end_date->format('d/m/Y H:i') : 'N/A' }}</li>
                                <li><strong>{{ translate('Durée') }}:</strong> {{ $webinar->duration ?? 'N/A' }} {{ translate('minutes') }}</li>
                                <li><strong>{{ translate('Prix') }}:</strong> {{ $webinar->is_free ? translate('Gratuit') : $webinar->price . ' €' }}</li>
                            </ul>
                        </div>
                    </div>

                    @if($webinar->meeting_url)
                        <div class="mt-4">
                            <h5>{{ translate('Informations de connexion') }}</h5>
                            <div class="alert alert-info">
                                <a href="{{ $webinar->meeting_url }}" target="_blank" class="btn btn-primary">
                                    <i class="fas fa-external-link-alt me-2"></i>{{ translate('Rejoindre le webinaire') }}
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5>{{ translate('Actions') }}</h5>
                </div>
                <div class="card-body">
                    @if($isRegistered)
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>{{ translate('Vous êtes inscrit à ce webinaire') }}
                        </div>
                    @else
                        <form action="{{ route('webinar.register', $webinar) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-user-plus me-2"></i>{{ translate('S\'inscrire') }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($relatedWebinars->count() > 0)
        <div class="row mt-4">
            <div class="col-12">
                <h4>{{ translate('Webinaires similaires') }}</h4>
                <div class="row">
                    @foreach($relatedWebinars as $relatedWebinar)
                        <div class="col-md-6 col-lg-3 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $relatedWebinar->title }}</h6>
                                    <p class="card-text text-muted">{{ $relatedWebinar->short_description }}</p>
                                    <a href="{{ route('webinar.detail', $relatedWebinar->slug) }}" class="btn btn-sm btn-outline-primary">
                                        {{ translate('Voir') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

