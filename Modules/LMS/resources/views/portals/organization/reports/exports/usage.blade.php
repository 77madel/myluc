<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Rapport - Usage</title>
    <style>
        @page { margin: 28px 28px; }
        body { font-family: DejaVu Sans, sans-serif; color: #111827; }
        .header { display:flex; align-items:center; justify-content:space-between; margin-bottom: 14px; }
        .brand-title { font-weight:700; font-size:18px; color:#111827; }
        .meta { font-size:11px; color:#6B7280; }
        table { width:100%; border-collapse: collapse; }
        thead th { background:#F3F4F6; color:#2563EB; text-align:left; padding:10px; font-size:12px; }
        tbody td { padding:10px; border-top:1px solid #E5E7EB; font-size:12px; }
        .footer { margin-top: 10px; font-size:10px; color:#9CA3AF; text-align:right; }
    </style>
    </head>
<body>
    <div class="header">
        <div class="brand-title">Rapport Usage</div>
        <div class="meta">Généré le {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <div class="meta" style="margin-bottom:10px">Période: {{ request('date_from') ?: '—' }} → {{ request('date_to') ?: '—' }}</div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Temps total (s)</th>
            </tr>
        </thead>
        <tbody>
            @foreach(($series ?? []) as $p)
                <tr>
                    <td>{{ $p->d }}</td>
                    <td>{{ $p->seconds }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">© {{ date('Y') }} Rapport généré automatiquement</div>
</body>
</html>


