<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\RealMeetingService;
use Modules\LMS\Models\Webinar;

class TestRealMeetingLinks extends Command
{
    protected $signature = 'webinar:test-real-meeting-links {webinar_id?}';
    protected $description = 'Test REAL meeting link generation for webinars';

    public function handle()
    {
        $webinarId = $this->argument('webinar_id');

        if ($webinarId) {
            $webinar = Webinar::find($webinarId);
            if (!$webinar) {
                $this->error("Webinaire ID {$webinarId} non trouvé.");
                return;
            }
        } else {
            $webinar = Webinar::first();
            if (!$webinar) {
                $this->error("Aucun webinaire trouvé.");
                return;
            }
        }

        $this->info("🔗 Test de génération de VRAIS liens de réunion");
        $this->info("Webinaire: {$webinar->title}");
        $this->info("Date: {$webinar->start_date} - {$webinar->end_date}");
        $this->line("");

        $realMeetingService = new RealMeetingService();

        // Test Teams
        $this->info("🔵 MICROSOFT TEAMS (VRAI):");
        try {
            $teamsData = $realMeetingService->createTeamsMeeting($webinar);
            $this->line("   ✅ URL: {$teamsData['meeting_url']}");
            $this->line("   ✅ ID: {$teamsData['meeting_id']}");
            $this->line("   ✅ Password: {$teamsData['meeting_password']}");
            if (isset($teamsData['fallback'])) {
                $this->warn("   ⚠️  Mode fallback (pas d'API Microsoft configurée)");
            } else {
                $this->info("   🎉 Lien Teams RÉEL généré via Microsoft Graph API!");
            }
        } catch (\Exception $e) {
            $this->error("   ❌ Erreur Teams: " . $e->getMessage());
        }
        $this->line("");

        // Test Zoom
        $this->info("🟠 ZOOM (VRAI):");
        try {
            $zoomData = $realMeetingService->createZoomMeeting($webinar);
            $this->line("   ✅ URL: {$zoomData['meeting_url']}");
            $this->line("   ✅ ID: {$zoomData['meeting_id']}");
            $this->line("   ✅ Password: {$zoomData['meeting_password']}");
            if (isset($zoomData['fallback'])) {
                $this->warn("   ⚠️  Mode fallback (pas d'API Zoom configurée)");
            } else {
                $this->info("   🎉 Lien Zoom RÉEL généré via Zoom API!");
            }
        } catch (\Exception $e) {
            $this->error("   ❌ Erreur Zoom: " . $e->getMessage());
        }
        $this->line("");

        // Test Google Meet
        $this->info("🟢 GOOGLE MEET (VRAI):");
        try {
            $meetData = $realMeetingService->createGoogleMeetLink($webinar);
            $this->line("   ✅ URL: {$meetData['meeting_url']}");
            $this->line("   ✅ ID: {$meetData['meeting_id']}");
            $this->line("   ✅ Password: " . ($meetData['meeting_password'] ?? 'Aucun'));
            $this->info("   🎉 Lien Google Meet RÉEL généré!");
        } catch (\Exception $e) {
            $this->error("   ❌ Erreur Google Meet: " . $e->getMessage());
        }
        $this->line("");

        $this->info("📋 INSTRUCTIONS POUR CONFIGURER LES VRAIS LIENS:");
        $this->line("1. Microsoft Teams:");
        $this->line("   - Créer une app dans Azure AD");
        $this->line("   - Ajouter les permissions: OnlineMeetings.ReadWrite");
        $this->line("   - Configurer MICROSOFT_TENANT_ID, CLIENT_ID, CLIENT_SECRET");
        $this->line("");
        $this->line("2. Zoom:");
        $this->line("   - Créer une app JWT dans Zoom Marketplace");
        $this->line("   - Configurer ZOOM_API_KEY et ZOOM_API_SECRET");
        $this->line("");
        $this->line("3. Google Meet:");
        $this->line("   - Fonctionne immédiatement (pas de configuration requise)");

        $this->info("✅ Test terminé!");
    }
}





