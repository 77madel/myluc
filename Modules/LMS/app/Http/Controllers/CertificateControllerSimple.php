<?php

namespace Modules\LMS\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\LMS\Models\Certificate\UserCertificate;

class CertificateControllerSimple extends Controller
{
    /**
     * Télécharger le certificat en PDF
     */
    public function downloadPdf($certificateId)
    {
        try {
            // Récupérer le certificat avec ses relations
            $certificate = UserCertificate::with(['course.instructors.userable'])->findOrFail($certificateId);

            // Vérifier que l'utilisateur a le droit de télécharger ce certificat
            if (authCheck()->id !== $certificate->user_id) {
                abort(403, 'Vous n\'avez pas le droit de télécharger ce certificat.');
            }

            // Vérifier si le certificat a déjà été téléchargé
            if ($certificate->isDownloaded()) {
                return redirect()->back()->with('error', 'Ce certificat a déjà été téléchargé et ne peut plus être téléchargé.');
            }

            // Préparer les données
            $user = authCheck();
            
            // Formater la date en français (ex: 25 Octobre 2025)
            $moisFr = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
            if ($certificate->certificated_date) {
                $jour = $certificate->certificated_date->format('d');
                $mois = $moisFr[(int)$certificate->certificated_date->format('m')];
                $annee = $certificate->certificated_date->format('Y');
                $completion_date = $jour . ' ' . $mois . ' ' . $annee;
            } else {
                $jour = date('d');
                $mois = $moisFr[(int)date('m')];
                $annee = date('Y');
                $completion_date = $jour . ' ' . $mois . ' ' . $annee;
            }
            
            $course = $certificate->course;
            $course_title = $course ? $course->title : ($certificate->subject ?? 'Formation');
            $instructor_name = 'Instructeur';

            if ($course && $course->instructors->count() > 0) {
                $instructor = $course->instructors->first();
                    if ($instructor && $instructor->userable) {
                        $instructor_name = ($instructor->userable->first_name ?? '').' '.($instructor->userable->last_name ?? '');
                }
            }

            // NOUVELLE APPROCHE: Créer une image complète avec GD
            $imagePath = base_path('Modules/LMS/storage/app/public/lms/certificates/lms-RK7Fn0CzaE.jpeg');
            $studentName = ($user->userable->first_name ?? 'Utilisateur') . ' ' . ($user->userable->last_name ?? '');
            
            // Charger l'image de fond et la REDIMENSIONNER à une taille plus gérable
            $originalImage = imagecreatefromjpeg($imagePath);
            $originalWidth = imagesx($originalImage);   // 2340px
            $originalHeight = imagesy($originalImage);  // 1654px
            
            // Nouvelle taille: 1200×850px (plus facile à gérer, ratio similaire)
            $width = 1200;
            $height = 850;
            
            // Créer une nouvelle image redimensionnée
            $image = imagecreatetruecolor($width, $height);
            
            // Copier et redimensionner l'image originale
            imagecopyresampled($image, $originalImage, 0, 0, 0, 0, $width, $height, $originalWidth, $originalHeight);
            imagedestroy($originalImage);
            
            // Taille de référence du conteneur HTML
            $htmlWidth = 800;   // Largeur du conteneur HTML
            $htmlHeight = 600;  // Hauteur du conteneur HTML
            
            // Définir les couleurs (selon votre CSS)
            $colorBlue = imagecolorallocate($image, 26, 58, 82);      // #1a3a52
            $colorDarkBlue = imagecolorallocate($image, 44, 82, 130);  // #2c5282
            $colorPurple = imagecolorallocate($image, 87, 37, 113);    // #572571 (date)
            $colorBlack = imagecolorallocate($image, 0, 0, 0);         // #000000
            
            // === POSITIONS EXACTES SELON VOTRE VUE HTML ===
            // Utiliser imagettftext() pour des tailles de police personnalisées
            
            // Charger une police TTF (DejaVu Sans est incluse avec TCPDF)
            $fontPath = dirname(__DIR__, 3) . '/../../vendor/tecnickcom/tcpdf/fonts/dejavusans.php';
            $fontFile = dirname(__DIR__, 3) . '/../../vendor/tecnickcom/tcpdf/fonts/dejavusans.ttf';
            
            // PAS DE POLICE TTF DISPONIBLE - Utiliser imagestring (tailles 1-5 seulement)
            // On redimensionne l'image encore plus pour compenser
            if (true) {  // Toujours utiliser cette méthode simple
                // Redimensionner ENCORE pour que taille 5 soit visible
                $finalImage = imagecreatetruecolor(800, 600);
                imagecopyresampled($finalImage, $image, 0, 0, 0, 0, 800, 600, $width, $height);
                imagedestroy($image);
                $image = $finalImage;
                $width = 800;
                $height = 600;
                
                // Maintenant les positions sont 1:1 avec votre HTML !
                // UTILISATION DE imagettftext() AVEC FALLBACK DE POLICES
                
                // Liste des polices à essayer (dans l'ordre de priorité)
                $fontOptions = [
                    'C:/Windows/Fonts/segoeui.ttf',      // Segoe UI (Windows)
                    'C:/Windows/Fonts/trebuc.ttf',       // Trebuchet MS (Windows)
                    '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',  // Linux
                    dirname(__DIR__, 3) . '/../../vendor/tecnickcom/tcpdf/fonts/dejavusans.ttf',  // TCPDF
                ];
                
                // Trouver la première police disponible
                $fontPath = null;
                foreach ($fontOptions as $font) {
                    if (file_exists($font)) {
                        $fontPath = $font;
                        break;
                    }
                }
                
                // Si aucune police TTF n'est trouvée, utiliser la méthode imagestring()
                if (!$fontPath) {
                    \Log::warning('Aucune police TTF trouvée, utilisation de imagestring()');
                    $fontPath = null;  // Sera géré par le code imagestring() plus bas
                }
                
                // Redéfinir les couleurs sur la nouvelle image
                $colorBlue = imagecolorallocate($image, 26, 58, 82);
                $colorDarkBlue = imagecolorallocate($image, 44, 82, 130);
                $colorPurple = imagecolorallocate($image, 87, 37, 113);
                $colorBlack = imagecolorallocate($image, 0, 0, 0);
                
                // CHOISIR LA MÉTHODE : TTF (si police trouvée) ou imagestring() (sinon)
                if ($fontPath && file_exists($fontPath)) {
                    // ============ MÉTHODE TTF (avec polices personnalisées) ============
                    
                    // 1. NOM ÉTUDIANT: left: 50%, top: 40%
                    $fontSize = 15;  // Taille de police pour le nom
                    $bbox = imagettfbbox($fontSize, 0, $fontPath, $studentName);
                    $textWidth = $bbox[2] - $bbox[0];
                    $x = 390 - ($textWidth / 2);  // Centré à 400px (50% de 800)
                    $y = 257;  // 40% de 600 + ajustement pour baseline
                    imagettftext($image, $fontSize, 0, $x, $y, $colorBlack, $fontPath, $studentName);
                
                // 2. TITRE: left: 50%, top: 50% (sur plusieurs lignes si long)
                $maxCharsPerLine = 45;  // Maximum de caractères par ligne
                $courseTitleText = $course_title;
                $titleFontSize = 18;  // Taille de police pour le titre
                
                // Découper le titre en plusieurs lignes si nécessaire
                if (strlen($courseTitleText) > $maxCharsPerLine) {
                    // Titre long - afficher sur 2 lignes
                    $words = explode(' ', $courseTitleText);
                    $line1 = '';
                    $line2 = '';
                    $currentLine = 1;
                    
                    foreach ($words as $word) {
                        if ($currentLine == 1 && strlen($line1 . ' ' . $word) <= $maxCharsPerLine) {
                            $line1 .= ($line1 ? ' ' : '') . $word;
                    } else {
                            $currentLine = 2;
                            $line2 .= ($line2 ? ' ' : '') . $word;
                        }
                    }
                    
                    // Afficher ligne 1
                    $bbox = imagettfbbox($titleFontSize, 0, $fontPath, $line1);
                    $textWidth = $bbox[2] - $bbox[0];
                    $x = 370 - ($textWidth / 2);  // Vous aviez déjà ajusté à 370
                    $y = 310;  // Un peu plus haut + ajustement baseline
                    imagettftext($image, $titleFontSize, 0, $x, $y, $colorBlack, $fontPath, $line1);
                    
                    // Afficher ligne 2
                    if ($line2) {
                        $bbox = imagettfbbox($titleFontSize, 0, $fontPath, $line2);
                        $textWidth = $bbox[2] - $bbox[0];
                        $x = 400 - ($textWidth / 2);
                        $y = 335;  // En dessous + ajustement baseline
                        imagettftext($image, $titleFontSize, 0, $x, $y, $colorBlack, $fontPath, $line2);
                    }
                } else {
                    // Titre court - une seule ligne
                    $bbox = imagettfbbox($titleFontSize, 0, $fontPath, $courseTitleText);
                    $textWidth = $bbox[2] - $bbox[0];
                    $x = 400 - ($textWidth / 2);
                    $y = 320;  // 50% de 600 + ajustement baseline
                    imagettftext($image, $titleFontSize, 0, $x, $y, $colorBlack, $fontPath, $courseTitleText);
                }
                
                // 3. N° CERTIFICAT: left: 525px, top: 524px
                $certFontSize = 8;
                $bbox = imagettfbbox($certFontSize, 0, $fontPath, $certificate->certificate_id);
                $textWidth = $bbox[2] - $bbox[0];
                $x = 516 - ($textWidth / 2);
                $y = 535;  // Ajustement baseline
                imagettftext($image, $certFontSize, 0, $x, $y, $colorBlack, $fontPath, $certificate->certificate_id);
                
                // 4. DATE: left: 60%, bottom: 33% = top: 402px (67% de 600)
                $dateText = 'Fait à Bamako, le ' . $completion_date;  // Avec accent maintenant !
                $dateFontSize = 10;
                $x = 460;  // Déplacé à gauche
                $y = 400;  // Ajusté pour baseline
                imagettftext($image, $dateFontSize, 0, $x, $y, $colorPurple, $fontPath, $dateText);
                
                // 5. INSTRUCTEUR: au niveau de "Formateur"
                $instructorFontSize = 10;
                $x = 333;  // Position à droite
                $y = 425;  // Ajusté pour baseline
                imagettftext($image, $instructorFontSize, 0, $x, $y, $colorBlack, $fontPath, $instructor_name);
                    
                } else {
                    // ============ MÉTHODE IMAGESTRING (sans police TTF) ============
                    
                    // 1. NOM ÉTUDIANT: left: 50%, top: 40%
                    $x = 400 - (strlen($studentName) * 4);  // Centré à 400px (50% de 800)
                    $y = 240;  // 40% de 600 = 240px
                    imagestring($image, 5, $x, $y, utf8_decode($studentName), $colorBlue);
                    
                    // 2. TITRE: left: 50%, top: 50% (sur plusieurs lignes si long)
                    $maxCharsPerLine = 45;  // Maximum de caractères par ligne
                    $courseTitleDecoded = utf8_decode($course_title);
                    
                    // Découper le titre en plusieurs lignes si nécessaire
                    if (strlen($courseTitleDecoded) > $maxCharsPerLine) {
                        // Titre long - afficher sur 2 lignes
                        $words = explode(' ', $courseTitleDecoded);
                        $line1 = '';
                        $line2 = '';
                        $currentLine = 1;
                        
                        foreach ($words as $word) {
                            if ($currentLine == 1 && strlen($line1 . ' ' . $word) <= $maxCharsPerLine) {
                                $line1 .= ($line1 ? ' ' : '') . $word;
                            } else {
                                $currentLine = 2;
                                $line2 .= ($line2 ? ' ' : '') . $word;
                            }
                        }
                        
                        // Afficher ligne 1
                        $x = 400 - (strlen($line1) * 3);
                        $y = 290;  // Un peu plus haut
                        imagestring($image, 4, $x, $y, $line1, $colorDarkBlue);
                        
                        // Afficher ligne 2
                        if ($line2) {
                            $x = 400 - (strlen($line2) * 3);
                            $y = 310;  // En dessous
                            imagestring($image, 4, $x, $y, $line2, $colorDarkBlue);
                }
            } else {
                        // Titre court - une seule ligne
                        $x = 400 - (strlen($courseTitleDecoded) * 3);
                        $y = 300;  // 50% de 600 = 300px
                        imagestring($image, 4, $x, $y, $courseTitleDecoded, $colorDarkBlue);
                    }
                    
                    // 3. N° CERTIFICAT: left: 525px, top: 524px
                    $x = 493 - (strlen($certificate->certificate_id) * 2);
                    $y = 524;
                    imagestring($image, 2, $x, $y, $certificate->certificate_id, $colorBlack);
                    
                    // 4. DATE: left: 60%, bottom: 33% = top: 402px (67% de 600)
                    $dateText = 'Fait a Bamako, le ' . $completion_date;  // Sans accent pour UTF-8
                    $x = 460;  // Déplacé à gauche (au lieu de 500px)
                    $y = 385;  // Monté un peu (au lieu de 402px)
                    imagestring($image, 4, $x, $y, utf8_decode($dateText), $colorPurple);
                    
                    // 5. INSTRUCTEUR: au niveau de "Formateur" (à droite)
                    $x = 580;  // Déplacé à droite pour être sous "Formateur"
                    $y = 410;  // Monté un peu (au lieu de 426px)
                    imagestring($image, 4, $x, $y, utf8_decode($instructor_name), $colorBlack);
                }
            } elseif (false) {  // Code TTF désactivé
                // Utiliser TTF pour des tailles personnalisées
                
                // POSITIONS EXACTES DEPUIS LA VUE HTML
                // Image: 1200×850px | HTML: 800×600px
                
                // 1. NOM ÉTUDIANT: left: 50%, top: 40% (HTML: font-size: 17px)
                $fontSize = 140;  // Agrandi de 60 à 80
                $bbox = imagettfbbox($fontSize, 0, $fontFile, $studentName);
                $textWidth = $bbox[2] - $bbox[0];
                $x = ($width * 1.00) - ($textWidth / 2);  // left: 50% centré
                $y = $height * 0.40;  // top: 40%
                imagettftext($image, $fontSize, 0, $x, $y, $colorBlue, $fontFile, $studentName);
                
                // 2. TITRE DU COURS: left: 50%, top: 50% (HTML: font-size: 22px)
                $fontSize = 60;  // Agrandi de 45 à 60
                $courseDisplay = substr($course_title, 0, 60);
                $bbox = imagettfbbox($fontSize, 0, $fontFile, $courseDisplay);
                $textWidth = $bbox[2] - $bbox[0];
                $x = ($width * 0.50) - ($textWidth / 2);  // left: 50% centré
                $y = $height * 0.50;  // top: 50%
                imagettftext($image, $fontSize, 0, $x, $y, $colorDarkBlue, $fontFile, $courseDisplay);
                
                // 3. N° CERTIFICAT: left: 525px, top: 524px (HTML: font-size: 11px)
                $fontSize = 20;  // 11px × 2.4
                $bbox = imagettfbbox($fontSize, 0, $fontFile, $certificate->certificate_id);
                $textWidth = $bbox[2] - $bbox[0];
                $xHtml = 525 * ($width / $htmlWidth);  // Convertir 525px → pixels image
                $x = $xHtml - ($textWidth / 2);  // Centrer sur la position
                $y = 524 * ($height / $htmlHeight);  // Convertir 524px → pixels image
                imagettftext($image, $fontSize, 0, $x, $y, $colorBlack, $fontFile, $certificate->certificate_id);
                
                // 4. DATE: left: 60%, bottom: 33% = top: 67% (HTML: font-size: 13px)
                $fontSize = 28;  // 13px × 2.2
                $dateText = 'Fait à Bamako, le ' . $completion_date;
                $x = $width * 0.60;  // left: 60%
                $y = $height * 0.67;  // bottom: 33% = top: 67%
                imagettftext($image, $fontSize, 0, $x, $y, $colorPurple, $fontFile, $dateText);
                
                // 5. INSTRUCTEUR: right: 377px = left: 423px, bottom: 29% = top: 71% (HTML: font-size: 13px)
                $fontSize = 28;  // 13px × 2.2
                $x = 423 * ($width / $htmlWidth);  // right: 377px = left: 423px converti
                $y = $height * 0.71;  // bottom: 29% = top: 71%
                imagettftext($image, $fontSize, 0, $x, $y, $colorBlack, $fontFile, $instructor_name);
            }
            
            // Sauvegarder l'image temporaire
            $tempImagePath = storage_path('app/temp_cert_' . $certificate->id . '.png');
            imagepng($image, $tempImagePath, 9);
            imagedestroy($image);
            
            // Créer le PDF avec cette image
            $pdf = new \TCPDF('L', 'mm', 'A4', true, 'UTF-8', true);
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetMargins(0, 0, 0);
            $pdf->SetAutoPageBreak(false, 0);
            $pdf->AddPage();
            
            // Insérer l'image complète
            $pdf->Image($tempImagePath, 0, 0, 297, 210, 'PNG', '', '', false, 300, '', false, false, 0);
            
            // Nettoyer
            if (file_exists($tempImagePath)) {
                unlink($tempImagePath);
            }

            // Générer le PDF
            $pdfOutput = $pdf->Output('', 'S');
            $filename = 'Certificat_'.preg_replace('/[^a-zA-Z0-9_-]/', '_', $course_title).'_'.date('Y-m-d').'.pdf';

            // Marquer comme téléchargé
            $certificate->markAsDownloaded();

            // Retourner le PDF
            return response($pdfOutput, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la génération du PDF: '.$e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la génération du certificat PDF.');
        }
    }

