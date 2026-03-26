<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('project_volunteers')) {
            Schema::create('project_volunteers', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('project_id');
                $table->unsignedBigInteger('user_id');
                $table->timestamps();
                $table->unique(['project_id','user_id']);
            });
        }
    }
    public function down(): void
    {
        if (Schema::hasTable('project_volunteers')) { Schema::dropIfExists('project_volunteers'); }
    }
};

