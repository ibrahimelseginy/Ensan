<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Enhancements to field_visits
        if (Schema::hasTable('field_visits')) {
            Schema::table('field_visits', function (Blueprint $table) {
                if (Schema::hasColumn('field_visits', 'beneficiary_id')) {
                    $table->unsignedBigInteger('beneficiary_id')->nullable()->change();
                }
                if (!Schema::hasColumn('field_visits', 'case_application_id')) {
                    $table->unsignedBigInteger('case_application_id')->nullable()->after('beneficiary_id');
                }
                if (Schema::hasColumn('field_visits', 'status')) {
                    $table->string('status', 50)->default('awaiting_discussion')->change();
                }
            });
        }

        // 2. Table: psychological_support_reports
        if (!Schema::hasTable('psychological_support_reports')) {
            Schema::create('psychological_support_reports', function (Blueprint $table) {
                $table->id();
                $table->foreignId('beneficiary_id')->constrained('beneficiaries')->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('volunteer_name')->nullable();
                $table->string('report_month', 7); // YYYY-MM
                $table->string('medical_condition')->nullable();
                $table->string('treatment_stage')->nullable();
                $table->text('call_condition')->nullable();
                $table->string('psychological_state', 50)->nullable();
                $table->unsignedTinyInteger('rating')->default(5);
                $table->text('needs_improvement')->nullable();
                $table->text('nutrition_diet')->nullable();
                $table->text('general_notes')->nullable();
                $table->text('supporter_opinion')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['beneficiary_id', 'report_month']);
            });
        }

        // 3. Table: collaborations
        if (!Schema::hasTable('collaborations')) {
            Schema::create('collaborations', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('category', 100);
                $table->string('specialty', 150)->nullable();
                $table->text('service_provided')->nullable();
                $table->string('discount_volunteers')->nullable();
                $table->string('activation_volunteers')->nullable();
                $table->string('discount_beneficiaries')->nullable();
                $table->string('activation_beneficiaries')->nullable();
                $table->string('target_audience')->nullable();
                $table->string('working_hours')->nullable();
                $table->text('address')->nullable();
                $table->string('city', 100)->nullable();
                $table->string('phone', 50)->nullable();
                $table->string('phone_alt', 50)->nullable();
                $table->string('contact_person')->nullable();
                $table->string('contact_person_phone', 50)->nullable();
                $table->string('contact_person_role', 100)->nullable();
                $table->string('partnership_source')->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->string('status', 50)->default('active');
                $table->unsignedTinyInteger('rating')->default(5);
                $table->text('notes')->nullable();
                $table->json('doc_links')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index('category');
                $table->index('status');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('collaborations');
        Schema::dropIfExists('psychological_support_reports');
        if (Schema::hasTable('field_visits')) {
            Schema::table('field_visits', function (Blueprint $table) {
                if (Schema::hasColumn('field_visits', 'case_application_id')) {
                    $table->dropColumn('case_application_id');
                }
            });
        }
    }
};
