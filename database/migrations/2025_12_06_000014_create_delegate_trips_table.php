<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('delegate_trips')) {
            Schema::create('delegate_trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delegate_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('description')->nullable(); // e.g. "Delivery to X"
            $table->decimal('cost', 10, 2)->default(0); // The value of the trip for the delegate
            $table->string('status')->default('pending'); // pending, paid
            $table->timestamps();
        });
        }
    }

    public function down()
    {
        Schema::dropIfExists('delegate_trips');
    }
};
