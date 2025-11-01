<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport - Cours</title>
    <style>
        @page { margin: 28px 28px; }
        body { font-family: DejaVu Sans, sans-serif; color: #111827; }
        .header { display:flex; align-items:center; justify-content:space-between; margin-bottom: 14px; }
        .brand-title { font-weight:700; font-size:18px; color:#111827; }
        .meta { font-size:11px; color:#6B7280; }
        table { width:100%; border-collapse: collapse; }
        thead th { background:#F3F4F6; color:#2563EB; text-align:left; padding:10px; font-size:12px; }
        tbody td { padding:10px; border-top:1px solid #E5E7EB; font-size:12px; }
        .progress-bar { width:100px; height:6px; background:#E5E7EB; border-radius:9999px; overflow:hidden; display:inline-block; vertical-align:middle; }
        .progress-fill { height:100%; background:#2563EB; }
        .footer { margin-top: 10px; font-size:10px; color:#9CA3AF; text-align:right; }
    </style>
    </head>
<body>
    <div class="header">
        <div class="brand-title">Rapport Cours</div>
        <div class="meta">Généré le {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <div class="meta" style="margin-bottom:10px">Période: {{ request('date_from') ?: '—' }} → {{ request('date_to') ?: '—' }}</div>

    <table>
        <thead>
            <tr>
                <th>Cours</th>
                <th>Participants actifs</th>
                <th>Progression moyenne</th>
                <th>Temps total (s)</th>
            </tr>
        </thead>
        <tbody>
            @foreach(($rows ?? []) as $r)
                <tr>
                    <td>{{ data_get($r, 'course.title', 'N/A') }}</td>
                    <td>{{ data_get($r, 'participants', 0) }}</td>
                    <td>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ (float) data_get($r, 'progress_avg', 0) }}%"></div>
                        </div>
                        <span style="margin-left:6px; font-size:11px;">{{ (float) data_get($r, 'progress_avg', 0) }}%</span>
                    </td>
                    <td>{{ data_get($r, 'time_spent', 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">© {{ date('Y') }} Rapport généré automatiquement</div>
</body>
</html>


