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
            $imagePath = base_path('Modules/LMS/storage/app/public/lms/certificates/lms-B7ZmOUUgXO.jpeg');
            $studentName = ($user->userable->first_name ?? 'Utilisateur') . ' ' . ($user->userable->last_name ?? '');
            
            // Charger l'image de fond
            $image = imagecreatefromjpeg($imagePath);
            $width = imagesx($image);   // 2340px
            $height = imagesy($image);  // 1654px
            
            // Taille de référence du conteneur HTML
            $htmlWidth = 800;   // Largeur du conteneur HTML
            $htmlHeight = 600;  // Hauteur du conteneur HTML
            
            // Définir les couleurs (selon votre CSS)
            $colorBlue = imagecolorallocate($image, 26, 58, 82);      // #1a3a52
            $colorDarkBlue = imagecolorallocate($image, 44, 82, 130);  // #2c5282
            $colorPurple = imagecolorallocate($image, 87, 37, 113);    // #572571 (date)
            $colorBlack = imagecolorallocate($image, 0, 0, 0);         // #000000
            
            // === POSITIONS EXACTES SELON VOTRE VUE HTML ===
            
            // 1. NOM ÉTUDIANT: left: 50%, top: 40%
            $fontSize = 5; // Taille max
            $x = ($width / 2) - (strlen($studentName) * 12);  // Centré
            $y = ($htmlHeight * 0.40) * ($height / $htmlHeight);  // top: 40%
            imagestring($image, $fontSize, $x, $y, $studentName, $colorBlue);
            
            // 2. TITRE DU COURS: left: 50%, top: 50%
            $fontSize = 4;
            $x = ($width / 2) - (strlen($course_title) * 6);  // Centré
            $y = ($htmlHeight * 0.50) * ($height / $htmlHeight);  // top: 50%
            imagestring($image, $fontSize, $x, $y, substr($course_title, 0, 60), $colorDarkBlue);
            
            // 3. N° CERTIFICAT: left: 525px, top: 524px
            $fontSize = 3;
            $x = 525 * ($width / $htmlWidth);   // Convertir 525px HTML → pixels image
            $y = 524 * ($height / $htmlHeight); // Convertir 524px HTML → pixels image
            imagestring($image, $fontSize, $x, $y, $certificate->certificate_id, $colorBlack);
            
            // 4. DATE: left: 60%, bottom: 33% (= top: 67%)
            $fontSize = 3;
            $dateText = 'Fait à Bamako, le ' . $completion_date;
            $x = $width * 0.60;  // left: 60%
            $y = ($htmlHeight * 0.67) * ($height / $htmlHeight);  // bottom: 33% = top: 67%
            imagestring($image, $fontSize, $x, $y, $dateText, $colorPurple);
            
            // 5. INSTRUCTEUR: right: 377px, bottom: 29% (= top: 71%)
            $fontSize = 3;
            // right: 377px signifie left: 800px - 377px = 423px
            $x = (423) * ($width / $htmlWidth);  // Convertir 423px HTML → pixels image
            $y = ($htmlHeight * 0.71) * ($height / $htmlHeight);  // bottom: 29% = top: 71%
            imagestring($image, $fontSize, $x, $y, $instructor_name, $colorBlack);
            
            // Sauvegarder l'image temporaire
            $tempImagePath = storage_path('app/temp_cert_' . $certificate->id . '.png');
            imagepng($image, $tempImagePath, 9);
            imagedestroy($image);
            
            // Créer le PDF avec cette image
            $pdf = new \TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);
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
}
