<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WebSetting;

class OptimizeExistingImages extends Command
{
    protected $signature = 'images:optimize';
    protected $description = 'Optimize all existing images in web_settings to WebP format';

    public function handle()
    {
        $this->info('🔍 Scanning for images in web_settings...');

        $imageSettings = WebSetting::where('type', 'image')->get();

        if ($imageSettings->isEmpty()) {
            $this->warn('No image settings found.');
            return 0;
        }

        $this->info("Found {$imageSettings->count()} image setting(s).");

        $optimized = 0;
        $skipped = 0;
        $failed = 0;

        $bar = $this->output->createProgressBar($imageSettings->count());
        $bar->start();

        foreach ($imageSettings as $setting) {
            $currentPath = $setting->value;

            if (empty($currentPath)) {
                $skipped++;
                $bar->advance();
                continue;
            }

            // Determine storage directory from the current path
            $storageDir = dirname($currentPath);

            $newPath = app(\App\Services\ImageUploadService::class)->uploadExisting($currentPath, $storageDir);

            if ($newPath !== null && $newPath !== $currentPath) {
                // Update the database with the new WebP path
                $setting->update(['value' => $newPath]);
                $optimized++;
                $this->line(" ✅ {$setting->key}: {$currentPath} → {$newPath}");
            }
            elseif ($newPath === $currentPath) {
                $skipped++; // Already WebP
            }
            else {
                $failed++;
                $this->line(" ⚠️  {$setting->key}: Could not optimize {$currentPath}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("📊 Results:");
        $this->info("   ✅ Optimized: {$optimized}");
        $this->info("   ⏭️  Skipped (already WebP or empty): {$skipped}");
        if ($failed > 0) {
            $this->warn("   ❌ Failed: {$failed}");
        }

        // Clear landing page cache since image paths changed
        \Illuminate\Support\Facades\Cache::forget('website_landing_page_data');
        $this->info('🗑️  Cache cleared.');

        $this->info('✨ Done!');

        return 0;
    }
}