    /**
     * Afficher le certificat en HTML (pas de PDF)
     */
    public function viewPdf($certificateId)
    {
        try {
            // Récupérer le certificat avec ses relations
            $certificate = UserCertificate::with(['course.instructors.userable'])->findOrFail($certificateId);

            // Vérifier que l'utilisateur a le droit de voir ce certificat
            if (authCheck()->id !== $certificate->user_id) {
                abort(403, 'Vous n\'avez pas le droit de voir ce certificat.');
            }

            // Préparer les données
            $user = authCheck();
            
            // Formater la date en français (ex: 25 Octobre 2025)
            $moisFr = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
            if ($certificate->certificated_date) {
                $jour = $certificate->certificated_date->format('d');
                $mois = $moisFr[(int)$certificate->certificated_date->format('m')];
                $annee = $certificate->certificated_date->format('Y');
                $completion_date = $jour . ' ' . $mois . ' ' . $annee;
            } else {
                $jour = date('d');
                $mois = $moisFr[(int)date('m')];
                $annee = date('Y');
                $completion_date = $jour . ' ' . $mois . ' ' . $annee;
            }

            // Utiliser la relation directe avec le cours
            $course = $certificate->course;
            $course_title = $course ? $course->title : ($certificate->subject ?? 'Formation');

            $instructor_name = 'Instructeur';

            if ($course) {
                $instructors = $course->instructors;

                if ($instructors->count() > 0) {
                    $instructor = $instructors->first();

                    if ($instructor && $instructor->userable) {
                        $instructor_name = ($instructor->userable->first_name ?? '').' '.($instructor->userable->last_name ?? '');
                    }
                }
            }

            // Retourner la vue HTML directement (pas de PDF)
            return view('portal::certificate.certificate-view-html', compact('certificate', 'user', 'instructor_name', 'completion_date', 'course_title'));

        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'affichage du certificat: '.$e->getMessage());

            return redirect()->back()->with('error', 'Erreur lors de l\'affichage du certificat.');
        }
    }

    /**
     * Afficher le certificat publiquement (pour partage sur réseaux sociaux)
     * Accessible via UUID sans authentification
     */
    public function showPublic(string $uuid)
    {
        try {
            // Récupérer le certificat par UUID public
            $certificate = UserCertificate::with(['user.userable', 'course.instructors.userable'])
                ->where('public_uuid', $uuid)
                ->firstOrFail();

            // Préparer les données
            $user = $certificate->user;
            $userable = $user->userable;
            
            // Nom de l'étudiant
            $student_name = $userable 
                ? ($userable->name ?? $userable->first_name . ' ' . $userable->last_name)
                : 'Étudiant';
            
            // Formater la date
            $moisFr = ['', 'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
            if ($certificate->certificated_date) {
                $jour = $certificate->certificated_date->format('d');
                $mois = $moisFr[(int)$certificate->certificated_date->format('m')];
                $annee = $certificate->certificated_date->format('Y');
                $completion_date = $jour . ' ' . $mois . ' ' . $annee;
            } else {
                $completion_date = date('d/m/Y');
            }
            
            $course = $certificate->course;
            $course_title = $course ? $course->title : ($certificate->subject ?? 'Formation');
            
            // Nom de l'instructeur
            $instructor_name = 'Instructeur';
            if ($course && $course->instructors->count() > 0) {
                $instructor = $course->instructors->first();
                if ($instructor && $instructor->userable) {
                    $instructor_name = $instructor->userable->name 
                        ?? $instructor->userable->first_name . ' ' . $instructor->userable->last_name 
                        ?? 'Instructeur';
                }
            }

            $platform_name = config('app.name', 'MyLMS');
            $certificate_number = $certificate->certificate_id;

            // URL de l'image du certificat (pour Open Graph)
            $certificateImageUrl = route('certificate.public.image', $uuid);
            
            // Message pour les réseaux sociaux
            $shareMessage = "🎓 Je suis fier(e) d'annoncer que j'ai obtenu mon certificat pour le cours '{$course_title}' !\n\nCertificat N° : {$certificate_number}\nDate d'obtention : {$completion_date}\n\n#Formation #Certificat #ApprentissageContinue";
            
            // Retourner la vue publique avec Open Graph meta tags
            return view('portals.certificate.public', compact(
                'certificate',
                'student_name',
                'course_title',
                'completion_date',
                'instructor_name',
                'platform_name',
                'certificate_number',
                'certificateImageUrl',
                'shareMessage'
            ));

        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'affichage public du certificat: '.$e->getMessage());
            abort(404, 'Certificat non trouvé.');
        }
    }

    /**
     * Enregistrer le partage d'un certificat (tracking)
     */
    public function trackShare(\Illuminate\Http\Request $request)
    {
        try {
            $validated = $request->validate([
                'certificate_id' => 'required|integer',
                'platform' => 'required|string|in:linkedin,facebook,twitter,instagram',
                'custom_message' => 'nullable|string'
            ]);

            // Enregistrer dans la table certificate_shares
            \DB::table('certificate_shares')->insert([
                'user_certificate_id' => $validated['certificate_id'],
                'user_id' => auth()->id(),
                'platform' => $validated['platform'],
                'custom_message' => $validated['custom_message'],
                'shared_at' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            \Log::info("📊 Partage enregistré", [
                'user_id' => auth()->id(),
                'certificate_id' => $validated['certificate_id'],
                'platform' => $validated['platform']
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Partage enregistré avec succès'
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'enregistrement du partage: '.$e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Erreur lors de l\'enregistrement'
            ], 500);
        }
    }

    /**
     * Générer et retourner l'image du certificat pour Open Graph
     */
    public function getPublicImage(string $uuid)
    {
        try {
            // Récupérer le certificat
            $certificate = UserCertificate::with(['user.userable', 'course.instructors.userable'])
                ->where('public_uuid', $uuid)
                ->firstOrFail();

            // Créer une image du certificat (1200x630 pour Open Graph)
            $width = 1200;
            $height = 630;
            $image = imagecreatetruecolor($width, $height);

            // Couleurs
            $white = imagecolorallocate($image, 255, 255, 255);
            $primary = imagecolorallocate($image, 16, 185, 129); // Vert
            $dark = imagecolorallocate($image, 31, 41, 55);
            $gray = imagecolorallocate($image, 107, 114, 128);

            // Fond blanc
            imagefill($image, 0, 0, $white);

            // Bordure
            imagerectangle($image, 20, 20, $width - 20, $height - 20, $primary);
            imagerectangle($image, 25, 25, $width - 25, $height - 25, $primary);

            // Texte (utiliser une police système)
            $fontPath = public_path('lms/frontend/assets/webfonts/lowball-neue/LowballNeueBold-w18A6.ttf');
            if (!file_exists($fontPath)) {
                $fontPath = null; // Utiliser la police par défaut
            }

            // Titre "CERTIFICAT"
            if ($fontPath) {
                imagettftext($image, 48, 0, 400, 150, $primary, $fontPath, 'CERTIFICAT');
            } else {
                imagestring($image, 5, 500, 100, 'CERTIFICAT', $primary);
            }

            // Nom de l'étudiant
            $studentName = $certificate->user->userable 
                ? ($certificate->user->userable->name ?? $certificate->user->userable->first_name . ' ' . $certificate->user->userable->last_name)
                : 'Étudiant';
            
            if ($fontPath) {
                imagettftext($image, 36, 0, 350, 250, $dark, $fontPath, $studentName);
            } else {
                imagestring($image, 5, 400, 200, $studentName, $dark);
            }

            // Cours
            $courseName = $certificate->course ? $certificate->course->title : $certificate->subject;
            if ($fontPath) {
                imagettftext($image, 28, 0, 300, 350, $dark, $fontPath, $courseName);
            } else {
                imagestring($image, 4, 350, 300, $courseName, $dark);
            }

            // Numéro et date
            $certInfo = "N° {$certificate->certificate_id} - " . ($certificate->certificated_date ? $certificate->certificated_date->format('d/m/Y') : date('d/m/Y'));
            if ($fontPath) {
                imagettftext($image, 20, 0, 400, 450, $gray, $fontPath, $certInfo);
            } else {
                imagestring($image, 3, 400, 400, $certInfo, $gray);
            }

            // Plateforme
            $platformName = config('app.name', 'MyLMS');
            if ($fontPath) {
                imagettftext($image, 24, 0, 450, 550, $primary, $fontPath, $platformName);
            } else {
                imagestring($image, 4, 450, 500, $platformName, $primary);
            }

            // Retourner l'image
            header('Content-Type: image/png');
            imagepng($image);
            imagedestroy($image);
            exit;

        } catch (\Exception $e) {
            \Log::error('Erreur génération image certificat: '.$e->getMessage());
            
            // Retourner une image par défaut
            $defaultImage = public_path('lms/frontend/assets/images/logo/Logo.png');
            if (file_exists($defaultImage)) {
                return response()->file($defaultImage);
            }
            
            abort(404);
        }
    }
}
