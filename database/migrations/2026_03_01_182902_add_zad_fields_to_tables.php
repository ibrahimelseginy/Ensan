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
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->string('mother_name')->nullable();
            $table->text('children_names')->nullable();
            $table->string('backup_phone')->nullable();
            $table->integer('children_count')->nullable()->default(0);
            $table->integer('sponsored_children_count')->nullable()->default(0);
            $table->string('study_grade')->nullable();
            $table->string('poultry_type')->nullable(); // فرخة/بطة
            $table->text('notes_cases')->nullable(); // ملاحظات تخص الحالات
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('merchant_name')->nullable(); // اسم التاجر
            $table->string('source_name')->nullable(); // اسم المصدر
            $table->unsignedBigInteger('project_id')->nullable();
            $table->text('notes')->nullable(); // ملاحظات

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->dropColumn(['mother_name', 'children_names', 'backup_phone', 'children_count', 'sponsored_children_count', 'study_grade', 'poultry_type', 'notes_cases']);
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn(['merchant_name', 'source_name', 'project_id', 'notes']);
        });
    }
};
