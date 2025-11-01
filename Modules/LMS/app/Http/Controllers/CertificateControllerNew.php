<?php

namespace Modules\LMS\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\LMS\Models\Certificate\UserCertificate;
use Illuminate\Routing\Controller;

class CertificateControllerNew extends Controller
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
            
            // Préparer les données pour le PDF
            $user = authCheck();
            $completion_date = $certificate->certificated_date ? $certificate->certificated_date->format('d/m/Y') : date('d/m/Y');
            
            // Récupérer l'instructeur du cours
            $instructor_name = 'Instructeur';
            \Log::info('Debug certificat - quiz_id:', ['quiz_id' => $certificate->quiz_id]);
            
            // Essayer d'abord via le quiz si disponible
            if ($certificate->quiz_id) {
                try {
                    $quiz = \Modules\LMS\Models\Courses\Topics\Quiz::find($certificate->quiz_id);
                    \Log::info('Debug certificat - quiz trouvé:', ['quiz' => $quiz ? 'oui' : 'non']);
                    
                    if ($quiz && $quiz->chapter && $quiz->chapter->course) {
                        $course = $quiz->chapter->course;
                        \Log::info('Debug certificat - cours trouvé via quiz:', ['course_id' => $course->id, 'course_title' => $course->title]);
                        
                        // Charger les instructeurs avec leurs relations userable
                        $instructors = $course->instructors()->with('userable')->get();
                        \Log::info('Debug certificat - instructeurs trouvés:', ['count' => $instructors->count()]);
                        
                        if ($instructors->count() > 0) {
                            $instructor = $instructors->first();
                            \Log::info('Debug certificat - instructeur:', ['instructor_id' => $instructor->id, 'userable_type' => $instructor->userable_type]);
                            
                            if ($instructor && $instructor->userable) {
                                $instructor_name = ($instructor->userable->first_name ?? '') . ' ' . ($instructor->userable->last_name ?? '');
                                \Log::info('Debug certificat - nom instructeur:', ['instructor_name' => $instructor_name]);
                            } else {
                                \Log::warning('Debug certificat - userable null:', ['instructor' => $instructor ? 'exists' : 'null']);
                            }
                        } else {
                            \Log::warning('Debug certificat - aucun instructeur trouvé pour le cours');
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning('Erreur lors de la récupération de l\'instructeur via quiz', ['error' => $e->getMessage()]);
                }
            } else {
                // Si pas de quiz_id, essayer de récupérer le cours via UserCourseExam
                \Log::info('Debug certificat - pas de quiz_id, recherche via UserCourseExam');
                
                try {
                    // Chercher via UserCourseExam qui a le même user_id et un cours
                    $userExam = \Modules\LMS\Models\Auth\UserCourseExam::where('user_id', $certificate->user_id)
                        ->with(['course.instructors.userable'])
                        ->first();
                    
                    \Log::info('Debug certificat - UserCourseExam trouvé:', [
                        'exam_id' => $userExam ? $userExam->id : 'null',
                        'course_id' => $userExam && $userExam->course ? $userExam->course->id : 'null',
                        'course_title' => $userExam && $userExam->course ? $userExam->course->title : 'null'
                    ]);
                    
                    if ($userExam && $userExam->course) {
                        $course = $userExam->course;
                        $instructors = $course->instructors;
                        \Log::info('Debug certificat - instructeurs trouvés via UserCourseExam:', ['count' => $instructors->count()]);
                        
                        if ($instructors->count() > 0) {
                            $instructor = $instructors->first();
                            \Log::info('Debug certificat - instructeur via UserCourseExam:', [
                                'instructor_id' => $instructor->id, 
                                'userable_type' => $instructor->userable_type,
                                'userable_id' => $instructor->userable_id
                            ]);
                            
                            if ($instructor && $instructor->userable) {
                                $instructor_name = ($instructor->userable->first_name ?? '') . ' ' . ($instructor->userable->last_name ?? '');
                                \Log::info('Debug certificat - nom instructeur via UserCourseExam:', ['instructor_name' => $instructor_name]);
                            } else {
                                \Log::warning('Debug certificat - userable null via UserCourseExam:', ['instructor' => $instructor ? 'exists' : 'null']);
                            }
                        } else {
                            \Log::warning('Debug certificat - aucun instructeur trouvé via UserCourseExam');
                        }
                    } else {
                        \Log::warning('Debug certificat - aucun UserCourseExam trouvé pour user_id:', ['user_id' => $certificate->user_id]);
                    }
                } catch (\Exception $e) {
                    \Log::warning('Erreur lors de la récupération de l\'instructeur via UserCourseExam', ['error' => $e->getMessage()]);
                }
            }
            
            // Utiliser la vue pdf-template.blade.php
            $html = view('portal::certificate.pdf-template', compact('certificate', 'user', 'instructor_name', 'completion_date'))->render();
            
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
            
            // Nom du fichier
            $filename = 'Certificat_' . $certificate->certificate_id . '_' . date('Y-m-d') . '.pdf';
            
            // Forcer le téléchargement avec les bons headers
            return response($pdfOutput, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0'
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la génération du PDF: ' . $e->getMessage());
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
            
            // Récupérer l'instructeur du cours
            $instructor_name = 'Instructeur';
            \Log::info('Debug certificat - quiz_id:', ['quiz_id' => $certificate->quiz_id]);
            
            // Essayer d'abord via le quiz si disponible
            if ($certificate->quiz_id) {
                try {
                    $quiz = \Modules\LMS\Models\Courses\Topics\Quiz::find($certificate->quiz_id);
                    \Log::info('Debug certificat - quiz trouvé:', ['quiz' => $quiz ? 'oui' : 'non']);
                    
                    if ($quiz && $quiz->chapter && $quiz->chapter->course) {
                        $course = $quiz->chapter->course;
                        \Log::info('Debug certificat - cours trouvé via quiz:', ['course_id' => $course->id, 'course_title' => $course->title]);
                        
                        // Charger les instructeurs avec leurs relations userable
                        $instructors = $course->instructors()->with('userable')->get();
                        \Log::info('Debug certificat - instructeurs trouvés:', ['count' => $instructors->count()]);
                        
                        if ($instructors->count() > 0) {
                            $instructor = $instructors->first();
                            \Log::info('Debug certificat - instructeur:', ['instructor_id' => $instructor->id, 'userable_type' => $instructor->userable_type]);
                            
                            if ($instructor && $instructor->userable) {
                                $instructor_name = ($instructor->userable->first_name ?? '') . ' ' . ($instructor->userable->last_name ?? '');
                                \Log::info('Debug certificat - nom instructeur:', ['instructor_name' => $instructor_name]);
                            } else {
                                \Log::warning('Debug certificat - userable null:', ['instructor' => $instructor ? 'exists' : 'null']);
                            }
                        } else {
                            \Log::warning('Debug certificat - aucun instructeur trouvé pour le cours');
                        }
                    }
                } catch (\Exception $e) {
                    \Log::warning('Erreur lors de la récupération de l\'instructeur via quiz', ['error' => $e->getMessage()]);
                }
            } else {
                // Si pas de quiz_id, essayer de récupérer le cours via UserCourseExam
                \Log::info('Debug certificat - pas de quiz_id, recherche via UserCourseExam');
                
                try {
                    // Chercher via UserCourseExam qui a le même user_id et un cours
                    $userExam = \Modules\LMS\Models\Auth\UserCourseExam::where('user_id', $certificate->user_id)
                        ->with(['course.instructors.userable'])
                        ->first();
                    
                    \Log::info('Debug certificat - UserCourseExam trouvé:', [
                        'exam_id' => $userExam ? $userExam->id : 'null',
                        'course_id' => $userExam && $userExam->course ? $userExam->course->id : 'null',
                        'course_title' => $userExam && $userExam->course ? $userExam->course->title : 'null'
                    ]);
                    
                    if ($userExam && $userExam->course) {
                        $course = $userExam->course;
                        $instructors = $course->instructors;
                        \Log::info('Debug certificat - instructeurs trouvés via UserCourseExam:', ['count' => $instructors->count()]);
                        
                        if ($instructors->count() > 0) {
                            $instructor = $instructors->first();
                            \Log::info('Debug certificat - instructeur via UserCourseExam:', [
                                'instructor_id' => $instructor->id, 
                                'userable_type' => $instructor->userable_type,
                                'userable_id' => $instructor->userable_id
                            ]);
                            
                            if ($instructor && $instructor->userable) {
                                $instructor_name = ($instructor->userable->first_name ?? '') . ' ' . ($instructor->userable->last_name ?? '');
                                \Log::info('Debug certificat - nom instructeur via UserCourseExam:', ['instructor_name' => $instructor_name]);
                            } else {
                                \Log::warning('Debug certificat - userable null via UserCourseExam:', ['instructor' => $instructor ? 'exists' : 'null']);
                            }
                        } else {
                            \Log::warning('Debug certificat - aucun instructeur trouvé via UserCourseExam');
                        }
                    } else {
                        \Log::warning('Debug certificat - aucun UserCourseExam trouvé pour user_id:', ['user_id' => $certificate->user_id]);
                    }
                } catch (\Exception $e) {
                    \Log::warning('Erreur lors de la récupération de l\'instructeur via UserCourseExam', ['error' => $e->getMessage()]);
                }
            }
            
            // Utiliser la vue pdf-template.blade.php
            $html = view('portal::certificate.pdf-template', compact('certificate', 'user', 'instructor_name', 'completion_date'))->render();
            
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
            \Log::error('Erreur lors de l\'affichage du PDF: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de l\'affichage du certificat PDF.');
        }
    }
}
