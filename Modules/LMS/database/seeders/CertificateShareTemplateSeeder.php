<?php

namespace Modules\LMS\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CertificateShareTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            [
                'platform' => 'linkedin',
                'template_text' => "🎓 Je suis fier(e) d'annoncer que j'ai obtenu mon certificat pour le cours '{course_title}' !

Cette formation sur {platform_name} m'a permis d'acquérir de nouvelles compétences et d'approfondir mes connaissances.

Certificat N° : {certificate_id}
Date d'obtention : {certificate_date}

Un grand merci à {instructor_name} pour cet excellent cours !

#Formation #Certificat #ApprentissageContinue #DeveloppementProfessionnel",
                'is_active' => true,
                'order' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'platform' => 'facebook',
                'template_text' => "🎉 Excellente nouvelle ! Je viens d'obtenir mon certificat pour le cours '{course_title}' !

📚 Formation : {course_title}
📅 Date d'obtention : {certificate_date}
🏫 Plateforme : {platform_name}
🎓 Certificat N° : {certificate_id}

Cette expérience d'apprentissage était enrichissante et je recommande vivement ce cours à tous ceux qui souhaitent développer leurs compétences !

#Formation #Certificat #Apprentissage #Réussite",
                'is_active' => true,
                'order' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'platform' => 'twitter',
                'template_text' => "🎓 Certificat obtenu pour '{course_title}' sur {platform_name} ! 🎉

Date : {certificate_date}
Certificat N° : {certificate_id}

#Formation #Certificat #Apprentissage #Réussite",
                'is_active' => true,
                'order' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'platform' => 'instagram',
                'template_text' => "🎓 Nouveau certificat obtenu ! 

Cours : {course_title}
Date : {certificate_date}
Plateforme : {platform_name}

#formation #certificat #apprentissage #réussite #education #{course_title_hashtag}",
                'is_active' => true,
                'order' => 4,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ];

        foreach ($templates as $template) {
            DB::table('certificate_share_templates')->updateOrInsert(
                ['platform' => $template['platform']],
                $template
            );
        }
    }
}

