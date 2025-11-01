<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ translate('Certificat') }}</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .certificate-wrapper {
            background: white;
            padding: 20px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .certificate-wrapper {
                box-shadow: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    <div class="certificate-wrapper">
        <div class="certificate-display">
                @php
                    // Chemin absolu vers l'image du certificat (JPEG plus léger)
                    $imagePath = base_path('Modules/LMS/storage/app/public/lms/certificates/lms-RK7Fn0CzaE.jpeg');
                    $imageData = '';

                    // Vérifier si le fichier existe
                    if (file_exists($imagePath)) {
                        // Lire le fichier et l'encoder en base64 pour l'affichage HTML
                        $imageData = base64_encode(file_get_contents($imagePath));
                        $imageData = "data:image/jpeg;base64,{$imageData}";
                    }
                @endphp

                <div class="certificate-template-container" id="certificateImg" style="
                    width: 800px;
                    height: 600px;
                    margin: 0 auto;
                    position: relative;
                    font-family: 'Segoe UI', 'Trebuchet MS', sans-serif;
                    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
                ">
                    <!-- Image de fond -->
                    <img src="{{ $imageData }}" style="
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        z-index: 1;
                    " alt="Certificate Background" />


                    <!-- Nom de l'étudiant -->
                    <div style="
                        position: absolute;
                        left: 50%;
                        top: 40%;
                        transform: translateX(-50%);
                        font-size: 17px;
                        font-weight: 700;
                        color: #1a3a52;
                        text-align: center;
                        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.08);
                        z-index: 10;
                        letter-spacing: 0.5px;
                        max-width: 650px;
                    ">{{ $user->userable->first_name ?? 'Utilisateur' }} {{ $user->userable->last_name ?? '' }}</div>

                    <!-- Texte "pour avoir terminé avec succès" -->


                    <!-- Titre du cours (multi-lignes si nécessaire) -->
                    @php
                        $courseTitle = $course_title;
                        $maxChars = 45;
                    @endphp
                    
                    @if(strlen($courseTitle) > $maxChars)
                        {{-- Titre long - afficher sur 2 lignes --}}
                        @php
                            $words = explode(' ', $courseTitle);
                            $line1 = '';
                            $line2 = '';
                            $currentLine = 1;
                            
                            foreach ($words as $word) {
                                if ($currentLine == 1 && strlen($line1 . ' ' . $word) <= $maxChars) {
                                    $line1 .= ($line1 ? ' ' : '') . $word;
                                } else {
                                    $currentLine = 2;
                                    $line2 .= ($line2 ? ' ' : '') . $word;
                                }
                            }
                        @endphp
                        
                        <div style="
                            position: absolute;
                            left: 50%;
                            top: 48.3%;
                            transform: translateX(-50%);
                            font-size: 18px;
                            font-weight: 600;
                            color: #2c5282;
                            text-align: center;
                            z-index: 10;
                            letter-spacing: 0.2px;
                            max-width: 550px;
                        ">{{ $line1 }}</div>
                        
                        @if($line2)
                        <div style="
                            position: absolute;
                            left: 50%;
                            top: 51.7%;
                            transform: translateX(-50%);
                            font-size: 18px;
                            font-weight: 600;
                            color: #2c5282;
                            text-align: center;
                            z-index: 10;
                            letter-spacing: 0.2px;
                            max-width: 550px;
                        ">{{ $line2 }}</div>
                        @endif
                    @else
                        {{-- Titre court - une seule ligne --}}
                        <div style="
                            position: absolute;
                            left: 50%;
                            top: 50%;
                            transform: translateX(-50%);
                            font-size: 18px;
                            font-weight: 600;
                            color: #2c5282;
                            text-align: center;
                            max-width: 550px;
                            z-index: 10;
                            letter-spacing: 0.2px;
                        ">{{ $courseTitle }}</div>
                    @endif

                    <!-- Date -->
                    <div style="
                        position: absolute;
                        left: 60%;
                        bottom: 33%;
                        font-size: 13px;
                        font-weight: 500;
                        color: #572571;
                        text-align: left;
                        z-index: 10;
                        letter-spacing: 0.2px;
                    "> Fait à Bamako, le {{ $completion_date }}</div>

                    <!-- Nom de l'instructeur -->
                    <div style="
                        position: absolute;
                        right: 377px;
                        bottom: 29%;
                        font-size: 13px;
                        font-weight: 400;
                        color: #000000;
                        text-align: center;
                        z-index: 10;
                        letter-spacing: 0.2px;
                    ">{{ $instructor_name }}</div>

                    <!-- N° du Certificat -->
                    <div style="
                        position: absolute;
                        left: 525px;
                        top: 524px;
                        transform: translateX(-50%);
                        font-size: 11px;
                        font-weight: 600;
                        color: #000000;
                        text-align: center;
                        z-index: 10;
                        letter-spacing: 0.8px;
                    ">{{ $certificate->certificate_id }}</div>

                </div>
            </div>
        </div>
    </div>
</body>
</html>

