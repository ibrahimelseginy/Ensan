<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (!Schema::hasColumn('users', 'is_sales')) {
                $table->boolean('is_sales')->default(false)->after('is_volunteer');
            }
            if (!Schema::hasColumn('users', 'monthly_target')) {
                $table->decimal('monthly_target', 15, 2)->default(0)->after('salary');
            }
            if (!Schema::hasColumn('users', 'commission_rate')) {
                $table->decimal('commission_rate', 5, 2)->default(0)->after('monthly_target');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $columns = array_filter(
                ['is_sales', 'monthly_target', 'commission_rate'],
                fn (string $column): bool => Schema::hasColumn('users', $column)
            );

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
