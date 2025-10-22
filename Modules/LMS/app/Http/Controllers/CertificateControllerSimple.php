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

            // Préparer les données pour le PDF
            $user = authCheck();
            $completion_date = $certificate->certificated_date ? $certificate->certificated_date->format('d/m/Y') : date('d/m/Y');

            // Utiliser la relation directe avec le cours
            $course = $certificate->course;
            $course_title = $course ? $course->title : ($certificate->subject ?? 'Formation');

            $instructor_name = 'Instructeur';
            \Log::info('Debug certificat - course_id:', ['course_id' => $certificate->course_id]);

            if ($course) {
                \Log::info('Debug certificat - cours trouvé via relation directe:', ['course_id' => $course->id, 'course_title' => $course->title]);

                $instructors = $course->instructors;
                \Log::info('Debug certificat - instructeurs trouvés:', ['count' => $instructors->count()]);

                if ($instructors->count() > 0) {
                    $instructor = $instructors->first();
                    \Log::info('Debug certificat - instructeur:', [
                        'instructor_id' => $instructor->id,
                        'userable_type' => $instructor->userable_type,
                        'userable_id' => $instructor->userable_id,
                    ]);

                    if ($instructor && $instructor->userable) {
                        // L'instructeur est un User avec userable_type = 'Instructor'
                        $instructor_name = ($instructor->userable->first_name ?? '').' '.($instructor->userable->last_name ?? '');
                        \Log::info('Debug certificat - nom instructeur:', ['instructor_name' => $instructor_name]);
                    } else {
                        \Log::warning('Debug certificat - userable null:', ['instructor' => $instructor ? 'exists' : 'null']);
                    }
                } else {
                    \Log::warning('Debug certificat - aucun instructeur trouvé pour le cours');
                }
            } else {
                \Log::warning('Debug certificat - aucun cours trouvé via relation directe');
            }

            // Utiliser la vue pdf-template.blade.php
            $html = view('portal::certificate.pdf-template', compact('certificate', 'user', 'instructor_name', 'completion_date', 'course_title'))->render();

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

            // Vérifier que le contenu généré est bien un PDF
            if (strpos($pdfOutput, '%PDF') !== 0) {
                \Log::error('Le contenu généré n\'est pas un PDF valide');

                return response()->json(['error' => 'Erreur de génération PDF'], 500);
            }

            // Nom du fichier
            $filename = 'Certificat_'.preg_replace('/[^a-zA-Z0-9_-]/', '_', $course_title).'_'.date('Y-m-d').'.pdf';

            // Marquer le certificat comme téléchargé
            $certificate->markAsDownloaded();

            // Retourner le PDF en téléchargement
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
     * Afficher le certificat en PDF
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

            // Préparer les données pour le PDF
            $user = authCheck();
            $completion_date = $certificate->certificated_date ? $certificate->certificated_date->format('d/m/Y') : date('d/m/Y');

            // Utiliser la relation directe avec le cours
            $course = $certificate->course;
            $course_title = $course ? $course->title : ($certificate->subject ?? 'Formation');

            $instructor_name = 'Instructeur';

            if ($course) {
                $instructors = $course->instructors;

                if ($instructors->count() > 0) {
                    $instructor = $instructors->first();

                    if ($instructor && $instructor->userable) {
                        // L'instructeur est un User avec userable_type = 'Instructor'
                        $instructor_name = ($instructor->userable->first_name ?? '').' '.($instructor->userable->last_name ?? '');
                    }
                }
            }

            // Utiliser la vue pdf-template.blade.php
            $html = view('portal::certificate.pdf-template', compact('certificate', 'user', 'instructor_name', 'completion_date', 'course_title'))->render();

            // Utiliser DomPDF pour convertir le HTML en PDF
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($html);
            $pdf->setPaper('A4', 'landscape');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
            ]);

            // Retourner le PDF en stream
            return $pdf->stream('certificat.pdf');

        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'affichage du PDF: '.$e->getMessage());

            return redirect()->back()->with('error', 'Erreur lors de l\'affichage du certificat PDF.');
        }
    }
}
