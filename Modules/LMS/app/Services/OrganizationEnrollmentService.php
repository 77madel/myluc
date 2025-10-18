<?php

namespace Modules\LMS\Services;

use Modules\LMS\Models\Auth\Organization;
use Modules\LMS\Models\Auth\OrganizationEnrollmentLink;
use Illuminate\Support\Str;

class OrganizationEnrollmentService
{
    /**
     * Créer un lien d'inscription pour une organisation
     */
    public function createEnrollmentLink(Organization $organization, string $name, array $options = [])
    {
        $slug = $this->generateUniqueSlug();
        
        $enrollmentLink = OrganizationEnrollmentLink::create([
            'organization_id' => $organization->id,
            'name' => $name,
            'slug' => $slug,
            'description' => $options['description'] ?? null,
            'valid_until' => $options['valid_until'] ?? null,
            'max_enrollments' => $options['max_enrollments'] ?? null,
            'course_id' => $options['course_id'] ?? null,
            'status' => 'active',
            'current_enrollments' => 0,
        ]);

        return $enrollmentLink;
    }

    /**
     * Générer un slug unique pour le lien d'inscription
     */
    private function generateUniqueSlug(): string
    {
        do {
            $slug = Str::random(8);
        } while (OrganizationEnrollmentLink::where('slug', $slug)->exists());

        return $slug;
    }

    /**
     * Valider un lien d'inscription
     */
    public function validateEnrollmentLink(string $slug): ?OrganizationEnrollmentLink
    {
        $link = OrganizationEnrollmentLink::where('slug', $slug)
            ->where('status', 'active')
            ->first();

        if (!$link) {
            return null;
        }

        // Vérifier si le lien a expiré
        if ($link->valid_until && $link->valid_until < now()) {
            return null;
        }

        // Vérifier si le nombre maximum d'inscriptions est atteint
        if ($link->max_enrollments && $link->current_enrollments >= $link->max_enrollments) {
            return null;
        }

        return $link;
    }

    /**
     * Incrémenter le nombre d'inscriptions pour un lien
     */
    public function incrementEnrollments(OrganizationEnrollmentLink $link): void
    {
        $link->increment('current_enrollments');
    }

    /**
     * Décrémenter le nombre d'inscriptions pour un lien
     */
    public function decrementEnrollments(OrganizationEnrollmentLink $link): void
    {
        if ($link->current_enrollments > 0) {
            $link->decrement('current_enrollments');
        }
    }
}
