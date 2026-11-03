<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add category and replenishment support to guest_house_custodies
        if (Schema::hasTable('guest_house_custodies')) {
            Schema::table('guest_house_custodies', function (Blueprint $table) {
                if (!Schema::hasColumn('guest_house_custodies', 'category')) {
                    $table->string('category', 50)->default('general')->after('type'); // food, equipment, general
                }
            });
        }

        // 2. Table: guest_house_assets (عهدة الأجهزة والمعدات)
        if (!Schema::hasTable('guest_house_assets')) {
            Schema::create('guest_house_assets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('guest_house_id')->constrained('guest_houses')->cascadeOnDelete();
                $table->foreignId('guest_house_wing_id')->nullable()->constrained('guest_house_wings')->nullOnDelete();
                $table->string('name'); // تكييف، ثلاجة، شاشة، كرسي متحرك، أسطوانة أكسجين...
                $table->string('code', 50)->nullable(); // كود الجهاز
                $table->enum('category', ['electrical', 'medical', 'furniture', 'other'])->default('electrical');
                $table->string('location')->nullable(); // الغرفة 102، الاستقبال، المطبخ
                $table->enum('status', ['working', 'needs_maintenance', 'out_of_service', 'scrapped'])->default('working');
                $table->string('serial_number', 100)->nullable();
                $table->date('purchase_date')->nullable();
                $table->date('warranty_until')->nullable();
                $table->decimal('cost', 12, 2)->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['guest_house_id', 'category', 'status']);
            });
        }

        // 3. Table: guest_house_asset_maintenances (سجل الصيانة وبلاغات الأعطال)
        if (!Schema::hasTable('guest_house_asset_maintenances')) {
            Schema::create('guest_house_asset_maintenances', function (Blueprint $table) {
                $table->id();
                $table->foreignId('guest_house_asset_id')->constrained('guest_house_assets')->cascadeOnDelete();
                $table->date('maintenance_date');
                $table->text('issue_description');
                $table->decimal('cost', 10, 2)->default(0);
                $table->string('technician_name', 150)->nullable();
                $table->string('technician_phone', 50)->nullable();
                $table->string('invoice_path')->nullable();
                $table->enum('status', ['pending', 'in_progress', 'completed'])->default('completed');
                $table->text('completion_notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        // 4. Table: guest_house_support_tickets (مركز التواصل والدعم والكول سنتر بالدار)
        if (!Schema::hasTable('guest_house_support_tickets')) {
            Schema::create('guest_house_support_tickets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('guest_house_id')->constrained('guest_houses')->cascadeOnDelete();
                $table->foreignId('beneficiary_id')->nullable()->constrained('beneficiaries')->nullOnDelete();
                $table->foreignId('guest_house_stay_id')->nullable()->constrained('guest_house_stays')->nullOnDelete();
                $table->string('caller_name');
                $table->string('caller_phone', 50)->nullable();
                $table->enum('caller_type', ['resident', 'companion', 'prospective_patient', 'hospital', 'donor', 'other'])->default('resident');
                $table->enum('category', ['inquiry', 'medical_followup', 'emergency', 'resident_service', 'complaint', 'satisfaction_survey'])->default('resident_service');
                $table->enum('priority', ['normal', 'urgent', 'emergency'])->default('normal');
                $table->enum('direction', ['incoming', 'outgoing', 'in_person'])->default('incoming');
                $table->string('subject');
                $table->text('details')->nullable();
                $table->text('action_taken')->nullable();
                $table->enum('status', ['open', 'in_progress', 'resolved', 'cancelled'])->default('open');
                $table->unsignedTinyInteger('satisfaction_rating')->nullable(); // 1 to 5
                $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('resolved_at')->nullable();
                $table->timestamps();

                $table->index(['guest_house_id', 'status', 'category']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_house_support_tickets');
        Schema::dropIfExists('guest_house_asset_maintenances');
        Schema::dropIfExists('guest_house_assets');

        if (Schema::hasTable('guest_house_custodies') && Schema::hasColumn('guest_house_custodies', 'category')) {
            Schema::table('guest_house_custodies', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }
    }
};
