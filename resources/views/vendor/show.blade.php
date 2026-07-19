<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pekerjaan Vendor</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f5f7fb; margin: 0; padding: 30px; }
        .wrap { max-width: 900px; margin: 0 auto; background: white; border-radius: 12px; padding: 24px; }
        .row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 16px; }
        .field { background: #f8fafc; padding: 12px; border-radius: 8px; }
        .label { font-size: 12px; color: #667085; margin-bottom: 4px; text-transform: uppercase; }
        .value { font-weight: 700; }
        a { color: #2563eb; text-decoration: none; }
    </style>
</head>
<body>
    <div class="wrap">
        <div style="margin-bottom: 18px;">
            <div style="font-size: 24px; font-weight: 700;">Detail Pekerjaan</div>
            <div style="color: #667085; margin-top: 6px;">History Number: {{ $history->history_number }}</div>
        </div>

        <div class="row">
            <div class="field"><div class="label">Work Order</div><div class="value">{{ $history->work_order_number ?? '-' }}</div></div>
            <div class="field"><div class="label">Status</div><div class="value">{{ $history->status->label() ?? $history->status }}</div></div>
            <div class="field"><div class="label">Komponen</div><div class="value">{{ $history->component ?? '-' }}</div></div>
            <div class="field"><div class="label">Reported At</div><div class="value">{{ $history->reported_at?->format('d M Y H:i') ?? '-' }}</div></div>
            <div class="field"><div class="label">Scheduled At</div><div class="value">{{ $history->scheduled_at?->format('d M Y H:i') ?? '-' }}</div></div>
            <div class="field"><div class="label">Started At</div><div class="value">{{ $history->started_at?->format('d M Y H:i') ?? '-' }}</div></div>
        </div>

        <div class="field" style="margin-bottom: 18px;">
            <div class="label">Problem Description</div>
            <div class="value">{{ $history->problem_description ?? '-' }}</div>
        </div>

        <div class="field" style="margin-bottom: 18px;">
            <div class="label">Action Taken</div>
            <div class="value">{{ $history->action_taken ?? '-' }}</div>
        </div>

        @if (session('success'))
            <div style="background:#ecfdf5;color:#027a48;padding:12px;border-radius:8px;margin-bottom:18px;">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('vendor.history.report', $history) }}" enctype="multipart/form-data" style="margin-top: 20px; border-top: 1px solid #e5e7eb; padding-top: 20px;">
            @csrf
            <div style="font-size: 20px; font-weight: 700; margin-bottom: 14px;">Update Laporan Pekerjaan</div>

            <div class="row">
                <div class="field">
                    <label class="label" for="status">Status Pekerjaan</label>
                    <select id="status" name="status" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #d0d5dd;">
                        @foreach ($statuses as $value => $label)
                            <option value="{{ $value }}" {{ old('status', $history->status->value) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label class="label" for="condition_after">Kondisi Setelah</label>
                    <select id="condition_after" name="condition_after" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #d0d5dd;">
                        <option value="">- pilih -</option>
                        @foreach ($conditions as $value => $label)
                            <option value="{{ $value }}" {{ old('condition_after', $history->condition_after?->value) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="field" style="margin-bottom: 16px;">
                <label class="label" for="action_taken">Laporan Hasil Kerja</label>
                <textarea id="action_taken" name="action_taken" rows="5" style="width: 100%; border: 1px solid #d0d5dd; border-radius: 8px; padding: 10px;">{{ old('action_taken', $history->action_taken ?? '') }}</textarea>
            </div>

            <div class="field" style="margin-bottom: 16px;">
                <label class="label" for="attachments">Upload Foto / Dokumen Pengerjaan</label>
                <input id="attachments" type="file" name="attachments[]" multiple accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt" style="width: 100%; padding: 10px; border: 1px solid #d0d5dd; border-radius: 8px; background: white;">
                <div style="font-size: 12px; color: #667085; margin-top: 6px;">Dapat mengupload beberapa foto atau dokumen sekaligus.</div>
            </div>

            @if ($history->attachments->isNotEmpty())
                <div class="field" style="margin-bottom: 16px;">
                    <div class="label">Lampiran saat ini</div>
                    <ul style="margin: 10px 0 0 18px; padding: 0;">
                        @foreach ($history->attachments as $attachment)
                            <li style="margin-bottom: 6px;">
                                <a href="{{ Storage::disk($attachment->disk)->url($attachment->file_path) }}" target="_blank" rel="noopener noreferrer">
                                    {{ $attachment->original_name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <button type="submit" style="background:#2563eb;color:white;border:none;border-radius:8px;padding:12px 16px;cursor:pointer;font-weight:700;">Simpan Laporan</button>
        </form>

        <div style="margin-top: 18px;"><a href="{{ route('vendor.dashboard') }}">← Kembali ke dashboard</a></div>
    </div>
</body>
</html>
