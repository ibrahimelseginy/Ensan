<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\BackupSetting;
use App\Models\BackupSnapshot;
use App\Services\SystemBackupService;
use Illuminate\Console\Command;

class SystemBackupCommand extends Command
{
    protected $signature = 'backup:manage
        {action=run : Action to perform: run, verify, prune, restore}
        {--scope= : Scope for run: db_only or full}
        {--code= : Snapshot code for verify or restore}
        {--scheduled : Mark run as scheduled automatic backup}
        {--pin : Pin snapshot to protect from auto-pruning}
        {--notes= : Optional notes}
        {--passphrase= : Optional custom encryption/decryption passphrase}
        {--yes : Confirm destructive restore without interactive prompt}';

    protected $description = 'إدارة النسخ الاحتياطي المشفر والتدوير والتحقق والاستعادة لمنظومة إنسان';

    public function handle(SystemBackupService $service): int
    {
        $action = (string) $this->argument('action');

        return match ($action) {
            'run' => $this->handleRun($service),
            'verify' => $this->handleVerify($service),
            'prune' => $this->handlePrune($service),
            'restore' => $this->handleRestore($service),
            default => $this->invalidAction($action),
        };
    }

    private function handleRun(SystemBackupService $service): int
    {
        $settings = BackupSetting::current();
        $isScheduled = (bool) $this->option('scheduled');

        if ($isScheduled && !$settings->auto_enabled) {
            $this->info('Scheduled backups are currently disabled.');
            return self::SUCCESS;
        }

        $scope = (string) ($this->option('scope') ?: $settings->default_scope ?: 'db_only');
        $trigger = $isScheduled ? 'scheduled' : 'manual';

        $this->info("Starting {$scope} backup ({$trigger})...");
        $snapshot = $service->createBackup(
            scope: $scope,
            triggerType: $trigger,
            userId: null,
            pin: (bool) $this->option('pin'),
            notes: $this->option('notes') ? (string) $this->option('notes') : null,
            passphrase: $this->option('passphrase') ? (string) $this->option('passphrase') : null
        );

        $this->info("Backup completed: {$snapshot->snapshot_code} ({$snapshot->formatted_size})");
        return self::SUCCESS;
    }

    private function handleVerify(SystemBackupService $service): int
    {
        $code = $this->option('code');
        $snapshot = $code
            ? BackupSnapshot::where('snapshot_code', $code)->firstOrFail()
            : BackupSnapshot::where('status', 'completed')->latest('id')->firstOrFail();

        $res = $service->verifySnapshot($snapshot, $this->option('passphrase') ? (string) $this->option('passphrase') : null);
        $this->info("Verified {$snapshot->snapshot_code}: SHA-256={$res['sha256']} | Frames={$res['frames_verified']}");
        return self::SUCCESS;
    }

    private function handlePrune(SystemBackupService $service): int
    {
        $count = $service->pruneSnapshots();
        $this->info("Pruned {$count} expired unpinned snapshots.");
        return self::SUCCESS;
    }

    private function handleRestore(SystemBackupService $service): int
    {
        $code = (string) $this->option('code');
        if ($code === '') {
            $this->error('--code is required for restore.');
            return self::FAILURE;
        }

        $snapshot = BackupSnapshot::where('snapshot_code', $code)->firstOrFail();
        if (!$this->option('yes') && !$this->confirm("Restore snapshot {$snapshot->snapshot_code} ({$snapshot->scope})? This overwrites current data after taking a safety backup.")) {
            $this->warn('Restore cancelled.');
            return self::SUCCESS;
        }

        $res = $service->restoreSnapshot($snapshot, null, $this->option('passphrase') ? (string) $this->option('passphrase') : null);
        $this->info("Restored {$res['restored_code']}. Safety backup: {$res['safety_snapshot_code']}");
        return self::SUCCESS;
    }

    private function invalidAction(string $action): int
    {
        $this->error("Unknown action '{$action}'. Use run, verify, prune, or restore.");
        return self::FAILURE;
    }
}
