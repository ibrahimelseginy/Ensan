<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('donors', function (Blueprint $table): void {
            $table->unsignedTinyInteger('monthly_donation_day')
                ->nullable()
                ->after('recurring_cycle')
                ->comment('يوم استحقاق التبرع الشهري من 1 إلى 31');
        });

        DB::table('donors')
            ->where('classification', 'recurring')
            ->where('recurring_cycle', 'monthly')
            ->whereNull('monthly_donation_day')
            ->update(['monthly_donation_day' => 1]);
    }

    public function down(): void
    {
        Schema::table('donors', function (Blueprint $table): void {
            $table->dropColumn('monthly_donation_day');
        });
    }
};
