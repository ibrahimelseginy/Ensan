<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ═══════════════════════════════════════════════════════
 * Migration: Mobile App Session Sync
 * Date: 2026-03-04
 * ═══════════════════════════════════════════════════════
 * يُزامن قاعدة البيانات مع كل التغييرات التي تمت
 * في هذه الجلسة على وحدة تطبيق الموبايل:
 *
 * 1. إضافة حقل about_us لجدول mobile_home_items (نوع جديد)
 * 2. تأكيد بنية جدول mobile_case_applications وإضافة أي أعمدة ناقصة
 * 3. تأكيد بنية جدول mobile_notifications وإضافة أي أعمدة ناقصة
 * 4. تأكيد بنية جدول mobile_in_kind_donations وإضافة أي أعمدة ناقصة
 * 5. التحقق من عمود applicant_id_number في mobile_case_applications
 * ═══════════════════════════════════════════════════════
 */
return new class extends Migration
{
    public function up(): void
    {
        // ═══════════════════════════════════════════════════════
        // 1. جدول mobile_home_items
        //    - التحقق من وجود الجدول وإضافة أي أعمدة ناقصة
        //    - الجدول يدعم type = 'about_us' بالإضافة إلى:
        //      hero, gallery, service, share, campaign, final
        // ═══════════════════════════════════════════════════════
        if (!Schema::hasTable('mobile_home_items')) {
            Schema::create('mobile_home_items', function (Blueprint $table) {
                $table->id();
                $table->string('type'); // hero | gallery | service | share | campaign | final | about_us
                $table->string('title')->nullable();
                $table->text('description')->nullable();
                $table->string('image_path')->nullable();
                $table->string('icon')->nullable();
                $table->decimal('price', 15, 2)->nullable();
                $table->decimal('share_price', 15, 2)->nullable();
                $table->text('details')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        } else {
            Schema::table('mobile_home_items', function (Blueprint $table) {
                if (!Schema::hasColumn('mobile_home_items', 'title')) {
                    $table->string('title')->nullable()->after('type');
                }
                if (!Schema::hasColumn('mobile_home_items', 'description')) {
                    $table->text('description')->nullable()->after('title');
                }
                if (!Schema::hasColumn('mobile_home_items', 'image_path')) {
                    $table->string('image_path')->nullable();
                }
                if (!Schema::hasColumn('mobile_home_items', 'icon')) {
                    $table->string('icon')->nullable();
                }
                if (!Schema::hasColumn('mobile_home_items', 'price')) {
                    $table->decimal('price', 15, 2)->nullable();
                }
                if (!Schema::hasColumn('mobile_home_items', 'share_price')) {
                    $table->decimal('share_price', 15, 2)->nullable();
                }
                if (!Schema::hasColumn('mobile_home_items', 'details')) {
                    $table->text('details')->nullable();
                }
                if (!Schema::hasColumn('mobile_home_items', 'sort_order')) {
                    $table->integer('sort_order')->default(0);
                }
            });
        }

        // ═══════════════════════════════════════════════════════
        // 2. جدول mobile_case_applications
        //    (طلبات الحالات المستحقة - زاد الأيتام، بعثاء الأمل, ...)
        // ═══════════════════════════════════════════════════════
        if (!Schema::hasTable('mobile_case_applications')) {
            Schema::create('mobile_case_applications', function (Blueprint $table) {
                $table->id();
                $table->string('applicant_name');
                $table->string('applicant_phone');
                $table->string('applicant_id_number')->nullable();
                $table->string('case_type'); // zad | hope | medical | financial | education
                $table->text('description');
                $table->string('governorate')->nullable();
                $table->string('city')->nullable();
                $table->text('address')->nullable();
                $table->string('id_image_path')->nullable();
                $table->string('medical_report_path')->nullable();
                $table->string('status')->default('pending'); // pending | reviewing | approved | rejected
                $table->text('admin_notes')->nullable();
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->timestamps();
            });
        } else {
            Schema::table('mobile_case_applications', function (Blueprint $table) {
                if (!Schema::hasColumn('mobile_case_applications', 'applicant_name')) {
                    $table->string('applicant_name')->after('id');
                }
                if (!Schema::hasColumn('mobile_case_applications', 'applicant_phone')) {
                    $table->string('applicant_phone')->after('applicant_name');
                }
                if (!Schema::hasColumn('mobile_case_applications', 'applicant_id_number')) {
                    $table->string('applicant_id_number')->nullable();
                }
                if (!Schema::hasColumn('mobile_case_applications', 'case_type')) {
                    $table->string('case_type');
                }
                if (!Schema::hasColumn('mobile_case_applications', 'description')) {
                    $table->text('description');
                }
                if (!Schema::hasColumn('mobile_case_applications', 'governorate')) {
                    $table->string('governorate')->nullable();
                }
                if (!Schema::hasColumn('mobile_case_applications', 'city')) {
                    $table->string('city')->nullable();
                }
                if (!Schema::hasColumn('mobile_case_applications', 'address')) {
                    $table->text('address')->nullable();
                }
                if (!Schema::hasColumn('mobile_case_applications', 'id_image_path')) {
                    $table->string('id_image_path')->nullable();
                }
                if (!Schema::hasColumn('mobile_case_applications', 'medical_report_path')) {
                    $table->string('medical_report_path')->nullable();
                }
                if (!Schema::hasColumn('mobile_case_applications', 'status')) {
                    $table->string('status')->default('pending');
                }
                if (!Schema::hasColumn('mobile_case_applications', 'admin_notes')) {
                    $table->text('admin_notes')->nullable();
                }
                if (!Schema::hasColumn('mobile_case_applications', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                }
            });
        }

        // ═══════════════════════════════════════════════════════
        // 3. جدول mobile_notifications
        //    (إشعارات التطبيق المرسلة للمستخدمين)
        // ═══════════════════════════════════════════════════════
        if (!Schema::hasTable('mobile_notifications')) {
            Schema::create('mobile_notifications', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('body');
                $table->string('image_path')->nullable();
                $table->string('target_audience')->nullable(); // all | donors | beneficiaries
                $table->boolean('is_sent')->default(false);
                $table->timestamp('sent_at')->nullable();
                $table->timestamps();
            });
        } else {
            Schema::table('mobile_notifications', function (Blueprint $table) {
                if (!Schema::hasColumn('mobile_notifications', 'title')) {
                    $table->string('title');
                }
                if (!Schema::hasColumn('mobile_notifications', 'body')) {
                    $table->text('body');
                }
                if (!Schema::hasColumn('mobile_notifications', 'image_path')) {
                    $table->string('image_path')->nullable();
                }
                if (!Schema::hasColumn('mobile_notifications', 'target_audience')) {
                    $table->string('target_audience')->nullable();
                }
                if (!Schema::hasColumn('mobile_notifications', 'is_sent')) {
                    $table->boolean('is_sent')->default(false);
                }
                if (!Schema::hasColumn('mobile_notifications', 'sent_at')) {
                    $table->timestamp('sent_at')->nullable();
                }
            });
        }

        // ═══════════════════════════════════════════════════════
        // 4. جدول mobile_in_kind_donations
        //    (التبرعات العينية من تطبيق الموبايل)
        // ═══════════════════════════════════════════════════════
        if (!Schema::hasTable('mobile_in_kind_donations')) {
            Schema::create('mobile_in_kind_donations', function (Blueprint $table) {
                $table->id();
                $table->string('donor_name')->nullable();
                $table->string('donor_phone');
                $table->string('item_name');
                $table->string('item_description')->nullable();
                $table->integer('quantity')->default(1);
                $table->string('image_path')->nullable();
                $table->string('pickup_address')->nullable();
                $table->timestamp('preferred_pickup_time')->nullable();
                $table->string('status')->default('pending'); // pending | scheduled | collected
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->timestamps();
            });
        } else {
            Schema::table('mobile_in_kind_donations', function (Blueprint $table) {
                if (!Schema::hasColumn('mobile_in_kind_donations', 'donor_name')) {
                    $table->string('donor_name')->nullable();
                }
                if (!Schema::hasColumn('mobile_in_kind_donations', 'donor_phone')) {
                    $table->string('donor_phone');
                }
                if (!Schema::hasColumn('mobile_in_kind_donations', 'item_name')) {
                    $table->string('item_name');
                }
                if (!Schema::hasColumn('mobile_in_kind_donations', 'item_description')) {
                    $table->string('item_description')->nullable();
                }
                if (!Schema::hasColumn('mobile_in_kind_donations', 'quantity')) {
                    $table->integer('quantity')->default(1);
                }
                if (!Schema::hasColumn('mobile_in_kind_donations', 'image_path')) {
                    $table->string('image_path')->nullable();
                }
                if (!Schema::hasColumn('mobile_in_kind_donations', 'pickup_address')) {
                    $table->string('pickup_address')->nullable();
                }
                if (!Schema::hasColumn('mobile_in_kind_donations', 'preferred_pickup_time')) {
                    $table->timestamp('preferred_pickup_time')->nullable();
                }
                if (!Schema::hasColumn('mobile_in_kind_donations', 'status')) {
                    $table->string('status')->default('pending');
                }
                if (!Schema::hasColumn('mobile_in_kind_donations', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                }
            });
        }

        // ═══════════════════════════════════════════════════════
        // 5. جدول mobile_banners  (التأكد من بنيته)
        // ═══════════════════════════════════════════════════════
        if (!Schema::hasTable('mobile_banners')) {
            Schema::create('mobile_banners', function (Blueprint $table) {
                $table->id();
                $table->string('image_path')->nullable();
                $table->string('title')->nullable();
                $table->string('link_type')->nullable();
                $table->string('link_id')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        } else {
            Schema::table('mobile_banners', function (Blueprint $table) {
                if (!Schema::hasColumn('mobile_banners', 'image_path')) {
                    $table->string('image_path')->nullable();
                }
                if (!Schema::hasColumn('mobile_banners', 'title')) {
                    $table->string('title')->nullable();
                }
                if (!Schema::hasColumn('mobile_banners', 'link_type')) {
                    $table->string('link_type')->nullable();
                }
                if (!Schema::hasColumn('mobile_banners', 'link_id')) {
                    $table->string('link_id')->nullable();
                }
                if (!Schema::hasColumn('mobile_banners', 'sort_order')) {
                    $table->integer('sort_order')->default(0);
                }
            });
        }
    }

    public function down(): void
    {
        // لا نحذف أي جداول في الـ down لحماية البيانات
        // يمكن إضافة rollback يدوي إذا لزم الأمر
    }
};
