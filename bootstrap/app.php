<?php

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',

    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'check.session.token' => \Modules\LMS\Http\Middleware\CheckSessionToken::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule) {
        // ✅ Queue Worker (existant)
        $schedule->command('queue:work --sleep=3 --tries=3')
            ->everyMinute()
            ->withoutOverlapping();
        
        // ✅ Mise à jour mensuelle de la base GeoIP MaxMind
        // Exécuté le 1er de chaque mois à 3h du matin
        $schedule->command('geoip:update')
            ->monthlyOn(1, '03:00')
            ->onSuccess(function () {
                \Log::info('✅ [Scheduler] Base GeoIP mise à jour avec succès');
            })
            ->onFailure(function () {
                \Log::error('❌ [Scheduler] Échec de la mise à jour GeoIP');
            });
        
        // ✅ Nettoyage des anciennes données analytics (RGPD - 12 mois)
        // Exécuté le 1er de chaque mois à 4h du matin
        $schedule->call(function () {
            $cutoffDate = now()->subMonths(12);
            
            $deletedAnalytics = \DB::table('user_analytics')->where('first_visit', '<', $cutoffDate)->delete();
            $deletedPageViews = \DB::table('page_views')->where('visited_at', '<', $cutoffDate)->delete();
            $deletedSessions = \DB::table('user_sessions')->where('started_at', '<', $cutoffDate)->delete();
            
            \Log::info('🧹 [Scheduler] Nettoyage analytics terminé', [
                'user_analytics_deleted' => $deletedAnalytics,
                'page_views_deleted' => $deletedPageViews,
                'user_sessions_deleted' => $deletedSessions,
            ]);
        })
            ->monthlyOn(1, '04:00')
            ->name('cleanup-old-analytics');
    })
    ->withExceptions(function (Exceptions $exceptions) {})->create();
