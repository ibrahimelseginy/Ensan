<?php

use App\Models\BackupSetting;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Schema;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::call(function () {
    try {
        if (!Schema::hasTable('backup_settings')) {
            return;
        }
        $settings = BackupSetting::current();
        if (!$settings->auto_enabled) {
            return;
        }

        $now = now();
        $targetHm = (string) ($settings->schedule_time ?: '02:00');
        if ($now->format('H:i') !== $targetHm) {
            return;
        }

        if ($settings->frequency === 'weekly' && $now->dayOfWeek !== 5) {
            return;
        }

        Artisan::call('backup:manage', [
            'action' => 'run',
            '--scheduled' => true,
        ]);
    } catch (\Throwable) {
    }
})->everyMinute()->name('system-backup-scheduler')->withoutOverlapping();
