<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('field_visits') && !Schema::hasColumn('field_visits', 'eligible_project_path')) {
            Schema::table('field_visits', function (Blueprint $table) {
                $table->string('eligible_project_path', 50)->default('general')->after('has_cancer_patient');
            });
        }

        if (Schema::hasTable('beneficiaries') && !Schema::hasColumn('beneficiaries', 'eligible_project_path')) {
            Schema::table('beneficiaries', function (Blueprint $table) {
                $table->string('eligible_project_path', 50)->default('general');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('field_visits') && Schema::hasColumn('field_visits', 'eligible_project_path')) {
            Schema::table('field_visits', function (Blueprint $table) {
                $table->dropColumn('eligible_project_path');
            });
        }

        if (Schema::hasTable('beneficiaries') && Schema::hasColumn('beneficiaries', 'eligible_project_path')) {
            Schema::table('beneficiaries', function (Blueprint $table) {
                $table->dropColumn('eligible_project_path');
            });
        }
    }
};
