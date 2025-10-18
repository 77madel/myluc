<x-dashboard-layout>
    <x-slot:title>{{ translate('Progression de l\'Étudiant') }}: {{ $student->userable->first_name ?? '' }} {{ $student->userable->last_name ?? '' }}</x-slot:title>
    <div class="card p-6">
        <h3 class="text-xl font-semibold mb-4">{{ translate('Détails de la Progression') }}</h3>

        <div class="mb-6">
            <p><strong>{{ translate('Nom') }}:</strong> {{ $student->userable->first_name ?? '' }} {{ $student->userable->last_name ?? '' }}</p>
            <p><strong>{{ translate('Email') }}:</strong> {{ $student->email }}</p>
            <p><strong>{{ translate('Département') }}:</strong> N/A</p>
            <p><strong>{{ translate('Statut') }}:</strong> Actif</p>
        </div>

        <h4 class="text-lg font-semibold mb-3">{{ translate('Progression par Cours') }}</h4>
        @forelse($progress as $item)
            <div class="mb-4 p-4 border rounded-md">
                <p><strong>{{ translate('Cours') }}:</strong> {{ $item->course->title ?? 'N/A' }}</p>
                <p><strong>{{ translate('Statut') }}:</strong> {{ ucfirst($item->status) }}</p>
                <p><strong>{{ translate('Pourcentage d\'achèvement') }}:</strong> {{ $item->completion_percentage }}%</p>
                <p><strong>{{ translate('Score') }}:</strong> {{ $item->score ?? 'N/A' }}</p>
                <p><strong>{{ translate('Commencé le') }}:</strong> {{ $item->started_at ? $item->started_at->format('Y-m-d H:i') : 'N/A' }}</p>
                <p><strong>{{ translate('Terminé le') }}:</strong> {{ $item->completed_at ? $item->completed_at->format('Y-m-d H:i') : 'N/A' }}</p>
            </div>
        @empty
            <p>{{ translate('Aucune progression enregistrée pour cet étudiant.') }}</p>
        @endforelse
    </div>
</x-dashboard-layout>
