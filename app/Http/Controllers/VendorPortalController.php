<?php

namespace App\Http\Controllers;

use App\Enums\AttachmentCategory;
use App\Enums\EquipmentCondition;
use App\Enums\ExecutorType;
use App\Enums\MaintenanceStatus;
use App\Models\EquipmentMaintenanceHistory;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class VendorPortalController extends Controller
{
    public function loginForm(): View
    {
        return view('vendor.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $phone = $this->normalizePhone($request->input('phone'));

        if ($phone === '') {
            return back()->withErrors([
                'phone' => 'Nomor telepon wajib diisi.',
            ])->withInput();
        }

        $vendor = Vendor::query()
            ->where('is_active', true)
            ->get()
            ->first(function (Vendor $vendor) use ($phone) {
                return $this->normalizePhone($vendor->phone) === $phone;
            });

        if (! $vendor) {
            return back()->withErrors([
                'phone' => 'Nomor telepon vendor tidak ditemukan atau tidak aktif.',
            ])->withInput();
        }

        session([
            'vendor_id' => $vendor->id,
            'vendor_name' => $vendor->name,
        ]);

        return redirect()->route('vendor.dashboard');
    }

    public function dashboard(): View|RedirectResponse
    {
        $vendorId = session('vendor_id');

        if (! $vendorId) {
            return redirect()->route('vendor.login');
        }

        $vendor = Vendor::query()->findOrFail($vendorId);

        $histories = EquipmentMaintenanceHistory::query()
            ->where('vendor_id', $vendorId)
            ->where('executor_type', ExecutorType::VENDOR->value)
            ->orderByDesc('reported_at')
            ->get();

        return view('vendor.dashboard', compact('vendor', 'histories'));
    }

    public function show(EquipmentMaintenanceHistory $history): View|RedirectResponse
    {
        $vendorId = session('vendor_id');

        if (! $vendorId) {
            return redirect()->route('vendor.login');
        }

        if ((int) $history->vendor_id !== (int) $vendorId) {
            abort(403);
        }

        $conditions = EquipmentCondition::options();
        $statuses = MaintenanceStatus::options();

        return view('vendor.show', compact('history', 'conditions', 'statuses'));
    }

    public function updateReport(Request $request, EquipmentMaintenanceHistory $history): RedirectResponse
    {
        $vendorId = session('vendor_id');

        if (! $vendorId) {
            return redirect()->route('vendor.login');
        }

        if ((int) $history->vendor_id !== (int) $vendorId) {
            abort(403);
        }

        $status = $request->input('status');
        $allowedStatuses = array_keys(MaintenanceStatus::options());

        if (! in_array($status, $allowedStatuses, true)) {
            return back()->withErrors([
                'status' => 'Status pekerjaan tidak valid.',
            ]);
        }

        $startedAt = $history->started_at;
        $completedAt = $history->completed_at;

        if ($status === MaintenanceStatus::IN_PROGRESS->value || $status === MaintenanceStatus::COMPLETED->value) {
            $startedAt ??= now();
        }

        if ($status === MaintenanceStatus::COMPLETED->value) {
            $completedAt = now();
        }

        $history->fill([
            'status' => $status,
            'action_taken' => $request->input('action_taken'),
            'condition_after' => $request->input('condition_after') ?: null,
            'started_at' => $startedAt,
            'completed_at' => $completedAt,
        ]);

        $history->save();

        $this->storeReportAttachments($history, $request);

        return redirect()->route('vendor.history.show', $history)->with('success', 'Laporan pekerjaan vendor berhasil disimpan.');
    }

    protected function storeReportAttachments(EquipmentMaintenanceHistory $history, Request $request): void
    {
        $files = $request->file('attachments', []);

        if (! is_array($files)) {
            return;
        }

        foreach ($files as $index => $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $extension = $file->getClientOriginalExtension() ?: $file->extension();
            $fileName = sprintf(
                '%s-%s-%s.%s',
                $history->history_number ?: 'maintenance',
                $index + 1,
                now()->timestamp,
                $extension,
            );

            $path = $file->storeAs('maintenance-attachments', $fileName, 'public');

            $history->attachments()->create([
                'category' => $this->resolveAttachmentCategory($file),
                'original_name' => $file->getClientOriginalName(),
                'file_name' => $fileName,
                'file_path' => $path,
                'disk' => 'public',
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'description' => 'Upload dari portal vendor',
                'uploaded_by' => null,
            ]);
        }
    }

    protected function resolveAttachmentCategory(UploadedFile $file): string
    {
        return str_contains((string) $file->getMimeType(), 'image/')
            ? AttachmentCategory::AFTER_PHOTO->value
            : AttachmentCategory::WORK_REPORT->value;
    }

    public function logout(): RedirectResponse
    {
        session()->forget(['vendor_id', 'vendor_name']);

        return redirect()->route('vendor.login');
    }

    protected function normalizePhone(?string $phone): string
    {
        return preg_replace('/\D+/', '', (string) ($phone ?? ''));
    }
}
