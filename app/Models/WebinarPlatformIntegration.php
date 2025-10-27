<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebinarPlatformIntegration extends Model
{
    use HasFactory;

    protected $fillable = [
        'platform',
        'name',
        'description',
        'api_key',
        'api_secret',
        'client_id',
        'client_secret',
        'webhook_url',
        'redirect_uri',
        'settings',
        'is_active',
        'is_default',
        'rate_limit_per_hour',
        'rate_limit_per_day',
        'last_api_call',
        'api_calls_today',
        'supports_recording',
        'supports_polling',
        'supports_breakout_rooms',
        'supports_waiting_room',
        'supports_chat',
        'supports_screen_sharing',
        'supports_whiteboard',
        'webhook_secret',
        'webhook_enabled',
        'webhook_events',
        'status',
        'last_error',
        'last_successful_call',
        'success_rate'
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'last_api_call' => 'datetime',
        'supports_recording' => 'boolean',
        'supports_polling' => 'boolean',
        'supports_breakout_rooms' => 'boolean',
        'supports_waiting_room' => 'boolean',
        'supports_chat' => 'boolean',
        'supports_screen_sharing' => 'boolean',
        'supports_whiteboard' => 'boolean',
        'webhook_enabled' => 'boolean',
        'webhook_events' => 'array',
        'last_successful_call' => 'datetime'
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeByPlatform($query, $platform)
    {
        return $query->where('platform', $platform);
    }

    // Methods
    public function canMakeApiCall()
    {
        if (!$this->is_active) {
            return false;
        }

        // Check rate limits
        $now = now();
        $today = $now->startOfDay();

        if ($this->last_api_call && $this->last_api_call->isToday()) {
            if ($this->api_calls_today >= $this->rate_limit_per_day) {
                return false;
            }
        }

        return true;
    }

    public function recordApiCall()
    {
        $now = now();

        if (!$this->last_api_call || !$this->last_api_call->isToday()) {
            $this->api_calls_today = 0;
        }

        $this->update([
            'last_api_call' => $now,
            'api_calls_today' => $this->api_calls_today + 1,
            'last_successful_call' => $now
        ]);
    }

    public function recordApiError($error)
    {
        $this->update([
            'last_error' => $error,
            'status' => 'error'
        ]);
    }

    public function createMeeting($webinar)
    {
        if (!$this->canMakeApiCall()) {
            throw new \Exception('API rate limit exceeded for platform: ' . $this->platform);
        }

        try {
            $meetingData = $this->callPlatformApi('create_meeting', [
                'title' => $webinar->title,
                'start_time' => $webinar->start_date,
                'duration' => $webinar->duration_minutes,
                'password' => $webinar->meeting_password,
                'max_participants' => $webinar->max_participants,
                'settings' => [
                    'allow_recording' => $webinar->allow_recording,
                    'allow_chat' => $webinar->allow_chat,
                    'allow_questions' => $webinar->allow_questions,
                    'allow_screen_sharing' => $webinar->allow_screen_sharing,
                    'waiting_room' => $this->supports_waiting_room
                ]
            ]);

            $this->recordApiCall();

            return $meetingData;
        } catch (\Exception $e) {
            $this->recordApiError($e->getMessage());
            throw $e;
        }
    }

    public function updateMeeting($webinar, $meetingId)
    {
        if (!$this->canMakeApiCall()) {
            throw new \Exception('API rate limit exceeded for platform: ' . $this->platform);
        }

        try {
            $meetingData = $this->callPlatformApi('update_meeting', [
                'meeting_id' => $meetingId,
                'title' => $webinar->title,
                'start_time' => $webinar->start_date,
                'duration' => $webinar->duration_minutes
            ]);

            $this->recordApiCall();

            return $meetingData;
        } catch (\Exception $e) {
            $this->recordApiError($e->getMessage());
            throw $e;
        }
    }

    public function deleteMeeting($meetingId)
    {
        if (!$this->canMakeApiCall()) {
            throw new \Exception('API rate limit exceeded for platform: ' . $this->platform);
        }

        try {
            $this->callPlatformApi('delete_meeting', [
                'meeting_id' => $meetingId
            ]);

            $this->recordApiCall();
        } catch (\Exception $e) {
            $this->recordApiError($e->getMessage());
            throw $e;
        }
    }

    public function getMeetingDetails($meetingId)
    {
        if (!$this->canMakeApiCall()) {
            throw new \Exception('API rate limit exceeded for platform: ' . $this->platform);
        }

        try {
            $meetingData = $this->callPlatformApi('get_meeting', [
                'meeting_id' => $meetingId
            ]);

            $this->recordApiCall();

            return $meetingData;
        } catch (\Exception $e) {
            $this->recordApiError($e->getMessage());
            throw $e;
        }
    }

    public function getMeetingParticipants($meetingId)
    {
        if (!$this->canMakeApiCall()) {
            throw new \Exception('API rate limit exceeded for platform: ' . $this->platform);
        }

        try {
            $participants = $this->callPlatformApi('get_participants', [
                'meeting_id' => $meetingId
            ]);

            $this->recordApiCall();

            return $participants;
        } catch (\Exception $e) {
            $this->recordApiError($e->getMessage());
            throw $e;
        }
    }

    private function callPlatformApi($action, $data = [])
    {
        // This would implement the actual API calls to the specific platform
        // For now, return mock data

        switch ($this->platform) {
            case 'zoom':
                return $this->callZoomApi($action, $data);
            case 'teams':
                return $this->callTeamsApi($action, $data);
            case 'google_meet':
                return $this->callGoogleMeetApi($action, $data);
            default:
                throw new \Exception('Unsupported platform: ' . $this->platform);
        }
    }

    private function callZoomApi($action, $data)
    {
        // Implement Zoom API calls
        // This would use the Zoom SDK or REST API
        return [
            'meeting_id' => 'zoom_' . uniqid(),
            'join_url' => 'https://zoom.us/j/' . uniqid(),
            'password' => $data['password'] ?? null
        ];
    }

    private function callTeamsApi($action, $data)
    {
        // Implement Microsoft Teams API calls
        // This would use the Microsoft Graph API
        return [
            'meeting_id' => 'teams_' . uniqid(),
            'join_url' => 'https://teams.microsoft.com/l/meetup-join/' . uniqid(),
            'password' => null
        ];
    }

    private function callGoogleMeetApi($action, $data)
    {
        // Implement Google Meet API calls
        // This would use the Google Calendar API
        return [
            'meeting_id' => 'meet_' . uniqid(),
            'join_url' => 'https://meet.google.com/' . uniqid(),
            'password' => null
        ];
    }

    public function handleWebhook($payload)
    {
        if (!$this->webhook_enabled) {
            return;
        }

        // Verify webhook signature
        if (!$this->verifyWebhookSignature($payload)) {
            throw new \Exception('Invalid webhook signature');
        }

        // Process webhook events
        $event = $payload['event'] ?? null;

        if (in_array($event, $this->webhook_events ?? [])) {
            $this->processWebhookEvent($event, $payload);
        }
    }

    private function verifyWebhookSignature($payload)
    {
        // Implement webhook signature verification
        // This depends on the specific platform
        return true; // Placeholder
    }

    private function processWebhookEvent($event, $payload)
    {
        // Process different webhook events
        switch ($event) {
            case 'meeting.started':
                $this->handleMeetingStarted($payload);
                break;
            case 'meeting.ended':
                $this->handleMeetingEnded($payload);
                break;
            case 'participant.joined':
                $this->handleParticipantJoined($payload);
                break;
            case 'participant.left':
                $this->handleParticipantLeft($payload);
                break;
            case 'recording.completed':
                $this->handleRecordingCompleted($payload);
                break;
        }
    }

    private function handleMeetingStarted($payload)
    {
        // Handle meeting started event
        $meetingId = $payload['meeting_id'] ?? null;
        if ($meetingId) {
            $webinar = Webinar::where('meeting_id', $meetingId)->first();
            if ($webinar) {
                $webinar->startWebinar();
            }
        }
    }

    private function handleMeetingEnded($payload)
    {
        // Handle meeting ended event
        $meetingId = $payload['meeting_id'] ?? null;
        if ($meetingId) {
            $webinar = Webinar::where('meeting_id', $meetingId)->first();
            if ($webinar) {
                $webinar->endWebinar();
            }
        }
    }

    private function handleParticipantJoined($payload)
    {
        // Handle participant joined event
        $meetingId = $payload['meeting_id'] ?? null;
        $participantId = $payload['participant_id'] ?? null;

        if ($meetingId && $participantId) {
            $webinar = Webinar::where('meeting_id', $meetingId)->first();
            if ($webinar) {
                // Update attendance
                $webinar->updateAttendance($participantId, now());
            }
        }
    }

    private function handleParticipantLeft($payload)
    {
        // Handle participant left event
        $meetingId = $payload['meeting_id'] ?? null;
        $participantId = $payload['participant_id'] ?? null;

        if ($meetingId && $participantId) {
            $webinar = Webinar::where('meeting_id', $meetingId)->first();
            if ($webinar) {
                // Update attendance
                $webinar->updateAttendance($participantId, null, now());
            }
        }
    }

    private function handleRecordingCompleted($payload)
    {
        // Handle recording completed event
        $meetingId = $payload['meeting_id'] ?? null;
        $recordingUrl = $payload['recording_url'] ?? null;

        if ($meetingId && $recordingUrl) {
            $webinar = Webinar::where('meeting_id', $meetingId)->first();
            if ($webinar) {
                $webinar->update(['recording_url' => $recordingUrl]);
            }
        }
    }
}




