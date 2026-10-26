<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Beneficiaries enhancements (Created by, Updated by, Classification)
        Schema::table('beneficiaries', function (Blueprint $table) {
            if (!Schema::hasColumn('beneficiaries', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('beneficiaries', 'updated_by')) {
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('beneficiaries', 'classification')) {
                $table->string('classification', 10)->default('A');
            }
        });

        // 2. Users enhancements (Unique volunteer / user code V-00001)
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'code')) {
                $table->string('code', 50)->nullable()->unique();
            }
        });

        // 3. Guest House Meal Servings (Companion meals count & name)
        Schema::table('guest_house_meal_servings', function (Blueprint $table) {
            if (!Schema::hasColumn('guest_house_meal_servings', 'patient_meals_count')) {
                $table->unsignedTinyInteger('patient_meals_count')->default(1);
            }
            if (!Schema::hasColumn('guest_house_meal_servings', 'companion_meals_count')) {
                $table->unsignedTinyInteger('companion_meals_count')->default(0);
            }
            if (!Schema::hasColumn('guest_house_meal_servings', 'total_meals_count')) {
                $table->unsignedTinyInteger('total_meals_count')->default(1);
            }
            if (!Schema::hasColumn('guest_house_meal_servings', 'companion_received')) {
                $table->boolean('companion_received')->default(false);
            }
            if (!Schema::hasColumn('guest_house_meal_servings', 'companion_name')) {
                $table->string('companion_name')->nullable();
            }
        });

        // 4. Guest House Meals (Inventory deduction & expense linkage)
        Schema::table('guest_house_meals', function (Blueprint $table) {
            if (!Schema::hasColumn('guest_house_meals', 'total_meals_served')) {
                $table->unsignedInteger('total_meals_served')->default(0);
            }
            if (!Schema::hasColumn('guest_house_meals', 'inventory_item_id')) {
                $table->foreignId('inventory_item_id')->nullable()->constrained('items')->nullOnDelete();
            }
            if (!Schema::hasColumn('guest_house_meals', 'warehouse_id')) {
                $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
            }
            if (!Schema::hasColumn('guest_house_meals', 'treasury_id')) {
                $table->foreignId('treasury_id')->nullable()->constrained('treasuries')->nullOnDelete();
            }
            if (!Schema::hasColumn('guest_house_meals', 'expense_amount')) {
                $table->decimal('expense_amount', 12, 2)->nullable();
            }
        });

        // 5. Child Assessments table for Project Zad
        if (!Schema::hasTable('child_assessments')) {
            Schema::create('child_assessments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('family_member_id')->constrained('beneficiary_family_members')->cascadeOnDelete();
                $table->foreignId('beneficiary_id')->constrained('beneficiaries')->cascadeOnDelete();
                $table->string('type', 20)->default('monthly'); // 'entry' or 'monthly'
                $table->date('assessment_date');
                $table->string('reviewer_name');
                $table->string('reviewer_role', 50)->default('employee'); // employee or volunteer
                $table->decimal('score_percentage', 5, 2)->nullable();
                $table->string('test_image_path')->nullable();
                $table->boolean('is_locked')->default(false); // Locked if entry assessment
                $table->string('academic_rating', 50)->nullable();
                $table->string('psychological_rating', 50)->nullable();
                $table->string('social_rating', 50)->nullable();
                $table->string('health_rating', 50)->nullable();
                $table->json('data')->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['family_member_id', 'type']);
                $table->index(['beneficiary_id', 'assessment_date']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('child_assessments');

        Schema::table('guest_house_meals', function (Blueprint $table) {
            $table->dropColumn([
                'total_meals_served',
                'inventory_item_id',
                'warehouse_id',
                'treasury_id',
                'expense_amount'
            ]);
        });

        Schema::table('guest_house_meal_servings', function (Blueprint $table) {
            $table->dropColumn([
                'patient_meals_count',
                'companion_meals_count',
                'total_meals_count',
                'companion_received',
                'companion_name'
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('code');
        });

        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            $table->dropColumn(['created_by', 'updated_by', 'classification']);
        });
    }
};
