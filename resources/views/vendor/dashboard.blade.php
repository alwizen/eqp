<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Vendor</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f7fb; margin: 0; padding: 30px; }
        .wrap { max-width: 1100px; margin: 0 auto; }
        .header { background: white; border-radius: 12px; padding: 20px; margin-bottom: 18px; display: flex; justify-content: space-between; align-items: center; }
        .pill { background: #e0f2fe; color: #075985; padding: 6px 10px; border-radius: 999px; font-size: 12px; font-weight: 700; }
        .card { background: white; border-radius: 12px; padding: 18px; box-shadow: 0 6px 18px rgba(0,0,0,.05); }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { text-align: left; padding: 10px; border-bottom: 1px solid #e5e7eb; }
        a { color: #2563eb; text-decoration: none; }
        .logout { color: #b42318; font-weight: 700; }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="header">
            <div>
                <div style="font-size: 24px; font-weight: 700;">Halo, {{ $vendor->name }}</div>
                <div style="color: #667085; margin-top: 4px;">Daftar pekerjaan maintenance yang ditugaskan ke vendor ini.</div>
            </div>
            <div>
                <span class="pill">Vendor Portal</span>
                <div style="margin-top: 10px;"><a class="logout" href="{{ route('vendor.logout') }}">Keluar</a></div>
            </div>
        </div>

        <div class="card">
            @if ($histories->isEmpty())
                <p>Tidak ada pekerjaan maintenance yang terhubung dengan vendor ini.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>History Number</th>
                            <th>Work Order</th>
                            <th>Komponen</th>
                            <th>Status</th>
                            <th>Reported At</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($histories as $history)
                            <tr>
                                <td>{{ $history->history_number }}</td>
                                <td>{{ $history->work_order_number ?? '-' }}</td>
                                <td>{{ $history->component ?? '-' }}</td>
                                <td>{{ $history->status->label() ?? $history->status }}</td>
                                <td>{{ $history->reported_at?->format('d M Y H:i') ?? '-' }}</td>
                                <td><a href="{{ route('vendor.history.show', $history) }}">Lihat detail</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</body>
</html>
