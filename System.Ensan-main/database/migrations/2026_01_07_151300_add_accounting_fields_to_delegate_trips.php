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
        Schema::table('delegate_trips', function (Blueprint $table) {
            if (!Schema::hasColumn('delegate_trips', 'journal_entry_id')) {
                $table->unsignedBigInteger('journal_entry_id')->nullable()->after('status');
                $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('delegate_trips', 'payment_method')) {
                $table->string('payment_method')->nullable()->after('journal_entry_id');
            }
            
            if (!Schema::hasColumn('delegate_trips', 'notes')) {
                $table->text('notes')->nullable()->after('payment_method');
            }
            
            if (!Schema::hasColumn('delegate_trips', 'from_location')) {
                $table->string('from_location')->nullable()->after('description');
            }
            
            if (!Schema::hasColumn('delegate_trips', 'to_location')) {
                $table->string('to_location')->nullable()->after('from_location');
            }
            
            if (!Schema::hasColumn('delegate_trips', 'distance_km')) {
                $table->decimal('distance_km', 10, 2)->nullable()->after('to_location');
            }
            
            if (!Schema::hasColumn('delegate_trips', 'fuel_cost')) {
                $table->decimal('fuel_cost', 10, 2)->default(0)->after('cost');
            }
            
            if (!Schema::hasColumn('delegate_trips', 'other_expenses')) {
                $table->decimal('other_expenses', 10, 2)->default(0)->after('fuel_cost');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delegate_trips', function (Blueprint $table) {
            $table->dropForeign(['journal_entry_id']);
            $table->dropColumn([
                'journal_entry_id',
                'payment_method',
                'notes',
                'from_location',
                'to_location',
                'distance_km',
                'fuel_cost',
                'other_expenses'
            ]);
        });
    }
};
