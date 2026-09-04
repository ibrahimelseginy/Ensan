<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class FixStoragePaths extends Command
{
    protected $signature = 'storage:fix-paths {--dry-run : Show what would be changed without actually changing}';

    protected $description = 'Fix old image paths in DB that incorrectly start with storage/ prefix';

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        $tables = [
            // table => [column(s)]
            'web_settings'           => ['value'],
            'campaigns'              => ['image_path', 'icon_path'],
            'web_partners'           => ['logo_path'],
            'web_news'               => ['image_path'],
            'web_pages'              => ['image_path'],
            'web_opinions'           => ['image_path'],
            'web_sectors'            => ['image_path'],
            'web_features'           => ['image_path'],
            'web_volunteer_requests' => ['image_path'],
            'web_volunteers_wall'    => ['image_path'],
            'web_dynamic_cards'      => ['image'],
            'users'                  => ['image_path'],
            'web_board_members'      => ['image_path'],
            'web_testimonials'       => ['image_path'],
            'projects'               => ['image_path'],
            'mobile_banners'         => ['image_path'],
            'mobile_hero_cards'      => ['image_path'],
            'mobile_news'            => ['image_path'],
        ];

        $totalFixed = 0;

        foreach ($tables as $table => $columns) {
            // Skip if table doesn't exist
            if (!$this->tableExists($table)) {
                $this->line("  <comment>Skipping</comment> {$table} (table not found)");
                continue;
            }

            foreach ($columns as $column) {
                // Skip if column doesn't exist
                if (!$this->columnExists($table, $column)) {
                    continue;
                }

                // Find rows with bad paths
                $rows = DB::table($table)
                    ->whereNotNull($column)
                    ->where(function ($q) use ($column) {
                        $q->where($column, 'like', 'storage/%')
                          ->orWhere($column, 'like', '/storage/%');
                    })
                    ->select('id', $column)
                    ->get();

                if ($rows->isEmpty()) {
                    continue;
                }

                $this->line("  Found <fg=yellow>{$rows->count()}</> bad paths in <fg=cyan>{$table}.{$column}</>");

                foreach ($rows as $row) {
                    $oldPath = $row->{$column};
                    $newPath = preg_replace('#^/?storage/#', '', $oldPath);

                    $this->line("    <fg=red>{$oldPath}</> → <fg=green>{$newPath}</>");

                    if (!$isDryRun) {
                        DB::table($table)
                            ->where('id', $row->id)
                            ->update([$column => $newPath]);
                    }

                    $totalFixed++;
                }
            }
        }

        if ($isDryRun) {
            $this->newLine();
            $this->warn("DRY RUN — No changes made. {$totalFixed} paths would be fixed.");
        } else {
            $this->newLine();
            $this->info("✅ Done! Fixed {$totalFixed} paths in the database.");
        }

        return self::SUCCESS;
    }

    private function tableExists(string $table): bool
    {
        return DB::getSchemaBuilder()->hasTable($table);
    }

    private function columnExists(string $table, string $column): bool
    {
        return DB::getSchemaBuilder()->hasColumn($table, $column);
    }
}
