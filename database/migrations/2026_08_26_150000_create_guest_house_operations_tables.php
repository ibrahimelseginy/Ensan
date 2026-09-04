<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guest_houses', function (Blueprint $table) {
            if (! Schema::hasColumn('guest_houses', 'governorate')) {
                $table->string('governorate')->nullable()->after('name')->index();
            }
        });

        Schema::create('guest_house_wings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_house_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['guest_house_id', 'name']);
        });

        Schema::create('guest_house_beds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_house_wing_id')->constrained()->cascadeOnDelete();
            $table->string('number');
            $table->enum('status', ['available', 'maintenance'])->default('available');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['guest_house_wing_id', 'number']);
        });

        Schema::create('guest_house_patient_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('guest_house_id')->constrained()->cascadeOnDelete();
            $table->enum('treatment_type', ['chemotherapy', 'radiation', 'other'])->nullable();
            $table->string('medical_center')->nullable();
            $table->unsignedInteger('sessions_count')->nullable();
            $table->string('patient_id_front_path')->nullable();
            $table->string('patient_id_back_path')->nullable();
            $table->string('followup_card_path')->nullable();
            $table->string('referral_letter_path')->nullable();
            $table->text('medical_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('guest_house_stays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_house_id')->constrained()->cascadeOnDelete();
            $table->foreignId('beneficiary_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guest_house_bed_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('previous_stay_id')->nullable()->constrained('guest_house_stays')->nullOnDelete();
            $table->nullableMorphs('source');
            $table->enum('status', ['pending', 'approved', 'resident', 'departed', 'rejected'])->default('resident');
            $table->date('arrival_date');
            $table->unsignedInteger('expected_days')->nullable();
            $table->timestamp('admitted_at')->nullable();
            $table->timestamp('departed_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['guest_house_id', 'status']);
        });

        Schema::create('guest_house_custodies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_house_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['financial', 'in_kind']);
            $table->foreignId('treasury_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['guest_house_id', 'name']);
        });

        Schema::create('guest_house_meals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_house_id')->constrained()->cascadeOnDelete();
            $table->date('meal_date');
            $table->enum('meal_type', ['breakfast', 'lunch', 'dinner']);
            $table->time('served_at')->nullable();
            $table->string('image_path')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->unique(['guest_house_id', 'meal_date', 'meal_type']);
        });

        Schema::create('guest_house_meal_servings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_house_meal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('beneficiary_id')->constrained()->cascadeOnDelete();
            $table->boolean('received')->default(false);
            $table->timestamp('received_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['guest_house_meal_id', 'beneficiary_id'], 'gh_meal_serving_unique');
        });

        Schema::table('inventory_transactions', function (Blueprint $table) {
            if (! Schema::hasColumn('inventory_transactions', 'guest_house_id')) {
                $table->foreignId('guest_house_id')->nullable()->after('campaign_id')->constrained()->nullOnDelete();
            }
        });

        Schema::table('web_room_bookings', function (Blueprint $table) {
            if (! Schema::hasColumn('web_room_bookings', 'beneficiary_id')) {
                $table->foreignId('beneficiary_id')->nullable()->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('web_room_bookings', 'guest_house_stay_id')) {
                $table->foreignId('guest_house_stay_id')->nullable()->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('web_room_bookings', 'admin_notes')) {
                $table->text('admin_notes')->nullable();
            }
            if (! Schema::hasColumn('web_room_bookings', 'treatment_type')) {
                $table->string('treatment_type')->nullable();
            }
            if (! Schema::hasColumn('web_room_bookings', 'sessions_count')) {
                $table->unsignedInteger('sessions_count')->nullable();
            }
        });

        Schema::table('mobile_case_applications', function (Blueprint $table) {
            if (! Schema::hasColumn('mobile_case_applications', 'guest_house_id')) {
                $table->foreignId('guest_house_id')->nullable()->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('mobile_case_applications', 'beneficiary_id')) {
                $table->foreignId('beneficiary_id')->nullable()->constrained()->nullOnDelete();
            }
            if (! Schema::hasColumn('mobile_case_applications', 'guest_house_stay_id')) {
                $table->foreignId('guest_house_stay_id')->nullable()->constrained()->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('mobile_case_applications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('guest_house_stay_id');
            $table->dropConstrainedForeignId('beneficiary_id');
            $table->dropConstrainedForeignId('guest_house_id');
        });
        Schema::table('web_room_bookings', function (Blueprint $table) {
            $table->dropColumn(['admin_notes', 'treatment_type', 'sessions_count']);
            $table->dropConstrainedForeignId('guest_house_stay_id');
            $table->dropConstrainedForeignId('beneficiary_id');
        });
        Schema::table('inventory_transactions', fn (Blueprint $table) => $table->dropConstrainedForeignId('guest_house_id'));
        Schema::dropIfExists('guest_house_meal_servings');
        Schema::dropIfExists('guest_house_meals');
        Schema::dropIfExists('guest_house_custodies');
        Schema::dropIfExists('guest_house_stays');
        Schema::dropIfExists('guest_house_patient_profiles');
        Schema::dropIfExists('guest_house_beds');
        Schema::dropIfExists('guest_house_wings');
        Schema::table('guest_houses', fn (Blueprint $table) => $table->dropColumn('governorate'));
    }
};
