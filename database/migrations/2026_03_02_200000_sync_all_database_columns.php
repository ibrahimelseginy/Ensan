<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration شامل لضمان اتساق قاعدة البيانات مع جميع الـ Models
 * كل عمود يتم إضافته فقط إذا لم يكن موجوداً
 */
return new class extends Migration 
{
    public function up(): void
    {
        // ═══════════════════════════════════════════════════════
        // جدول المستخدمين (users)
        // ═══════════════════════════════════════════════════════
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                if (!Schema::hasColumn('users', 'annual_leave_quota')) {
                    $table->integer('annual_leave_quota')->default(21);
                }
                if (!Schema::hasColumn('users', 'leave_balance')) {
                    $table->decimal('leave_balance', 8, 2)->default(21);
                }
            });
        }

        // ═══════════════════════════════════════════════════════
        // جدول المتبرعين (donors)
        // ═══════════════════════════════════════════════════════
        if (Schema::hasTable('donors')) {
            Schema::table('donors', function (Blueprint $table) {
                if (!Schema::hasColumn('donors', 'sponsorship_type')) {
                    $table->string('sponsorship_type')->nullable();
                }
                if (!Schema::hasColumn('donors', 'sponsored_beneficiary_id')) {
                    $table->unsignedBigInteger('sponsored_beneficiary_id')->nullable();
                }
                if (!Schema::hasColumn('donors', 'sponsorship_project_id')) {
                    $table->unsignedBigInteger('sponsorship_project_id')->nullable();
                }
                if (!Schema::hasColumn('donors', 'sponsorship_monthly_amount')) {
                    $table->decimal('sponsorship_monthly_amount', 12, 2)->nullable();
                }
                if (!Schema::hasColumn('donors', 'allocation_type')) {
                    $table->string('allocation_type')->nullable();
                }
                if (!Schema::hasColumn('donors', 'campaign_id')) {
                    $table->unsignedBigInteger('campaign_id')->nullable();
                }
                if (!Schema::hasColumn('donors', 'guest_house_id')) {
                    $table->unsignedBigInteger('guest_house_id')->nullable();
                }
            });
        }

        // ═══════════════════════════════════════════════════════
        // جدول التبرعات (donations)
        // ═══════════════════════════════════════════════════════
        if (Schema::hasTable('donations')) {
            Schema::table('donations', function (Blueprint $table) {
                if (!Schema::hasColumn('donations', 'cash_channel')) {
                    $table->string('cash_channel')->nullable();
                }
                if (!Schema::hasColumn('donations', 'receipt_number')) {
                    $table->string('receipt_number')->nullable();
                }
                if (!Schema::hasColumn('donations', 'item_id')) {
                    $table->unsignedBigInteger('item_id')->nullable();
                }
                if (!Schema::hasColumn('donations', 'quantity')) {
                    $table->decimal('quantity', 10, 2)->nullable();
                }
                if (!Schema::hasColumn('donations', 'guest_house_id')) {
                    $table->unsignedBigInteger('guest_house_id')->nullable();
                }
                if (!Schema::hasColumn('donations', 'treasury_id')) {
                    $table->unsignedBigInteger('treasury_id')->nullable();
                }
                if (!Schema::hasColumn('donations', 'created_by')) {
                    $table->unsignedBigInteger('created_by')->nullable();
                }
                if (!Schema::hasColumn('donations', 'auto_added_to_inventory')) {
                    $table->boolean('auto_added_to_inventory')->default(false);
                }
                if (!Schema::hasColumn('donations', 'status')) {
                    $table->string('status')->default('active');
                }
                if (!Schema::hasColumn('donations', 'cancelled_at')) {
                    $table->timestamp('cancelled_at')->nullable();
                }
                if (!Schema::hasColumn('donations', 'cancelled_by')) {
                    $table->unsignedBigInteger('cancelled_by')->nullable();
                }
                if (!Schema::hasColumn('donations', 'cancellation_reason')) {
                    $table->text('cancellation_reason')->nullable();
                }
            });
        }

        // ═══════════════════════════════════════════════════════
        // جدول المستفيدين (beneficiaries)
        // ═══════════════════════════════════════════════════════
        if (Schema::hasTable('beneficiaries')) {
            Schema::table('beneficiaries', function (Blueprint $table) {
                if (!Schema::hasColumn('beneficiaries', 'code')) {
                    $table->string('code')->nullable()->unique();
                }
                if (!Schema::hasColumn('beneficiaries', 'notes')) {
                    $table->text('notes')->nullable();
                }
                if (!Schema::hasColumn('beneficiaries', 'rejection_reason')) {
                    $table->text('rejection_reason')->nullable();
                }
                if (!Schema::hasColumn('beneficiaries', 'guest_house_id')) {
                    $table->unsignedBigInteger('guest_house_id')->nullable();
                }
                if (!Schema::hasColumn('beneficiaries', 'mother_name')) {
                    $table->string('mother_name')->nullable();
                }
                if (!Schema::hasColumn('beneficiaries', 'children_names')) {
                    $table->text('children_names')->nullable();
                }
                if (!Schema::hasColumn('beneficiaries', 'backup_phone')) {
                    $table->string('backup_phone')->nullable();
                }
                if (!Schema::hasColumn('beneficiaries', 'children_count')) {
                    $table->integer('children_count')->nullable();
                }
                if (!Schema::hasColumn('beneficiaries', 'sponsored_children_count')) {
                    $table->integer('sponsored_children_count')->nullable();
                }
                if (!Schema::hasColumn('beneficiaries', 'study_grade')) {
                    $table->string('study_grade')->nullable();
                }
                if (!Schema::hasColumn('beneficiaries', 'poultry_type')) {
                    $table->string('poultry_type')->nullable();
                }
                if (!Schema::hasColumn('beneficiaries', 'notes_cases')) {
                    $table->text('notes_cases')->nullable();
                }
                if (!Schema::hasColumn('beneficiaries', 'meat')) {
                    $table->boolean('meat')->default(false);
                }
                if (!Schema::hasColumn('beneficiaries', 'allocation_type')) {
                    $table->string('allocation_type')->nullable();
                }
                if (!Schema::hasColumn('beneficiaries', 'child_sponsorship_type')) {
                    $table->string('child_sponsorship_type')->nullable();
                }
            });
        }

        // ═══════════════════════════════════════════════════════
        // جدول المهام (tasks)
        // ═══════════════════════════════════════════════════════
        if (Schema::hasTable('tasks')) {
            Schema::table('tasks', function (Blueprint $table) {
                if (!Schema::hasColumn('tasks', 'entity_type')) {
                    $table->string('entity_type')->nullable();
                }
                if (!Schema::hasColumn('tasks', 'entity_id')) {
                    $table->unsignedBigInteger('entity_id')->nullable();
                }
                if (!Schema::hasColumn('tasks', 'is_volunteer_task')) {
                    $table->boolean('is_volunteer_task')->default(false);
                }
                if (!Schema::hasColumn('tasks', 'volunteer_role')) {
                    $table->string('volunteer_role')->nullable();
                }
                if (!Schema::hasColumn('tasks', 'quality_rating')) {
                    $table->tinyInteger('quality_rating')->nullable();
                }
                if (!Schema::hasColumn('tasks', 'punctuality_rating')) {
                    $table->tinyInteger('punctuality_rating')->nullable();
                }
                if (!Schema::hasColumn('tasks', 'evaluation_notes')) {
                    $table->text('evaluation_notes')->nullable();
                }
            });
        }

        // ═══════════════════════════════════════════════════════
        // جدول المصروفات (expenses)
        // ═══════════════════════════════════════════════════════
        if (Schema::hasTable('expenses')) {
            Schema::table('expenses', function (Blueprint $table) {
                if (!Schema::hasColumn('expenses', 'treasury_id')) {
                    $table->unsignedBigInteger('treasury_id')->nullable();
                }
                if (!Schema::hasColumn('expenses', 'workspace_id')) {
                    $table->unsignedBigInteger('workspace_id')->nullable();
                }
                if (!Schema::hasColumn('expenses', 'category')) {
                    $table->string('category')->nullable();
                }
                if (!Schema::hasColumn('expenses', 'status')) {
                    $table->string('status')->default('active');
                }
                if (!Schema::hasColumn('expenses', 'cancelled_at')) {
                    $table->timestamp('cancelled_at')->nullable();
                }
                if (!Schema::hasColumn('expenses', 'cancelled_by')) {
                    $table->unsignedBigInteger('cancelled_by')->nullable();
                }
                if (!Schema::hasColumn('expenses', 'cancellation_reason')) {
                    $table->text('cancellation_reason')->nullable();
                }
                if (!Schema::hasColumn('expenses', 'journal_entry_id')) {
                    $table->unsignedBigInteger('journal_entry_id')->nullable();
                }
            });
        }

        // ═══════════════════════════════════════════════════════
        // جدول الرواتب (payrolls)
        // ═══════════════════════════════════════════════════════
        if (Schema::hasTable('payrolls')) {
            Schema::table('payrolls', function (Blueprint $table) {
                if (!Schema::hasColumn('payrolls', 'treasury_id')) {
                    $table->unsignedBigInteger('treasury_id')->nullable();
                }
                if (!Schema::hasColumn('payrolls', 'notes')) {
                    $table->text('notes')->nullable();
                }
                if (!Schema::hasColumn('payrolls', 'bonuses')) {
                    $table->decimal('bonuses', 12, 2)->default(0);
                }
                if (!Schema::hasColumn('payrolls', 'deductions')) {
                    $table->decimal('deductions', 12, 2)->default(0);
                }
                if (!Schema::hasColumn('payrolls', 'net_amount')) {
                    $table->decimal('net_amount', 12, 2)->default(0);
                }
                if (!Schema::hasColumn('payrolls', 'journal_entry_id')) {
                    $table->unsignedBigInteger('journal_entry_id')->nullable();
                }
                if (!Schema::hasColumn('payrolls', 'status')) {
                    $table->string('status')->default('pending');
                }
                if (!Schema::hasColumn('payrolls', 'cancelled_at')) {
                    $table->timestamp('cancelled_at')->nullable();
                }
                if (!Schema::hasColumn('payrolls', 'cancelled_by')) {
                    $table->unsignedBigInteger('cancelled_by')->nullable();
                }
                if (!Schema::hasColumn('payrolls', 'cancellation_reason')) {
                    $table->text('cancellation_reason')->nullable();
                }
            });
        }

        // ═══════════════════════════════════════════════════════
        // جدول حضور الموظفين (employee_attendances)
        // ═══════════════════════════════════════════════════════
        if (Schema::hasTable('employee_attendances')) {
            Schema::table('employee_attendances', function (Blueprint $table) {
                if (!Schema::hasColumn('employee_attendances', 'quality_rating')) {
                    $table->tinyInteger('quality_rating')->nullable();
                }
                if (!Schema::hasColumn('employee_attendances', 'punctuality_rating')) {
                    $table->tinyInteger('punctuality_rating')->nullable();
                }
                if (!Schema::hasColumn('employee_attendances', 'evaluation_notes')) {
                    $table->text('evaluation_notes')->nullable();
                }
            });
        }

        // ═══════════════════════════════════════════════════════
        // جدول حضور المتطوعين (volunteer_attendances)
        // ═══════════════════════════════════════════════════════
        if (Schema::hasTable('volunteer_attendances')) {
            Schema::table('volunteer_attendances', function (Blueprint $table) {
                if (!Schema::hasColumn('volunteer_attendances', 'quality_rating')) {
                    $table->tinyInteger('quality_rating')->nullable();
                }
                if (!Schema::hasColumn('volunteer_attendances', 'punctuality_rating')) {
                    $table->tinyInteger('punctuality_rating')->nullable();
                }
                if (!Schema::hasColumn('volunteer_attendances', 'evaluation_notes')) {
                    $table->text('evaluation_notes')->nullable();
                }
            });
        }

        // ═══════════════════════════════════════════════════════
        // جدول الإجازات (leaves)
        // ═══════════════════════════════════════════════════════
        if (!Schema::hasTable('leaves')) {
            Schema::create('leaves', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->date('start_date');
                $table->date('end_date');
                $table->string('type')->default('annual');
                $table->string('status')->default('pending');
                $table->text('reason')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->unsignedBigInteger('approved_by')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->timestamps();
            });
        }

        // ═══════════════════════════════════════════════════════
        // جدول خزينة المعاملات (treasury_transactions)
        // ═══════════════════════════════════════════════════════
        if (Schema::hasTable('treasury_transactions')) {
            Schema::table('treasury_transactions', function (Blueprint $table) {
                if (!Schema::hasColumn('treasury_transactions', 'payroll_id')) {
                    $table->unsignedBigInteger('payroll_id')->nullable();
                }
            });
        }

        // ═══════════════════════════════════════════════════════
        // جدول المرفقات (attachments)
        // ═══════════════════════════════════════════════════════
        if (Schema::hasTable('attachments')) {
            Schema::table('attachments', function (Blueprint $table) {
                if (!Schema::hasColumn('attachments', 'original_name')) {
                    $table->string('original_name')->nullable();
                }
                if (!Schema::hasColumn('attachments', 'mime_type')) {
                    $table->string('mime_type')->nullable();
                }
                if (!Schema::hasColumn('attachments', 'size')) {
                    $table->unsignedBigInteger('size')->nullable();
                }
                if (!Schema::hasColumn('attachments', 'uploaded_by')) {
                    $table->unsignedBigInteger('uploaded_by')->nullable();
                }
            });
        }

        // ═══════════════════════════════════════════════════════
        // جدول سجل الاستقبال (reception_logs)
        // ═══════════════════════════════════════════════════════
        if (!Schema::hasTable('reception_logs')) {
            Schema::create('reception_logs', function (Blueprint $table) {
                $table->id();
                $table->string('visitor_name');
                $table->string('visitor_phone')->nullable();
                $table->string('national_id')->nullable();
                $table->string('visit_purpose')->nullable();
                $table->string('host_name')->nullable();
                $table->unsignedBigInteger('received_by')->nullable();
                $table->timestamp('check_in')->nullable();
                $table->timestamp('check_out')->nullable();
                $table->string('status')->default('in');
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }

        // ═══════════════════════════════════════════════════════
        // جدول سجلات Audits - التأكد من وجود كل أعمدته
        // ═══════════════════════════════════════════════════════
        if (Schema::hasTable('audits')) {
            Schema::table('audits', function (Blueprint $table) {
                if (!Schema::hasColumn('audits', 'status_code')) {
                    $table->integer('status_code')->nullable();
                }
                if (!Schema::hasColumn('audits', 'ip')) {
                    $table->string('ip', 45)->nullable();
                }
                if (!Schema::hasColumn('audits', 'user_agent')) {
                    $table->text('user_agent')->nullable();
                }
                if (!Schema::hasColumn('audits', 'entity_type')) {
                    $table->string('entity_type')->nullable();
                }
                if (!Schema::hasColumn('audits', 'entity_id')) {
                    $table->unsignedBigInteger('entity_id')->nullable();
                }
            });
        }

        // ═══════════════════════════════════════════════════════
        // جدول مناديب الرحلات (delegate_trips)
        // ═══════════════════════════════════════════════════════
        if (Schema::hasTable('delegate_trips')) {
            Schema::table('delegate_trips', function (Blueprint $table) {
                if (!Schema::hasColumn('delegate_trips', 'status')) {
                    $table->string('status')->default('scheduled');
                }
                if (!Schema::hasColumn('delegate_trips', 'actual_distance_km')) {
                    $table->decimal('actual_distance_km', 10, 2)->nullable();
                }
                if (!Schema::hasColumn('delegate_trips', 'fuel_cost')) {
                    $table->decimal('fuel_cost', 12, 2)->nullable();
                }
                if (!Schema::hasColumn('delegate_trips', 'notes')) {
                    $table->text('notes')->nullable();
                }
                if (!Schema::hasColumn('delegate_trips', 'cancelled_at')) {
                    $table->timestamp('cancelled_at')->nullable();
                }
                if (!Schema::hasColumn('delegate_trips', 'cancelled_by')) {
                    $table->unsignedBigInteger('cancelled_by')->nullable();
                }
                if (!Schema::hasColumn('delegate_trips', 'cancellation_reason')) {
                    $table->text('cancellation_reason')->nullable();
                }
                if (!Schema::hasColumn('delegate_trips', 'journal_entry_id')) {
                    $table->unsignedBigInteger('journal_entry_id')->nullable();
                }
                if (!Schema::hasColumn('delegate_trips', 'treasury_id')) {
                    $table->unsignedBigInteger('treasury_id')->nullable();
                }
            });
        }

        // ═══════════════════════════════════════════════════════
        // جدول الأدوار (roles) - إضافة description
        // ═══════════════════════════════════════════════════════
        if (Schema::hasTable('roles')) {
            Schema::table('roles', function (Blueprint $table) {
                if (!Schema::hasColumn('roles', 'description')) {
                    $table->text('description')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
    // لا نحذف أي بيانات في الـ down
    }
};
