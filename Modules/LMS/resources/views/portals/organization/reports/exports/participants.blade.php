<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport - Participants</title>
    <style>
        @page { margin: 28px 28px; }
        body { font-family: DejaVu Sans, sans-serif; color: #111827; }
        .header { display:flex; align-items:center; justify-content:space-between; margin-bottom: 14px; }
        .branding { display:flex; align-items:center; gap:10px; }
        .brand-title { font-weight:700; font-size:18px; color:#111827; }
        .subtitle { font-size:12px; color:#6B7280; }
        .badge { display:inline-block; padding:2px 8px; border-radius:9999px; background:#EEF2FF; color:#3730A3; font-size:10px; }
        .meta { font-size:11px; color:#6B7280; }
        .card { border:1px solid #E5E7EB; border-radius:10px; padding:12px; }
        table { width:100%; border-collapse: collapse; }
        thead th { background:#F3F4F6; color:#2563EB; text-align:left; padding:10px; font-size:12px; }
        tbody td { padding:10px; border-top:1px solid #E5E7EB; font-size:12px; }
        .progress-bar { width:100px; height:6px; background:#E5E7EB; border-radius:9999px; overflow:hidden; display:inline-block; vertical-align:middle; }
        .progress-fill { height:100%; background:#2563EB; }
        .footer { margin-top: 10px; font-size:10px; color:#9CA3AF; text-align:right; }
        .pill { padding:2px 8px; border-radius:9999px; background:#F3F4F6; color:#374151; font-size:11px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="branding">
            <div class="brand-title">Rapport Participants</div>
            <span class="badge">Organisation</span>
        </div>
        <div class="meta">
            Généré le {{ now()->format('d/m/Y H:i') }}
        </div>
    </div>

    <div class="card" style="margin-bottom:10px">
        <div class="meta">
            Période: {{ request('date_from') ?: '—' }} → {{ request('date_to') ?: '—' }}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Participant</th>
                <th>Email</th>
                <th>Cours</th>
                <th>Leçons terminées</th>
                <th>Total leçons</th>
                <th>Progression</th>
                <th>Temps total</th>
            </tr>
        </thead>
        <tbody>
            @foreach(($rows ?? []) as $r)
                @php $student = $r['student']; $userInfo = $student->userable ?? null; @endphp
                <tr>
                    <td>{{ trim(($userInfo->first_name ?? '') . ' ' . ($userInfo->last_name ?? '')) ?: $student->email }}</td>
                    <td>{{ $student->email }}</td>
                    <td><span class="pill">{{ $r['courses_count'] }}</span></td>
                    <td>{{ $r['completed_topics'] }}</td>
                    <td>{{ $r['total_topics'] }}</td>
                    <td>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $r['avg_progress'] }}%"></div>
                        </div>
                        <span style="margin-left:6px; font-size:11px;">{{ $r['avg_progress'] }}%</span>
                    </td>
                    <td>{{ \App\Helpers\TimeHelper::formatTimeSpent($r['time_spent'] ?? 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">© {{ date('Y') }} Rapport généré automatiquement</div>
</body>
</html>


