<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('campaign_daily_menus')) {
            Schema::create('campaign_daily_menus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->cascadeOnDelete();
            $table->date('day_date');
            $table->foreignId('responsible_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('meal_type')->nullable(); // iftar, suhoor
            $table->string('menu')->nullable();
            $table->integer('meal_count')->default(0);
            $table->text('ingredients')->nullable(); // quantities
            $table->timestamps();
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('campaign_daily_menus');
    }
};
