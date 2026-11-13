<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('charity_guides')) {
            Schema::create('charity_guides', function (Blueprint $table) {
                $table->id();
                $table->string('governorate')->default('كفر الشيخ');
                $table->string('center')->index(); // المركز / المدينة
                $table->string('village')->index(); // القرية
                $table->string('coordinator_name')->index(); // الدليل / المنسق
                $table->string('phone')->nullable();
                $table->string('secondary_phone')->nullable();
                $table->string('exhibition_amount')->nullable(); // مبلغ المعرض + التاريخ
                $table->text('notes')->nullable(); // ملاحظات المعرض والدعاية
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('charity_guides');
    }
};

