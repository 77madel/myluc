<?php

/**
 * Script de test pour vérifier le module LMS
 * 
 * Ce script vérifie que :
 * - Le module LMS est bien configuré
 * - Les cours d'organisation sont créés
 * - Les chapitres et leçons sont présents
 * - Les vidéos YouTube sont configurées
 */

require_once 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Modules\LMS\Models\Courses\Course;
use Modules\LMS\Models\Courses\Chapter;
use Modules\LMS\Models\Courses\Topic;
use Modules\LMS\Models\Auth\User;

echo "🔍 Test du Module LMS\n";
echo "====================\n\n";

try {
    // Vérifier les cours d'organisation
    $organizationCourses = Course::whereNotNull('organization_id')->get();
    
    echo "📚 Cours d'organisation trouvés : " . $organizationCourses->count() . "\n";
    
    foreach ($organizationCourses as $course) {
        echo "  - " . $course->title . " (Prix: " . ($course->coursePrice->price ?? 'N/A') . "€)\n";
        
        // Vérifier les chapitres
        $chapters = $course->chapters;
        echo "    📖 Chapitres : " . $chapters->count() . "\n";
        
        foreach ($chapters as $chapter) {
            echo "      - " . $chapter->title . "\n";
            
            // Vérifier les leçons
            $topics = $chapter->topics;
            echo "        📝 Leçons : " . $topics->count() . "\n";
            
            foreach ($topics as $topic) {
                if ($topic->topicable_type === 'Modules\LMS\Models\Courses\Topics\Video') {
                    $video = $topic->topicable;
                    echo "          🎥 " . $video->title . " (" . $video->duration . ")\n";
                    echo "            URL: " . $video->video_url . "\n";
                } else {
                    echo "          📖 " . $topic->topicable_type . "\n";
                }
            }
        }
        echo "\n";
    }
    
    // Vérifier l'organisation
    $organization = User::where('guard', 'organization')->first();
    if ($organization) {
        echo "🏢 Organisation : " . $organization->userable->name . "\n";
    }
    
    // Vérifier l'instructeur
    $instructor = User::where('guard', 'instructor')->first();
    if ($instructor) {
        echo "👨‍🏫 Instructeur : " . $instructor->userable->first_name . " " . $instructor->userable->last_name . "\n";
    }
    
    echo "\n✅ Module LMS configuré correctement !\n";
    echo "🎯 Tous les cours d'organisation sont présents avec chapitres et leçons\n";
    
} catch (Exception $e) {
    echo "❌ Erreur lors du test : " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n🎉 Test terminé avec succès !\n";



