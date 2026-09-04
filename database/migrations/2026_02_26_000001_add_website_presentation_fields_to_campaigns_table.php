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
            $table->string('icon_path')->nullable();
            $table->string('start_date_text')->nullable()->comment('مثال: تبدأ بعد 45 يوم 12 ساعة');
            $table->string('ui_progress_override')->nullable()->comment('مثال: 65%');
            $table->string('ui_collected_override')->nullable()->comment('مثال: 325,000 ج.م');
            $table->string('ui_goal_override')->nullable()->comment('مثال: من 500,000 ج.م أو 10,000 كرتونة');
            $table->string('ui_beneficiaries_override')->nullable()->comment('مثال: +2,500 مستفيد');
            $table->string('ui_share_override')->nullable()->comment('مثال: سهم البطانية: 250 ج.م');
            $table->string('ui_theme_color')->nullable()->default('#0d6efd');
            $table->string('ui_button_color')->nullable()->default('#0d6efd');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropColumn([
                'icon_path',
                'start_date_text',
                'ui_progress_override',
                'ui_collected_override',
                'ui_goal_override',
                'ui_beneficiaries_override',
                'ui_share_override',
                'ui_theme_color',
                'ui_button_color'
            ]);
        });
    }
};
