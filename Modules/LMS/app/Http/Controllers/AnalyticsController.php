<?php

namespace Modules\LMS\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\LMS\Services\AnalyticsService;
use Illuminate\Support\Facades\Log;

class AnalyticsController extends Controller
{
    protected $analyticsService;
    
    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }
    
    /**
     * Enregistrer les données de tracking
     */
    public function track(Request $request)
    {
        try {
            $data = [
                'session_id' => $request->session_id,
                'user_id' => auth()->id(),
                
                // Données client (JavaScript)
                'device_type' => $request->input('device.type'),
                'screen_width' => $request->input('device.screen_width'),
                'screen_height' => $request->input('device.screen_height'),
                
                // Données serveur
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'referrer' => $request->input('page.referrer'),
                
                // Page
                'page_url' => $request->input('page.url'),
                'page_title' => $request->input('page.title'),
                'time_on_page' => $request->time_on_page ?? 0,
                'scroll_depth' => $request->scroll_depth ?? 0,
            ];
            
            $this->analyticsService->track($data);
            
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            Log::error('Analytics tracking error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }
    
    /**
     * Enregistrer une conversion
     */
    public function trackConversion(Request $request)
    {
        $request->validate([
            'session_id' => 'required|string',
            'type' => 'required|in:signup,purchase,enroll',
        ]);
        
        $this->analyticsService->trackConversion(
            $request->session_id,
            $request->type
        );
        
        return response()->json(['status' => 'success']);
    }
}

