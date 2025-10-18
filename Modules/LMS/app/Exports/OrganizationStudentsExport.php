<?php

namespace Modules\LMS\Exports;

use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Modules\LMS\Models\Auth\Organization;

class OrganizationStudentsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $organization;

    public function __construct(Organization $organization)
    {
        $this->organization = $organization;
    }

    public function query()
    {
        // Utiliser le système unifié avec users + students
        return \Modules\LMS\Models\User::where('organization_id', $this->organization->id)
            ->where('userable_type', 'Modules\LMS\Models\Auth\Student')
            ->with(['userable', 'enrollments.course']);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nom Complet',
            'Email',
            'Téléphone',
            'Département',
            'Statut',
            'Date d\'Inscription',
            'Cours Assignés',
            'Progression Moyenne',
            'Modules Complétés'
        ];
    }

    public function map($user): array
    {
        $userable = $user->userable;
        $enrollments = $user->enrollments;

        $totalCourses = $enrollments->count();
        $completedCourses = $enrollments->where('status', 'completed')->count();
        $averageProgress = $totalCourses > 0 ? round($enrollments->avg('progress') ?? 0, 2) : 0;

        return [
            $user->id,
            $userable->first_name . ' ' . $userable->last_name,
            $user->email,
            $userable->phone ?? 'N/A',
            'N/A', // Department - pas disponible dans le système unifié
            'Actif', // Status - tous les utilisateurs actifs
            $user->created_at->format('d/m/Y H:i'),
            $totalCourses,
            $averageProgress . '%',
            $completedCourses . '/' . $totalCourses
        ];
    }
}
