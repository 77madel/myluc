<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Certificat de Formation</title>
    <style>
        @page {
            margin: 15mm;
            size: A4 landscape;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            background: white;
            color: #333;
        }

        .certificate-container {
            width: 100%;
            height: 100vh;
            background: white;
            border: 3px solid #667eea;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            position: relative;
        }

        .certificate-title {
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
        }

        .certificate-subtitle {
            font-size: 20px;
            color: #4a5568;
            margin-bottom: 40px;
        }

        .certificate-text {
            font-size: 18px;
            color: #2d3748;
            line-height: 1.6;
            margin: 20px 0;
        }

        .student-name {
            font-size: 28px;
            font-weight: bold;
            color: #667eea;
            margin: 30px 0;
            text-decoration: underline;
        }

        .course-name {
            font-size: 22px;
            font-weight: bold;
            color: #4a5568;
            margin: 20px 0;
        }

        .certificate-footer {
            position: absolute;
            bottom: 40px;
            left: 40px;
            right: 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .signature-section {
            text-align: center;
        }

        .signature-line {
            border-bottom: 2px solid #667eea;
            width: 200px;
            margin: 0 auto 10px;
        }

        .signature-text {
            font-size: 14px;
            color: #4a5568;
            font-weight: bold;
        }

        .date-section {
            text-align: center;
        }

        .date-text {
            font-size: 16px;
            color: #4a5568;
            font-weight: bold;
        }

        .certificate-id {
            position: absolute;
            bottom: 20px;
            right: 20px;
            font-size: 12px;
            color: #a0aec0;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <h1 class="certificate-title">CERTIFICAT DE FORMATION</h1>
        <p class="certificate-subtitle">Certificat de Réussite</p>

        <p class="certificate-text">
            Ce certificat atteste que
        </p>

        <div class="student-name">
            {{ $user->first_name }} {{ $user->last_name }}
        </div>

        <p class="certificate-text">
            a suivi avec succès la formation
        </p>

        <div class="course-name">
            {{ $course->title ?? 'Formation' }}
        </div>

        <p class="certificate-text">
            et a démontré une compréhension complète des concepts enseignés.
        </p>

        <div class="certificate-footer">
            <div class="signature-section">
                <div class="signature-line"></div>
                <p class="signature-text">Directeur de Formation</p>
            </div>

            <div class="date-section">
                <p class="date-text">Date d'obtention : {{ $completion_date }}</p>
            </div>
        </div>

        <div class="certificate-id">
            Certificat N° {{ $certificate->id }} - {{ date('Y') }}
        </div>
    </div>
</body>
</html>
