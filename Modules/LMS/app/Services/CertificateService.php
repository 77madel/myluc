<?php

namespace Modules\LMS\Services;

use Modules\LMS\Models\Certificate\Certificate;
use Modules\LMS\Models\Certificate\UserCertificate;
use Modules\LMS\Models\Courses\Course;
use Modules\LMS\Models\TopicProgress;
use Modules\LMS\Models\ChapterProgress;
use Illuminate\Support\Facades\Log;

class CertificateService
{
    /**
     * Vérifier si un cours est éligible pour un certificat
     */
    public static function isCourseEligibleForCertificate(int $userId, int $courseId): bool
    {
        Log::info("🔍 Vérification de l'éligibilité au certificat pour l'utilisateur {$userId} et le cours {$courseId}");

        // Vérifier si le cours a la certification activée
        $course = Course::find($courseId);
        if (!$course) {
            Log::info("❌ Le cours {$courseId} n'existe pas");
            return false;
        }

        // Vérifier les paramètres du cours
        $courseSetting = $course->courseSetting;
        if (!$courseSetting || !$courseSetting->is_certificate) {
            Log::info("❌ Le cours {$courseId} n'a pas la certification activée");
            return false;
        }

        // Vérifier si tous les chapitres sont terminés
        $totalChapters = $course->chapters()->count();
        $completedChapters = ChapterProgress::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->where('status', 'completed')
            ->count();

        Log::info("📊 Chapitres: {$completedChapters}/{$totalChapters} terminés");

        if ($completedChapters < $totalChapters) {
            Log::info("❌ Tous les chapitres ne sont pas terminés");
            return false;
        }

        // Vérifier si tous les topics sont terminés (via les chapitres)
        $totalTopics = 0;
        foreach ($course->chapters as $chapter) {
            $totalTopics += $chapter->topics()->count();
        }

        $completedTopics = TopicProgress::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->where('status', 'completed')
            ->count();

        Log::info("📊 Topics: {$completedTopics}/{$totalTopics} terminés");

        if ($completedTopics < $totalTopics) {
            Log::info("❌ Tous les topics ne sont pas terminés");
            return false;
        }

        Log::info("✅ Le cours est éligible pour un certificat");
        return true;
    }

    /**
     * Générer un certificat pour un cours
     */
    public static function generateCertificate(int $userId, int $courseId): ?UserCertificate
    {
        Log::info("🎓 Génération du certificat pour l'utilisateur {$userId} et le cours {$courseId}");

        // Récupérer le cours
        $course = Course::find($courseId);
        if (!$course) {
            Log::error("❌ Cours non trouvé: {$courseId}");
            return null;
        }

        // Vérifier l'éligibilité
        Log::info("🔍 Vérification de l'éligibilité...");
        if (!self::isCourseEligibleForCertificate($userId, $courseId)) {
            Log::info("❌ Le cours n'est pas éligible pour un certificat");
            return null;
        }
        Log::info("✅ Le cours est éligible pour un certificat");

        // Vérifier si un certificat existe déjà
        $existingCertificate = UserCertificate::where('user_id', $userId)
            ->where('subject', $course->title)
            ->where('type', 'course')
            ->first();

        if ($existingCertificate) {
            Log::info("⚠️ Un certificat existe déjà pour ce cours");
            return $existingCertificate;
        }

        // Récupérer le modèle de certificat
        Log::info("🔍 Recherche du modèle de certificat...");
        $certificateTemplate = Certificate::where('type', 'course')->first();
        if (!$certificateTemplate) {
            Log::error("❌ Aucun modèle de certificat trouvé");
            return null;
        }
        Log::info("✅ Modèle de certificat trouvé: {$certificateTemplate->title}");

        // Récupérer les informations du cours et de l'utilisateur
        Log::info("🔍 Récupération des informations utilisateur...");
        $user = \Modules\LMS\Models\User::find($userId);
        if (!$user) {
            Log::error("❌ Utilisateur non trouvé: {$userId}");
            return null;
        }

        // Récupérer le nom depuis le userable
        $userable = $user->userable;
        $userName = 'Étudiant';
        if ($userable) {
            $userName = $userable->name ?? $userable->first_name . ' ' . $userable->last_name ?? 'Étudiant';
        }

        Log::info("✅ Utilisateur trouvé: {$userName}");

        // Le cours est déjà récupéré plus haut

        // Générer le numéro unique du certificat
        $certificateId = self::generateUniqueCertificateId();

        // Remplacer les variables dans le contenu
        $certificateContent = self::replaceCertificateVariables(
            $certificateTemplate->certificate_content,
            $user,
            $course
        );

        // Créer le certificat
        $userCertificate = UserCertificate::create([
            'user_id' => $userId,
            'course_id' => $courseId, // Ajouter le course_id
            'certificate_id' => $certificateId,
            'type' => 'course',
            'subject' => $course->title,
            'certificate_content' => $certificateContent,
            'certificated_date' => now(),
        ]);

        Log::info("✅ Certificat généré avec succès: {$certificateId}");

        // SUPPRIMER l'enrollment après l'obtention du certificat (soft delete)
        try {
            $enrollment = \Modules\LMS\Models\Purchase\PurchaseDetails::where('user_id', $userId)
                ->where('course_id', $courseId)
                ->where('type', 'enrolled')
                ->first();
            
            if ($enrollment) {
                // Soft delete (suppression douce - conserve les données avec deleted_at)
                $enrollment->delete();
                Log::info("🗑️ Enrollment supprimé pour l'utilisateur {$userId} au cours {$courseId} - Certificat obtenu");
            } else {
                Log::warning("⚠️ Aucun enrollment trouvé à supprimer pour l'utilisateur {$userId} et le cours {$courseId}");
            }
        } catch (\Exception $e) {
            Log::error("❌ Erreur lors de la suppression de l'enrollment: " . $e->getMessage());
        }

        // Envoyer une notification (à implémenter)
        self::sendCertificateNotification($user, $course, $userCertificate);

        return $userCertificate;
    }

