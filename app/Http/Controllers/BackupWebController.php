<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\BackupSetting;
use App\Models\BackupSnapshot;
use App\Models\User;
use App\Services\SystemBackupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class BackupWebController extends Controller
{
    public function __construct(
        private SystemBackupService $backupService
    ) {}

    public function index(Request $request): View
    {
        $this->authorizeBackupAccess($request->user(), 'backups.view');

        $settings = BackupSetting::current();
        $stats = $this->backupService->getVaultStats();
        $snapshots = BackupSnapshot::with('creator')
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(15);

        return view('system.backups.index', compact('settings', 'stats', 'snapshots'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeBackupAccess($request->user(), 'backups.create');

        $data = $request->validate([
            'scope' => 'required|in:db_only,full',
            'pin' => 'nullable|boolean',
            'notes' => 'nullable|string|max:500',
            'passphrase' => 'nullable|string|min:8|max:128',
        ], [
            'passphrase.min' => 'كلمة مرور التشفير الخاصة يجب ألا تقل عن 8 أحرف.',
        ]);

        try {
            $snapshot = $this->backupService->createBackup(
                scope: $data['scope'],
                triggerType: 'manual',
                userId: $request->user()?->id,
                pin: (bool) ($data['pin'] ?? false),
                notes: $data['notes'] ?? null,
                passphrase: !empty($data['passphrase']) ? $data['passphrase'] : null
            );

            return redirect()->route('backups.index')
                ->with('success', "تم إنشاء النسخة الاحتياطية ({$snapshot->snapshot_code}) بنجاح بحجم {$snapshot->formatted_size}.");
        } catch (\Throwable $e) {
            return redirect()->route('backups.index')
                ->with('error', 'فشل إنشاء النسخة الاحتياطية: ' . $e->getMessage());
        }
    }

    public function togglePin(Request $request, BackupSnapshot $snapshot): RedirectResponse
    {
        $this->authorizeBackupAccess($request->user(), 'backups.create');

        $snapshot->update(['is_pinned' => !$snapshot->is_pinned]);

        $msg = $snapshot->is_pinned
            ? "تم تثبيت النسخة ({$snapshot->snapshot_code}) للاحتفاظ الدائم بها وحمايتها من الحذف التلقائي."
            : "تم إلغاء تثبيت النسخة ({$snapshot->snapshot_code}).";

        return redirect()->route('backups.index')->with('success', $msg);
    }

    public function verify(Request $request, BackupSnapshot $snapshot): RedirectResponse
    {
        $this->authorizeBackupAccess($request->user(), 'backups.view');

        $passphrase = $request->input('passphrase');

        try {
            $result = $this->backupService->verifySnapshot(
                $snapshot,
                !empty($passphrase) ? (string) $passphrase : null
            );

            return redirect()->route('backups.index')
                ->with('success', "تم التحقق التشفيري من سلامة النسخة ({$snapshot->snapshot_code}) بنجاح (عدد الإطارات المفحوصة: {$result['frames_verified']}).");
        } catch (\Throwable $e) {
            return redirect()->route('backups.index')
                ->with('error', 'فشل التحقق من النسخة: ' . $e->getMessage());
        }
    }

    public function download(Request $request, BackupSnapshot $snapshot): StreamedResponse|RedirectResponse
    {
        $this->authorizeBackupAccess($request->user(), 'backups.download');

        $request->validate([
            'admin_password' => 'required|string',
            'download_mode' => 'required|in:encrypted,portable_passphrase,decrypted_tar',
            'export_passphrase' => 'required_if:download_mode,portable_passphrase|nullable|string|min:8|max:128',
        ], [
            'admin_password.required' => 'يجب إدخال كلمة مرور حسابك لتأكيد تنزيل النسخة الاحتياطية.',
            'export_passphrase.required_if' => 'يرجى إدخال كلمة مرور استرداد (8 أحرف فأكثر) لحماية النسخة المحمولة.',
            'export_passphrase.min' => 'كلمة مرور الاسترداد يجب ألا تقل عن 8 أحرف.',
        ]);

        $this->confirmUserPassword($request->user(), (string) $request->input('admin_password'));

        try {
            return $this->backupService->streamDownload(
                $snapshot,
                (string) $request->input('download_mode'),
                !empty($request->input('export_passphrase')) ? (string) $request->input('export_passphrase') : null
            );
        } catch (\Throwable $e) {
            return redirect()->route('backups.index')
                ->with('error', 'تعذر تنزيل النسخة الاحتياطية: ' . $e->getMessage());
        }
    }

    public function upload(Request $request): RedirectResponse
    {
        $this->authorizeBackupAccess($request->user(), 'backups.restore');

        $request->validate([
            'backup_file' => 'required|file|max:1048576',
            'admin_password' => 'required|string',
            'passphrase' => 'nullable|string',
            'notes' => 'nullable|string|max:500',
        ], [
            'backup_file.required' => 'يرجى اختيار ملف النسخة الاحتياطية.',
            'admin_password.required' => 'يرجى إدخال كلمة مرور حسابك للتأكيد.',
        ]);

        $this->confirmUserPassword($request->user(), (string) $request->input('admin_password'));

        try {
            $snapshot = $this->backupService->storeUploadedBackup(
                $request->file('backup_file'),
                $request->user()?->id,
                $request->input('notes'),
                !empty($request->input('passphrase')) ? (string) $request->input('passphrase') : null
            );

            return redirect()->route('backups.index')
                ->with('success', "تم استيراد والتحقق من النسخة الاحتياطية ({$snapshot->snapshot_code}) وتثبيتها في الخزنة.");
        } catch (\Throwable $e) {
            return redirect()->route('backups.index')
                ->with('error', 'فشل رفع النسخة الاحتياطية: ' . $e->getMessage());
        }
    }

    public function restore(Request $request, BackupSnapshot $snapshot): RedirectResponse
    {
        $this->authorizeBackupAccess($request->user(), 'backups.restore');

        $request->validate([
            'admin_password' => 'required|string',
            'confirm_restore' => 'accepted',
            'passphrase' => 'nullable|string',
        ], [
            'admin_password.required' => 'يجب إدخال كلمة مرور حسابك لتأكيد الاستعادة.',
            'confirm_restore.accepted' => 'يجب الموافقة على إقرار استبدال البيانات الحالية قبل الاستعادة.',
        ]);

        $this->confirmUserPassword($request->user(), (string) $request->input('admin_password'));

        try {
            $result = $this->backupService->restoreSnapshot(
                $snapshot,
                $request->user()?->id,
                !empty($request->input('passphrase')) ? (string) $request->input('passphrase') : null
            );

            return redirect()->route('backups.index')
                ->with('success', "تمت استعادة النسخة ({$result['restored_code']}) بنجاح. تم حفظ نسخة أمان تلقائية قبل الاستعادة برقم ({$result['safety_snapshot_code']}).");
        } catch (\Throwable $e) {
            return redirect()->route('backups.index')
                ->with('error', 'فشل تنفيذ الاستعادة: ' . $e->getMessage());
        }
    }

    public function destroy(Request $request, BackupSnapshot $snapshot): RedirectResponse
    {
        $this->authorizeBackupAccess($request->user(), 'backups.delete');

        $request->validate([
            'admin_password' => 'required|string',
        ], [
            'admin_password.required' => 'يجب إدخال كلمة مرور حسابك لتأكيد الحذف.',
        ]);

        $this->confirmUserPassword($request->user(), (string) $request->input('admin_password'));

        try {
            $code = $snapshot->snapshot_code;
            $this->backupService->deleteSnapshot($snapshot);

            return redirect()->route('backups.index')
                ->with('success', "تم حذف النسخة الاحتياطية ({$code}) بنجاح.");
        } catch (\Throwable $e) {
            return redirect()->route('backups.index')
                ->with('error', $e->getMessage());
        }
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $this->authorizeBackupAccess($request->user(), 'backups.create');

        $data = $request->validate([
            'auto_enabled' => 'nullable|boolean',
            'frequency' => 'required|in:daily,weekly',
            'schedule_time' => ['required', 'regex:/^\d{2}:\d{2}$/'],
            'default_scope' => 'required|in:db_only,full',
            'keep_daily_count' => 'required|integer|min:1|max:90',
            'keep_weekly_count' => 'required|integer|min:0|max:52',
            'keep_monthly_count' => 'required|integer|min:0|max:36',
            'max_storage_mb' => 'required|integer|min:256|max:512000',
            'encrypt_backups' => 'nullable|boolean',
        ]);

        $settings = BackupSetting::current();
        $settings->update([
            'auto_enabled' => $request->boolean('auto_enabled'),
            'frequency' => $data['frequency'],
            'schedule_time' => $data['schedule_time'],
            'default_scope' => $data['default_scope'],
            'keep_daily_count' => (int) $data['keep_daily_count'],
            'keep_weekly_count' => (int) $data['keep_weekly_count'],
            'keep_monthly_count' => (int) $data['keep_monthly_count'],
            'max_storage_mb' => (int) $data['max_storage_mb'],
            'encrypt_backups' => $request->boolean('encrypt_backups', true),
            'updated_by' => $request->user()?->id,
        ]);

        return redirect()->route('backups.index')
            ->with('success', 'تم تحديث سياسة الجدولة والاحتفاظ بالنسخ الاحتياطية بنجاح.');
    }

    public function prune(Request $request): RedirectResponse
    {
        $this->authorizeBackupAccess($request->user(), 'backups.delete');

        $count = $this->backupService->pruneSnapshots();

        return redirect()->route('backups.index')
            ->with('success', "تم تنفيذ التدوير التلقائي للنسخ وحذف ({$count}) نسخة منتهية الصلاحية مع الإبقاء على جميع النسخ المثبتة.");
    }

    private function authorizeBackupAccess(?User $user, string $permission): void
    {
        abort_unless($user instanceof User, 401);

        // Explicit SuperAdmin check (avoids empty 'manager' escalation) or explicit permission
        if ($user->hasRole(['admin', 'super_admin', 'مدير المؤسسة بالكامل'])) {
            return;
        }

        abort_unless($user->hasPermission($permission), 403, 'ليس لديك صلاحية للوصول إلى وحدة النسخ الاحتياطي.');
    }

    private function confirmUserPassword(?User $user, string $password): void
    {
        if (!$user || !Hash::check($password, (string) $user->password)) {
            abort(redirect()->route('backups.index')->with('error', 'كلمة مرور التأكيد غير صحيحة.'));
        }
    }
}

