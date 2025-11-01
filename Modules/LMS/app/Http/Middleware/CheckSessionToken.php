<?php

namespace Modules\LMS\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Liste des guards à vérifier
        $guards = [
            'web' => [
                'session_key' => 'session_token_web',
                'login_route' => 'login',
                'message' => '⚠️ Vous avez été déconnecté car une nouvelle connexion a été détectée sur un autre appareil.'
            ],
            'admin' => [
                'session_key' => 'session_token_admin',
                'login_route' => 'admin.login',
                'message' => '⚠️ Vous avez été déconnecté car une nouvelle connexion administrateur a été détectée sur un autre appareil.'
            ],
        ];

        foreach ($guards as $guardName => $config) {
            // Vérifier si l'utilisateur est authentifié avec ce guard
            if (Auth::guard($guardName)->check()) {
                $user = Auth::guard($guardName)->user();
                $currentSessionToken = session($config['session_key']);
                
                // Log pour debug
                Log::debug('🔍 [Session Check] Vérification du token', [
                    'guard' => $guardName,
                    'user_id' => $user->id,
                    'has_session_token' => !empty($currentSessionToken),
                    'has_db_token' => !empty($user->session_token),
                    'tokens_match' => $currentSessionToken === $user->session_token
                ]);
                
                // Comparer le token de la session avec celui en BDD
                if ($currentSessionToken !== $user->session_token) {
                    // Token différent = connexion sur un autre appareil
                    Log::warning('⚠️ [Session Unique] Déconnexion détectée', [
                        'guard' => $guardName,
                        'user_id' => $user->id,
                        'email' => $user->email ?? 'N/A',
                        'reason' => 'Token mismatch - nouvelle connexion détectée ailleurs'
                    ]);
                    
                    // Déconnecter l'utilisateur
                    Auth::guard($guardName)->logout();
                    
                    // Invalider la session
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    
                    // Rediriger vers la page de connexion avec un message
                    return redirect()->route($config['login_route'])
                        ->with('warning', $config['message']);
                }
                
                // Token valide - continuer normalement
                break;
            }
        }

        return $next($request);
    }
}

