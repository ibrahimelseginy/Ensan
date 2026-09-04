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
        Schema::table('campaigns', function (Blueprint $table) {
            $table->string('ui_contribute_btn')->nullable();
            $table->string('ui_remind_btn')->nullable();
            $table->string('ui_ended_btn')->nullable();
            $table->string('ui_filter_upcoming')->nullable();
            $table->string('ui_collected_label')->nullable();
            $table->string('ui_benefited_label')->nullable();
            $table->string('ui_share_label')->nullable();
            $table->string('ui_goal_label')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn([
                'ui_contribute_btn',
                'ui_remind_btn',
                'ui_ended_btn',
                'ui_filter_upcoming',
                'ui_collected_label',
                'ui_benefited_label',
                'ui_share_label',
                'ui_goal_label'
            ]);
        });
    }
};
