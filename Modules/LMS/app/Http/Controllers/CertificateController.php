<?php

namespace Modules\LMS\Http\Controllers;

use Illuminate\Routing\Controller;
use Modules\LMS\Models\Certificate\UserCertificate;

class CertificateController extends Controller
{
    /**
     * Télécharger le certificat en PDF
     */
    public function downloadPdf($certificateId)
    {
        try {
            // Récupérer le certificat
            $certificate = UserCertificate::findOrFail($certificateId);

            // Vérifier que l'utilisateur a le droit de télécharger ce certificat
            if (authCheck()->id !== $certificate->user_id) {
                abort(403, 'Vous n\'avez pas le droit de télécharger ce certificat.');
            }

            // Préparer les données pour le PDF
            $user = authCheck();
            $completion_date = $certificate->certificated_date ? $certificate->certificated_date->format('d/m/Y') : date('d/m/Y');

            // Récupérer le cours depuis le certificat
            $course = null;
            $course_title = $certificate->subject ?? 'Formation';

            // Essayer de récupérer le cours depuis le quiz si disponible
            if ($certificate->quiz_id) {
                try {
                    $quiz = \Modules\LMS\Models\Courses\Topics\Quiz::find($certificate->quiz_id);
                    if ($quiz && $quiz->chapter && $quiz->chapter->course) {
                        $course = $quiz->chapter->course;
                        $course_title = $course->title;
                    }
                } catch (\Exception $e) {
                    \Log::warning('Erreur lors de la récupération du cours via quiz', ['error' => $e->getMessage()]);
                }
            }

            // Si pas de cours trouvé, créer un objet par défaut
            if (! $course) {
                \Log::warning('Certificat sans cours associé', ['certificate_id' => $certificateId]);
                $course = (object) [
                    'id' => null,
                    'title' => $course_title,
                    'instructors' => null,
                ];
            }

            // Vérifier que les données essentielles existent
            if (! $user) {
                abort(404, 'Utilisateur non trouvé');
            }

            \Log::info('Génération PDF avec template HTML - Données:', [
                'certificate_id' => $certificateId,
                'user_id' => $user->id,
                'course_id' => $course ? $course->id : 'null',
                'course_title' => $course ? $course->title : 'null',
                'completion_date' => $completion_date,
            ]);

            // Récupérer l'instructeur du cours
            $instructor_name = 'Instructeur';
            if ($course && $course->id) {
                try {
                    $instructor = $course->instructors()->first();
                    if ($instructor && $instructor->userable) {
                        $instructor_name = ($instructor->userable->first_name ?? '').' '.($instructor->userable->last_name ?? '');
                    }
                } catch (\Exception $e) {
                    \Log::warning('Erreur lors de la récupération de l\'instructeur', ['error' => $e->getMessage()]);
                }
            }

            // Template HTML du certificat
            $html = '
            <div class="certificate-template-container" id="certificateImg" style="
                background: url(\'{{ Storage::url('lms/certificates/lms-RK7Fn0CzaE.jpeg') }}\'); 
                background-repeat: no-repeat; 
                background-size: 100% 100%;
                width: 800px;
                height: 600px;
                margin: 0 auto;
                position: relative;
                font-family: \'Segoe UI\', \'Trebuchet MS\', sans-serif;
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
                
                <!-- Nom de l\'étudiant - Point focal principal -->
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
                ">'.$user->userable->first_name.' '.$user->userable->last_name.'</div>
                
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
                ">'.$course_title.'</div>
                
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
                ">LMS Platform</div>
                
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
                ">Date: '.$completion_date.'</div>
                
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
                
                <!-- Nom de l\'instructeur - Bas droit -->
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
                ">'.$instructor_name.'</div>
                
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
                
            </div>';

            // Utiliser DomPDF pour convertir le HTML en PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'landscape');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
            ]);

            // Générer le PDF
            $pdfOutput = $pdf->output();
            \Log::info('PDF TCPDF généré - Taille:', ['size' => strlen($pdfOutput)]);
            \Log::info('PDF TCPDF généré - Début:', ['start' => substr($pdfOutput, 0, 50)]);

            // Vérifier si c'est un vrai PDF
            if (strpos($pdfOutput, '%PDF') !== 0) {
                \Log::error('Le contenu généré n\'est pas un PDF valide');

                return response()->json(['error' => 'Erreur de génération PDF'], 500);
            }

            // Nom du fichier
            $courseTitle = $certificate->course->title ?? 'Formation';
            $filename = 'Certificat_'.preg_replace('/[^a-zA-Z0-9_-]/', '_', $courseTitle).'_'.date('Y-m-d').'.pdf';

            // Forcer le téléchargement avec les bons headers
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
     * Afficher le certificat en PDF dans le navigateur
     */
    public function viewPdf($certificateId)
    {
        try {
            // Récupérer le certificat
            $certificate = UserCertificate::findOrFail($certificateId);

            // Vérifier que l'utilisateur a le droit de voir ce certificat
            if (authCheck()->id !== $certificate->user_id) {
                abort(403, 'Vous n\'avez pas le droit de voir ce certificat.');
            }

            // Préparer les données pour le PDF
            $user = authCheck();
            $completion_date = $certificate->certificated_date ? $certificate->certificated_date->format('d/m/Y') : date('d/m/Y');

            // Récupérer le cours depuis le certificat
            $course = null;
            $course_title = $certificate->subject ?? 'Formation';

            // Essayer de récupérer le cours depuis le quiz si disponible
            if ($certificate->quiz_id) {
                try {
                    $quiz = \Modules\LMS\Models\Courses\Topics\Quiz::find($certificate->quiz_id);
                    if ($quiz && $quiz->chapter && $quiz->chapter->course) {
                        $course = $quiz->chapter->course;
                        $course_title = $course->title;
                    }
                } catch (\Exception $e) {
                    \Log::warning('Erreur lors de la récupération du cours via quiz', ['error' => $e->getMessage()]);
                }
            }

            // Si pas de cours trouvé, créer un objet par défaut
            if (! $course) {
                \Log::warning('Certificat sans cours associé', ['certificate_id' => $certificateId]);
                $course = (object) [
                    'id' => null,
                    'title' => $course_title,
                    'instructors' => null,
                ];
            }

            // Vérifier que les données essentielles existent
            if (! $user) {
                abort(404, 'Utilisateur non trouvé');
            }

            // Récupérer l'instructeur du cours
            $instructor_name = 'Instructeur';
            if ($course && $course->id) {
                try {
                    $instructor = $course->instructors()->first();
                    if ($instructor && $instructor->userable) {
                        $instructor_name = ($instructor->userable->first_name ?? '').' '.($instructor->userable->last_name ?? '');
                    }
                } catch (\Exception $e) {
                    \Log::warning('Erreur lors de la récupération de l\'instructeur', ['error' => $e->getMessage()]);
                }
            }

            // Template HTML du certificat
            $html = '
            <div class="certificate-template-container" id="certificateImg" style="
                background: url(\'{{ Storage::url('lms/certificates/lms-RK7Fn0CzaE.jpeg') }}\'); 
                background-repeat: no-repeat; 
                background-size: 100% 100%;
                width: 800px;
                height: 600px;
                margin: 0 auto;
                position: relative;
                font-family: \'Segoe UI\', \'Trebuchet MS\', sans-serif;
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
                
                <!-- Nom de l\'étudiant - Point focal principal -->
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
                ">'.$user->userable->first_name.' '.$user->userable->last_name.'</div>
                
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
                ">'.$course_title.'</div>
                
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
                ">LMS Platform</div>
                
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
                ">Date: '.$completion_date.'</div>
                
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
                
                <!-- Nom de l\'instructeur - Bas droit -->
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
                ">'.$instructor_name.'</div>
                
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
                
            </div>';

            // Utiliser DomPDF pour convertir le HTML en PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'landscape');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
            ]);

            // Afficher le PDF dans le navigateur
            return $pdf->stream('certificat.pdf');

        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'affichage du PDF: '.$e->getMessage());

            return redirect()->back()->with('error', 'Erreur lors de l\'affichage du certificat PDF.');
        }
    }
}
