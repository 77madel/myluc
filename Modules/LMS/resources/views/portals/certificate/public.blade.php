<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificat - {{ $course_title }}</title>
    
    <!-- Open Graph Meta Tags pour Facebook/LinkedIn -->
    <meta property="og:title" content="🎓 Certificat : {{ $course_title }}">
    <meta property="og:description" content="{{ $shareMessage }}">
    <meta property="og:url" content="{{ route('certificate.public', $certificate->public_uuid) }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $platform_name }}">
    <meta property="og:image" content="{{ $certificateImageUrl }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:image:alt" content="Certificat de {{ $student_name }} pour {{ $course_title }}">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="🎓 Certificat : {{ $course_title }}">
    <meta name="twitter:description" content="{{ $shareMessage }}">
    <meta name="twitter:image" content="{{ $certificateImageUrl }}">
    
    <!-- LinkedIn Specific -->
    <meta property="article:published_time" content="{{ $certificate->certificated_date ? $certificate->certificated_date->toIso8601String() : now()->toIso8601String() }}">
    <meta property="article:author" content="{{ $student_name }}">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .certificate-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 800px;
            width: 100%;
            padding: 60px;
            text-align: center;
        }
        
        .certificate-header {
            border-bottom: 3px solid #10B981;
            padding-bottom: 30px;
            margin-bottom: 40px;
        }
        
        .certificate-title {
            font-size: 48px;
            font-weight: 700;
            color: #1F2937;
            margin-bottom: 10px;
        }
        
        .certificate-subtitle {
            font-size: 18px;
            color: #6B7280;
        }
        
        .certificate-body {
            margin: 40px 0;
        }
        
        .student-name {
            font-size: 36px;
            font-weight: 700;
            color: #10B981;
            margin: 20px 0;
        }
        
        .course-info {
            font-size: 20px;
            color: #374151;
            margin: 15px 0;
        }
        
        .certificate-details {
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #E5E7EB;
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .detail-item {
            text-align: center;
        }
        
        .detail-label {
            font-size: 12px;
            color: #9CA3AF;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .detail-value {
            font-size: 16px;
            font-weight: 600;
            color: #1F2937;
        }
        
        .watermark {
            margin-top: 30px;
            font-size: 12px;
            color: #9CA3AF;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="certificate-header">
            <div class="certificate-title">🎓 Certificat</div>
            <div class="certificate-subtitle">de Réussite</div>
        </div>
        
        <div class="certificate-body">
            <p class="course-info">Ce certificat est décerné à</p>
            <h1 class="student-name">{{ $student_name }}</h1>
            <p class="course-info">pour avoir terminé avec succès le cours</p>
            <h2 class="course-info" style="font-weight: 700; color: #1F2937; margin-top: 20px;">
                « {{ $course_title }} »
            </h2>
            
            <div class="certificate-details">
                <div class="detail-item">
                    <div class="detail-label">Certificat N°</div>
                    <div class="detail-value">{{ $certificate_number }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Date d'Obtention</div>
                    <div class="detail-value">{{ $completion_date }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Instructeur</div>
                    <div class="detail-value">{{ $instructor_name }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Plateforme</div>
                    <div class="detail-value">{{ $platform_name }}</div>
                </div>
            </div>
        </div>
        
        <div class="watermark">
            Certificat partagé publiquement • Vérifier l'authenticité sur {{ $platform_name }}
        </div>
    </div>
</body>
</html>

