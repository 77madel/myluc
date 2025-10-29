<?php

namespace Modules\LMS\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SessionCheckController extends Controller
{
    /**
     * Vérifier si la session est toujours valide
     */
    public function check(Request $request)
    {
        // Liste des guards à vérifier
        $guards = [
            'web' => [
                'session_key' => 'session_token_web',
                'redirect' => route('login'),
                'message' => '⚠️ Vous avez été déconnecté car une nouvelle connexion a été détectée sur un autre appareil.'
            ],
            'admin' => [
                'session_key' => 'session_token_admin',
                'redirect' => route('admin.login'),
                'message' => '⚠️ Vous avez été déconnecté car une nouvelle connexion administrateur a été détectée sur un autre appareil.'
            ],
        ];

        foreach ($guards as $guardName => $config) {
            // Vérifier si l'utilisateur est authentifié avec ce guard
            if (Auth::guard($guardName)->check()) {
                $user = Auth::guard($guardName)->user();
                $currentSessionToken = session($config['session_key']);
                
                Log::debug('🔍 [Session Check API] Vérification', [
                    'guard' => $guardName,
                    'user_id' => $user->id,
                    'has_session_token' => !empty($currentSessionToken),
                    'has_db_token' => !empty($user->session_token),
                    'tokens_match' => $currentSessionToken === $user->session_token
                ]);
                
                // Comparer le token de la session avec celui en BDD
                if ($currentSessionToken !== $user->session_token) {
                    // Token différent = connexion sur un autre appareil
                    Log::warning('⚠️ [Session Check API] Session invalide détectée', [
                        'guard' => $guardName,
                        'user_id' => $user->id,
                        'email' => $user->email ?? 'N/A',
                        'reason' => 'Token mismatch - nouvelle connexion détectée'
                    ]);
                    
                    // Déconnecter l'utilisateur
                    Auth::guard($guardName)->logout();
                    
                    // Invalider la session
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    
                    return response()->json([
                        'status' => 'invalid',
                        'message' => $config['message'],
                        'redirect' => $config['redirect']
                    ]);
                }
                
                // Token valide
                return response()->json([
                    'status' => 'valid',
                    'message' => 'Session active'
                ]);
            }
        }

        // Aucun guard authentifié
        return response()->json([
            'status' => 'unauthenticated',
            'message' => 'Non authentifié',
            'redirect' => route('login')
        ]);
    }
}

