<?php

namespace Modules\LMS\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\LMS\Models\Analytics\UserAnalytics;
use Modules\LMS\Models\Analytics\PageView;
use Modules\LMS\Models\Analytics\UserSession;
use Illuminate\Support\Facades\DB;

class AnalyticsDashboardController extends Controller
{
    /**
     * Afficher le dashboard analytics
     */
    public function index(Request $request)
    {
        // Période sélectionnée (par défaut: 30 derniers jours)
        $period = $request->input('period', '30');
        $startDate = now()->subDays($period);
        
        // 1. STATISTIQUES GÉNÉRALES
        $stats = [
            'total_visitors' => UserAnalytics::where('first_visit', '>=', $startDate)->count(),
            'total_users' => UserAnalytics::where('first_visit', '>=', $startDate)->whereNotNull('user_id')->count(),
            'total_sessions' => UserSession::where('started_at', '>=', $startDate)->count(),
            'total_page_views' => PageView::where('visited_at', '>=', $startDate)->count(),
            'avg_session_duration' => UserSession::where('started_at', '>=', $startDate)
                ->whereNotNull('duration')
                ->avg('duration'),
            'avg_pages_per_session' => UserSession::where('started_at', '>=', $startDate)
                ->avg('pages_visited'),
        ];
        
        // 2. APPAREILS
        $devices = UserAnalytics::where('first_visit', '>=', $startDate)
            ->select('device_type', DB::raw('count(*) as count'))
            ->groupBy('device_type')
            ->get();
        
        // 3. NAVIGATEURS
        $browsers = UserAnalytics::where('first_visit', '>=', $startDate)
            ->select('browser', DB::raw('count(*) as count'))
            ->groupBy('browser')
            ->orderByDesc('count')
            ->limit(5)
            ->get();
        
        // 4. SYSTÈMES D'EXPLOITATION
        $os = UserAnalytics::where('first_visit', '>=', $startDate)
            ->select('os', DB::raw('count(*) as count'))
            ->groupBy('os')
            ->orderByDesc('count')
            ->limit(5)
            ->get();
        
        // 5. PAYS
        $countries = UserAnalytics::where('first_visit', '>=', $startDate)
            ->select('country', 'country_code', DB::raw('count(*) as count'))
            ->whereNotNull('country')
            ->groupBy('country', 'country_code')
            ->orderByDesc('count')
            ->limit(10)
            ->get();
        
        // 6. VILLES
        $cities = UserAnalytics::where('first_visit', '>=', $startDate)
            ->select('city', 'country', DB::raw('count(*) as count'))
            ->whereNotNull('city')
            ->groupBy('city', 'country')
            ->orderByDesc('count')
            ->limit(10)
            ->get();
        
        // 7. SOURCES DE TRAFIC
        $trafficSources = UserAnalytics::where('first_visit', '>=', $startDate)
            ->select('traffic_source', DB::raw('count(*) as count'))
            ->whereNotNull('traffic_source')
            ->groupBy('traffic_source')
            ->get();
        
        // 8. MOTEURS DE RECHERCHE
        $searchEngines = UserAnalytics::where('first_visit', '>=', $startDate)
            ->select('search_engine', DB::raw('count(*) as count'))
            ->whereNotNull('search_engine')
            ->groupBy('search_engine')
            ->orderByDesc('count')
            ->get();
        
        // 9. PAGES LES PLUS VISITÉES
        $topPages = PageView::where('visited_at', '>=', $startDate)
            ->select('page_title', 'page_url', DB::raw('count(*) as views'), DB::raw('avg(time_on_page) as avg_time'))
            ->groupBy('page_title', 'page_url')
            ->orderByDesc('views')
            ->limit(10)
            ->get();
        
        // 10. DONNÉES DÉMOGRAPHIQUES
        $demographics = [
            'age_groups' => UserAnalytics::where('first_visit', '>=', $startDate)
                ->whereNotNull('age')
                ->select(
                    DB::raw('CASE 
                        WHEN age < 18 THEN "< 18"
                        WHEN age BETWEEN 18 AND 24 THEN "18-24"
                        WHEN age BETWEEN 25 AND 34 THEN "25-34"
                        WHEN age BETWEEN 35 AND 44 THEN "35-44"
                        WHEN age BETWEEN 45 AND 54 THEN "45-54"
                        ELSE "55+" 
                    END as age_group'),
                    DB::raw('count(*) as count')
                )
                ->groupBy('age_group')
                ->get(),
            
            'genders' => UserAnalytics::where('first_visit', '>=', $startDate)
                ->whereNotNull('gender')
                ->select('gender', DB::raw('count(*) as count'))
                ->groupBy('gender')
                ->get(),
            
            'professions' => UserAnalytics::where('first_visit', '>=', $startDate)
                ->whereNotNull('profession')
                ->select('profession', DB::raw('count(*) as count'))
                ->groupBy('profession')
                ->orderByDesc('count')
                ->limit(10)
                ->get(),
        ];
        
        // 11. ÉVOLUTION TEMPORELLE (7 derniers jours)
        $timeline = UserAnalytics::where('first_visit', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(first_visit) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        // 12. CONVERSIONS
        $conversions = UserSession::where('started_at', '>=', $startDate)
            ->where('converted', true)
            ->select('conversion_type', DB::raw('count(*) as count'))
            ->groupBy('conversion_type')
            ->get();
        
        return view('lms::portals.admin.analytics.index', compact(
            'stats',
            'devices',
            'browsers',
            'os',
            'countries',
            'cities',
            'trafficSources',
            'searchEngines',
            'topPages',
            'demographics',
            'timeline',
            'conversions',
            'period'
        ));
    }
}

