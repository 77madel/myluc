<!-- Webinar Dashboard Widget -->
<div class="col-xl-3 col-md-6">
    <div class="card">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-6">
                    <h5 class="text-muted fw-normal mt-0 text-truncate" title="Webinaires">Webinaires</h5>
                    <h3 class="my-2 py-1">{{ $webinarStats['total'] ?? 0 }}</h3>
                    <p class="mb-0 text-muted">
                        <span class="text-success me-2">
                            <i class="mdi mdi-arrow-up-bold"></i> {{ $webinarStats['this_month'] ?? 0 }}
                        </span>
                        <span class="text-nowrap">Ce mois</span>
                    </p>
                </div>
                <div class="col-6">
                    <div class="text-end">
                        <div id="webinar-chart" class="apex-charts" dir="ltr">
                            <div class="apexcharts-canvas">
                                <svg height="100" width="100">
                                    <circle cx="50" cy="50" r="40" fill="none" stroke="#e3eaef" stroke-width="8"/>
                                    <circle cx="50" cy="50" r="40" fill="none" stroke="#3b82f6" stroke-width="8"
                                            stroke-dasharray="251.2" stroke-dashoffset="125.6" stroke-linecap="round"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <div class="row align-items-center">
                <div class="col-6">
                    <a href="{{ route('webinars.index') }}" class="text-muted">
                        Voir tous les webinaires
                    </a>
                </div>
                <div class="col-6 text-end">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#quickCreateWebinarModal">
                        <i class="fas fa-plus"></i> Créer
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
