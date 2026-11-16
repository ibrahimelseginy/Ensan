<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('hr_documents')) {
            Schema::create('hr_documents', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('category', 50)->default('sessions')->index();
                $table->date('report_date')->nullable()->index();
                $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
                $table->text('description')->nullable();
                $table->string('file_path');
                $table->string('original_name');
                $table->string('mime', 150)->nullable();
                $table->unsignedBigInteger('file_size')->nullable();
                $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('hr_documents');
    }
};
