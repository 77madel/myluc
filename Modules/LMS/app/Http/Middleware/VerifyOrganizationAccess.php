<?php

namespace Modules\LMS\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerifyOrganizationAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier que l'utilisateur est authentifié
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        // Vérifier que l'utilisateur a le rôle Organization
        if (!$user->hasRole('Organization')) {
            abort(403, 'Accès refusé. Seules les organisations peuvent accéder à cette section.');
        }

        // Vérifier que l'utilisateur a une organisation associée
        if (!$user->organization) {
            abort(403, 'Aucune organisation associée à votre compte.');
        }

        // Vérifier que l'organisation est active
        $organization = $user->organization;
        if (!$organization || ($organization->status !== 'active' && $organization->status != 1)) {
            abort(403, 'Votre organisation n\'est pas active.');
        }

        // Ajouter l'organisation à la requête pour faciliter l'accès
        $request->merge(['current_organization' => $organization]);

        return $next($request);
    }
}
