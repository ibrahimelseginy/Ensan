<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('project_monthly_volunteers')) {
            Schema::create('project_monthly_volunteers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->string('notes')->nullable();
            $table->timestamps();

            // Ensure a user is not added twice for the same month/year/project? 
            // Or maybe allow it if they have different roles? 
            // For now, let's keep it flexible but maybe add an index.
            $table->index(['project_id', 'year', 'month']);
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('project_monthly_volunteers');
    }
};
