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
        font-size: 150%;
        font-weight: 800;
        color: #1a3a52;
        text-align: center;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.08);
        z-index: 10;
        letter-spacing: 0.5px;
        width: 100%;
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
        font-size: 14px;
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
        left: 40px;
        bottom: 18%;
        font-size: 10px;
        font-weight: 600;
        color: #2c3e50;
        text-align: left;
        z-index: 10;
        letter-spacing: 0.3px;
    ">MyLuc | Laboratoire Universel de Compétences</div>

    <!-- Date - Bas gauche -->
    <div data-name="course-completed-date" class="dragable-element" style="
        position: absolute;
        left: 100px;
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
        right: 130px;
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
