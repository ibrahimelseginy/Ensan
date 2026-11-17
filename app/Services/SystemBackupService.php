<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\BackupSetting;
use App\Models\BackupSnapshot;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SystemBackupService
{
    private const MAGIC = 'ENSANBK1';
    private const KEY_MODE_SERVER = 1;
    private const KEY_MODE_PASSPHRASE = 2;
    private const FRAME_SIZE = 1048576; // 1 MiB streaming frames
    private const HEADER_BYTES = 61;    // 8 + 1 + 16 + 4 + 32

    public function vaultPath(string $relative = ''): string
    {
        $base = storage_path('app/private/backups');
        if (!is_dir($base)) {
            mkdir($base, 0750, true);
        }

        $htaccess = $base . DIRECTORY_SEPARATOR . '.htaccess';
        if (!file_exists($htaccess)) {
            @file_put_contents($htaccess, "Order Allow,Deny\nDeny from all\n");
        }

        return $relative !== '' ? $base . DIRECTORY_SEPARATOR . ltrim($relative, DIRECTORY_SEPARATOR) : $base;
    }

    public function createBackup(
        string $scope = 'db_only',
        string $triggerType = 'manual',
        ?int $userId = null,
        bool $pin = false,
        ?string $notes = null,
        ?string $passphrase = null
    ): BackupSnapshot {
        if (!in_array($scope, ['full', 'db_only'], true)) {
            throw new \InvalidArgumentException('نوع النسخة الاحتياطية غير صالح.');
        }

        $lock = Cache::lock('ensan_system_backup_lock', 600);
        if (!$lock->get()) {
            throw new \RuntimeException('توجد عملية نسخ احتياطي أخرى قيد التنفيذ حالياً. يرجى الانتظار لحين اكتمالها.');
        }

        @set_time_limit(600);
        @ini_set('memory_limit', '256M');

        $settings = BackupSetting::current();
        $code = 'BKP-' . now()->format('Ymd-His') . '-' . strtoupper(Str::random(4));

        $snapshot = BackupSnapshot::create([
            'snapshot_code' => $code,
            'scope' => $scope,
            'trigger_type' => $triggerType,
            'status' => 'running',
            'format' => $settings->encrypt_backups ? 'ensanbak' : 'tar_gz',
            'is_encrypted' => (bool) $settings->encrypt_backups,
            'is_pinned' => $pin || $triggerType === 'pre_restore',
            'app_version' => app()->version(),
            'notes' => $notes,
            'created_by' => $userId,
        ]);

        $workDir = $this->vaultPath('.tmp_' . $code . '_' . Str::random(6));
        $defaultsCnf = null;

        try {
            mkdir($workDir . '/database', 0750, true);
            mkdir($workDir . '/config', 0750, true);

            // 1. Dump MySQL database consistently
            $dumpFile = $workDir . '/database/dump.sql';
            $defaultsCnf = $this->createMysqlDefaultsFile();
            $tablesCount = $this->dumpDatabase($dumpFile, $defaultsCnf);

            // 2. Count files if full scope
            $filesCount = 0;
            $archiveTargets = [];
            if ($scope === 'full') {
                foreach (['storage/app/private', 'storage/app/public', 'public/uploads'] as $relDir) {
                    $absDir = base_path($relDir);
                    if (is_dir($absDir)) {
                        $archiveTargets[] = $relDir;
                        $filesCount += $this->countFilesInDirectory($absDir, $relDir === 'storage/app/private' ? ['backups'] : []);
                    }
                }
            }

            // 3. Build env vault & manifest
            $envVault = [
                'app_name' => config('app.name'),
                'app_key' => config('app.key'),
                'db_connection' => config('database.default'),
                'db_database' => config('database.connections.mysql.database'),
                'captured_at' => now()->toIso8601String(),
            ];
            file_put_contents($workDir . '/config/env_vault.json', json_encode($envVault, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            $manifest = [
                'snapshot_code' => $code,
                'scope' => $scope,
                'trigger_type' => $triggerType,
                'created_at' => now()->toIso8601String(),
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
                'db_tables_count' => $tablesCount,
                'files_count' => $filesCount,
                'db_dump_sha256' => hash_file('sha256', $dumpFile),
            ];
            file_put_contents($workDir . '/manifest.json', json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            // 4. Pack compressed tar.gz archive (excludes backups directory itself)
            $tarGzPath = $workDir . '/' . $code . '.tar.gz';
            $this->createTarGzArchive($workDir, $tarGzPath, $archiveTargets);

            // 5. Encrypt streaming to final .ensanbak (or store tar.gz if encryption disabled)
            $ext = $settings->encrypt_backups ? 'ensanbak' : 'tar.gz';
            $finalFileName = $code . '_' . $scope . '.' . $ext;
            $finalPath = $this->vaultPath($finalFileName);

            if ($settings->encrypt_backups) {
                $this->encryptFileStreaming($tarGzPath, $finalPath, $code, $passphrase);
                @unlink($tarGzPath);
            } else {
                rename($tarGzPath, $finalPath);
            }

            $fileSize = (int) filesize($finalPath);
            $sha256 = (string) hash_file('sha256', $finalPath);
            [, $macKey] = $this->deriveServerKeys();
            $hmac = hash_hmac('sha256', $sha256 . '|' . $code . '|' . $fileSize, $macKey);

            $snapshot->update([
                'status' => 'completed',
                'format' => $settings->encrypt_backups ? 'ensanbak' : 'tar_gz',
                'file_path' => $finalFileName,
                'file_name' => $finalFileName,
                'file_size_bytes' => $fileSize,
                'sha256_checksum' => $sha256,
                'hmac_signature' => $hmac,
                'db_tables_count' => $tablesCount,
                'files_count' => $filesCount,
                'verified_at' => now(),
            ]);

            $settings->update([
                'last_run_at' => now(),
                'last_status' => 'completed',
            ]);

            // Auto-prune old unpinned snapshots according to retention policy
            if ($triggerType !== 'pre_restore') {
                $this->pruneSnapshots();
            }

            return $snapshot->fresh();
        } catch (\Throwable $e) {
            $snapshot->update([
                'status' => 'failed',
                'error_message' => Str::limit($e->getMessage(), 1500),
            ]);
            $settings->update([
                'last_run_at' => now(),
                'last_status' => 'failed',
            ]);
            Log::error('SystemBackupService::createBackup failed', [
                'code' => $code,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        } finally {
            if ($defaultsCnf && file_exists($defaultsCnf)) {
                @unlink($defaultsCnf);
            }
            $this->removeDirectory($workDir);
            optional($lock)->release();
        }
    }

    public function verifySnapshot(BackupSnapshot $snapshot, ?string $passphrase = null): array
    {
        $absPath = $this->resolveSnapshotPath($snapshot);

        $actualSha = (string) hash_file('sha256', $absPath);
        if (!empty($snapshot->sha256_checksum) && !hash_equals($snapshot->sha256_checksum, $actualSha)) {
            throw new \RuntimeException('فشل التحقق: بصمة SHA-256 للملف لا تطابق السجل المحفوظ (الملف تالف أو معدل).');
        }

        $framesVerified = 0;
        if ($snapshot->is_encrypted || $snapshot->format === 'ensanbak') {
            $tmpCheck = $this->vaultPath('.verify_' . Str::random(10) . '.tar.gz');
            try {
                $framesVerified = $this->decryptFileStreaming($absPath, $tmpCheck, $passphrase);
            } finally {
                if (file_exists($tmpCheck)) {
                    @unlink($tmpCheck);
                }
            }
        }

        $snapshot->update(['verified_at' => now()]);

        return [
            'sha256' => $actualSha,
            'frames_verified' => $framesVerified,
            'verified_at' => $snapshot->verified_at?->format('Y-m-d H:i:s'),
        ];
    }

    public function streamDownload(BackupSnapshot $snapshot, string $mode = 'encrypted', ?string $exportPassphrase = null): StreamedResponse
    {
        $absPath = $this->resolveSnapshotPath($snapshot);

        // Mode 1: Raw encrypted .ensanbak (already stored encrypted on disk)
        if ($mode === 'encrypted' && $exportPassphrase === null) {
            return response()->streamDownload(function () use ($absPath) {
                $fp = fopen($absPath, 'rb');
                if ($fp) {
                    while (!feof($fp)) {
                        echo fread($fp, 1048576);
                        flush();
                    }
                    fclose($fp);
                }
            }, $snapshot->file_name ?: ($snapshot->snapshot_code . '.ensanbak'), [
                'Content-Type' => 'application/octet-stream',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        // Mode 2: Portable encrypted .ensanbak protected with user's custom recovery passphrase
        if ($mode === 'portable_passphrase' && !empty($exportPassphrase)) {
            $tmpTar = $this->vaultPath('.dl_tar_' . Str::random(10) . '.tar.gz');
            $tmpEnc = $this->vaultPath('.dl_enc_' . Str::random(10) . '.ensanbak');

            if ($snapshot->is_encrypted) {
                $this->decryptFileStreaming($absPath, $tmpTar);
            } else {
                copy($absPath, $tmpTar);
            }

            $this->encryptFileStreaming($tmpTar, $tmpEnc, $snapshot->snapshot_code, $exportPassphrase);
            @unlink($tmpTar);

            $downloadName = $snapshot->snapshot_code . '_' . $snapshot->scope . '_portable.ensanbak';
            return response()->streamDownload(function () use ($tmpEnc) {
                try {
                    $fp = fopen($tmpEnc, 'rb');
                    if ($fp) {
                        while (!feof($fp)) {
                            echo fread($fp, 1048576);
                            flush();
                        }
                        fclose($fp);
                    }
                } finally {
                    @unlink($tmpEnc);
                }
            }, $downloadName, [
                'Content-Type' => 'application/octet-stream',
                'X-Content-Type-Options' => 'nosniff',
            ]);
        }

        // Mode 3: Decrypted standard .tar.gz streamed directly without writing plaintext permanently
        $tmpTar = $this->vaultPath('.dl_plain_' . Str::random(10) . '.tar.gz');
        if ($snapshot->is_encrypted) {
            $this->decryptFileStreaming($absPath, $tmpTar);
        } else {
            copy($absPath, $tmpTar);
        }

        $downloadName = $snapshot->snapshot_code . '_' . $snapshot->scope . '.tar.gz';
        return response()->streamDownload(function () use ($tmpTar) {
            try {
                $fp = fopen($tmpTar, 'rb');
                if ($fp) {
                    while (!feof($fp)) {
                        echo fread($fp, 1048576);
                        flush();
                    }
                    fclose($fp);
                }
            } finally {
                @unlink($tmpTar);
            }
        }, $downloadName, [
            'Content-Type' => 'application/gzip',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function storeUploadedBackup(UploadedFile $file, ?int $userId, ?string $notes = null, ?string $passphrase = null): BackupSnapshot
    {
        $origName = $file->getClientOriginalName();
        $lower = strtolower($origName);
        if (!str_ends_with($lower, '.ensanbak') && !str_ends_with($lower, '.tar.gz') && !str_ends_with($lower, '.tgz')) {
            throw new \InvalidArgumentException('الصيغ المسموح برفعها هي .ensanbak أو .tar.gz فقط.');
        }

        $isEnc = str_ends_with($lower, '.ensanbak');
        $code = 'UPL-' . now()->format('Ymd-His') . '-' . strtoupper(Str::random(4));
        $ext = $isEnc ? 'ensanbak' : 'tar.gz';
        $destName = $code . '_uploaded.' . $ext;
        $destPath = $this->vaultPath($destName);

        $file->move($this->vaultPath(), $destName);

        $tmpExtract = $this->vaultPath('.inspect_' . Str::random(8));
        mkdir($tmpExtract, 0750, true);
        $tmpTar = $tmpExtract . '/archive.tar.gz';

        try {
            if ($isEnc) {
                $this->decryptFileStreaming($destPath, $tmpTar, $passphrase);
            } else {
                copy($destPath, $tmpTar);
            }

            $this->extractTarGzArchive($tmpTar, $tmpExtract . '/content');
            $manifestPath = $tmpExtract . '/content/manifest.json';
            $dumpPath = $tmpExtract . '/content/database/dump.sql';

            if (!file_exists($dumpPath) || filesize($dumpPath) < 64) {
                throw new \RuntimeException('الأرشيف المرفوع لا يحتوي على ملف database/dump.sql صالح.');
            }

            $manifest = file_exists($manifestPath) ? (json_decode((string) file_get_contents($manifestPath), true) ?: []) : [];
            $scope = ($manifest['scope'] ?? null) === 'full' ? 'full' : 'db_only';

            $fileSize = (int) filesize($destPath);
            $sha256 = (string) hash_file('sha256', $destPath);
            [, $macKey] = $this->deriveServerKeys();
            $hmac = hash_hmac('sha256', $sha256 . '|' . $code . '|' . $fileSize, $macKey);

            return BackupSnapshot::create([
                'snapshot_code' => $code,
                'scope' => $scope,
                'trigger_type' => 'uploaded',
                'status' => 'completed',
                'format' => $isEnc ? 'ensanbak' : 'tar_gz',
                'file_path' => $destName,
                'file_name' => $destName,
                'file_size_bytes' => $fileSize,
                'sha256_checksum' => $sha256,
                'hmac_signature' => $hmac,
                'is_encrypted' => $isEnc,
                'is_pinned' => true,
                'db_tables_count' => (int) ($manifest['db_tables_count'] ?? 0),
                'files_count' => (int) ($manifest['files_count'] ?? 0),
                'app_version' => $manifest['laravel_version'] ?? app()->version(),
                'notes' => $notes ?: ('نسخة مرفوعة: ' . $origName),
                'created_by' => $userId,
                'verified_at' => now(),
            ]);
        } catch (\Throwable $e) {
            if (file_exists($destPath)) {
                @unlink($destPath);
            }
            throw $e;
        } finally {
            $this->removeDirectory($tmpExtract);
        }
    }

    public function restoreSnapshot(BackupSnapshot $snapshot, ?int $userId, ?string $passphrase = null): array
    {
        $absPath = $this->resolveSnapshotPath($snapshot);

        @set_time_limit(900);
        @ini_set('memory_limit', '256M');

        // 1. Always create a pre-restore safety snapshot first!
        $safetySnapshot = $this->createBackup(
            scope: $snapshot->scope,
            triggerType: 'pre_restore',
            userId: $userId,
            pin: true,
            notes: 'نسخة أمان تلقائية قبل استعادة ' . $snapshot->snapshot_code
        );

        // Preserve current backup ledger rows in memory so restoring older DB doesn't lose vault history
        $preservedSnapshots = BackupSnapshot::all()->map(fn (BackupSnapshot $s) => $s->toArray())->all();
        $preservedSettings = BackupSetting::current()->toArray();

        $tmpDir = $this->vaultPath('.restore_' . $snapshot->snapshot_code . '_' . Str::random(6));
        mkdir($tmpDir, 0750, true);
        $tmpTar = $tmpDir . '/archive.tar.gz';
        $extractDir = $tmpDir . '/extracted';
        mkdir($extractDir, 0750, true);

        $defaultsCnf = null;

        try {
            if ($snapshot->is_encrypted || $snapshot->format === 'ensanbak') {
                $this->decryptFileStreaming($absPath, $tmpTar, $passphrase);
            } else {
                copy($absPath, $tmpTar);
            }

            $this->extractTarGzArchive($tmpTar, $extractDir);
            $dumpFile = $extractDir . '/database/dump.sql';
            if (!file_exists($dumpFile) || filesize($dumpFile) < 64) {
                throw new \RuntimeException('تعذر العثور على ملف database/dump.sql داخل النسخة الاحتياطية.');
            }

            $defaultsCnf = $this->createMysqlDefaultsFile();
            $this->restoreDatabase($dumpFile, $defaultsCnf);

            $filesRestored = false;
            if ($snapshot->scope === 'full') {
                foreach (['storage/app/private', 'storage/app/public', 'public/uploads'] as $relDir) {
                    $src = $extractDir . '/' . $relDir;
                    if (is_dir($src)) {
                        $this->syncDirectorySafe($src, base_path($relDir), $relDir === 'storage/app/private' ? ['backups'] : []);
                        $filesRestored = true;
                    }
                }
            }

            // Re-sync backup ledger rows & settings so newly created safety snapshot stays tracked
            $this->resyncBackupMetadata($preservedSnapshots, $preservedSettings);

            try {
                Artisan::call('view:clear');
                Artisan::call('route:clear');
                Artisan::call('cache:clear');
            } catch (\Throwable) {
            }

            return [
                'restored_code' => $snapshot->snapshot_code,
                'safety_snapshot_code' => $safetySnapshot->snapshot_code,
                'files_restored' => $filesRestored,
            ];
        } finally {
            if ($defaultsCnf && file_exists($defaultsCnf)) {
                @unlink($defaultsCnf);
            }
            $this->removeDirectory($tmpDir);
        }
    }

    public function deleteSnapshot(BackupSnapshot $snapshot): void
    {
        if ($snapshot->is_pinned) {
            throw new \RuntimeException('لا يمكن حذف نسخة مثبتة ومحمية. يرجى إلغاء التثبيت أولاً.');
        }

        if (!empty($snapshot->file_path)) {
            $abs = $this->vaultPath($snapshot->file_path);
            if (file_exists($abs)) {
                @unlink($abs);
            }
        }

        $snapshot->delete();
    }

    public function pruneSnapshots(): int
    {
        $settings = BackupSetting::current();
        $completed = BackupSnapshot::where('status', 'completed')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        if ($completed->count() <= 1) {
            return 0;
        }

        $keepIds = [];
        // Always keep newest completed snapshot + all pinned snapshots
        $keepIds[$completed->first()->id] = true;
        foreach ($completed->where('is_pinned', true) as $pinned) {
            $keepIds[$pinned->id] = true;
        }

        // GFS Daily / Weekly / Monthly selection
        $dailyKept = 0;
        $weeklyBuckets = [];
        $monthlyBuckets = [];

        foreach ($completed as $snap) {
            $dt = $snap->created_at ?? now();
            if ($dailyKept < max(1, $settings->keep_daily_count)) {
                $keepIds[$snap->id] = true;
                $dailyKept++;
            }

            $weekKey = $dt->format('o-W');
            if (!isset($weeklyBuckets[$weekKey]) && count($weeklyBuckets) < max(0, $settings->keep_weekly_count)) {
                $weeklyBuckets[$weekKey] = true;
                $keepIds[$snap->id] = true;
            }

            $monthKey = $dt->format('Y-m');
            if (!isset($monthlyBuckets[$monthKey]) && count($monthlyBuckets) < max(0, $settings->keep_monthly_count)) {
                $monthlyBuckets[$monthKey] = true;
                $keepIds[$snap->id] = true;
            }
        }

        $prunedCount = 0;
        foreach ($completed as $snap) {
            if (!isset($keepIds[$snap->id]) && !$snap->is_pinned) {
                $this->deleteSnapshot($snap);
                $prunedCount++;
            }
        }

        // Enforce max_storage_mb cap on remaining unpinned snapshots (newest never deleted)
        $maxBytes = max(256, (int) $settings->max_storage_mb) * 1024 * 1024;
        $remaining = BackupSnapshot::where('status', 'completed')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        $totalBytes = (int) $remaining->sum('file_size_bytes');
        $newestId = optional($remaining->last())->id;

        foreach ($remaining as $snap) {
            if ($totalBytes <= $maxBytes) {
                break;
            }
            if ($snap->is_pinned || $snap->id === $newestId) {
                continue;
            }
            $size = (int) $snap->file_size_bytes;
            $this->deleteSnapshot($snap);
            $totalBytes -= $size;
            $prunedCount++;
        }

        return $prunedCount;
    }

    public function getVaultStats(): array
    {
        $completed = BackupSnapshot::where('status', 'completed');
        $totalBytes = (int) (clone $completed)->sum('file_size_bytes');
        $count = (int) (clone $completed)->count();
        $pinnedCount = (int) (clone $completed)->where('is_pinned', true)->count();
        $latest = (clone $completed)->orderByDesc('created_at')->orderByDesc('id')->first();

        $diskFree = @disk_free_space($this->vaultPath()) ?: 0;

        return [
            'total_count' => $count,
            'pinned_count' => $pinnedCount,
            'total_size_bytes' => $totalBytes,
            'total_size_human' => $this->formatBytes($totalBytes),
            'disk_free_human' => $this->formatBytes((int) $diskFree),
            'latest_snapshot' => $latest,
        ];
    }

    private function encryptFileStreaming(string $inputPath, string $outputPath, string $snapshotCode, ?string $passphrase = null): void
    {
        $salt = random_bytes(16);
        $keyMode = !empty($passphrase) ? self::KEY_MODE_PASSPHRASE : self::KEY_MODE_SERVER;
        [$encKey, $macKey] = !empty($passphrase)
            ? $this->derivePassphraseKeys($passphrase, $salt)
            : $this->deriveServerKeys($salt);

        $contextHash = hash('sha256', $snapshotCode, true);
        $header = self::MAGIC
            . chr($keyMode)
            . $salt
            . pack('N', self::FRAME_SIZE)
            . $contextHash;

        $in = fopen($inputPath, 'rb');
        $out = fopen($outputPath, 'wb');
        if (!$in || !$out) {
            throw new \RuntimeException('تعذر فتح ملفات التشفير للنسخة الاحتياطية.');
        }

        try {
            fwrite($out, $header);
            $hmacCtx = hash_init('sha256', HASH_HMAC, $macKey);
            hash_update($hmacCtx, $header);

            $frameIdx = 0;
            while (!feof($in)) {
                $chunk = fread($in, self::FRAME_SIZE);
                if ($chunk === '' || $chunk === false) {
                    break;
                }

                $iv = random_bytes(12);
                $tag = '';
                $aad = $header . pack('N', $frameIdx);
                $cipher = openssl_encrypt($chunk, 'aes-256-gcm', $encKey, OPENSSL_RAW_DATA, $iv, $tag, $aad, 16);
                if ($cipher === false || strlen($tag) !== 16) {
                    throw new \RuntimeException('فشل تشفير أحد أجزاء النسخة الاحتياطية.');
                }

                $frameBin = pack('N', strlen($cipher)) . $iv . $tag . $cipher;
                fwrite($out, $frameBin);
                hash_update($hmacCtx, $frameBin);
                $frameIdx++;
            }

            $sentinel = pack('N', 0);
            fwrite($out, $sentinel);
            hash_update($hmacCtx, $sentinel);

            $finalMac = hash_final($hmacCtx, true);
            fwrite($out, $finalMac);
        } finally {
            fclose($in);
            fclose($out);
        }
    }

    private function decryptFileStreaming(string $inputPath, string $outputPath, ?string $passphrase = null): int
    {
        $fileSize = filesize($inputPath);
        if ($fileSize === false || $fileSize < self::HEADER_BYTES + 36) {
            throw new \RuntimeException('ملف النسخة المشفر غير مكتمل أو تالف.');
        }

        $in = fopen($inputPath, 'rb');
        $out = fopen($outputPath, 'wb');
        if (!$in || !$out) {
            throw new \RuntimeException('تعذر قراءة ملف النسخة المشفرة.');
        }

        try {
            $header = fread($in, self::HEADER_BYTES);
            if ($header === false || strlen($header) !== self::HEADER_BYTES || substr($header, 0, 8) !== self::MAGIC) {
                throw new \RuntimeException('ترويسة ملف النسخة المشفرة غير صالحة.');
            }

            $keyMode = ord($header[8]);
            $salt = substr($header, 9, 16);

            if ($keyMode === self::KEY_MODE_PASSPHRASE) {
                if (empty($passphrase)) {
                    throw new \RuntimeException('هذه النسخة محمية بكلمة مرور استرداد خاصة. يرجى إدخال كلمة مرور الاسترداد.');
                }
                [$encKey, $macKey] = $this->derivePassphraseKeys($passphrase, $salt);
            } else {
                [$encKey, $macKey] = $this->deriveServerKeys($salt);
            }

            // Pass 1: Verify whole-file HMAC before outputting trusted plaintext
            fseek($in, 0);
            $hmacCtx = hash_init('sha256', HASH_HMAC, $macKey);
            $payloadBytes = $fileSize - 32;
            $readSoFar = 0;
            while ($readSoFar < $payloadBytes) {
                $toRead = min(1048576, $payloadBytes - $readSoFar);
                $buf = fread($in, $toRead);
                if ($buf === false || $buf === '') {
                    throw new \RuntimeException('انقطاع مفاجئ أثناء التحقق من توقيع النسخة.');
                }
                hash_update($hmacCtx, $buf);
                $readSoFar += strlen($buf);
            }
            $storedMac = fread($in, 32);
            $calcMac = hash_final($hmacCtx, true);
            if ($storedMac === false || !hash_equals($calcMac, $storedMac)) {
                throw new \RuntimeException('فشل التوقيع الأمني (HMAC) للنسخة: كلمة المرور أو المفتاح غير مطابق أو الملف معدل.');
            }

            // Pass 2: AEAD Frame-by-frame decryption
            fseek($in, self::HEADER_BYTES);
            $frameIdx = 0;
            while (ftell($in) < $payloadBytes) {
                $lenBin = fread($in, 4);
                if ($lenBin === false || strlen($lenBin) !== 4) {
                    throw new \RuntimeException('تلف في إطار بيانات النسخة المشفرة.');
                }
                $len = unpack('Nlen', $lenBin)['len'];
                if ($len === 0) {
                    break;
                }
                $iv = fread($in, 12);
                $tag = fread($in, 16);
                $cipher = fread($in, $len);

                if ($iv === false || $tag === false || $cipher === false || strlen($cipher) !== $len) {
                    throw new \RuntimeException('تلف في محتوى الإطار المشفر رقم ' . $frameIdx);
                }

                $aad = $header . pack('N', $frameIdx);
                $plain = openssl_decrypt($cipher, 'aes-256-gcm', $encKey, OPENSSL_RAW_DATA, $iv, $tag, $aad);
                if ($plain === false) {
                    throw new \RuntimeException('فشل فك التشفير GCM عند الإطار رقم ' . $frameIdx);
                }

                fwrite($out, $plain);
                $frameIdx++;
            }

            return $frameIdx;
        } finally {
            fclose($in);
            fclose($out);
        }
    }

    private function deriveServerKeys(?string $salt = null): array
    {
        $secret = (string) (env('BACKUP_ENCRYPTION_KEY') ?: config('app.key'));
        if (str_starts_with($secret, 'base64:')) {
            $decoded = base64_decode(substr($secret, 7), true);
            if ($decoded !== false) {
                $secret = $decoded;
            }
        }
        $keyMaterial = hash_hkdf('sha256', $secret, 64, 'ensan-backup-v1', $salt ?? '');
        return [substr($keyMaterial, 0, 32), substr($keyMaterial, 32, 32)];
    }

    private function derivePassphraseKeys(string $passphrase, string $salt): array
    {
        $keyMaterial = hash_pbkdf2('sha256', $passphrase, $salt, 100000, 64, true);
        return [substr($keyMaterial, 0, 32), substr($keyMaterial, 32, 32)];
    }

    private function createMysqlDefaultsFile(): string
    {
        $cnfPath = $this->vaultPath('.my_' . Str::random(10) . '.cnf');
        $cfg = config('database.connections.mysql');
        $password = str_replace(['\\', '"'], ['\\\\', '\\"'], (string) ($cfg['password'] ?? ''));

        $lines = [
            '[client]',
            'host=' . ($cfg['host'] ?? '127.0.0.1'),
            'port=' . ($cfg['port'] ?? '3306'),
            'user=' . ($cfg['username'] ?? 'root'),
            'password="' . $password . '"',
        ];
        if (!empty($cfg['unix_socket'])) {
            $lines[] = 'socket=' . $cfg['unix_socket'];
        }

        file_put_contents($cnfPath, implode("\n", $lines) . "\n");
        @chmod($cnfPath, 0600);

        return $cnfPath;
    }

    private function dumpDatabase(string $dumpFile, string $defaultsCnf): int
    {
        $dbName = (string) config('database.connections.mysql.database');
        $tables = DB::select('SHOW TABLES');
        $tablesCount = count($tables);

        $mysqldump = $this->findBinary('mysqldump');
        if ($mysqldump && function_exists('proc_open')) {
            $cmd = escapeshellarg($mysqldump)
                . ' --defaults-extra-file=' . escapeshellarg($defaultsCnf)
                . ' --single-transaction --routines --triggers --quick --default-character-set=utf8mb4'
                . ' ' . escapeshellarg($dbName)
                . ' > ' . escapeshellarg($dumpFile) . ' 2>&1';

            $exitCode = $this->runShellCommand($cmd);
            if ($exitCode === 0 && file_exists($dumpFile) && filesize($dumpFile) > 64) {
                return $tablesCount;
            }
        }

        // Pure-PHP PDO Streaming Fallback
        $this->dumpDatabasePurePhp($dumpFile, $dbName);
        return $tablesCount;
    }

    private function dumpDatabasePurePhp(string $dumpFile, string $dbName): void
    {
        $pdo = DB::connection('mysql')->getPdo();
        $fp = fopen($dumpFile, 'wb');
        if (!$fp) {
            throw new \RuntimeException('تعذر فتح ملف تفريغ قاعدة البيانات.');
        }

        try {
            fwrite($fp, "-- Ensan Pure-PHP Fallback SQL Dump\nSET FOREIGN_KEY_CHECKS=0;\nSET SQL_MODE='NO_AUTO_VALUE_ON_ZERO';\n\n");
            $tables = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'")->fetchAll(\PDO::FETCH_NUM);

            foreach ($tables as [$table]) {
                $quotedTable = '`' . str_replace('`', '``', $table) . '`';
                fwrite($fp, "DROP TABLE IF EXISTS {$quotedTable};\n");
                $createRow = $pdo->query("SHOW CREATE TABLE {$quotedTable}")->fetch(\PDO::FETCH_NUM);
                fwrite($fp, ($createRow[1] ?? '') . ";\n\n");

                $stmt = $pdo->query("SELECT * FROM {$quotedTable}");
                $batch = [];
                while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                    $vals = array_map(fn ($v) => $v === null ? 'NULL' : $pdo->quote((string) $v), array_values($row));
                    $batch[] = '(' . implode(',', $vals) . ')';
                    if (count($batch) >= 100) {
                        fwrite($fp, "INSERT INTO {$quotedTable} VALUES " . implode(',', $batch) . ";\n");
                        $batch = [];
                    }
                }
                if (!empty($batch)) {
                    fwrite($fp, "INSERT INTO {$quotedTable} VALUES " . implode(',', $batch) . ";\n");
                }
                fwrite($fp, "\n");
            }

            fwrite($fp, "SET FOREIGN_KEY_CHECKS=1;\n");
        } finally {
            fclose($fp);
        }
    }

    private function restoreDatabase(string $dumpFile, string $defaultsCnf): void
    {
        $dbName = (string) config('database.connections.mysql.database');
        $mysql = $this->findBinary('mysql');

        if ($mysql && function_exists('proc_open')) {
            $cmd = escapeshellarg($mysql)
                . ' --defaults-extra-file=' . escapeshellarg($defaultsCnf)
                . ' --default-character-set=utf8mb4 ' . escapeshellarg($dbName)
                . ' < ' . escapeshellarg($dumpFile) . ' 2>&1';

            $exitCode = $this->runShellCommand($cmd);
            if ($exitCode === 0) {
                return;
            }
        }

        throw new \RuntimeException('تعذر تنفيذ أداة mysql لاستعادة قاعدة البيانات على هذا الخادم.');
    }

    private function createTarGzArchive(string $workDir, string $tarGzPath, array $appRelativeDirs): void
    {
        $tar = $this->findBinary('tar');
        if (!$tar) {
            throw new \RuntimeException('أداة tar غير متوفرة على الخادم.');
        }

        $parts = [
            escapeshellarg($tar),
            '-czf',
            escapeshellarg($tarGzPath),
            '--exclude=' . escapeshellarg('storage/app/private/backups'),
            '--exclude=' . escapeshellarg('storage/app/private/backups/*'),
            '-C',
            escapeshellarg($workDir),
            'manifest.json',
            'database',
            'config',
        ];

        if (!empty($appRelativeDirs)) {
            $parts[] = '-C';
            $parts[] = escapeshellarg(base_path());
            foreach ($appRelativeDirs as $relDir) {
                $parts[] = escapeshellarg($relDir);
            }
        }

        $cmd = implode(' ', $parts) . ' 2>&1';
        $exitCode = $this->runShellCommand($cmd);
        if ($exitCode !== 0 || !file_exists($tarGzPath)) {
            throw new \RuntimeException('فشل ضغط حزمة النسخة الاحتياطية (tar exit ' . $exitCode . ').');
        }
    }

    private function extractTarGzArchive(string $tarGzPath, string $destDir): void
    {
        if (!is_dir($destDir)) {
            mkdir($destDir, 0750, true);
        }

        $tar = $this->findBinary('tar');
        if (!$tar) {
            throw new \RuntimeException('أداة tar غير متوفرة على الخادم.');
        }

        $cmd = escapeshellarg($tar) . ' -xzf ' . escapeshellarg($tarGzPath) . ' -C ' . escapeshellarg($destDir) . ' 2>&1';
        $exitCode = $this->runShellCommand($cmd);
        if ($exitCode !== 0) {
            throw new \RuntimeException('تعذر فك ضغط أرشيف النسخة الاحتياطية (tar exit ' . $exitCode . ').');
        }
    }

    private function resyncBackupMetadata(array $preservedSnapshots, array $preservedSettings): void
    {
        if (!empty($preservedSettings)) {
            unset($preservedSettings['created_at'], $preservedSettings['updated_at']);
            DB::table('backup_settings')->updateOrInsert(['id' => 1], $preservedSettings);
        }

        foreach ($preservedSnapshots as $row) {
            $code = $row['snapshot_code'] ?? null;
            if (!$code) {
                continue;
            }
            unset($row['id']);
            foreach (['created_at', 'updated_at', 'verified_at', 'expires_at'] as $dtCol) {
                if (!empty($row[$dtCol])) {
                    $row[$dtCol] = Carbon::parse($row[$dtCol])->format('Y-m-d H:i:s');
                }
            }
            DB::table('backup_snapshots')->updateOrInsert(['snapshot_code' => $code], $row);
        }
    }

    private function resolveSnapshotPath(BackupSnapshot $snapshot): string
    {
        if (empty($snapshot->file_path)) {
            throw new \RuntimeException('مسار ملف النسخة الاحتياطية غير مسجل.');
        }
        $absPath = $this->vaultPath(basename($snapshot->file_path));
        if (!file_exists($absPath)) {
            throw new \RuntimeException('ملف النسخة الاحتياطية غير موجود فعلياً على القرص.');
        }
        return $absPath;
    }

    private function findBinary(string $name): ?string
    {
        foreach (['/usr/bin/' . $name, '/usr/local/bin/' . $name, '/opt/homebrew/bin/' . $name, '/bin/' . $name] as $candidate) {
            if (is_executable($candidate)) {
                return $candidate;
            }
        }
        return null;
    }

    private function runShellCommand(string $cmd): int
    {
        $descriptors = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];
        $process = proc_open($cmd, $descriptors, $pipes);
        if (!is_resource($process)) {
            return 1;
        }
        fclose($pipes[0]);
        stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        stream_get_contents($pipes[2]);
        fclose($pipes[2]);
        return proc_close($process);
    }

    private function countFilesInDirectory(string $dir, array $excludeDirs = []): int
    {
        $count = 0;
        $items = @scandir($dir);
        if (!is_array($items)) {
            return 0;
        }
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }
            $full = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($full)) {
                if (!in_array($item, $excludeDirs, true)) {
                    $count += $this->countFilesInDirectory($full);
                }
            } else {
                $count++;
            }
        }
        return $count;
    }

    private function syncDirectorySafe(string $src, string $dst, array $excludeTopDirs = []): void
    {
        if (!is_dir($dst)) {
            mkdir($dst, 0755, true);
        }
        $items = @scandir($src);
        if (!is_array($items)) {
            return;
        }
        foreach ($items as $item) {
            if ($item === '.' || $item === '..' || in_array($item, $excludeTopDirs, true)) {
                continue;
            }
            $srcPath = $src . DIRECTORY_SEPARATOR . $item;
            $dstPath = $dst . DIRECTORY_SEPARATOR . $item;
            if (is_dir($srcPath)) {
                $this->syncDirectorySafe($srcPath, $dstPath);
            } else {
                @copy($srcPath, $dstPath);
            }
        }
    }

    private function removeDirectory(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $items = @scandir($dir);
        if (is_array($items)) {
            foreach ($items as $item) {
                if ($item === '.' || $item === '..') {
                    continue;
                }
                $full = $dir . DIRECTORY_SEPARATOR . $item;
                if (is_dir($full) && !is_link($full)) {
                    $this->removeDirectory($full);
                } else {
                    @unlink($full);
                }
            }
        }
        @rmdir($dir);
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = min((int) floor(log($bytes, 1024)), count($units) - 1);
        return round($bytes / (1024 ** $power), 2) . ' ' . $units[$power];
    }
}

