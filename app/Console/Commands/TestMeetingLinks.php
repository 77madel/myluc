<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MeetingLinkService;
use Modules\LMS\Models\Webinar;

class TestMeetingLinks extends Command
{
    protected $signature = 'webinar:test-meeting-links {webinar_id?}';
    protected $description = 'Test meeting link generation for webinars';

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

        $this->info("Test de génération de liens de réunion pour le webinaire: {$webinar->title}");
        $this->line("");

        $meetingService = new MeetingLinkService();

        // Test Teams
        $this->info("🔵 MICROSOFT TEAMS:");
        $teamsData = $meetingService->generateTeamsLink($webinar);
        $this->line("   URL: {$teamsData['meeting_url']}");
        $this->line("   ID: {$teamsData['meeting_id']}");
        $this->line("   Password: {$teamsData['meeting_password']}");
        $this->line("");

        // Test Zoom
        $this->info("🟠 ZOOM:");
        $zoomData = $meetingService->generateZoomLink($webinar);
        $this->line("   URL: {$zoomData['meeting_url']}");
        $this->line("   ID: {$zoomData['meeting_id']}");
        $this->line("   Password: {$zoomData['meeting_password']}");
        $this->line("");

        // Test Google Meet
        $this->info("🟢 GOOGLE MEET:");
        $meetData = $meetingService->generateGoogleMeetLink($webinar);
        $this->line("   URL: {$meetData['meeting_url']}");
        $this->line("   ID: {$meetData['meeting_id']}");
        $this->line("   Password: " . ($meetData['meeting_password'] ?? 'Aucun'));
        $this->line("");

        // Test scheduled meeting
        $this->info("📅 RÉUNION PROGRAMMÉE (Teams):");
        $scheduledData = $meetingService->generateScheduledMeeting($webinar, 'teams');
        $this->line("   URL: {$scheduledData['meeting_url']}");
        $this->line("   Titre: {$scheduledData['title']}");
        $this->line("   Début: {$scheduledData['start_time']}");
        $this->line("   Fin: {$scheduledData['end_time']}");
        $this->line("   Durée: {$scheduledData['duration']} minutes");
        $this->line("");

        $this->info("✅ Test terminé avec succès!");
    }
}

