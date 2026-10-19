<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

final class PrivatizeDocuments extends Command
{
    protected $signature = 'documents:privatize';
    protected $description = 'Move existing identity documents, attachments and donation proofs to private storage.';

    public function handle(): int
    {
        $failed = 0;
        foreach ([
            'users' => ['contract_image', 'criminal_record_image', 'id_card_image'],
            'attachments' => ['path'],
            'donation_proofs' => ['image_path'],
        ] as $table => $columns) {
            if (!Schema::hasTable($table)) continue;
            foreach ($columns as $column) {
                if (!Schema::hasColumn($table, $column)) continue;
                DB::table($table)->whereNotNull($column)->orderBy('id')->chunkById(100, function ($rows) use ($table, $column, &$failed) {
                    foreach ($rows as $row) {
                        $raw = $row->{$column};
                        if (!$raw || str_starts_with($raw, 'private/')) continue;
                        $path = app(\App\Services\ImageUploadService::class)->normalizePath($raw);
                        if (!$path || str_contains($raw, '..')) { $failed++; continue; }
                        $sources = array_values(array_filter(['uploads', 'public'], fn ($disk) => Storage::disk($disk)->exists($path)));
                        if (!$sources) { $this->warn("Missing file: $table #{$row->id} $column"); $failed++; continue; }
                        $target = 'private/'.$path;
                        try {
                            $contents = Storage::disk($sources[0])->get($path);
                            if (!Storage::disk('local')->put($target, $contents) || hash('sha256', Storage::disk('local')->get($target)) !== hash('sha256', $contents)) {
                                throw new \RuntimeException('Copy verification failed.');
                            }
                            $changed = DB::table($table)->where('id', $row->id)->where($column, $raw)->update([$column => $target]);
                            if (!$changed) throw new \RuntimeException('Document changed during migration.');
                            foreach ($sources as $disk) {
                                if (!Storage::disk($disk)->delete($path)) throw new \RuntimeException('Public copy could not be removed.');
                            }
                        } catch (\Throwable $e) {
                            $this->error("Failed: $table #{$row->id} $column: ".$e->getMessage());
                            $failed++;
                        }
                    }
                });
            }
        }
        $this->info($failed ? "Incomplete: $failed documents require attention." : 'Documents are now private.');
        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
