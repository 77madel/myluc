<?php

namespace Modules\LMS\Services;

use Modules\LMS\Models\Analytics\UserAnalytics;
use Modules\LMS\Models\Analytics\PageView;
use Modules\LMS\Models\Analytics\UserSession;
use Jenssegers\Agent\Agent;
use GeoIp2\Database\Reader;
use Illuminate\Support\Facades\Log;

class AnalyticsService
{
    protected $geoReader = null;
    
    public function __construct()
    {
        // Initialiser le lecteur GeoIP2 si la base existe
        $geoDbPath = storage_path('app/geoip/GeoLite2-City.mmdb');
        
        if (file_exists($geoDbPath)) {
            try {
                $this->geoReader = new Reader($geoDbPath);
            } catch (\Exception $e) {
                Log::warning('GeoIP2 Reader initialization failed: ' . $e->getMessage());
            }
        }
    }
    
    /**
     * Enregistrer les données de tracking
     */
    public function track(array $data)
    {
        try {
            // 1. Parser User Agent
            $deviceInfo = $this->parseUserAgent($data['user_agent'] ?? '');
            
            // 2. Géolocalisation IP
            $geoData = $this->getGeoLocation($data['ip_address'] ?? null);
            
            // 3. Analyser la source de trafic
            $trafficData = $this->analyzeTrafficSource($data['referrer'] ?? null);
            
            // 4. Extraire les paramètres UTM
            $utmParams = $this->extractUtmParams($data['page_url'] ?? '');
            
            // 5. Récupérer les données démographiques (si utilisateur connecté)
            $demographics = $this->getUserDemographics($data['user_id'] ?? null);
            
            // 6. Enregistrer ou mettre à jour dans user_analytics
            $analytics = UserAnalytics::updateOrCreate(
                ['session_id' => $data['session_id']],
                [
                    'user_id' => $data['user_id'],
                    
                    // Données techniques
                    'device_type' => $data['device_type'] ?? $deviceInfo['device_type'],
                    'os' => $deviceInfo['os'],
                    'browser' => $deviceInfo['browser'],
                    'browser_version' => $deviceInfo['browser_version'],
                    'screen_width' => $data['screen_width'] ?? null,
                    'screen_height' => $data['screen_height'] ?? null,
                    
                    // Géolocalisation
                    'ip_address' => $data['ip_address'],
                    'country' => $geoData['country'],
                    'country_code' => $geoData['country_code'],
                    'city' => $geoData['city'],
                    'timezone' => $geoData['timezone'],
                    
                    // Source de trafic
                    'referrer' => $data['referrer'],
                    'traffic_source' => $trafficData['type'],
                    'search_engine' => $trafficData['engine'],
                    'utm_source' => $utmParams['source'],
                    'utm_medium' => $utmParams['medium'],
                    'utm_campaign' => $utmParams['campaign'],
                    
                    // Démographie
                    'age' => $demographics['age'],
                    'gender' => $demographics['gender'],
                    'profession' => $demographics['profession'],
                    
                    // Timestamps
                    'last_visit' => now(),
                ]
            );
            
            // 7. Enregistrer la page vue (si fournie)
            if (isset($data['page_url'])) {
                PageView::create([
                    'session_id' => $data['session_id'],
                    'user_id' => $data['user_id'],
                    'page_url' => $data['page_url'],
                    'page_title' => $data['page_title'] ?? null,
                    'referrer_url' => $data['referrer'],
                    'time_on_page' => $data['time_on_page'] ?? 0,
                    'scroll_depth' => $data['scroll_depth'] ?? 0,
                    'visited_at' => now(),
                ]);
            }
            
            // 8. Mettre à jour ou créer la session
            UserSession::updateOrCreate(
                ['session_id' => $data['session_id']],
                [
                    'user_id' => $data['user_id'],
                    'started_at' => $analytics->first_visit,
                    'pages_visited' => PageView::where('session_id', $data['session_id'])->count(),
                ]
            );
            
            Log::info('✅ Analytics tracked', [
                'session_id' => $data['session_id'],
                'user_id' => $data['user_id'],
                'country' => $geoData['country'],
            ]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('❌ Analytics tracking failed: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Parser User Agent pour extraire device, OS, browser
     */
    private function parseUserAgent($userAgent)
    {
        $agent = new Agent();
        $agent->setUserAgent($userAgent);
        
        // Déterminer le type d'appareil
        $deviceType = 'desktop';
        if ($agent->isTablet()) {
            $deviceType = 'tablet';
        } elseif ($agent->isMobile()) {
            $deviceType = 'mobile';
        }
        
        return [
            'device_type' => $deviceType,
            'os' => $agent->platform() ?: 'Unknown',
            'browser' => $agent->browser() ?: 'Unknown',
            'browser_version' => $agent->version($agent->browser()) ?: 'Unknown',
        ];
    }
    
    /**
     * Obtenir la géolocalisation depuis l'IP
     */
    private function getGeoLocation($ip)
    {
        // Si pas d'IP ou IP locale, retourner null
        if (!$ip || $this->isLocalIp($ip)) {
            return [
                'country' => 'Local',
                'country_code' => 'LO',
                'city' => 'Localhost',
                'timezone' => 'UTC',
            ];
        }
        
        // Si MaxMind est disponible
        if ($this->geoReader) {
            try {
                $record = $this->geoReader->city($ip);
                
                return [
                    'country' => $record->country->name ?? null,
                    'country_code' => $record->country->isoCode ?? null,
                    'city' => $record->city->name ?? null,
                    'timezone' => $record->location->timeZone ?? null,
                ];
            } catch (\Exception $e) {
                Log::warning('GeoIP2 lookup failed: ' . $e->getMessage());
            }
        }
        
        // Fallback : API gratuite ip-api.com (si MaxMind indisponible)
        try {
            $response = @file_get_contents("http://ip-api.com/json/{$ip}?fields=status,country,countryCode,city,timezone");
            
            if ($response) {
                $data = json_decode($response, true);
                
                if ($data && $data['status'] === 'success') {
                    return [
                        'country' => $data['country'] ?? null,
                        'country_code' => $data['countryCode'] ?? null,
                        'city' => $data['city'] ?? null,
                        'timezone' => $data['timezone'] ?? null,
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning('IP-API lookup failed: ' . $e->getMessage());
        }
        
        return [
            'country' => null,
            'country_code' => null,
            'city' => null,
            'timezone' => null,
        ];
    }
    
    /**
     * Vérifier si l'IP est locale
     */
    private function isLocalIp($ip)
    {
        $localIps = ['127.0.0.1', '::1', 'localhost'];
        return in_array($ip, $localIps) || strpos($ip, '192.168.') === 0 || strpos($ip, '10.') === 0;
    }
    
    /**
     * Analyser la source de trafic
     */
    private function analyzeTrafficSource($referrer)
    {
        if (empty($referrer)) {
            return ['type' => 'direct', 'engine' => null];
        }
        
        // Détecter les moteurs de recherche
        $searchEngines = [
            'google.com' => 'google',
            'google.fr' => 'google',
            'bing.com' => 'bing',
            'yahoo.com' => 'yahoo',
            'duckduckgo.com' => 'duckduckgo',
            'yandex.com' => 'yandex',
            'baidu.com' => 'baidu',
        ];
        
        foreach ($searchEngines as $domain => $engine) {
            if (strpos($referrer, $domain) !== false) {
                return ['type' => 'organic', 'engine' => $engine];
            }
        }
        
        // Détecter les réseaux sociaux
        $socialNetworks = [
            'facebook.com' => 'facebook',
            'twitter.com' => 'twitter',
            'x.com' => 'twitter',
            'linkedin.com' => 'linkedin',
            'instagram.com' => 'instagram',
            'tiktok.com' => 'tiktok',
            'youtube.com' => 'youtube',
        ];
        
        foreach ($socialNetworks as $domain => $social) {
            if (strpos($referrer, $domain) !== false) {
                return ['type' => 'social', 'engine' => $social];
            }
        }
        
        // Sinon, c'est un referral
        return ['type' => 'referral', 'engine' => parse_url($referrer, PHP_URL_HOST)];
    }
    
    /**
     * Extraire les paramètres UTM de l'URL
     */
    private function extractUtmParams($url)
    {
        $params = [];
        parse_str(parse_url($url, PHP_URL_QUERY), $params);
        
        return [
            'source' => $params['utm_source'] ?? null,
            'medium' => $params['utm_medium'] ?? null,
            'campaign' => $params['utm_campaign'] ?? null,
        ];
    }
    
    /**
     * Récupérer les données démographiques de l'utilisateur
     */
    private function getUserDemographics($userId)
    {
        if (!$userId) {
            return ['age' => null, 'gender' => null, 'profession' => null];
        }
        
        try {
            $user = \Modules\LMS\Models\User::with('userable')->find($userId);
            
            if ($user && $user->userable) {
                return [
                    'age' => $user->userable->age ?? null,
                    'gender' => $user->userable->gender ?? null,
                    'profession' => $user->userable->profession ?? null,
                ];
            }
        } catch (\Exception $e) {
            Log::warning('Failed to get user demographics: ' . $e->getMessage());
        }
        
        return ['age' => null, 'gender' => null, 'profession' => null];
    }
    
    /**
     * Enregistrer une conversion
     */
    public function trackConversion($sessionId, $type)
    {
        UserSession::where('session_id', $sessionId)->update([
            'converted' => true,
            'conversion_type' => $type,
        ]);
        
        Log::info('🎯 Conversion tracked', [
            'session_id' => $sessionId,
            'type' => $type,
        ]);
    }
}

