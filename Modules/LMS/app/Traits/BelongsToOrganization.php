<?php

namespace Modules\LMS\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

trait BelongsToOrganization
{
    protected static function bootBelongsToOrganization()
    {
        static::addGlobalScope('organization', function (Builder $builder) {
            $user = Auth::user();

            // Les comptes avec guard 'admin' voient tout
            if ($user && ($user->guard === 'admin')) {
                return;
            }

            // Les comptes d'organisation ne voient que leurs enregistrements
            if ($user && ($user->guard === 'organization')) {
                $builder->where('organization_id', $user->organization_id);
            } else {
                // Non authentifié ou sans organisation => aucun résultat
                $builder->whereRaw('0 = 1');
            }
        });
    }
}



