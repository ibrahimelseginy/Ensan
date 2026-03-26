<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mobile_contact_phones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_info_id')->constrained('mobile_contact_infos')->onDelete('cascade');
            $table->string('phone', 30);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mobile_contact_phones');
    }
};
