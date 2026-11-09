<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add monthly_amount and notes to beneficiary_sponsors pivot table
        if (Schema::hasTable('beneficiary_sponsors')) {
            Schema::table('beneficiary_sponsors', function (Blueprint $table) {
                if (!Schema::hasColumn('beneficiary_sponsors', 'monthly_amount')) {
                    $table->decimal('monthly_amount', 12, 2)->nullable();
                }
                if (!Schema::hasColumn('beneficiary_sponsors', 'notes')) {
                    $table->string('notes', 500)->nullable();
                }
            });
        }

        // 2. Add quantity to guest_house_assets (العدد في عهدة الأجهزة والمعدات)
        if (Schema::hasTable('guest_house_assets')) {
            Schema::table('guest_house_assets', function (Blueprint $table) {
                if (!Schema::hasColumn('guest_house_assets', 'quantity')) {
                    $table->unsignedInteger('quantity')->default(1);
                }
            });
        }

        // 3. Add quantity to guest_house_custodies (العدد في العهد العامة)
        if (Schema::hasTable('guest_house_custodies')) {
            Schema::table('guest_house_custodies', function (Blueprint $table) {
                if (!Schema::hasColumn('guest_house_custodies', 'quantity')) {
                    $table->unsignedInteger('quantity')->default(1);
                }
            });
        }

        // 4. Add companion_history to guest_house_patient_profiles (سجل المرافقين السابقين)
        if (Schema::hasTable('guest_house_patient_profiles')) {
            Schema::table('guest_house_patient_profiles', function (Blueprint $table) {
                if (!Schema::hasColumn('guest_house_patient_profiles', 'companion_history')) {
                    $table->json('companion_history')->nullable();
                }
            });
        }

        // 5. Make donations.donor_id nullable and widen purpose for direct aid records
        if (Schema::hasTable('donations')) {
            try {
                Schema::table('donations', function (Blueprint $table) {
                    $table->unsignedBigInteger('donor_id')->nullable()->change();
                    if (Schema::hasColumn('donations', 'purpose')) {
                        $table->string('purpose', 191)->nullable()->change();
                    }
                });
            } catch (\Throwable $e) {
                // Fallback raw SQL for MySQL/SQLite if doctrine/dbal change() is restricted
                try {
                    DB::statement('ALTER TABLE donations MODIFY donor_id BIGINT UNSIGNED NULL');
                    DB::statement('ALTER TABLE donations MODIFY purpose VARCHAR(191) NULL');
                } catch (\Throwable $ignored) {
                }
            }
        }

        // 6. Add missing reference & created_by columns to journal_entries and description to journal_entry_lines
        if (Schema::hasTable('journal_entries')) {
            Schema::table('journal_entries', function (Blueprint $table) {
                if (!Schema::hasColumn('journal_entries', 'reference')) {
                    $table->string('reference', 191)->nullable()->index();
                }
                if (!Schema::hasColumn('journal_entries', 'created_by')) {
                    $table->unsignedBigInteger('created_by')->nullable()->index();
                }
            });
        }

        if (Schema::hasTable('journal_entry_lines')) {
            Schema::table('journal_entry_lines', function (Blueprint $table) {
                if (!Schema::hasColumn('journal_entry_lines', 'description')) {
                    $table->string('description', 500)->nullable();
                }
            });
        }

        // 7. Add departure_notes to guest_house_stays
        if (Schema::hasTable('guest_house_stays')) {
            Schema::table('guest_house_stays', function (Blueprint $table) {
                if (!Schema::hasColumn('guest_house_stays', 'departure_notes')) {
                    $table->text('departure_notes')->nullable();
                }
            });
        }

        // 8. Widen inventory_transactions.type to support transfer_in/transfer_out/stock_count_*
        if (Schema::hasTable('inventory_transactions') && Schema::hasColumn('inventory_transactions', 'type')) {
            try {
                DB::statement('ALTER TABLE inventory_transactions MODIFY type VARCHAR(50) NOT NULL');
            } catch (\Throwable $ignored) {
            }
        }

        // 9. Widen field_visits.recommendation and add recommendation_notes
        if (Schema::hasTable('field_visits')) {
            Schema::table('field_visits', function (Blueprint $table) {
                if (!Schema::hasColumn('field_visits', 'recommendation_notes')) {
                    $table->text('recommendation_notes')->nullable();
                }
            });
            try {
                DB::statement('ALTER TABLE field_visits MODIFY recommendation VARCHAR(50) NULL');
            } catch (\Throwable $ignored) {
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('beneficiary_sponsors')) {
            Schema::table('beneficiary_sponsors', function (Blueprint $table) {
                $cols = array_filter(['monthly_amount', 'notes'], fn ($c) => Schema::hasColumn('beneficiary_sponsors', $c));
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }

        if (Schema::hasTable('guest_house_assets') && Schema::hasColumn('guest_house_assets', 'quantity')) {
            Schema::table('guest_house_assets', function (Blueprint $table) {
                $table->dropColumn('quantity');
            });
        }

        if (Schema::hasTable('guest_house_custodies') && Schema::hasColumn('guest_house_custodies', 'quantity')) {
            Schema::table('guest_house_custodies', function (Blueprint $table) {
                $table->dropColumn('quantity');
            });
        }

        if (Schema::hasTable('guest_house_patient_profiles') && Schema::hasColumn('guest_house_patient_profiles', 'companion_history')) {
            Schema::table('guest_house_patient_profiles', function (Blueprint $table) {
                $table->dropColumn('companion_history');
            });
        }
    }
};