    /**
     * Générer un numéro unique de certificat
     */
    private static function generateUniqueCertificateId(): string
    {
        do {
            $certificateId = 'LUC-' . date('Y') . '-' . strtoupper(uniqid());
        } while (UserCertificate::where('certificate_id', $certificateId)->exists());

        return $certificateId;
    }

    /**
     * Remplacer les variables dans le contenu du certificat
     */
    private static function replaceCertificateVariables(string $content, $user, $course): string
    {
        // Récupérer le nom complet de l'utilisateur depuis le userable
        $userable = $user->userable;
        $studentName = 'Étudiant';
        $studentFirstName = '';
        $studentLastName = '';

        if ($userable) {
            $studentName = $userable->name ?? $userable->first_name . ' ' . $userable->last_name ?? 'Étudiant';
            $studentFirstName = $userable->first_name ?? '';
            $studentLastName = $userable->last_name ?? '';
        }

        // Récupérer le nom de la plateforme
        $platformName = config('app.name', 'MyLMS');

        // Récupérer le nom de l'instructeur
        $instructorName = 'Instructeur';
        if ($course->instructors && $course->instructors->count() > 0) {
            $instructor = $course->instructors->first();
            if ($instructor && $instructor->userable) {
                $instructorName = $instructor->userable->name ?? $instructor->userable->first_name . ' ' . $instructor->userable->last_name ?? 'Instructeur';
            }
        }

        // Date de génération du certificat
        $certificateDate = now()->format('d/m/Y');

        // Remplacer les variables
        $replacements = [
            '{student_name}' => $studentName,
            '{student_first_name}' => $studentFirstName,
            '{student_last_name}' => $studentLastName,
            '{student_email}' => $user->email ?? '',
            '{platform_name}' => $platformName,
            '{course_title}' => $course->title,
            '{instructor_name}' => $instructorName,
            '{course_completed_date}' => $certificateDate,
            '{certificate_date}' => $certificateDate,
            '{current_date}' => $certificateDate,
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $content);
    }

    /**
     * Envoyer une notification de certificat
     */
    public static function sendCertificateNotification($user, $course, $userCertificate): void
    {
        try {
            Log::info("🔔 Envoi de notification de certificat à l'utilisateur {$user->id}");

            // Envoyer la notification via le système Laravel
            $user->notify(new \Modules\LMS\Notifications\NotifyCertificateGenerated($userCertificate, $course, $user));

            Log::info("✅ Notification certificat envoyée avec succès");

        } catch (\Exception $e) {
            Log::error("❌ Erreur envoi notification certificat: " . $e->getMessage());
        }
    }
}
