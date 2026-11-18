<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Password Policies Table
        if (!Schema::hasTable('password_policies')) {
            Schema::create('password_policies', function (Blueprint $table) {
                $table->id();
                $table->string('tier_name', 32)->unique()->comment('المفتاح البرمجي: tier1_financial, tier2_field, tier3_volunteer');
                $table->string('display_name', 100);
                $table->unsignedInteger('min_length')->default(10);
                $table->unsignedInteger('max_age_days')->default(180);
                $table->unsignedInteger('grace_period_days')->default(7);
                $table->unsignedTinyInteger('max_snoozes')->default(3);
                $table->unsignedInteger('snooze_hours')->default(8);
                $table->unsignedTinyInteger('history_limit')->default(6);
                $table->boolean('disallow_common_passwords')->default(true);
                $table->boolean('require_mixed_case')->default(true);
                $table->boolean('require_numbers')->default(true);
                $table->boolean('require_special_chars')->default(true);
                $table->timestamps();
            });

            // Seed initial policies
            DB::table('password_policies')->insert([
                [
                    'tier_name'                 => 'tier1_financial',
                    'display_name'              => 'المستوى السيادي والمالي (خزائن، محاسبون، إدارة عليا)',
                    'min_length'                => 14,
                    'max_age_days'              => 90,
                    'grace_period_days'         => 5,
                    'max_snoozes'               => 2,
                    'snooze_hours'              => 4,
                    'history_limit'             => 10,
                    'disallow_common_passwords' => true,
                    'require_mixed_case'        => true,
                    'require_numbers'           => true,
                    'require_special_chars'     => true,
                    'created_at'                => now(),
                    'updated_at'                => now(),
                ],
                [
                    'tier_name'                 => 'tier2_field',
                    'display_name'              => 'المستوى التشغيلي والميداني (مناديب، باحثون، استقبال)',
                    'min_length'                => 10,
                    'max_age_days'              => 180,
                    'grace_period_days'         => 7,
                    'max_snoozes'               => 3,
                    'snooze_hours'              => 8,
                    'history_limit'             => 6,
                    'disallow_common_passwords' => true,
                    'require_mixed_case'        => true,
                    'require_numbers'           => true,
                    'require_special_chars'     => true,
                    'created_at'                => now(),
                    'updated_at'                => now(),
                ],
                [
                    'tier_name'                 => 'tier3_volunteer',
                    'display_name'              => 'المستوى الخدمي والتطوعي',
                    'min_length'                => 8,
                    'max_age_days'              => 365,
                    'grace_period_days'         => 14,
                    'max_snoozes'               => 5,
                    'snooze_hours'              => 24,
                    'history_limit'             => 3,
                    'disallow_common_passwords' => true,
                    'require_mixed_case'        => false,
                    'require_numbers'           => true,
                    'require_special_chars'     => false,
                    'created_at'                => now(),
                    'updated_at'                => now(),
                ],
            ]);
        }

        // 2. Alter Users Table for Password Lifecycle & Security Tier
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'security_tier')) {
                $table->string('security_tier', 32)->default('tier2_field')->after('password')->index();
            }
            if (!Schema::hasColumn('users', 'password_changed_at')) {
                $table->timestamp('password_changed_at')->nullable()->after('security_tier');
            }
            if (!Schema::hasColumn('users', 'password_expires_at')) {
                $table->timestamp('password_expires_at')->nullable()->after('password_changed_at')->index();
            }
            if (!Schema::hasColumn('users', 'force_password_change')) {
                $table->boolean('force_password_change')->default(false)->after('password_expires_at')->index();
            }
            if (!Schema::hasColumn('users', 'password_snooze_count')) {
                $table->unsignedTinyInteger('password_snooze_count')->default(0)->after('force_password_change');
            }
            if (!Schema::hasColumn('users', 'password_snoozed_until')) {
                $table->timestamp('password_snoozed_until')->nullable()->after('password_snooze_count');
            }
            if (!Schema::hasColumn('users', 'password_version')) {
                $table->unsignedInteger('password_version')->default(1)->after('password_snoozed_until');
            }
            if (!Schema::hasColumn('users', 'consecutive_failed_logins')) {
                $table->unsignedTinyInteger('consecutive_failed_logins')->default(0)->after('password_version');
            }
            if (!Schema::hasColumn('users', 'locked_until')) {
                $table->timestamp('locked_until')->nullable()->after('consecutive_failed_logins')->index();
            }
        });

        // Initialize password_changed_at for existing users if null
        DB::table('users')->whereNull('password_changed_at')->update([
            'password_changed_at' => now(),
            'password_expires_at' => now()->addDays(180),
        ]);

        // 3. Password Histories Table (Anti-Recycling)
        if (!Schema::hasTable('password_histories')) {
            Schema::create('password_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('password_hash');
                $table->timestamp('created_at')->useCurrent();

                $table->index(['user_id', 'created_at']);
            });
        }

        // 4. Password Reset Requests Table (Email Magic Link + 6-Digit OTP)
        if (!Schema::hasTable('password_reset_requests')) {
            Schema::create('password_reset_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->char('token_selector', 16)->index();
                $table->char('token_validator_hash', 64);
                $table->string('otp_code_hash')->nullable();
                $table->enum('channel', ['email_link', 'email_otp', 'admin_assisted'])->default('email_link');
                $table->string('target_destination', 191);
                $table->unsignedTinyInteger('attempts_count')->default(0);
                $table->unsignedTinyInteger('max_attempts')->default(3);
                $table->boolean('is_used')->default(false);
                $table->timestamp('used_at')->nullable();
                $table->timestamp('expires_at');
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamps();

                $table->index(['user_id', 'expires_at', 'is_used']);
            });
        }

        // 5. Security Audit Logs Table
        if (!Schema::hasTable('security_audit_logs')) {
            Schema::create('security_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('event_type', 64)->index();
                $table->enum('severity', ['info', 'warning', 'danger', 'critical'])->default('info');
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->string('channel', 32)->nullable();
                $table->json('metadata_payload')->nullable();
                $table->timestamp('created_at')->useCurrent();

                $table->index(['user_id', 'event_type', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('security_audit_logs');
        Schema::dropIfExists('password_reset_requests');
        Schema::dropIfExists('password_histories');
        Schema::dropIfExists('password_policies');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'security_tier',
                'password_changed_at',
                'password_expires_at',
                'force_password_change',
                'password_snooze_count',
                'password_snoozed_until',
                'password_version',
                'consecutive_failed_logins',
                'locked_until',
            ]);
        });
    }
};
