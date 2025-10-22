<x-dashboard-layout>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title mb-0">Certificat de Formation</h4>
            <div class="btn-group">
                <a href="{{ route('certificate.view', $certificate->id) }}"
                   class="btn btn-info btn-sm"
                   target="_blank"
                   title="Aperçu du certificat">
                    <i class="fas fa-eye me-1"></i> Aperçu
                </a>
                @if($certificate->isDownloaded())
                    <button class="btn btn-secondary btn-sm" disabled title="Certificat déjà téléchargé">
                        <i class="fas fa-download me-1"></i> Déjà téléchargé
                    </button>
                    <small class="text-muted ms-2">
                        Téléchargé le {{ $certificate->downloaded_at->format('d/m/Y à H:i') }}
                    </small>
                @else
                    <a href="{{ route('certificate.download', $certificate->id) }}"
                       class="btn btn-success btn-sm"
                       id="download-btn"
                       title="Télécharger le certificat en PDF">
                        <i class="fas fa-download me-1"></i> Télécharger PDF
                    </a>
                @endif
            </div>
        </div>

        <div class="card-body">
            @if($certificate->isDownloaded())
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Certificat déjà téléchargé</strong> - Ce certificat a été téléchargé le {{ $certificate->downloaded_at->format('d/m/Y à H:i') }}.
                    Vous pouvez toujours le visualiser, mais il ne peut plus être téléchargé.
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Félicitations !</strong> Vous avez terminé avec succès cette formation.
                    Vous pouvez télécharger votre certificat au format PDF ou le visualiser directement dans votre navigateur.
                    <strong>Attention :</strong> Le téléchargement n'est autorisé qu'une seule fois.
                </div>
            @endif

        <div id="certificate-builder-area" class="certificate-builder-area text-align-justify !overflow-x-auto">
                <div class="certificate-template-container" id="certificateImg" style="
                    background: url('https://edulab.hivetheme.com/lms/assets/images/certificate-template.jpg');
                    background-repeat: no-repeat;
                    background-size: 100% 100%;
                    width: 800px;
                    height: 600px;
                    margin: 0 auto;
                    position: relative;
                    font-family: 'Segoe UI', 'Trebuchet MS', sans-serif;
                    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
                ">

                    <!-- Texte "Ce certificat est décerné à" - Introduction -->
                    <div style="
                        position: absolute;
                        left: 50%;
                        top: 36%;
                        transform: translateX(-50%);
                        font-size: 12px;
                        letter-spacing: 1px;
                        color: #000000;
                        text-align: center;
                        font-style: italic;
                        font-weight: 400;
                        z-index: 10;
                    ">Ce certificat est décerné à</div>

                    <!-- Nom de l'étudiant - Point focal principal -->
                    <div data-name="student" class="dragable-element" style="
                        position: absolute;
                        left: 50%;
                        top: 42%;
                        transform: translateX(-50%);
                        font-size: 42px;
                        font-weight: 700;
                        color: #1a3a52;
                        text-align: center;
                        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.08);
                        z-index: 10;
                        letter-spacing: 0.5px;
                        max-width: 650px;
                    ">{{ $user->userable->first_name ?? 'Utilisateur' }} {{ $user->userable->last_name ?? '' }}</div>

                    <!-- Texte "pour avoir terminé avec succès" - Sous le nom -->
                    <div style="
                        position: absolute;
                        left: 50%;
                        top: 51%;
                        transform: translateX(-50%);
                        font-size: 13px;
                        color: #5a6c7d;
                        text-align: center;
                        font-style: italic;
                        font-weight: 400;
                        letter-spacing: 0.3px;
                        z-index: 10;
                    ">pour avoir terminé avec succès le cours</div>

                    <!-- Titre du cours - Accent secondaire -->
                    <div data-name="course-title" class="dragable-element" style="
                        position: absolute;
                        left: 50%;
                        top: 56%;
                        transform: translateX(-50%);
                        font-size: 22px;
                        font-weight: 600;
                        color: #2c5282;
                        text-align: center;
                        max-width: 550px;
                        line-height: 1.4;
                        z-index: 10;
                        letter-spacing: 0.2px;
                    ">{{ $certificate->subject ?? 'Formation' }}</div>

                    <!-- Séparateur visuel subtle -->
                    <div style="
                        position: absolute;
                        left: 50%;
                        top: 63%;
                        transform: translateX(-50%);
                        width: 120px;
                        height: 2px;
                        background: linear-gradient(to right, transparent, #2c5282, transparent);
                        z-index: 10;
                    "></div>

                    <!-- Plateforme - Bas gauche -->
                    <div data-name="platform-name" class="dragable-element" style="
                        position: absolute;
                        left: 110px;
                        bottom: 18%;
                        font-size: 14px;
                        font-weight: 600;
                        color: #2c3e50;
                        text-align: left;
                        z-index: 10;
                        letter-spacing: 0.3px;
                    ">MyLuc | Laboratoire Universel de Compétences'</div>

                    <!-- Date - Bas gauche -->
                    <div data-name="course-completed-date" class="dragable-element" style="
                        position: absolute;
                        left: 170px;
                        bottom: 10%;
                        font-size: 13px;
                        font-weight: 500;
                        color: #6b7280;
                        text-align: left;
                        z-index: 10;
                        letter-spacing: 0.2px;
                    ">Date: {{ $certificate->certificated_date ? $certificate->certificated_date->format('d/m/Y') : date('d/m/Y') }}</div>

                    <!-- Ligne de signature instructeur
                    <div style="
                        position: absolute;
                        right: 70px;
                        bottom: 18%;
                        width: 180px;
                        height: 1.5px;
                        background-color: #2c3e50;
                        z-index: 10;
                    "></div> -->

                    <!-- Nom de l'instructeur - Bas droit -->
                    <div data-name="instructor" class="dragable-element" style="
                        position: absolute;
                        right: 200px;
                        bottom: 18%;
                        font-size: 13px;
                        font-weight: 600;
                        color: #2c3e50;
                        text-align: center;
                        z-index: 10;
                        letter-spacing: 0.2px;
                    ">{{ $instructor_name ?? 'Instructeur' }}</div>

                    <!-- Texte "Instructeur" sous la signature
                    <div style="
                        position: absolute;
                        right: 70px;
                        bottom: 11%;
                        font-size: 11px;
                        font-weight: 400;
                        color: #9ca3af;
                        text-align: center;
                        z-index: 10;
                        letter-spacing: 0.5px;
                        font-style: italic;
                    ">Instructeur</div> -->

                </div>
            </div>
        </div>
    </div>

    @if(!$certificate->isDownloaded())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const downloadBtn = document.getElementById('download-btn');
            
            if (downloadBtn) {
                downloadBtn.addEventListener('click', function(e) {
                    // Attendre un peu pour que le téléchargement commence
                    setTimeout(function() {
                        // Rafraîchir la page après 2 secondes
                        window.location.reload();
                    }, 2000);
                });
            }
        });
    </script>
    @endif
</x-dashboard-layout>
