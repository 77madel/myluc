<?php

namespace Modules\LMS\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\LMS\Models\Auth\Admin;
use Modules\LMS\Models\Auth\User;
use Modules\LMS\Models\Category;
use Modules\LMS\Models\Subject;
use Modules\LMS\Models\Level;
use Modules\LMS\Models\TimeZone;
use Modules\LMS\Models\Courses\Course;
use Modules\LMS\Models\Courses\CoursePrice;
use Modules\LMS\Models\Courses\CourseSetting;
use Modules\LMS\Models\Courses\Chapter;
use Modules\LMS\Models\Courses\Topic;
use Modules\LMS\Models\Courses\Topics\Video;
use Modules\LMS\Models\Courses\Topics\Lecture;
use Modules\LMS\Models\Courses\TopicType;
use Modules\LMS\Models\Language;

class OrganizationCoursesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // =================================================================
        // 1. CRÉATION DE L'ORGANISATION
        // =================================================================
        $organization = User::updateOrCreate(
            ['email' => 'organization@example.com'],
            [
                'email' => 'organization@example.com',
                'guard' => 'organization',
                'status' => 1,
            ]
        );

        // Créer le profil utilisateur pour l'organisation
        $organization->userable()->updateOrCreate(
            ['user_id' => $organization->id],
            [
                'first_name' => 'Tech',
                'last_name' => 'Academy',
                'name' => 'Tech Academy',
                'phone' => '+1234567890',
                'address' => '123 Tech Street, Digital City',
            ]
        );

        // =================================================================
        // 2. CRÉATION DE L'INSTRUCTEUR
        // =================================================================
        $instructor = User::updateOrCreate(
            ['email' => 'instructor@example.com'],
            [
                'email' => 'instructor@example.com',
                'guard' => 'instructor',
                'status' => 1,
            ]
        );

        // Créer le profil utilisateur pour l'instructeur
        $instructor->userable()->updateOrCreate(
            ['user_id' => $instructor->id],
            [
                'first_name' => 'Jean',
                'last_name' => 'Dupont',
                'name' => 'Jean Dupont',
                'phone' => '+1234567891',
                'address' => '456 Instructor Avenue',
            ]
        );

        // =================================================================
        // 3. CRÉATION DES DONNÉES DE BASE
        // =================================================================
        
        // Catégorie
        $category = Category::updateOrCreate(
            ['slug' => 'technologie'],
            [
                'title' => 'Technologie & Innovation',
            ]
        );

        // Sujet
        $subject = Subject::updateOrCreate(
            ['slug' => 'programmation-web'],
            [
                'name' => 'Programmation Web',
            ]
        );

        // Langue
        $language = Language::updateOrCreate(
            ['slug' => 'francais'],
            [
                'name' => 'Français',
                'code' => 'fr',
            ]
        );

        // Niveau
        $level = Level::updateOrCreate(
            ['slug' => 'debutant'],
            [
                'name' => 'Débutant',
            ]
        );

        // Fuseau horaire
        $timeZone = TimeZone::updateOrCreate(
            ['name' => 'Europe/Paris'],
            [
                'name' => 'Europe/Paris',
                'offset' => '+01:00',
            ]
        );

        // Types de topics
        $videoTopicType = TopicType::updateOrCreate(
            ['slug' => 'video'],
            [
                'name' => 'Vidéo',
                'slug' => 'video',
            ]
        );

        $lectureTopicType = TopicType::updateOrCreate(
            ['slug' => 'lecture'],
            [
                'name' => 'Lecture',
                'slug' => 'lecture',
            ]
        );

        // =================================================================
        // 4. CRÉATION DES COURS
        // =================================================================
        
        $courses = [
            [
                'title' => 'Introduction à React en 2 minutes',
                'short_description' => 'Découvrez les bases de React en seulement 2 minutes avec ce cours express.',
                'description' => '<p>Ce cours rapide vous introduit aux concepts fondamentaux de React :</p><ul><li>Composants React</li><li>JSX et rendu</li><li>État et props</li><li>Cycle de vie des composants</li></ul><p>Parfait pour les débutants qui veulent une introduction rapide à React.</p>',
                'duration' => '2 minutes',
                'video_src_type' => 'youtube',
                'demo_url' => 'https://www.youtube.com/watch?v=Ke90Tje7VS0', // Vidéo React de 2 minutes
                'status' => 'Approved',
                'price' => 29.99,
                'currency_id' => 1, // EUR
            ],
            [
                'title' => 'CSS Grid en 90 secondes',
                'short_description' => 'Maîtrisez CSS Grid en moins de 2 minutes avec des exemples pratiques.',
                'description' => '<p>Apprenez CSS Grid rapidement :</p><ul><li>Définition des grilles</li><li>Positionnement des éléments</li><li>Responsive design avec Grid</li><li>Exemples concrets</li></ul><p>Un cours express pour comprendre CSS Grid sans perdre de temps.</p>',
                'duration' => '1.5 minutes',
                'video_src_type' => 'youtube',
                'demo_url' => 'https://www.youtube.com/watch?v=0xMQfnTU6oE', // Vidéo CSS Grid courte
                'status' => 'Approved',
                'price' => 19.99,
                'currency_id' => 1, // EUR
            ],
        ];

        foreach ($courses as $courseData) {
            // Créer le cours
            $course = Course::updateOrCreate(
                [
                    'slug' => Str::slug($courseData['title'])
                ],
                [
                    'title' => $courseData['title'],
                    'slug' => Str::slug($courseData['title']),
                    'short_description' => $courseData['short_description'],
                    'description' => $courseData['description'],
                    'duration' => $courseData['duration'],
                    'video_src_type' => $courseData['video_src_type'],
                    'demo_url' => $courseData['demo_url'],
                    'status' => $courseData['status'],
                    'organization_id' => $organization->id,
                    'category_id' => $category->id,
                    'subject_id' => $subject->id,
                    'time_zone_id' => $timeZone->id,
                    'thumbnail' => 'default-course-thumbnail.jpg', // Image par défaut
                ]
            );

            // Associer l'instructeur au cours
            $course->instructors()->sync([$instructor->id]);

            // Associer la langue au cours
            $course->languages()->sync([$language->id]);

            // Associer le niveau au cours
            $course->levels()->sync([$level->id]);

            // Créer le prix du cours
            CoursePrice::updateOrCreate(
                ['course_id' => $course->id],
                [
                    'course_id' => $course->id,
                    'price' => $courseData['price'],
                    'currency_id' => $courseData['currency_id'],
                    'discount_flag' => 0,
                    'platform_fee' => 2.99, // Frais de plateforme
                ]
            );

            // Créer les paramètres du cours
            CourseSetting::updateOrCreate(
                ['course_id' => $course->id],
                [
                    'course_id' => $course->id,
                    'seat_capacity' => 100,
                    'has_support' => 1,
                    'is_certificate' => 1, // Certificat activé
                    'is_upcoming' => 0,
                    'is_free' => 0, // Cours payant
                    'is_live' => 0,
                    'is_subscribe' => 0,
                ]
            );

            echo "✅ Cours créé : {$course->title} - Prix : {$courseData['price']}€\n";

            // =================================================================
            // 5. CRÉATION DES CHAPITRES ET LEÇONS
            // =================================================================
            
            if ($courseData['title'] === 'Introduction à React en 2 minutes') {
                // Chapitre 1 : Introduction
                $chapter1 = Chapter::create([
                    'course_id' => $course->id,
                    'title' => 'Introduction à React',
                    'order' => 1,
                ]);

                // Leçon 1 : Vidéo YouTube courte
                $video1 = Video::create([
                    'topic_type_id' => $videoTopicType->id,
                    'title' => 'Qu\'est-ce que React ?',
                    'duration' => '2 minutes',
                    'video_src_type' => 'youtube',
                    'video_url' => 'https://www.youtube.com/watch?v=Ke90Tje7VS0',
                ]);

                Topic::create([
                    'chapter_id' => $chapter1->id,
                    'course_id' => $course->id,
                    'topicable_id' => $video1->id,
                    'topicable_type' => Video::class,
                    'order' => 1,
                ]);

                // Leçon 2 : Lecture
                $lecture1 = Lecture::create([
                    'topic_type_id' => $lectureTopicType->id,
                ]);

                Topic::create([
                    'chapter_id' => $chapter1->id,
                    'course_id' => $course->id,
                    'topicable_id' => $lecture1->id,
                    'topicable_type' => Lecture::class,
                    'order' => 2,
                ]);

                // Chapitre 2 : Concepts de base
                $chapter2 = Chapter::create([
                    'course_id' => $course->id,
                    'title' => 'Concepts de base',
                    'order' => 2,
                ]);

                // Leçon 3 : Vidéo YouTube courte
                $video2 = Video::create([
                    'topic_type_id' => $videoTopicType->id,
                    'title' => 'Composants et JSX',
                    'duration' => '1.5 minutes',
                    'video_src_type' => 'youtube',
                    'video_url' => 'https://www.youtube.com/watch?v=Ke90Tje7VS0',
                ]);

                Topic::create([
                    'chapter_id' => $chapter2->id,
                    'course_id' => $course->id,
                    'topicable_id' => $video2->id,
                    'topicable_type' => Video::class,
                    'order' => 1,
                ]);

                echo "  📖 Chapitres et leçons créés pour React\n";

            } elseif ($courseData['title'] === 'CSS Grid en 90 secondes') {
                // Chapitre 1 : Introduction CSS Grid
                $chapter1 = Chapter::create([
                    'course_id' => $course->id,
                    'title' => 'Introduction à CSS Grid',
                    'order' => 1,
                ]);

                // Leçon 1 : Vidéo YouTube courte
                $video1 = Video::create([
                    'topic_type_id' => $videoTopicType->id,
                    'title' => 'Définir une grille CSS',
                    'duration' => '90 secondes',
                    'video_src_type' => 'youtube',
                    'video_url' => 'https://www.youtube.com/watch?v=0xMQfnTU6oE',
                ]);

                Topic::create([
                    'chapter_id' => $chapter1->id,
                    'course_id' => $course->id,
                    'topicable_id' => $video1->id,
                    'topicable_type' => Video::class,
                    'order' => 1,
                ]);

                // Leçon 2 : Lecture
                $lecture1 = Lecture::create([
                    'topic_type_id' => $lectureTopicType->id,
                ]);

                Topic::create([
                    'chapter_id' => $chapter1->id,
                    'course_id' => $course->id,
                    'topicable_id' => $lecture1->id,
                    'topicable_type' => Lecture::class,
                    'order' => 2,
                ]);

                // Chapitre 2 : Positionnement
                $chapter2 = Chapter::create([
                    'course_id' => $course->id,
                    'title' => 'Positionnement des éléments',
                    'order' => 2,
                ]);

                // Leçon 3 : Vidéo YouTube courte
                $video2 = Video::create([
                    'topic_type_id' => $videoTopicType->id,
                    'title' => 'Grid areas et placement',
                    'duration' => '1 minute',
                    'video_src_type' => 'youtube',
                    'video_url' => 'https://www.youtube.com/watch?v=0xMQfnTU6oE',
                ]);

                Topic::create([
                    'chapter_id' => $chapter2->id,
                    'course_id' => $course->id,
                    'topicable_id' => $video2->id,
                    'topicable_type' => Video::class,
                    'order' => 1,
                ]);

                echo "  📖 Chapitres et leçons créés pour CSS Grid\n";
            }
        }

        echo "\n🎉 Seeder OrganizationCoursesSeeder exécuté avec succès !\n";
        echo "📚 2 cours créés avec des vidéos YouTube courtes (1-2 minutes)\n";
        echo "💰 Cours payants avec prix : 29.99€ et 19.99€\n";
        echo "🏢 Organisation : Tech Academy\n";
        echo "👨‍🏫 Instructeur : Jean Dupont\n";
        echo "🏆 Certification activée pour les deux cours\n";
        echo "📖 Chapitres et leçons ajoutés avec des vidéos YouTube courtes\n";
    }
}
