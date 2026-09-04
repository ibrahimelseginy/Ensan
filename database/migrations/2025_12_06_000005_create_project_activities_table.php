<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('project_activities')) {
            Schema::create('project_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('type'); // exhibition, advertising, sorting
            $table->date('activity_date');
            $table->decimal('revenue', 12, 2)->default(0); // For exhibitions
            $table->text('description')->nullable(); // Data/Details
            $table->timestamps();
            
            $table->index(['project_id', 'type']);
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('project_activities');
    }
};
