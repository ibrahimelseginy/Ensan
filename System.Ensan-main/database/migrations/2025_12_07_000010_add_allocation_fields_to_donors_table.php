<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('donors', function (Blueprint $table) {
            $table->string('allocation_type')->nullable()->after('sponsorship_monthly_amount')
                ->comment('project, campaign, sponsorship, guest_house, sadaqa_jariya');
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete()->after('allocation_type');
            $table->foreignId('guest_house_id')->nullable()->constrained()->nullOnDelete()->after('campaign_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donors', function (Blueprint $table) {
            $table->dropForeign(['campaign_id']);
            $table->dropForeign(['guest_house_id']);
            $table->dropColumn(['allocation_type', 'campaign_id', 'guest_house_id']);
        });
    }
};
