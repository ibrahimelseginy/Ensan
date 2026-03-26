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
        // Delegate Ratings Table
        if (!Schema::hasTable('delegate_ratings')) {
            Schema::create('delegate_ratings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('delegate_id')->constrained('delegates')->onDelete('cascade');
                $table->foreignId('trip_id')->nullable()->constrained('delegate_trips')->onDelete('set null');
                $table->foreignId('rated_by')->nullable()->constrained('users')->onDelete('set null');
                $table->integer('punctuality_rating')->default(5); // 1-5
                $table->integer('professionalism_rating')->default(5); // 1-5
                $table->integer('communication_rating')->default(5); // 1-5
                $table->integer('overall_rating')->default(5); // 1-5
                $table->text('comments')->nullable();
                $table->date('rating_date');
                $table->timestamps();
            });
        }

        // Vehicle Maintenance Table
        if (!Schema::hasTable('vehicle_maintenance')) {
            Schema::create('vehicle_maintenance', function (Blueprint $table) {
                $table->id();
                $table->foreignId('delegate_id')->constrained('delegates')->onDelete('cascade');
                $table->string('vehicle_type')->nullable(); // car, motorcycle, bicycle
                $table->string('vehicle_plate')->nullable();
                $table->string('maintenance_type'); // oil_change, tire_change, repair, inspection
                $table->text('description')->nullable();
                $table->decimal('cost', 10, 2)->default(0);
                $table->date('maintenance_date');
                $table->date('next_maintenance_date')->nullable();
                $table->integer('odometer_reading')->nullable();
                $table->string('status')->default('completed'); // scheduled, in_progress, completed
                $table->timestamps();
            });
        }

        // Scheduled Trips Table
        if (!Schema::hasTable('scheduled_trips')) {
            Schema::create('scheduled_trips', function (Blueprint $table) {
                $table->id();
                $table->foreignId('delegate_id')->constrained('delegates')->onDelete('cascade');
                $table->foreignId('route_id')->nullable()->constrained('travel_routes')->onDelete('set null');
                $table->string('title');
                $table->text('description')->nullable();
                $table->date('scheduled_date');
                $table->time('scheduled_time')->nullable();
                $table->string('from_location')->nullable();
                $table->string('to_location')->nullable();
                $table->decimal('estimated_cost', 10, 2)->nullable();
                $table->decimal('estimated_distance', 10, 2)->nullable();
                $table->string('status')->default('scheduled'); // scheduled, confirmed, in_progress, completed, cancelled
                $table->foreignId('actual_trip_id')->nullable()->constrained('delegate_trips')->onDelete('set null');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // Add additional fields to delegates table
        Schema::table('delegates', function (Blueprint $table) {
            if (!Schema::hasColumn('delegates', 'vehicle_type')) {
                $table->string('vehicle_type')->nullable()->after('active');
            }
            if (!Schema::hasColumn('delegates', 'vehicle_plate')) {
                $table->string('vehicle_plate')->nullable()->after('vehicle_type');
            }
            if (!Schema::hasColumn('delegates', 'license_number')) {
                $table->string('license_number')->nullable()->after('vehicle_plate');
            }
            if (!Schema::hasColumn('delegates', 'license_expiry')) {
                $table->date('license_expiry')->nullable()->after('license_number');
            }
            if (!Schema::hasColumn('delegates', 'emergency_contact')) {
                $table->string('emergency_contact')->nullable()->after('license_expiry');
            }
            if (!Schema::hasColumn('delegates', 'emergency_phone')) {
                $table->string('emergency_phone')->nullable()->after('emergency_contact');
            }
            if (!Schema::hasColumn('delegates', 'national_id')) {
                $table->string('national_id')->nullable()->after('emergency_phone');
            }
            if (!Schema::hasColumn('delegates', 'address')) {
                $table->text('address')->nullable()->after('national_id');
            }
            if (!Schema::hasColumn('delegates', 'hire_date')) {
                $table->date('hire_date')->nullable()->after('address');
            }
            if (!Schema::hasColumn('delegates', 'notes')) {
                $table->text('notes')->nullable()->after('hire_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_trips');
        Schema::dropIfExists('vehicle_maintenance');
        Schema::dropIfExists('delegate_ratings');
        
        Schema::table('delegates', function (Blueprint $table) {
            $table->dropColumn([
                'vehicle_type',
                'vehicle_plate',
                'license_number',
                'license_expiry',
                'emergency_contact',
                'emergency_phone',
                'national_id',
                'address',
                'hire_date',
                'notes'
            ]);
        });
    }
};
